<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityCards extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->hasMany(Students::class, 'identity_card_id');
    }
}
