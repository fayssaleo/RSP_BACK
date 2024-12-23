<?php

namespace App\Modules\Department\Http\Controllers;

use App\Modules\Department\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $departments = Department::all();
            return [
                "payload" => $departments,
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
        // Define validation rules for department registration
        $rules = [
            'name' => 'required|string',
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
            // Create the new department
            $department = Department::create([
                'name' => $request->name,
            ]);

            // Generate token for the department

            // Return token and department in response
            return [
                "payload" => $department,
                "message" => "department created successfully",
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
            $department = Department::findOrFail($id);
            $department->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "department not found",
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
            $department = Department::findOrFail($id);
            $rules = [
                'name' => 'string|max:255'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(), // Get the first validation error message
                    "status" => 422
                ];
            }
            $department->update($request->all());
            return [
                "payload" => $department,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Department not found",
                "status" => 404
            ];
        }
        
    }
}
