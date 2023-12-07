<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'rekap-harian', "as" => 'rekap-harian.'], function () {
        Route::get('/invoice-service', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'invoiceService']);
        Route::get('/invoice-sell', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'invoiceSell']);
        Route::get('/lainnya', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'lainnya']);
        Route::get('/get-pdf', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'getPdf']);

        Route::group(['prefix' => 'pengeluaran', "as" => 'pengeluaran.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'pengeluaran']);
            Route::get('/get-pdf', [\App\Http\Controllers\Api\RekapHarian\RekapHarianController::class, 'getPdfPengeluaran']);
        });
    });
});