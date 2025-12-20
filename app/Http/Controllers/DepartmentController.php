<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departments;

class DepartmentController extends Controller
{
    // PAGE LOAD (AJAX via sidebar)
    public function index()
    {
        $departments = Departments::orderBy('id', 'desc')->get();
        return view('departments.index', compact('departments'));
    }

    // LOAD FORM (OFFCANVAS)
    public function manage(Request $request)
    {
        $department = null;

        if ($request->id) {
            $department = Departments::findOrFail($request->id);
        }

        return view('departments.manage', compact('department'));
    }

    // SAVE (CREATE / UPDATE)
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $request->id,
        ]);

        Departments::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'description' => $request->description,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Department saved successfully'
        ]);
    }

    // DELETE
    public function delete(Request $request)
    {
        Departments::findOrFail($request->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Department deleted successfully'
        ]);
    }
}
