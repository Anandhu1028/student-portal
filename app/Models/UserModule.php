<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    protected $table = 'user_modules';

    protected $fillable = [
        'user_id', 'module_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
