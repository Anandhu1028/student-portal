<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
   protected $fillable = [
      'name',
      'display_name',
      'menu_id',
      'url',
      'icon',
      'order',
      'is_active',
      'is_documented',
      'action_menu'
   ];

   public function menu()
   {
      return $this->belongsTo(Menu::class);
   }


   public function allowedUrls()
   {
      return $this->hasMany(AllowedUrls::class, 'sub_menu_id');
   }



   public function allowedUrl()
   {
      return $this->hasMany(AllowedUrls::class, 'sub_menu_id')
         ->where('url', $this->url);
   }


   public function OperationUrls()
   {
      return $this->hasMany(OtherMenuOperationUrls::class, 'sub_menu_id');
   }
}
