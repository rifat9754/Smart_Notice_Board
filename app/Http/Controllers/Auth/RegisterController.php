<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
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

    protected function registered(\Illuminate\Http\Request $request, $user)
{
    auth()->logout();
    return redirect('/login')->with('error',
        'Registration successful! Please wait for admin approval before logging in.');
}

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
protected function validator(array $data)
{
    return Validator::make($data, [
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
        'role'     => ['required', 'in:teacher,student'],
    ], [
        'role.in' => 'Please select a valid role.',
    ])->after(function ($validator) use ($data) {
        // student হলে email অবশ্যই @stud.kuet.ac.bd হতে হবে
        if (($data['role'] ?? null) === 'student'
            && ! str_ends_with(strtolower($data['email'] ?? ''), '@stud.kuet.ac.bd')) {
            $validator->errors()->add('email', 'Students must use a @stud.kuet.ac.bd email.');
        }
    });
}

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
protected function create(array $data)
{
    return User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => Hash::make($data['password']),
        'role'     => $data['role'],
        'status'   => 'pending',   // approve-এর অপেক্ষায়
    ]);
}
}
