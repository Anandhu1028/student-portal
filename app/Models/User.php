<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
   use HasApiTokens, HasFactory, Notifiable;

   /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   // protected $fillable = [
   //    'name',
   //    'email',
   //    'password',
   //    'role_id',
   //    'profile_picture'
   // ];

   protected $fillable = [
      'name',
      'email',
      'password',
      'role_id',
      'profile_picture',
      'phone_number',
      'developer_mode',
      'default_menu_id',
      'default_sub_menu_id',
      'default_module_id',
      'branch_id',
      'admin_privilege',
      'timezone',
      'is_active',
   ];



   /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
   protected $hidden = [
      'password',
      'remember_token',
   ];

   /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
   protected $casts = [
      'email_verified_at' => 'datetime',
   ];

   public function employees()
   {
      return $this->hasMany(Employees::class, 'user_id');
   }



   public function roles()
   {
      return $this->belongsTo(Roles::class, 'role_id');
   }

   public function payments_created()
   {
      return $this->hasMany(Payments::class, 'created_by');
   }

   public function modules()
   {
      return $this->belongsToMany(Module::class, 'user_modules');
   }


   public function defaultMenu()
   {
      return $this->belongsTo(Menu::class, 'default_menu_id');
   }

   public function defaultSubMenu()
   {
      return $this->belongsTo(SubMenu::class, 'default_sub_menu_id');
   }

   public function defaultModule()
   {
      return $this->belongsTo(Module::class, 'default_module_id');
   }

   public function branch()
   {
      return $this->belongsTo(Branches::class);
   }

  public function departments()
    {
        return $this->belongsToMany(
            Departments::class,
            'department_user',
            'user_id',        // ✅ FIXED
            'department_id'   // ✅ FIXED
        );
    }


}
