<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use App\Models\Countries;
use App\Models\States;
use App\Models\Universities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    private $limit = 20;
    public function __construct() {}

    public function load_cities(Request $request)
    {

        if ($request->input('current_state_id') !== null) {
            $current_state_id = $request->input('current_state_id');
            $cities = Cities::with('state.country')
                ->where('state_id', $current_state_id)
                ->get();
        } else if ($request->input('city_id') !== null) {
            $cities = Cities::with('state.country')
                ->where('id', $request->input('city_id'))
                ->get();
        } else if ($request->input('current_state_ids') !== null) {
            $current_state_ids = $request->input('current_state_ids');
            $cities = Cities::with('state.country')
                ->whereIn('state_id', $current_state_ids)
                ->get();
        } else {
            $name = $request->input('name');
            $cities = Cities::with('state.country')
                ->where('name', 'LIKE', "$name%")
                ->take($this->limit)
                ->get();
        }



        return response()->json($cities);
    }

    public function load_states(Request $request)
    {
        if ($request->input('current_country_ids') !== null) {
            $current_country_ids = $request->input('current_country_ids');
            $states = States::with('country')->whereIn('country_id', $current_country_ids)->get();
            return response()->json($states);
        } else if ($request->input('current_country_id') !== null) {
            $current_country_id = $request->input('current_country_id');
            $states = States::with('country')->where('country_id', $current_country_id)->get();
            return response()->json($states);
        } else {
            $name = $request->input('name');
            $states = States::with('country')->where('name', 'LIKE', "$name%")->take($this->limit)->get();
            return response()->json($states);
        }
    }

    public function load_countries(Request $request)
    {

        if ($request->input('current_country_id') !== null) {
            $current_country_id = $request->input('current_country_id');
            $countries = Countries::where('id', $current_country_id)->get();
        } else if ($request->input('current_country_ids') !== null) {
            $current_country_ids = $request->input('current_country_ids');
            $countries = Countries::whereIn('id', $current_country_ids)->get();
        } else {
            $name = $request->input('name');
            $countries = Countries::where('name', 'LIKE', "$name%")->take($this->limit)->get();
        }

        return response()->json($countries);
    }

    public function load_university(Request $request)
    {
        if ($request->input('save_as_new')) {
            return 1;
        }

        if ($request->input('university_id') !== null) {
            $universities = Universities::where('id', $request->input('university_id'));
        } else {
            $name = $request->input('name');
            $universities = Universities::where('name', 'LIKE', "$name%")
                ->take($this->limit);
        }
        if ($request->input('active') !== null) {
            $universities = $universities->where('status', 'active');
        }
        $universities = $universities->select(
            'id',
            DB::raw("CONCAT(name, ' (',university_code,')') AS name")
        )->get();
        return response()->json($universities);
    }
}
