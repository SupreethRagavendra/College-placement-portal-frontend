@extends('layouts.student')

@section('title', 'Assessment Result')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chart-line"></i> Assessment Result</h1>
        <a href="{{ route('student.results.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Results
        </a>
    </div>

    <!-- Result Summary Card -->
    <div class="card mb-4 border-0 shadow-lg">
        <div class="card-header bg-{{ $studentAssessment->pass_status == 'pass' ? 'success' : 'danger' }} text-white py-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-0">{{ $studentAssessment->assessment->title }}</h3>
                    <p class="mb-0 mt-2">
                        <span class="badge bg-light text-dark">{{ $studentAssessment->assessment->category }}</span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <h1 class="display-4 mb-0">{{ number_format($studentAssessment->percentage, 2) }}%</h1>
                    <h5 class="mb-0">
                        @if($studentAssessment->pass_status == 'pass')
                            <i class="fas fa-check-circle"></i> PASSED
                        @else
                            <i class="fas fa-times-circle"></i> FAILED
                        @endif
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="stat-box">
                        <i class="fas fa-star text-warning fa-2x mb-2"></i>
                        <h4>{{ $studentAssessment->obtained_marks }}</h4>
                        <p class="text-muted mb-0">out of {{ $studentAssessment->total_marks }}</p>
                        <small class="text-muted">Total Score</small>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="stat-box">
                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                        <h4>{{ $studentAssessment->studentAnswers->where('is_correct', true)->count() }}</h4>
                        <p class="text-muted mb-0">out of {{ $studentAssessment->studentAnswers->count() }}</p>
                        <small class="text-muted">Correct Answers</small>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="stat-box">
                        <i class="fas fa-clock text-info fa-2x mb-2"></i>
                        <h4>{{ $studentAssessment->formatted_time_taken }}</h4>
                        <p class="text-muted mb-0">Time Taken</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="stat-box">
                        <i class="fas fa-calendar text-primary fa-2x mb-2"></i>
                        <h4>{{ $studentAssessment->submit_time ? $studentAssessment->submit_time->format('M d, Y') : 'N/A' }}</h4>
                        <p class="text-muted mb-0">Submitted</p>
                    </div>
                </div>
            </div>

            <div class="progress mt-4" style="height: 40px;">
                <div class="progress-bar bg-{{ $studentAssessment->percentage >= 50 ? 'success' : 'danger' }}" 
                     role="progressbar" 
                     style="width: {{ $studentAssessment->percentage }}%; font-size: 1.2rem; font-weight: bold;">
                    {{ number_format($studentAssessment->percentage, 2) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Breakdown -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check"></i> Strengths</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-success">Correct Answers</h6>
                    <h2 class="text-success">{{ $studentAssessment->studentAnswers->where('is_correct', true)->count() }}</h2>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ ($studentAssessment->studentAnswers->where('is_correct', true)->count() / $studentAssessment->studentAnswers->count()) * 100 }}%">
                            {{ number_format(($studentAssessment->studentAnswers->where('is_correct', true)->count() / $studentAssessment->studentAnswers->count()) * 100, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times"></i> Areas for Improvement</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-danger">Incorrect Answers</h6>
                    <h2 class="text-danger">{{ $studentAssessment->studentAnswers->where('is_correct', false)->count() }}</h2>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-danger" 
                             style="width: {{ ($studentAssessment->studentAnswers->where('is_correct', false)->count() / $studentAssessment->studentAnswers->count()) * 100 }}%">
                            {{ number_format(($studentAssessment->studentAnswers->where('is_correct', false)->count() / $studentAssessment->studentAnswers->count()) * 100, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question-wise Analysis -->
    @if($studentAssessment->assessment->show_correct_answers)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list-alt"></i> Detailed Question Analysis</h5>
        </div>
        <div class="card-body">
            @foreach($studentAssessment->studentAnswers as $index => $answer)
            <div class="card mb-3 border-{{ $answer->is_correct ? 'success' : 'danger' }}">
                <div class="card-header bg-{{ $answer->is_correct ? 'success' : 'danger' }} bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <span class="badge bg-dark">Q{{ $index + 1 }}</span>
                            {{ $answer->question->question ?? $answer->question->question_text }}
                        </h6>
                        <div>
                            @if($answer->is_correct)
                                <span class="badge bg-success"><i class="fas fa-check"></i> Correct</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times"></i> Incorrect</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(is_array($answer->question->options))
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Options:</h6>
                            <ul class="list-group">
                                @foreach($answer->question->options as $optIndex => $option)
                                <li class="list-group-item 
                                    {{ $optIndex == $answer->question->correct_option ? 'list-group-item-success' : '' }}
                                    {{ $answer->student_answer == chr(65 + $optIndex) && !$answer->is_correct ? 'list-group-item-danger' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <strong>{{ chr(65 + $optIndex) }}:</strong> {{ $option }}
                                        </span>
                                        <div>
                                            @if($optIndex == $answer->question->correct_option)
                                                <span class="badge bg-success">Correct Answer</span>
                                            @endif
                                            @if($answer->student_answer == chr(65 + $optIndex))
                                                <span class="badge bg-primary">Your Answer</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Correct answers are not shown for this assessment.
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="text-center mt-4">
        @if($studentAssessment->assessment->allow_multiple_attempts)
        <a href="{{ route('student.assessments.show', $studentAssessment->assessment) }}" class="btn btn-lg btn-success">
            <i class="fas fa-redo"></i> Retake Assessment
        </a>
        @endif
        <a href="{{ route('student.results.index') }}" class="btn btn-lg btn-secondary">
            <i class="fas fa-list"></i> View All Results
        </a>
        <a href="{{ route('student.assessments.index') }}" class="btn btn-lg btn-primary">
            <i class="fas fa-clipboard-list"></i> Browse Assessments
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
.stat-box {
    padding: 1rem;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
}

.stat-box h4 {
    color: #333;
    font-weight: bold;
    margin: 0.5rem 0;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.list-group-item {
    border-left: 4px solid transparent;
}

.list-group-item-success {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.list-group-item-danger {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}

.card {
    border-radius: 1rem;
}
</style>
@endsection