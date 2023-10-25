<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'register', "as" => 'register.'], function () {
        //Register Purchasing Sparepart
        Route::group(['prefix' => 'purchasing-sparepart', "as" => 'purchasing-sparepart.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Register\RegisterSparepartPurchasingController::class, 'list']);
        });
    });
});