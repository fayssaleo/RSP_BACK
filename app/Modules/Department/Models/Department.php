<?php

namespace App\Modules\Department\Models;

use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function roles(){
        return $this->hasMany(Role::class);
    }
}
