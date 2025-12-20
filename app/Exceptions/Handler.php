<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
   /**
    * A list of the exception types that are not reported.
    *
    * @var array<int, class-string<Throwable>>
    */
   protected $dontReport = [
      //
   ];

   /**
    * A list of the inputs that are never flashed for validation exceptions.
    *
    * @var array<int, string>
    */
   protected $dontFlash = [
      'current_password',
      'password',
      'password_confirmation',
   ];

   /**
    * Register the exception handling callbacks for the application.
    *
    * @return void
    */
   public function register()
   {
      $this->reportable(function (Throwable $e) {
         //
      });
   }

   public function render($request, Throwable $exception)
   {

      if ($request->is('api/*') && $exception instanceof \Illuminate\Auth\AuthenticationException) {
         return response()->json(['error' => 'Unauthenticated'], 401);
      }
      if ($exception instanceof PostTooLargeException) {
         return response()->json(['errors' => 'The uploaded file exceeds the maximum size allowed. Please Contact IT Support', 'error_type' => 'upload_limit'], 413);
      }

      return parent::render($request, $exception);
   }
}
