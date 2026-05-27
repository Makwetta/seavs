@extends('layouts.app')
@section('title', 'Attendance Records')
@section('page-title', 'Attendance Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Attendance Records</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">All fingerprint verification records</p>
    </div>
    <a href="{{ route('attendance.verify') }}" class="btn btn-primary-ihet">
        <i class="bi bi-fingerprint me-2"></i>Live Verification
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Student name or reg number..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="exam_id" class="form-select">
                    <option value="">All Exams</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->exam_id }}" {{ request('exam_id') == $exam->exam_id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-select" value="{{ request('date') }}">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary-ihet flex-grow-1">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>
            <i class="bi bi-list-check me-2"></i>
            {{ $records->total() }} Records
        </span>
        <div class="d-flex gap-2">
            <span class="badge badge-verified">
                <i class="bi bi-check-circle me-1"></i>{{ $verifiedCount }} Verified
            </span>
            <span class="badge badge-rejected">
                <i class="bi bi-x-circle me-1"></i>{{ $rejectedCount }} Rejected
            </span>
            <a href="{{ route('reports.attendance') }}?{{ http_build_query(request()->all()) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download me-1"></i>Export
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Examination</th>
                        <th>Supervisor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $i => $record)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $records->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:50%;
                                    background:{{ $record->status === 'Verified' ? '#dcfce7' : '#fee2e2' }};
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:.75rem;font-weight:700;
                                    color:{{ $record->status === 'Verified' ? '#166534' : '#991b1b' }};
                                    flex-shrink:0;">
                                    {{ strtoupper(substr($record->student->full_name ?? 'UN', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-600" style="font-size:.87rem;">{{ $record->student->full_name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $record->student->reg_no ?? '—' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-600" style="font-size:.87rem;">{{ $record->exam->name ?? '—' }}</div>
                            <small class="text-muted">{{ $record->exam->subject->name ?? '' }}</small>
                        </td>
                        <td style="font-size:.85rem;">{{ $record->user->name ?? '—' }}</td>
                        <td>
                            <div style="font-size:.85rem;">{{ \Carbon\Carbon::parse($record->time)->format('d M Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($record->time)->format('H:i:s') }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $record->status === 'Verified' ? 'badge-verified' : 'badge-rejected' }}">
                                <i class="bi bi-{{ $record->status === 'Verified' ? 'check' : 'x' }}-circle me-1"></i>
                                {{ $record->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-fingerprint" style="font-size:2.5rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">No attendance records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())
    <div class="card-footer bg-transparent">
        {{ $records->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection