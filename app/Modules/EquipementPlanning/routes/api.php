<?php

use App\Modules\EquipementPlanning\Http\Controllers\EquipementPlanningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/equipementsplannings',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::post('/add', [EquipementPlanningController::class,'add']);
    Route::put('/update', [EquipementPlanningController::class,'update']);
    Route::post('/getByPlanning', [EquipementPlanningController::class,'getByPlanning']);
});