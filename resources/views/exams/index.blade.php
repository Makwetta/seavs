@extends('layouts.app')
@section('title', 'Exam Schedule')
@section('page-title', 'Examination Schedule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Examinations</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">Schedule and manage examination sessions</p>
    </div>
    <a href="{{ route('exams.create') }}" class="btn btn-primary-ihet">
        <i class="bi bi-plus-circle me-2"></i>Schedule Exam
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Search exams..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-select" value="{{ request('date') }}" title="Filter by date">
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary-ihet">Filter</button>
                <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-text me-2"></i> {{ $exams->total() }} Examinations
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Examination</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $i => $exam)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $exams->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-600" style="font-size:.88rem;">{{ $exam->name }}</div>
                        </td>
                        <td style="font-size:.85rem;">{{ $exam->subject->name ?? '—' }}</td>
                        <td>
                            @php $date = \Carbon\Carbon::parse($exam->exam_date); @endphp
                            <div style="font-size:.85rem;">{{ $date->format('d M Y') }}</div>
                            <small class="text-muted">{{ $date->format('l') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($exam->exam_time)->format('H:i') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-verified">
                                <i class="bi bi-people me-1"></i>
                                {{ $exam->attendance_count ?? 0 }} verified
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('attendance.verify') }}?exam_id={{ $exam->exam_id }}"
                                   class="btn btn-sm btn-outline-success" title="Start Verification">
                                    <i class="bi bi-fingerprint"></i>
                                </a>
                                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('exams.destroy', $exam) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this exam?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x" style="font-size:2.5rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">No exams scheduled. <a href="{{ route('exams.create') }}">Schedule one</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($exams->hasPages())
    <div class="card-footer bg-transparent">{{ $exams->withQueryString()->links() }}</div>
    @endif
</div>
@endsection