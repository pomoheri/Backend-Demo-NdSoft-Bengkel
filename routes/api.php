<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user-profile', [\App\Http\Controllers\Api\AuthController::class, 'getUserProfile']);
    Route::get('/default-menu', [\App\Http\Controllers\Api\AuthController::class, 'defaultMenu']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    Route::group(['prefix' => 'master-data', "as" => 'master-data.'], function () {
        Route::get('/list-role', [\App\Http\Controllers\Api\MasterDataController::class, 'listRole']);
        Route::get('/list-menu', [\App\Http\Controllers\Api\MasterDataController::class, 'listMenu']);
    });
    Route::group(['prefix' => 'menu-management', "as" => 'menu-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\MenuManagementController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\MenuManagementController::class, 'add']);
        Route::get('/delete/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'delete']);
        Route::get('/detail/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'detail']);
        Route::post('/update/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'update']);
    });
});
