<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Exam;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $query = Attendance::with(['student.course', 'exam.subject', 'user']);

        if ($request->filled('from')) {
            $query->whereDate('time', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('time', '<=', $request->to);
        }
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest('time')->paginate(30);
        $exams   = Exam::orderBy('exam_date', 'desc')->get();

        $summary = [
            'total'    => Attendance::count(),
            'verified' => Attendance::where('status', 'Verified')->count(),
            'rejected' => Attendance::where('status', 'Rejected')->count(),
        ];

        return view('reports.attendance', compact('records', 'exams', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $query = Attendance::with(['student.course', 'exam.subject', 'user']);

        if ($request->filled('from'))    $query->whereDate('time', '>=', $request->from);
        if ($request->filled('to'))      $query->whereDate('time', '<=', $request->to);
        if ($request->filled('exam_id')) $query->where('exam_id', $request->exam_id);
        if ($request->filled('status'))  $query->where('status', $request->status);

        $records = $query->latest('time')->get();
        $summary = [
            'total'    => $records->count(),
            'verified' => $records->where('status', 'Verified')->count(),
            'rejected' => $records->where('status', 'Rejected')->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf', compact('records', 'summary'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('attendance_report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Attendance::with(['student.course', 'exam.subject', 'user']);

        if ($request->filled('from'))    $query->whereDate('time', '>=', $request->from);
        if ($request->filled('to'))      $query->whereDate('time', '<=', $request->to);
        if ($request->filled('exam_id')) $query->where('exam_id', $request->exam_id);
        if ($request->filled('status'))  $query->where('status', $request->status);

        $records = $query->latest('time')->get();

        // Build CSV manually (no extra package needed)
        $filename = 'attendance_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');

            // CSV header row
            fputcsv($handle, [
                '#', 'Student Name', 'Reg. Number', 'Course',
                'Examination', 'Subject', 'Date', 'Time',
                'Supervisor', 'Status'
            ]);

            foreach ($records as $i => $r) {
                fputcsv($handle, [
                    $i + 1,
                    $r->student->full_name  ?? '—',
                    $r->student->reg_no     ?? '—',
                    $r->student->course->name ?? '—',
                    $r->exam->name          ?? '—',
                    $r->exam->subject->name ?? '—',
                    \Carbon\Carbon::parse($r->time)->format('d M Y'),
                    \Carbon\Carbon::parse($r->time)->format('H:i:s'),
                    $r->user->name          ?? '—',
                    $r->status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
