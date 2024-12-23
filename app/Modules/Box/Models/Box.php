<?php

namespace App\Modules\Box\Models;

use App\Modules\Equipement\Models\Equipement;
use App\Modules\Planning\Models\Planning;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    protected $fillable = ['start_time','ends_time','break','doubleBreak','role','user_id','planning_id','equipement_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function equipement(){
        return $this->belongsTo(Equipement::class);
    }
    public function planning(){
        return $this->belongsTo(Planning::class);
    }
}
