<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employees;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        register as traitRegister; // Alias the original register method
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'emp_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $profilePicturePath = null;
        if (request()->hasFile('profile_picture')) {
            $file = request()->file('profile_picture');
            $profilePicturePath = $file->store('profile_pictures', 'public');
        }
        $user_create = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role'],
            'password' => Hash::make($data['password']),
            'profile_picture' => $profilePicturePath,
        ]);
        $employees = new Employees();
        $employees->emp_code =  $data['emp_code'];
        $employees->user_id =  $user_create->id;
        $employees->first_name =  $data['name'];
        $employees->email =  $data['email'];
        $employees->save();
        return $user_create;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $this->create($request->all());

        // Do not log in the user. Instead, redirect to the registration page.
        return redirect()->route('register')->with('success', 'Registration successful. Verify email and Login');
    }
}
