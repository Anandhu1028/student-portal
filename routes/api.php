<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UniversityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/generate-access-token', [ApiTokenController::class, 'generateAccessToken']);
Route::post('/refresh-access-token', [ApiTokenController::class, 'refreshAccessToken']);

Route::middleware('validate_api_token')->group(function () {
   Route::post('/create_universiy', [UniversityController::class, 'storeUniversitiesAPI']);
   Route::post('/create_course', [CourseController::class, 'storeCourseAPI']);
   Route::post('/create_student', [StudentController::class, 'storeStudentsAPI']);
   Route::post('/create_payment', [PaymentController::class, 'storePaymentsAPI']);
});
