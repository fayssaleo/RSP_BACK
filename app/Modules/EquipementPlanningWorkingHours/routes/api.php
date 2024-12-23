<?php

use App\Modules\EquipementPlanningWorkingHours\Http\Controllers\EquipementPlanningWorkingHoursController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/equipementsplanningsworkinghours',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::post('/add', [EquipementPlanningWorkingHoursController::class,'add']);
});