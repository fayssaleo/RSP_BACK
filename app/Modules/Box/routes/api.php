<?php

use App\Modules\Box\Http\Controllers\BoxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/boxes',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::post('/setWHAction', [BoxController::class,'setWHAction']);
    Route::post('/setWHAction_automatically', [BoxController::class,'setWHAction_automatically']);
    Route::post('/UnSetWHAction', [BoxController::class,'UnSetWHAction']);
    Route::post('/add', [BoxController::class,'add']);
    Route::post('/updatePlanningAndBoxes', [BoxController::class,'updatePlanningAndBoxes']);
    Route::post('/addPlanningAndBoxes', [BoxController::class,'addPlanningAndBoxes']);
    Route::post('/getPlanningByIdAndBoxes', [BoxController::class,'getPlanningByIdAndBoxes']);
    Route::post('/addPlanningBoxes', [BoxController::class,'addPlanningBoxes']);
    Route::post('/getByPlanning', [BoxController::class,'getBoxesByPlanningId']);
    Route::post('/update', [BoxController::class,'update']);
});
