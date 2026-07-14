<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\FcmService;

class TeacherNotificationController extends Controller
{
   
public function index()
{
    $user = Auth::user();

    // ১. CR-এর পাঠানো (এই teacher-কে notify করা) — আগের মতো
    $fromCr = Notice::with('author', 'course')
        ->where('notified_teacher_id', $user->id)
        ->latest()
        ->get();

    // ২. Admin-এর teacher-notice
    $forTeachers = Notice::with('author')
        ->where('audience', 'teachers')
        ->where('status', 'published')
        ->latest()
        ->get();

    // seen mark করো (CR notice গুলো)
    Notice::where('notified_teacher_id', $user->id)
        ->where('notified_seen', false)
        ->update(['notified_seen' => true]);

    return view('teacher.notifications', compact('fromCr', 'forTeachers'));
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


        $cr = \App\Models\User::find($notice->author_id);
    if ($cr && $cr->fcm_token) {
        $teacher = Auth::user();
        app(FcmService::class)->send(
            $cr->fcm_token,
            'Teacher Replied',
            "{$teacher->name} replied to your notice: {$notice->title}",
            ['notice_id' => $notice->id, 'type' => 'reply']
        );
    }


    return back()->with('success', 'Reply sent to CR.');
}
}