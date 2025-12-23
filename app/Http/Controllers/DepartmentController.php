<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Departments::with('users:id,name')
            ->orderBy('id', 'desc')
            ->get();

        $users = User::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('departments.index', compact('departments', 'users'));
    }

    public function manage(Request $request)
    {
        $department = null;

        if ($request->id) {
            $department = Departments::with('users:id')->findOrFail($request->id);
        }

        $users = User::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('departments.manage', compact('department', 'users'));
    }

    public function save(Request $request)
    {
        if ($request->has('toggle_status')) {
            $dept = Departments::findOrFail($request->id);
            $dept->status = !$dept->status;
            $dept->save();

            return response()->json(['status' => $dept->status ? 1 : 0]);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'required|string|max:20',
            'status'      => 'required|boolean',
            'users'       => 'nullable|array',
            'users.*'     => 'integer|exists:users,id',
        ]);

        $department = Departments::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        $department->users()->sync($request->input('users', []));

        return response()->json(['message' => 'Department saved successfully']);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:departments,id'
        ]);

        Departments::where('id', $request->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ]);
    }

    public function toggleUser(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:departments,id',
        'attach'        => 'array',
        'detach'        => 'array',
        'attach.*'      => 'exists:users,id',
        'detach.*'      => 'exists:users,id',
    ]);

    $dept = Departments::findOrFail($request->department_id);

    if (!empty($request->attach)) {
        $dept->users()->syncWithoutDetaching($request->attach);
    }

    if (!empty($request->detach)) {
        $dept->users()->detach($request->detach);
    }

    return response()->json(['success' => true]);
}


}
