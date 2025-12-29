<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'task_type',
        'task_type_id',
        'task_status_id',
        'task_priority_id',
        'task_owner_id',
        'start_at',
        'due_at',
        'completed_at',
        'visibility',
        'created_by',
    ];

    protected $dates = ['due_at', 'start_at', 'completed_at'];

    use SoftDeletes;

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function priority()
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'task_owner_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(
            User::class,
            'task_relations',
            'task_id',
            'related_id'
        )->wherePivot('relation_type', 'assignee');
    }

    // Alias required by spec
    public function assignedUsers()
    {
        return $this->assignees();
    }

    public function subTasks()
    {
        return $this->hasMany(TaskSubTask::class, 'task_id');
    }

    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'task_id')->latest();
    }

    public function type()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function forwards()
    {
        return $this->hasMany(TaskForward::class);
    }

}
