<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsTracknos extends Model
{
    use HasFactory;

    protected $table = 'students_tracknos'; // Replace with the actual table name

    // Allow mass assignment for these attributes
    protected $fillable = [
        'university_id',
        'branch_id',
        'next_number',
    ];
}
