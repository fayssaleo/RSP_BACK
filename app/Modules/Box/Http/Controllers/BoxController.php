<?php

namespace App\Modules\Box\Http\Controllers;

use App\Modules\Box\Models\Box;
use App\Modules\Equipement\Models\Equipement;
use App\Modules\Planning\Models\Planning;
use App\Modules\User\Models\CountWhHistory;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoxController
{
    function calculateTotalHours($periods) {
        $totalHours = 0;

        foreach ($periods as $period) {
            list($start, $end) = explode('-', $period);

            $startTime = strtotime($start);
            $endTime = strtotime($end);

            // Check if the end time is before the start time, indicating a period crossing midnight
            if ($endTime <= $startTime) {
                $endTime += 86400; // Add 24 hours (86400 seconds) to the end time
            }

            $hours = ($endTime - $startTime) / 3600;
            $totalHours += $hours;
        }

        return floor($totalHours * 100) / 100;
    }
    public function setWHAction_automatically(){
        try {
            // Create a new planning record
            $plannings = Planning::where('wh_set', 0)
                    ->whereDate('planned_at', '<=', now())
                    ->with('boxes')
                    ->get();
            for ($pi=0; $pi < count($plannings); $pi++) {
                $planningBoxes=$this->getUsersFromBoxes($plannings[$pi]->boxes);
                $driversList=[];
                $boxes = explode('.', $plannings[$pi]->planning_header);
                for ($i=0; $i <count($planningBoxes) ; $i++) {
                    $driver = User::where('id',$planningBoxes[$i][0]->id)->first();
                    if (!$driver) {
                        return [
                            "payload" => "driver not found",
                            "status" => 404
                        ];
                    }
                    $periodes=[];
                    for($k=1; $k <count($planningBoxes[$i]) ; $k++) {
                        if($planningBoxes[$i][$k]!="P" && $planningBoxes[$i][$k]!="DP"){
                            array_push($periodes,$boxes[$k-1]);

                        }
                    }
                    $driver->workingHours=(floor($driver->workingHours * 100) / 100)+$this->calculateTotalHours($periodes);
                    $driver->wh_global=(floor($driver->wh_global * 100) / 100)+$this->calculateTotalHours($periodes);
                    $driver->save();
                    array_push($driversList,$driver);
                }
                $plannings[$pi]->wh_set=true;
                $plannings[$pi]->save();
                CountWhHistory::create([
                    'shift_id' => $plannings[$pi]->shift_id,
                    'profile_group_id' => $plannings[$pi]->profile_group_id,
                    'user_id' => null,
                    'resetedBy' => "SYSTEM",
                    'type' => "ADD",
                    'planning' => Carbon::parse($plannings[$pi]->planned_at)->format('d/m/Y') .' '.$plannings[$pi]->shift_periode

                ]);
            }







            return [
                'payload' => count($plannings)." row(s) affected",
                'status' => 200
            ];



        } catch (\Exception $e) {
            return [
                'method' => 'setWHAction_automatically',
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function setWHAction(Request $request){
        try {
            // Create a new planning record
            $planning = Planning::where('id',$request->id)
                                    ->with("boxes")
                                    ->first();
            if (!$planning) {
                return [
                    "payload" => "planning not found",
                    "status" => 404
                ];
            }
            $planningBoxes=$this->getUsersFromBoxes($planning->boxes);
            $driversList=[];
            $boxes = explode('.', $planning->planning_header);

            for ($i=0; $i <count($planningBoxes) ; $i++) {
                $driver = User::where('id',$planningBoxes[$i][0]->id)->first();
                if (!$driver) {
                    return [
                        "payload" => "driver not found",
                        "status" => 404
                    ];
                }
                $periodes=[];
                for($k=1; $k <count($planningBoxes[$i]) ; $k++) {
                    if($planningBoxes[$i][$k]!="P" && $planningBoxes[$i][$k]!="DP"){
                        array_push($periodes,$boxes[$k-1]);

                    }
                }
                $driver->workingHours=(floor($driver->workingHours * 100) / 100)+$this->calculateTotalHours($periodes);
                $driver->wh_global=(floor($driver->wh_global * 100) / 100)+$this->calculateTotalHours($periodes);
                $driver->save();
                array_push($driversList,$driver);
            }
            $planning->wh_set=true;
            $planning->save();
            CountWhHistory::create([
                'shift_id' => $planning->shift_id,
                'profile_group_id' => $planning->profile_group_id,
                'user_id' => $request->user_id,
                'resetedBy' => "user",
                'type' => "ADD",
                'planning' => Carbon::parse($planning->planned_at)->format('d/m/Y') .' '.$planning->shift_periode

            ]);
            return [
                'payload' => $driversList,
                'status' => 200
            ];



        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function UnSetWHAction(Request $request){
        try {
            // Create a new planning record
            $planning = Planning::where('id',$request->id)
                                    ->with("boxes")
                                    ->first();
            if (!$planning) {
                return [
                    "payload" => "planning not found",
                    "status" => 404
                ];
            }
            $planningBoxes=$this->getUsersFromBoxes($planning->boxes);
            $driversList=[];
            $boxes = explode('.', $planning->planning_header);

            for ($i=0; $i <count($planningBoxes) ; $i++) {
                $driver = User::where('id',$planningBoxes[$i][0]->id)->first();
                if (!$driver) {
                    return [
                        "payload" => "driver not found",
                        "status" => 404
                    ];
                }
                $periodes=[];
                for($k=1; $k <count($planningBoxes[$i]) ; $k++) {
                    if($planningBoxes[$i][$k]!="P" && $planningBoxes[$i][$k]!="DP"){
                        array_push($periodes,$boxes[$k-1]);

                    }
                }
                $driver->workingHours=(floor($driver->workingHours * 100) / 100)-$this->calculateTotalHours($periodes);
                $driver->wh_global=(floor($driver->wh_global * 100) / 100)-$this->calculateTotalHours($periodes);
                $driver->save();
                array_push($driversList,$driver);
            }
            $planning->wh_set=false;
            $planning->save();
            CountWhHistory::create([
                'shift_id' => $planning->shift_id,
                'profile_group_id' => $planning->profile_group_id,
                'user_id' => $request->user_id,
                'resetedBy' => "user",
                'type' => "REMOVE",
                'planning' => Carbon::parse($planning->planned_at)->format('d/m/Y') .' '.$planning->shift_periode
            ]);
            return [
                'payload' => $driversList,
                'status' => 200
            ];



        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function updatePlanningAndBoxes(Request $request){
        try {
            // Create a new planning record
            $planning = Planning::where('id',$request->planning['id'], $request->id)->first();
            if (!$planning) {
                return [
                    "payload" => "planning not found",
                    "status" => 404
                ];
            }
            $planning->planning_header=$request->planning_header;
            $planning->save();
            $planning->wh_set=$planning->wh_set;
            $planning->shift=$planning->shift;
            $planning->shift_periode_index=$this->getShiftPeriode($planning->shift_periode);
            $planning->boxes()->delete();

            for ($i = 1; $i < count($request->planning_boxes); $i++) {
                for ($j = 1; $j < count($request->planning_boxes[$i]); $j++) {

                    if (
                        $request->planning_boxes[$i][$j] != null &&
                        $request->planning_boxes[$i][$j] != "P" &&
                        $request->planning_boxes[$i][$j] != "DP"
                    )
                        try {
                            // Create a new box record
                            $box = Box::create(
                                [
                                    'start_time' => explode("-", $request->planning_boxes[0][$j]["title"])[0],
                                    'ends_time' => explode("-", $request->planning_boxes[0][$j]["title"])[1],
                                    'break' => 0,
                                    'role' => null,
                                    'user_id' => $request->planning_boxes[$i][0]["id"],
                                    'planning_id' => $planning->id,
                                    'equipement_id' => $request->planning_boxes[$i][$j]["id"]
                                ]
                            );
                        } catch (\Exception $e) {
                            return [
                                'payload' => $e->getMessage(),
                                'status' => 200
                            ];
                        }
                    else {
                        try {
                            // Create a new box record
                            $doubleBreak=false;
                            if($request->planning_boxes[$i][$j] == "DP"){
                                $doubleBreak=true;
                            }
                            $box = Box::create(
                                [
                                    'start_time' => explode("-", $request->planning_boxes[0][$j]["title"])[0],
                                    'ends_time' => explode("-", $request->planning_boxes[0][$j]["title"])[1],
                                    'break' => 1,
                                    'doubleBreak' => $doubleBreak,
                                    'role' => null,
                                    'user_id' => $request->planning_boxes[$i][0]["id"],
                                    'planning_id' => $planning->id

                                ]
                            );
                        } catch (\Exception $e) {
                            return [
                                'payload' => $e->getMessage(),
                                'status' => 200
                            ];
                        }
                    }
                }
            }

            return [
                'payload' => [
                    'planning' => $planning,
                    'planning_boxes' => $planning->boxes,
                ],
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function addPlanningAndBoxes(Request $request)
    {
        $planning_test = Planning::
        where('shift_id',$request->planning['shift_id'])
        ->whereDate('planned_at',$request->planning['planned_at'])
        ->first();
        if ($planning_test) {
            return [
                "payload" => 'This planning is already existed',
                "status" => 505
            ];
        }
        //firstCreateThePlanning
        try {
            // Create a new planning record
            $planning = Planning::create(
                [
                    'shift_id' => $request->planning['shift_id'],
                    'profile_group_id' => $request->planning['profile_group_id'],
                    'checker_number' => $request->planning['checker_number'] ?? null,
                    'deckman_number' => $request->planning['deckman_number'] ?? null,
                    'assistant' => $request->planning['assistant'] ?? null,
                    'planned_at' => $request->planning['planned_at'],
                    'shift_periode' => $request->planning['shift_periode'],
                    'planning_header' => $request->planning_header,
                    'wh_set' => $request->wh_set,
                ]
            );
            for ($i = 1; $i < count($request->planning_boxes); $i++) {
                for ($j = 1; $j < count($request->planning_boxes[$i]); $j++) {

                    if (
                        $request->planning_boxes[$i][$j] != null &&
                        $request->planning_boxes[$i][$j] != "P" &&
                        $request->planning_boxes[$i][$j] != "DP"
                    )
                        try {
                            // Create a new box record
                            $box = Box::create(
                                [
                                    'start_time' => explode("-", $request->planning_boxes[0][$j]["title"])[0],
                                    'ends_time' => explode("-", $request->planning_boxes[0][$j]["title"])[1],
                                    'break' => 0,
                                    'role' => null,
                                    'user_id' => $request->planning_boxes[$i][0]["id"],
                                    'planning_id' => $planning->id,
                                    'equipement_id' => $request->planning_boxes[$i][$j]["id"]
                                ]
                            );
                        } catch (\Exception $e) {
                            return [
                                'payload' => "rf",
                                'status' => 10
                            ];
                        }
                    else {
                        try {
                            // Create a new box record
                            $doubleBreak=false;
                            if($request->planning_boxes[$i][$j] == "DP"){
                                $doubleBreak=true;
                            }
                            $box = Box::create(
                                [
                                    'start_time' => explode("-", $request->planning_boxes[0][$j]["title"])[0],
                                    'ends_time' => explode("-", $request->planning_boxes[0][$j]["title"])[1],
                                    'break' => 1,
                                    'doubleBreak' => $doubleBreak,
                                    'role' => null,
                                    'user_id' => $request->planning_boxes[$i][0]["id"],
                                    'planning_id' => $planning->id

                                ]
                            );
                        } catch (\Exception $e) {
                            return [
                                'payload' => $request->planning_boxes[0][$j],
                                'status' => 20
                            ];
                        }
                    }
                }
            }
            $planning->shift=$planning->shift;
            $planning->shift_periode_index=$this->getShiftPeriode($planning->shift_periode);
            return [
                'payload' => [
                    'planning' => $planning,
                    'planning_boxes' => $planning->boxes,
                ],
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function addPlanningBoxes(Request $request)
    {
        //dd($request->planningItems[0][1]);
        //dd($request->planningId);
        for ($i = 1; $i < count($request->planningItems); $i++) {
            for ($j = 1; $j < count($request->planningItems[$i]); $j++) {

                if (
                    $request->planningItems[$i][$j] != null &&
                    $request->planningItems[$i][$j] != "P" &&
                    $request->planningItems[$i][$j] != "DP"
                )
                    try {
                        // Create a new box record
                        $box = Box::create(
                            [
                                'start_time' => explode("-", $request->planningItems[0][$j]["title"])[0],
                                'ends_time' => explode("-", $request->planningItems[0][$j]["title"])[1],
                                'break' => 0,
                                'role' => null,
                                'user_id' => $request->planningItems[$i][0]["id"],
                                'planning_id' => $request->planningId,
                                'equipement_id' => $request->planningItems[$i][$j]["id"]
                            ]
                        );
                    } catch (\Exception $e) {
                        return [
                            'payload' => $e->getMessage(),
                            'status' => 200
                        ];
                    }
                else {
                    try {
                        // Create a new box record
                        $box = Box::create(
                            [
                                'start_time' => explode("-", $request->planningItems[0][$j]["title"])[0],
                                'ends_time' => explode("-", $request->planningItems[0][$j]["title"])[1],
                                'break' => 1,
                                'role' => null,
                                'user_id' => $request->planningItems[$i][0]["id"],
                                'planning_id' => $request->planningId

                            ]
                        );
                    } catch (\Exception $e) {
                        return [
                            'payload' => $e->getMessage(),
                            'status' => 200
                        ];
                    }
                }
            }
        }
        return [
            'payload' => Box::where("planning_id", $request->planningId)
                ->with("user")
                ->with("equipement")
                ->with("planning")
                ->get(),
            'status' => 200
        ];
    }
    public function add(Request $request)
    {
        $rules = [
            'start_time' => 'required|string ',
            'ends_time' => 'required|string ',
            'break' => 'nullable|boolean',
            'role' => 'nullable|string',
            'user_id' => 'nullable',
            'planning_id' => 'required',
            'equipement_id' => 'nullable',
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
            // Create a new box record
            $box = Box::create(
                [
                    'start_time' => $request->start_time,
                    'ends_time' => $request->ends_time,
                    'break' => $request->break,
                    'role' => $request->role,
                    'user_id' => $request->user_id,
                    'planning_id' => $request->planning_id,
                    'equipement_id' => $request->equipement_id
                ]
            );


            return [
                "payload" => $box,
                "message" => "Box created successfully",
                "status" => 201
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    public function getUsersFromBoxes($boxes)
    {
        $drivers = [];
        $equipments = [];
        $planning_boxes = [];
        if (count($boxes)) {
            $drivers = [];
            $driver = User::where('id', $boxes[0]->user_id)->first();
            array_push($drivers, $driver);

            for ($i = 1; $i < count($boxes); $i++) {
                if ($boxes[$i]->user_id != $boxes[$i - 1]->user_id) {
                    $driver = User::where('id', $boxes[$i]->user_id)->first();
                    array_push($drivers, $driver);
                }
            }
            for ($i = 0; $i < count($drivers); $i++) {
                $driverBoxes = [$drivers[$i]];
                for ($j = 0; $j < count($boxes); $j++) {
                    if ($drivers[$i]->id == $boxes[$j]->user_id) {
                        if ($boxes[$j]->break) {
                            if($boxes[$j]->doubleBreak){
                                array_push($driverBoxes, "DP");
                            }
                            else{
                                array_push($driverBoxes, "P");
                            }
                        } else {
                            $equipent = Equipement::where("id", $boxes[$j]->equipement_id)->first();
                            $equipent->profileGroup = $equipent->profileGroup;
                            array_push($driverBoxes, $equipent);
                        }
                    }
                }
                array_push($planning_boxes, $driverBoxes);
            }
        }


        return $planning_boxes;
    }
    function getShiftPeriode($shiftIndex) {
        if ($shiftIndex == "morning") return 0;
        if ($shiftIndex == "evening") return 1;
        if ($shiftIndex == "night") return 2;
    }
    public function getPlanningByIdAndBoxes(Request $request)
    {
        try {
            $planning = Planning::where('id', $request->id)->first();
            if (!$planning) {
                return [
                    "payload" => "planning not found",
                    "status" => 404
                ];
            }
            $planning->boxes = $planning->boxes()->get();
            $planning->shift=$planning->shift;
            $planning->shift_periode_index=$this->getShiftPeriode($planning->shift_periode);
            return [
                'payload' => [
                    'planning' => $planning,
                    'planning_boxes' => $this->getUsersFromBoxes($planning->boxes),
                ],
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => $e->getMessage(),
                "status" => 500
            ];
        }
    }
    public function getBoxesByPlanningId(Request $request)
    {
        try {
            $boxes = Box::with(['user', 'equipement', 'planning'])->where('planning_id', $request->planning_id)->get();

            return [
                "payload" => $boxes,
                "status" => 200
            ];
        } catch (\Exception $e) {
            return [
                "error" => $e->getMessage(),
                "status" => 500
            ];
        }
    }
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $box = Box::findOrFail($id);
            $rules = [
                'start_time' => 'string',
                'ends_time' => 'string',
                'break' => 'boolean',
                'role' => 'string|nullable',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    "error" => $validator->errors()->first(),
                    "status" => 422
                ];
            }
            $box->update($request->all());
            return [
                "payload" => $box,
                "status" => 200
            ];
        } catch (ModelNotFoundException $e) {
            return [
                "error" => "Box not found",
                "status" => 404
            ];
        }
    }
}
