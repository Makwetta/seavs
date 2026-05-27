<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student.course', 'exam.subject', 'user']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('full_name', 'like', "%$s%")->orWhere('reg_no', 'like', "%$s%")
            );
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('time', $request->date);
        }

        $records       = $query->latest('time')->paginate(25);
        $exams         = Exam::orderBy('exam_date', 'desc')->get();
        $verifiedCount = Attendance::where('status', 'Verified')->count();
        $rejectedCount = Attendance::where('status', 'Rejected')->count();

        return view('attendance.index', compact('records', 'exams', 'verifiedCount', 'rejectedCount'));
    }

    public function verify(Request $request)
    {
        $exams = Exam::with('subject')
            ->orderBy('exam_date', 'desc')
            ->orderBy('exam_time')
            ->get();
        return view('attendance.verify', compact('exams'));
    }

    /**
     * AJAX endpoint: receives fingerprint template + exam_id,
     * matches against all enrolled students, saves record.
     *
     * In production, integrate your biometric SDK here for real matching.
     * The current implementation does a basic encrypted-template comparison.
     */
    public function verifyAjax(Request $request)
    {
        $request->validate([
            'fingerprint_template' => 'required|string',
            'exam_id'              => 'required|exists:exams,exam_id',
        ]);

        $incomingTemplate = $request->fingerprint_template;
        $examId           = $request->exam_id;
        $matchedStudent   = null;

        // ── REPLACE WITH REAL BIOMETRIC SDK MATCHING ─────────────────────
        // In production:
        //   1. Load all enrolled student fingerprint templates from DB
        //   2. Pass to SDK: $match = $biometricSdk->identify($incomingTemplate, $storedTemplates);
        //   3. SDK returns matched student_id or null
        //
        // Simple demo: compare decrypted templates
        $students = Student::whereNotNull('fingerprint')->get();
        foreach ($students as $student) {
            try {
                $stored = decrypt($student->fingerprint);
                if ($stored === $incomingTemplate) {
                    $matchedStudent = $student;
                    break;
                }
            } catch (\Exception $e) {
                continue; // decryption failed, skip
            }
        }
        // ─────────────────────────────────────────────────────────────────

        $status = $matchedStudent ? 'Verified' : 'Rejected';

        // Record the attempt
        Attendance::create([
            'student_id' => $matchedStudent?->student_id,
            'exam_id'    => $examId,
            'user_id'    => Auth::id(),
            'time'       => now(),
            'status'     => $status,
        ]);

        if ($matchedStudent) {
            return response()->json([
                'success' => true,
                'message' => 'Identity verified successfully',
                'student' => [
                    'name'   => $matchedStudent->full_name,
                    'reg_no' => $matchedStudent->reg_no,
                    'course' => $matchedStudent->course?->name,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Fingerprint not recognized. Student not enrolled or not registered.',
        ]);
    }
}