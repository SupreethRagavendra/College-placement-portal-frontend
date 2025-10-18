<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - KIT Training Portal</title>
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" media="print" onload="this.media='all';this.onload=null;">
    
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
        
        /* Sidebar Styles - Red & Black Theme (Matching Dashboard) */
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
        
        .sidebar h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .sidebar hr {
            border-color: rgba(255,255,255,0.15);
            margin: 15px 0;
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
            overflow: hidden;
        }
        
        /* Content Container */
        .content-container {
            padding: 30px;
        }
        
        /* Card Hover Effect */
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
    </style>
    @stack('head')
    @yield('styles')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4 class="text-white mb-4" style="font-weight: 700; letter-spacing: 0.5px;">
                            <i class="fas fa-shield-alt me-2"></i>
                            Admin Portal
                        </h4>
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            
                            <a class="nav-link {{ request()->is('admin/students/pending') ? 'active' : '' }}" href="{{ route('admin.pending-students') }}">
                                <i class="fas fa-clock me-2"></i>Pending Students
                            </a>
                            
                            <a class="nav-link {{ request()->is('admin/students/approved') ? 'active' : '' }}" href="{{ route('admin.approved-students') }}">
                                <i class="fas fa-check-circle me-2"></i>Approved Students
                            </a>
                            
                            <a class="nav-link {{ request()->is('admin/students/rejected') ? 'active' : '' }}" href="{{ route('admin.rejected-students') }}">
                                <i class="fas fa-times-circle me-2"></i>Rejected Students
                            </a>
                            
                            <hr class="text-white-50">
                            
                            <a class="nav-link {{ request()->is('admin/assessments*') ? 'active' : '' }}" href="{{ route('admin.assessments.index') }}">
                                <i class="fas fa-clipboard-list me-2"></i>Assessments
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.reports.student-performance') ? 'active' : '' }}" href="{{ route('admin.reports.student-performance') }}">
                                <i class="fas fa-user-graduate me-2"></i>Student Progress
                            </a>
                            
                            <a class="nav-link {{ request()->is('admin/reports*') && !request()->routeIs('admin.reports.student-performance') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-chart-line me-2"></i>Reports
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
                            <h5 class="navbar-brand mb-0">@yield('page-title', 'Training Portal Dashboard')</h5>
                            
                            <div class="navbar-nav ms-auto">
                                <span class="navbar-text me-3">
                                    Welcome, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong>!
                                </span>
                            </div>
                        </div>
                    </nav>
                    
                    <!-- Content Container -->
                    <div class="container-fluid p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>


