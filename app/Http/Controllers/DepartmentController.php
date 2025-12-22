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

        return view('departments.index', compact('departments'));
    }

    public function manage(Request $request)
    {
        $department = null;

        if ($request->id) {
            $department = Departments::with('users:id')->findOrFail($request->id);
        }

        // 🔥 GUARANTEE UNIQUE USERS
        $users = User::query()
            ->where('is_active', 1)
            ->select('id', 'name')
            ->groupBy('id', 'name')
            ->orderBy('name')
            ->get();

        return view('departments.manage', compact('department', 'users'));
    }

    public function save(Request $request)
    {
        // TOGGLE STATUS
        if ($request->has('toggle_status')) {
            $dept = Departments::findOrFail($request->id);
            $dept->status = !$dept->status;
            $dept->save();

            return response()->json([
                'status' => $dept->status ? 1 : 0
            ]);
        }

        // NORMAL SAVE
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

        // 🔥 ALWAYS SYNC (EMPTY ARRAY REMOVES ALL)
        $users = collect($request->input('users', []))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $department->users()->sync($users);

        return response()->json([
            'message' => 'Department saved successfully'
        ]);
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
}
