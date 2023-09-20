<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'supplier-management', "as" => 'supplier-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\PartInventory\ManagementSupplierController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\PartInventory\ManagementSupplierController::class, 'add']);
        Route::get('/detail/{supplier}', [\App\Http\Controllers\Api\PartInventory\ManagementSupplierController::class, 'detail']);
        Route::post('/update/{supplier}', [\App\Http\Controllers\Api\PartInventory\ManagementSupplierController::class, 'update']);
        Route::get('/delete/{supplier}', [\App\Http\Controllers\Api\PartInventory\ManagementSupplierController::class, 'delete']);
    });
});