<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeView;
use Illuminate\Http\Request;
use App\Services\FcmService;

class NoticeApiController extends Controller
{
public function index(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        // ── Departmental notices (admin/teacher-এর) ──
        $departmental = Notice::where('status', 'published')
            ->whereDoesntHave('author', fn($q) => $q->where('role', 'cr'))
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get()
            ->map(fn($n) => $this->format($n));

        // ── Class updates (CR-এর) ──
        $classQuery = Notice::where('status', 'published')
            ->whereHas('author', fn($q) => $q->where('role', 'cr'));

        if (in_array($user->role, ['student', 'cr']) && $user->year && $user->section) {
            $classQuery->where('year', $user->year)
                       ->where('section', $user->section);
        }

        $classUpdates = $classQuery->latest()->get()->map(fn($n) => $this->format($n));

        return response()->json([
            'departmental'  => $departmental->values(),
            'class_updates' => $classUpdates->values(),
        ]);
    }

    public function show(Notice $notice)
    {
        return response()->json($this->format($notice));
    }

    public function logView(Notice $notice)
    {
        NoticeView::create(['notice_id' => $notice->id]);
        return response()->json(['ok' => true]);
    }

private function format($n)
    {
        return [
            'id'         => $n->id,
            'title'      => $n->title,
            'body'       => $n->body,
            'type'       => $n->type,
            'priority'   => $n->priority,
            'file_url'   => $n->file_path ? asset('storage/' . $n->file_path) : null,
            'ai_summary' => $n->ai_summary,
            'year'       => $n->year,
            'section'    => $n->section,
            'author'     => $n->author->name ?? null,
            'created_at' => $n->created_at,
        ];
    }


    public function myNotifications(Request $request)
{
    $user = $request->user();   

    if (!in_array($user->role, ['teacher', 'super_admin'])) {
        return response()->json(['notices' => [], 'unseen' => 0]);
    }

    $notices = Notice::where('notified_teacher_id', $user->id)
        ->with('author')
        ->latest()
        ->get()
        ->map(fn($n) => [
            'id'        => $n->id,
            'title'     => $n->title,
            'body'      => $n->body,
            'priority'  => $n->priority,
            'year'      => $n->year,
            'section'   => $n->section,
            'from'      => $n->author->name ?? 'Unknown',
            'seen'      => (bool) $n->notified_seen,
            'created'   => $n->created_at->diffForHumans(),
            'reply'     => $n->teacher_reply,
        ]);

    $unseen = Notice::where('notified_teacher_id', $user->id)
        ->where('notified_seen', false)->count();

    return response()->json(['notices' => $notices, 'unseen' => $unseen]);
}

public function myCrNotices(Request $request)
{
    $user = $request->user();
    if ($user->role !== 'cr') {
        return response()->json([]);
    }

    return Notice::where('author_id', $user->id)
        ->with('notifiedTeacher')
        ->latest()
        ->get()
        ->map(fn($n) => [
            'id'          => $n->id,
            'title'       => $n->title,
            'body'        => $n->body,
            'priority'    => $n->priority,
            'year'        => $n->year,
            'section'     => $n->section,
            'teacher'     => $n->notifiedTeacher->name ?? null,
            'reply'       => $n->teacher_reply,
            'replied_at'  => $n->replied_at?->diffForHumans(),
        ]);
}

public function deleteCrNotice(Request $request, Notice $notice)
{
    $user = $request->user();
    
    if ($notice->author_id !== $user->id) {
        return response()->json(['error' => 'Not allowed'], 403);
    }
    $notice->delete();
    return response()->json(['ok' => true]);
}



public function markNotificationsSeen(Request $request)
{
    $user = $request->user();
    Notice::where('notified_teacher_id', $user->id)
        ->where('notified_seen', false)
        ->update(['notified_seen' => true]);
    return response()->json(['ok' => true]);
}

public function teachers()
{
    return \App\Models\User::where('role','teacher')->where('status','active')
        ->orderBy('name')->get(['id','name']);
}

public function crStore(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'title'     => 'required|string|max:255',
        'body'      => 'required|string',
        'priority'  => 'required|in:high,medium,low',
        'course_id' => 'required|exists:courses,id',
        'notified_teacher_id' => 'required|exists:users,id',
    ]);

    // যাচাই: এই teacher কি সত্যিই ওই course-এর?
    $course = \App\Models\Course::find($data['course_id']);
    if (!$course->teachers->contains($data['notified_teacher_id'])) {
        return response()->json([
            'message' => 'The selected teacher does not teach this course.',
        ], 422);
    }

    $notice = Notice::create([
        'title'    => $data['title'],
        'body'     => $data['body'],
        'priority' => $data['priority'],
        'type'     => 'text',
        'status'   => 'published',
        'author_id'=> $user->id,
        'year'     => $user->year,
        'section'  => $user->section,
        'course_id' => $data['course_id'],
        'notified_teacher_id' => $data['notified_teacher_id'],
    ]);

    $teacher = \App\Models\User::find($notice->notified_teacher_id);
    if ($teacher && $teacher->fcm_token) {
        app(FcmService::class)->send(
            $teacher->fcm_token,
            "New Notice: {$course->course_no}",
            "{$user->name} ({$user->year}-{$user->section}): {$notice->title}",
            ['notice_id' => $notice->id, 'type' => 'cr_notice']
        );
    }

    return response()->json(['message' => 'Notice posted', 'notice' => $notice], 201);
}

public function replyToNotice(Request $request, Notice $notice)
{
    $user = $request->user();
    if ($notice->notified_teacher_id !== $user->id) {
        return response()->json(['error' => 'Not allowed'], 403);
    }
    $data = $request->validate(['reply' => 'required|string|max:1000']);
    $notice->update(['teacher_reply' => $data['reply'], 'replied_at' => now()]);

        $cr = \App\Models\User::find($notice->author_id);
    if ($cr && $cr->fcm_token) {
        app(FcmService::class)->send(
            $cr->fcm_token,
            'Teacher Replied',
            "{$user->name} replied to your notice: {$notice->title}",
            ['notice_id' => $notice->id, 'type' => 'reply']
        );
    }


    return response()->json(['ok' => true]);
}

public function courses()
{
    return response()->json(
        \App\Models\Course::orderBy('course_no')->get(['id', 'course_no', 'course_title'])
    );
}

public function courseTeachers(\App\Models\Course $course)
{
    return response()->json(
        $course->teachers()->get(['users.id', 'users.name'])
    );
}


}