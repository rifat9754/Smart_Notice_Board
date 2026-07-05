<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeView;

class PublicNoticeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $notices = Notice::where('status', 'published')
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get();
        return view('public.notices', compact('notices'));
    }

    public function show(Notice $notice)
    {
       
        NoticeView::create(['notice_id' => $notice->id]);
        return view('public.show', compact('notice'));
    }
}