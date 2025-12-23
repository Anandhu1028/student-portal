<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskRelation extends Model
{
    protected $fillable = [
        'task_id',
        'related_type',
        'related_id',
        'relation_type',
    ];

    public $timestamps = false;
}
