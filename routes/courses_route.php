<?php

use App\Http\Controllers\CourseBatchController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
   Route::any('/view_courses/{course_id?}', [CourseController::class, 'ViewCourses'])->name('view_courses');
   Route::post('/manage_courses', [CourseController::class, 'ManageCourses'])->name('manage_courses');
   Route::post('/create_course', [CourseController::class, 'createCourse'])->name('create_course');
   Route::post('/load_streams', [CourseController::class, 'LoadStreams'])->name('load_streams');
   Route::post('/load_specialization', [CourseController::class, 'LoadSpecialization'])->name('load_specialization');
   Route::post('/save_specialization', [CourseController::class, 'SaveSpecialization'])->name('save_specialization');
   Route::post('/get_course_schedule_form', [CourseController::class, 'GetCourseScheduleForm'])->name('get_course_schedule_form');
   Route::post('/course-fees-structure/save', [CourseController::class, 'saveCourseFeeStructure'])->name('course-fees-structure.save');
   Route::post('/save_course_data', [CourseController::class, 'saveCourseData'])->name('save_course_data');
   Route::post('/remove_course_schedule', [CourseController::class, 'removeCourseSchedule'])->name('remove_course_schedule');
   Route::post('/get_specialization_keys', [CourseController::class, 'getSpecializationKeys'])->name('get_specialization_keys');
   Route::post('/load_student_courses', [CourseController::class, 'loadStudentCourses'])->name('load_student_courses');

   Route::get('/view_universities', [UniversityController::class, 'viewUniversities'])->name('univ.view_universities');

   Route::get('/universities/manage/{id?}', [UniversityController::class, 'manageUniversity'])->name('univ.manage_university');
   Route::post('/universities/store', [UniversityController::class, 'storeUniversity'])->name('univ.store_university');

   Route::get('/create_course_batch/{couse_id?}', [CourseBatchController::class, 'manageBatch'])->name('batch.create_course_batch');
   Route::post('/save_course_batch/{couse_id?}', [CourseBatchController::class, 'saveAndUpdateBatch'])->name('batch.save_course_batch');

   Route::any('/view_course_batches', [CourseBatchController::class, 'index'])->name('batch.view_course_batches');
   Route::get('/create_new_batch', [CourseBatchController::class, 'index'])->name('batch.create_new_batch');

   Route::get('batches/load-streams', [CourseController::class, 'load_streams_by_university'])
      ->name('batch.load_streams_by_university');
   Route::get('batches/load-courses', [CourseController::class, 'load_courses_by_stream'])
      ->name('batch.load_courses_by_stream');
   Route::post('/batches/manage-admissions', [CourseBatchController::class, 'manageAdmissions'])
      ->name('batches.manage.admissions');

   Route::post('/students/check-batches', [CourseBatchController::class, 'checkBatches'])->name('checkBatches');
   Route::post('/students/update-batch', [CourseBatchController::class, 'updateBatch'])->name('updateBatch');


   Route::get('/fee-types', [FeeTypeController::class, 'index'])->name('fee_types.index');
   Route::post('/manage_fee_type', [FeeTypeController::class, 'manage'])->name('fee_types.manage');
   Route::post('/save_fee_type', [FeeTypeController::class, 'save'])->name('fee_types.save');
   Route::post('/delete_fee_type', [FeeTypeController::class, 'delete'])->name('fee_types.delete');
});
