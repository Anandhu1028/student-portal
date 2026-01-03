<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentInteraction;
use App\Models\CourseSchedules;
use App\Models\CoursePayments;
use App\Models\Students;

class StudentInteractionController extends Controller
{
    /* ================= LIST ================= */
    public function view()
    {
        $interactions = StudentInteraction::with([
                'student:id,first_name,last_name',
                'type:id,name',
                'performer:id,name'
            ])
            ->latest()
            ->get();

        return view('student_interactions.view_student_interactions', compact('interactions'));
    }

    /* ================= OFFCANVAS ================= */
    public function create()
    {
        return view('student_interactions.partials.create_interaction');
    }

    /* ================= SELECT STUDENTS MODAL ================= */
    public function selectStudentsModal()
{
    $batches = CourseSchedules::with([
            'course:id,specialization',
            'university:id,name'
        ])
        ->where('status', 'active')
        ->where('admission_closed', 0)
        ->orderBy('start_date', 'desc')
        ->get();

    return view(
        'student_interactions.partials.select_students_modal',
        compact('batches')
    );
}

    /* ================= LOAD STUDENTS ================= */
   public function loadStudentsByBatch(Request $request)
{
    $batchIds = (array) $request->batch_ids;

    if (empty($batchIds)) {
        return response()->json([]);
    }

    $students = Students::whereHas('coursePayments', function ($q) use ($batchIds) {
            $q->whereIn('course_schedule_id', $batchIds)
              ->where('status', 'active');
        })
        ->with(['university:id,name'])
        ->select('id', 'first_name', 'last_name', 'email', 'phone_number')
        ->orderBy('first_name')
        ->get()
        ->map(function ($s) {
            return [
                'id'    => $s->id,
                'name'  => trim($s->first_name . ' ' . $s->last_name),
                'email' => $s->email,
                'phone' => $s->phone_number,
                'university' => $s->university,
            ];
        });

    return response()->json($students);
}
}
