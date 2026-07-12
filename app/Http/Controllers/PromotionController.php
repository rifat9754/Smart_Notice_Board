<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    // promotion page দেখাও
    public function index()
    {
        // প্রতিটা year-এ কতজন student আছে
        $counts = [
            '1st' => User::where('role', 'student')->where('year', '1st')->count(),
            '2nd' => User::where('role', 'student')->where('year', '2nd')->count(),
            '3rd' => User::where('role', 'student')->where('year', '3rd')->count(),
            '4th' => User::where('role', 'student')->where('year', '4th')->count(),
        ];

        // individual update-এর জন্য সব student
        $students = User::whereIn('role', ['student', 'cr'])
            ->orderBy('year')->orderBy('section')->orderBy('name')
            ->get();

        return view('promotion.index', compact('counts', 'students'));
    }

    // একসাথে সবাইকে পরের year-এ তোলো (bulk)
    public function promoteAll(Request $request)
    {
        $request->validate([
            'from_year' => 'required|in:1st,2nd,3rd',
        ]);

        $next = ['1st' => '2nd', '2nd' => '3rd', '3rd' => '4th'];
        $from = $request->from_year;
        $to   = $next[$from];

        $count = User::whereIn('role', ['student', 'cr'])
            ->where('year', $from)
            ->update(['year' => $to]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => "promoted all {$from} to {$to} ({$count} students)",
            'target_type' => 'User',
            'target_id' => 0,
        ]);

        return back()->with('success', "{$count} students promoted from {$from} to {$to}.");
    }

    // একজনের year/section বদলাও (individual)
    public function updateStudent(Request $request, User $user)
    {
        $data = $request->validate([
            'year'    => 'required|in:1st,2nd,3rd,4th',
            'section' => 'required|in:A,B',
        ]);

        $user->update($data);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => "updated year/section of {$user->name}",
            'target_type' => 'User',
            'target_id' => $user->id,
        ]);

        return back()->with('success', "{$user->name} updated to {$data['year']} - Section {$data['section']}.");
    }

// 4th year-এর সব student/CR delete করো (graduation-এর পর)
    public function deleteFinalYear()
    {
        $students = User::whereIn('role', ['student', 'cr'])
            ->where('year', '4th')
            ->get();

        $count = $students->count();

        if ($count === 0) {
            return back()->with('success', 'No 4th year students to remove.');
        }

        // audit log আগে (delete-এর পর user_id থাকবে না)
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => "deleted all 4th year students ({$count})",
            'target_type' => 'User',
            'target_id'   => 0,
        ]);

        // তাদের notice-গুলোর author_id null করে দাও (নইলে foreign key ভাঙবে)
        \App\Models\Notice::whereIn('author_id', $students->pluck('id'))
            ->update(['author_id' => null]);

        User::whereIn('role', ['student', 'cr'])
            ->where('year', '4th')
            ->delete();

        return back()->with('success', "{$count} graduated (4th year) students removed from the system.");
    }
}