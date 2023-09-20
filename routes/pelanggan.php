<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'car-type-management', "as" => 'car-type-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\Pelanggan\ManagementCarTypeController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\Pelanggan\ManagementCarTypeController::class, 'add']);
        Route::get('/detail/{car_type}', [\App\Http\Controllers\Api\Pelanggan\ManagementCarTypeController::class, 'detail']);
        Route::post('/update/{car_type}', [\App\Http\Controllers\Api\Pelanggan\ManagementCarTypeController::class, 'update']);
        Route::get('/delete/{car_type}', [\App\Http\Controllers\Api\Pelanggan\ManagementCarTypeController::class, 'delete']);
    });
});