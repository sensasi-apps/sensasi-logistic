<?php

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class Helper
{
	public static function logAction(string $action, mixed $modelInstance): UserActivity
	{
		$agent = new Agent();

		return UserActivity::create([
			'user_id' => auth()->id(),
			'action' => $action,
			'model' => $modelInstance::class,
			'model_id' => $modelInstance->id,
			'value' => $modelInstance->getDirty(),
			'ip' => request()->ip(),
			'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
			'device' => $agent->device(),
			'os' => $agent->platform() . ' ' . $agent->version($agent->platform())
		]);
	}

	public static function logAuth(string $action, int $userId = null): UserActivity
	{
		$agent = new Agent();

		return UserActivity::create([
			'user_id' => $userId ?? auth()->id(),
			'action' => $action,
			'ip' => request()->ip(),
			'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
			'device' => $agent->device(),
			'os' => $agent->platform() . ' ' . $agent->version($agent->platform())
		]);
	}

	public static function getSuccessCrudResponse(string $event, string $dataType, string $id_for_human): JsonResponse|RedirectResponse
	{
		$message = __("notification.data_{$event}", ['type' => $dataType, 'name' => "<b>{$id_for_human}</b>"]);
		$color = $event == 'deleted' ? 'warning' : 'success';

		if (request()->wantsJson()) {
			return response()->json([
				'notifications' => [[
					'messageHtml' => $message,
					'color' => $color
				]]
			]);
		}

		return back()->with('notifications', [
			[$message, $color]
		]);
	}

	public static function createSuperman(): User
	{
		DB::table('users')->insert([
			'name' => 'superman',
			'email' => 'super@man.com',
			'password' => bcrypt('superman')
		]);

		return User::where('email', 'super@man.com')
			->first()->assignRole('Super Admin');
	}

	public static function numberFomat(int|float $number, int $min_fraction_digits = 0, int $max_fraction_digits = 4): string
	{
		$formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::DECIMAL);
		$formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $min_fraction_digits);
		$formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $max_fraction_digits);

		return $formatter->format($number);
	}
}
