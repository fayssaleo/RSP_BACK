<?php

namespace App\Modules\UserPlanning\Http\Controllers;

use App\Modules\User\Models\User;
use App\Modules\UserPlanning\Models\UserPlanning;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPlanningController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'planning_id' => 'required',
            'departure_at' => 'nullable|float',
            'reason' => 'nullable|string',
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
            // Create a new userplanning record
            $userplanning = UserPlanning::create(
                [
                    'user_id' => $request->user_id,
                    'planning_id' => $request->planning_id,
                    'departure_at' => $request->departure_at,
                    'reason' => $request->reason
                ]
            );


            return [
                "payload" => $userplanning,
                "message" => "UserPlanning created successfully",
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
            $userplanning = UserPlanning::findOrFail($id);
            $rules = [
                'departure_at' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'reason' => 'nullable|string',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $userplanning->update($request->all());
            return [
                "payload" => $userplanning,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "UserPlanning not found",
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
            $userPlannings = UserPlanning::where('planning_id', $request->planning_id)->get();

            return [
                "payload" => $userPlannings,
                "message" => "Users retrieved successfully",
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
