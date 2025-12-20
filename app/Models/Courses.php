<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
   use HasFactory;

   protected $fillable = [
      'specialization',
      'status',
      'university_id',
      'department_id',
      'stream_id',
      'exam_mode_id',
      'fee_threshold',
      'tax_rate_id',
      'promocode',
      'remote_status',
      'remote_course_id',
      'remote_status_message',
   ];

   public function university()
   {
      return $this->belongsTo(Universities::class, 'university_id');
   }

   public function payments()
   {
      return $this->hasMany(Courses::class, 'course_id');
   }

   public function coursepayments()
   {
      return $this->hasMany(Courses::class, 'course_id');
   }

   public function specializations()
   {
      return $this->belongsToMany(
         Specializations::class, // related model
         'course_specialization', // pivot table
         'course_id',             // foreign key on pivot table for this model
         'specialization_id'      // foreign key on pivot table for related model
      );
   }

   public function course_schedules()
   {
      return $this->hasMany(CourseSchedules::class, 'course_id');
   }

   public function streams()
   {
      return $this->belongsTo(Streams::class, 'stream_id');
   }

   public function discounts()
   {
      return $this->belongsTo(Discounts::class, 'promocode');
   }

   public function course_installments()
   {
      return $this->belongsTo(CourseInstallments::class, 'course_id');
   }

   public function courseFeeStructure()
   {
      return $this->hasMany(CourseFeesStructure::class, 'course_id');
   }
}
