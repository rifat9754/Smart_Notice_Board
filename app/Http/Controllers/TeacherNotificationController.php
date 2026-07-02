<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class TeacherNotificationController extends Controller
{
   
    public function index()
    {
        $notices = Notice::where('notified_teacher_id', Auth::id())
            ->with('author')
            ->latest()
            ->get();


        Notice::where('notified_teacher_id', Auth::id())
            ->where('notified_seen', false)
            ->update(['notified_seen' => true]);

        return view('teacher.notifications', compact('notices'));
    }

    public function reply(Request $request, Notice $notice)
{
   
    if ($notice->notified_teacher_id !== Auth::id()) {
        abort(403);
    }
    $data = $request->validate(['teacher_reply' => 'required|string|max:1000']);
    $notice->update([
        'teacher_reply' => $data['teacher_reply'],
        'replied_at'    => now(),
    ]);
    return back()->with('success', 'Reply sent to CR.');
}
}