@extends('layouts.student')

@section('title', 'Assessment History - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.history-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
    position: relative;
    overflow: hidden;
}

.history-header .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.stat-box {
    background: rgba(255,255,255,0.2);
    padding: 16px 24px;
    border-radius: 12px;
    display: inline-block;
    margin-right: 16px;
    backdrop-filter: blur(10px);
}

.stat-box .stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.stat-box .stat-label {
    font-size: 0.9rem;
    opacity: 0.95;
    font-weight: 500;
}

.history-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
}

.score-badge {
    font-size: 0.9rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 8px;
}

.score-excellent { 
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
    color: #155724;
    border: 1px solid #c3e6cb;
}

.score-good { 
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); 
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.score-average { 
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); 
    color: #856404;
    border: 1px solid #ffeeba;
}

.score-poor { 
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
    color: #721c24;
    border: 1px solid #f5c6cb;
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

.btn-group .btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="history-header">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1 class="h2 mb-2 fw-bold">
                <i class="fas fa-history me-2"></i>Assessment History
            </h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">View all your past assessment attempts and track your progress</p>
        </div>
        <div class="d-flex gap-3 flex-wrap">
            @php
                $totalAttempts = count($results ?? []);
                $avgScore = $totalAttempts > 0 ? collect($results)->avg(function($r) {
                    return ($r['total_questions'] ?? 0) > 0 ? ($r['score'] ?? 0) / $r['total_questions'] * 100 : 0;
                }) : 0;
            @endphp
            <div class="stat-box">
                <div class="stat-value">{{ $totalAttempts }}</div>
                <div class="stat-label">Total Attempts</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ round($avgScore) }}%</div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>
    </div>
    <i class="fas fa-clock-rotate-left hero-icon"></i>
</div>

<!-- Back to Dashboard Button -->
<div class="mb-3">
    <a href="{{ route('student.dashboard') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none;">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
</div>

<!-- History Table Card -->
<div class="card history-card">
    <div class="card-body p-0">
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
                            <i class="fas fa-chart-simple me-2" style="color: var(--primary-red);"></i>Score
                        </th>
                        <th>
                            <i class="fas fa-clock me-2" style="color: var(--primary-red);"></i>Time Taken
                        </th>
                        <th>
                            <i class="fas fa-calendar me-2" style="color: var(--primary-red);"></i>Date
                        </th>
                        <th class="text-center">
                            <i class="fas fa-cog me-2" style="color: var(--primary-red);"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                    @php 
                        $assessment = $assessmentsById[(int)($result['assessment_id'] ?? 0)] ?? null;
                        $scorePercent = ($result['total_questions'] ?? 0) > 0 ? 
                            round(($result['score'] ?? 0) / $result['total_questions'] * 100) : 0;
                        $scoreClass = $scorePercent >= 80 ? 'score-excellent' : 
                                     ($scorePercent >= 60 ? 'score-good' : 
                                     ($scorePercent >= 40 ? 'score-average' : 'score-poor'));
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-alt" style="color: white; font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color: var(--text-dark); font-size: 1rem;">{{ $assessment['title'] ?? $assessment['name'] ?? 'Unknown Assessment' }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i>{{ $assessment['questions_count'] ?? 0 }} questions
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge" style="background: linear-gradient(135deg, {{ ($assessment['category'] ?? '')==='Aptitude' ? '#007bff, #0056b3' : '#28a745, #218838' }}); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-tag me-1"></i>{{ $assessment['category'] ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <span class="score-badge {{ $scoreClass }}">
                                    {{ $result['score'] ?? 0 }}/{{ $result['total_questions'] ?? 0 }}
                                </span>
                                <div class="fw-bold mt-1" style="color: {{ $scorePercent >= 60 ? '#28a745' : '#dc3545' }}; font-size: 1.1rem;">{{ $scorePercent }}%</div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div style="color: #6c757d; font-weight: 500;">
                                <i class="far fa-clock me-1"></i>
                                @php
                                    $seconds = $result['time_taken'] ?? 0;
                                    $minutes = floor($seconds / 60);
                                    $remainingSeconds = $seconds % 60;
                                @endphp
                                {{ $minutes }}m {{ $remainingSeconds }}s
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <div class="fw-bold" style="color: var(--text-dark);">{{ \Carbon\Carbon::parse($result['submitted_at'] ?? now())->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($result['submitted_at'] ?? now())->format('h:i A') }}</small>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $assessmentModel = \App\Models\Assessment::find($result['assessment_id'] ?? 0); @endphp
                            @if($assessmentModel && $assessmentModel->is_active)
                                <div class="btn-group" role="group">
                                    <a class="btn btn-sm" href="{{ route('student.assessments.result', $assessmentModel) }}" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 8px 16px; font-weight: 600;">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a class="btn btn-sm ms-1" href="{{ route('student.assessments.show', $assessmentModel) }}" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none; padding: 8px 16px; font-weight: 600;">
                                        <i class="fas fa-redo me-1"></i>Retake
                                    </a>
                                </div>
                            @elseif($assessmentModel)
                                <a class="btn btn-sm" href="{{ route('student.assessments.result', $assessmentModel) }}" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600;">
                                    <i class="fas fa-eye me-1"></i>View Result
                                </a>
                            @else
                                <span class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>Not available
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div style="padding: 40px;">
                                <i class="fas fa-inbox fa-4x mb-3" style="color: var(--primary-red); opacity: 0.3;"></i>
                                <h5 class="fw-bold" style="color: var(--text-dark);">No Assessment History</h5>
                                <p class="text-muted mb-4">You haven't taken any assessments yet. Start your learning journey now!</p>
                                <a href="{{ route('student.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
                                    <i class="fas fa-clipboard-list me-2"></i>Browse Assessments
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(count($results ?? []) > 10)
<div class="mt-4 text-center">
    <div class="alert" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: none; border-left: 4px solid #28a745; border-radius: 12px; display: inline-block; padding: 12px 24px;">
        <i class="fas fa-info-circle me-2" style="color: #28a745;"></i>
        <strong>Showing {{ count($results) }} assessment attempts</strong>
    </div>
</div>
@endif

@endsection
