<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });

    Route::get('datatable/{model_name}/{params_json?}', '\App\Http\Controllers\Api\DatatableController')->name('api.datatable');
    Route::get('select2/{modelName}', '\App\Http\Controllers\Api\Select2Controller');
});
