@extends('layouts.student')

@section('title', 'Available Assessments - KIT Training Portal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-clipboard-list me-2" style="color: var(--primary-red);"></i>
            Available Assessments
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">Browse and take assessments to test your skills</p>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none;">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 4px solid #28a745; border-radius: 10px; color: #155724; font-weight: 500;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 10px; color: #0c5460; font-weight: 500;">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: none; border-left: 4px solid var(--primary-red); border-radius: 10px; color: #721c24; font-weight: 500;">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 15px 25px; border: none;">
        <h6 class="mb-0 fw-bold"><i class="fas fa-filter me-2"></i>Filter Assessments</h6>
    </div>
    <div class="card-body p-4">
        <form method="GET" action="{{ route('student.assessments.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search assessments..." value="{{ request('search') }}" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Category</label>
                    <select name="category" class="form-select" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px;">
                        <option value="">All Categories</option>
                        <option value="Aptitude" {{ request('category') == 'Aptitude' ? 'selected' : '' }}>Aptitude</option>
                        <option value="Technical" {{ request('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Difficulty Level</label>
                    <select name="difficulty" class="form-select" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px;">
                        <option value="">All Levels</option>
                        <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assessments Grid -->
<div class="row g-4">
    @forelse($assessments as $assessment)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 assessment-card" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
            <div class="card-header text-white" style="background: linear-gradient(135deg, {{ $assessment->category == 'Aptitude' ? '#007bff, #0056b3' : '#28a745, #218838' }}); padding: 20px 25px; border: none;">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mb-0 fw-bold" style="flex: 1;">{{ $assessment->title }}</h5>
                    <span class="badge ms-2" style="background: rgba(255,255,255,0.25); padding: 6px 12px; border-radius: 8px;">{{ $assessment->category }}</span>
                </div>
            </div>
            <div class="card-body p-4">
                @if($assessment->description)
                <p class="text-muted mb-3" style="font-size: 0.95rem;">{{ Str::limit($assessment->description, 120) }}</p>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge-sm me-2" style="width: 35px; height: 35px; border-radius: 8px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock" style="color: white; font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Duration</small>
                                <strong style="color: var(--text-dark); font-size: 0.9rem;">{{ $assessment->duration }} min</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge-sm me-2" style="width: 35px; height: 35px; border-radius: 8px; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-question-circle" style="color: white; font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Questions</small>
                                <strong style="color: var(--text-dark); font-size: 0.9rem;">{{ $assessment->questions->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge-sm me-2" style="width: 35px; height: 35px; border-radius: 8px; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-star" style="color: white; font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Total Marks</small>
                                <strong style="color: var(--text-dark); font-size: 0.9rem;">{{ $assessment->total_marks }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge-sm me-2" style="width: 35px; height: 35px; border-radius: 8px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line" style="color: white; font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Pass Mark</small>
                                <strong style="color: var(--text-dark); font-size: 0.9rem;">{{ $assessment->pass_percentage }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 d-flex flex-wrap gap-2">
                    <span class="badge" style="background: linear-gradient(135deg, {{ $assessment->difficulty_level == 'easy' ? '#28a745, #218838' : ($assessment->difficulty_level == 'hard' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); padding: 6px 12px; border-radius: 8px;">
                        <i class="fas fa-signal me-1"></i> {{ ucfirst($assessment->difficulty_level) }}
                    </span>
                    @if($assessment->allow_multiple_attempts)
                        <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 6px 12px; border-radius: 8px;">
                            <i class="fas fa-redo me-1"></i> Multiple Attempts
                        </span>
                    @endif
                    @if($assessment->show_results_immediately)
                        <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); padding: 6px 12px; border-radius: 8px;">
                            <i class="fas fa-eye me-1"></i> Instant Results
                        </span>
                    @endif
                </div>

                @if($assessment->student_attempt)
                <div class="alert mb-0" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 10px; padding: 12px;">
                    <i class="fas fa-info-circle me-1"></i> 
                    @if($assessment->student_attempt->status == 'completed')
                        <strong>You scored {{ number_format($assessment->student_attempt->percentage, 2) }}%</strong>
                        @if(!$assessment->allow_multiple_attempts)
                            (Completed)
                        @endif
                    @else
                        <strong>In Progress</strong>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-footer bg-transparent p-4" style="border-top: 1px solid #e0e0e0;">
                @if($assessment->student_attempt && $assessment->student_attempt->status == 'completed')
                    @if($assessment->allow_multiple_attempts)
                        <div class="d-flex gap-2">
                            <a href="{{ route('student.assessments.result', $assessment) }}" class="btn flex-fill" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-eye me-1"></i> View Results
                            </a>
                            <a href="{{ route('student.assessments.show', $assessment) }}" class="btn flex-fill" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-redo me-1"></i> Retake
                            </a>
                        </div>
                    @else
                        <a href="{{ route('student.assessments.result', $assessment) }}" class="btn w-100" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-eye me-1"></i> View Results
                        </a>
                    @endif
                @elseif($assessment->student_attempt && $assessment->student_attempt->status == 'started')
                    <a href="{{ route('student.assessments.take', [$assessment, 'attempt' => $assessment->student_attempt->id]) }}" class="btn w-100" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-play me-1"></i> Continue Assessment
                    </a>
                @else
                    <a href="{{ route('student.assessments.show', $assessment) }}" class="btn w-100" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-play-circle me-1"></i> Start Assessment
                    </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm" style="border: none; border-radius: 15px;">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x mb-3" style="color: var(--primary-red); opacity: 0.3;"></i>
                <h4 class="fw-bold" style="color: var(--text-dark);">No Assessments Available</h4>
                <p class="text-muted mb-0">There are no active assessments at the moment. Please check back later.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($assessments->hasPages())
<div class="mt-4">
    {{ $assessments->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --light-red: #EF4444;
    --text-dark: #333333;
}

.assessment-card {
    transition: all 0.3s ease;
}

.assessment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
}

/* Pagination styling */
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
</script>
@endsection
