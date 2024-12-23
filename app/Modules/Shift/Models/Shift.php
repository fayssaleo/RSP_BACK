<?php

namespace App\Modules\Shift\Models;

use App\Modules\Planning\Models\Planning;
use App\Modules\User\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['category'];
    public function users(){
        return $this->hasMany(User::class);
    }

    public function planning(){
        return $this->hasMany(Planning::class);
    }
}
