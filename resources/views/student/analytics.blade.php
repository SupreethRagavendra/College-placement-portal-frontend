@extends('layouts.student')

@section('title', 'Analytics - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.analytics-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
    position: relative;
    overflow: hidden;
}

.analytics-header .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.analytics-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.analytics-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 3px solid var(--primary-red);
    padding: 18px 24px;
    font-weight: 700;
    color: var(--text-dark);
}

.stat-box {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
}

.stat-box h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.stat-box p {
    margin-bottom: 0;
    opacity: 0.9;
    font-weight: 600;
}

.chart-container {
    position: relative;
    height: 300px;
    padding: 20px 10px;
}
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="analytics-header">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1 class="h2 mb-2 fw-bold">
                <i class="fas fa-chart-line me-2"></i>Performance Analytics
            </h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">Visualize your progress, identify strengths, and track your performance trends</p>
        </div>
        <div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    <i class="fas fa-chart-line hero-icon"></i>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-box">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <h3>{{ $totalAttempts ?? 0 }}</h3>
            <p>Total Assessments</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <i class="fas fa-percentage fa-2x mb-2"></i>
            <h3>{{ number_format($averageScore ?? 0, 1) }}%</h3>
            <p>Average Score</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <i class="fas fa-trophy fa-2x mb-2"></i>
            <h3>{{ $bestScore ?? 0 }}%</h3>
            <p>Best Performance</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card analytics-card">
            <div class="card-header">
                <i class="fas fa-chart-line me-2" style="color: var(--primary-red);"></i>
                Score Trends Over Time
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="scoreTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card analytics-card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2" style="color: var(--primary-red);"></i>
                Category Performance
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoryPie"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Info -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card analytics-card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2" style="color: var(--primary-red);"></i>
                Performance Insights
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start p-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-radius: 12px;">
                            <div class="me-3">
                                <i class="fas fa-check-circle fa-2x" style="color: #155724;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #155724;">Strong Areas</h6>
                                <p class="mb-0 small">Focus on maintaining consistency in categories where you score above 70%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start p-3" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); border-radius: 12px;">
                            <div class="me-3">
                                <i class="fas fa-exclamation-triangle fa-2x" style="color: #856404;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #856404;">Areas for Improvement</h6>
                                <p class="mb-0 small">Practice more in categories where you score below 60% to improve</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Chart.js defaults
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6c757d';

// Line chart: scores vs time
new Chart(document.getElementById('scoreTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($trendLabels ?? []) !!},
        datasets: [{
            label: 'Score %',
            data: {!! json_encode($scoreTrends ?? []) !!},
            borderColor: '#DC143C',
            backgroundColor: 'rgba(220, 20, 60, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
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
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: {
                        size: 14,
                        weight: '600'
                    },
                    padding: 15
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: '700'
                },
                bodyFont: {
                    size: 13
                },
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    },
                    font: {
                        size: 12,
                        weight: '600'
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 12,
                        weight: '600'
                    }
                }
            }
        }
    }
});

// Pie chart: category-wise performance
new Chart(document.getElementById('categoryPie'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($categoryStats ?? [])) !!},
        datasets: [{
            data: {!! json_encode(array_values($categoryStats ?? [])) !!},
            backgroundColor: [
                'rgba(220, 20, 60, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(0, 123, 255, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: 13,
                        weight: '600'
                    },
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: '700'
                },
                bodyFont: {
                    size: 13
                },
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + '%';
                        return label;
                    }
                }
            }
        }
    }
});
</script>
@endsection
