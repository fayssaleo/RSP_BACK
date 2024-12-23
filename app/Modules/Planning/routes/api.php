<?php

use App\Modules\Planning\Http\Controllers\PlanningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/plannings',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::post('/add', [PlanningController::class,'add']);
    Route::post('/getByDate', [PlanningController::class,'getByDate']);
    Route::post('/delete',[PlanningController::class,'deletePlanning']);
    Route::post('/getPlanningByRange',[PlanningController::class,'getPlanningByRange']);
});
