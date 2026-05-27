<?php
// app/Http/Controllers/StudentController.php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('full_name', 'like', "%$s%")->orWhere('reg_no', 'like', "%$s%"));
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->fingerprint === 'enrolled') {
            $query->whereNotNull('fingerprint');
        } elseif ($request->fingerprint === 'not_enrolled') {
            $query->whereNull('fingerprint');
        }

        $students       = $query->orderBy('full_name')->paginate(20);
        $courses        = Course::orderBy('name')->get();
        $enrolledCount  = Student::whereNotNull('fingerprint')->count();
        $notEnrolledCount = Student::whereNull('fingerprint')->count();

        return view('students.index', compact('students', 'courses', 'enrolledCount', 'notEnrolledCount'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get();
        return view('students.form', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'reg_no'    => 'required|string|max:50|unique:students,reg_no',
            'gender'    => 'required|in:Male,Female',
            'dob'       => 'required|date',
            'course_id' => 'required|exists:courses,course_id',
        ]);

        Student::create($data);
        return redirect()->route('students.index')->with('success', 'Student registered successfully.');
    }

    public function show(Student $student)
    {
        $student->load('course', 'attendance.exam');
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $courses = Course::orderBy('name')->get();
        return view('students.form', compact('student', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'reg_no'    => 'required|string|max:50|unique:students,reg_no,' . $student->student_id . ',student_id',
            'gender'    => 'required|in:Male,Female',
            'dob'       => 'required|date',
            'course_id' => 'required|exists:courses,course_id',
        ]);

        $student->update($data);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    // ── Fingerprint Enrollment ────────────────────────────────────────────

    public function enroll(Student $student)
    {
        return view('students.enroll', compact('student'));
    }

    public function saveEnroll(Request $request, Student $student)
    {
        $request->validate([
            'fingerprint_template' => 'required|string',
        ]);

        // Encrypt the fingerprint template before saving
        // In production use: encrypt($request->fingerprint_template)
        // or your biometric SDK's built-in template protection.
        $student->update([
            'fingerprint' => encrypt($request->fingerprint_template),
        ]);

        return redirect()->route('students.index')
            ->with('success', "Fingerprint enrolled successfully for {$student->full_name}.");
    }
}