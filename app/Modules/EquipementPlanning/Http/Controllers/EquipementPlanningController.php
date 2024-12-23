<?php

namespace App\Modules\EquipementPlanning\Http\Controllers;

use App\Modules\EquipementPlanning\Models\EquipementPlanning;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipementPlanningController
{
    public function add(Request $request)
    {
        $rules = [
            'equipement_id' => 'required',
            'planning_id' => 'required',
            'stopped_at' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'reason' => 'nullable|string',
            'subcontract ' => 'nullable|string'
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
            // Create a new equipementplanning record
            $equipementplanning = EquipementPlanning::create(
                [
                    'equipement_id' => $request->equipement_id,
                    'planning_id' => $request->planning_id,
                    'stopped_at' => $request->stopped_at,
                    'reason' => $request->reason,
                    'subcontract' => $request->subcontract
                ]
                );


            return [
                "payload" => $equipementplanning,
                "message" => "EquipementPlanning created successfully",
                "status" => 201
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $equipementplanning = EquipementPlanning::findOrFail($id);
            $rules = [
                'stopped_at' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'reason' => 'nullable|string',
                'subcontract ' => 'nullable|string'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $equipementplanning->update($request->all());
            return [
                "payload" => $equipementplanning,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "EquipementPlanning not found",
                "status" => 404
            ];
        }

    }

    public function getByPlanning(Request $request)
    {
        $rules = [
            'planning_id' => 'required',
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
            // Convert the input date to a format suitable for querying

            // Retrieve planning records created on the specified date
            $userPlannings = EquipementPlanning::where('planning_id', $request->planning_id)
            ->with(['equipementPlanningWorkingHours','equipement'])
            ->get();

            return [
                "payload" => $userPlannings,
                "message" => "Equipements retrieved successfully",
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
}
