<?php

use App\Modules\Department\Http\Controllers\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/departments',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::post('/add', [DepartmentController::class,'add']);
    Route::post('/delete', [DepartmentController::class,'delete']);
    Route::put('/update', [DepartmentController::class,'update']);
});