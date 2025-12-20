<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherMenuOperationUrls extends Model
{
   protected $fillable = ['module_id', 'menu_id', 'sub_menu_id', 'url','name'];

   public function module()
   {
      return $this->belongsTo(Module::class, 'module_id');
   }

   public function menus()
   {
      return $this->belongsTo(Menu::class, 'menu_id');
   }

   public function sub_menus()
   {
      return $this->belongsTo(SubMenu::class, 'sub_menu_id');
   }

   public function allowedUrls()
   {
      return $this->hasMany(AllowedUrls::class, 'url', 'url');
   }
}
