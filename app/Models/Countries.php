<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    public function states()
    {
        return $this->hasMany(States::class);
    }

    public function student()
    {
        return $this->hasMany(Students::class,'nationality_id');
    }
}
