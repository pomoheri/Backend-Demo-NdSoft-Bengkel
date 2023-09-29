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
    Route::group(['prefix' => 'part-location-management', "as" => 'part-location-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\PartInventory\ParLocationController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\PartInventory\ParLocationController::class, 'add']);
        Route::get('/detail/{part_location}', [\App\Http\Controllers\Api\PartInventory\ParLocationController::class, 'detail']);
        Route::post('/update/{part_location}', [\App\Http\Controllers\Api\PartInventory\ParLocationController::class, 'update']);
        Route::get('/delete/{part_location}', [\App\Http\Controllers\Api\PartInventory\ParLocationController::class, 'delete']);
    });
    Route::group(['prefix' => 'spare-part-management', "as" => 'spare-part-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\PartInventory\SparePartController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\PartInventory\SparePartController::class, 'add']);
        Route::get('/edit/{spare_part}', [\App\Http\Controllers\Api\PartInventory\SparePartController::class, 'edit']);
        Route::post('/update/{spare_part}', [\App\Http\Controllers\Api\PartInventory\SparePartController::class, 'update']);
    });
});