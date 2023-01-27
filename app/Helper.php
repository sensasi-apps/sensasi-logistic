<?php

use App\Models\UserActivity;
use Jenssegers\Agent\Agent;

/**
 * undocumented class
 */
class Helper
{
	public static function logAction(string $action, mixed $modelInstance)
	{
		if (app()->environment('testing')) {
			return;
		}

		$agent = new Agent();

		UserActivity::create([
			'user_id' => auth()->id(),
			'action' => $action,
			'model' => $modelInstance::class,
			'model_id' => $modelInstance->id,
			'value' => json_encode($modelInstance->getDirty()),
			'ip' => request()->ip(),
			'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
			'device' => $agent->device(),
			'os' => $agent->platform() . ' ' . $agent->version($agent->platform())
		]);
	}

	public static function logAuth(string $action)
	{
		$agent = new Agent();

		return UserActivity::create([
			'user_id' => auth()->id(),
			'action' => $action,
			'ip' => request()->ip(),
			'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
			'device' => $agent->device(),
			'os' => $agent->platform() . ' ' . $agent->version($agent->platform())
		]);
	}
}
