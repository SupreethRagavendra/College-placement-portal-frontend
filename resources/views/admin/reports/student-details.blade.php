@extends('layouts.admin')

@section('title', 'Student Performance Details - ' . $student->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
                <i class="fas fa-user-graduate me-2" style="color: var(--primary-red);"></i>
                Student Performance Details
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: var(--primary-red); text-decoration: none;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" style="color: var(--primary-red); text-decoration: none;">Reports</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.student-performance') }}" style="color: var(--primary-red); text-decoration: none;">Students</a></li>
                    <li class="breadcrumb-item active" style="color: var(--text-dark);">{{ $student->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
            <a href="{{ route('admin.reports.student-performance') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-body" style="padding: 30px;">
                    <div class="row align-items-center">
                        <div class="col-md-auto">
                            <div style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="col-md">
                            <h4 class="mb-2" style="font-weight: 700; color: var(--text-dark);">{{ $student->name }}</h4>
                            <p class="text-muted mb-3" style="font-size: 0.95rem;">
                                <i class="fas fa-envelope me-1"></i> {{ $student->email }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-id-badge me-1"></i> Register No: {{ $student->register_number ?? 'N/A' }}
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 500; font-size: 0.85rem;">
                                    <i class="fas fa-clipboard-list me-1"></i>{{ $overallStats['total_assessments'] }} Assessments
                                </span>
                                <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 500; font-size: 0.85rem;">
                                    <i class="fas fa-check-circle me-1"></i>{{ number_format($overallStats['pass_rate'], 1) }}% Pass Rate
                                </span>
                                <span class="badge" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 500; font-size: 0.85rem;">
                                    <i class="fas fa-chart-line me-1"></i>{{ number_format($overallStats['average_score'], 1) }}% Avg Score
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; border-left: 4px solid var(--primary-red); overflow: hidden;">
                <div class="card-body" style="padding: 25px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase mb-2" style="font-weight: 700; color: var(--primary-red); font-size: 0.8rem; letter-spacing: 0.5px;">Total Assessments</div>
                            <div class="h4 mb-1" style="font-weight: 700; color: var(--text-dark);">{{ $overallStats['total_assessments'] }}</div>
                            <small class="text-muted">{{ $overallStats['unique_assessments'] }} unique</small>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, rgba(220, 20, 60, 0.1) 0%, rgba(185, 28, 28, 0.1) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: var(--primary-red);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; border-left: 4px solid #28a745; overflow: hidden;">
                <div class="card-body" style="padding: 25px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase mb-2" style="font-weight: 700; color: #28a745; font-size: 0.8rem; letter-spacing: 0.5px;">Average Score</div>
                            <div class="h4 mb-1" style="font-weight: 700; color: var(--text-dark);">{{ number_format($overallStats['average_score'], 1) }}%</div>
                            <small class="text-muted">Best: {{ number_format($overallStats['best_score'], 1) }}%</small>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(40, 167, 69, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #28a745;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; border-left: 4px solid #17a2b8; overflow: hidden;">
                <div class="card-body" style="padding: 25px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase mb-2" style="font-weight: 700; color: #17a2b8; font-size: 0.8rem; letter-spacing: 0.5px;">Time Spent</div>
                            <div class="h4 mb-1" style="font-weight: 700; color: var(--text-dark);">
                                @php
                                    $totalSeconds = $overallStats['total_time_spent'];
                                    $hours = floor($totalSeconds / 3600);
                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                @endphp
                                {{ $hours }}h {{ $minutes }}m
                            </div>
                            <small class="text-muted">Avg: {{ round($overallStats['average_time'] / 60) }}m</small>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(23, 162, 184, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="font-size: 1.5rem; color: #17a2b8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; border-left: 4px solid #ffc107; overflow: hidden;">
                <div class="card-body" style="padding: 25px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase mb-2" style="font-weight: 700; color: #ffc107; font-size: 0.8rem; letter-spacing: 0.5px;">Pass Rate</div>
                            <div class="h4 mb-1" style="font-weight: 700; color: var(--text-dark);">{{ number_format($overallStats['pass_rate'], 1) }}%</div>
                            <small class="text-muted">
                                @php
                                    $passed = collect($assessmentHistory)->where('score_percentage', '>=', 50)->count();
                                @endphp
                                {{ $passed }} passed
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255, 193, 7, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trophy" style="font-size: 1.5rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Performance Trend Chart -->
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-chart-area me-2"></i>Performance Trend (Last 10 Assessments)
                    </h5>
                </div>
                <div class="card-body" style="padding: 30px;">
                    <canvas id="performanceTrendChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-chart-pie me-2"></i>Grade Distribution
                    </h5>
                </div>
                <div class="card-body" style="padding: 30px;">
                    <canvas id="gradeChart" height="200"></canvas>
                    <div class="mt-4">
                        @foreach($gradeDistribution as $grade => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 10px; background: #f8f9fa; border-radius: 8px;">
                            <span class="badge" style="background: linear-gradient(135deg, {{ $grade == 'A' ? '#28a745, #218838' : ($grade == 'F' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">Grade {{ $grade }}</span>
                            <span style="font-weight: 600; color: var(--text-dark);">{{ $count }} assessment(s)</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category & Difficulty Performance -->
    <div class="row mb-4">
        <!-- Category Performance -->
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-layer-group me-2"></i>Category-wise Performance
                    </h5>
                </div>
                <div class="card-body" style="padding: 30px;">
                    <canvas id="categoryChart" height="150"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover" style="border-radius: 8px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <tr>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Category</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Attempts</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Avg Score</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Pass Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryPerformance as $cat)
                                <tr>
                                    <td style="padding: 12px; font-weight: 500;">{{ $cat['category'] ?? 'N/A' }}</td>
                                    <td style="padding: 12px;">{{ $cat['attempts'] }}</td>
                                    <td style="padding: 12px; font-weight: 600;">{{ number_format($cat['average_score'], 1) }}%</td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: linear-gradient(135deg, {{ $cat['pass_rate'] >= 60 ? '#28a745, #218838' : '#ffc107, #e0a800' }}); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ number_format($cat['pass_rate'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Difficulty Performance -->
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-signal me-2"></i>Difficulty-wise Performance
                    </h5>
                </div>
                <div class="card-body" style="padding: 30px;">
                    <canvas id="difficultyChart" height="150"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover" style="border-radius: 8px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <tr>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Difficulty</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Attempts</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Avg Score</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Pass Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($difficultyPerformance as $diff)
                                <tr>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: linear-gradient(135deg, {{ strtolower($diff['difficulty']) == 'easy' ? '#28a745, #218838' : (strtolower($diff['difficulty']) == 'hard' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ $diff['difficulty'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">{{ $diff['attempts'] }}</td>
                                    <td style="padding: 12px; font-weight: 600;">{{ $diff['average_score'] }}%</td>
                                    <td style="padding: 12px; font-weight: 600;">{{ $diff['pass_rate'] }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment History Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 20px 25px; border: none;">
                    <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="fas fa-history me-2"></i>Complete Assessment History
                    </h5>
                    <span class="badge" style="background: rgba(255, 255, 255, 0.2); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600;">{{ $assessmentHistory->count() }} Total Attempts</span>
                </div>
                <div class="card-body" style="padding: 25px;">
                    <div class="table-responsive">
                        <table class="table table-hover" id="assessmentHistoryTable" style="border-radius: 8px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <tr>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Date</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Assessment</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Category</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Difficulty</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Score</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Grade</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Time Taken</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Status</th>
                                    <th style="font-weight: 700; color: var(--text-dark); padding: 12px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assessmentHistory as $attempt)
                                <tr>
                                    <td style="padding: 12px; font-weight: 500;">{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y') : 'N/A' }}</td>
                                    <td style="padding: 12px; font-weight: 500;">{{ $attempt->assessment->name ?? 'N/A' }}</td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">{{ $attempt->assessment->category ?? 'N/A' }}</span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: linear-gradient(135deg, {{ ($attempt->assessment->difficulty_level ?? '') == 'easy' ? '#28a745, #218838' : (($attempt->assessment->difficulty_level ?? '') == 'hard' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ ucfirst($attempt->assessment->difficulty_level ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <strong>{{ $attempt->score }}/{{ $attempt->total_questions }}</strong>
                                        <br>
                                        <small class="text-muted">{{ number_format($attempt->score_percentage, 1) }}%</small>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span class="badge" style="background: linear-gradient(135deg, {{ $attempt->grade == 'A' ? '#28a745, #218838' : ($attempt->grade == 'F' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                            {{ $attempt->grade }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; font-weight: 500;">
                                        @php
                                            $minutes = floor($attempt->time_taken / 60);
                                            $seconds = $attempt->time_taken % 60;
                                        @endphp
                                        {{ $minutes }}m {{ $seconds }}s
                                    </td>
                                    <td style="padding: 12px;">
                                        @if($attempt->score_percentage >= 50)
                                            <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">Passed</span>
                                        @else
                                            <span class="badge" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">Failed</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="{{ route('admin.reports.assessment-details', $attempt->assessment_id) }}" 
                                           class="btn btn-sm" 
                                           title="View Assessment Details"
                                           style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.table-hover tbody tr:hover {
    background-color: rgba(220, 20, 60, 0.05);
}

@media print {
    .btn, nav {
        display: none !important;
    }
}
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#assessmentHistoryTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Performance Trend Chart
    const trendData = @json($recentTrend);
    new Chart(document.getElementById('performanceTrendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Score %',
                data: trendData.map(d => d.score),
                borderColor: '#DC143C',
                backgroundColor: 'rgba(220, 20, 60, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#DC143C',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Grade Distribution Chart
    const gradeData = @json($gradeDistribution);
    new Chart(document.getElementById('gradeChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(gradeData),
            datasets: [{
                data: Object.values(gradeData),
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#fd7e14',
                    '#dc3545',
                    '#DC143C'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Category Performance Chart
    const categoryData = @json($categoryPerformance);
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.category || 'N/A'),
            datasets: [{
                label: 'Average Score %',
                data: categoryData.map(c => c.average_score),
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Difficulty Performance Chart
    const difficultyData = @json($difficultyPerformance);
    new Chart(document.getElementById('difficultyChart'), {
        type: 'radar',
        data: {
            labels: difficultyData.map(d => d.difficulty),
            datasets: [{
                label: 'Average Score',
                data: difficultyData.map(d => d.average_score),
                borderColor: '#DC143C',
                backgroundColor: 'rgba(220, 20, 60, 0.2)'
            }, {
                label: 'Pass Rate',
                data: difficultyData.map(d => d.pass_rate),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
