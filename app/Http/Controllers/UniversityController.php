<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function viewUniversities()
    {
        // TODO: implement
        return view('universities.view');
    }

    public function manageUniversity($id = null)
    {
        // TODO: implement
        return view('universities.manage');
    }

    public function storeUniversity(Request $request)
    {
        // TODO: implement
        return redirect()->back();
    }

    public function storeUniversitiesAPI(Request $request)
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented']);
    }
}