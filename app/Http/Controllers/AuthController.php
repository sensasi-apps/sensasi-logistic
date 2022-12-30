<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\User;
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

		if (Auth::attempt($credentials, isset($request->remember))) {

			Helper::logAuth('login via form');

			
			$request->session()->regenerate();

			$apiToken = $request->user()->createToken('api-token')->plainTextToken;

			Cookie::queue('api-token', encrypt($apiToken), time() + 10 * 365 * 24 * 60 * 60);
		}

		return back()->withErrors(['attemp' => 'email dan password tidak sesuai']);
	}

	public function googleOauth()
	{
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleOauth()
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::firstWhere('email', $googleUser->getEmail());

		if ($user !== null) {
			Auth::login($user);
			Helper::logAuth('login via google');

			session()->regenerate();
			$apiToken = $user->createToken('api-token');

			Cookie::queue('api-token', encrypt($apiToken), time() + 10 * 365 * 24 * 60 * 60);


			request()->session()->flash('alerts', [
				[
					'class' => 'warning',
					'message' => 'Password belum diatur, silahkan <a href="#profile" data-toggle="modal">atur password</a>.'
				]
			]);
		}

		return redirect()->back()->withErrors([
			'attemp' => 'email <strong>' . $googleUser->getEmail() . '</strong> tidak terdaftar pada sistem, silahkan menghubungi admin.'
		]);
	}

	public function logout(Request $request)
	{
		Helper::logAuth('logout');
		$request->user()->tokens()->delete();
		
		Cookie::forget('api-token');

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
			Helper::logAuth('request reset password');
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
				$user->forceFill([
					'password' => bcrypt($password)
				])->setRememberToken(Str::random(60));

				$user->save();

				Helper::logAuth('password reseted');

				event(new PasswordReset($user));
			}
		);

		return $status === Password::PASSWORD_RESET
			? redirect()->back()->with('notifications', [[__('password.reset'), 'success']])
			: back()->withErrors(['email' => [__($status)]]);
	}
}
