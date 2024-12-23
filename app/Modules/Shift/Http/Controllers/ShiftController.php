<?php

namespace App\Modules\Shift\Http\Controllers;

use App\Modules\Shift\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $shifts = Shift::all();
            return [
                "payload" => $shifts,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }


    public function add(Request $request)
    {
        // Define validation rules for shift registration
        $rules = [
            'category' => 'required|string',
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
            // Create the new shift
            $shift = Shift::create([
                'category' => $request->category,
            ]);

            // Generate token for the shift

            // Return token and shift in response
            return [
                "payload" => $shift,
                "message" => "shift created successfully",
                "status" => 201
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        try {
            $shift = Shift::findOrFail($id);
            $shift->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "shift not found",
                "status" => 404
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $shift = Shift::findOrFail($id);
            $rules = [
                'category' => 'string|max:255'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $shift->update($request->all());
            return [
                "payload" => $shift,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Shift not found",
                "status" => 404
            ];
        }
        
    }

    public function getByCategory(Request $request)
{
    // Define validation rules for category parameter
    $rules = [
        'category' => 'required|string',
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
        // Retrieve shifts by category
        $category = $request->input('category');
        $shifts = Shift::where('category', $category)->get();

        // Check if any shifts were found
        if ($shifts->isEmpty()) {
            return [
                "error" => "No shifts found for the specified category",
                "status" => 404
            ];
        }

        // Return shifts in response
        return [
            "payload" => $shifts,
            "status" => 200
        ];
    } catch (\Exception $e) {
        return [
            "error" => "Internal Server Error",
            "status" => 500
        ];
    }
}

    public function getShiftByTime(){
        $currentTime = Carbon::now();
            $shift = null;
            // Determine the shift category based on the current time
            if ($currentTime->between('07:00', '14:59')) {
                $shift = 'A';
            } elseif ($currentTime->between('15:00', '22:59')) {
                $shift = 'B';
            } elseif ($currentTime->between('23:00', '23:59') || $currentTime->between('00:00', '06:59')) {
                $shift = 'C';
            }
            try {
            $shifts = Shift::where('category', $shift)->get();

            if ($shifts->isEmpty()) {
                return [
                    "error" => "No shifts found for the specified category",
                    "status" => 404
                ];
            }
    
            // Return shifts in response
            return [
                "payload" => $shifts,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
            
    
    }

}
