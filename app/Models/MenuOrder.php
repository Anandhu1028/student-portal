<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOrder extends Model
{
    protected $fillable = [
        'menu_id', 'module_id', 'order'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
