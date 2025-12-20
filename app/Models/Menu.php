<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
   protected $fillable = [
      'name',
      'display_name',
      'module_id',
      'menu_title_id',
      'url',
      'icon',
      'is_active',
      'is_documented',
      'action_menu',
      'default_menu'
   ];

   public function module()
   {
      return $this->belongsTo(Module::class);
   }

   public function menuTitle()
   {
      return $this->belongsTo(MenuTitle::class);
   }

   public function subMenus()
   {
      return $this->hasMany(SubMenu::class,'menu_id');
   }



   public function allowedUrls()
   {
      return $this->hasMany(AllowedUrls::class,'menu_id');
   }

   public function OperationUrls()
   {
      return $this->hasMany(OtherMenuOperationUrls::class,'menu_id');
   }
}
