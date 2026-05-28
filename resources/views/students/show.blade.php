@extends('layouts.app')
@section('title', $student->full_name)
@section('page-title', 'Student Profile')

@section('content')
<div class="mb-4">
    <a href="{{ route('students.index') }}" class="text-decoration-none text-muted" style="font-size:.85rem;">
        <i class="bi bi-arrow-left me-1"></i> Back to Students
    </a>
</div>

<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div style="width:80px;height:80px;border-radius:50%;
                    background:{{ $student->gender === 'Male' ? '#dbeafe' : '#fce7f3' }};
                    display:flex;align-items:center;justify-content:center;
                    margin:0 auto 16px;font-size:1.8rem;font-weight:700;
                    color:{{ $student->gender === 'Male' ? '#1a3a5c' : '#be185d' }};">
                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                </div>

                <h5 class="fw-700 mb-1" style="font-family:'Space Grotesk',sans-serif;">
                    {{ $student->full_name }}
                </h5>
                <p class="text-muted mb-3" style="font-size:.85rem;">{{ $student->reg_no }}</p>

                @if($student->fingerprint)
                    <span class="badge badge-verified mb-3">
                        <i class="bi bi-fingerprint me-1"></i>Fingerprint Enrolled
                    </span>
                @else
                    <span class="badge badge-rejected mb-3">
                        <i class="bi bi-exclamation-circle me-1"></i>Not Enrolled
                    </span>
                @endif

                <hr>

                <div class="text-start">
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Course</span>
                        <span class="fw-600">{{ $student->course->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Gender</span>
                        <span class="fw-600">{{ $student->gender }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="font-size:.83rem;">
                        <span class="text-muted">Date of Birth</span>
                        <span class="fw-600">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('students.edit', $student) }}"
                       class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('students.enroll', $student) }}"
                       class="btn btn-primary-ihet flex-grow-1">
                        <i class="bi bi-fingerprint me-1"></i>Enroll
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance History --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Attendance History</span>
                <div class="d-flex gap-2">
                    <span class="badge badge-verified">
                        {{ $student->attendance->where('status','Verified')->count() }} Verified
                    </span>
                    <span class="badge badge-rejected">
                        {{ $student->attendance->where('status','Rejected')->count() }} Rejected
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Examination</th>
                                <th>Subject</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->attendance->sortByDesc('time') as $i => $att)
                            <tr>
                                <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                                <td style="font-size:.85rem;">{{ $att->exam->name ?? '—' }}</td>
                                <td style="font-size:.83rem;">{{ $att->exam->subject->name ?? '—' }}</td>
                                <td>
                                    <div style="font-size:.85rem;">
                                        {{ \Carbon\Carbon::parse($att->time)->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($att->time)->format('H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge {{ $att->status === 'Verified' ? 'badge-verified' : 'badge-rejected' }}">
                                        <i class="bi bi-{{ $att->status === 'Verified' ? 'check' : 'x' }}-circle me-1"></i>
                                        {{ $att->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-clock" style="font-size:1.8rem;opacity:.4;"></i>
                                    <p class="mt-2 mb-0 small">No attendance records yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
