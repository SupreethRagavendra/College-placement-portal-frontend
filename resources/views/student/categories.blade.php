@extends('layouts.student')

@section('title', 'Assessment Categories - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.categories-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
    position: relative;
    overflow: hidden;
}

.categories-header .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.category-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.category-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.aptitude-icon {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.technical-icon {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
}

.stat-divider {
    width: 2px;
    height: 40px;
    background: linear-gradient(180deg, transparent, #dee2e6, transparent);
}

.btn-start {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
}

.btn-start:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
    color: white;
}

.info-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
}

.info-card .card-body {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.guideline-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="categories-header">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1 class="h2 mb-2 fw-bold">
                <i class="fas fa-layer-group me-2"></i>Assessment Categories
            </h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">Select a test category to begin your assessment journey and evaluate your skills</p>
        </div>
        <div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    <i class="fas fa-layer-group hero-icon"></i>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius: 12px; border-left: 4px solid #dc3545;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius: 12px; border-left: 4px solid #28a745;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Categories Grid -->
<div class="row justify-content-center g-4 mb-4">
    @forelse ($categories as $cat)
        <div class="col-12 col-md-6">
            <div class="card category-card">
                <div class="card-body text-center p-5">
                    <div class="category-icon {{ $cat->name === 'Aptitude' ? 'aptitude-icon' : 'technical-icon' }}">
                        <i class="fa-solid {{ $cat->name === 'Aptitude' ? 'fa-brain' : 'fa-microchip' }} fa-3x text-white"></i>
                    </div>
                    <h3 class="card-title mb-3 fw-bold" style="color: var(--text-dark);">{{ $cat->name }}</h3>
                    <p class="text-muted mb-4" style="font-size: 1rem;">
                        {{ $cat->name === 'Aptitude' ? 
                           'Test your quantitative aptitude, logical reasoning, and verbal ability skills.' : 
                           'Evaluate your programming, databases, data structures, and CS fundamentals.' }}
                    </p>
                    
                    <div class="d-flex justify-content-center align-items-center gap-4 mb-4">
                        <div class="text-center">
                            <div class="fw-bold mb-1" style="font-size: 2rem; color: var(--primary-red);">{{ $cat->total_assessments }}</div>
                            <small class="text-muted fw-semibold">{{ Str::plural('Assessment', $cat->total_assessments) }}</small>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="text-center">
                            <div class="fw-bold mb-1" style="font-size: 2rem; color: var(--primary-red);">{{ $cat->total_questions }}</div>
                            <small class="text-muted fw-semibold">{{ Str::plural('Question', $cat->total_questions) }}</small>
                        </div>
                    </div>

                    @if($cat->total_assessments > 0)
                        <a href="{{ route('student.test', $cat->id) }}" class="btn btn-start btn-lg w-100">
                            <i class="fas fa-play-circle me-2"></i>Start Assessment
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg w-100" style="border-radius: 50px; font-weight: 600;" disabled>
                            <i class="fas fa-lock me-2"></i>No Assessments Available
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card category-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-4x mb-3" style="color: var(--primary-red); opacity: 0.3;"></i>
                    <h4 class="fw-bold" style="color: var(--text-dark);">No Categories Available</h4>
                    <p class="text-muted mb-4">There are no assessment categories available at the moment. Please check back later.</p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-start">
                        <i class="fas fa-home me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Assessment Guidelines -->
<div class="row">
    <div class="col-12">
        <div class="card info-card">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold" style="color: var(--text-dark); font-size: 1.3rem;">
                    <i class="fas fa-info-circle me-2" style="color: var(--primary-red);"></i>Assessment Guidelines
                </h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="guideline-icon" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-2 fw-bold" style="color: var(--text-dark);">Multiple Assessments</h6>
                                <p class="text-muted mb-0">Each category contains various assessments to test different skills and knowledge areas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="guideline-icon" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); color: #856404;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-2 fw-bold" style="color: var(--text-dark);">Time Limits</h6>
                                <p class="text-muted mb-0">Each assessment has specific time limits to complete. Manage your time wisely</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="guideline-icon" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460;">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-2 fw-bold" style="color: var(--text-dark);">Instant Results</h6>
                                <p class="text-muted mb-0">Get detailed results and analysis immediately after submission to track your performance</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="guideline-icon" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24;">
                                    <i class="fas fa-history"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-2 fw-bold" style="color: var(--text-dark);">Track Progress</h6>
                                <p class="text-muted mb-0">Review your assessment history and performance trends anytime from your dashboard</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
