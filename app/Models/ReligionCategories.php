<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReligionCategories extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->hasMany(Students::class, 'religion_category_id');
    }
}
