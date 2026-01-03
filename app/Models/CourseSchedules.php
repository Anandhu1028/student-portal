<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedules extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_batch_type_id',
        'start_date',
        'end_date',
        'status',
        // Add other fields as per migration
    ];

    public function course()
    {
        return $this->belongsTo(Courses::class);
    }

    public function batch_type()
    {
        return $this->belongsTo(CourseBatchTypes::class, 'course_batch_type_id');
    }

    public function course_payments()
    {
        return $this->hasMany(CoursePayments::class);
    }
}