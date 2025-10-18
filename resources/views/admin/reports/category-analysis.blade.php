@extends('layouts.admin')

@section('title', 'Category Analysis')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="mb-0">Category Analysis</h1>
            <p class="text-muted mb-0">Performance breakdown by assessment categories</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    @if($categoryStats->count() > 0)
        <!-- Category Cards -->
        <div class="row g-3 mb-4">
            @foreach($categoryStats as $category)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-{{ $category->category === 'Aptitude' ? 'primary' : ($category->category === 'Technical' ? 'success' : 'info') }} text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-{{ $category->category === 'Aptitude' ? 'brain' : ($category->category === 'Technical' ? 'code' : 'tag') }} me-2"></i>
                                {{ $category->category }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div>
                                        <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                                        <h4 class="mb-0">{{ $category->total_assessments }}</h4>
                                        <small class="text-muted">Assessments</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div>
                                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-0">{{ $category->unique_students }}</h4>
                                        <small class="text-muted">Students</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div>
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <h4 class="mb-0">{{ $category->total_attempts }}</h4>
                                        <small class="text-muted">Total Attempts</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div>
                                        <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                                        <h4 class="mb-0">{{ number_format($category->avg_percentage, 1) }}%</h4>
                                        <small class="text-muted">Avg Score</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <label class="small text-muted">Average Performance</label>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-{{ $category->avg_percentage >= 70 ? 'success' : ($category->avg_percentage >= 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ min($category->avg_percentage, 100) }}%"
                                         aria-valuenow="{{ $category->avg_percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($category->avg_percentage, 1) }}%
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Indicator -->
                            <div class="mt-3">
                                @if($category->avg_percentage >= 70)
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Excellent Performance!</strong> Students are performing well in this category.
                                    </div>
                                @elseif($category->avg_percentage >= 50)
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Moderate Performance.</strong> There's room for improvement.
                                    </div>
                                @else
                                    <div class="alert alert-danger mb-0">
                                        <i class="fas fa-times-circle me-2"></i>
                                        <strong>Needs Attention!</strong> Students are struggling with this category.
                                    </div>
                                @endif
                            </div>

                            <!-- View Details Button -->
                            <div class="mt-3">
                                <a href="{{ route('admin.assessments.index') }}?category={{ $category->category }}" 
                                   class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-eye me-2"></i>View {{ $category->category }} Assessments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Comparison Chart Placeholder -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Category Comparison
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Assessments</th>
                                <th>Total Attempts</th>
                                <th>Unique Students</th>
                                <th>Avg Score</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryStats as $category)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $category->category === 'Aptitude' ? 'primary' : ($category->category === 'Technical' ? 'success' : 'info') }}">
                                            {{ $category->category }}
                                        </span>
                                    </td>
                                    <td>{{ $category->total_assessments }}</td>
                                    <td>{{ $category->total_attempts }}</td>
                                    <td>{{ $category->unique_students }}</td>
                                    <td>
                                        <strong>{{ number_format($category->avg_percentage, 1) }}%</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 100px; height: 10px;">
                                            <div class="progress-bar bg-{{ $category->avg_percentage >= 70 ? 'success' : ($category->avg_percentage >= 50 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ min($category->avg_percentage, 100) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                <h5>No Category Data Available</h5>
                <p class="text-muted">No assessments have been completed yet to generate category analytics.</p>
                <a href="{{ route('admin.assessments.index') }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-list me-2"></i>View Assessments
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

.btn-block {
    width: 100%;
}
</style>
@endsection
