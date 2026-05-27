@extends('layouts.app')
@section('title', isset($exam) ? 'Edit Exam' : 'Schedule Exam')
@section('page-title', isset($exam) ? 'Edit Examination' : 'Schedule New Examination')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="mb-4">
            <a href="{{ route('exams.index') }}" class="text-decoration-none text-muted" style="font-size:.85rem;">
                <i class="bi bi-arrow-left me-1"></i> Back to Exams
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-{{ isset($exam) ? 'text' : 'plus' }} me-2"></i>
                {{ isset($exam) ? 'Update Examination' : 'Schedule New Examination' }}
            </div>
            <div class="card-body p-4">
                <form method="POST"
                      action="{{ isset($exam) ? route('exams.update', $exam) : route('exams.store') }}">
                    @csrf
                    @if(isset($exam)) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Please fix errors:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errors->all() as $e)<li style="font-size:.85rem;">{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $exam->name ?? '') }}"
                                   placeholder="e.g. End of Semester Examination - IT 2024">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
                                <option value="">Select subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->subject_id }}"
                                        {{ old('subject_id', $exam->subject_id ?? '') == $subject->subject_id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Exam Date <span class="text-danger">*</span></label>
                            <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror"
                                   value="{{ old('exam_date', isset($exam) ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : '') }}">
                            @error('exam_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="exam_time" class="form-control @error('exam_time') is-invalid @enderror"
                                   value="{{ old('exam_time', isset($exam) ? \Carbon\Carbon::parse($exam->exam_time)->format('H:i') : '') }}">
                            @error('exam_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary-ihet px-4">
                            <i class="bi bi-{{ isset($exam) ? 'check2' : 'plus-circle' }} me-2"></i>
                            {{ isset($exam) ? 'Update Exam' : 'Schedule Exam' }}
                        </button>
                        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection