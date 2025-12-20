<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;

    public function payments()
    {
        return $this->hasMany(Payments::class, 'bank_id');
    }
}
