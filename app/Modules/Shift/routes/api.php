<?php

use App\Modules\Shift\Http\Controllers\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/shifts',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::get('/', [ShiftController::class, 'index']);
    Route::post('/add', [ShiftController::class,'add']);
    Route::post('/delete', [ShiftController::class,'delete']);
    Route::put('/update', [ShiftController::class,'update']);
    Route::post('/getByCategory', [ShiftController::class,'getByCategory']);
    Route::get('/getShiftByTime', [ShiftController::class,'getShiftByTime']);
}); 