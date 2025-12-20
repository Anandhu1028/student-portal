<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalQualifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'field_of_study',
        'graduation_year',
        'gpa',
        'other_degree_name',
        'other_college_name',
        'institution_id',
        'degree_id'
    ];

    public function university()
    {
        return $this->belongsTo(Universities::class, 'institution_id');
    }

   
}
