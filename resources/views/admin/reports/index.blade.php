@extends('layouts.admin')

@section('title', 'Reports & Analytics - KIT Training Portal')
@section('page-title', 'Reports & Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
                <i class="fas fa-chart-line me-2" style="color: var(--primary-red);"></i>
                Reports & Analytics
            </h1>
            <p class="text-muted mb-0" style="font-size: 1rem;">Comprehensive analytics and performance insights</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export', ['type' => 'all']) }}" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
            <a href="{{ route('admin.reports.student-performance') }}" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
                <i class="fas fa-user-graduate me-2"></i>Student Progress
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                <div class="card-body" style="padding: 25px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-users fa-2x" style="color: white;"></i>
                    </div>
                    <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $overallStats['total_students'] }}</h4>
                    <p class="text-muted mb-0" style="font-weight: 500;">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                <div class="card-body" style="padding: 25px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-clipboard-list fa-2x" style="color: white;"></i>
                    </div>
                    <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $overallStats['total_assessments'] }}</h4>
                    <p class="text-muted mb-0" style="font-weight: 500;">Total Assessments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                <div class="card-body" style="padding: 25px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-chart-line fa-2x" style="color: white;"></i>
                    </div>
                    <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ round($overallStats['average_score'], 1) }}%</h4>
                    <p class="text-muted mb-0" style="font-weight: 500;">Average Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                <div class="card-body" style="padding: 25px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fas fa-trophy fa-2x" style="color: white;"></i>
                    </div>
                    <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $overallStats['total_attempts'] }}</h4>
                    <p class="text-muted mb-0" style="font-weight: 500;">Total Attempts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Performance Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                        <i class="fas fa-chart-bar me-2"></i>Assessment Performance Overview
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($assessmentStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Assessment</th>
                                        <th style="width: 100px">Category</th>
                                        <th style="width: 100px">Questions</th>
                                        <th style="width: 100px">Attempts</th>
                                        <th style="width: 120px">Avg Score</th>
                                        <th style="width: 100px">Pass Rate</th>
                                        <th style="width: 100px">Avg Time</th>
                                        <th style="width: 120px" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessmentStats as $assessment)
                                        <tr data-assessment-id="{{ $assessment['id'] }}">
                                            <td>
                                                <div style="font-weight: 600; color: var(--text-dark);">
                                                    {{ $assessment['title'] }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: linear-gradient(135deg, {{ $assessment['category'] === 'Aptitude' ? 'var(--primary-red) 0%, var(--dark-red) 100%' : '#28a745 0%, #218838 100%' }}); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                                    {{ $assessment['category'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                                    {{ $assessment['total_questions'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                                    {{ $assessment['total_attempts'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 8px; border-radius: 10px; background-color: #e9ecef;">
                                                        <div class="progress-bar bg-{{ $assessment['avg_percentage'] >= 70 ? 'success' : ($assessment['avg_percentage'] >= 50 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ min($assessment['avg_percentage'], 100) }}%; border-radius: 10px;"></div>
                                                    </div>
                                                    <span class="fw-bold" style="color: var(--text-dark);">{{ $assessment['avg_percentage'] }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $passGradient = $assessment['pass_rate'] >= 70 
                                                        ? 'linear-gradient(135deg, #28a745 0%, #218838 100%)' 
                                                        : ($assessment['pass_rate'] >= 50 
                                                            ? 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)' 
                                                            : 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)');
                                                @endphp
                                                <span class="badge" style="background: {{ $passGradient }}; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                                    {{ $assessment['pass_rate'] }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ round($assessment['avg_time'] / 60, 1) }}m
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group-actions">
                                                    <a href="{{ route('admin.reports.assessment-details', $assessment['id']) }}" 
                                                       class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.assessments.questions', $assessment['id']) }}" 
                                                       class="btn btn-sm ms-1" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                                        <i class="fas fa-question-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x mb-3" style="color: var(--primary-red);"></i>
                            <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Assessment Data</h5>
                            <p class="text-muted" style="font-size: 1rem;">No assessments have been created or attempted yet.</p>
                            <a href="{{ route('admin.assessments.create') }}" class="btn mt-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                                <i class="fas fa-plus me-2"></i>Create Assessment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 15px 25px; border: none;">
                    <h5 class="mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px 25px;">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.student-performance') }}" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-user-graduate me-2"></i>View Student Progress
                        </a>
                        <a href="{{ route('admin.reports.category-analysis') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-chart-pie me-2"></i>Category Analysis
                        </a>
                        <a href="{{ route('admin.assessments.index') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-clipboard-list me-2"></i>Manage Assessments
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 15px 25px; border: none;">
                    <h5 class="mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-download me-2"></i>Export Options
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px 25px;">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.export', ['type' => 'all']) }}" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-file-csv me-2"></i>Export All Results
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'student']) }}" class="btn btn-lg" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-users me-2"></i>Export Student Data
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'assessment']) }}" class="btn btn-lg" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.3s; text-align: left;">
                            <i class="fas fa-clipboard-list me-2"></i>Export Assessment Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
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

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
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
}

.btn-group-actions a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Action buttons hover effects */
.d-grid .btn:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Header buttons hover effects */
a[href*="export"]:hover,
a[href*="student-performance"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
}

/* Stats cards animation on hover */
.row.g-3 .card:hover {
    transform: translateY(-8px) scale(1.02);
}
</style>

<script>
// Auto-dismiss alerts if any
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
