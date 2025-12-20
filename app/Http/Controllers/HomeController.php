<?php

namespace App\Http\Controllers;

use App\Models\CourseBatchFees;
use App\Models\CourseFeesStructure;
use App\Models\CoursePayments;
use App\Models\Courses;
use App\Models\CourseSchedules;
use App\Models\CourseSpecialization;
use App\Models\DiscountCondition;
use App\Models\Discounts;
use App\Models\Notifications;
use App\Models\Payments;
use App\Models\Specializations;
use App\Models\Students;
use App\Models\Universities;
use App\Services\UserMenuService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate;

class HomeController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
      $this->middleware('auth');
   }

   /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
   public function index()
   {
      return view('home.index');
   }


   public function dashboard(Request $request)
   {
      $created_from_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
      $created_to_date = Carbon::now()->endOfMonth()->toDateString();
      $count_new_students = Students::where('student_status', 'active')->whereBetween('created_at', [$created_from_date, $created_to_date])
         ->count();
      $count_all_students = Students::where('student_status', 'active')
         ->count();
      $total_revenue = CoursePayments::select(DB::raw('SUM(amount + discount) as total_revenue'))
         ->where('status', 'active')
         ->pluck('total_revenue')
         ->first();
      $total_revenue = CoursePayments::select(DB::raw('SUM(amount + discount) as total_revenue'))
         ->pluck('total_revenue')
         ->first();

      $summaryPending = CoursePayments::join('course_schedules', 'course_schedules.id', '=', 'course_payments.course_schedule_id')
         ->where('course_schedules.status', 'active')
         ->where('course_payments.status', 'active')
         ->where('course_payments.payment_status', 'pending')
         ->selectRaw('SUM(course_schedules.course_fee - (course_payments.amount + course_payments.discount)) as total_pending')
         ->selectRaw('COUNT(DISTINCT course_payments.id) as student_count')
         ->first();
      $studentsIdsPendingCompletion = Students::where('profile_completion', '<', 3)->count();
      if ($summaryPending) {

         $total_pending_amount = $summaryPending->total_pending;
         $total_pending_student_count = $summaryPending->student_count;
      } else {
         $total_pending_amount = 0;
         $total_pending_student_count = 0;
      }
      $total_pending_amount = $this->format_number($total_pending_amount);
      $total_revenue = $this->format_number($total_revenue);
      $date_one = date('Y-m');
      $date_two = date('Y-m', strtotime('-1 year'));

      return view('home.dashboard', [
         'count_new_students' => $count_new_students,
         'count_all_students' => $count_all_students,
         'total_revenue' => $total_revenue,
         'total_pending_amount' => $total_pending_amount,
         'total_pending_student_count' => $total_pending_student_count,
         'studentsIdsPendingCompletion' => $studentsIdsPendingCompletion
      ]);
   }

   public function getStudentsAdmissionData()
   {



      $data = DB::table('course_payments')
         ->select(
            'course_payments.created_by',
            DB::raw('COUNT(course_payments.id) as student_count'), // Count the number of course payment entries
            DB::raw('MAX(CONCAT(employees.first_name, " ", employees.last_name)) as employee_name') // Get employee name
         )
         ->join('employees', 'course_payments.created_by', '=', 'employees.id')
         ->groupBy('course_payments.created_by') // Group by created_by only
         ->get();
      return response()->json($data);;
   }

   public function clearPageSession(Request $request)
   {
      // if ($request->input('student_id') !== null) {
      //     session()->forget('student_id');
      // }
      // return 123;
   }

   public function refreshSession(Request $request)
   {

      $user = Auth::user();

      if (!$user) {
         return redirect()->route('login')->with('error', 'Please login first.');
      }

      try {
         app(UserMenuService::class)->saveUserMenusInSession($user);
      } catch (\Exception $e) {
         return redirect()->back()->with('error', 'Failed to refresh session menus.');
      }

      return redirect('/')->with('success', 'Session refreshed successfully.');
   }

   public function reset_core(Request $request)
   {

      $user_category = Auth::user()->roles->unique_key;
      if ($user_category != 'super_admin') {
         return 'No permission';
      }
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');

      // First group (must be cleared first)
      $firstGroup = [
         Payments::class,
         CoursePayments::class,
         Students::class,
         CourseSchedules::class,
         CourseBatchFees::class,
         CourseFeesStructure::class,
         CourseSpecialization::class,
         Specializations::class,
         DiscountCondition::class,
      ];

      // Remaining models
      $remaining = [
         Courses::class,
         Discounts::class,
         Notifications::class,
         Universities::class,
      ];

      // Truncate first group
      foreach ($firstGroup as $model) {
         $model::truncate();
      }

      // Truncate remaining
      foreach ($remaining as $model) {
         $model::truncate();
      }

      // Enable foreign key checks
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');

      return response()->json(['status' => 'core reset successful']);
   }
}
