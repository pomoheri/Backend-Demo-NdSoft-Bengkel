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
        //Hand Over (Serah Terima)
        Route::group(['prefix' => 'hand-over', "as" => 'hand-over.'], function () {
            Route::get('/', [\App\Http\Controllers\Api\Service\HandOverController::class, 'list']);
            Route::post('/add', [\App\Http\Controllers\Api\Service\HandOverController::class, 'add']);
            Route::get('/detail/{estimation_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'detail']);
            Route::post('/update', [\App\Http\Controllers\Api\Service\HandOverController::class, 'update']);
            Route::get('/print-hand-over/{estimation_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'printHandOver']);
            Route::get('/delete/{estimation_unique}', [\App\Http\Controllers\Api\Service\HandOverController::class, 'delete']);
        });

    });
});