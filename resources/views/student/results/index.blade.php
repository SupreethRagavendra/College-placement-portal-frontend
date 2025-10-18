@extends('layouts.student')

@section('title', 'My Results - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.results-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
    position: relative;
    overflow: hidden;
}

.results-header .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.stat-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.results-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
}

.results-card .card-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: white;
    border: none;
    padding: 20px 25px;
    font-weight: 700;
}

.table thead th {
    background-color: #f8f9fa;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-weight: 700;
    color: var(--text-dark);
    padding: 18px 20px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.table tbody tr:hover {
    background-color: rgba(220, 20, 60, 0.03) !important;
}

.score-badge {
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-pass {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    color: white;
}

.status-fail {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.progress {
    border-radius: 8px;
    overflow: hidden;
}

.progress-bar {
    font-weight: 700;
}

.btn-action {
    border-radius: 8px;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="results-header">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1 class="h2 mb-2 fw-bold">
                <i class="fas fa-chart-bar me-2"></i>My Assessment Results
            </h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">View your performance across all assessments and track your progress</p>
        </div>
        <div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    <i class="fas fa-chart-line hero-icon"></i>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius: 12px; border-left: 4px solid #28a745;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Overall Statistics -->
@if($studentAssessments->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-body text-center">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <i class="fas fa-clipboard-list text-white"></i>
                </div>
                <h3 class="mb-1 fw-bold" style="color: var(--text-dark);">{{ $studentAssessments->total() }}</h3>
                <p class="text-muted mb-0 fw-semibold">Total Assessments</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-body text-center">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%);">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <h3 class="mb-1 fw-bold" style="color: var(--text-dark);">{{ $studentAssessments->where('pass_status', 'pass')->count() }}</h3>
                <p class="text-muted mb-0 fw-semibold">Passed</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-body text-center">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <i class="fas fa-times-circle text-white"></i>
                </div>
                <h3 class="mb-1 fw-bold" style="color: var(--text-dark);">{{ $studentAssessments->where('pass_status', 'fail')->count() }}</h3>
                <p class="text-muted mb-0 fw-semibold">Failed</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="card-body text-center">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);">
                    <i class="fas fa-percentage text-white"></i>
                </div>
                <h3 class="mb-1 fw-bold" style="color: var(--text-dark);">{{ number_format($studentAssessments->avg('percentage'), 2) }}%</h3>
                <p class="text-muted mb-0 fw-semibold">Average Score</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Results Table -->
<div class="card results-card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Assessment History
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>
                        <i class="fas fa-clipboard-list me-2" style="color: var(--primary-red);"></i>Assessment
                    </th>
                    <th>
                        <i class="fas fa-tag me-2" style="color: var(--primary-red);"></i>Category
                    </th>
                    <th>
                        <i class="fas fa-trophy me-2" style="color: var(--primary-red);"></i>Score
                    </th>
                    <th>
                        <i class="fas fa-chart-bar me-2" style="color: var(--primary-red);"></i>Percentage
                    </th>
                    <th>
                        <i class="fas fa-flag me-2" style="color: var(--primary-red);"></i>Status
                    </th>
                    <th>
                        <i class="fas fa-clock me-2" style="color: var(--primary-red);"></i>Time Taken
                    </th>
                    <th>
                        <i class="fas fa-calendar me-2" style="color: var(--primary-red);"></i>Submitted
                    </th>
                    <th class="text-center">
                        <i class="fas fa-cog me-2" style="color: var(--primary-red);"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentAssessments as $result)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-alt text-white" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold" style="color: var(--text-dark); font-size: 1rem;">{{ $result->assessment->title }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-question-circle me-1"></i>{{ $result->total_questions ?? 0 }} questions
                                </small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge" style="background: linear-gradient(135deg, {{ $result->assessment->category === 'Aptitude' ? '#007bff, #0056b3' : '#28a745, #218838' }}); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-tag me-1"></i>{{ $result->assessment->category }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">
                            {{ $result->obtained_marks }}/{{ $result->total_marks }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="progress" style="height: 28px; width: 120px;">
                            <div class="progress-bar" 
                                 style="background: linear-gradient(135deg, {{ $result->percentage >= 50 ? '#28a745, #218838' : '#dc3545, #c82333' }});" 
                                 role="progressbar" 
                                 style="width: {{ $result->percentage }}%"
                                 aria-valuenow="{{ $result->percentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                {{ number_format($result->percentage, 1) }}%
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($result->pass_status == 'pass')
                            <span class="badge status-pass" style="padding: 8px 14px; border-radius: 8px;">
                                <i class="fas fa-check-circle me-1"></i>Pass
                            </span>
                        @elseif($result->pass_status == 'fail')
                            <span class="badge status-fail" style="padding: 8px 14px; border-radius: 8px;">
                                <i class="fas fa-times-circle me-1"></i>Fail
                            </span>
                        @else
                            <span class="badge bg-secondary" style="padding: 8px 14px; border-radius: 8px;">
                                <i class="fas fa-info-circle me-1"></i>{{ ucfirst($result->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div style="color: #6c757d; font-weight: 500;">
                            <i class="far fa-clock me-1"></i>{{ $result->formatted_time_taken }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($result->submit_time)
                            <div>
                                <div class="fw-bold" style="color: var(--text-dark);">{{ $result->submit_time->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $result->submit_time->format('H:i A') }}</small>
                            </div>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Not submitted
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($result->status == 'completed')
                            <a href="{{ route('student.results.show', $result) }}" class="btn btn-sm btn-action" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none;">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                        @else
                            <span class="text-muted small">
                                <i class="fas fa-spinner me-1"></i>{{ ucfirst($result->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="padding: 40px;">
                            <i class="fas fa-inbox fa-4x mb-3" style="color: var(--primary-red); opacity: 0.3;"></i>
                            <h5 class="fw-bold" style="color: var(--text-dark);">No Results Yet</h5>
                            <p class="text-muted mb-4">You haven't completed any assessments yet. Start your learning journey now!</p>
                            <a href="{{ route('student.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
                                <i class="fas fa-play-circle me-2"></i>Browse Assessments
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($studentAssessments->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $studentAssessments->links() }}
    </div>
    @endif
</div>

@endsection
