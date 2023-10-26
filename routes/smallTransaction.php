<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'small-transaction', "as" => 'small-transaction.'], function () {
        Route::get('/list/{start_date?}/{end_date?}', [\App\Http\Controllers\Api\SmallTransaction\SmallTransactionController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\SmallTransaction\SmallTransactionController::class, 'add']);
        Route::get('/detail/{id}', [\App\Http\Controllers\Api\SmallTransaction\SmallTransactionController::class, 'detail']);
        Route::post('/update', [\App\Http\Controllers\Api\SmallTransaction\SmallTransactionController::class, 'update']);
        Route::get('/delete/{id}', [\App\Http\Controllers\Api\SmallTransaction\SmallTransactionController::class, 'delete']);
    });
});