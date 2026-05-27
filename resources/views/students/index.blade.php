@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Student Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Students</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">Manage registered students and fingerprint enrollment</p>
    </div>
    <a href="{{ route('students.create') }}" class="btn btn-primary-ihet">
        <i class="bi bi-person-plus me-2"></i>Add Student
    </a>
</div>

{{-- Filter Bar --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Search by name or reg number..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->course_id }}" {{ request('course_id') == $course->course_id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="fingerprint" class="form-select">
                    <option value="">All Status</option>
                    <option value="enrolled" {{ request('fingerprint') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="not_enrolled" {{ request('fingerprint') == 'not_enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-ihet flex-grow-1">Filter</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people me-2"></i>
            {{ $students->total() }} Students
        </span>
        <div class="d-flex gap-2">
            <span class="badge" style="background:#dcfce7;color:#166534;">
                <i class="bi bi-fingerprint me-1"></i>{{ $enrolledCount }} Enrolled
            </span>
            <span class="badge" style="background:#fee2e2;color:#991b1b;">
                <i class="bi bi-person-x me-1"></i>{{ $notEnrolledCount }} Pending
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Reg. Number</th>
                        <th>Course</th>
                        <th>Gender</th>
                        <th>Fingerprint</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">
                            {{ $students->firstItem() + $index }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;
                                    background:{{ $student->gender === 'Male' ? '#dbeafe' : '#fce7f3' }};
                                    display:flex;align-items:center;justify-content:center;
                                    color:{{ $student->gender === 'Male' ? '#1d4ed8' : '#be185d' }};
                                    font-size:.8rem;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-600" style="font-size:.88rem;">{{ $student->full_name }}</div>
                                    <small class="text-muted">DOB: {{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:5px;font-size:.82rem;">{{ $student->reg_no }}</code></td>
                        <td>{{ $student->course->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $student->gender === 'Male' ? 'badge-admin' : '' }}" style="{{ $student->gender === 'Female' ? 'background:#fce7f3;color:#be185d;' : '' }}">
                                {{ $student->gender }}
                            </span>
                        </td>
                        <td>
                            @if($student->fingerprint)
                                <span class="badge badge-verified"><i class="bi bi-fingerprint me-1"></i>Enrolled</span>
                            @else
                                <span class="badge badge-rejected"><i class="bi bi-exclamation-circle me-1"></i>Not Enrolled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('students.enroll', $student) }}" class="btn btn-sm btn-outline-success" title="Enroll Fingerprint">
                                    <i class="bi bi-fingerprint"></i>
                                </a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}" class="d-inline"
                                      onsubmit="return confirm('Delete {{ $student->full_name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">No students found. <a href="{{ route('students.create') }}">Add the first student</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-transparent">
        {{ $students->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection