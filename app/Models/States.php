<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;

    public function cities()
    {
        return $this->hasMany(Cities::class);
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'state_id');
    }
}
