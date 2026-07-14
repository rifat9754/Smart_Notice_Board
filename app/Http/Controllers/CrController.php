<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FcmService;

class CrController extends Controller
{
    // CR-এর নিজের দেওয়া notice-এর তালিকা
    public function index()
    {
        $notices = Notice::where('author_id', Auth::id())
            ->with('notifiedTeacher')
            ->latest()
            ->get();
        return view('cr.index', compact('notices'));
    }


public function create()
    {
$user = Auth::user();

$courses = \App\Models\Course::with('teachers')
    ->when($user->year, fn($q) => $q->where('year', $user->year))
    ->orderBy('course_no')
    ->get();

return view('cr.create', compact('courses'));
    }

 public function store(Request $request)
{
$user = Auth::user();

$data = $request->validate([
'title'               => 'required|string|max:255',
'body'                => 'required|string',
'priority'            => 'required|in:high,medium,low',
'course_id'           => 'required|exists:courses,id',
'notified_teacher_id' => 'required|exists:users,id',
    ]);

$course = \App\Models\Course::find($data['course_id']);

// CR-এর year-এর course কিনা যাচাই
if ($user->year && $course->year && $course->year !== $user->year) {
return back()->withErrors(['course_id' => "You can only post notices for your own year's courses."])->withInput();
}

// যাচাই: এই teacher কি সত্যিই ওই course-এর?
if (!$course->teachers->contains($data['notified_teacher_id'])) {
return back()->withErrors(['notified_teacher_id' => 'The selected teacher does not teach this course.'])->withInput();
}

$notice = Notice::create([
'title'               => $data['title'],
'body'                => $data['body'],
'type'                => 'text',
'priority'            => $data['priority'],
'status'              => 'published',
'is_emergency'        => false,
'author_id'           => $user->id,
'course_id'           => $data['course_id'],
'notified_teacher_id' => $data['notified_teacher_id'],
'notified_seen'       => false,
'year'    => $user->year,
'section' => $user->section,
    ]);

AuditLog::create([
'user_id'     => Auth::id(),
'action'      => 'created (CR)',
'target_type' => 'Notice',
'target_id'   => $notice->id,
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

return redirect()->route('cr.index')->with('success', 'Notice posted successfully.');
    }   

    public function destroy(Notice $notice)
    {
        
        if ($notice->author_id !== Auth::id()) {
            abort(403);
        }
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'deleted (CR)',
            'target_type' => 'Notice',
            'target_id'   => $notice->id,
        ]);
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }
}