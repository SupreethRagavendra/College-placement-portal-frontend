@extends('layouts.admin')

@section('title', 'Edit Question - KIT Training Portal')
@section('page-title', 'Edit Question')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-edit me-2" style="color: #ffc107;"></i>
            Edit Question
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">{{ $assessment->title }}</p>
    </div>
    <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
        <i class="fas fa-arrow-left me-2"></i>Back to Questions
    </a>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 4px solid #28a745; border-radius: 10px; color: #155724; font-weight: 500;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: none; border-left: 4px solid var(--primary-red); border-radius: 10px; color: #721c24; font-weight: 500;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-edit me-2"></i>Edit Question Details
                </h5>
            </div>
            <div class="card-body" style="padding: 30px;">
                <form action="{{ route('admin.assessments.update-question', [$assessment, $question]) }}" method="POST" id="editQuestionForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="question_text" class="form-label fw-bold" style="color: var(--text-dark);">
                            Question <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="4" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;" placeholder="Enter the question here...">{{ old('question_text', $question->question_text) }}</textarea>
                        <small class="text-muted">Enter a clear and concise question</small>
                    </div>

                    <h6 class="fw-bold mb-3" style="color: var(--text-dark);">Answer Options</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="option_a" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Option A <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="option_a" name="option_a" value="{{ old('option_a', $question->options[0] ?? '') }}" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;" placeholder="First option">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="option_b" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Option B <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="option_b" name="option_b" value="{{ old('option_b', $question->options[1] ?? '') }}" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;" placeholder="Second option">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="option_c" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Option C <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="option_c" name="option_c" value="{{ old('option_c', $question->options[2] ?? '') }}" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;" placeholder="Third option">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="option_d" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Option D <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="option_d" name="option_d" value="{{ old('option_d', $question->options[3] ?? '') }}" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;" placeholder="Fourth option">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3" style="color: var(--text-dark);">Question Settings</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="correct_answer" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Correct Answer <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="correct_answer" name="correct_answer" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;">
                                <option value="">Select...</option>
                                <option value="A" {{ old('correct_answer', chr(65 + $question->correct_option)) == 'A' ? 'selected' : '' }}>Option A</option>
                                <option value="B" {{ old('correct_answer', chr(65 + $question->correct_option)) == 'B' ? 'selected' : '' }}>Option B</option>
                                <option value="C" {{ old('correct_answer', chr(65 + $question->correct_option)) == 'C' ? 'selected' : '' }}>Option C</option>
                                <option value="D" {{ old('correct_answer', chr(65 + $question->correct_option)) == 'D' ? 'selected' : '' }}>Option D</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="marks" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Marks <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="marks" name="marks" value="{{ old('marks', $question->marks ?? 1) }}" min="1" max="100" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="difficulty_level" class="form-label fw-semibold" style="color: var(--text-dark);">
                                Difficulty Level <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="difficulty_level" name="difficulty_level" required style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 12px; transition: all 0.3s;">
                                <option value="easy" {{ old('difficulty_level', $question->difficulty_level) == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty_level', $question->difficulty_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty_level', $question->difficulty_level) == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e0e0e0;">
                        <a href="{{ route('admin.assessments.questions', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 10px 30px; border-radius: 8px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);">
                            <i class="fas fa-save me-2"></i>Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Column -->
    <div class="col-lg-4">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; position: sticky; top: 20px;">
            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 20px 25px; border: none;">
                <h5 class="mb-0" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-eye me-2"></i>Live Preview
                </h5>
            </div>
            <div class="card-body" style="padding: 25px;">
                <div class="mb-3">
                    <h6 class="fw-bold" style="color: var(--text-dark);">Question:</h6>
                    <p id="preview-question" class="mb-3" style="color: #555; font-size: 1rem; line-height: 1.6;">{{ $question->question_text }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold" style="color: var(--text-dark);">Options:</h6>
                    <div class="list-group">
                        <div class="list-group-item" style="border-radius: 8px; margin-bottom: 8px; padding: 10px;">
                            <strong>A:</strong> <span id="preview-option-a">{{ $question->options[0] ?? 'Option A' }}</span>
                        </div>
                        <div class="list-group-item" style="border-radius: 8px; margin-bottom: 8px; padding: 10px;">
                            <strong>B:</strong> <span id="preview-option-b">{{ $question->options[1] ?? 'Option B' }}</span>
                        </div>
                        <div class="list-group-item" style="border-radius: 8px; margin-bottom: 8px; padding: 10px;">
                            <strong>C:</strong> <span id="preview-option-c">{{ $question->options[2] ?? 'Option C' }}</span>
                        </div>
                        <div class="list-group-item" style="border-radius: 8px; margin-bottom: 8px; padding: 10px;">
                            <strong>D:</strong> <span id="preview-option-d">{{ $question->options[3] ?? 'Option D' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Marks</small>
                        <span class="badge bg-info" id="preview-marks">{{ $question->marks ?? 1 }} mark{{ ($question->marks ?? 1) != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Difficulty</small>
                        <span class="badge bg-warning" id="preview-difficulty">{{ ucfirst($question->difficulty_level ?? 'Medium') }}</span>
                    </div>
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

.form-control:focus,
.form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
}

.card {
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const questionInput = document.getElementById('question_text');
    const optionAInput = document.getElementById('option_a');
    const optionBInput = document.getElementById('option_b');
    const optionCInput = document.getElementById('option_c');
    const optionDInput = document.getElementById('option_d');
    const marksInput = document.getElementById('marks');
    const difficultyInput = document.getElementById('difficulty_level');

    // Update preview function
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
            difficultyBadge.className = 'badge bg-success';
        } else if (difficulty == 'hard') {
            difficultyBadge.className = 'badge bg-danger';
        } else {
            difficultyBadge.className = 'badge bg-warning';
        }
    }

    // Add event listeners
    questionInput.addEventListener('input', updatePreview);
    optionAInput.addEventListener('input', updatePreview);
    optionBInput.addEventListener('input', updatePreview);
    optionCInput.addEventListener('input', updatePreview);
    optionDInput.addEventListener('input', updatePreview);
    marksInput.addEventListener('input', updatePreview);
    difficultyInput.addEventListener('change', updatePreview);

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
