<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'laporan', "as" => 'laporan.'], function () {
        Route::get('/get-data', [\App\Http\Controllers\Api\Laporan\LaporanController::class, 'getData']);
    });
});