<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $pending = User::where('status', 'pending')->latest()->get();
        $all     = User::where('status', '!=', 'pending')->latest()->get();
        return view('approvals.index', compact('pending', 'all'));
    }

    public function approve(Request $request, User $user)
    {
        $role = $request->input('role', $user->role); // admin চাইলে role বদলাতে পারে
        $user->update(['status' => 'active', 'role' => $role]);
        $this->log('approved', $user);
        return back()->with('success', "{$user->name} approved as {$role}.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);
        $this->log('rejected', $user);
        return back()->with('success', "{$user->name} rejected.");
    }

    public function deactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }
        $user->update(['status' => 'inactive']);
        $this->log('deactivated', $user);
        return back()->with('success', "{$user->name} deactivated.");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        $this->log('activated', $user);
        return back()->with('success', "{$user->name} re-activated.");
    }

    private function log($action, $user)
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => 'User',
            'target_id'   => $user->id,
        ]);
    }
}