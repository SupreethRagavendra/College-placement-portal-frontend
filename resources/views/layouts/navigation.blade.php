<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('student.dashboard') }}">
            <img src="{{ asset('css/logo1-removebg-preview.png') }}" alt="College Logo" style="height: 40px; margin-right: 10px;" loading="lazy">
            College Placement Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.categories') ? 'active' : '' }}" href="{{ route('student.categories') }}">Categories</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.assessments.analytics') ? 'active' : '' }}" href="{{ route('student.assessments.analytics') }}">Analytics</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('student.assessment.history') ? 'active' : '' }}" href="{{ route('student.assessment.history') }}">History</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDd" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name ?? 'User' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDd">
                        <li><a class="dropdown-item" href="{{ route('student.profile') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
