<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'sparepart-transaction', "as" => 'sparepart-transaction.'], function () {
        //puchasing order
        Route::group(['prefix' => 'purchasing', "as" => 'purchasing.'], function () {
            Route::get('/list', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'add']);
            Route::post('/add-detail', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'addDetail']);
            Route::get('/detail-po/{transaction_unique}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'detailPo']);
            Route::post('/update-po/{transaction_unique}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'updatePo']);
            Route::get('/accept-goods/{transaction_unique}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'terimaBarang']);
            Route::get('/accept-goods-by-detail/{transaction_unique}/{id}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'terimaBarangByDetail']);
            Route::post('/submit-payment-po', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\PurchasingSparePartController::class, 'submitPayment']);
            Route::get('/kredit-history/{transaction_unique}', [\App\Http\Controllers\Api\SparePartTransaction\Purchasing\KreditHistoryController::class, 'kreditHistory']);
        });

        //sell sparepart transaction
        Route::group(['prefix' => 'sell', "as" => 'sell.'], function () {
            Route::get('/list', [\App\Http\Controllers\Api\SparePartTransaction\Sell\SellSparePartController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\SparePartTransaction\Sell\SellSparePartController::class, 'add']);
            Route::post('/submit-payment', [\App\Http\Controllers\Api\SparePartTransaction\Sell\SellSparePartController::class, 'submitPayment']);
        });
    });
});