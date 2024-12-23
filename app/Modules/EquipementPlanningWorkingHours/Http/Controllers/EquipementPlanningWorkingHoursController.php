<?php

namespace App\Modules\EquipementPlanningWorkingHours\Http\Controllers;

use App\Modules\EquipementPlanningWorkingHours\Models\EquipementPlanningWorkingHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipementPlanningWorkingHoursController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $rules = [
            'equipement_planning_id' => 'required',
            'start_time' => 'required | string',
            'end_time' => 'required | string',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return [
                "error" => $validator->errors()->first(),
                "status" => 422
            ];
        }
        try {
            // Create a new equipementplanningworkinghours record
            $equipementplanningworkinghours = EquipementPlanningWorkingHours::create(
                [
                    'equipement_planning_id' => $request->equipement_planning_id,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time
                ]
                );
            

            return [
                "payload" => $equipementplanningworkinghours,
                "message" => "EquipementPlanningWorkingHours created successfully",
                "status" => 201
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
}
