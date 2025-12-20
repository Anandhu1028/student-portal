<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
   use HasFactory;

   protected $fillable = [
      'promocode',
      'description',
      'start_date',
      'end_date',
      'status'
   ];
   public $incrementing = true;

   public function payments()
   {
      return $this->hasMany(Payments::class, 'promocode');
   }

   public function courses()
   {
      return $this->hasMany(Courses::class, 'promocode');
   }


   public function course_batches()
   {
      return $this->hasMany(CourseSchedules::class, 'promocode_id');
   }

   public function conditions()
   {
      return $this->hasMany(DiscountCondition::class, 'discount_id');
   }
}
