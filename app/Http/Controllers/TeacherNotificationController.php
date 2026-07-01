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
}