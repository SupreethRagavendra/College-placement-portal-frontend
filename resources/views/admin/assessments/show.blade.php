@extends('layouts.admin')

@section('title', 'Assessment Details - KIT Training Portal')
@section('page-title', 'Assessment Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-clipboard-list me-2" style="color: var(--primary-red);"></i>
            Assessment Details
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">View complete assessment information and statistics</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('admin.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 4px solid #28a745; border-radius: 10px; color: #155724; font-weight: 500;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Assessment Info -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-info-circle me-2"></i>Assessment Information
                </h5>
            </div>
            <div class="card-body" style="padding: 25px;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong style="color: var(--text-dark);">Title:</strong><br>
                        <span class="text-muted" style="font-size: 1rem;">{{ $assessment->title }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong style="color: var(--text-dark);">Category:</strong><br>
                        <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                            {{ $assessment->category }}
                        </span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong style="color: var(--text-dark);">Description:</strong><br>
                        <span class="text-muted">{{ $assessment->description ?: 'No description provided' }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong style="color: var(--text-dark);">Duration:</strong><br>
                        <span class="text-muted"><i class="fas fa-clock me-1"></i>{{ $assessment->duration ?? $assessment->total_time ?? 'N/A' }} minutes</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong style="color: var(--text-dark);">Total Marks:</strong><br>
                        <span class="text-muted">{{ $assessment->total_marks }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong style="color: var(--text-dark);">Pass Percentage:</strong><br>
                        <span class="text-muted">{{ $assessment->pass_percentage }}%</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong style="color: var(--text-dark);">Difficulty Level:</strong><br>
                        @php
                            $diffGradient = $assessment->difficulty_level == 'easy' 
                                ? 'linear-gradient(135deg, #28a745 0%, #218838 100%)' 
                                : ($assessment->difficulty_level == 'hard' 
                                    ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' 
                                    : 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)');
                        @endphp
                        <span class="badge" style="background: {{ $diffGradient }}; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                            {{ ucfirst($assessment->difficulty_level ?? 'medium') }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong style="color: var(--text-dark);">Status:</strong><br>
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
                    </div>
                    <div class="col-md-12">
                        <strong style="color: var(--text-dark);">Options:</strong><br>
                        @if($assessment->allow_multiple_attempts)
                            <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500; margin-right: 5px;">
                                <i class="fas fa-redo me-1"></i>Multiple Attempts
                            </span>
                        @endif
                        @if($assessment->show_results_immediately)
                            <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500; margin-right: 5px;">
                                <i class="fas fa-bolt me-1"></i>Instant Results
                            </span>
                        @endif
                        @if($assessment->show_correct_answers)
                            <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                <i class="fas fa-check-double me-1"></i>Show Answers
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-chart-bar me-2"></i>Statistics
                </h5>
            </div>
            <div class="card-body" style="padding: 25px;">
                <div class="mb-4">
                    <strong style="color: var(--text-dark);">Total Questions:</strong>
                    <h3 style="color: var(--primary-red); font-weight: 700; margin-top: 5px;">{{ $assessment->questions_count ?? 0 }}</h3>
                </div>
                <div class="mb-4">
                    <strong style="color: var(--text-dark);">Total Attempts:</strong>
                    <h3 style="color: #17a2b8; font-weight: 700; margin-top: 5px;">{{ $assessment->student_assessments_count ?? 0 }}</h3>
                </div>
                @if(($assessment->student_assessments_count ?? 0) > 0)
                <div class="mb-4">
                    <strong style="color: var(--text-dark);">Average Score:</strong>
                    <h3 style="color: #28a745; font-weight: 700; margin-top: 5px;">{{ number_format($assessment->average_score ?? 0, 2) }}%</h3>
                </div>
                <div class="mb-0">
                    <strong style="color: var(--text-dark);">Pass Rate:</strong>
                    <h3 style="color: #ffc107; font-weight: 700; margin-top: 5px;">{{ number_format($assessment->pass_rate ?? 0, 2) }}%</h3>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, #343a40 0%, #23272b 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-tasks me-2"></i>Quick Actions
        </h5>
    </div>
    <div class="card-body" style="padding: 25px;">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 20px; border-radius: 12px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-question-circle fa-2x mb-2"></i>
                    Manage Questions
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.assessments.add-question', $assessment) }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 20px; border-radius: 12px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                    Add Question
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.results.index', $assessment) }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 20px; border-radius: 12px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    View Results
                </a>
            </div>
            <div class="col-md-3">
                <form action="{{ route('admin.assessments.duplicate', $assessment) }}" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 20px; border-radius: 12px; font-weight: 600; transition: all 0.3s; display: flex; flex-direction: column; align-items: center;">
                        <i class="fas fa-copy fa-2x mb-2"></i>
                        Duplicate
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attempts -->
@if(isset($assessment->studentAssessments) && $assessment->studentAssessments->count() > 0)
<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-history me-2"></i>Recent Attempts (Last 10)
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Time Taken</th>
                        <th>Submitted At</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessment->studentAssessments as $attempt)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">{{ $attempt->student->name ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $attempt->obtained_marks }}/{{ $attempt->total_marks }}</td>
                        <td><strong>{{ number_format($attempt->percentage, 2) }}%</strong></td>
                        <td>
                            @if($attempt->pass_status == 'pass')
                                <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    <i class="fas fa-check me-1"></i>Pass
                                </span>
                            @elseif($attempt->pass_status == 'fail')
                                <span class="badge" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    <i class="fas fa-times me-1"></i>Fail
                                </span>
                            @else
                                <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ $attempt->formatted_time_taken ?? 'N/A' }}</span></td>
                        <td><span class="text-muted">{{ $attempt->submit_time ? $attempt->submit_time->format('M d, Y H:i') : 'Not submitted' }}</span></td>
                        <td class="text-end">
                            @if($attempt->status == 'completed')
                            <a href="{{ route('admin.results.show', $attempt) }}" class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
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

.card {
    transition: all 0.3s ease;
}

/* Only apply hover effect to statistics card */
.col-md-4 .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
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

.btn-lg:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

a[href*="edit"]:hover,
a[href*="assessments"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
}
</style>

<script>
// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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
