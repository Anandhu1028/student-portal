<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Departments::orderBy('id', 'desc')->get();
        return view('departments.index', compact('departments'));
    }

    public function manage(Request $request)
    {
        $department = null;

        if ($request->id) {
            $department = Departments::findOrFail($request->id);
        }

        return view('departments.manage', compact('department'));
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'required|string',
            'status'      => 'required|boolean',
        ]);

        Departments::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json([
            'status'  => true,
            'title'   => 'Department Saved',
            'message' => 'Department details saved successfully.'
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
