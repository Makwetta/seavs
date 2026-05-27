@extends('layouts.app')
@section('title', isset($student) ? 'Edit Student' : 'Add Student')
@section('page-title', isset($student) ? 'Edit Student' : 'Add New Student')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="mb-4">
            <a href="{{ route('students.index') }}" class="text-decoration-none text-muted" style="font-size:.85rem;">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-{{ isset($student) ? 'gear' : 'plus' }} me-2"></i>
                {{ isset($student) ? 'Update Student Information' : 'Register New Student' }}
            </div>
            <div class="card-body p-4">
                <form method="POST"
                      action="{{ isset($student) ? route('students.update', $student) : route('students.store') }}">
                    @csrf
                    @if(isset($student)) @method('PUT') @endif

                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Please fix the errors below:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errors->all() as $e)
                                    <li style="font-size:.85rem;">{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                   value="{{ old('full_name', $student->full_name ?? '') }}"
                                   placeholder="e.g. John Michael Doe">
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Registration Number <span class="text-danger">*</span></label>
                            <input type="text" name="reg_no" class="form-control @error('reg_no') is-invalid @enderror"
                                   value="{{ old('reg_no', $student->reg_no ?? '') }}"
                                   placeholder="e.g. IHET/DIT/2024/0001">
                            @error('reg_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select gender</option>
                                <option value="Male"   {{ old('gender', $student->gender ?? '') === 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                   value="{{ old('dob', isset($student) ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}">
                            @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Course <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror">
                                <option value="">Select course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_id }}"
                                        {{ old('course_id', $student->course_id ?? '') == $course->course_id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary-ihet px-4">
                            <i class="bi bi-{{ isset($student) ? 'check2' : 'person-plus' }} me-2"></i>
                            {{ isset($student) ? 'Update Student' : 'Register Student' }}
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        @if(isset($student) && !$student->fingerprint)
                            <a href="{{ route('students.enroll', $student) }}" class="btn btn-outline-success ms-auto">
                                <i class="bi bi-fingerprint me-2"></i>Enroll Fingerprint
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection