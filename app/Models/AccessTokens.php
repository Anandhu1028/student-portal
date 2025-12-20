<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessTokens extends Model
{
   protected $fillable = ['client_id', 'access_token', 'refresh_token', 'expires_at','refresh_token_ex_at'];

   protected $dates = ['expires_at', 'refresh_token_ex_at'];

   public function isAccessTokenExpired()
   {
      return $this->expires_at->isPast();
   }

   public function isRefreshTokenExpired()
   {
      return $this->refresh_token_ex_at->isPast();
   }
}
