<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student') - KIT Training Portal</title>
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Fonts with font-display swap -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.5.2 - Deferred -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
    
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
        
        * {
            font-family: 'Figtree', sans-serif;
        }
        
        body {
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        /* Sidebar Styles - Red & Black Theme */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--black) 0%, var(--dark-gray) 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 14px 20px;
            border-radius: 12px;
            margin: 6px 0;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(220, 20, 60, 0.2);
            transform: translateX(6px);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
            transform: translateX(6px);
        }
        
        .sidebar h5 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .sidebar hr {
            border-color: rgba(255,255,255,0.15);
            margin: 15px 0;
        }
        
        .sidebar .logo-container {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .sidebar .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* Main Content Area */
        .main-content {
            background-color: var(--light-gray);
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 30px;
            border-bottom: 2px solid var(--light-gray);
        }
        
        .navbar-brand {
            color: var(--text-dark) !important;
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .navbar-text {
            color: var(--text-dark);
            font-weight: 500;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        /* Content Container */
        .content-container {
            padding: 30px;
        }
    </style>
    @stack('head')
    @yield('styles')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="logo-container me-3">
                                <img src="{{ asset('css/logo1-removebg-preview.png') }}" alt="KIT Logo" loading="lazy">
                            </div>
                            <h5 class="text-white mb-0" style="font-size: 1.1rem;">Student Portal</h5>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('student.assessments.*') ? 'active' : '' }}" href="{{ route('student.assessments.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i>Assessments
                            </a>
                            <a class="nav-link {{ request()->routeIs('student.assessment.history') ? 'active' : '' }}" href="{{ route('student.assessment.history') }}">
                                <i class="fas fa-history me-2"></i>Test History
                            </a>
                            <a class="nav-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}" href="{{ route('student.results.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>Results
                            </a>
                            <a class="nav-link {{ request()->routeIs('student.categories') ? 'active' : '' }}" href="{{ route('student.categories') }}">
                                <i class="fas fa-tags me-2"></i>Categories
                            </a>
                            <a class="nav-link {{ request()->routeIs('student.assessments.analytics') ? 'active' : '' }}" href="{{ route('student.assessments.analytics') }}">
                                <i class="fas fa-chart-line me-2"></i>Analytics
                            </a>
                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                            <hr class="text-white-50">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                        <div class="container-fluid">
                            <h5 class="navbar-brand mb-0">@yield('title', 'Student Portal')</h5>
                            
                            <div class="navbar-nav ms-auto">
                                <span class="navbar-text me-3">
                                    Welcome, <strong>{{ Auth::user()->name ?? 'Student' }}</strong>!
                                </span>
                            </div>
                        </div>
                    </nav>
                    
                    <!-- Content Container -->
                    <div class="container-fluid p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: none; border-left: 4px solid #28a745; border-radius: 10px; color: #155724; font-weight: 500;">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: none; border-left: 4px solid var(--primary-red); border-radius: 10px; color: #721c24; font-weight: 500;">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    
    <!-- Student Chatbot Component -->
    <x-student-chatbot />
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>
