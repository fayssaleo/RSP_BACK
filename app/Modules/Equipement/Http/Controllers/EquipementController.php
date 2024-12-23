<?php

namespace App\Modules\Equipement\Http\Controllers;

use App\Modules\Equipement\Models\Equipement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipementController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function getByProfileGroup(Request $request)
    {
        try {
            $equipements = Equipement::where('profile_group_id',$request->profile_group_id)
            ->where('status',1)
            ->with('profileGroup')
            ->get();
            return [
                "payload" => $equipements,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
    public function index()
    {
        try {
            $equipements = Equipement::with('profileGroup')->get();
            return [
                "payload" => $equipements,
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
        // Define validation rules for equipement registration
        $rules = [
            'matricule' => 'required|string',
            'profile_group_id' => 'required',
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
            // Create the new equipement
            $equipement = Equipement::create([
                'matricule' => $request->matricule,
                'profile_group_id' => $request->profile_group_id,
                'status' => 1
            ]);

            // Generate token for the equipement

            // Return token and equipement in response
            return [
                "payload" => $equipement,
                "message" => "equipement created successfully",
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
            $equipement = Equipement::findOrFail($id);
            $equipement->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "equipement not found",
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
            $equipement = Equipement::findOrFail($id);
            $rules = [
                'matricule' => 'string|max:255'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $equipement->update($request->all());
            return [
                "payload" => $equipement,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Equipement not found",
                "status" => 404
            ];
        }
    }

    public function getById(Request $request)
    {
        $id = $request->input('equipement_id');
        try {
            $equipement = Equipement::with('profileGroup')->findOrFail($id);
            return [
                "payload" => $equipement,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Equipement not found",
                "status" => 404
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
}
