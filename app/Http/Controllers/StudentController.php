<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeView;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
public function feed()
    {
$user  = Auth::user();
$today = now()->toDateString();

// Departmental (admin/teacher-এর notice)
$departmental = Notice::where('status', 'published')
            ->whereDoesntHave('author', fn($q) => $q->where('role', 'cr'))
            ->where(fn($q) => $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('show_to')->orWhereDate('show_to', '>=', $today))
            ->latest()
            ->get();

// Class Updates (CR-এর notice) — নিজের year/section-এর
$classQuery = Notice::where('status', 'published')
            ->whereHas('author', fn($q) => $q->where('role', 'cr'));

if ($user->year && $user->section) {
$classQuery->where('year', $user->year)->where('section', $user->section);
}

$classUpdates = $classQuery->latest()->get();

return view('student.feed', compact('departmental', 'classUpdates'));
    }

    public function show(Notice $notice)
    {
        NoticeView::create(['notice_id' => $notice->id]); // view count বাড়বে (analytics-এ যোগ হবে)
        return view('student.show', compact('notice'));
    }
}