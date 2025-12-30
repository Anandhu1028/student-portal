<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaxRatesController;
use App\Http\Controllers\TaskController;
use App\Models\Students;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::post('/save_user_profile', [AdminContoller::class, 'SaveUserProfile'])->name('save_user_profile');
// Route::get('/approve_user/{id}/{approve}', [AdminContoller::class, 'ApproveUser'])->name('approve_user');
// Route::any('/managewebvideo', [AdminContoller::class, 'managewebvideo'])->name('managewebvideo');

Auth::routes(['verify' => true]);

Route::get('/access_denied', function () {
   return view('permissions.permission_denied');
})->name('access_denied');




Route::middleware(['auth', 'verified', 'check_url_access'])->group(function () {
   Route::get('/clear_cache', function () {
      Artisan::call('view:clear');
      Artisan::call('route:clear');
      Artisan::call('cache:clear');
      Artisan::call('config:cache');
      Artisan::call('queue:clear');
      Artisan::call('optimize');
      Artisan::call('optimize:clear');
      return 'Cache cleared';
   });
   Route::get('/refresh_session', [HomeController::class, 'refreshSession'])
      ->name('session.refresh');

   Route::get('/storage_link', function () {
      Artisan::call('storage:link');
      return 123;
   });

   Route::get('/', [HomeController::class, 'index'])->name('home_page');
   Route::get('/dashboard/', [HomeController::class, 'dashboard'])->name('dashboard');
   Route::any('/add_students/{step?}/{student_id?}/{course_id_param?}/{course_schedule_id?}/{installment_id?}', [StudentController::class, 'add_students'])->name('add_students');
   // Route::any('/add_students', [StudentController::class, 'add_students'])->name('add_students');

   Route::post('/save_personal_info', [StudentController::class, 'savePersonalInfo'])->name('save_personal_info');
   Route::post('/save_edcation_info', [StudentController::class, 'saveEdcationInfo'])->name('save_edcation_info');
   Route::post('/save_payments_info', [PaymentController::class, 'savePaymentInfo'])->name('save_payments_info');
   Route::post('/clear_page_session', [HomeController::class, 'clearPageSession'])->name('clear_page_session');
   Route::post('/upload_student_docs', [DocumentsController::class, 'uploadStudentDocs'])->name('upload_student_docs');
   Route::post('/load_view_student_filter', [StudentController::class, 'loadViewStudentFilter'])->name('load_view_student_filter');

   Route::post('/export_students_excel', [StudentController::class, 'view_students'])->name('export_students_excel');
   Route::post('/get_graph_data', [HomeController::class, 'getStudentsAdmissionData'])->name('get_graph_data');


   Route::get('/view_employees', [EmployeeController::class, 'viewEmployees'])->name('view_employees');
   Route::post('/update_emp_status', [EmployeeController::class, 'updateEmpStatus'])->name('update_emp_status');
   Route::post('/update_emp_role', [EmployeeController::class, 'updateEmpRole'])->name('update_emp_role');
   Route::get('/employees/manage/{user?}', [EmployeeController::class, 'manageUser'])->name('employee.manage_user');
   Route::post('/employees/store', [EmployeeController::class, 'storeUser'])->name('employee.store_user');
   Route::post('/employees/manage_user_permissions', [EmployeeController::class, 'manageUserPermissions'])->name('employee.manage_user_permissions');

   Route::get('/autocomplete/menus', [EmployeeController::class, 'autocompleteMenus'])->name('autocomplete.menus');
   Route::get('/autocomplete/submenus', [EmployeeController::class, 'autocompleteSubMenus'])->name('autocomplete.submenus');
   Route::post('/employees/update_password', [EmployeeController::class, 'updatePassword'])->name('employee.update_password');
   Route::get('/menus/view_all_menus', [MenuController::class, 'index'])->name('manage_menus');
   Route::get('/menus/by-module', [MenuController::class, 'getMenusByModule'])->name('menus.byModule');
   Route::get('/menus/by-menu', [MenuController::class, 'getSubMenusByMenu'])->name('subMenus.byMenu');
   Route::get('/menus/form/{id?}', [MenuController::class, 'showMenuForm'])->name('menus.form');
   Route::post('/menus/save_menus', [MenuController::class, 'storeMenu'])->name('menus.store');
   Route::get('menus/sub-menus/form/{id?}', [MenuController::class, 'showSubMenuForm'])->name('subMenus.form');
   Route::post('menus/sub-menus', [MenuController::class, 'storeSubMenu'])->name('subMenus.store');
   Route::get('menus/urls/form/{id?}', [MenuController::class, 'showUrlForm'])->name('urls.form');
   Route::post('menus/urls', [MenuController::class, 'storeUrl'])->name('urls.store');

   Route::get('taxrates', [TaxRatesController::class, 'index'])->name('taxrates.index');
   Route::post('manage_taxrate', [TaxRatesController::class, 'manage'])->name('taxrates.manage');
   Route::post('save_taxrate', [TaxRatesController::class, 'save'])->name('taxrates.save');

   Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
   Route::post('/manage_department', [DepartmentController::class, 'manage'])->name('departments.manage');
   Route::post('/save_department', [DepartmentController::class, 'save'])->name('departments.save');
   Route::post('/delete_department', [DepartmentController::class, 'delete'])->name('departments.delete');

   Route::post('/department/user-toggle', [DepartmentController::class, 'toggleUser'])->name('departments.user.toggle');

   Route::get('/reset_core', [HomeController::class, 'reset_core'])->name('reset_core');



    /*
    |--------------------------------------------------------------------------
    | TASK MANAGEMENT
    |--------------------------------------------------------------------------
    */
    Route::get('/tasks/list', [TaskController::class, 'list'])->name('tasks.list');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/delete', [TaskController::class, 'delete'])->name('tasks.delete');

    Route::post('/tasks/{task}/comment', [TaskController::class, 'comment'])->name('tasks.comment');
    Route::post('/tasks/{task}/attachment', [TaskController::class, 'uploadAttachment'])->name('tasks.attachment');

    Route::post('/tasks/{task}/subtasks', [TaskController::class, 'storeSubTask'])->name('tasks.subtasks.store');
    Route::patch('/tasks/subtasks/{subtask}', [TaskController::class, 'updateSubTask'])->name('tasks.subtasks.update');
    Route::post('/tasks/subtasks/{subtask}/status', [TaskController::class, 'changeSubTaskStatus'])->name('tasks.subtasks.change_status');
    Route::delete('/tasks/subtasks/{subtask}',[TaskController::class, 'deleteSubTask'])->name('tasks.subtasks.delete');
    
    Route::post('/tasks/{task}/comment', [TaskController::class, 'comment'])->name('tasks.comment');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus']) ->name('tasks.updateStatus');

    Route::post('/tasks/{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.updatePriority');

    Route::post('/tasks/{task}/forward',  [TaskController::class, 'forward'])->name('tasks.forward');
    Route::delete('/tasks/forwards/{forward}', [TaskController::class, 'deleteForward']);

    Route::delete('/task-attachments/{attachment}', [TaskController::class, 'deleteAttachment'])->name('tasks.attachments.delete');

Route::delete(
    '/task-attachments/{id}',
    [TaskController::class, 'deleteAttachment']
)->name('tasks.attachments.delete');






});

Route::post('/load_cities', [LocationController::class, 'load_cities'])->name('load_cities');
Route::post('/load_university', [LocationController::class, 'load_university'])->name('load_university');
Route::post('/load_states', [LocationController::class, 'load_states'])->name('load_states');
Route::post('/load_countries', [LocationController::class, 'load_countries'])->name('load_countries');
Route::post('/load_states_and_countries', [LocationController::class, 'load_states_and_countries'])->name('load_states_and_countries');



Route::post('/load_courses', [CourseController::class, 'load_courses'])->name('load_courses');
Route::post('/load_courses_period', [CourseController::class, 'load_courses_period'])->name('load_courses_period');
Route::post('/load_courses_payments', [CourseController::class, 'loadCoursePayments'])->name('load_courses_payments');


Route::post('/load_payment_method', [PaymentController::class, 'load_payment_method'])->name('load_payment_method');
Route::post('/load_promo_codes', [PaymentController::class, 'load_promo_codes'])->name('load_promo_codes');
Route::post('/load_card_types', [PaymentController::class, 'load_card_types'])->name('load_card_types');
Route::post('/load_banks', [PaymentController::class, 'load_banks'])->name('load_banks');
