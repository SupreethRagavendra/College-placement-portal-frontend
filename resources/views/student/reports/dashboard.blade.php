@extends('layouts.student')

@section('title', 'Performance Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Performance Dashboard</h1>
</div>

@if($totalAssessments == 0)
<div class="text-center py-5">
    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
    <h4>No assessment data available</h4>
    <p class="text-muted">Complete assessments to see your performance analytics.</p>
    <a href="{{ route('student.assessments.index') }}" class="btn btn-primary">View Available Assessments</a>
</div>
@else
<!-- Overall Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $totalAssessments }}</h5>
                <p class="card-text">Total Assessments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $passedAssessments }}</h5>
                <p class="card-text">Passed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $failedAssessments }}</h5>
                <p class="card-text">Failed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ number_format($averagePercentage, 2) }}%</h5>
                <p class="card-text">Average Score</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Recent Assessments -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Recent Assessments</h5>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Assessment</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAssessments as $attempt)
                        <tr>
                            <td>{{ $attempt->assessment->title }}</td>
                            <td><span class="badge bg-info">{{ $attempt->assessment->category }}</span></td>
                            <td>{{ number_format($attempt->percentage, 2) }}%</td>
                            <td>
                                @if($attempt->pass_status == 'pass')
                                    <span class="badge bg-success">Pass</span>
                                @else
                                    <span class="badge bg-danger">Fail</span>
                                @endif
                            </td>
                            <td>{{ $attempt->submit_time->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Category Performance -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Category Performance</h5>
            </div>
            <div class="card-body">
                @foreach($categoryPerformance as $category => $stats)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $category }}</strong>
                        <span>{{ $stats['average_percentage'] }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $stats['average_percentage'] }}%"></div>
                    </div>
                    <div class="small text-muted mt-1">
                        {{ $stats['passed'] }}/{{ $stats['total_attempts'] }} passed
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Overall Pass Rate -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Overall Pass Rate</h5>
            </div>
            <div class="card-body text-center">
                <h1 class="display-4 text-{{ $passRate >= 50 ? 'success' : 'danger' }}">{{ $passRate }}%</h1>
                <p class="mb-0">Pass Rate</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection