@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Reports & Analytics</h1>
        <a href="{{ url('/admin/reports/export') }}" class="btn btn-outline-primary">Export CSV</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="fw-bold">Total Students</div>
                    <div class="display-6">{{ $totalStudents }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="fw-bold">Total Attempts</div>
                    <div class="display-6">{{ $totalAttempts }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="fw-bold">Average Score</div>
                    <div class="display-6">{{ $averageScore }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="fw-bold">Category Performance</div>
                    <div>Aptitude: {{ $avgByCategory['Aptitude'] }}</div>
                    <div>Technical: {{ $avgByCategory['Technical'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Average score by category</div>
        <div class="card-body">
            <canvas id="barChart" height="100"></canvas>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Test activity over time</div>
        <div class="card-body">
            <canvas id="lineChart" height="100"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Assessment Attempts by Assessment</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Assessment</th>
                            <th>Category</th>
                            <th>Total Attempts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attemptsByAssessment as $assessmentId => $attempts)
                            @php $assessment = $assessmentsById[$assessmentId] ?? null; @endphp
                            @if($assessment)
                                <tr>
                                    <td>{{ $assessment['name'] ?? 'Unknown' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assessment['category'] === 'Aptitude' ? 'primary' : 'success' }}">
                                            {{ $assessment['category'] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>{{ $attempts }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No assessment attempts found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Student Results</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Assessment</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Time Taken</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentResults as $result)
                            @php 
                                $assessmentId = (int)($result['assessment_id'] ?? 0);
                                $assessment = $assessmentsById[$assessmentId] ?? null;
                                $studentId = (int)($result['student_id'] ?? 0);
                                $studentName = $usersById[$studentId] ?? 'Unknown Student';
                            @endphp
                            <tr>
                                <td>{{ $studentName }}</td>
                                <td>{{ $assessment['name'] ?? 'Unknown Assessment' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($assessment['category'] ?? '') === 'Aptitude' ? 'primary' : 'success' }}">
                                        {{ $assessment['category'] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ (int)($result['score'] ?? 0) >= (int)($result['total_questions'] ?? 1) * 0.7 ? 'success' : 'warning' }}">
                                        {{ $result['score'] ?? 0 }}/{{ $result['total_questions'] ?? 0 }}
                                    </span>
                                </td>
                                <td>{{ gmdate('H:i:s', (int)($result['time_taken'] ?? 0)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($result['submitted_at'] ?? '')->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No student results found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const barCtx = document.getElementById('barChart');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Aptitude', 'Technical'],
            datasets: [{
                label: 'Average Score',
                data: [{{ $avgByCategory['Aptitude'] }}, {{ $avgByCategory['Technical'] }}],
                backgroundColor: ['#0d6efd', '#198754'],
            }]
        },
        options: { scales: { y: { beginAtZero: true, max: 100 } } }
    });

    const lineCtx = document.getElementById('lineChart');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($activityLabels) !!},
            datasets: [{
                label: 'Tests',
                data: {!! json_encode($activityValues) !!},
                borderColor: '#0d6efd',
                tension: 0.2,
            }]
        }
    });
</script>
@endsection


