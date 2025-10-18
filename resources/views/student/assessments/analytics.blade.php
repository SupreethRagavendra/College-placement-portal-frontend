@extends('layouts.student')

@section('title', 'Analytics')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 36px 24px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .hero-section .hero-icon {
        position: absolute;
        right: -40px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.15;
    }
    .analytics-card {
        border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        border-radius: 12px;
    }
    .stat-box {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="display-6 fw-bold mb-2"><i class="fas fa-chart-line me-3"></i>Performance Analytics</h1>
            <p class="mb-0 opacity-90">Visualize your progress, identify strengths, and track improvement over time.</p>
        </div>
        <i class="fa-solid fa-chart-line hero-icon"></i>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Category Performance Section -->
    <div class="col-12 col-lg-6">
        <div class="card analytics-card h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Performance by Category</h5>
                <small class="text-muted">Average scores across different assessment categories</small>
            </div>
            <div class="card-body">
                @if($categoryPerformance && count($categoryPerformance) > 0)
                    <canvas id="categoryChart" height="250"></canvas>
                    <div class="row mt-4">
                        @foreach($categoryPerformance as $category)
                        <div class="col-6 mb-3">
                            <div class="stat-box">
                                <div class="text-muted small mb-1">{{ $category->category ?? 'General' }}</div>
                                <div class="h3 mb-1">{{ number_format($category->avg_percentage, 1) }}%</div>
                                <div class="text-muted small">{{ $category->attempts }} {{ Str::plural('attempt', $category->attempts) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No category data available yet. Complete assessments to see your performance.</p>
                        <a href="{{ route('student.assessments.index') }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-list me-2"></i>Browse Assessments
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Performance Trend -->
    <div class="col-12 col-lg-6">
        <div class="card analytics-card h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>Monthly Performance Trend</h5>
                <small class="text-muted">Your average scores over the last 6 months</small>
            </div>
            <div class="card-body">
                @if($monthlyPerformance && count($monthlyPerformance) > 0)
                    <canvas id="monthlyChart" height="250"></canvas>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Total Attempts:</strong> {{ $monthlyPerformance->sum('attempts') }} assessments completed
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No trend data available yet. Complete more assessments to track your progress.</p>
                        <a href="{{ route('student.assessments.index') }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-list me-2"></i>Browse Assessments
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
@if($categoryPerformance && count($categoryPerformance) > 0)
<div class="row g-4">
    <div class="col-12">
        <div class="card analytics-card">
            <div class="card-body">
                <h5 class="mb-4"><i class="fas fa-lightbulb me-2 text-warning"></i>Performance Insights</h5>
                <div class="row">
                    @php
                        $totalAttempts = $categoryPerformance->sum('attempts');
                        $overallAvg = $categoryPerformance->avg('avg_percentage');
                        $bestCategory = $categoryPerformance->sortByDesc('avg_percentage')->first();
                    @endphp
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary-subtle p-3 me-3">
                                <i class="fas fa-clipboard-check text-primary fa-lg"></i>
                            </div>
                            <div>
                                <div class="h4 mb-0">{{ $totalAttempts }}</div>
                                <small class="text-muted">Total Assessments</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success-subtle p-3 me-3">
                                <i class="fas fa-percentage text-success fa-lg"></i>
                            </div>
                            <div>
                                <div class="h4 mb-0">{{ number_format($overallAvg, 1) }}%</div>
                                <small class="text-muted">Overall Average</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning-subtle p-3 me-3">
                                <i class="fas fa-trophy text-warning fa-lg"></i>
                            </div>
                            <div>
                                <div class="h4 mb-0">{{ $bestCategory->category ?? 'N/A' }}</div>
                                <small class="text-muted">Strongest Category</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    @if($categoryPerformance && count($categoryPerformance) > 0)
    // Category Performance Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryPerformance->pluck('category')) !!},
                datasets: [{
                    data: {!! json_encode($categoryPerformance->pluck('avg_percentage')) !!},
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(237, 100, 166, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if($monthlyPerformance && count($monthlyPerformance) > 0)
    // Monthly Performance Trend Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        @php
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $formattedLabels = $monthlyPerformance->map(function($item) use ($monthNames) {
                return $monthNames[$item->month - 1] . ' ' . $item->year;
            })->reverse()->values();
        @endphp
        
        const gradient = monthlyCtx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(75, 192, 192, 0.4)');
        gradient.addColorStop(1, 'rgba(75, 192, 192, 0.0)');

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($formattedLabels) !!},
                datasets: [{
                    label: 'Average Score %',
                    data: {!! json_encode($monthlyPerformance->pluck('avg_percentage')->reverse()->values()) !!},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: gradient,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(75, 192, 192, 1)',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Average: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endsection
