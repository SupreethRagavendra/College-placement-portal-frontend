@extends('layouts.admin')

@section('title', 'Assessment Results - KIT Training Portal')
@section('page-title', 'Assessment Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-chart-bar me-2" style="color: var(--primary-red);"></i>
            Assessment Results
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">{{ $assessment->title }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.results.export', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
            <i class="fas fa-file-excel me-2"></i>Export to CSV
        </a>
        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
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

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-users fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $studentAssessments->total() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Total Attempts</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-check-circle fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $studentAssessments->where('pass_status', 'pass')->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Passed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-times-circle fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $studentAssessments->where('pass_status', 'fail')->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Failed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-percentage fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $studentAssessments->count() > 0 ? number_format($studentAssessments->avg('percentage'), 2) : 0 }}%</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Average Score</p>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-chart-bar me-2"></i>Student Results
            @if($studentAssessments->total() > 0)
                <span class="badge ms-2" style="background: rgba(255,255,255,0.25); padding: 8px 15px; font-size: 0.9rem;">{{ $studentAssessments->total() }}</span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        @if($studentAssessments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Time Taken</th>
                            <th>Submitted At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentAssessments as $result)
                        <tr data-result-id="{{ $result->id }}">
                            <td>
                                <div style="font-weight: 600; color: var(--text-dark);">{{ $result->student->name ?? 'Unknown' }}</div>
                            </td>
                            <td><span class="text-muted">{{ $result->student->email ?? 'N/A' }}</span></td>
                            <td>
                                <strong style="color: var(--text-dark);">{{ $result->obtained_marks }}/{{ $result->total_marks }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="height: 10px; width: 80px; border-radius: 10px; background-color: #e9ecef;">
                                        <div class="progress-bar" 
                                             style="width: {{ min($result->percentage, 100) }}%; border-radius: 10px; background: {{ $result->percentage >= 70 ? 'linear-gradient(135deg, #28a745 0%, #218838 100%)' : ($result->percentage >= 50 ? 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)' : 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)') }};">
                                        </div>
                                    </div>
                                    <span class="fw-bold" style="color: var(--text-dark);">{{ number_format($result->percentage, 2) }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($result->pass_status == 'pass')
                                    <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-check me-1"></i>Pass
                                    </span>
                                @elseif($result->pass_status == 'fail')
                                    <span class="badge" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-times me-1"></i>Fail
                                    </span>
                                @else
                                    <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        {{ ucfirst($result->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $result->formatted_time_taken ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($result->submit_time)
                                    <div>
                                        <span class="text-muted">{{ $result->submit_time->format('M d, Y') }}</span><br>
                                        <small class="text-muted">{{ $result->submit_time->format('H:i A') }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($result->status == 'completed')
                                <a href="{{ route('admin.results.show', $result) }}" class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($studentAssessments->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $studentAssessments->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--primary-red);"></i>
                <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Results Yet</h5>
                <p class="text-muted" style="font-size: 1rem;">No student has attempted this assessment yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Performance Chart -->
@if($studentAssessments->count() > 0)
<div class="card shadow-sm mt-4" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-chart-pie me-2"></i>Performance Distribution
        </h5>
    </div>
    <div class="card-body" style="padding: 30px;">
        <canvas id="performanceChart" height="80"></canvas>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($studentAssessments->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Calculate score ranges
    const ranges = {
        '90-100%': {{ $studentAssessments->filter(fn($r) => $r->percentage >= 90)->count() }},
        '80-89%': {{ $studentAssessments->filter(fn($r) => $r->percentage >= 80 && $r->percentage < 90)->count() }},
        '70-79%': {{ $studentAssessments->filter(fn($r) => $r->percentage >= 70 && $r->percentage < 80)->count() }},
        '60-69%': {{ $studentAssessments->filter(fn($r) => $r->percentage >= 60 && $r->percentage < 70)->count() }},
        '50-59%': {{ $studentAssessments->filter(fn($r) => $r->percentage >= 50 && $r->percentage < 60)->count() }},
        'Below 50%': {{ $studentAssessments->filter(fn($r) => $r->percentage < 50)->count() }}
    };

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(ranges),
            datasets: [{
                label: 'Number of Students',
                data: Object.values(ranges),
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(102, 126, 234, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Score Distribution',
                    font: {
                        size: 16,
                        weight: '700'
                    },
                    color: '#333333',
                    padding: {
                        bottom: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: '#DC143C',
                    borderWidth: 2,
                    cornerRadius: 8
                }
            }
        }
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
@endif

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

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}

.progress {
    font-size: 0.875rem;
    font-weight: bold;
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

.table td {
    vertical-align: middle;
}

a[href*="export"]:hover,
a[href*="show"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
}

.btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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
@endpush
