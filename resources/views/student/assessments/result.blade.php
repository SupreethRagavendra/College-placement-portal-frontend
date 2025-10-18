@extends('layouts.student')

@section('title', 'Assessment Result')

@section('styles')
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

    .result-hero {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: #fff;
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
    }
    .result-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .score-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border: 4px solid rgba(255,255,255,0.3);
    }
    .score-circle .score-value {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1;
    }
    .score-circle .score-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-top: 5px;
        font-weight: 600;
    }
    .stat-box {
        background: white;
        border-radius: 15px;
        padding: 25px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }
    .stat-icon.bg-success-custom {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
    }
    .stat-icon.bg-danger-custom {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
        color: white;
    }
    .stat-icon.bg-info-custom {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }
    .bg-light-success {
        background-color: rgba(40, 167, 69, 0.08) !important;
    }
    .bg-light-danger {
        background-color: rgba(220, 20, 60, 0.08) !important;
    }
    .question-review {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    .question-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    .review-card {
        border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-radius: 15px;
        overflow: hidden;
    }
    .btn-kit-primary {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
    }
    .btn-kit-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
        color: white;
    }
    .btn-kit-secondary {
        background: white;
        color: var(--text-dark);
        border: 2px solid #e0e0e0;
        padding: 10px 28px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
    }
    .btn-kit-secondary:hover {
        background: var(--light-gray);
        border-color: var(--text-dark);
        color: var(--text-dark);
        transform: translateY(-2px);
    }
    .btn-kit-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .btn-kit-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-dark); font-weight: 700;">
        <i class="fas fa-chart-line me-2" style="color: #28a745;"></i>
        Assessment Result
    </h1>
    <div class="badge fs-6" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 10px 20px; border-radius: 20px; font-weight: 600;">
        @php
            $percentage = $result->total_questions > 0 ? round(($result->score / $result->total_questions) * 100, 1) : 0;
        @endphp
        {{ $percentage >= 60 ? 'Passed' : 'Review Required' }}
    </div>
</div>

<div class="result-hero">
    <div class="position-relative">
        @php
            $performanceText = $percentage >= 80 ? 'Excellent!' : ($percentage >= 60 ? 'Good Job!' : ($percentage >= 40 ? 'Keep Practicing!' : 'Need Improvement'));
            $performanceIcon = $percentage >= 80 ? 'fa-trophy' : ($percentage >= 60 ? 'fa-thumbs-up' : ($percentage >= 40 ? 'fa-star' : 'fa-book'));
        @endphp
        <div class="mb-3">
            <i class="fas {{ $performanceIcon }} fa-3x mb-3"></i>
            <h2 class="mb-2" style="font-weight: 700;">{{ $performanceText }}</h2>
            <h4 class="opacity-90" style="font-weight: 600;">{{ $assessment->name ?? 'Assessment' }}</h4>
        </div>
        <div class="score-circle">
            <div class="score-value">{{ $percentage }}%</div>
            <div class="score-label">Your Score</div>
        </div>
        <p class="mb-0 opacity-90" style="font-weight: 500;">You answered {{ $result->score ?? 0 }} out of {{ $result->total_questions ?? 0 }} questions correctly</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-box text-center">
            <div class="stat-icon bg-success-custom mx-auto">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="mb-1" style="color: var(--text-dark); font-weight: 700;">{{ $result->score ?? 0 }}</h3>
            <p class="text-muted mb-0" style="font-weight: 600;">Correct Answers</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box text-center">
            <div class="stat-icon bg-danger-custom mx-auto">
                <i class="fas fa-times"></i>
            </div>
            <h3 class="mb-1" style="color: var(--text-dark); font-weight: 700;">{{ ($result->total_questions ?? 0) - ($result->score ?? 0) }}</h3>
            <p class="text-muted mb-0" style="font-weight: 600;">Incorrect Answers</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box text-center">
            <div class="stat-icon bg-info-custom mx-auto">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="mb-1" style="color: var(--text-dark); font-weight: 700; font-size: 1.3rem;">{{ $result->submitted_at ? $result->submitted_at->format('M d, Y') : 'N/A' }}</h3>
            <p class="text-muted mb-0" style="font-weight: 600;">Completed On</p>
        </div>
    </div>
</div>

<div class="d-flex gap-3 justify-content-center mb-4">
    <a href="{{ route('student.assessments.index') }}" class="btn btn-kit-primary">
        <i class="fas fa-clipboard-list me-2"></i>Back to Assessments
    </a>
    <a href="{{ route('student.assessment.history') }}" class="btn btn-kit-secondary">
        <i class="fas fa-history me-2"></i>View History
    </a>
    @if($assessment->is_active)
    <a href="{{ route('student.assessments.show', $assessment) }}" class="btn btn-kit-success">
        <i class="fas fa-redo me-2"></i>Retake Assessment
    </a>
    @endif
</div>

<!-- Detailed Review Section -->
@if(!empty($detailedResults))
<div class="card review-card">
    <div class="card-header text-white" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); padding: 20px 25px; border: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1" style="font-weight: 700;">
                    <i class="fas fa-clipboard-check me-2"></i>Detailed Answer Review
                </h5>
                <small style="opacity: 0.9; font-weight: 500;">Review your answers and learn from the correct solutions</small>
            </div>
            <span class="badge fs-6" style="background: rgba(255,255,255,0.2); padding: 10px 18px; border-radius: 20px; font-weight: 600;">{{ count($detailedResults) }} Questions</span>
        </div>
    </div>
    <div class="card-body p-4" style="background: #fafafa;">
        @foreach($detailedResults as $index => $detail)
        <div class="question-review mb-4 p-4 border {{ $detail['is_correct'] ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}" style="background: white !important; border-width: 2px !important;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge mb-2" style="background: linear-gradient(135deg, {{ $detail['is_correct'] ? '#28a745, #218838' : 'var(--primary-red), var(--dark-red)' }}); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                        <i class="fas {{ $detail['is_correct'] ? 'fa-check' : 'fa-times' }} me-1"></i>
                        {{ $detail['is_correct'] ? 'Correct' : 'Incorrect' }}
                    </span>
                    <h6 class="mb-0 text-muted" style="font-weight: 600;">Question {{ $index + 1 }} of {{ count($detailedResults) }}</h6>
                </div>
            </div>
            
            <div class="question-text mb-4">
                <h6 class="fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $detail['question']->question }}</h6>
            </div>
            
            <div class="options">
                @php 
                    $options = is_array($detail['question']->options) ? $detail['question']->options : json_decode($detail['question']->options, true) ?? [];
                    $letters = ['A', 'B', 'C', 'D'];
                    $userAnswerLetter = strtoupper($detail['user_answer'] ?? '');
                    $correctAnswerLetter = strtoupper($detail['correct_answer'] ?? '');
                @endphp
                
                @foreach($options as $optIndex => $option)
                @php 
                    $currentLetter = $letters[$optIndex] ?? chr(65 + $optIndex);
                    $isCorrect = $currentLetter === $correctAnswerLetter;
                    $isUserAnswer = $currentLetter === $userAnswerLetter;
                @endphp
                <div class="form-check mb-3 p-3 rounded {{ $isCorrect ? 'bg-success-subtle' : ($isUserAnswer && !$detail['is_correct'] ? 'bg-danger-subtle' : 'bg-light') }}" style="border: 2px solid {{ $isCorrect ? '#28a745' : ($isUserAnswer && !$detail['is_correct'] ? 'var(--primary-red)' : '#e0e0e0') }}; transition: all 0.3s;">
                    <input class="form-check-input" type="radio" disabled {{ $isUserAnswer ? 'checked' : '' }}>
                    <label class="form-check-label d-flex align-items-center w-100">
                        <span class="badge me-3" style="background: linear-gradient(135deg, {{ $isCorrect ? '#28a745, #218838' : ($isUserAnswer && !$detail['is_correct'] ? 'var(--primary-red), var(--dark-red)' : '#6c757d, #5a6268') }}); padding: 6px 12px; border-radius: 8px; font-weight: 700; min-width: 35px; text-align: center;">{{ $currentLetter }}</span>
                        <span class="flex-grow-1" style="font-weight: {{ $isCorrect || $isUserAnswer ? '600' : '500' }}; color: {{ $isCorrect ? '#28a745' : ($isUserAnswer && !$detail['is_correct'] ? 'var(--primary-red)' : 'var(--text-dark)') }};">
                            {{ $option }}
                        </span>
                        @if($isCorrect)
                            <i class="fas fa-check-circle fa-lg ms-2" style="color: #28a745;"></i>
                        @elseif($isUserAnswer && !$detail['is_correct'])
                            <i class="fas fa-times-circle fa-lg ms-2" style="color: var(--primary-red);"></i>
                        @endif
                    </label>
                </div>
                @endforeach
            </div>
            
            @if(!$detail['is_correct'])
            <div class="alert mt-3 mb-0 d-flex align-items-start" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 10px; padding: 15px;">
                <i class="fas fa-lightbulb fa-lg me-3 mt-1" style="color: #0c5460;"></i>
                <div>
                    @php
                        $correctAnswerIndex = array_search($correctAnswerLetter, $letters);
                        $correctOptionText = ($correctAnswerIndex !== false && isset($options[$correctAnswerIndex])) ? $options[$correctAnswerIndex] : 'N/A';
                    @endphp
                    <strong style="color: #0c5460; font-weight: 700;">Correct Answer:</strong> 
                    <span style="color: #0c5460; font-weight: 500;">Option {{ $correctAnswerLetter }} - {{ $correctOptionText }}</span>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
