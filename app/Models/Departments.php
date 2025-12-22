<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'department_user',
            'department_id', // ✅ FIXED
            'user_id'        // ✅ FIXED
        );
    }
}
