<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeView;

class PublicNoticeController extends Controller
{
public function index()
    {
$today = now()->toDateString();

// Departmental (admin/teacher-এর notice)
$departmental = Notice::where('status', 'published')
            ->whereDoesntHave('author', fn($q) => $q->where('role', 'cr'))
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get();

// Class Updates (CR-এর notice) — public page-এ কে দেখছে জানা যায় না, তাই সব দেখাই
$classUpdates = Notice::where('status', 'published')
            ->whereHas('author', fn($q) => $q->where('role', 'cr'))
            ->latest()
            ->get();

return view('public.notices', compact('departmental', 'classUpdates'));
    }

    public function show(Notice $notice)
    {
       
        NoticeView::create(['notice_id' => $notice->id]);
        return view('public.show', compact('notice'));
    }
}