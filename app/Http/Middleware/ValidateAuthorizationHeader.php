<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateAuthorizationHeader
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
        $authorizationHeader = $request->header('Authorization');
        if (!$authorizationHeader || !preg_match('/Bearer (.+)/', $authorizationHeader, $matches)) {
            return response()->json(['error' => 'Authorization token not provided or malformed'], 401);
        }

        // Check if the token matches the expected value
        $expectedToken = 'a3f9b5d023cb3ec5e13a7c84bc62fada0a781635e1a5ff4b210fe9486172f093';
        if ($matches[1] !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
