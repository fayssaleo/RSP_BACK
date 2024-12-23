<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\Role\Models\Role;
use App\Modules\Shift\Models\Shift;
use App\Modules\User\Models\CountWhHistory;
use App\Modules\User\Models\User;
use App\Modules\User\Models\WhHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function wh_index()
    {
        $payload_replay = [];
        try {
            for ($i = 1; $i < 5; $i++) {
                for ($j = 1; $j < 5; $j++) {
                    $users = User::where('shift_id', $i)
                        ->where('profile_group_id', $j)
                        ->with(['shift', 'profileGroup', 'role'])
                        ->orderBy('workingHours', 'asc')
                        ->get();
                    if (count($users) > 0) {
                        //return $users;
                        $equipment_type = $j;
                        $shift = $i;
                        $min = $users[0]->workingHours;
                        $max = $users[count($users) - 1]->workingHours;
                        $last = 'empty';
                        $last_ = WhHistory::where('shift_id', $i)
                            ->where('profile_group_id', $j)
                            ->get();
                        if (count($last_) > 0) {
                            $last = $last_[count($last_) - 1];
                        }
                        array_push(
                            $payload_replay,
                            [
                                'min' => $min,
                                'max' => $max,
                                'last' => $last,
                                'equipment_type' => $equipment_type,
                                'shift' => $shift
                            ]
                        );
                    } else {
                        array_push(
                            $payload_replay,
                            [
                                'min' => 0,
                                'max' => 0,
                                'last' => 'empty',
                                'equipment_type' => $j,
                                'shift' => $i
                            ]
                        );
                    }
                }
            }
            return [
                "payload" => $payload_replay,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
    public function WHReset_manual_(Request $request)
    {

        $users = User::where('profile_group_id', $request->profile_group_id)
            ->where('shift_id', $request->shift_id)
            ->with(['shift', 'profileGroup', 'role'])
            ->orderBy('workingHours', 'asc')
            ->get();

        if (count($users) > 0) {

            //return $users;

            $numberToMince = 0;
            for ($k = 0; $k < count($users); $k++) {
                if ($users[$k]->workingHours <= 0) {
                    $users[$k]->workingHours = 0;
                    $users[$k]->save();
                } else {
                    $numberToMince = $users[$k]->workingHours;
                    $users[$k]->workingHours = 0;
                    $users[$k]->save();
                    break;
                }
            }
            for ($k = 0; $k < count($users); $k++) {
                if ($users[$k]->workingHours <= 0) {
                    $users[$k]->workingHours = 0;
                } else {
                    $users[$k]->workingHours = $users[$k]->workingHours - $numberToMince;
                    $users[$k]->save();
                }
            }
            WhHistory::create([
                'shift_id' => $request->shift_id,
                'profile_group_id' => $request->profile_group_id,
                'user_id' => $request->user_id,
                'resetedBy' => "user",
                'min' => $request->min,
                'max' => $request->max,
                'tobeMinced' => $numberToMince,
            ]);
        }

        $payload_replay = [];
        for ($i = 1; $i < 5; $i++) {
            for ($j = 1; $j < 5; $j++) {
                $users = User::where('shift_id', $i)
                    ->where('profile_group_id', $j)
                    ->with(['shift', 'profileGroup', 'role'])
                    ->orderBy('workingHours', 'asc')
                    ->get();
                if (count($users) > 0) {
                    //return $users;
                    $equipment_type = $j;
                    $shift = $i;
                    $min = $users[0]->workingHours;
                    $max = $users[count($users) - 1]->workingHours;
                    $last = 'empty';
                    $last_ = WhHistory::where('shift_id', $i)
                        ->where('profile_group_id', $j)
                        ->get();
                    if (count($last_) > 0) {
                        $last = $last_[count($last_) - 1];
                    }
                    array_push(
                        $payload_replay,
                        [
                            'min' => $min,
                            'max' => $max,
                            'last' => $last,
                            'equipment_type' => $equipment_type,
                            'shift' => $shift
                        ]
                    );
                } else {
                    array_push(
                        $payload_replay,
                        [
                            'min' => 0,
                            'max' => 0,
                            'last' => 'empty',
                            'equipment_type' => $j,
                            'shift' => $i
                        ]
                    );
                }
            }
        }
        $users = User::get();
        return [
            "payload" => $payload_replay,
            "usersList" => $users,
            "status" => 200
        ];
    }
    public function mensuelWHReset(Request $request)
    {
        try {
            for ($i = 0; $i < count($request->profile_groups); $i++) {
                for ($j = 0; $j < count($request->shifts); $j++) {
                    $users = User::where('profile_group_id', $request->profile_groups[$i])
                        ->where('shift_id', $request->shifts[$j])
                        ->with(['shift', 'profileGroup', 'role'])
                        ->orderBy('workingHours', 'asc')
                        ->get();
                    if (count($users) > 0) {
                        //return $users;
                        $numberToMince = 0;
                        $min = 0;
                        $max = 0;
                        for ($k = 0; $k < count($users); $k++) {
                            if($users[$k]->workingHours<$min)
                            $min=$users[$k]->workingHours;
                            if($users[$k]->workingHours>$max)
                            $max=$users[$k]->workingHours;


                            if ($users[$k]->workingHours <= 0) {
                                $users[$k]->workingHours = 0;
                                $users[$k]->save();
                            } else {
                                $numberToMince = $users[$k]->workingHours;
                                $users[$k]->workingHours = 0;
                                $users[$k]->save();
                                break;
                            }
                        }
                        for ($k = 0; $k < count($users); $k++) {
                            if ($users[$k]->workingHours <= 0) {
                                $users[$k]->workingHours = 0;
                            } else {
                                $users[$k]->workingHours = $users[$k]->workingHours - $numberToMince;
                                $users[$k]->save();
                            }
                        }
                        WhHistory::create([
                            'shift_id' => $request->shifts[$j],
                            'profile_group_id' => $request->profile_groups[$i],
                            'user_id' => null,
                            'resetedBy' => 'SYSTEM',
                            'min' => $min,
                            'max' => $max,
                            'tobeMinced' => $numberToMince,
                        ]);
                    }
                }
            }
            return [
                "payload" => $users,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "method" => "mensuelWHReset",
                "error" => $e,
                "request" => $request->all(),
                "status" => 500
            ];
        }
    }
    public function addFromAPI(Request $request)
    {
        for ($i = 1; $i < count($request->payload); $i++) {
            try {
                if ($request->payload[$i]["profile_groups"][0]["id"] == 1 || $request->payload[$i]["profile_groups"][0]["id"] == 2) {
                    $user = User::create([
                        'matricule' => $request->payload[$i]["username"],
                        'firstname' => $request->payload[$i]["firstName"],
                        'lastname' => $request->payload[$i]["lastName"],
                        'email' => ($request->payload[$i]["email"]) ? $request->payload[$i]["email"] : $request->payload[$i]["id"] . 'test@test.com',
                        'shift_id' => rand(1, 4),
                        'profile_group_id' => ($request->payload[$i]["profile_groups"][0]["id"]) ? $request->payload[$i]["profile_groups"][0]["id"] : 1,
                        'role_id' => 2,
                        'workingHours' => 0,
                        'wh_global' => 0,
                        'sby_workingHours' => 0,
                        'checker_workingHours' => 0,
                        'deckman_workingHours' => 0,
                        'assistant_workingHours' => 0,
                        'password' => Hash::make("Initial123")

                    ]);
                }
            } catch (\Throwable $th) {
                dd($th);
            }
        }
    }
    public function index()
    {
        try {
            $users = User::with(['shift', 'profileGroup', 'role', 'department'])->get();
            return [
                "payload" => $users,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
    public function WhHistory_details_index(Request $request)
    {
        try {
            $wh_details_sh_gp = WhHistory::where('shift_id', $request->shift_id)
            ->where('profile_group_id', $request->profile_group_id)
            ->with("user")
            ->orderBy('created_at', 'desc')
            ->get();
            $count_wh_details_sh_gp = CountWhHistory::where('shift_id', $request->shift_id)
            ->where('profile_group_id', $request->profile_group_id)
            ->with("user")
            ->orderBy('created_at', 'desc')
            ->get();
            $users_wh_details_sh_gp = User::where('shift_id', $request->shift_id)
            ->where('profile_group_id', $request->profile_group_id)
            ->orderBy('workingHours', 'desc')
            ->get();

            return [
                "wh_details_sh_gp" => $wh_details_sh_gp,
                "count_wh_details_sh_gp" => $count_wh_details_sh_gp,
                "users_wh_details_sh_gp" => $users_wh_details_sh_gp,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "WhHistory_details_index Error",
                "request" => $request,
                "status" => 500
            ];
        }
    }
    public function COUNT_WhHistory_details_index(Request $request)
    {
        try {
            $count_shift_groupèwh_history = CountWhHistory::where('shift_id', $request->shift_id)
            ->where('profile_group_id', $request->profile_group_id)
            ->with("user")
            ->orderBy('created_at', 'desc')
            ->get();
            return [
                "payload" => $count_shift_groupèwh_history,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => "COUNT_WhHistory_details_index Error",
                "request" => $request,
                "status" => 500
            ];
        }
    }
    public function getById(Request $request)
    {
        $id = $request->input('user_id');
        try {
            $user = User::with(['shift', 'profileGroup', 'role'])->findOrFail($id);
            return [
                "payload" => $user,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
    public function login(Request $request)
    {

        // Define validation rules
        $rules = [
            'matricule' => 'required|string',
            'password' => 'required|string',
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
            // Attempt to authenticate the user
            if (Auth::attempt($request->only('matricule', 'password'))) {
                $user = Auth::user();
                // Retrieve the authenticated user
                if ($user->isactive == 1) {

                    // Generate token for the user
                    $token = $user->createToken('auth-token')->plainTextToken;
                    $user->load('role');

                    // Return token in response
                    return [
                        'payload' => ['user' => $user, 'token' => $token, 'role' => $user->role],
                        'status' => 200
                    ];
                } else {
                    return [
                        'error' => 'User is not active',
                        'status' => 403
                    ];
                }
            }

            // If authentication fails, return error response
            return [
                'error' => 'Unauthorized',
                'status' => 401
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function registerAll(Request $request)
    {
        $success = [];
        $fails = [];
        $updated = [];

        for ($i = 0; $i < count($request->all()); $i++) {

            // Check if user with the same matricule exists
            $existingUser = User::where('matricule', $request[$i]["matricule"])->first();

            if ($existingUser) {
                // Update the existing user's details
                $existingUser->update([
                    'firstname' => isset($request[$i]["firstname"]) ? $request[$i]["firstname"] : $existingUser->firstname,
                    'lastname' => isset($request[$i]["lastname"]) ? $request[$i]["lastname"] : $existingUser->lastname,
                    'email' => isset($request[$i]["email"]) ? $request[$i]["email"] : $existingUser->email,
                    'shift_id' => isset($request[$i]["shift_id"]) ? $request[$i]["shift_id"] : $existingUser->shift_id,
                    'profile_group_id' => isset($request[$i]["profile_group_id"]) ? $request[$i]["profile_group_id"] : $existingUser->profile_group_id,
                    'role_id' => isset($request[$i]["role_id"]) ? $request[$i]["role_id"] : $existingUser->role_id,
                    'workingHours' => isset($request[$i]["workingHours"]) ? $request[$i]["workingHours"] : $existingUser->workingHours,
                    'wh_global' => isset($request[$i]["wh_global"]) ? $request[$i]["wh_global"] : $existingUser->wh_global,
                    'sby_workingHours' => isset($request[$i]["sby_workingHours"]) ? $request[$i]["sby_workingHours"] : $existingUser->sby_workingHours,
                    'checker_workingHours' => isset($request[$i]["checker_workingHours"]) ? $request[$i]["checker_workingHours"] : $existingUser->checker_workingHours,
                    'deckman_workingHours' => isset($request[$i]["deckman_workingHours"]) ? $request[$i]["deckman_workingHours"] : $existingUser->deckman_workingHours,
                    'assistant_workingHours' => isset($request[$i]["assistant_workingHours"]) ? $request[$i]["assistant_workingHours"] : $existingUser->assistant_workingHours,
                ]);

                // Add to updated array after updating
                array_push($updated, $existingUser);
            } else {
                // Define validation rules for new user
                $validator = Validator::make($request[$i], [
                    'matricule' => 'required|unique:users',
                ]);

                if ($validator->fails()) {
                    // Add to fails if validation fails
                    array_push($fails, $request[$i]);
                } else {
                    try {
                        // Create the new user
                        $user = User::create([
                            'matricule' => $request[$i]["matricule"],
                            'firstname' => isset($request[$i]["firstname"]) ? $request[$i]["firstname"] : null,
                            'lastname' => isset($request[$i]["lastname"]) ? $request[$i]["lastname"] : null,
                            'email' => isset($request[$i]["email"]) ? $request[$i]["email"] : null,
                            'shift_id' => isset($request[$i]["shift_id"]) ? $request[$i]["shift_id"] : null,
                            'profile_group_id' => isset($request[$i]["profile_group_id"]) ? $request[$i]["profile_group_id"] : null,
                            'role_id' => isset($request[$i]["role_id"]) ? $request[$i]["role_id"] : null,
                            'workingHours' => isset($request[$i]["workingHours"]) ? $request[$i]["workingHours"] : null,
                            'wh_global' => isset($request[$i]["wh_global"]) ? $request[$i]["wh_global"] : null,
                            'sby_workingHours' => isset($request[$i]["sby_workingHours"]) ? $request[$i]["sby_workingHours"] : null,
                            'checker_workingHours' => isset($request[$i]["checker_workingHours"]) ? $request[$i]["checker_workingHours"] : null,
                            'deckman_workingHours' => isset($request[$i]["deckman_workingHours"]) ? $request[$i]["deckman_workingHours"] : null,
                            'assistant_workingHours' => isset($request[$i]["assistant_workingHours"]) ? $request[$i]["assistant_workingHours"] : null,
                            'password' => Hash::make("Initial123"),
                        ]);

                        // Add to success array
                        array_push($success, $request[$i]);
                    } catch (\Exception $e) {
                        array_push($fails, $request[$i]);
                    }
                }
            }
        }

        // Return response with success, fails, and updated users
        return [
            'success' => $success,
            'fails' => $fails,
            'updated' => $updated,
            'status' => 200
        ];
    }
    public function register(Request $request)
    {
        // Define validation rules for user registration
        $rules = [
            'matricule' => 'required|unique:users',
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
            // Create the new user
            $user = User::create([
                'matricule' => $request->matricule,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'shift_id' => $request->shift_id,
                'profile_group_id' => $request->profile_group_id,
                'role_id' => $request->role_id,
                'workingHours' => $request->workingHours,
                'wh_global' => $request->wh_global,
                'sby_workingHours' => $request->sby_workingHours,
                'checker_workingHours' => $request->checker_workinghours,
                'deckman_workingHours' => $request->checker_workinghours,
                'assistant_workingHours' => $request->checker_workinghours,
                'password' => Hash::make("123456")

            ]);

            return [
                "payload" => $user,
                "message" => "User created successfully",
                "status" => 201
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function logout(Request $request)
    {
        try {
            // Récupérer l'utilisateur actuellement authentifié

            // Supprimer tous les tokens d'authentification de l'utilisateur
            //  $user->tokens()->delete();
            auth()->user()->tokens()->delete();
            $user = Auth::user();
            // Déconnecter l'utilisateur
            // Auth::logout();

            return [
                'message' => 'User logged out successfully',
                'user' => $user,
                'status' => 200
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
            $user = User::findOrFail($id);
            $user->delete();
            return [
                "payload" => "Deleted successfully",
                "status" => 204
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        } catch (\Exception $e) {
            return [
                "error" => "Internal Server Error",
                "status" => 500
            ];
        }
    }
    public function updatePassword(Request $request)
    {
        $oldPassword = $request->input('old_password');
        $newPassword = $request->input('new_password');
        $rules = [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
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
            $user = Auth::user();
            if (Hash::check($oldPassword, $user->password)) {
                $hashedPassword = Hash::make($newPassword);
                $user->password = $hashedPassword;
                $user->save();

                return [
                    "message" => "Password updated successfully",
                    "status" => 200
                ];
            } else {
                return [
                    "error" => "Old password is incorrect",
                    "status" => 400
                ];
            }
        } catch (\Exception $e) {
            // Return error response if user is not found or any other exception occurs
            return [
                "error" => "Error updating password: " . $e->getMessage(),
                "status" => 500
            ];
        }
    }
    public function resetPassword(Request $request)
    {
        $id = $request->input('id');
        // Validate the request data

        try {
            $user = User::findOrFail($id);
            $hashedPassword = Hash::make("Initial1234");
            $user->password = $hashedPassword;
            $user->save();
            return [
                "payload" => $user,
                "message" => "Password updated successfully",
                "status" => 200
            ];
        } catch (\Exception $e) {
            // Return error response if user is not found or any other exception occurs
            return [
                "error" => "Error updating password: " . $e->getMessage(),
                "status" => 500
            ];
        }
    }
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return [
                "payload" => $user,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        }
    }
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');

            $user = User::with(['shift', 'profileGroup', 'role', 'department'])->findOrFail($id);
            $rules = [
                'matricule' => [
                    'string',
                    'max:255',
                    Rule::unique('users', 'matricule')
                    ->ignore($user->id),
                ],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(),
                    "status" => 422
                ];
            }

            $input = $request->all();
            $input['isactive']=($input['isactive']==true || $input['isactive']==1)?1:0;
            //$user->update($input);

            $user->matricule = $request->input('matricule');
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->isactive = $request->input('isactive') == true ? 1 : 0;
            $user->email = $request->input('email');
            if($request->input('shift_id')!='null')
            $user->shift_id = $request->input('shift_id');
            else
            $user->shift_id = null;
            $user->role_id = $request->input('role_id');
            $user->workingHours = $request->input('workingHours');
            $user->wh_global = $request->input('wh_global');
            $user->sby_workingHours = $request->input('sby_workingHours');
            $user->checker_workingHours = $request->input('checker_workingHours');
            $user->deckman_workingHours = $request->input('deckman_workingHours');
            $user->assistant_workingHours = $request->input('assistant_workingHours');
            if($request->input('profile_group_id')!='null')
            $user->profile_group_id = $request->input('profile_group_id');
            else
            $user->profile_group_id = null;
            $user->save();

            if($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()){
                $oldFileName = $user->profile_picture;
                $fileName = time().'.'.$request->profile_picture->extension();
                $request->profile_picture->move(public_path('uploads'), $fileName);

                // Delete old image file
                if ($oldFileName && file_exists(public_path('uploads/' . $oldFileName))) {
                    @unlink(public_path('uploads/' . $oldFileName));
                }

                $user->profile_picture = $fileName;
                $user->save();
            }

            $user->refresh();

            return [
                "payload" => $user,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        }
    }
    public function getDriversActiveList_byF(Request $request)
    {
        try {
            $users = User::where('shift_id', $request->shift_id)
                ->where('profile_group_id', $request->profile_group)
                ->where('role_id', $request->role_id)
                ->get();
            return [
                "payload" => $users,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        }
    }
    public function getDriversActiveList_all(Request $request)
    {
        try {
            $users = User::where('profile_group_id', $request->profile_group_id)
                ->where('role_id', $request->role_id)
                ->get();
            return [
                "payload" => $users,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "User not found",
                "status" => 404
            ];
        }
    }
    public function getDrivers(Request $request)
    {
        $shift = null;
        $shiftTwo = null;
        $profileGroupName = null;
        $roleName = null;
        $requestInputId = $request->input('shift_id');
        // $got = "A";
        try {
            if ($request->has('shift_id')) {
                $shiftTwo = Shift::findOrFail($request->input('shift_id'));
                $shift = $shiftTwo->category;
            } else {
                $currentTime = Carbon::now();
                // Determine the shift category based on the current time
                if ($currentTime->between('07:00', '14:59')) {
                    $shift = 'A';
                } elseif ($currentTime->between('15:00', '22:59')) {
                    $shift = 'B';
                } elseif ($currentTime->between('23:00', '23:59') || $currentTime->between('00:00', '06:59')) {
                    $shift = 'C';
                }
            }
            $profileGroupName = $request->input('profile_group');
            $roleName = $request->input('role');

            // If shift category is determined, fetch profile group ID and role ID
            if ($shift && $profileGroupName && $roleName) {
                $profileGroupId = ProfileGroup::where('type', $profileGroupName)->value('id');
                $roleId = Role::where('name', $roleName)->value('id');

                // Retrieve users for the specified shift, profile group, and role
                $users = User::whereHas('shift', function ($query) use ($shift) {
                    $query->where('category', $shift);
                })->where('profile_group_id', $profileGroupId)
                    ->where('role_id', $roleId)
                    ->get();

                return [
                    "payload" => $users,
                    "addedValue" => $shiftTwo,
                    "shift" => $shift,
                    "status" => 200
                ];
            } else {
                return [
                    "error" => "Shift category, profile group, or role could not be determined.",
                    "status" => 404
                ];
            }
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Shift category, profile group, or role could not be determined.",
                "status" => 404
            ];
        }
    }

    public function add_user(Request $request)
{
    try {
        // Validate input
        $rules = [
            'matricule' => 'string|max:255|unique:users,matricule',
            // Add other fields as necessary
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                "error" => $validator->errors()->first(),
                "status" => 422
            ];
        }

        // Create new user instance
        $user = new User();
        $user->matricule = $request->input('matricule');
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->isactive = $request->input('isactive');
        $user->email = $request->input('email');
        $user->shift_id = $request->input('shift_id') != 'null' ? $request->input('shift_id') : null;
        $user->role_id = $request->input('role_id');
        $user->profile_group_id = $request->input('profile_group_id') != 'null' ? $request->input('profile_group_id') : null;
        $user->workingHours = $request->input('workingHours');
        $user->wh_global = $request->input('wh_global');
        $user->sby_workingHours = $request->input('sby_workingHours');
        $user->checker_workingHours = $request->input('checker_workingHours');
        $user->deckman_workingHours = $request->input('deckman_workingHours');
        $user->assistant_workingHours = $request->input('assistant_workingHours');
        $user->password = Hash::make("Initial123");
        $user->save();

        // Handle file upload
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $fileName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads'), $fileName);
            $user->profile_picture = $fileName;
            $user->save();
        }

        $user->refresh();
        $user->role=$user->role;
        $user->shift=$user->shift;
        $user->department=$user->department;
        return [
            "payload" => $user,
            "status" => 200
        ];

    } catch (Exception $e) {
        return [
            "error" => "Failed to create user: " . $e->getMessage(),
            "status" => 500
        ];
    }
}

}
