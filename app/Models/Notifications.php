<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'notification_header',
      'notification_message',
      'notification_link',
      'is_read'
   ];
}
