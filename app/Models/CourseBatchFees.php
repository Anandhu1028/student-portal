<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseBatchFees extends Model
{
   use HasFactory;


   protected $fillable = [
      'university_id',
      'course_id',
      'course_schedule_id',
      'fee_type_id',
      'effective_date',
      'amount',
      'tax_amount',
      'promocode_id',
      'discount_amount',
      'late_amount',
      'other_amount',
      'total_amount',
      'is_refundable',
      'due_days',
      'is_mandatory',
      'tax_rate_id',
   ];
}
