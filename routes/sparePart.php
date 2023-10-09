<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'sparepart-transaction', "as" => 'sparepart-transaction.'], function () {
        Route::group(['prefix' => 'purchasing', "as" => 'purchasing.'], function () {
            Route::get('/list', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'add']);
            Route::post('/add-detail', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'addDetail']);
            Route::get('/accept-goods/{transaction_unique}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'terimaBarang']);
            Route::post('/submit-payment-po', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'submitPayment']);
        });
    });
});