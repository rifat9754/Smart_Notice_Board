<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teachers')->latest()->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::whereIn('role', ['teacher', 'super_admin'])
            ->where('status', 'active')
            ->orderBy('name')->get();

        return view('courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_no'    => 'required|string|max:50',
            'course_title' => 'required|string|max:255',
            'teachers'     => 'required|array|min:1',      // একাধিক teacher
            'teachers.*'   => 'exists:users,id',
        ]);

        $course = Course::create([
            'course_no'    => $data['course_no'],
            'course_title' => $data['course_title'],
        ]);

        $course->teachers()->sync($data['teachers']);      // teacher যুক্ত করো

        return redirect()->route('courses.index')->with('success', 'Course added successfully.');
    }

    public function edit(Course $course)
    {
        $teachers = User::whereIn('role', ['teacher', 'super_admin'])
            ->where('status', 'active')
            ->orderBy('name')->get();

        $course->load('teachers');
        return view('courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'course_no'    => 'required|string|max:50',
            'course_title' => 'required|string|max:255',
            'teachers'     => 'required|array|min:1',
            'teachers.*'   => 'exists:users,id',
        ]);

        $course->update([
            'course_no'    => $data['course_no'],
            'course_title' => $data['course_title'],
        ]);

        $course->teachers()->sync($data['teachers']);

        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Course deleted.');
    }
}