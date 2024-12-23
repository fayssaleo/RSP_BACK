<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountWhHistory extends Model
{
    use HasFactory;
    protected $table = 'count_wh_history';
    protected $fillable = [
        'shift_id',
        'profile_group_id',
        'user_id',
        'resetedBy',
        'type',
        'planning',
    ];
        // Define an accessor for the created_at field
        public function getCreatedAtAttribute($value)
        {
            return \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
        }
        public function user(){
            return $this->belongsTo(related: User::class);
        }
}
