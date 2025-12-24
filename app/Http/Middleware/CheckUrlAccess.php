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
        // ✅ ALWAYS allow AJAX requests (GET + POST)
        if ($request->ajax()) {
            return $next($request);
        }

        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $currentRoute = $request->route()?->getName();

        // Allow home
        if ($currentRoute === 'home_page') {
            return $next($request);
        }

        // Explicit user deny
        $userDenied = AllowedUrls::where('user_id', $user->id)
            ->where('url', $currentRoute)
            ->where('allowed', false)
            ->exists();

        // Role permission
        $isAllowed = AllowedUrls::where('role_id', $user->role_id)
            ->where('url', $currentRoute)
            ->where('allowed', true)
            ->exists();

        if ($userDenied || !$isAllowed) {
            return response()->view('permissions.permission_denied', [], 403);
        }

        return $next($request);
    }


   
   
}
