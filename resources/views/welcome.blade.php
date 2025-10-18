<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KIT Coimbatore - Placement Training Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
        @vite(['resources/css/app.css'])

            <style>
        :root {
            --primary-yellow: #FDB913;
            --dark-teal: #2C5F5F;
            --light-teal: #4A9B9B;
            --bright-yellow: #FFD700;
            --white: #FFFFFF;
            --light-gray: #F5F5F5;
            --text-dark: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

            body {
                font-family: 'Figtree', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Top Bar */
        .top-bar {
            background: var(--light-gray);
            padding: 10px 0;
            font-size: 0.85rem;
            border-bottom: 1px solid #ddd;
        }

        .top-bar a {
            color: var(--text-dark);
            text-decoration: none;
            margin: 0 15px;
            transition: color 0.3s;
        }

        .top-bar a:hover {
            color: var(--primary-yellow);
        }

        /* Main Navigation */
        .main-nav {
            background: var(--dark-teal);
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            padding: 15px 0;
        }

        .navbar-brand-custom img {
            height: 60px;
            margin-right: 15px;
        }

        .brand-text h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
            margin: 0;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.9);
            margin: 0;
        }

        .main-nav .nav-link {
            color: var(--white) !important;
            padding: 20px 20px !important;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .main-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .main-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--primary-yellow);
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .main-nav .nav-link:hover::after {
            width: 80%;
        }

        /* Hero Section */
            .hero-section {
            position: relative;
            height: 600px;
            background: linear-gradient(rgba(44, 95, 95, 0.7), rgba(44, 95, 95, 0.7)), 
                        url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1920&h=600&fit=crop') center/cover;
            display: flex;
            align-items: center;
            color: var(--white);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-content .highlight {
            color: var(--primary-yellow);
            display: block;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            max-width: 600px;
        }

        .hero-btn {
            background: var(--primary-yellow);
            color: var(--text-dark);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(253, 185, 19, 0.4);
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(253, 185, 19, 0.6);
            color: var(--text-dark);
        }

        /* Yellow Banner */
        .yellow-banner {
            background: var(--primary-yellow);
            padding: 25px 0;
                position: relative;
                overflow: hidden;
            }

        .yellow-banner::before {
                content: '';
                position: absolute;
            left: 0;
                top: 0;
            width: 100%;
                height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .yellow-banner h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .banner-icon {
            width: 50px;
            height: 50px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-yellow);
        }

        /* Three Column Features */
        .features-section {
            background: var(--dark-teal);
            padding: 60px 0;
            color: var(--white);
        }

        .feature-box {
            padding: 40px 30px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }

        .feature-box:last-child {
            border-right: none;
        }

        .feature-box:hover {
            background: rgba(255,255,255,0.1);
        }

        .feature-icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid var(--primary-yellow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary-yellow);
        }

        .feature-box h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary-yellow);
        }

        .feature-box p {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Years Section */
        .years-section {
            background: var(--primary-yellow);
            padding: 80px 0;
            text-align: center;
        }

        .years-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .years-section h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 50px;
        }

        /* Training Items Grid */
        .training-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
            max-width: 900px;
            margin: 0 auto;
        }

        .training-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            text-align: left;
        }

        .training-icon {
                width: 70px;
                height: 70px;
                border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .training-icon.teal { color: var(--dark-teal); }
        .training-icon.yellow { color: var(--primary-yellow); }

        .training-content h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .training-content p {
            font-size: 0.95rem;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Portal Description */
        .portal-description {
            background: var(--white);
            padding: 80px 0;
        }

        .portal-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .portal-title h4 {
            font-size: 1.2rem;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .portal-title h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-yellow);
            margin-bottom: 30px;
        }

        .portal-features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 50px;
        }

        .portal-feature {
            text-align: center;
            padding: 20px;
        }

        .portal-feature-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            border-radius: 50%;
            border: 2px solid var(--light-teal);
                display: flex;
                align-items: center;
                justify-content: center;
            font-size: 2.5rem;
            color: var(--light-teal);
        }

        .portal-feature h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .portal-feature p {
            font-size: 0.95rem;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .testing-section {
            background: var(--light-gray);
            padding: 40px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .testing-section h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-yellow);
            text-align: center;
            margin-bottom: 20px;
        }

        .testing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            text-align: center;
        }

        .testing-item h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .testing-item ul {
            list-style: none;
            padding: 0;
        }

        .testing-item li {
            font-size: 0.9rem;
            color: var(--text-dark);
            padding: 5px 0;
        }

        .testing-item li i {
            color: var(--primary-yellow);
            margin-right: 5px;
        }

        /* Internship Program */
        .internship-section {
            background: var(--white);
            padding: 80px 0;
        }

        .internship-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 60px;
        }

        .internship-flow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .flow-item {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .flow-item::after {
            content: '→';
            position: absolute;
            right: -25px;
            top: 30px;
                font-size: 2rem;
            color: var(--primary-yellow);
        }

        .flow-item:last-child::after {
            display: none;
        }

        .flow-box {
            background: var(--white);
            border: 2px solid var(--light-teal);
            border-radius: 10px;
            padding: 30px 20px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.3s;
        }

        .flow-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(74, 155, 155, 0.3);
        }

        .flow-box.yellow {
            background: var(--primary-yellow);
            border-color: var(--primary-yellow);
        }

        .flow-box h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark-teal);
            margin-bottom: 15px;
        }

        .flow-box.yellow h4 {
            color: var(--text-dark);
        }

        .flow-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--light-teal);
        }

        .flow-box.yellow .flow-icon {
            color: var(--text-dark);
        }

        /* Find Portal Section */
        .find-portal-section {
            background: var(--light-gray);
            padding: 80px 0;
        }

        .find-portal-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .portal-image {
                text-align: center;
        }

        .portal-image img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .portal-text h4 {
            font-size: 1.2rem;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .portal-text h2 {
                font-size: 3rem;
                font-weight: 700;
            color: var(--primary-yellow);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .portal-text h3 {
            font-size: 2rem;
                font-weight: 600;
            color: var(--dark-teal);
            margin-bottom: 30px;
        }

        .portal-text p {
                font-size: 1.1rem;
            color: var(--text-dark);
            line-height: 1.8;
            margin-bottom: 20px;
        }

        /* Statistics Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card.teal {
            background: var(--light-teal);
            color: var(--white);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1rem;
            margin: 0;
        }

        /* Training Section */
        .training-section {
            background: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .training-section h4 {
            font-size: 1.2rem;
            color: var(--dark-teal);
            margin-bottom: 10px;
        }

        .training-section h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-yellow);
            margin-bottom: 20px;
        }

        .training-section p {
            font-size: 1.2rem;
            color: var(--text-dark);
            max-width: 800px;
            margin: 0 auto 50px;
            line-height: 1.8;
        }

        /* Footer */
        .footer {
            background: var(--dark-teal);
            color: var(--white);
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo img {
            height: 60px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }

        .footer-logo h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .footer-logo p {
            font-size: 0.9rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .footer-section h5 {
            font-size: 1.1rem;
                font-weight: 600;
            margin-bottom: 20px;
            color: var(--primary-yellow);
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: var(--white);
                text-decoration: none;
            opacity: 0.9;
            transition: all 0.3s;
        }

        .footer-section ul li a:hover {
            opacity: 1;
            color: var(--primary-yellow);
            padding-left: 5px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
                border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background: var(--primary-yellow);
            color: var(--text-dark);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 20px;
            text-align: center;
        }

        .footer-bottom p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-content h1 { font-size: 2.5rem; }
            .training-grid { grid-template-columns: 1fr; }
            .portal-features { grid-template-columns: 1fr; }
            .internship-flow { flex-direction: column; }
            .flow-item::after { display: none; }
            .find-portal-content { grid-template-columns: 1fr; }
            .stats-cards { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2rem; }
            .hero-content p { font-size: 1rem; }
            .testing-grid { grid-template-columns: 1fr; }
            }
            </style>
    </head>
    <body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="d-flex justify-content-end">
                <a href="#"><i class="fas fa-envelope me-1"></i> placement@kitcoimbatore.edu</a>
                <a href="#"><i class="fas fa-phone me-1"></i> +91 123 456 7890</a>
                <a href="#"><i class="fas fa-map-marker-alt me-1"></i> Coimbatore</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg main-nav sticky-top">
            <div class="container">
            <a class="navbar-brand-custom" href="{{ url('/') }}">
                <img src="{{ asset('css/logo1-removebg-preview.png') }}" alt="KIT Logo" loading="lazy">
                <div class="brand-text">
                    <h1>KIT COIMBATORE</h1>
                    <p>Excellence Beyond Expectation</p>
                    </div>
                </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#internship">Internship</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            @if (Route::has('login'))
                    @auth
                            <li class="nav-item"><a class="nav-link" href="{{ url('/dashboard') }}"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                    @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i> Login</a></li>
                        @if (Route::has('register'))
                                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus me-1"></i> Register</a></li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
                </nav>

        <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>
                    College Placement<br>
                    <span class="highlight">Training Portal</span><br>
                    Honing Edge for Bets
                </h1>
                <p>Empowering students with world-class training and placement opportunities at Kalaignar Karunanidhi Institute of Technology</p>
                <a href="{{ route('register') }}" class="hero-btn"><i class="fas fa-rocket me-2"></i> Join Now</a>
            </div>
        </div>
    </section>

    <!-- Yellow Banner -->
    <div class="yellow-banner">
        <div class="container">
            <h2>
                <div class="banner-icon"><i class="fas fa-graduation-cap"></i></div>
                College Placement Training College Training Proceeding For Bets
            </h2>
        </div>
    </div>

    <!-- Three Column Features -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-circle">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3>ADDITION<br>INTERN DETAIL</h3>
                        <p>Add and manage student internship details with comprehensive tracking system for better placement preparation and monitoring.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-circle">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>CAREER<br>AVRDELING</h3>
                        <p>Personalized career guidance and counseling services to help students identify their strengths and choose the right career path.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-circle">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h3>ASSESSMENT<br>INAH SO V</h3>
                        <p>Comprehensive assessment system with real-time analytics to track student progress and identify areas for improvement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Years of Transparency Section -->
    <section class="years-section">
            <div class="container">
            <h2>Years of Transparency College Placement</h2>
            <h3>College Placement Portal</h3>

            <div class="training-grid">
                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="training-content">
                        <h4>Training Objects</h4>
                        <p>Comprehensive training modules designed to enhance technical skills, soft skills, and interview preparation for successful placements.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-mobile-alt"></i>
                        </div>
                    <div class="training-content">
                        <h4>Portfolio</h4>
                        <p>Build and showcase your professional portfolio with projects, certifications, and achievements to stand out to recruiters.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-user-tie"></i>
                        </div>
                    <div class="training-content">
                        <h4>Announced</h4>
                        <p>Stay updated with the latest placement drives, campus recruitment schedules, and company visit announcements in real-time.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="training-content">
                        <h4>Data Screening</h4>
                        <p>Secure and efficient screening process ensuring data privacy while matching student profiles with company requirements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Portal Description Section -->
    <section class="portal-description">
            <div class="container">
            <div class="portal-title">
                <h4>Stopnia eva przednim The</h4>
                <h2>Strongoccupaticoen Placement Portal</h2>
            </div>

            <div class="portal-features">
                <div class="portal-feature">
                    <div class="portal-feature-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h5>Intelligent Assessment</h5>
                    <p>AI-powered assessment system that adapts to student performance and provides personalized learning recommendations.</p>
                </div>

                <div class="portal-feature">
                    <div class="portal-feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5>Easy Procedure</h5>
                    <p>Simple and streamlined registration process with user-friendly interface for quick access to all portal features.</p>
                </div>

                <div class="portal-feature">
                    <div class="portal-feature-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                    <h5>Testing Service</h5>
                    <p>Comprehensive testing platform with mock interviews, aptitude tests, and technical assessments for placement preparation.</p>
                </div>
            </div>

            <div class="testing-section">
                <h4>Testing Service Coverage</h4>
                <div class="testing-grid">
                    <div class="testing-item">
                        <h5>Aptitude Tests</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Logical Reasoning</li>
                            <li><i class="fas fa-check"></i> Quantitative Ability</li>
                            <li><i class="fas fa-check"></i> Verbal Ability</li>
                        </ul>
                    </div>
                    <div class="testing-item">
                        <h5>Technical Tests</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Programming Skills</li>
                            <li><i class="fas fa-check"></i> Data Structures</li>
                            <li><i class="fas fa-check"></i> Database Management</li>
                        </ul>
                        </div>
                    <div class="testing-item">
                        <h5>Soft Skills</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Communication</li>
                            <li><i class="fas fa-check"></i> Group Discussions</li>
                            <li><i class="fas fa-check"></i> Mock Interviews</li>
                        </ul>
                    </div>
                </div>
                            </div>
                        </div>
    </section>

    <!-- Internship Program Section -->
    <section id="internship" class="internship-section">
        <div class="container">
            <h2>College Internship Program</h2>
            
            <div class="internship-flow">
                <div class="flow-item">
                    <div class="flow-box">
                        <div class="flow-icon"><i class="fas fa-file-alt"></i></div>
                        <h4>Course Psois</h4>
                        <p>Structured course modules covering industry-relevant topics and technologies</p>
                    </div>
                </div>

                <div class="flow-item">
                    <div class="flow-box yellow">
                        <div class="flow-icon"><i class="fas fa-clipboard-list"></i></div>
                        <h4>Testing Procedures<br>E-portly</h4>
                        <p>Regular assessments and evaluations to track learning progress</p>
                            </div>
                        </div>

                <div class="flow-item">
                    <div class="flow-box">
                        <div class="flow-icon"><i class="fas fa-users"></i></div>
                        <h4>Constraction<br>Caring yuor</h4>
                        <p>Hands-on project construction and real-world application development</p>
                    </div>
                            </div>

                <div class="flow-item">
                    <div class="flow-box yellow">
                        <div class="flow-icon"><i class="fas fa-trophy"></i></div>
                        <h4>Suwe Eiprnmerot<br>Ganus Elaieet</h4>
                        <p>Final evaluation and certification upon successful completion</p>
                    </div>
                    </div>
                </div>
        </div>
        </section>

    <!-- Find The Placement Portal Section -->
    <section class="find-portal-section">
        <div class="container">
            <div class="find-portal-content">
                <div class="portal-image">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&h=800&fit=crop" alt="Professional Student" loading="lazy">
                </div>
                <div class="portal-text">
                    <h4>Find The Placement Portal</h4>
                    <h2>Find The Placement<br>Portal</h2>
                    <h3>Office Placement Training For Bets</h3>
                    <p>Discover comprehensive placement training solutions designed specifically for students at KIT Coimbatore. Our portal offers end-to-end support from skill development to final placement.</p>
                    <div class="stat-card teal" style="display: inline-block; padding: 20px 40px; margin-top: 20px;">
                        <p style="margin: 0; font-size: 1.1rem;">Explore our advanced features and AI-powered chatbot assistance for personalized guidance</p>
                    </div>
                </div>
            </div>

            <div class="stats-cards">
                <div class="stat-card teal">
                    <h3>500+</h3>
                    <p>Students Placed Successfully</p>
                </div>
                <div class="stat-card teal">
                    <h3>100+</h3>
                    <p>Top Companies Visiting</p>
                </div>
                <div class="stat-card">
                    <h3 style="color: var(--dark-teal);">12 LPA</h3>
                    <p>Highest Package Offered</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Placement Section -->
    <section class="training-section">
        <div class="container">
            <h4>Training Placement Training</h4>
            <h2>Training Placement Training</h2>
            <p>Leading and been dont Theres are your than acoment at ail of this sales set much ed acy poorrecages. Our comprehensive training program ensures every student is industry-ready with the right skills and confidence.</p>
            </div>
        </section>

        <!-- Footer -->
    <footer id="contact" class="footer">
            <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="{{ asset('css/logo1-removebg-preview.png') }}" alt="KIT Logo" loading="lazy">
                    <h4>KIT COIMBATORE</h4>
                    <p>Kalaignar Karunanidhi Institute of Technology<br>
                    Excellence Beyond Expectation<br><br>
                    Empowering students with world-class placement opportunities and comprehensive training programs.</p>
                            </div>

                <div class="footer-section">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#internship">Internship</a></li>
                        <li><a href="https://kitcbe.com/" target="_blank">KIT Official Site</a></li>
                    </ul>
                        </div>

                <div class="footer-section">
                    <h5>Resources</h5>
                    <ul>
                        <li><a href="{{ route('register') }}">Student Registration</a></li>
                        <li><a href="{{ route('login') }}">Login Portal</a></li>
                        <li><a href="#">Assessment Center</a></li>
                        <li><a href="#">Career Guidance</a></li>
                        <li><a href="#">AI Chatbot Help</a></li>
                        </ul>
                    </div>

                <div class="footer-section">
                    <h5>Contact Us</h5>
                    <ul>
                        <li><i class="fas fa-envelope me-2"></i> placement@kitcoimbatore.edu</li>
                        <li><i class="fas fa-phone me-2"></i> +91 123 456 7890</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Coimbatore, Tamil Nadu</li>
                    </ul>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} KIT Coimbatore. All rights reserved. | Designed with <i class="fas fa-heart" style="color: var(--primary-yellow);"></i> for students</p>
                </div>
            </div>
        </footer>

        <!-- JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.main-nav');
            if (window.scrollY > 100) {
                nav.style.boxShadow = '0 5px 20px rgba(0,0,0,0.2)';
            } else {
                nav.style.boxShadow = '0 2px 10px rgba(126, 34, 206, 0.3)';
            }
        });
        </script>
    </body>
</html>
