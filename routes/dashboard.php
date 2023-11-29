<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'dashboard', "as" => 'dashboard.'], function () {
        Route::get('/', [\App\Http\Controllers\Api\Dashboard\DashboardController::class, 'getData']);
    });
});