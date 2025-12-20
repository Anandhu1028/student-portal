<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }

    public function student()
    {
        return $this->hasMany(Students::class, 'city_id');
    }

    // public function student_state()
    // {
    //     return $this->belongsTo(Students::class, 'state_id');
    // }
}
