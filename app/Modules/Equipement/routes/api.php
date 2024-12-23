<?php

use App\Modules\Equipement\Http\Controllers\EquipementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'api/equipements',
    'middleware' => ['auth:sanctum'],
], function ($router) {
    Route::get('/', [EquipementController::class, 'index']);
    Route::post('/add', [EquipementController::class,'add']);
    Route::post('/delete', [EquipementController::class,'delete']);
    Route::put('/update', [EquipementController::class,'update']);
    Route::post('/getById', [EquipementController::class,'getById']);
    Route::post('/getByProfileGroup', [EquipementController::class,'getByProfileGroup']);
});
