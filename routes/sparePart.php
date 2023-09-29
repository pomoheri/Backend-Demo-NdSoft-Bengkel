<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'sparepart-transaction', "as" => 'sparepart-transaction.'], function () {
        Route::group(['prefix' => 'purchasing', "as" => 'purchasing.'], function () {
            Route::get('/list', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'add']);
        });
    });
});