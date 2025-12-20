<?php

namespace App\Http\Middleware;

use App\Models\AllowedUrls;
use Illuminate\Http\Request;
use Closure;

class CheckUrlAccess
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

      $currentUrl = $request->path();
      $currentRoute = $request->route()->getName();

      $pathSegments = explode('/', ltrim($currentUrl, "/"));
      $current_page =  $pathSegments[0];

      $user = auth()->user();


      $userDenied = AllowedUrls::where('user_id', $user->id)
         ->where('url', $currentRoute)
         ->where('allowed', false)
         ->exists();

      $isUrlApproved = AllowedUrls::where('role_id', $user->role_id)
         ->where('url', $currentRoute)
         ->exists();
      if (($userDenied || !$isUrlApproved) && $currentRoute != 'home_page' && $current_page != 'refresh_session' && $current_page != 'clear_cache'  && $current_page != 'reset_core') {
         return response()->view('permissions.permission_denied', [], 403);
      }

      return $next($request);
   }
}
