@extends('layouts.admin')

@section('title', 'Add Question - KIT Training Portal')
@section('page-title', 'Add New Question')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-plus-circle me-2" style="color: var(--primary-red);"></i>
            Add New Question
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">{{ $assessment->title }}</p>
    </div>
    <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
        <i class="fas fa-arrow-left me-2"></i>Back to Questions
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: none; border-left: 4px solid var(--primary-red); border-radius: 10px; color: #721c24; font-weight: 500;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-plus-circle me-2"></i>Create New Question
                </h5>
            </div>
            <div class="card-body" style="padding: 30px;">
                <form action="{{ route('admin.assessments.store-question', $assessment) }}" method="POST" id="questionForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="question_text" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Question <span style="color: var(--primary-red);">*</span></label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">{{ old('question_text') }}</textarea>
                        <small class="text-muted">Enter the question text clearly and concisely</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="option_a" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Option A <span style="color: var(--primary-red);">*</span></label>
                            <input type="text" class="form-control" id="option_a" name="option_a" value="{{ old('option_a') }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="option_b" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Option B <span style="color: var(--primary-red);">*</span></label>
                            <input type="text" class="form-control" id="option_b" name="option_b" value="{{ old('option_b') }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="option_c" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Option C <span style="color: var(--primary-red);">*</span></label>
                            <input type="text" class="form-control" id="option_c" name="option_c" value="{{ old('option_c') }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="option_d" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Option D <span style="color: var(--primary-red);">*</span></label>
                            <input type="text" class="form-control" id="option_d" name="option_d" value="{{ old('option_d') }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="correct_answer" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Correct Answer <span style="color: var(--primary-red);">*</span></label>
                            <select class="form-select" id="correct_answer" name="correct_answer" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                <option value="">Select correct answer...</option>
                                <option value="A" {{ old('correct_answer') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('correct_answer') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ old('correct_answer') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ old('correct_answer') == 'D' ? 'selected' : '' }}>D</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="marks" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Marks <span style="color: var(--primary-red);">*</span></label>
                            <input type="number" class="form-control" id="marks" name="marks" value="{{ old('marks', 1) }}" min="1" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="difficulty_level" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Difficulty Level <span style="color: var(--primary-red);">*</span></label>
                            <select class="form-select" id="difficulty_level" name="difficulty_level" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                <option value="easy" {{ old('difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty_level', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between" style="margin-top: 30px;">
                        <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn btn-lg" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; transition: all 0.3s;">
                            <i class="fas fa-save me-2"></i>Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Preview Card -->
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; position: sticky; top: 20px;">
            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 15px 20px; border: none;">
                <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.1rem;">
                    <i class="fas fa-eye me-2"></i>Live Preview
                </h5>
            </div>
            <div class="card-body" style="padding: 20px;">
                <p id="preview-question" class="fw-bold" style="color: var(--text-dark); margin-bottom: 15px;">Your question will appear here...</p>
                <div id="preview-options" class="ms-2">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label"><strong>A.</strong> <span id="preview-option-a">Option A</span></label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label"><strong>B.</strong> <span id="preview-option-b">Option B</span></label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label"><strong>C.</strong> <span id="preview-option-c">Option C</span></label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label"><strong>D.</strong> <span id="preview-option-d">Option D</span></label>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <span class="badge" id="preview-marks" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 8px 14px; border-radius: 8px; font-weight: 500;">1 mark</span>
                    <span class="badge" id="preview-difficulty" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; padding: 8px 14px; border-radius: 8px; font-weight: 500;">Medium</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

.form-control:focus, .form-select:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
}

button[type="submit"]:hover,
a[href*="questions"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.3);
}

.card {
    transition: all 0.3s ease;
}

.form-check-input:checked {
    background-color: var(--primary-red);
    border-color: var(--primary-red);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    const questionInput = document.getElementById('question_text');
    const optionAInput = document.getElementById('option_a');
    const optionBInput = document.getElementById('option_b');
    const optionCInput = document.getElementById('option_c');
    const optionDInput = document.getElementById('option_d');
    const marksInput = document.getElementById('marks');
    const difficultyInput = document.getElementById('difficulty_level');

    function updatePreview() {
        document.getElementById('preview-question').textContent = questionInput.value || 'Your question will appear here...';
        document.getElementById('preview-option-a').textContent = optionAInput.value || 'Option A';
        document.getElementById('preview-option-b').textContent = optionBInput.value || 'Option B';
        document.getElementById('preview-option-c').textContent = optionCInput.value || 'Option C';
        document.getElementById('preview-option-d').textContent = optionDInput.value || 'Option D';
        
        const marks = marksInput.value || 1;
        document.getElementById('preview-marks').textContent = marks + (marks == 1 ? ' mark' : ' marks');
        
        const difficulty = difficultyInput.value || 'medium';
        const difficultyBadge = document.getElementById('preview-difficulty');
        difficultyBadge.textContent = difficulty.charAt(0).toUpperCase() + difficulty.slice(1);
        
        // Update difficulty badge color
        if (difficulty == 'easy') {
            difficultyBadge.style.background = 'linear-gradient(135deg, #28a745 0%, #218838 100%)';
        } else if (difficulty == 'hard') {
            difficultyBadge.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
        } else {
            difficultyBadge.style.background = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
        }
    }

    questionInput.addEventListener('input', updatePreview);
    optionAInput.addEventListener('input', updatePreview);
    optionBInput.addEventListener('input', updatePreview);
    optionCInput.addEventListener('input', updatePreview);
    optionDInput.addEventListener('input', updatePreview);
    marksInput.addEventListener('input', updatePreview);
    difficultyInput.addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        });
    }, 5000);
});
</script>
@endpush
