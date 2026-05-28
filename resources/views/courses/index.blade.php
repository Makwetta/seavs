@extends('layouts.app')
@section('title', 'Courses')
@section('page-title', 'Course Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-700" style="font-family:'Space Grotesk',sans-serif;">Courses</h5>
        <p class="text-muted mb-0" style="font-size:.85rem;">Manage academic courses for student enrollment</p>
    </div>
    <button class="btn btn-primary-ihet" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-2"></i>Add Course
    </button>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-mortarboard me-2"></i>{{ $courses->count() }} Courses
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Enrolled Students</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $i => $course)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:8px;
                                    background:#fef9c3;display:flex;align-items:center;
                                    justify-content:center;color:#854d0e;font-size:.85rem;
                                    font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($course->name, 0, 2)) }}
                                </div>
                                <span class="fw-600" style="font-size:.88rem;">{{ $course->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:#f0fdf4;color:#166534;">
                                <i class="bi bi-people me-1"></i>
                                {{ $course->students_count }} student(s)
                            </span>
                        </td>
                        <td style="font-size:.83rem;">{{ $course->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="editCourse({{ $course->course_id }}, '{{ addslashes($course->name) }}')"
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('courses.destroy', $course) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete {{ $course->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            {{ $course->students_count > 0 ? 'disabled title=Cannot delete course with enrolled students' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard" style="font-size:2.5rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">No courses yet. <a href="#" data-bs-toggle="modal" data-bs-target="#createModal">Add the first course</a></p>
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
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Add New Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf
                <div class="modal-body px-4 py-3">
                    @if($errors->any())
                        <div class="alert alert-danger" style="font-size:.83rem;">
                            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Course Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}"
                               placeholder="e.g. Diploma in Information Technology" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-ihet">
                        <i class="bi bi-check2 me-1"></i>Add Course
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
                    <i class="bi bi-pencil me-2 text-warning"></i>Edit Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label">Course Name <span class="text-danger">*</span></label>
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
function editCourse(id, name) {
    document.getElementById('editName').value = name;
    document.getElementById('editForm').action = '/courses/' + id;
}
</script>
@endpush
