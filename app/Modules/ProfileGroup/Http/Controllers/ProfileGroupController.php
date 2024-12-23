<?php

namespace App\Modules\ProfileGroup\Http\Controllers;

use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileGroupController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $profilegroups = ProfileGroup::all();
            return [
                "payload" => $profilegroups,
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
        // Define validation rules for profilegroup registration
        $rules = [
            'type' => 'required|string',
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
            // Create the new profilegroup
            $profilegroup = ProfileGroup::create([
                'type' => $request->type,
            ]);

            // Generate token for the profilegroup

            // Return token and profilegroup in response
            return [
                "payload" => $profilegroup,
                "message" => "profilegroup created successfully",
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
            $profilegroup = ProfileGroup::findOrFail($id);
            $profilegroup->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "profilegroup not found",
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
            $profilegroup = ProfileGroup::findOrFail($id);
            $rules = [
                'type' => 'string|max:255'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $profilegroup->update($request->all());
            return [
                "payload" => $profilegroup,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "ProfileGroup not found",
                "status" => 404
            ];
        }
        
    }

    public function getByType(Request $request)
    {
        // Define validation rules for type parameter
        $rules = [
            'type' => 'required|string',
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
            // Retrieve profilegroups by type
            $type = $request->input('type');
            $profilegroups = ProfileGroup::where('type', $type)->get();
    
            // Check if any profilegroups were found
            if ($profilegroups->isEmpty()) {
                return [
                    "error" => "No profilegroups found for the specified type",
                    "status" => 404
                ];
            }
    
            // Return profilegroups in response
            return [
                "payload" => $profilegroups,
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
