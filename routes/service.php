<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'service', "as" => 'service.'], function () {
        //CRUD Labour
        Route::group(['prefix' => 'labour', "as" => 'labour.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\LabourController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\Service\LabourController::class, 'add']);
            Route::get('/detail/{id}', [\App\Http\Controllers\Api\Service\LabourController::class, 'detail']);
            Route::post('/update', [\App\Http\Controllers\Api\Service\LabourController::class, 'update']);
            Route::get('/delete/{labour}', [\App\Http\Controllers\Api\Service\LabourController::class, 'delete']);
        });
    });
});