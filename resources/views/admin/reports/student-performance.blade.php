@extends('layouts.admin')

@section('title', 'Student Progress - KIT Training Portal')
@section('page-title', 'Student Performance Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
                <i class="fas fa-user-graduate me-2" style="color: var(--primary-red);"></i>
                Student Progress
            </h1>
            <p class="text-muted mb-0" style="font-size: 1rem;">Track individual student performance and progress</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export', ['type' => 'student']) }}" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 15px 25px; border: none;">
            <h6 class="mb-0" style="font-weight: 700;">
                <i class="fas fa-filter me-2"></i>Filter Students
            </h6>
        </div>
        <div class="card-body" style="padding: 20px 25px;">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;">Search Students</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name or email..." style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                </div>
                <div class="col-md-3">
                    <label for="min_attempts" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.9rem;">Minimum Attempts</label>
                    <input type="number" class="form-control" id="min_attempts" name="min_attempts" 
                           value="{{ request('min_attempts') }}" min="1" placeholder="e.g., 3" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn me-2" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.student-performance') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Performance Table -->
    <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
            <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                <i class="fas fa-user-graduate me-2"></i>Student Performance Overview
            </h5>
            <span class="badge" style="background: rgba(255,255,255,0.25); padding: 8px 15px; font-size: 0.9rem; font-weight: 600;">{{ $studentPerformance->total() }} Students</span>
        </div>
        <div class="card-body p-0">
            @if($studentPerformance->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px">#</th>
                                <th>Student</th>
                                <th style="width: 120px">Total Attempts</th>
                                <th style="width: 120px">Average Score</th>
                                <th style="width: 120px">Best Score</th>
                                <th style="width: 120px">Total Time</th>
                                <th style="width: 100px">Performance</th>
                                <th style="width: 150px" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentPerformance as $index => $performance)
                                @php
                                    $student = $performance->student;
                                    $avgPercentage = round($performance->avg_percentage, 1);
                                    $bestPercentage = round($performance->best_percentage, 1);
                                    $totalTimeHours = round($performance->total_time / 3600, 1);
                                    
                                    // Performance grade
                                    $grade = 'F';
                                    $gradeClass = 'danger';
                                    $gradeGradient = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                                    if ($avgPercentage >= 90) {
                                        $grade = 'A+';
                                        $gradeClass = 'success';
                                        $gradeGradient = 'linear-gradient(135deg, #28a745 0%, #218838 100%)';
                                    } elseif ($avgPercentage >= 80) {
                                        $grade = 'A';
                                        $gradeClass = 'success';
                                        $gradeGradient = 'linear-gradient(135deg, #28a745 0%, #218838 100%)';
                                    } elseif ($avgPercentage >= 70) {
                                        $grade = 'B';
                                        $gradeClass = 'info';
                                        $gradeGradient = 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)';
                                    } elseif ($avgPercentage >= 60) {
                                        $grade = 'C';
                                        $gradeClass = 'warning';
                                        $gradeGradient = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                                    } elseif ($avgPercentage >= 50) {
                                        $grade = 'D';
                                        $gradeClass = 'warning';
                                        $gradeGradient = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
                                    }
                                @endphp
                                <tr data-student-id="{{ $student->id }}">
                                    <td><strong>{{ $studentPerformance->firstItem() + $index }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: var(--text-dark);">{{ $student->name }}</div>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: {{ $gradeGradient }}; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500; font-size: 0.85rem;">
                                            {{ $performance->total_attempts }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px; border-radius: 10px; background-color: #e9ecef;">
                                                <div class="progress-bar bg-{{ $gradeClass }}" 
                                                     style="width: {{ min($avgPercentage, 100) }}%; border-radius: 10px;"></div>
                                            </div>
                                            <span class="fw-bold" style="color: var(--text-dark);">{{ $avgPercentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                            {{ $bestPercentage }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $totalTimeHours }}h
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: {{ $gradeGradient }}; color: white; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                                            {{ $grade }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group-actions">
                                            <a href="{{ route('admin.reports.student-details', $student->id) }}" 
                                               class="btn btn-sm" 
                                               title="View Detailed Performance"
                                               style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                                <i class="fas fa-chart-line me-1"></i>Details
                                            </a>
                                            <button class="btn btn-sm" 
                                                    onclick="showQuickStats({{ $student->id }}, '{{ addslashes($student->name) }}', '{{ $student->register_number ?? '' }}')"
                                                    style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($studentPerformance->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-center">
                            {{ $studentPerformance->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x mb-3" style="color: var(--primary-red);"></i>
                    <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Student Performance Data</h5>
                    <p class="text-muted" style="font-size: 1rem;">No students have completed assessments yet.</p>
                    <a href="{{ route('admin.assessments.index') }}" class="btn mt-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                        <i class="fas fa-clipboard-list me-2"></i>Manage Assessments
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Statistics -->
    @if($studentPerformance->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                    <div class="card-body" style="padding: 25px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-users fa-2x" style="color: white;"></i>
                        </div>
                        <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $studentPerformance->total() }}</h4>
                        <p class="text-muted mb-0" style="font-weight: 500;">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                    <div class="card-body" style="padding: 25px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-chart-line fa-2x" style="color: white;"></i>
                        </div>
                        <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ round($studentPerformance->avg('avg_percentage'), 1) }}%</h4>
                        <p class="text-muted mb-0" style="font-weight: 500;">Average Performance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                    <div class="card-body" style="padding: 25px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-trophy fa-2x" style="color: white;"></i>
                        </div>
                        <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ round($studentPerformance->max('best_percentage'), 1) }}%</h4>
                        <p class="text-muted mb-0" style="font-weight: 500;">Best Performance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
                    <div class="card-body" style="padding: 25px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-clock fa-2x" style="color: white;"></i>
                        </div>
                        <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ round($studentPerformance->sum('total_time') / 3600, 1) }}h</h4>
                        <p class="text-muted mb-0" style="font-weight: 500;">Total Study Time</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Stats Modal -->
<div class="modal fade" id="quickStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); border: none; padding: 20px 25px;">
                <h5 class="modal-title" style="font-weight: 700;">
                    <i class="fas fa-chart-bar me-2"></i>Quick Statistics
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 25px;">
                <div id="quickStatsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px;">
                <a href="#" id="viewFullDetailsBtn" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-chart-line me-2"></i>View Full Details
                </a>
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">Close</button>
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

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 16px;
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

.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.btn-group-actions {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
}

.btn-group-actions .btn:hover,
.btn-group-actions button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

a[href*="export"]:hover,
a[href*="reports"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
}

.form-control:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
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
function showQuickStats(studentId, studentName, studentRegisterNumber) {
    // Show quick statistics in modal
    document.getElementById('quickStatsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border" style="color: var(--primary-red);" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading statistics for ${studentName}...</p>
        </div>
    `;
    
    // Set the link for full details
    document.getElementById('viewFullDetailsBtn').href = `/admin/reports/students/${studentId}`;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('quickStatsModal')).show();
    
    // In a real implementation, you would make an AJAX call here to get quick stats
    setTimeout(() => {
        document.getElementById('quickStatsContent').innerHTML = `
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3" style="width: 50px; height: 50px; font-size: 20px;">
                            ${studentName.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h5 class="mb-0" style="color: var(--text-dark); font-weight: 700;">${studentName}</h5>
                            <p class="text-muted mb-0">Register No: ${studentRegisterNumber || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px;">
                        <div class="card-body">
                            <h6 class="card-title" style="color: var(--text-dark); font-weight: 700;"><i class="fas fa-clock me-2" style="color: var(--primary-red);"></i>Recent Activity</h6>
                            <p class="card-text mb-1"><strong>Last assessment:</strong> 2 days ago</p>
                            <p class="card-text mb-0"><strong>Active since:</strong> 30 days ago</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px;">
                        <div class="card-body">
                            <h6 class="card-title" style="color: var(--text-dark); font-weight: 700;"><i class="fas fa-chart-line me-2" style="color: var(--primary-red);"></i>Performance Summary</h6>
                            <p class="card-text mb-1"><strong>Strengths:</strong> Technical Assessments</p>
                            <p class="card-text mb-0"><strong>Needs Improvement:</strong> Aptitude Tests</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 10px; color: #0c5460;">
                        <i class="fas fa-info-circle me-2"></i>
                        Click "View Full Details" for complete performance analytics, charts, and assessment history.
                    </div>
                </div>
            </div>
        `;
    }, 500);
}

// Auto-dismiss alerts
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
