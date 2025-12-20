<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseNames extends Model
{
    use HasFactory;

    public function stream()
    {
        return $this->belongsTo(Streams::class, 'stream_id');
    }

    public function course()
    {
        return $this->hasMany(Courses::class, 'course_name_id');
    }
}
