<?php

use App\Http\Controllers\AppSystemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InitializeAppController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialInController;
use App\Http\Controllers\MaterialOutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInController;
use App\Http\Controllers\ProductOutController;
use App\Http\Controllers\ManufactureIndexController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialReportController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\ManufactureReportController;
use App\Http\Controllers\MaterialIndexController;
use App\Http\Controllers\MaterialManufactureController;
use App\Http\Controllers\PhpInfoController;
use App\Http\Controllers\ProductIndexController;
use App\Http\Controllers\ProductManufactureController;
use Illuminate\Support\Facades\Route;

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

    // all user
    Route::get('/', IndexController::class)->name('/');
    Route::post('user/update', [UserController::class, 'selfUpdate'])->name('user.update');
    Route::post('user/update-password', [UserController::class, 'selfUpdatePassword'])->name('user.update-password');
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::middleware('role:Super Admin|Stackholder')->get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:Super Admin|Stackholder')->group(function () {
        Route::prefix('report')->name('report.')->group(function () {
            route::get('materials', MaterialReportController::class)->name('material.index');
            route::get('products', ProductReportController::class)->name('product.index');
            route::get('manufactures', ManufactureReportController::class)->name('manufacture.index');
        });
    });


    Route::middleware('role:Super Admin|Warehouse|Purchase|Sales')->group(function () {
        Route::get('materials', MaterialIndexController::class)->name('materials.index');

        Route::resource('materials', MaterialController::class)->only([
            'store', 'update', 'destroy'
        ]);

        Route::resource('material-ins', MaterialInController::class)->only([
            'store', 'update', 'destroy'
        ]);

        Route::resource('material-outs', MaterialOutController::class)->only([
            'store', 'update', 'destroy'
        ]);
    });

    Route::middleware('role:Super Admin|Warehouse|Purchase|Sales')->group(function () {
        Route::get('products', ProductIndexController::class)->name('products.index');

        Route::resource('products', ProductController::class)->only([
            'store', 'update', 'destroy'
        ]);

        Route::resource('product-ins', ProductInController::class)->only([
            'store', 'update', 'destroy'
        ]);

        Route::resource('product-outs', ProductOutController::class)->only([
            'store', 'update', 'destroy'
        ]);
    });

    Route::middleware('role:Super Admin|Manufacture')->group(function () {

        Route::get('manufactures', ManufactureIndexController::class)->name('manufactures.index');

        Route::resource('product-manufactures', ProductManufactureController::class)->only([
            'store', 'update', 'destroy'
        ]);

        Route::resource('material-manufactures', MaterialManufactureController::class)->only([
            'store', 'update', 'destroy'
        ]);
    });

    Route::middleware('role:Super Admin|Admin')->controller(AppSystemController::class)->group(function () {
        Route::view('system/user-activities', 'pages.system.user-activities');

        Route::prefix('system')->name('system.')->group(function () {
            Route::resource('users', UserController::class)->except([
                'create', 'show', 'edit', 'destroy'
            ]);
        });
    });

    Route::middleware('role:Super Admin')->prefix('_')->group(function () {
        Route::view('basic-page-format', 'basic-page-format');
        Route::get('ip-addr', [AppSystemController::class, 'ipAddrIndex'])->name('ip-addr');
        Route::get('phpinfo', PhpInfoController::class);
    });
});
