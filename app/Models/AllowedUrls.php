<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedUrls extends Model
{
   use HasFactory;

   protected $fillable = [
      'module_id',
      'menu_id',
      'sub_menu_id',
      'role_id',
      'url',
      'user_id',
      'allowed',
   ];


   protected $table_name = 'allowed_urls';

   public function user()
   {
      return $this->belongsTo(User::class);
   }


   public function role()
   {
      return $this->belongsTo(Roles::class, 'role_id');
   }
}
