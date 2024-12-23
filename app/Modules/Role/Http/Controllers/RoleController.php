<?php

namespace App\Modules\Role\Http\Controllers;

use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return [
                "payload" => $roles,
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
        // Define validation rules for role registration
        $rules = [
            'name' => 'required|string',
            'department_id' => 'required',
            'sub_category' => 'nullable|string',
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
            // Create the new role
            $role = Role::create([
                'name' => $request->name,
                'sub_category' => $request->sub_category,
                'department_id' => $request->department_id
            ]);

            // Generate token for the role

            // Return token and role in response
            return [
                "payload" => $role,
                "message" => "role created successfully",
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
            $role = Role::findOrFail($id);
            $role->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "role not found",
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
            $role = Role::findOrFail($id);
            $rules = [
                'name' => 'string|max:255',
                'sub_category' => 'string|max:255'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $role->update($request->all());
            return [
                "payload" => $role,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Role not found",
                "status" => 404
            ];
        }
        
    }
}
