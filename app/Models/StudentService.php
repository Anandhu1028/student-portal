<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentService extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'is_chargeable',
        'default_fee',
        'status'
    ];
}
