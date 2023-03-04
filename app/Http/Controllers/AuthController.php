<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
	public function login(Request $request)
	{
		$credentials = $request->validate([
			'email' => 'required|email:dns',
			'password' => 'required|min:8|max:255',
		]);

		if (!Auth::attempt($credentials, isset($request->remember))) {
			return back()->withErrors(['attemp' => __('auth.failed')]);
		}

		$request->session()->regenerate();

		$apiToken = $request->user()->createToken('api-token')->plainTextToken;

		Helper::logAuth('login via form');

		return redirect()
			->intended(RouteServiceProvider::HOME)
			->withCookie(cookie('api-token', encrypt($apiToken), 10 * 365 * 24 * 60 * 60));
	}

	public function googleOauth()
	{
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleOauth(Request $request)
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::firstWhere(
			'email',
			$googleUser->getEmail()
		);

		// if user not found
		if (!$user) {
			return redirect('login')->withErrors([
				'attemp' => __('Your email :email is not registered on the system, please contact the administrator.', ['email' => "<strong>{$googleUser->getEmail()}</strong>"])
			]);
		}

		Auth::login($user, true);

		$request->session()->regenerate();

		$apiToken = $request->user()->createToken('api-token')->plainTextToken;

		Helper::logAuth('login via google');

		// if user password is default
		if (Hash::check(config('app.key'), $user->password)) {
			session()->flash('notifications', [
				[
					__('Password is not configured, please set your password :here', ['here' => '<a href="javascript;;" class="alert-link" data-toggle="modal" data-target="#profile">' . __('here') . '</a>.']),
					'warning'
				]
			]);
		}

		return redirect()
			->intended(RouteServiceProvider::HOME)
			->withCookie(cookie('api-token', encrypt($apiToken), 10 * 365 * 24 * 60 * 60));
	}

	public function logout(Request $request)
	{
		Helper::logAuth('logout');
		$request->user()->tokens()->delete();

		Cookie::queue(Cookie::forget('api-token'));

		Auth::logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return redirect()->back();
	}


	public function forgotPassword(Request $request)
	{
		$request->validate([
			'email' => 'required|email:dns'
		]);

		$user = User::where('email', $request->email)->first();

		if ($user) {
			Password::sendResetLink(['email' => $request->email]);
			Helper::logAuth('request reset password', $user->id);
		}

		return redirect()
			->action("App\Http\Controllers\AuthController@login")
			->with('notifications', [
				"Reset password akun <b>$request->email</b> telah terkirim via email."
			]);
	}

	public function resetPasswordForm(Request $request, $token)
	{
		$email = $request->email;

		$passwordReset = DB::table('password_resets')->where('email', $email)->first();

		if (!$passwordReset) {
			abort(403, 'Invalid Email');
		}

		if (!Hash::check($token, $passwordReset->token)) {
			abort(403, 'Invalid Token');
		}

		return view('pages.auth.reset-password-form', compact('token', 'email'));
	}

	public function resetPassword(Request $request)
	{
		$request->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {

				DB::update('update users set password = ?, remember_token = ? where email = ?', [bcrypt($password), Str::random(60), $user->email]);

				Helper::logAuth('password resetted', $user->id);

				DB::table('password_resets')->where('email', $user->email)->delete();

				event(new PasswordReset($user));
			}
		);

		return $status === Password::PASSWORD_RESET
			? redirect()->back()->with('notifications', [[__('password.reset'), 'success']])
			: back()->withErrors(['email' => [__($status)]]);
	}
}
