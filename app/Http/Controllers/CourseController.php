<?php

// ═══════════════════════════════════════════════════════════════
// app/Http/Controllers/CourseController.php
// ═══════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('students')->orderBy('name')->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:courses,name']);
        Course::create($request->only('name'));
        return redirect()->route('courses.index')->with('success', 'Course added successfully.');
    }

    public function edit(Course $course)
    {
        return view('courses.form', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:courses,name,' . $course->course_id . ',course_id'
        ]);
        $course->update($request->only('name'));
        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        if ($course->students()->exists()) {
            return redirect()->route('courses.index')
                ->with('error', 'Cannot delete course with enrolled students.');
        }
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }
}
