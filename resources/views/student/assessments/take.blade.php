@extends('layouts.student')

@section('title', 'Taking Assessment')

@section('content')
<div class="assessment-container">
    <!-- Top Bar -->
    <div class="fixed-top bg-white shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <div class="col-md-4">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> {{ $assessment->title }}</h5>
                </div>
                <div class="col-md-4 text-center">
                    <div id="timer" class="timer"></div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-sm btn-outline-info me-2" id="saveProgressBtn">
                        <i class="fas fa-save"></i> Save Progress
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" id="submitBtn">
                        <i class="fas fa-check-circle"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="container-fluid mt-5 pt-5">
        <div class="row">
            <!-- Questions Panel -->
            <div class="col-md-9">
                <form id="assessmentForm" action="{{ route('student.assessments.submit', $assessment) }}" method="POST">
                    @csrf
                    <input type="hidden" name="time_taken" id="timeTakenInput" value="0">
                    
                    @foreach($questions as $index => $question)
                    <div class="card question-card mb-4" data-question="{{ $index + 1 }}">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Question {{ $index + 1 }} of {{ $questions->count() }}</h5>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-star"></i> {{ $question->marks ?? 1 }} {{ ($question->marks ?? 1) == 1 ? 'mark' : 'marks' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="question-text mb-4">{{ $question->question_text }}</h6>
                            
                            @php
                                $options = $question->options ?? [];
                                // Fallback to individual columns if options array is empty
                                if (empty($options)) {
                                    // Use explicit keys to preserve option positions
                                    $options = [];
                                    if (!empty($question->option_a)) $options[0] = $question->option_a;
                                    if (!empty($question->option_b)) $options[1] = $question->option_b;
                                    if (!empty($question->option_c)) $options[2] = $question->option_c;
                                    if (!empty($question->option_d)) $options[3] = $question->option_d;
                                }
                            @endphp
                            
                            @if(!empty($options))
                            <div class="options-container">
                                @foreach($options as $optIndex => $option)
                                @php
                                    // Ensure we use the correct letter based on the original index
                                    $letterValue = chr(65 + $optIndex); // A=0, B=1, C=2, D=3
                                @endphp
                                <div class="form-check option-item mb-3">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $letterValue }}"
                                           id="q{{ $question->id }}_opt{{ $optIndex }}"
                                           {{ ($existingAnswers[$question->id] ?? '') == $letterValue ? 'checked' : '' }}>
                                    <label class="form-check-label" for="q{{ $question->id }}_opt{{ $optIndex }}">
                                        <strong>{{ $letterValue }}.</strong> {{ $option }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No options available for this question.
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <!-- Navigation Buttons -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" id="prevBtn" disabled>
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" id="nextBtn">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar - Question Navigator -->
            <div class="col-md-3">
                <div class="card sticky-sidebar">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-th"></i> Question Navigator</h6>
                    </div>
                    <div class="card-body">
                        <div class="question-grid" id="questionGrid">
                            @foreach($questions as $index => $question)
                            <button type="button" class="question-nav-btn" data-question="{{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        <div class="legend mt-3">
                            <div class="legend-item mb-2">
                                <span class="badge bg-success"></span> Answered
                            </div>
                            <div class="legend-item mb-2">
                                <span class="badge bg-secondary"></span> Not Answered
                            </div>
                            <div class="legend-item">
                                <span class="badge bg-primary"></span> Current
                            </div>
                        </div>

                        <hr>

                        <div class="stats mt-3">
                            <p class="mb-1"><strong>Total Questions:</strong> {{ $questions->count() }}</p>
                            <p class="mb-1"><strong>Answered:</strong> <span id="answeredCount">0</span></p>
                            <p class="mb-1"><strong>Not Answered:</strong> <span id="notAnsweredCount">{{ $questions->count() }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Submit Assessment?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You have answered <strong><span id="modalAnsweredCount">0</span></strong> out of <strong>{{ $questions->count() }}</strong> questions.</p>
                <p class="text-danger"><strong>Warning:</strong> Once submitted, you cannot change your answers.</p>
                <p>Are you sure you want to submit?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmSubmit">
                    <i class="fas fa-check-circle"></i> Yes, Submit Now
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentQuestion = 1;
    const totalQuestions = {{ $questions->count() }};
    const duration = {{ $assessment->duration }} * 60; // Convert to seconds
    let remainingTime = duration;
    let timerInterval;
    let autoSaveInterval;

    // Initialize
    showQuestion(1);
    startTimer();
    startAutoSave();
    updateStats();

    // Timer
    function startTimer() {
        updateTimerDisplay();
        timerInterval = setInterval(function() {
            remainingTime--;
            updateTimerDisplay();
            
            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                autoSubmit();
            } else if (remainingTime <= 300) { // Last 5 minutes
                document.getElementById('timer').classList.add('timer-warning');
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const hours = Math.floor(remainingTime / 3600);
        const minutes = Math.floor((remainingTime % 3600) / 60);
        const seconds = remainingTime % 60;
        
        const display = hours > 0 
            ? `${hours}:${pad(minutes)}:${pad(seconds)}`
            : `${minutes}:${pad(seconds)}`;
        
        document.getElementById('timer').innerHTML = `<i class="fas fa-clock"></i> ${display}`;
    }

    function pad(num) {
        return num < 10 ? '0' + num : num;
    }

    // Auto-save
    function startAutoSave() {
        autoSaveInterval = setInterval(saveProgress, 60000); // Every minute
    }

    function saveProgress() {
        const formData = new FormData(document.getElementById('assessmentForm'));
        
        fetch('{{ route('student.assessments.save-progress', $assessment) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  showNotification('Progress saved', 'success');
              }
          }).catch(error => {
              console.error('Error saving progress:', error);
          });
    }

    // Navigation
    function showQuestion(questionNum) {
        document.querySelectorAll('.question-card').forEach(card => {
            card.style.display = 'none';
        });
        
        const card = document.querySelector(`.question-card[data-question="${questionNum}"]`);
        if (card) {
            card.style.display = 'block';
            currentQuestion = questionNum;
        }
        
        // Update navigation buttons
        document.getElementById('prevBtn').disabled = (currentQuestion === 1);
        document.getElementById('nextBtn').disabled = (currentQuestion === totalQuestions);
        
        // Update question grid
        updateQuestionGrid();
        
        // Scroll to top
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function updateQuestionGrid() {
        document.querySelectorAll('.question-nav-btn').forEach(btn => {
            btn.classList.remove('active', 'answered');
            
            const questionNum = parseInt(btn.dataset.question);
            
            // Check if answered
            const questionId = getQuestionId(questionNum);
            if (questionId && isQuestionAnswered(questionId)) {
                btn.classList.add('answered');
            }
            
            // Highlight current
            if (questionNum === currentQuestion) {
                btn.classList.add('active');
            }
        });
    }

    function getQuestionId(questionNum) {
        const card = document.querySelector(`.question-card[data-question="${questionNum}"]`);
        if (!card) return null;
        const radio = card.querySelector('input[type="radio"]');
        if (!radio) return null;
        const match = radio.name.match(/answers\[(\d+)\]/);
        return match ? match[1] : null;
    }

    function isQuestionAnswered(questionId) {
        const radios = document.querySelectorAll(`input[name="answers[${questionId}]"]`);
        return Array.from(radios).some(radio => radio.checked);
    }

    function updateStats() {
        let answeredCount = 0;
        
        @foreach($questions as $question)
        if (isQuestionAnswered({{ $question->id }})) {
            answeredCount++;
        }
        @endforeach
        
        document.getElementById('answeredCount').textContent = answeredCount;
        document.getElementById('notAnsweredCount').textContent = totalQuestions - answeredCount;
        document.getElementById('modalAnsweredCount').textContent = answeredCount;
    }

    // Event listeners
    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentQuestion > 1) {
            showQuestion(currentQuestion - 1);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', function() {
        if (currentQuestion < totalQuestions) {
            showQuestion(currentQuestion + 1);
        }
    });

    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            showQuestion(parseInt(this.dataset.question));
        });
    });

    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateStats();
            updateQuestionGrid();
        });
    });

    document.getElementById('saveProgressBtn').addEventListener('click', function() {
        saveProgress();
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        updateStats();
        const modal = new bootstrap.Modal(document.getElementById('submitModal'));
        modal.show();
    });

    document.getElementById('confirmSubmit').addEventListener('click', function() {
        clearInterval(timerInterval);
        clearInterval(autoSaveInterval);
        // Calculate time taken in seconds
        const timeTaken = duration - remainingTime;
        document.getElementById('timeTakenInput').value = timeTaken;
        document.getElementById('assessmentForm').submit();
    });

    function autoSubmit() {
        alert('Time is up! Your assessment will be submitted automatically.');
        clearInterval(autoSaveInterval);
        // Calculate time taken in seconds
        const timeTaken = duration - remainingTime;
        document.getElementById('timeTakenInput').value = timeTaken;
        document.getElementById('assessmentForm').submit();
    }

    function showNotification(message, type) {
        // Simple notification - you can enhance this
        const alertDiv = document.createElement('div');
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Prevent accidental page leave
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endsection

@section('styles')
<style>
.assessment-container {
    background-color: #f5f5f5;
    min-height: 100vh;
    padding-bottom: 2rem;
}

.timer {
    font-size: 1.5rem;
    font-weight: bold;
    color: #28a745;
    padding: 0.5rem 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    display: inline-block;
}

.timer-warning {
    color: #dc3545 !important;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.question-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.question-text {
    font-size: 1.1rem;
    line-height: 1.6;
}

.option-item {
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 0.5rem;
    transition: all 0.3s;
    cursor: pointer;
}

.option-item:hover {
    background-color: #f8f9fa;
    border-color: #667eea;
}

.option-item .form-check-input:checked ~ .form-check-label {
    color: #667eea;
    font-weight: 600;
}

.sticky-sidebar {
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.question-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.5rem;
}

.question-nav-btn {
    aspect-ratio: 1;
    border: 2px solid #ddd;
    background-color: white;
    border-radius: 0.375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.question-nav-btn:hover {
    background-color: #f8f9fa;
    transform: scale(1.05);
}

.question-nav-btn.active {
    background-color: #667eea;
    color: white;
    border-color: #667eea;
}

.question-nav-btn.answered {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-item .badge {
    width: 20px;
    height: 20px;
    display: inline-block;
}

.stats {
    font-size: 0.9rem;
}
</style>
@endsection