<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InitializeAppController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('initialize-app', [InitializeAppController::class, 'index']);
Route::get('initialize-app/check', [InitializeAppController::class, 'check'])->name('initialize-app.check');

Route::middleware('guest')->group(function () {
    Route::controller(InitializeAppController::class)
        ->prefix('initialize-app')
        ->name('initialize-app')
        ->group(function () {

            Route::name('.create-admin-user')->prefix('create-admin-user')->group(function () {
                Route::get('/', 'createAdminUser');
                Route::post('/', 'storeAdminUser')->name('.store');

                Route::name('.oauth.google')->prefix('oauth/google')->group(function () {
                    Route::get('/', 'signUpWithGoogle');
                    Route::get('redirect', 'handleGoogleCallback')->name('.redirect');
                });
            });
        });


    Route::controller(AuthController::class)
        ->prefix('login')
        ->name('login')
        ->group(function () {
            Route::get('/', [AuthController::class, 'loginForm']);
            Route::post('/', 'login');
            Route::get('oauth/google', 'googleOauth')->name('.oauth.google');
            Route::get('oauth/google/redirect', 'handleGoogleOauth')->name('.oauth.google.callback');
        });


    // Route::get('forgot-password', [ForgotPassword::class, 'index'])->name('forgot-password');
    // Route::post('forgot-password', [ForgotPassword::class, 'send'])->name('forgot-password.send');
    // Route::get('reset-password/{token}', [ForgotPassword::class, 'resetPasswordForm'])->name('password.reset');
    // Route::post('reset-password', [ForgotPassword::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::controller(InitializeAppController::class)
        ->prefix('sistem')
        ->name('sistem')
        ->group(function () {
            Route::get('ip-addr', [WebSystemController::class, 'ipAddrIndex'])->name('ip-addr');
        });

    if (App::environment('local')) {
        Route::get('basic-page-format', function () {
            return view('basic-page-format');
        });
    }
});
