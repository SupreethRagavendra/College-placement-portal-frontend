@extends('layouts.student')

@section('title', 'Dashboard - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --light-red: #EF4444;
    --text-dark: #333333;
}

.stat-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.icon-badge {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.hero-section {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
}

.hero-section .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.assessment-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.assessment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.chart-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
</style>
@endsection

@section('content')
@php
    $totalTests = $totalTests ?? 0;
    $avgScore = isset($avgScore) ? round($avgScore) : 0;
    $rankText = $rankText ?? '—';
    $trendLabels = $trendLabels ?? [];
    $trendScores = $trendScores ?? [];
    $recentTests = $recentTests ?? [];
@endphp

<!-- Hero Section -->
<div class="hero-section">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <h1 class="display-6 fw-bold mb-2">Welcome back, {{ Auth::user()->name ?? 'Student' }}</h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">Track your progress, take tests, and analyze performance.</p>
            @if(Auth::user()->register_number)
                <p class="mb-0 opacity-75" style="font-size: 0.95rem;"><i class="fas fa-id-badge me-1"></i>Register No: {{ Auth::user()->register_number }}</p>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('student.assessments.index') }}" class="btn btn-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,255,255,0.2);">
                <i class="fa-solid fa-clipboard-list me-2"></i>View Assessments
            </a>
            <a href="{{ route('student.assessment.history') }}" class="btn btn-outline-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; border: 2px solid rgba(255,255,255,0.4);">
                <i class="fa-solid fa-history me-2"></i>Test History
            </a>
        </div>
    </div>
    <i class="fa-solid fa-graduation-cap hero-icon"></i>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold mb-2" style="color: #888; letter-spacing: 0.5px;">Tests Taken</div>
                        <div class="fs-2 fw-bold" style="color: var(--text-dark);">{{ $totalTests }}</div>
                    </div>
                    <span class="icon-badge" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-uppercase small fw-semibold" style="color: #888; letter-spacing: 0.5px;">Average Score</div>
                    <span class="icon-badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white;">
                        <i class="fa-solid fa-percent"></i>
                    </span>
                </div>
                <div class="fs-2 fw-bold mb-2" style="color: var(--text-dark);">{{ $avgScore }}%</div>
                <div class="progress" style="height: 8px; border-radius: 10px; background-color: #e9ecef;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $avgScore }}%; background: linear-gradient(135deg, #28a745 0%, #218838 100%);" aria-valuenow="{{ $avgScore }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold mb-2" style="color: #888; letter-spacing: 0.5px;">Current Rank</div>
                        <div class="fs-2 fw-bold" style="color: var(--text-dark);">{{ $rankText }}</div>
                    </div>
                    <span class="icon-badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white;">
                        <i class="fa-solid fa-ranking-star"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small fw-semibold mb-2" style="color: #888; letter-spacing: 0.5px;">Last Activity</div>
                        <div class="fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $recentTests[0]['date'] ?? '—' }}</div>
                    </div>
                    <span class="icon-badge" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white;">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Assessments Section -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card stat-card">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clipboard-list me-2"></i>Available Assessments</h5>
                <a href="{{ route('student.assessments.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 20px; border-radius: 50px; font-weight: 600;">View All</a>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @forelse($assessments as $assessment)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card assessment-card h-100" style="border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="mb-3 flex-grow-1">
                                        <h6 class="card-title mb-2 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->title ?? $assessment->name ?? 'Assessment' }}</h6>
                                        <p class="text-muted mb-3" style="font-size: 0.9rem;">{{ Str::limit($assessment->description ?? 'No description', 80) }}</p>
                                        <div class="d-flex gap-2 flex-wrap mb-3">
                                            <span class="badge" style="background: linear-gradient(135deg, {{ ($assessment->category ?? '')==='Aptitude' ? '#007bff, #0056b3' : '#28a745, #218838' }}); padding: 6px 12px; border-radius: 8px; font-size: 0.8rem;">
                                                <i class="fas fa-tag me-1"></i>{{ $assessment->category ?? 'General' }}
                                            </span>
                                            <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 6px 12px; border-radius: 8px; font-size: 0.8rem;">
                                                <i class="fa-regular fa-clock me-1"></i>{{ $assessment->duration ?? $assessment->total_time ?? 0 }} min
                                            </span>
                                        </div>
                                        <div class="row g-2 small">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-question-circle me-2" style="color: #17a2b8;"></i>
                                                    <span class="text-muted">{{ $assessment->questions_count ?? $assessment->questions->count() ?? 0 }} Questions</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-star me-2" style="color: #ffc107;"></i>
                                                    <span class="text-muted">{{ $assessment->total_marks ?? 0 }} Marks</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($attemptsByAssessment[$assessment->id]))
                                        @php $attempt = $attemptsByAssessment[$assessment->id]; @endphp
                                        <div class="mb-3 p-3 rounded" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-left: 4px solid #28a745;">
                                            <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">Your Score:</small>
                                            <strong style="color: var(--text-dark); font-size: 1.1rem;">{{ $attempt['score'] ?? 0 }}/{{ $attempt['total_questions'] ?? 0 }}</strong>
                                            <small class="text-muted d-block mt-1">{{ number_format(($attempt['score'] ?? 0) / max($attempt['total_questions'] ?? 1, 1) * 100, 1) }}%</small>
                                        </div>
                                        <a href="{{ route('student.assessments.result', $assessment) }}" class="btn w-100" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; transition: all 0.3s; text-decoration: none;">
                                            <i class="fas fa-eye me-2"></i>View Results & Review
                                        </a>
                                    @else
                                        <a href="{{ route('student.assessments.show', $assessment) }}" class="btn w-100" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; transition: all 0.3s; text-decoration: none; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
                                            <i class="fas fa-play me-2"></i>Start Assessment
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-4x mb-3" style="color: var(--primary-red); opacity: 0.3;"></i>
                                <h5 class="fw-bold" style="color: var(--text-dark);">No Assessments Available</h5>
                                <p class="text-muted mb-0">Check back later for new assessments.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance & Recent Tests -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card h-100 chart-card">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: white; padding: 20px 25px; border-bottom: 2px solid #f0f0f0;">
                <h5 class="mb-0 fw-bold" style="color: var(--text-dark);">
                    <i class="fa-solid fa-chart-line me-2" style="color: var(--primary-red);"></i>Performance Trend
                </h5>
                <small class="text-muted">Recent attempts</small>
            </div>
            <div class="card-body p-4" style="min-height: 300px;">
                <canvas id="performanceChart" height="120" style="display: none;"></canvas>
                <div id="noChartData" class="text-center text-muted d-flex align-items-center justify-content-center" style="min-height: 250px;">
                    <div>
                        <i class="fa-solid fa-chart-line fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p class="mb-0">No trend data available yet. Take some assessments to see your progress!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card h-100 chart-card">
            <div class="card-header" style="background: white; padding: 20px 25px; border-bottom: 2px solid #f0f0f0;">
                <h5 class="mb-0 fw-bold" style="color: var(--text-dark);">
                    <i class="fa-regular fa-clock me-2" style="color: var(--primary-red);"></i>Recent Tests
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($recentTests as $t)
                        <li class="list-group-item p-3">
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <span class="icon-badge" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white;">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-1" style="color: var(--text-dark);">{{ $t['assessment_name'] ?? 'Assessment' }}</div>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($t['date'])->format('d M Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-5" style="color: var(--primary-red);">{{ $t['score'] }}%</div>
                                </div>
                            </div>
                            @php $assessment = \App\Models\Assessment::where('id', $t['assessment_id'] ?? 0)->where('is_active', true)->first(); @endphp
                            @if($assessment)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('student.assessments.result', $assessment) }}" class="btn btn-sm flex-grow-1" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 6px; font-weight: 500;">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('student.assessments.show', $assessment) }}" class="btn btn-sm flex-grow-1" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; border-radius: 6px; font-weight: 500;">
                                        <i class="fas fa-redo me-1"></i>Retake
                                    </a>
                                </div>
                            @else
                                <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Assessment no longer available</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-5">
                            <i class="fa-regular fa-clock fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0">No recent tests found.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function(){
        const labels = {!! json_encode($trendLabels) !!};
        const scores = {!! json_encode($trendScores) !!};
        const canvas = document.getElementById('performanceChart');
        const noData = document.getElementById('noChartData');
        
        if (Array.isArray(labels) && labels.length && Array.isArray(scores) && scores.length) {
            canvas.style.display = 'block';
            noData.style.display = 'none';
            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(220, 20, 60, 0.3)');
            gradient.addColorStop(1, 'rgba(220, 20, 60, 0)');
            
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Score %',
                        data: scores,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#DC143C',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#DC143C',
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
                                callback: (v) => v + '%',
                                font: { weight: '600' }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: '600' } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            intersect: false,
                            mode: 'index',
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return 'Score: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    })();
</script>
@endsection
