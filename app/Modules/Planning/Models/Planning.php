<?php

namespace App\Modules\Planning\Models;

use App\Modules\Box\Models\Box;
use App\Modules\Equipement\Models\Equipement;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\Shift\Models\Shift;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = ['shift_id',
     'profile_group_id',
     'checker_number',
     'deckman_number',
     'assistant',
     'planned_at',
     'shift_periode',
     'planning_header',
    ];
    public function setPlannedAtAttribute($value)
{
    $this->attributes['planned_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
}
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function profileGroup()
    {
        return $this->belongsTo(ProfileGroup::class);
    }
}
