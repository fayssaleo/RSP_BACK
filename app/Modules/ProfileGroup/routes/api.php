<?php

use App\Modules\ProfileGroup\Http\Controllers\ProfileGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/profilegroups',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::get('/', [ProfileGroupController::class, 'index']);
    Route::post('/add', [ProfileGroupController::class,'add']);
    Route::post('/delete', [ProfileGroupController::class,'delete']);
    Route::put('/update', [ProfileGroupController::class,'update']);
    Route::post('/getByType', [ProfileGroupController::class,'getByType']);
});