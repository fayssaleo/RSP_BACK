<?php

namespace App\Modules\EquipementPlanningWorkingHours\Models;

use App\Modules\EquipementPlanning\Models\EquipementPlanning;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipementPlanningWorkingHours extends Model
{
    use HasFactory;
    protected $fillable = [
        'equipement_planning_id',
        'start_time',
        'end_time'      
    ];

    protected $table = 'equipementsplanningworkinghours';
    public function equipement_planning(){
        return $this->belongsTo(EquipementPlanning::class);
    }
}
