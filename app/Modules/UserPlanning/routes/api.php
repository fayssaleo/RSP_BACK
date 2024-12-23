<?php

use App\Modules\UserPlanning\Http\Controllers\UserPlanningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/usersplannings',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::post('/add', [UserPlanningController::class,'add']);
    Route::put('/update', [UserPlanningController::class,'update']);
    Route::post('/getByPlanning', [UserPlanningController::class,'getByPlanning']);
});