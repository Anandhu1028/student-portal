<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // PUBLIC LOGIN: returns access + refresh token
   //  public function login(Request $request)
   //  {
   //      $validator = Validator::make($request->all(), [
   //          'email' => 'required|email',
   //          'password' => 'required'
   //      ]);

   //      if ($validator->fails()) {
   //          return response()->json($validator->errors(), 422);
   //      }

   //      $response = Http::asForm()->post(url('/oauth/token'), [
   //          'grant_type' => 'password',
   //          'client_id' => env('PASSPORT_CLIENT_ID'),
   //          'client_secret' => env('PASSPORT_CLIENT_SECRET'),
   //          'username' => $request->email,
   //          'password' => $request->password,
   //          'scope' => '',
   //      ]);

   //      return $response->json();
   //  }

    // PUBLIC REFRESH: returns new access + refresh token
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = Http::asForm()->post(url('/oauth/token'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'scope' => '',
        ]);

        return $response->json();
    }
}
