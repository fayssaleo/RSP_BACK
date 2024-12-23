<?php

namespace App\Modules\Planning\Http\Controllers;

use App\Modules\Planning\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanningController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $rules = [
            'shift_id' => 'required',
            'profile_group_id' => 'required',
            'checker_number' => 'nullable|numeric',
            'deckman_number' => 'nullable|numeric',
            'assistant' => 'nullable|boolean',
            'planned_at' => 'nullable'
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
            // Create a new planning record
            $planning = Planning::create(
                [
                    'shift_id' => $request->shift_id,
                    'profile_group_id' => $request->profile_group_id,
                    'checker_number' => $request->checker_number,
                    'deckman_number' => $request->deckman_number,
                    'assistant' => $request->assistant,
                    'planned_at' => $request->planned_at ?? now()
                ]
            );
            return [
                "payload" => $planning,
                "message" => "Planning created successfully",
                "status" => 201
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function getPlanningByRange(Request $request)
    {

        // Validate the request data

        // If validation fails, return error response


        try {
            // Convert the input date to a format suitable for querying

            // Retrieve planning records created on the specified date
            $plannings = Planning::where('profile_group_id',$request->profile_group_id)->get();


            return [
                "payload" => $plannings,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }



    public function getByDate(Request $request)
    {
        $rules = [
            'date' => 'required|date',
            'shift_id' => 'required',
            'profile_group_id' => 'required'
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
            $date = date('Y-m-d', strtotime($request->date));

            // Retrieve planning records created on the specified date
            $planningRecord = Planning::whereDate('planned_at', $date)
                ->where('shift_id', $request->shift_id)
                ->where('profile_group_id', $request->profile_group_id)
                ->latest('id') // Assuming 'id' is the primary key column
                ->first();


            return [
                "payload" => $planningRecord,
                "message" => "Planning record retrieved successfully",
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function deletePlanning(Request $request){

        $rules = [
            'id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                "error" => $validator->errors()->first(),
                "status" => 422
            ];
        }
        $planning = Planning::find($request->id);
        if(!$planning){
            return [
                "error" => "Planning not found",
                "status" => 404
            ];
        }
        $planning->delete();
        return [
            "payload" => $planning,
            "message" => "Planning deleted successfully",
            "status" => 200
        ];
    }
}
