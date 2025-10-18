@extends('layouts.admin')

@section('title', 'Manage Questions - KIT Training Portal')
@section('page-title', 'Manage Assessment Questions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2" style="color: var(--text-dark); font-weight: 700;">
            <i class="fas fa-question-circle me-2" style="color: var(--primary-red);"></i>
            Manage Questions
        </h1>
        <p class="text-muted mb-0" style="font-size: 1rem;">{{ $assessment->title }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.assessments.add-question', $assessment) }}" class="btn" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);">
            <i class="fas fa-plus me-2"></i>Add New Question
        </a>
        @if($questions->count() > 0)
        <form action="{{ route('admin.assessments.remove-all-questions', $assessment) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn" onclick="return confirm('Are you sure you want to remove ALL questions from this assessment? This action cannot be undone.');" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-trash-alt me-2"></i>Remove All
            </button>
        </form>
        @endif
        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 4px solid #28a745; border-radius: 10px; color: #155724; font-weight: 500;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: none; border-left: 4px solid var(--primary-red); border-radius: 10px; color: #721c24; font-weight: 500;">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-list fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $allQuestions->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Total Questions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #218838 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-check fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $allQuestions->where('difficulty_level', 'easy')->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Easy Questions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-star fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $allQuestions->where('difficulty_level', 'medium')->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Medium Questions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center" style="border: none; border-radius: 15px; overflow: hidden; transition: all 0.3s;">
            <div class="card-body" style="padding: 25px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-fire fa-2x" style="color: white;"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 700; margin-bottom: 5px;">{{ $allQuestions->where('difficulty_level', 'hard')->count() }}</h4>
                <p class="text-muted mb-0" style="font-weight: 500;">Hard Questions</p>
            </div>
        </div>
    </div>
</div>

<!-- Questions List -->
<div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; padding: 20px 25px; border: none;">
        <h5 class="card-title mb-0" style="font-weight: 700; font-size: 1.2rem;">
            <i class="fas fa-list me-2"></i>Questions List
            @if($questions->total() > 0)
                <span class="badge ms-2" style="background: rgba(255,255,255,0.25); padding: 8px 15px; font-size: 0.9rem;">{{ $questions->total() }}</span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        @if($questions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="45%">Question</th>
                            <th width="15%">Difficulty</th>
                            <th width="10%">Marks</th>
                            <th width="10%">Active</th>
                            <th width="15%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $index => $question)
                        <tr>
                            <td><strong>{{ $questions->firstItem() + $index }}</strong></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-dark);">{{ Str::limit($question->question_text ?? $question->question, 100) }}</div>
                                @if(is_array($question->options) && count($question->options) > 0)
                                <small class="text-muted">
                                    Options: {{ count($question->options) }} | 
                                    Correct: Option {{ chr(65 + ($question->correct_option ?? 0)) }}
                                </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $diffGradient = $question->difficulty_level == 'easy' 
                                        ? 'linear-gradient(135deg, #28a745 0%, #218838 100%)' 
                                        : ($question->difficulty_level == 'hard' 
                                            ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' 
                                            : 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)');
                                @endphp
                                <span class="badge" style="background: {{ $diffGradient }}; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    {{ ucfirst($question->difficulty_level ?? 'medium') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                    {{ $question->marks ?? 1 }}
                                </span>
                            </td>
                            <td>
                                @if($question->is_active)
                                    <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        <i class="fas fa-check me-1"></i>Yes
                                    </span>
                                @else
                                    <span class="badge" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        No
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $question->id }}" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.assessments.edit-question', [$assessment, $question]) }}" class="btn btn-sm ms-1" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s; text-decoration: none;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.assessments.delete-question', [$assessment, $question]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm ms-1" onclick="return confirm('Remove this question from assessment?');" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($questions->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $questions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--primary-red);"></i>
                <h5 style="color: var(--text-dark); font-weight: 700; margin-top: 20px;">No Questions Added Yet</h5>
                <p class="text-muted" style="font-size: 1rem;">Start building your assessment by adding questions</p>
                <a href="{{ route('admin.assessments.add-question', $assessment) }}" class="btn mt-3" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                    <i class="fas fa-plus me-2"></i>Add Your First Question
                </a>
            </div>
        @endif
    </div>
</div>

<!-- View Modals -->
@foreach($questions as $question)
<div class="modal fade" id="viewModal{{ $question->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $question->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #DC143C 0%, #B91C1C 100%); border: none; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold" id="viewModalLabel{{ $question->id }}">
                    <i class="fas fa-question-circle me-2"></i>Question Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h6 class="fw-bold mb-2" style="color: #333;">Question:</h6>
                    <p class="mb-0" style="color: #555; font-size: 1.05rem; line-height: 1.6;">{{ $question->question_text ?? $question->question ?? 'No question text' }}</p>
                </div>
                
                @if(is_array($question->options) && count($question->options) > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-3" style="color: #333;">Options:</h6>
                    <div class="list-group">
                        @foreach($question->options as $idx => $option)
                        <div class="list-group-item d-flex justify-content-between align-items-center {{ $idx == ($question->correct_option ?? 0) ? 'list-group-item-success' : '' }}" style="border-radius: 8px; margin-bottom: 8px; padding: 12px 15px;">
                            <span><strong>{{ chr(65 + $idx) }}:</strong> {{ $option }}</span>
                            @if($idx == ($question->correct_option ?? 0))
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Correct</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-md-4 mb-2">
                        <strong style="color: #333;">Difficulty:</strong><br>
                        <span class="badge mt-1 bg-{{ $question->difficulty_level == 'easy' ? 'success' : ($question->difficulty_level == 'hard' ? 'danger' : 'warning') }}" style="padding: 6px 12px;">
                            {{ ucfirst($question->difficulty_level ?? 'medium') }}
                        </span>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong style="color: #333;">Marks:</strong><br>
                        <span class="badge mt-1 bg-info" style="padding: 6px 12px;">{{ $question->marks ?? 1 }} Mark{{ ($question->marks ?? 1) != 1 ? 's' : '' }}</span>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong style="color: #333;">Status:</strong><br>
                        <span class="badge mt-1 bg-{{ $question->is_active ? 'success' : 'secondary' }}" style="padding: 6px 12px;">
                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; background-color: #f8f9fa; border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <a href="{{ route('admin.assessments.edit-question', [$assessment, $question]) }}" class="btn btn-danger">
                    <i class="fas fa-edit me-1"></i>Edit Question
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

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

.card {
    transition: all 0.3s ease;
}

/* Only apply hover effect to stats cards */
.row.g-3 .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}

.table th {
    border-top: none;
    font-weight: 700;
    color: var(--text-dark);
    background-color: #f8f9fa;
    padding: 15px 12px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(220, 20, 60, 0.05) !important;
}

.btn-group-actions {
    display: flex;
    gap: 5px;
    justify-content: flex-end;
}

.btn-group-actions .btn:hover,
.btn-group-actions button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Pagination styling */
.pagination {
    margin: 0;
}

.pagination .page-link {
    color: var(--primary-red);
    border-radius: 8px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
    transition: all 0.3s;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: white;
    border-color: var(--primary-red);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    border-color: var(--primary-red);
}

/* Modal styling */
.modal-content {
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-backdrop.show {
    opacity: 0.5;
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
