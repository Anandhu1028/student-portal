<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskForward extends Model
{
    protected $fillable = [
        'task_id',
        'department_id',
        'user_id',
        'forwarded_by',
        'follow_up_date',
    ];

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

