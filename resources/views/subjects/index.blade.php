@extends('layouts.app')
@section('title', 'Subjects')
@section('page-title', 'Subject Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Subjects</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">Manage examination subjects</p>
    </div>
    <button class="btn btn-primary-ihet" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i>Add Subject
    </button>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-book me-2"></i>{{ $subjects->count() }} Subjects
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject Name</th>
                        <th>Linked Exams</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $i => $subject)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:8px;
                                    background:#eff6ff;display:flex;align-items:center;
                                    justify-content:center;color:#1a3a5c;font-size:.85rem;
                                    font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($subject->name, 0, 2)) }}
                                </div>
                                <span class="fw-600" style="font-size:.88rem;">{{ $subject->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-admin">
                                <i class="bi bi-journal-text me-1"></i>
                                {{ $subject->exams_count }} exam(s)
                            </span>
                        </td>
                        <td style="font-size:.83rem;">{{ $subject->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="editSubject({{ $subject->subject_id }}, '{{ addslashes($subject->name) }}')"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete {{ $subject->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            {{ $subject->exams_count > 0 ? 'disabled title=Cannot delete subject linked to exams' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-book" style="font-size:2.5rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">No subjects yet. <a href="#" data-bs-toggle="modal" data-bs-target="#createModal">Add the first subject</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" style="font-family:'Space Grotesk',sans-serif;">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Add New Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('subjects.store') }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    @if($errors->any())
                        <div class="alert alert-danger" style="font-size:.83rem;">
                            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}"
                               placeholder="e.g. Database Management Systems" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-ihet">
                        <i class="bi bi-check2 me-1"></i>Add Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" style="font-family:'Space Grotesk',sans-serif;">
                    <i class="bi bi-pencil me-2 text-warning"></i>Edit Subject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-ihet">
                        <i class="bi bi-check2 me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editSubject(id, name) {
    document.getElementById('editName').value = name;
    document.getElementById('editForm').action = '/subjects/' + id;
}
</script>
@endpush
