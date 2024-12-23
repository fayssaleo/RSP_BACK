<?php

namespace App\Modules\Equipement\Models;

use App\Modules\Box\Models\Box;
use App\Modules\Planning\Models\Planning;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = ['matricule','status','profile_group_id'];

    public function profileGroup(){
        return $this->belongsTo(ProfileGroup::class);
    }

    public function boxes(){
        return $this->hasMany(Box::class);
    }
    public function plannings(){
        return $this->hasMany(Planning::class);
    }
}
