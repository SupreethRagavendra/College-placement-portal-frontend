@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.student')

@section('title', 'Profile Settings - KIT Training Portal')

@section('styles')
<style>
:root {
    --primary-red: #DC143C;
    --dark-red: #B91C1C;
    --text-dark: #333333;
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
    position: relative;
    overflow: hidden;
}

.profile-header .hero-icon {
    position: absolute;
    right: -40px;
    bottom: -20px;
    font-size: 10rem;
    opacity: 0.1;
}

.profile-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.profile-card .card-header {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    color: white;
    border: none;
    padding: 20px 25px;
    font-weight: 700;
}

.password-card .card-header {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: white;
}

.danger-card {
    border: 2px solid #dc3545;
}

.danger-card .card-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.form-control:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
}

.btn-save {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
    border: none;
    color: white;
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 20, 60, 0.4);
    color: white;
}

.btn-update-password {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    border: none;
    color: white;
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.btn-update-password:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
    color: white;
}

.btn-delete {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    color: white;
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    color: white;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
}
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="profile-header">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1 class="h2 mb-2 fw-bold">
                <i class="fas fa-user-circle me-2"></i>Profile Settings
            </h1>
            <p class="mb-0 opacity-90" style="font-size: 1.1rem;">Manage your account information and security settings</p>
        </div>
        <div>
            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}" class="btn btn-light" style="padding: 12px 24px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    <i class="fas fa-user-cog hero-icon"></i>
</div>

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-lg-6">
        <div class="card profile-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Profile Information
                </h5>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Update Password -->
    <div class="col-lg-6">
        <div class="card profile-card password-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2"></i>Update Password
                </h5>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="col-lg-12">
        <div class="card profile-card danger-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                </h5>
            </div>
            <div class="card-body p-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
