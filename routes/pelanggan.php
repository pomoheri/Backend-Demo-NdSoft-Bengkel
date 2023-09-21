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

    Route::group(['prefix' => 'customer-management', "as" => 'customer-management'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\Pelanggan\CustomerManagementController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\Pelanggan\CustomerManagementController::class, 'add']);
        Route::get('/edit/{customer}', [\App\Http\Controllers\Api\Pelanggan\CustomerManagementController::class, 'edit']);
        Route::post('/update/{customer}', [\App\Http\Controllers\Api\Pelanggan\CustomerManagementController::class, 'update']);
        Route::get('/delete/{customer}', [\App\Http\Controllers\Api\Pelanggan\CustomerManagementController::class, 'delete']);
    });
});