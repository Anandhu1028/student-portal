<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessTokens;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    /**
     * Generate access token with automatic client_id
     */
    public function generateAccessToken()
    {
        $clientId = Str::random(10);        // generate client id
        $accessToken = Str::random(60);     // generate access token
        $refreshToken = Str::random(60);    // generate refresh token

        $token = AccessTokens::create([
            'client_id' => $clientId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => Carbon::now()->addMonths(2),        // access token expires in 2 months
            'refresh_token_ex_at' => Carbon::now()->addMonths(2) // refresh token expires in 6 months
        ]);

        return response()->json([
            'client_id' => $clientId,
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_at' => $token->expires_at,
            'refresh_token_ex_at' => $token->refresh_token_ex_at
        ]);
    }

    /**
     * Refresh access token using client_id + refresh_token
     */
    public function refreshAccessToken(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'refresh_token' => 'required'
        ]);

        $token = AccessTokens::where('client_id', $request->client_id)
                    ->where('refresh_token', $request->refresh_token)
                    ->first();

        if (!$token) {
            return response()->json(['error' => 'Invalid client_id or refresh_token'], 401);
        }

        if ($token->isRefreshTokenExpired()) {
            return response()->json(['error' => 'Refresh token expired'], 401);
        }

        // Generate new tokens
        $token->access_token = Str::random(60);
        $token->refresh_token = Str::random(60);
        $token->expires_at = Carbon::now()->addMonths(2);
        $token->refresh_token_ex_at = Carbon::now()->addMonths(2);
        $token->save();

        return response()->json([
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_at' => $token->expires_at,
            'refresh_token_ex_at' => $token->refresh_token_ex_at
        ]);
    }

}
