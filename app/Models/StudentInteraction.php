<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInteraction extends Model
{
    protected $fillable = [
        'student_id',
        'interaction_type_id',
        'student_service_id',
        'university_id',
        'course_id',
        'course_payment_id',
        'task_id',
        'follow_up_date',
        'follow_up_time',
        'due_date',
        'remarks',
        'performed_by',
        'counts_for_performance'
    ];

    /* ================= RELATIONS ================= */

    public function student() {
        return $this->belongsTo(Students::class);
    }

    public function type() {
        return $this->belongsTo(InteractionType::class, 'interaction_type_id');
    }

    public function service() {
        return $this->belongsTo(StudentService::class, 'student_service_id');
    }

    public function payments() {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function performer() {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
