@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    {{-- Stat Cards --}}
    <div class="col-6 col-xl-3">
        <div class="stat-card card" style="border-left: 4px solid #1a3a5c;">
            <div class="stat-icon" style="background:#eff6ff;">
                <i class="bi bi-people" style="color:#1a3a5c;"></i>
            </div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-label">Registered Students</div>
            <div class="stat-change text-success">
                <i class="bi bi-arrow-up-short"></i> {{ $enrolledStudents }} with fingerprints
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card card" style="border-left: 4px solid #e8a020;">
            <div class="stat-icon" style="background:#fffbeb;">
                <i class="bi bi-journal-text" style="color:#e8a020;"></i>
            </div>
            <div class="stat-value">{{ $totalExams }}</div>
            <div class="stat-label">Scheduled Exams</div>
            <div class="stat-change text-warning">
                <i class="bi bi-clock"></i> {{ $todayExams }} today
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card card" style="border-left: 4px solid #16a34a;">
            <div class="stat-icon" style="background:#f0fdf4;">
                <i class="bi bi-fingerprint" style="color:#16a34a;"></i>
            </div>
            <div class="stat-value">{{ $todayAttendance }}</div>
            <div class="stat-label">Verified Today</div>
            <div class="stat-change text-success">
                <i class="bi bi-check-circle"></i> attendance records
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card card" style="border-left: 4px solid #dc2626;">
            <div class="stat-icon" style="background:#fff1f2;">
                <i class="bi bi-x-circle" style="color:#dc2626;"></i>
            </div>
            <div class="stat-value">{{ $rejectedToday }}</div>
            <div class="stat-label">Rejected Today</div>
            <div class="stat-change text-danger">
                <i class="bi bi-exclamation-triangle"></i> failed verifications
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Today's Exams --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2 text-primary"></i>Today's Examinations</span>
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($todayExamsList->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
                        <p class="mt-2 mb-0">No examinations scheduled for today</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th>Time</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayExamsList as $exam)
                            <tr>
                                <td>
                                    <div class="fw-600">{{ $exam->name }}</div>
                                    <small class="text-muted">{{ $exam->subject->name ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($exam->exam_time)->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            @php
                                                $pct = $exam->enrolled_count > 0
                                                    ? round(($exam->attendance_count / $exam->enrolled_count) * 100)
                                                    : 0;
                                            @endphp
                                            <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $exam->attendance_count }}/{{ $exam->enrolled_count }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('attendance.verify') }}?exam_id={{ $exam->exam_id }}"
                                       class="btn btn-sm btn-primary-ihet">
                                        <i class="bi bi-fingerprint"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Verifications --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-activity me-2 text-success"></i>Recent Verifications
            </div>
            <div class="card-body p-0">
                @if($recentAttendance->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clock-history" style="font-size:2rem;"></i>
                        <p class="mt-2 mb-0 small">No recent activity</p>
                    </div>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($recentAttendance as $att)
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm" style="width:32px;height:32px;border-radius:50%;
                                background:{{ $att->status === 'Verified' ? '#dcfce7' : '#fee2e2' }};
                                display:flex;align-items:center;justify-content:center;font-size:.8rem;
                                color:{{ $att->status === 'Verified' ? '#166534' : '#991b1b' }};flex-shrink:0;">
                                <i class="bi bi-{{ $att->status === 'Verified' ? 'check' : 'x' }}"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-600" style="font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $att->student->full_name ?? 'Unknown' }}
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ \Carbon\Carbon::parse($att->time)->format('H:i:s') }}
                                </div>
                            </div>
                            <span class="badge {{ $att->status === 'Verified' ? 'badge-verified' : 'badge-rejected' }}" style="font-size:.7rem;">
                                {{ $att->status }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-0">
    {{-- Quick Actions --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('students.create') }}" class="text-decoration-none">
                            <div class="p-3 rounded-3 text-center" style="background:#eff6ff;border:1.5px dashed #bfdbfe;transition:.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                <i class="bi bi-person-plus" style="font-size:1.8rem;color:#1a3a5c;"></i>
                                <div class="mt-2 fw-600" style="font-size:.82rem;color:#1e293b;">Enroll Student</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('exams.create') }}" class="text-decoration-none">
                            <div class="p-3 rounded-3 text-center" style="background:#fffbeb;border:1.5px dashed #fde68a;transition:.2s;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                                <i class="bi bi-plus-circle" style="font-size:1.8rem;color:#e8a020;"></i>
                                <div class="mt-2 fw-600" style="font-size:.82rem;color:#1e293b;">Schedule Exam</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('attendance.verify') }}" class="text-decoration-none">
                            <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1.5px dashed #bbf7d0;transition:.2s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                <i class="bi bi-fingerprint" style="font-size:1.8rem;color:#16a34a;"></i>
                                <div class="mt-2 fw-600" style="font-size:.82rem;color:#1e293b;">Verify Attendance</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('reports.attendance') }}" class="text-decoration-none">
                            <div class="p-3 rounded-3 text-center" style="background:#fdf4ff;border:1.5px dashed #e9d5ff;transition:.2s;" onmouseover="this.style.background='#f3e8ff'" onmouseout="this.style.background='#fdf4ff'">
                                <i class="bi bi-file-earmark-pdf" style="font-size:1.8rem;color:#7c3aed;"></i>
                                <div class="mt-2 fw-600" style="font-size:.82rem;color:#1e293b;">Export Report</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection