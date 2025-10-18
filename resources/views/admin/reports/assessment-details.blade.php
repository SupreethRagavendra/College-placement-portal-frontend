@extends('layouts.admin')

@section('title', 'Assessment Details - ' . ($assessment->name ?? $assessment->title ?? 'Assessment'))

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="mb-0">{{ $assessment->name ?? $assessment->title ?? 'Untitled Assessment' }}</h1>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $assessment->category === 'Aptitude' ? 'primary' : 'success' }}">
                    {{ $assessment->category }}
                </span>
                <span class="ms-2">Detailed performance report</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export', ['type' => 'assessment', 'assessment_id' => $assessment->id]) }}" 
               class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
            <a href="{{ route('admin.reports.question-analysis', $assessment) }}" 
               class="btn btn-primary">
                <i class="fas fa-chart-bar me-2"></i>Question Analysis
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">{{ $stats['total_attempts'] }}</h4>
                    <p class="text-muted mb-0">Total Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">{{ number_format($stats['avg_score'], 1) }}%</h4>
                    <p class="text-muted mb-0">Average Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-trophy fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">{{ number_format($stats['pass_rate'], 1) }}%</h4>
                    <p class="text-muted mb-0">Pass Rate</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">{{ round($stats['avg_time'] / 60, 1) }}m</h4>
                    <p class="text-muted mb-0">Avg Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Analysis Section -->
    @if(isset($categoryAnalysis) && count($categoryAnalysis) > 0)
        @php
            $strugglingCategories = collect($categoryAnalysis)->where('is_struggling', true);
        @endphp
        
        @if($strugglingCategories->count() > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">⚠️ Needs Attention! Students are struggling with {{ $strugglingCategories->count() }} {{ Str::plural('category', $strugglingCategories->count()) }}.</h5>
                        <p class="mb-0">Categories with accuracy below 60% require additional support or review.</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Category Analysis
                    <small class="text-muted ms-2">Performance breakdown by question category</small>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($categoryAnalysis as $category)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 {{ $category['is_struggling'] ? 'border-danger' : ($category['accuracy'] >= 80 ? 'border-success' : 'border-warning') }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $category['category'] }}</h6>
                                        @if($category['is_struggling'])
                                            <span class="badge bg-danger">Struggling</span>
                                        @elseif($category['accuracy'] >= 80)
                                            <span class="badge bg-success">Strong</span>
                                        @else
                                            <span class="badge bg-warning">Moderate</span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Accuracy</small>
                                            <small class="fw-bold {{ $category['accuracy'] < 60 ? 'text-danger' : ($category['accuracy'] >= 80 ? 'text-success' : 'text-warning') }}">
                                                {{ $category['accuracy'] }}%
                                            </small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar {{ $category['accuracy'] < 60 ? 'bg-danger' : ($category['accuracy'] >= 80 ? 'bg-success' : 'bg-warning') }}" 
                                                 style="width: {{ $category['accuracy'] }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-muted small">Questions</div>
                                            <div class="fw-bold">{{ $category['question_count'] }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Attempts</div>
                                            <div class="fw-bold">{{ $category['total_attempts'] }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Correct</div>
                                            <div class="fw-bold text-success">{{ $category['correct_answers'] }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($category['is_struggling'])
                                        <div class="mt-3 pt-3 border-top">
                                            <small class="text-danger">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Consider reviewing questions in this category or providing additional study materials.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(count($categoryAnalysis) > 0)
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">📊 Category Performance Summary</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Strongest Category:</small>
                                @php
                                    $strongest = collect($categoryAnalysis)->sortByDesc('accuracy')->first();
                                @endphp
                                <div class="fw-bold text-success">
                                    {{ $strongest['category'] }} ({{ $strongest['accuracy'] }}%)
                                </div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Weakest Category:</small>
                                @php
                                    $weakest = collect($categoryAnalysis)->sortBy('accuracy')->first();
                                @endphp
                                <div class="fw-bold text-danger">
                                    {{ $weakest['category'] }} ({{ $weakest['accuracy'] }}%)
                                </div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Overall Average:</small>
                                @php
                                    $overallAvg = collect($categoryAnalysis)->avg('accuracy');
                                @endphp
                                <div class="fw-bold {{ $overallAvg < 60 ? 'text-danger' : ($overallAvg >= 80 ? 'text-success' : 'text-warning') }}">
                                    {{ round($overallAvg, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.assessment-details', $assessment) }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Student</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name or Email">
                </div>
                <div class="col-md-3">
                    <label for="grade" class="form-label">Filter by Grade</label>
                    <select class="form-select" id="grade" name="grade">
                        <option value="">All Grades</option>
                        <option value="A" {{ request('grade') == 'A' ? 'selected' : '' }}>A (90%+)</option>
                        <option value="B" {{ request('grade') == 'B' ? 'selected' : '' }}>B (80-89%)</option>
                        <option value="C" {{ request('grade') == 'C' ? 'selected' : '' }}>C (70-79%)</option>
                        <option value="D" {{ request('grade') == 'D' ? 'selected' : '' }}>D (60-69%)</option>
                        <option value="F" {{ request('grade') == 'F' ? 'selected' : '' }}>F (<60%)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.assessment-details', $assessment) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Student Results
            </h5>
        </div>
        <div class="card-body">
            @if($results->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th style="width: 120px">Score</th>
                                <th style="width: 100px">Grade</th>
                                <th style="width: 120px">Percentage</th>
                                <th style="width: 100px">Time Taken</th>
                                <th style="width: 150px">Submitted At</th>
                                <th style="width: 100px" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                @php
                                    $percentage = $result->total_questions > 0 ? round(($result->score / $result->total_questions) * 100, 2) : 0;
                                    $grade = $percentage >= 90 ? 'A' : 
                                            ($percentage >= 80 ? 'B' : 
                                            ($percentage >= 70 ? 'C' : 
                                            ($percentage >= 60 ? 'D' : 'F')));
                                @endphp
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $result->student->name }}</h6>
                                            <small class="text-muted">{{ $result->student->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $result->score }}/{{ $result->total_questions }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $grade == 'A' ? 'success' : 
                                                                ($grade == 'B' ? 'info' : 
                                                                ($grade == 'C' ? 'primary' : 
                                                                ($grade == 'D' ? 'warning' : 'danger'))) }} fs-6">
                                            {{ $grade }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar bg-{{ $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ min($percentage, 100) }}%"></div>
                                            </div>
                                            <span class="fw-bold">{{ $percentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ round($result->time_taken / 60, 1) }}m</span>
                                    </td>
                                    <td>
                                        <small>{{ $result->submitted_at ? $result->submitted_at->format('d-M-Y H:i') : 'N/A' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewDetails({{ $result->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $results->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <h5>No Results Found</h5>
                    <p class="text-muted">No students have attempted this assessment yet or your filters returned no results.</p>
                    <a href="{{ route('admin.reports.assessment-details', $assessment) }}" class="btn btn-outline-primary">
                        <i class="fas fa-redo me-2"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Result Details Modal -->
<div class="modal fade" id="resultDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check me-2"></i>Assessment Result Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resultDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading result details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Version: {{ time() }} - Force cache refresh
function viewDetails(resultId) {
    // Show modal with loading state
    const modal = new bootstrap.Modal(document.getElementById('resultDetailsModal'));
    modal.show();
    
    // Fetch result details via AJAX
    fetch(`/admin/reports/result/${resultId}?t=${Date.now()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Build HTML for result details
            let questionsHtml = '';
            data.question_breakdown.forEach((q, index) => {
                let statusClass, statusIcon, statusText;
                
                // Check if answer is correct, unanswered, or wrong
                if (q.is_correct) {
                    statusClass = 'success';
                    statusIcon = 'check-circle';
                    statusText = 'Correct';
                } else if (q.user_answer_letter === null && q.user_answer_index === null) {
                    statusClass = 'secondary';
                    statusIcon = 'minus-circle';
                    statusText = 'Unanswered';
                } else {
                    statusClass = 'danger';
                    statusIcon = 'times-circle';
                    statusText = 'Wrong';
                }
                
                // Format user answer with letter prefix if available
                let userAnswerDisplay = q.user_answer_text;
                if (q.user_answer_letter && q.user_answer_text !== 'Not Answered') {
                    userAnswerDisplay = `${q.user_answer_letter}. ${q.user_answer_text}`;
                }
                
                // Format correct answer with letter prefix
                let correctAnswerDisplay = q.correct_answer_text;
                if (q.correct_answer_letter && q.correct_answer_text !== 'N/A') {
                    correctAnswerDisplay = `${q.correct_answer_letter}. ${q.correct_answer_text}`;
                }
                
                questionsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td class="text-wrap">
                            <small>${q.question.question_text || 'N/A'}</small>
                        </td>
                        <td>
                            <small class="text-${q.user_answer_letter || q.user_answer_index !== null ? 'primary' : 'muted'}">
                                ${userAnswerDisplay}
                            </small>
                        </td>
                        <td>
                            <small class="text-success fw-bold">
                                ${correctAnswerDisplay}
                            </small>
                        </td>
                        <td class="text-center">
                            <i class="fas fa-${statusIcon} text-${statusClass}" title="${statusText}"></i>
                        </td>
                    </tr>
                `;
            });
            
            const passClass = data.pass_status === 'Passed' ? 'success' : 'danger';
            const gradeClass = data.grade === 'A' ? 'success' : 
                              (data.grade === 'B' ? 'info' : 
                              (data.grade === 'C' ? 'primary' : 
                              (data.grade === 'D' ? 'warning' : 'danger')));
            
            const html = `
                <!-- Student & Assessment Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Student Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Name:</strong> ${data.student_name}</p>
                                <p class="mb-2"><strong>Email:</strong> ${data.student_email}</p>
                                <p class="mb-0"><strong>Submitted:</strong> ${data.submitted_at}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-clipboard me-2"></i>Assessment Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Assessment:</strong> ${data.assessment_name}</p>
                                <p class="mb-2"><strong>Category:</strong> ${data.category}</p>
                                <p class="mb-0"><strong>Time Taken:</strong> ${data.time_taken}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Performance Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h2 class="text-primary mb-0">${data.score}/${data.total_questions}</h2>
                            <small class="text-muted">Score</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h2 class="text-info mb-0">${data.percentage}%</h2>
                            <small class="text-muted">Percentage</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h2 class="mb-0">
                                <span class="badge bg-${gradeClass} fs-4">${data.grade}</span>
                            </h2>
                            <small class="text-muted">Grade</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h2 class="mb-0">
                                <span class="badge bg-${passClass} fs-4">${data.pass_status}</span>
                            </h2>
                            <small class="text-muted">Result</small>
                        </div>
                    </div>
                </div>
                
                <!-- Question-wise Breakdown -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Question-wise Breakdown
                            <span class="float-end">
                                <span class="badge bg-success">${data.correct_answers || 0} Correct</span>
                                <span class="badge bg-danger">${data.wrong_answers || 0} Wrong</span>
                                ${(data.unanswered_questions && data.unanswered_questions > 0) ? `<span class="badge bg-secondary ms-1">${data.unanswered_questions} Unanswered</span>` : ''}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">Question</th>
                                        <th width="20%">Student Answer</th>
                                        <th width="20%">Correct Answer</th>
                                        <th width="15%" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${questionsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('resultDetailsContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading result details:', error);
            document.getElementById('resultDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error loading result details.</strong><br>
                    <small>Please check the console for more details or try again.</small><br>
                    <small class="text-muted">Error: ${error.message}</small>
                </div>
            `;
        });
}
</script>

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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}
</style>
@endsection
