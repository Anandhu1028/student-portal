<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'check_url_access'])->group(function () {
   Route::post('/search_students', [StudentController::class, 'searchStudents'])->name('search_students');
   Route::post('/save_student_track_number', [PaymentController::class, 'SaveStudentTrackNumber'])->name('save_student_track_number');
   Route::post('/username_autocomplete', [EmployeeController::class, 'UsernameAutocomplete'])->name('username_autocomplete');
   Route::any('/view_students/{search?}/{batchId?}/{studentId?}',[StudentController::class, 'view_students'])->name('view_students');
   Route::get('/view_profile/{step}/{student_id}', [StudentController::class, 'view_profile'])->name('view_profile');
});
