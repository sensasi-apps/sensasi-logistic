<?php

use App\Http\Controllers\AppSystemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InitializeAppController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialInController;
use App\Http\Controllers\MaterialOutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInController;
use App\Http\Controllers\ProductOutController;
use App\Http\Controllers\ManufactureController;
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

    //
    Route::controller(InitializeAppController::class)->group(function () {
        Route::prefix('initialize-app')->name('initialize-app')->group(function () {
            Route::name('.create-admin-user')->prefix('create-admin-user')->group(function () {
                Route::get('/', 'createAdminUser');
                Route::post('/', 'storeAdminUser')->name('.store');

                Route::name('.oauth.google')->prefix('oauth/google')->group(function () {
                    Route::get('/', 'signUpWithGoogle');
                    Route::get('redirect', 'handleGoogleCallback')->name('.redirect');
                });
            });
        });
    });




    Route::view('forgot-password', 'pages.auth.forgot-password-form');

    Route::controller(AuthController::class)->group(function () {
        
        Route::post('login', 'login');
        
        Route::prefix('login')->name('login')->group(function () {
            Route::view('/', 'pages.auth.login-form');
            Route::get('oauth/google', 'googleOauth')->name('.oauth.google');
            Route::get('oauth/google/redirect', 'handleGoogleOauth')->name('.oauth.google.callback');
        });
        
        Route::post('forgot-password', 'forgotPassword');
        Route::get('reset-password/{token}', 'resetPasswordForm')->name('password.reset');
        Route::post('reset-password', 'resetPassword')->name('password.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => null)->name('/');
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');


    Route::controller(AppSystemController::class)->group(function () {
        Route::prefix('system')->name('system')->group(function () {
            Route::get('ip-addr', 'ipAddrIndex')->name('.ip-addr');
        });
    });

    
    Route::resource('materials', MaterialController::class)->except([
        'create', 'show', 'edit'
    ]);

    Route::resource('material-ins', MaterialInController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::resource('material-outs', MaterialOutController::class)->only([
        'store', 'update', 'destroy'
    ]);

    Route::resource('products', ProductController::class)->except([
        'create', 'show', 'edit'
    ]);

    Route::resource('product-ins', ProductInController::class)->except([
        'create', 'show', 'edit'
    ]);

    Route::resource('product-outs', ProductOutController::class)->except([
        'create', 'show', 'edit'
    ]);

    Route::resource('manufactures', ManufactureController::class)->except([
        'create', 'show', 'edit'
    ]);

    if (App::environment('local')) {
        Route::get('basic-page-format', fn () => view('basic-page-format'));
    }
});
