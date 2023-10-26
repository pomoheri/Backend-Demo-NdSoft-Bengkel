<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'register', "as" => 'register.'], function () {
        //Register Purchasing Sparepart
        Route::group(['prefix' => 'purchasing-sparepart', "as" => 'purchasing-sparepart.'], function () {
            Route::get('/{start_date?}/{end_date?}', [\App\Http\Controllers\Api\Register\RegisterSparepartPurchasingController::class, 'list']);
        });
        //Register Sell Sparepart
        Route::group(['prefix' => 'sell-sparepart', "as" => 'sell-sparepart.'], function () {
            Route::get('/{start_date?}/{end_date?}', [\App\Http\Controllers\Api\Register\RegisterSellController::class, 'list']);
        });
        //Register Small Transaction
        Route::group(['prefix' => 'small-transaction', "as" => 'small-transaction.'], function () {
            Route::get('/{start_date?}/{end_date?}', [\App\Http\Controllers\Api\Register\RegisterSmallTransactionController::class, 'list']);
        });
    });
});