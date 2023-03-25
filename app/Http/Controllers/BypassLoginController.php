<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class BypassLoginController extends Controller
{
    public function signedUrlGeneration(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(401);
        }

        $url = URL::temporarySignedRoute(
            'bypass-login',
            now()->addMinutes(30),
            [
                'user' => $user->id,
                'bypass_by_user_id' => auth()->user()->id,
            ]
        );

        return response()->json([
            'url' => $url
        ]);
    }

    public function signedUrlLogin(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        if (auth()->user()) {
            abort(401);
        }

        $request->session()->regenerate();

        auth()->login($user);

        $apiToken = $request->user()->createToken('api-token')->plainTextToken;

        Helper::logAuth("Bypass login by user id: {$request->bypass_by_user_id}");

        return redirect()
            ->intended(RouteServiceProvider::HOME)
            ->withCookie(cookie('api-token', encrypt($apiToken), 10 * 365 * 24 * 60 * 60));
    }
}
