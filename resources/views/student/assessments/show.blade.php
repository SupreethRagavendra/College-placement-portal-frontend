@extends('layouts.student')

@section('title', 'Assessment Details - KIT Training Portal')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('student.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none;">
                    <i class="fas fa-arrow-left me-2"></i>Back to Assessments
                </a>
            </div>

            <!-- Assessment Header Card -->
            <div class="card mb-4 shadow-sm" style="border: none; border-radius: 20px; overflow: hidden;">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);">
                    <h2 class="mb-0 fw-bold"><i class="fas fa-clipboard-check me-2"></i>{{ $assessment->title }}</h2>
                </div>
                <div class="card-body p-4">
                    @if($assessment->description)
                    <div class="alert mb-4" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border: none; border-left: 4px solid #17a2b8; border-radius: 12px; padding: 16px;">
                        <i class="fas fa-info-circle me-2" style="color: #17a2b8;"></i>
                        <strong>{{ $assessment->description }}</strong>
                    </div>
                    @endif

                    <h5 class="mb-4 fw-bold" style="color: var(--text-dark);">
                        <i class="fas fa-list-check me-2" style="color: var(--primary-red);"></i>Assessment Details
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid var(--primary-red);">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tag" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Category</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->category }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid #ffc107;">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clock" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Duration</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->duration }} minutes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid #17a2b8;">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-question-circle" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Total Questions</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->questions->count() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid #28a745;">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-star" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Total Marks</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->total_marks }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid var(--primary-red);">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-chart-line" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Pass Percentage</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ $assessment->pass_percentage }}%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box" style="display: flex; align-items: center; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid #ffc107;">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, {{ $assessment->difficulty_level == 'easy' ? '#28a745, #218838' : ($assessment->difficulty_level == 'hard' ? '#dc3545, #c82333' : '#ffc107, #e0a800') }}); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-signal" style="color: white; font-size: 1.3rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">Difficulty Level</small>
                                    <p class="mb-0 fw-bold" style="color: var(--text-dark); font-size: 1.1rem;">{{ ucfirst($assessment->difficulty_level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-4 fw-bold" style="color: var(--text-dark);">
                        <i class="fas fa-exclamation-triangle me-2" style="color: var(--primary-red);"></i>Important Instructions
                    </h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-check-circle me-2" style="color: #28a745;"></i> 
                            <strong>Read each question carefully before answering</strong>
                        </li>
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-clock me-2" style="color: #ffc107;"></i> 
                            You have <strong>{{ $assessment->duration }} minutes</strong> to complete the assessment
                        </li>
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-save me-2" style="color: #17a2b8;"></i> 
                            Your progress will be <strong>auto-saved</strong> periodically
                        </li>
                        @if($assessment->allow_multiple_attempts)
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-redo me-2" style="color: #007bff;"></i> 
                            <strong>Multiple attempts</strong> are allowed for this assessment
                        </li>
                        @else
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-ban me-2" style="color: var(--primary-red);"></i> 
                            Only <strong>ONE attempt</strong> is allowed for this assessment
                        </li>
                        @endif
                        @if($assessment->show_results_immediately)
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-eye me-2" style="color: #28a745;"></i> 
                            Results will be shown <strong>immediately</strong> after submission
                        </li>
                        @endif
                        @if($assessment->show_correct_answers)
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-lightbulb me-2" style="color: #ffc107;"></i> 
                            <strong>Correct answers</strong> will be shown in the results
                        </li>
                        @endif
                        <li class="list-group-item" style="border: none; border-left: 3px solid transparent; padding: 16px; margin-bottom: 8px; border-radius: 10px; background: #f8f9fa; transition: all 0.3s;">
                            <i class="fas fa-wifi me-2" style="color: var(--primary-red);"></i> 
                            Make sure you have a <strong>stable internet connection</strong>
                        </li>
                    </ul>

                    <!-- Start Button -->
                    <form action="{{ route('student.assessments.start', $assessment) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-lg" onclick="return confirm('Are you ready to start the assessment? The timer will begin immediately.')" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 18px; border-radius: 12px; font-weight: 700; font-size: 1.2rem; box-shadow: 0 6px 20px rgba(220, 20, 60, 0.3); transition: all 0.3s;">
                                <i class="fas fa-play-circle me-2"></i>Start Assessment Now
                            </button>
                            <a href="{{ route('student.assessments.index') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 18px; border-radius: 12px; font-weight: 700; font-size: 1.1rem; text-decoration: none; transition: all 0.3s;">
                                <i class="fas fa-arrow-left me-2"></i>Back to Assessments
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.list-group-item:hover {
    background: white !important;
    border-left-color: var(--primary-red) !important;
    transform: translateX(5px);
}

.btn-lg:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important;
}

.card {
    transition: all 0.3s ease;
}
</style>
@endsection
