<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivityAttachment extends Model
{
    protected $fillable = ['task_activity_id', 'file_path'];

    public function activity()
    {
        return $this->belongsTo(TaskActivity::class, 'task_activity_id');
    }
}
