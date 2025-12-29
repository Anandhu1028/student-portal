<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskForward extends Model
{
    protected $fillable = [
        'task_id',
        'department_id',
        'forwarded_by',
        'message',
    ];

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }
}
