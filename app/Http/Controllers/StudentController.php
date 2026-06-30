<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeView;

class StudentController extends Controller
{
    public function feed()
    {
        $today = now()->toDateString();

        $notices = Notice::where('status', 'published')
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get();

        return view('student.feed', compact('notices'));
    }

    public function show(Notice $notice)
    {
        NoticeView::create(['notice_id' => $notice->id]); // view count বাড়বে (analytics-এ যোগ হবে)
        return view('student.show', compact('notice'));
    }
}