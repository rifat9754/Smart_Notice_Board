<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        if ($user->status !== 'active') {
            auth()->logout();
            return redirect('/login')->with('error',
                'Your account is awaiting admin approval (or has been deactivated).');
        }
    }


protected function redirectTo()
{
    $role = auth()->user()->role;
    if ($role === 'student') return '/feed';
    if ($role === 'cr') return '/cr';
    return '/home';
}

    protected function loggedOut(\Illuminate\Http\Request $request)
    {
        return redirect('/');
    }
}