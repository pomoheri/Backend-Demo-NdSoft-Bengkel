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
    Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    Route::group(['prefix' => 'master-data', "as" => 'master-data.'], function () {
        Route::get('/list-role', [\App\Http\Controllers\Api\MasterDataController::class, 'listRole']);
        Route::get('/list-menu', [\App\Http\Controllers\Api\MasterDataController::class, 'listMenu']);
        Route::get('/list-car-brand', [\App\Http\Controllers\Api\MasterDataController::class, 'listCarBrand']);
        Route::get('/list-teknisi', [\App\Http\Controllers\Api\MasterDataController::class, 'listTeknisi']);
    });

    Route::group(['prefix' => 'menu-management', "as" => 'menu-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\MenuManagementController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\MenuManagementController::class, 'add']);
        Route::get('/delete/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'delete']);
        Route::get('/detail/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'detail']);
        Route::post('/update/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'update']);
        Route::get('/role-menu-lists/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'roleMenuList']);
        Route::post('/set-role-menu/{menus}', [\App\Http\Controllers\Api\MenuManagementController::class, 'setRoleMenu']);
    });

    Route::group(['prefix' => 'users-management', "as" => 'users-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\UsersManagementController::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\UsersManagementController::class, 'add']);
        Route::get('/detail/{user}', [\App\Http\Controllers\Api\UsersManagementController::class, 'detail']);
        Route::post('/update/{user}', [\App\Http\Controllers\Api\UsersManagementController::class, 'update']);
        Route::get('/delete/{user}', [\App\Http\Controllers\Api\UsersManagementController::class, 'delete']);
    });

    Route::group(['prefix' => 'role-access-management', "as" => 'role-access-management.'], function () {
        Route::get('/list', [\App\Http\Controllers\Api\RoleAccessManagement::class, 'list']);
        Route::post('/add', [\App\Http\Controllers\Api\RoleAccessManagement::class, 'add']);
        Route::get('/detail/{role}', [\App\Http\Controllers\Api\RoleAccessManagement::class, 'detail']);
        Route::post('/update/{role}', [\App\Http\Controllers\Api\RoleAccessManagement::class, 'update']);
        Route::get('/delete/{role}', [\App\Http\Controllers\Api\RoleAccessManagement::class, 'delete']);
    });
});
