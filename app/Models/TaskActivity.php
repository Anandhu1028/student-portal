<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    protected $fillable = [
        'task_id',
        'sub_task_id',
        'actor_id',
        'task_owner_id',
        'task_activity_type_id',
        'message',
        'due_at',
        'from_status_id',
        'to_status_id',
        'target_user_ids',
    ];

    protected $casts = [
        'target_user_ids' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskActivityAttachment::class, 'task_activity_id');
    }

    public function type()
    {
        return $this->belongsTo(TaskActivityType::class, 'task_activity_type_id');
    }

    public function subTask()
    {
        return $this->belongsTo(TaskSubTask::class, 'sub_task_id');
    }
}
