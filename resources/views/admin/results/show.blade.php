@extends('layouts.admin')

@section('title', 'Result Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Result Details</h1>
        <p class="text-muted mb-0">{{ $studentAssessment->assessment->title }}</p>
    </div>
    <a href="{{ route('admin.results.index', $studentAssessment->assessment) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Results
    </a>
</div>

<!-- Student & Assessment Info -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Student Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $studentAssessment->student->name }}</p>
                <p><strong>Email:</strong> {{ $studentAssessment->student->email }}</p>
                <p class="mb-0"><strong>Register No:</strong> {{ $studentAssessment->student->register_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-{{ $studentAssessment->pass_status == 'pass' ? 'success' : 'danger' }} text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p><strong>Score:</strong> {{ $studentAssessment->obtained_marks }}/{{ $studentAssessment->total_marks }}</p>
                        <p><strong>Percentage:</strong> {{ number_format($studentAssessment->percentage, 2) }}%</p>
                    </div>
                    <div class="col-6">
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $studentAssessment->pass_status == 'pass' ? 'success' : 'danger' }}">
                                {{ ucfirst($studentAssessment->pass_status) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Time Taken:</strong> {{ $studentAssessment->formatted_time_taken }}</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 30px;">
                    <div class="progress-bar bg-{{ $studentAssessment->percentage >= 50 ? 'success' : 'danger' }}" 
                         role="progressbar" 
                         style="width: {{ $studentAssessment->percentage }}%"
                         aria-valuenow="{{ $studentAssessment->percentage }}">
                        {{ number_format($studentAssessment->percentage, 2) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Answers -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-alt"></i> Detailed Answers</h5>
    </div>
    <div class="card-body">
        @forelse($studentAssessment->studentAnswers as $index => $answer)
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
                        <span class="badge bg-primary">{{ $answer->marks_obtained }}/{{ $answer->question->marks ?? 1 }} marks</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(is_array($answer->question->options) && count($answer->question->options) > 0)
                <div class="row">
                    <div class="col-md-6">
                        <h6>Options:</h6>
                        <ul class="list-group">
                            @foreach($answer->question->options as $optIndex => $option)
                            <li class="list-group-item 
                                {{ $optIndex == $answer->question->correct_option ? 'list-group-item-success' : '' }}
                                {{ $answer->student_answer == chr(65 + $optIndex) && $optIndex != $answer->question->correct_option ? 'list-group-item-danger' : '' }}">
                                <strong>{{ chr(65 + $optIndex) }}:</strong> {{ $option }}
                                @if($optIndex == $answer->question->correct_option)
                                    <span class="badge bg-success float-end">Correct Answer</span>
                                @endif
                                @if($answer->student_answer == chr(65 + $optIndex))
                                    <span class="badge bg-primary float-end me-2">Student's Answer</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Answer Summary:</h6>
                        <p><strong>Student's Answer:</strong> 
                            <span class="badge bg-{{ $answer->is_correct ? 'success' : 'danger' }}">
                                Option {{ $answer->student_answer }}
                            </span>
                        </p>
                        <p><strong>Correct Answer:</strong> 
                            <span class="badge bg-success">
                                Option {{ chr(65 + $answer->question->correct_option) }}
                            </span>
                        </p>
                        <p><strong>Difficulty:</strong> 
                            <span class="badge bg-{{ $answer->question->difficulty_level == 'easy' ? 'success' : ($answer->question->difficulty_level == 'hard' ? 'danger' : 'warning') }}">
                                {{ ucfirst($answer->question->difficulty_level) }}
                            </span>
                        </p>
                        @if($answer->time_spent)
                        <p class="mb-0"><strong>Time Spent:</strong> {{ $answer->formatted_time_spent }}</p>
                        @endif
                    </div>
                </div>
                @else
                <p><strong>Student's Answer:</strong> {{ $answer->student_answer ?? 'No answer provided' }}</p>
                <p><strong>Correct Answer:</strong> {{ $answer->question->correct_answer }}</p>
                @endif
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No answers recorded for this assessment.
        </div>
        @endforelse
    </div>
</div>

<!-- Analysis Section -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Correct Answers</h6>
            </div>
            <div class="card-body text-center">
                <h2 class="text-success">{{ $studentAssessment->studentAnswers->where('is_correct', true)->count() }}</h2>
                <p class="text-muted mb-0">out of {{ $studentAssessment->studentAnswers->count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Incorrect Answers</h6>
            </div>
            <div class="card-body text-center">
                <h2 class="text-danger">{{ $studentAssessment->studentAnswers->where('is_correct', false)->count() }}</h2>
                <p class="text-muted mb-0">out of {{ $studentAssessment->studentAnswers->count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Accuracy Rate</h6>
            </div>
            <div class="card-body text-center">
                <h2 class="text-info">
                    {{ $studentAssessment->studentAnswers->count() > 0 ? number_format(($studentAssessment->studentAnswers->where('is_correct', true)->count() / $studentAssessment->studentAnswers->count()) * 100, 2) : 0 }}%
                </h2>
                <p class="text-muted mb-0">overall accuracy</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.list-group-item {
    border-left: 3px solid transparent;
}

.list-group-item-success {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.list-group-item-danger {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.progress {
    font-size: 1rem;
    font-weight: bold;
}
</style>
@endsection