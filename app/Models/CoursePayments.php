<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_schedule_id',
        'amount',
        'discount',
        'status',
        // Add other fields
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    public function course_schedule()
    {
        return $this->belongsTo(CourseSchedules::class);
    }
}