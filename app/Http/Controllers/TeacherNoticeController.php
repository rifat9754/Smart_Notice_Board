<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\AuditLog;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeacherNoticeController extends Controller
{
    // admin-এর পাঠানো teacher-notice গুলোর তালিকা
    public function index()
    {
        $notices = Notice::where('audience', 'teachers')
            ->with('author')
            ->latest()
            ->get();

        return view('teacher-notices.index', compact('notices'));
    }

    public function create()
    {
        return view('teacher-notices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'body'     => 'required|string',
            'priority' => 'required|in:high,medium,low',
        ]);

        $notice = Notice::create([
            'title'     => $data['title'],
            'body'      => $data['body'],
            'priority'  => $data['priority'],
            'type'      => 'text',
            'status'    => 'published',
            'audience'  => 'teachers',          // ← এটাই মূল
            'author_id' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'sent notice to teachers',
            'target_type' => 'Notice',
            'target_id'   => $notice->id,
        ]);

        // সব teacher-কে FCM notification
        $tokens = \App\Models\User::whereIn('role', ['teacher', 'super_admin'])
            ->where('status', 'active')
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($tokens)) {
            app(FcmService::class)->sendToMany(
                $tokens,
                'Notice from Head: ' . $notice->title,
                Str::limit(strip_tags($notice->body), 100),
                ['notice_id' => $notice->id, 'type' => 'from_head']
            );
        }

        return redirect()->route('teacher-notices.index')
            ->with('success', 'Notice sent to all teachers.');
    }

    public function destroy(Notice $notice)
    {
        // শুধু teacher-notice ই মুছতে দাও
        abort_unless($notice->audience === 'teachers', 403);

        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }

    public function edit(Notice $notice)
{
    abort_unless($notice->audience === 'teachers', 403);
    return view('teacher-notices.edit', compact('notice'));
}

public function update(Request $request, Notice $notice)
{
    abort_unless($notice->audience === 'teachers', 403);

    $data = $request->validate([
        'title'    => 'required|string|max:255',
        'body'     => 'required|string',
        'priority' => 'required|in:high,medium,low',
    ]);

    $notice->update($data);

    AuditLog::create([
        'user_id'     => Auth::id(),
        'action'      => 'updated teacher notice',
        'target_type' => 'Notice',
        'target_id'   => $notice->id,
    ]);

    return redirect()->route('teacher-notices.index')
        ->with('success', 'Notice updated.');
}
}