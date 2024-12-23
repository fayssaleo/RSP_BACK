<?php

namespace App\Modules\ProfileGroup\Models;

use App\Modules\Equipement\Models\Equipement;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileGroup extends Model
{
    use HasFactory;

    protected $fillable = ['type'];
    protected $table = 'profilegroups';

    public function users(){
        return $this->hasMany(User::class);
    }

    public function equipements(){
        return $this->hasMany(Equipement::class);
    }
}
