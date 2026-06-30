<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:super_admin,teacher,student',
            'department' => 'nullable|string|max:255',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'created',
            'target_type' => 'User',
            'target_id'   => $user->id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'deleted',
            'target_type' => 'User',
            'target_id'   => $user->id,
        ]);

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}