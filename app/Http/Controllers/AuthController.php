<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
  public function loginForm()
  {
    return view('pages.auth.login');
  }

  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email:dns',
      'password' => 'required|min:8|max:255',
    ]);


    if (Auth::attempt($credentials, isset($request->remember))) {
      $user = Auth::user();
      $request->session()->regenerate();
      return redirect()->intended('/');
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
      session()->regenerate();

      return $user->has_default_password
        ? redirect()->intended('/')->with('alerts', [
          [
            'class' => 'warning',
            'message' => 'Password belum diator, silahkan <a href="#">atur password</a>.'
            // 'message' => 'Password belum diator, silahkan <a href="' . route('profile.setPassword') . '">atur password</a>.'
          ]
        ])
        : redirect();
    }

    return redirect()->route('login')->withErrors([
      'attemp' => 'email <strong>' . $googleUser->getEmail() . '</strong> tidak terdaftar pada sistem, silahkan menghubungi admin.'
    ]);
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('index');
  }
}
