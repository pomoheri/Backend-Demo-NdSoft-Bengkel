<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'rekap-harian', "as" => 'rekap-harian.'], function () {
        Route::get('/invoice-service', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'invoiceService']);
        Route::get('/invoice-sell', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'invoiceSell']);
        Route::get('/get-pdf', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'getPdf']);
    });
});