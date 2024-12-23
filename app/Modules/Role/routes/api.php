<?php

use App\Modules\Role\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/roles',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/add', [RoleController::class,'add']);
    Route::post('/delete', [RoleController::class,'delete']);
    Route::put('/update', [RoleController::class,'update']);
});
Route::group([
    'prefix' => 'api/roles',
    // 'middleware' => ['auth:sanctum'],
], function ($router) {
    // Route::get('/', [RoleController::class, 'index']);
    // Route::post('/add', [RoleController::class,'add']);
    // Route::post('/delete', [RoleController::class,'delete']);
    // Route::put('/update', [RoleController::class,'update']);
});