<?php

namespace App\Services;

use App\Models\Notifications;
use App\Models\User;

class NotificationService
{
   /**
    * Send notifications to multiple users.
    *
    * @param string $message
    * @param string|null $link
    * @param array $additionalUsersIds
    * @return void
    */
   public static function sendNotification($message, $header = 'Notification', $class_name, $params, $link = null, $additionalUsersIds = [], $additionalUserRoles = [])
   {
      // Step 1: Get Super Admins and Admins
      $roles = ['Admin', 'Super Admin'];
      if (!empty($additionalUserRoles)) {
         $roles = array_merge($roles, $additionalUserRoles);
      }
      $roleUsers = User::whereHas('roles', function ($query) use ($roles) {
         $query->whereIn('role_name', $roles);
      })->pluck('id')->toArray();

      // Step 2: Merge with additional user IDs (admitted_for, admitted_by, assigned_to)
      $allUserIds = array_unique(array_merge($roleUsers, $additionalUsersIds));

      // Step 3: Prepare notifications array
      $notifications = [];
      foreach ($allUserIds as $userId) {
         $notifications[] = [
            'user_id' => $userId,
            'notification_header' => $header,
            'class_name' => $class_name,
            'params' => $params,
            'notification_message' => $message,
            'notification_link' => $link,
            'created_at' => now(),
            'updated_at' => now()
         ];
      }

      // Step 4: Insert into notifications table
      if (!empty($notifications)) {
         Notifications::insert($notifications);
      }
   }
}
