<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
   public static function timeAgo($datetime)
   {
      $now = Carbon::now();
      $created = Carbon::parse($datetime);

      $diff = $now->diffInSeconds($created);

      if ($diff < 60) {
         return 'Just now';
      } elseif ($diff < 3600) {
         $minutes = $now->diffInMinutes($created);
         return $minutes . ' minute' . ($minutes != 1 ? 's' : '') . ' ago';
      } elseif ($diff < 86400) {
         $hours = $now->diffInHours($created);
         return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
      } elseif ($diff < 864000) { // less than 10 days
         $days = $now->diffInDays($created);
         return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
      } else {
         return $created->format('d-m-Y');
      }
   }
}
