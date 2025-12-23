<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'task_status_id',
        'task_priority_id',
        'task_owner_id',
        'due_at',
        'created_by',
    ];

    protected $dates = ['due_at'];

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
}
