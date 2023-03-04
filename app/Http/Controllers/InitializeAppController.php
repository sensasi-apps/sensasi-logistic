<?php

namespace App\Http\Controllers;

use Helper;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class InitializeAppController extends Controller
{
    private function isAdminExist()
    {
        return User::role('Super Admin')->count() > 0;
    }

    public function check()
    {
        if (!$this->isAdminExist()) {
            return redirect()->route('initialize-app.create-admin-user');
        }

        return redirect(RouteServiceProvider::HOME);
    }

    public function index()
    {
        return redirect()->route('initialize-app.check');
    }

    public function createAdminUser()
    {
        if ($this->isAdminExist()) {
            abort('403');
        }

        return view('pages.initialize-app.admin-user-form');
    }

    public function storeAdminUser(Request $request)
    {
        if ($this->isAdminExist()) {
            abort('403');
        }

        $validatedInput = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email:dns',
            'password' => 'required|confirmed|min:8|max:255',
        ]);

        $validatedInput['password'] = bcrypt($validatedInput['password']);

        User::insert($validatedInput);

        $user = User::where('email', $validatedInput['email'])
            ->first()
            ->assignRole('Super Admin');

        Auth::login($user);

        Helper::logAuth('first registered and login via form');

        $request->session()->regenerate();

        $apiToken = request()->user()->createToken('api-token')->plainTextToken;

        return redirect()
            ->route('initialize-app.check')
            ->withCookie(cookie('api-token', encrypt($apiToken), 10 * 365 * 24 * 60 * 60));
    }

    public function signUpWithGoogle()
    {
        if ($this->isAdminExist()) {
            abort('403');
        }

        return Socialite::driver('google')
            ->redirectUrl(route('initialize-app.create-admin-user.oauth.google.redirect'))
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($this->isAdminExist()) {
            abort('403');
        }

        $googleUser = Socialite::driver('google')
            ->redirectUrl(route('initialize-app.create-admin-user.oauth.google.redirect'))
            ->stateless()
            ->user();

        User::insert([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'password' => bcrypt(env('APP_KEY'))
        ]);

        $user = User::where('email', $googleUser->email)
            ->first()->assignRole('Super Admin');

        Auth::login($user);

        $request->session()->regenerate();

        session()->flash('notifications', [
            [
                'Password belum diatur, silahkan <b><a href="#profile" data-toggle="modal">atur password</a></b>.',
                'warning'
            ]
        ]);

        Helper::logAuth('first registered and login via google');

        $apiToken = request()->user()->createToken('api-token')->plainTextToken;

        return redirect()
            ->route('initialize-app.check')
            ->withCookie(cookie('api-token', encrypt($apiToken), 10 * 365 * 24 * 60 * 60));
    }
}
