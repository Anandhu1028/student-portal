<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
   protected $fillable = [
      'name',
      'app_name',
      'description',
      'url',
      'icon',
      'image_path',
      'order',
      'is_active',
      'timezone'
   ];

   public function users()
   {
      return $this->belongsToMany(User::class, 'user_modules');
   }

   public function menus()
   {
      return $this->hasMany(Menu::class);
   }

     public function OperationUrls()
   {
      return $this->hasMany(OtherMenuOperationUrls::class,'module_id');
   }
}
