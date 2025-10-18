@extends('layouts.admin')

@section('title', 'Edit Assessment - KIT Training Portal')
@section('page-title', 'Edit Assessment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-edit me-2" style="color: var(--primary-red);"></i>
            Edit Assessment
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">Update assessment details and settings</p>
    </div>
    <a href="{{ route('admin.assessments.index') }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
        <i class="fas fa-arrow-left me-2"></i>Back to Assessments
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

<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-clipboard-list me-2"></i>Assessment Information
        </h5>
    </div>
    <div class="card-body" style="padding: 30px;">
        <form action="{{ route('admin.assessments.update', $assessment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Title <span style="color: var(--primary-red);">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $assessment->title) }}" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Category <span style="color: var(--primary-red);">*</span></label>
                        <select class="form-control" id="category" name="category" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                            <option value="">Select Category</option>
                            <option value="Aptitude" {{ old('category', $assessment->category) == 'Aptitude' ? 'selected' : '' }}>Aptitude</option>
                            <option value="Technical" {{ old('category', $assessment->category) == 'Technical' ? 'selected' : '' }}>Technical</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">{{ old('description', $assessment->description) }}</textarea>
                <small class="text-muted">Provide a brief description of the assessment</small>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="duration" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Duration (minutes) <span style="color: var(--primary-red);">*</span></label>
                        <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration', $assessment->duration ?? $assessment->total_time) }}" min="1" max="300" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="total_marks" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Total Marks <span style="color: var(--primary-red);">*</span></label>
                        <input type="number" class="form-control" id="total_marks" name="total_marks" value="{{ old('total_marks', $assessment->total_marks) }}" min="1" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="pass_percentage" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Pass Percentage <span style="color: var(--primary-red);">*</span></label>
                        <input type="number" class="form-control" id="pass_percentage" name="pass_percentage" value="{{ old('pass_percentage', $assessment->pass_percentage) }}" min="1" max="100" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="difficulty_level" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Difficulty Level <span style="color: var(--primary-red);">*</span></label>
                        <select class="form-control" id="difficulty_level" name="difficulty_level" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                            <option value="">Select Difficulty</option>
                            <option value="easy" {{ old('difficulty_level', $assessment->difficulty_level) == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty_level', $assessment->difficulty_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty_level', $assessment->difficulty_level) == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label" style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Status <span style="color: var(--primary-red);">*</span></label>
                        <select class="form-control" id="status" name="status" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                            <option value="draft" {{ old('status', $assessment->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $assessment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $assessment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: #f8f9fa;">
                <div class="card-body" style="padding: 20px;">
                    <h6 style="color: var(--text-dark); font-weight: 700; margin-bottom: 15px;">
                        <i class="fas fa-cog me-2" style="color: var(--primary-red);"></i>Assessment Options
                    </h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_multiple_attempts" name="allow_multiple_attempts" {{ old('allow_multiple_attempts', $assessment->allow_multiple_attempts) ? 'checked' : '' }} style="width: 18px; height: 18px; border-radius: 4px;">
                            <label class="form-check-label" for="allow_multiple_attempts" style="margin-left: 8px; font-weight: 500;">
                                Allow Multiple Attempts
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_results_immediately" name="show_results_immediately" {{ old('show_results_immediately', $assessment->show_results_immediately) ? 'checked' : '' }} style="width: 18px; height: 18px; border-radius: 4px;">
                            <label class="form-check-label" for="show_results_immediately" style="margin-left: 8px; font-weight: 500;">
                                Show Results Immediately After Submission
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_correct_answers" name="show_correct_answers" {{ old('show_correct_answers', $assessment->show_correct_answers) ? 'checked' : '' }} style="width: 18px; height: 18px; border-radius: 4px;">
                            <label class="form-check-label" for="show_correct_answers" style="margin-left: 8px; font-weight: 500;">
                                Show Correct Answers in Results
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between" style="margin-top: 30px;">
                <a href="{{ route('admin.assessments.index') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; transition: all 0.3s;">
                    <i class="fas fa-save me-2"></i>Update Assessment
                </button>
            </div>
        </form>
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

.form-check-input:checked {
    background-color: var(--primary-red);
    border-color: var(--primary-red);
}

.form-check-input:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.15);
}

button[type="submit"]:hover,
a[href*="assessments"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.3);
}

.card {
    transition: all 0.3s ease;
}
</style>

<script>
// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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
