<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeView;
use Illuminate\Http\Request;

class NoticeApiController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $notices = Notice::where('status', 'published')
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get()
            ->map(fn($n) => $this->format($n));

        return response()->json($notices);
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
        ]);

    $unseen = Notice::where('notified_teacher_id', $user->id)
        ->where('notified_seen', false)->count();

    return response()->json(['notices' => $notices, 'unseen' => $unseen]);
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
    if ($user->role !== 'cr') return response()->json(['error'=>'Not allowed'], 403);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
        'priority' => 'required|in:high,medium,low',
        'year' => 'required',
        'section' => 'required',
        'notified_teacher_id' => 'nullable|exists:users,id',
    ]);

    $notice = \App\Models\Notice::create([
        'title'=>$data['title'], 'body'=>$data['body'], 'type'=>'text',
        'priority'=>$data['priority'], 'status'=>'published', 'is_emergency'=>false,
        'author_id'=>$user->id, 'year'=>$data['year'], 'section'=>$data['section'],
        'notified_teacher_id'=>$data['notified_teacher_id'] ?? null, 'notified_seen'=>false,
    ]);
    return response()->json(['ok'=>true, 'id'=>$notice->id]);
}
}