<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// All authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze built-in - keep)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/enroll',  [StudentController::class, 'enroll'])->name('students.enroll');
    Route::post('/students/{student}/enroll', [StudentController::class, 'saveEnroll'])->name('students.enroll.save');

    // Exams
    Route::resource('exams', ExamController::class);

    // Attendance
    Route::get('/attendance',              [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/verify',       [AttendanceController::class, 'verify'])->name('attendance.verify');
    Route::post('/attendance/verify-ajax', [AttendanceController::class, 'verifyAjax'])->name('attendance.verify.ajax');

    // Reports
    Route::get('/reports/attendance',    [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/export/pdf',    [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel',  [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Courses & Subjects
    Route::resource('courses',  CourseController::class)->except(['show']);
    Route::resource('subjects', SubjectController::class)->except(['show']);
});

require __DIR__.'/auth.php';