<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTypes extends Model
{
    use HasFactory;

    public function payments()
    {
        return $this->hasMany(CardTypes::class, 'card_type_id');
    }
}
