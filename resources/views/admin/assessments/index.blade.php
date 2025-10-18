@extends('layouts.admin')

@section('title', 'Assessments - KIT Training Portal')
@section('page-title', 'Assessment Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-dark); font-weight: 700;">
        <i class="fas fa-clipboard-list me-2" style="color: var(--primary-red);"></i>
        Assessments
    </h1>
    <a href="{{ route('admin.assessments.create') }}" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 25px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
        <i class="fas fa-plus me-2"></i>Create Assessment
    </a>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 15px 25px; border: none;">
        <h6 class="mb-0" style="font-weight: 700;">
            <i class="fas fa-filter me-2"></i>Filter Assessments
        </h6>
    </div>
    <div class="card-body" style="padding: 20px 25px;">
        <form method="GET" action="{{ route('admin.assessments.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by title" value="{{ request('search') }}" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;">Status</label>
                    <select name="status" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;">Category</label>
                    <select name="category" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        <option value="">All Categories</option>
                        <option value="Aptitude" {{ request('category') == 'Aptitude' ? 'selected' : '' }}>Aptitude</option>
                        <option value="Technical" {{ request('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="w-100">
                        <button type="submit" class="btn me-2" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assessments Table -->
<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-clipboard-list me-2"></i>All Assessments
            @if($assessments->total() > 0)
                <span class="badge ms-2" style="background: rgba(255,255,255,0.25); padding: 8px 15px; font-size: 0.9rem;">{{ $assessments->total() }}</span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        @if($assessments->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Questions</th>
                            <th>Created At</th>
                            <th width="200" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessments as $assessment)
                        <tr data-assessment-id="{{ $assessment->id }}">
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);">{{ $assessment->title }}</div>
                                    @if($assessment->description)
                                        <small class="text-muted">{{ Str::limit($assessment->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    {{ $assessment->category }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $assessment->duration ?? $assessment->total_time ?? 'N/A' }} min
                                </span>
                            </td>
                            <td>
                                @if($assessment->status == 'active')
                                    <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @elseif($assessment->status == 'inactive')
                                    <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-pause-circle me-1"></i>Inactive
                                    </span>
                                @else
                                    <span class="badge" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-pencil-alt me-1"></i>Draft
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($assessment->questions_count > 0)
                                    <span class="badge" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-question-circle me-1"></i>{{ $assessment->questions_count }}
                                    </span>
                                @else
                                    <span class="badge" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>No questions
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $assessment->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-sm" title="View" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-sm" title="Edit" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn btn-sm" title="Questions" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-question-circle"></i>
                                    </a>
                                    <a href="{{ route('admin.assessments.add-question', $assessment) }}" class="btn btn-sm" title="Add Question" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                    <a href="{{ route('admin.results.index', $assessment) }}" class="btn btn-sm" title="Results" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    @if($assessment->status == 'active')
                                    <form action="{{ route('admin.assessments.toggle-status', $assessment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="btn btn-sm" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this assessment?');" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.assessments.toggle-status', $assessment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-sm" title="Activate" onclick="return confirm('Are you sure you want to activate this assessment?');" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.assessments.destroy', $assessment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assessment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" title="Delete" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.assessments.duplicate', $assessment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm" title="Duplicate" style="background: linear-gradient(135deg, #343a40 0%, #23272b 100%); color: white; border: none; padding: 6px 10px; border-radius: 6px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($assessments->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $assessments->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3" style="color: var(--primary-red);"></i>
                <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Assessments Found</h5>
                <p class="text-muted" style="font-size: 1rem;">
                    @if(request()->has('search') || request()->has('status') || request()->has('category'))
                        No assessments match your filter criteria. Try adjusting your filters.
                    @else
                        Get started by creating your first assessment.
                    @endif
                </p>
                <a href="{{ route('admin.assessments.create') }}" class="btn mt-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                    <i class="fas fa-plus me-2"></i>Create Assessment
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --light-red: #EF4444;
    --black: #1A1A1A;
    --dark-gray: #2D2D2D;
    --white: #FFFFFF;
    --light-gray: #F5F5F5;
    --text-dark: #333333;
    --accent-red: #FF0000;
}

.table th {
    border-top: none;
    font-weight: 700;
    color: var(--text-dark);
    background-color: #f8f9fa;
    padding: 15px 12px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(220, 20, 60, 0.05) !important;
}

.btn-group-actions {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.btn-group-actions .btn:hover,
.btn-group-actions button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

a[href*="create"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4) !important;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: none;
    border-left: 4px solid #28a745;
    border-radius: 10px;
    color: #155724;
    font-weight: 500;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: none;
    border-left: 4px solid var(--primary-red);
    border-radius: 10px;
    color: #721c24;
    font-weight: 500;
}

.form-control:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Pagination styling */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: var(--primary-red);
    border-radius: 8px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
    transition: all 0.3s;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: white;
    border-color: var(--primary-red);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    border-color: var(--primary-red);
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        });
    }, 5000);
});
</script>
@endpush
