<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students() {
        return $this->hasMany(Students::class);
    }
}
