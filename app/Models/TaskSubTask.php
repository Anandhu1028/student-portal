<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskSubTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'task_status_id',
        'task_priority_id',
        'task_owner_id',
        'start_at',
        'due_at',
        'completed_at',
    ];

    protected $dates = ['due_at', 'start_at', 'completed_at'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'task_owner_id');
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function priority()
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

}
