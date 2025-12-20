<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    use HasFactory;

    public function coursePayments()
    {
        return $this->hasMany(CoursePayments::class, 'branch_id');
    }

}
