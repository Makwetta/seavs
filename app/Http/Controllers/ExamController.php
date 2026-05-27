<?php
// ═══════════════════════════════════════════════════════════════════
// app/Http/Controllers/ExamController.php
// ═══════════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with('subject')
            ->withCount(['attendance as attendance_count' => fn($q) => $q->where('status', 'Verified')]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('exam_date', $request->date);
        }

        $exams = $query->orderBy('exam_date', 'desc')->orderBy('exam_time')->paginate(20);
        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('exams.form', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,subject_id',
            'exam_date'  => 'required|date',
            'exam_time'  => 'required',
        ]);

        Exam::create($data);
        return redirect()->route('exams.index')->with('success', 'Examination scheduled successfully.');
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::orderBy('name')->get();
        return view('exams.form', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'subject_id' => 'required|exists:subjects,subject_id',
            'exam_date'  => 'required|date',
            'exam_time'  => 'required',
        ]);

        $exam->update($data);
        return redirect()->route('exams.index')->with('success', 'Examination updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Examination deleted.');
    }
}