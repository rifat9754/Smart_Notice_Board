<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrController extends Controller
{
    // CR-এর নিজের দেওয়া notice-এর তালিকা
    public function index()
    {
        $notices = Notice::where('author_id', Auth::id())
            ->with('notifiedTeacher')
            ->latest()
            ->get();
        return view('cr.index', compact('notices'));
    }

    // নতুন notice form (teacher list সহ)
    public function create()
    {
        $teachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();
        return view('cr.create', compact('teachers'));
    }

    public function store(Request $request)
    {
$data = $request->validate([
    'title'               => 'required|string|max:255',
    'body'                => 'required|string',
    'priority'            => 'required|in:high,medium,low',
    'year'                => 'required|in:1st,2nd,3rd,4th',
    'section'             => 'required|in:A,B',
    'notified_teacher_id' => 'nullable|exists:users,id',
]);

        $notice = Notice::create([
            'title'               => $data['title'],
            'body'                => $data['body'],
            'type'                => 'text',
            'priority'            => $data['priority'],
            'status'              => 'published',   // CR notice সরাসরি প্রকাশ
            'is_emergency'        => false,
            'author_id'           => Auth::id(),
            'notified_teacher_id' => $data['notified_teacher_id'] ?? null,
            'notified_seen'       => false,
            'year'    => $data['year'],
            'section' => $data['section'],
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'created (CR)',
            'target_type' => 'Notice',
            'target_id'   => $notice->id,
        ]);

        return redirect()->route('cr.index')->with('success', 'Notice posted successfully.');
    }

    public function destroy(Notice $notice)
    {
        // CR শুধু নিজের notice মুছতে পারবে
        if ($notice->author_id !== Auth::id()) {
            abort(403);
        }
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'deleted (CR)',
            'target_type' => 'Notice',
            'target_id'   => $notice->id,
        ]);
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }
}