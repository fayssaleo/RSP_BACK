<?php

namespace App\Modules\UserPlanning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlanning extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'planning_id',
        'departure_at',
        'reason'
    ];

    protected $table = 'users_plannings';
}
