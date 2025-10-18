@extends('layouts.admin')

@section('title', 'Question Analysis - ' . ($assessment->name ?? $assessment->title ?? 'Assessment'))

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="mb-0">Question Analysis</h1>
            <p class="text-muted mb-0">
                <strong>{{ $assessment->name ?? $assessment->title ?? 'Untitled Assessment' }}</strong> - 
                <span class="badge bg-{{ $assessment->category === 'Aptitude' ? 'primary' : 'success' }}">
                    {{ $assessment->category }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.assessment-details', $assessment) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assessment
            </a>
        </div>
    </div>

    @if(count($questionStats) > 0)
        <!-- Overall Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-question-circle fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">{{ count($questionStats) }}</h4>
                        <p class="text-muted mb-0">Total Questions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        @php
                            $avgAccuracy = collect($questionStats)->avg('accuracy_rate');
                        @endphp
                        <h4 class="mb-1">{{ number_format($avgAccuracy, 1) }}%</h4>
                        <p class="text-muted mb-0">Avg Accuracy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        @php
                            $totalAttempts = collect($questionStats)->sum('total_attempts');
                        @endphp
                        <h4 class="mb-1">{{ $totalAttempts }}</h4>
                        <p class="text-muted mb-0">Total Responses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-bar fa-2x text-warning mb-2"></i>
                        @php
                            $hardQuestions = collect($questionStats)->where('accuracy_rate', '<', 50)->count();
                        @endphp
                        <h4 class="mb-1">{{ $hardQuestions }}</h4>
                        <p class="text-muted mb-0">Difficult Questions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Details -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Question Performance Details
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="questionAccordion">
                    @foreach($questionStats as $index => $stat)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $index }}" 
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ $index }}">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <div>
                                            <strong>Question {{ $index + 1 }}:</strong> 
                                            {{ Str::limit($stat['question']->question_text, 80) }}
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $stat['accuracy_rate'] >= 70 ? 'success' : 
                                                                     ($stat['accuracy_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                {{ $stat['accuracy_rate'] }}% Accuracy
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" 
                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                 aria-labelledby="heading{{ $index }}" 
                                 data-bs-parent="#questionAccordion">
                                <div class="accordion-body">
                                    <!-- Question Text -->
                                    <div class="mb-4">
                                        <h6 class="text-muted">Question:</h6>
                                        <p class="fw-bold">{{ $stat['question']->question_text }}</p>
                                    </div>

                                    <!-- Options with Statistics -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Options & Response Distribution:</h6>
                                            @php
                                                $options = is_array($stat['question']->options) ? $stat['question']->options : 
                                                          [$stat['question']->option_a, $stat['question']->option_b, 
                                                           $stat['question']->option_c, $stat['question']->option_d];
                                                $optionLetters = ['A', 'B', 'C', 'D'];
                                            @endphp
                                            @foreach($options as $i => $option)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <div>
                                                            <strong>{{ $optionLetters[$i] }}.</strong> {{ $option }}
                                                            @if($stat['question']->correct_answer == $optionLetters[$i] || 
                                                                $stat['question']->correct_answer == $i)
                                                                <span class="badge bg-success ms-2">Correct</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-muted">
                                                            {{ $stat['option_counts'][$i] }} 
                                                            ({{ $stat['option_percentages'][$i] }}%)
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar {{ ($stat['question']->correct_answer == $optionLetters[$i] || 
                                                                                     $stat['question']->correct_answer == $i) ? 'bg-success' : 'bg-secondary' }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $stat['option_percentages'][$i] }}%"
                                                             aria-valuenow="{{ $stat['option_percentages'][$i] }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Statistics Summary -->
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Performance Metrics:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted">Total Attempts:</td>
                                                            <td class="fw-bold">{{ $stat['total_attempts'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Correct Answers:</td>
                                                            <td class="fw-bold text-success">{{ $stat['correct_attempts'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Incorrect Answers:</td>
                                                            <td class="fw-bold text-danger">{{ $stat['total_attempts'] - $stat['correct_attempts'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Accuracy Rate:</td>
                                                            <td>
                                                                <span class="badge bg-{{ $stat['accuracy_rate'] >= 70 ? 'success' : 
                                                                                        ($stat['accuracy_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                                    {{ $stat['accuracy_rate'] }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Difficulty Level:</td>
                                                            <td>
                                                                <span class="badge bg-{{ $stat['question']->difficulty_level == 'easy' ? 'success' : 
                                                                                        ($stat['question']->difficulty_level == 'hard' ? 'danger' : 'warning') }}">
                                                                    {{ ucfirst($stat['question']->difficulty_level) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Performance Indicator -->
                                            @if($stat['accuracy_rate'] < 30)
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Very Difficult!</strong> Most students are struggling with this question.
                                                </div>
                                            @elseif($stat['accuracy_rate'] < 50)
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    <strong>Challenging!</strong> This question needs review.
                                                </div>
                                            @elseif($stat['accuracy_rate'] > 90)
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Too Easy!</strong> Consider increasing difficulty.
                                                </div>
                                            @else
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    <strong>Well Balanced!</strong> Good difficulty level.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5>No Question Data Available</h5>
                <p class="text-muted">No students have attempted this assessment yet.</p>
                <a href="{{ route('admin.reports.assessment-details', $assessment) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Assessment Details
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: #dee2e6;
}
</style>
@endsection
