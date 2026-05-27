@extends('layouts.app')
@section('title', 'Attendance Reports')
@section('page-title', 'Attendance Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Attendance Reports</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">Generate and export attendance data</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.pdf', request()->all()) }}" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
        </a>
        <a href="{{ route('reports.export.excel', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-funnel me-2"></i>Filter Report</div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Examination</label>
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
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary-ihet d-block w-100">Go</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card card" style="border-left:4px solid #1a3a5c;">
            <div class="stat-icon" style="background:#eff6ff;"><i class="bi bi-list-check" style="color:#1a3a5c;"></i></div>
            <div class="stat-value">{{ $summary['total'] }}</div>
            <div class="stat-label">Total Records</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card card" style="border-left:4px solid #16a34a;">
            <div class="stat-icon" style="background:#f0fdf4;"><i class="bi bi-check-circle" style="color:#16a34a;"></i></div>
            <div class="stat-value text-success">{{ $summary['verified'] }}</div>
            <div class="stat-label">Verified</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card card" style="border-left:4px solid #dc2626;">
            <div class="stat-icon" style="background:#fff1f2;"><i class="bi bi-x-circle" style="color:#dc2626;"></i></div>
            <div class="stat-value text-danger">{{ $summary['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card card" style="border-left:4px solid #e8a020;">
            <div class="stat-icon" style="background:#fffbeb;"><i class="bi bi-percent" style="color:#e8a020;"></i></div>
            <div class="stat-value" style="color:#e8a020;">{{ $summary['total'] > 0 ? round(($summary['verified']/$summary['total'])*100) : 0 }}%</div>
            <div class="stat-label">Success Rate</div>
        </div>
    </div>
</div>

{{-- Report Table --}}
<div class="card">
    <div class="card-header">
        <i class="bi bi-table me-2"></i>Detailed Attendance Report
        <span class="text-muted ms-2" style="font-size:.82rem;">{{ $records->total() }} records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="reportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Reg. Number</th>
                        <th>Course</th>
                        <th>Examination</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Supervisor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $i => $r)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $records->firstItem() + $i }}</td>
                        <td class="fw-600" style="font-size:.87rem;">{{ $r->student->full_name ?? '—' }}</td>
                        <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:.78rem;">{{ $r->student->reg_no ?? '—' }}</code></td>
                        <td style="font-size:.83rem;">{{ $r->student->course->name ?? '—' }}</td>
                        <td style="font-size:.85rem;">{{ $r->exam->name ?? '—' }}</td>
                        <td style="font-size:.83rem;">{{ $r->exam->subject->name ?? '—' }}</td>
                        <td style="font-size:.83rem;">{{ \Carbon\Carbon::parse($r->time)->format('d M Y') }}</td>
                        <td style="font-size:.83rem;">{{ \Carbon\Carbon::parse($r->time)->format('H:i:s') }}</td>
                        <td style="font-size:.83rem;">{{ $r->user->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $r->status === 'Verified' ? 'badge-verified' : 'badge-rejected' }}">
                                {{ $r->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x" style="font-size:2.5rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">No records found for the selected filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())
    <div class="card-footer bg-transparent">{{ $records->withQueryString()->links() }}</div>
    @endif
</div>
@endsection