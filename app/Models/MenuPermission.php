<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{
   protected $fillable = [
      'user_id',
      'menu_id',
      'sub_menu_id',
      'created_at',
      'updated_at'
   ];

   public function user()
   {
      return $this->belongsTo(User::class);
   }

   public function menu()
   {
      return $this->belongsTo(Menu::class);
   }

   public function subMenu()
   {
      return $this->belongsTo(SubMenu::class);
   }
}
