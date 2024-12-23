<?php

namespace App\Modules\Role\Models;

use App\Modules\Department\Models\Department;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name','sub_category','department_id'];

    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function users(){
        return $this->hasMany(User::class);
    }
}
