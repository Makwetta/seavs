<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('exams')->orderBy('name')->get();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:subjects,name']);
        Subject::create($request->only('name'));
        return redirect()->route('subjects.index')->with('success', 'Subject added successfully.');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.form', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:subjects,name,' . $subject->subject_id . ',subject_id'
        ]);
        $subject->update($request->only('name'));
        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->exams()->exists()) {
            return redirect()->route('subjects.index')
                ->with('error', 'Cannot delete subject linked to existing exams.');
        }
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }
}
