<?php

namespace App\Http\Middleware;

use App\Models\AccessTokens;
use Closure;
use Illuminate\Http\Request;

class ValidateApiToken
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
    * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    */
   public function handle(Request $request, Closure $next)
   {
      $token = str_replace("Bearer ", "", $request->header('Authorization'));
      if (!$token || !$request->client_id) {
         return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized, missing token / client id',
            'access_token' => $token,
            'client_id' =>  $request->client_id,
         ], 401);
      }
      $tokenData = AccessTokens::where('client_id', $request->client_id)
         ->where('access_token', $token)
         ->first();
      if (!$tokenData) {
         return response()->json([
            'status' => 'error',
            'message' => 'Invalid or revoked token',
            'access_token' => $token,
            'client_id' =>  $request->client_id,
         ], 401);
      }
      if ($tokenData->isAccessTokenExpired()) {
         return response()->json([
            'status' => 'error',
            'message' => 'Invalid or revoked token',
            'access_token' => $token,
            'client_id' =>  $request->client_id,
         ], 401);
      }
      return $next($request);
   }
}
