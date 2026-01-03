<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'student_id',
        'student_interaction_id',
        'university_id',
        'course_id',
        'course_payment_id',
        'task_id',
        'amount',
        'scheduled_date',
        'status',
        'created_by'
    ];

    public function interaction() {
        return $this->belongsTo(StudentInteraction::class);
    }
}
