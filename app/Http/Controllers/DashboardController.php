<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Exam;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalStudents   = Student::count();
        $enrolledStudents = Student::whereNotNull('fingerprint')->count();
        $totalExams      = Exam::count();
        $todayExams      = Exam::whereDate('exam_date', $today)->count();
        $todayAttendance = Attendance::whereDate('time', $today)->where('status', 'Verified')->count();
        $rejectedToday   = Attendance::whereDate('time', $today)->where('status', 'Rejected')->count();

        $todayExamsList = Exam::with(['subject'])
            ->whereDate('exam_date', $today)
            ->withCount(['attendance as attendance_count' => fn($q) => $q->where('status', 'Verified')])
            ->withCount('attendance as enrolled_count')
            ->orderBy('exam_time')
            ->get();

        $recentAttendance = Attendance::with('student')
            ->latest('time')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalStudents', 'enrolledStudents', 'totalExams',
            'todayExams', 'todayAttendance', 'rejectedToday',
            'todayExamsList', 'recentAttendance'
        ));
    }
}