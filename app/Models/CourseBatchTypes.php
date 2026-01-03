<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Students;

class CourseBatchTypes extends Model
{
    use HasFactory;

    protected $table = 'course_batch_types';

    public function students()
    {
        return $this->belongsToMany(
            Students::class,
            'student_course_batch_types',
            'course_batch_type_id',
            'student_id'
        );
    }
}
