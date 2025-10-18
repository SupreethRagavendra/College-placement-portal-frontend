<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KIT Coimbatore - Placement Training Portal</title>

    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    
    <!-- Fonts with font-display swap for faster rendering -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            color: var(--primary-red);
        }

        /* Main Navigation */
        .main-nav {
            background: var(--black);
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .main-nav .container {
            display: flex;
            align-items: center;
        }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            padding: 0;
            text-decoration: none;
            margin-right: auto;
        }

        .navbar-brand-custom:hover {
            text-decoration: none;
        }

        .navbar-brand-custom img {
            height: 65px;
            width: 65px;
            margin-right: 15px;
            background: white;
            padding: 3px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center center;
            display: block;
            box-shadow: 0 2px 8px rgba(255,255,255,0.2);
            flex-shrink: 0;
        }

        .brand-text {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1;
        }

        .brand-text h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--white);
            margin: 0 0 5px 0;
            line-height: 1.2;
            text-decoration: none;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .brand-text p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.95);
            margin: 0;
            text-decoration: none;
            font-weight: 500;
            line-height: 1.2;
        }

        .main-nav .nav-link {
            color: var(--white) !important;
            padding: 25px 20px !important;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            display: flex;
            align-items: center;
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
            background: var(--primary-red);
            transition: all 0.3s;
            transform: translateX(-50%);
        }

        .main-nav .nav-link:hover::after {
            width: 80%;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
        }

        .main-nav .navbar-collapse {
            display: flex;
            align-items: center;
        }

        .navbar-toggler {
            border-color: var(--white) !important;
            padding: 8px 10px;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 600px;
            overflow: hidden;
        }

        .hero-carousel {
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .hero-carousel .carousel-inner,
        .hero-carousel .carousel-item {
            height: 100%;
        }

        .hero-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            opacity: 1;
            filter: brightness(1.1) contrast(1.05);
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(26, 26, 26, 0.4), rgba(185, 28, 28, 0.3));
            z-index: 1;
            pointer-events: none;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: left;
            width: 90%;
            max-width: 1200px;
            color: var(--white);
        }


        .carousel-indicators {
            z-index: 3;
            bottom: 20px;
            margin-bottom: 0;
        }

        .carousel-indicators [data-bs-target] {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            border: 2px solid white;
            margin: 0 6px;
            opacity: 1;
            transition: all 0.3s ease;
        }

        .carousel-indicators [data-bs-target]:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: scale(1.1);
        }

        .carousel-indicators .active {
            background-color: var(--primary-red) !important;
            border-color: var(--primary-red) !important;
            width: 16px;
            height: 16px;
        }

        .carousel-item {
            transition: transform 1s ease-in-out;
        }

        .carousel-fade .carousel-item {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .carousel-fade .carousel-item.active {
            opacity: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8), 0 0 20px rgba(0,0,0,0.6);
        }

        .hero-content .highlight {
            color: var(--primary-red);
            display: block;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.9), 0 0 20px rgba(0,0,0,0.7);
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            max-width: 600px;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.8), 0 0 15px rgba(0,0,0,0.6);
        }

        .hero-btn {
            background: var(--primary-red);
            color: var(--white);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.4);
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.6);
            background: var(--dark-red);
            color: var(--white);
        }

        /* Red Banner */
        .yellow-banner {
            background: var(--primary-red);
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
            color: var(--white);
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
            color: var(--primary-red);
        }

        /* Three Column Features */
        .features-section {
            background: var(--black);
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
            border: 3px solid var(--primary-red);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary-red);
        }

        .feature-box h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary-red);
        }

        .feature-box p {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Training Section (Red Background) */
        .years-section {
            background: var(--primary-red);
            padding: 80px 0;
            text-align: center;
        }

        .years-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 10px;
        }

        .years-section h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--white);
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

        .training-icon.teal { color: var(--black); }
        .training-icon.yellow { color: var(--primary-red); }

        .training-content h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 10px;
        }

        .training-content p {
            font-size: 0.95rem;
            color: var(--white);
            line-height: 1.6;
            opacity: 0.95;
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
            color: var(--black);
            margin-bottom: 10px;
        }

        .portal-title h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-red);
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
            border: 2px solid var(--primary-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-red);
        }

        .portal-feature h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--black);
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
            color: var(--primary-red);
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
            color: var(--black);
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
            color: var(--primary-red);
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
            color: var(--primary-red);
        }

        .flow-item:last-child::after {
            display: none;
        }

        .flow-box {
            background: var(--white);
            border: 2px solid var(--black);
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
            box-shadow: 0 10px 30px rgba(220, 20, 60, 0.3);
        }

        .flow-box.yellow {
            background: var(--primary-red);
            border-color: var(--primary-red);
        }

        .flow-box h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 15px;
        }

        .flow-box.yellow h4 {
            color: var(--white);
        }

        .flow-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--black);
        }

        .flow-box.yellow .flow-icon {
            color: var(--white);
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
            color: var(--black);
            margin-bottom: 10px;
        }

        .portal-text h2 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-red);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .portal-text h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--black);
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
            background: var(--primary-red);
            color: var(--white);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stat-card:not(.teal) h3 {
            color: var(--primary-red);
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
            color: var(--black);
            margin-bottom: 10px;
        }

        .training-section h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-red);
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
            background: var(--black);
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
            color: var(--primary-red);
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
            color: var(--primary-red);
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
            background: var(--primary-red);
            color: var(--white);
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

        /* Navigation Button Styles */
        .btn-primary-red {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white !important;
            padding: 12px 30px;
            border-radius: 50px;
            margin-left: 15px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
            line-height: 1;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-primary-red:hover {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--primary-red) 100%);
            color: white !important;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.5);
        }

        .btn-primary-red:active {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(220, 20, 60, 0.4);
        }

        .btn-primary-red:focus {
            color: white !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
            outline: none;
        }

        /* Login Button Styling */
        .btn-login {
            background: transparent;
            color: var(--white) !important;
            padding: 12px 30px;
            border-radius: 50px;
            margin-left: 10px;
            margin-right: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid var(--white);
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
            line-height: 1;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: var(--white);
            color: var(--black) !important;
            border-color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }

        .btn-login:active {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 255, 255, 0.2);
        }

        .btn-login:focus {
            color: var(--white) !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
            outline: none;
        }

        /* CTA Button Styles */
        .btn-primary {
            background: var(--primary-red);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: var(--dark-red);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
            color: white;
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
                    <img src="{{ asset('css/kit-logo.png') }}" alt="KIT Coimbatore Logo" loading="lazy">
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
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    @if (Route::has('login'))
                @auth
                    <li class="nav-item">
                        <a class="btn-primary-red" href="{{ url('/dashboard') }}">Dashboard</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn-login" href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="btn-primary-red" href="{{ route('register') }}">Register</a>
                    </li>
                    @endif
                @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('css/hero-slide-1.jpg') }}" class="d-block w-100" alt="KIT Campus Daytime">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('css/hero-slide-2.jpg') }}" class="d-block w-100" alt="KIT Campus Night">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('css/hero-slide-3.jpg') }}" class="d-block w-100" alt="KIT Campus Aerial View">
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="container">
                <h1>
                    College Placement<br>
                    <span class="highlight">Training Portal</span><br>
                    Your Path to Success
                </h1>
                <p>AI-Powered Assessment Platform with Intelligent Chatbot Assistance for Comprehensive Placement Training</p>
                <a href="{{ route('register') }}" class="hero-btn"><i class="fas fa-rocket me-2"></i> Start Training</a>
            </div>
        </div>
    </section>

    <!-- Yellow Banner -->
    <div class="yellow-banner">
        <div class="container">
            <h2>
                <div class="banner-icon"><i class="fas fa-brain"></i></div>
                Smart Assessment System • AI Chatbot • Progress Tracking • Mock Tests
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
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3>SMART<br>ASSESSMENTS</h3>
                        <p>Take real-time assessments across multiple categories with instant scoring, detailed analytics, and performance insights to track your progress.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-circle">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>AI CHATBOT<br>ASSISTANT</h3>
                        <p>Get instant help and personalized guidance from our intelligent AI chatbot trained on placement materials and frequently asked questions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon-circle">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>PROGRESS<br>TRACKING</h3>
                        <p>Monitor your learning journey with comprehensive analytics, performance reports, and skill improvement recommendations based on test results.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Features Section -->
    <section class="years-section">
        <div class="container">
            <h2>Complete Placement Training Platform</h2>
            <h3>Everything You Need to Excel</h3>

            <div class="training-grid">
                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="training-content">
                        <h4>Multiple Categories</h4>
                        <p>Practice assessments in aptitude, technical skills, logical reasoning, verbal ability, and programming across various difficulty levels.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="training-content">
                        <h4>AI-Powered Chatbot</h4>
                        <p>Get instant answers to your questions with our intelligent RAG-based chatbot trained on comprehensive placement preparation materials.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="training-content">
                        <h4>Performance Analytics</h4>
                        <p>View detailed analytics dashboard showing your strengths, weaknesses, score trends, and personalized improvement recommendations.</p>
                    </div>
                </div>

                <div class="training-item">
                    <div class="training-icon teal">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="training-content">
                        <h4>Complete History</h4>
                        <p>Access your complete assessment history with detailed reports, correct answers, and explanations to learn from mistakes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portal Description Section -->
    <section id="categories" class="portal-description">
        <div class="container">
            <div class="portal-title">
                <h4>Advanced Training System</h4>
                <h2>Smart Placement Training Portal</h2>
            </div>

            <div class="portal-features">
                <div class="portal-feature">
                    <div class="portal-feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h5>Interactive Learning</h5>
                    <p>Engage with interactive assessments featuring instant feedback, detailed explanations, and adaptive difficulty levels based on your performance.</p>
                </div>

                <div class="portal-feature">
                    <div class="portal-feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h5>AI Chat Support</h5>
                    <p>24/7 AI chatbot assistance providing instant answers, study tips, exam strategies, and personalized guidance throughout your preparation.</p>
                </div>

                <div class="portal-feature">
                    <div class="portal-feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h5>Track Progress</h5>
                    <p>Comprehensive dashboard tracking scores, time spent, topics mastered, areas needing improvement, and overall readiness level.</p>
                </div>
            </div>

            <div class="testing-section">
                <h4>Assessment Categories Available</h4>
                <div class="testing-grid">
                    <div class="testing-item">
                        <h5>Aptitude & Reasoning</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Logical Reasoning</li>
                            <li><i class="fas fa-check"></i> Quantitative Aptitude</li>
                            <li><i class="fas fa-check"></i> Verbal Reasoning</li>
                            <li><i class="fas fa-check"></i> Data Interpretation</li>
                        </ul>
                    </div>
                    <div class="testing-item">
                        <h5>Technical Skills</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Programming (C, C++, Java, Python)</li>
                            <li><i class="fas fa-check"></i> Data Structures & Algorithms</li>
                            <li><i class="fas fa-check"></i> DBMS & SQL</li>
                            <li><i class="fas fa-check"></i> Operating Systems</li>
                        </ul>
                        </div>
                    <div class="testing-item">
                        <h5>Core Subjects</h5>
                        <ul>
                            <li><i class="fas fa-check"></i> Computer Networks</li>
                            <li><i class="fas fa-check"></i> Software Engineering</li>
                            <li><i class="fas fa-check"></i> Web Technologies</li>
                            <li><i class="fas fa-check"></i> General Knowledge</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="internship-section">
        <div class="container">
            <h2>How The Training Portal Works</h2>
            
            <div class="internship-flow">
                <div class="flow-item">
                    <div class="flow-box">
                        <div class="flow-icon"><i class="fas fa-user-plus"></i></div>
                        <h4>Register &<br>Login</h4>
                        <p>Create your account and get admin approval to access the training portal</p>
                    </div>
                        </div>

                <div class="flow-item">
                    <div class="flow-box yellow">
                        <div class="flow-icon"><i class="fas fa-clipboard-list"></i></div>
                        <h4>Take<br>Assessments</h4>
                        <p>Choose from multiple categories and attempt timed assessments with instant results</p>
                    </div>
                </div>

                <div class="flow-item">
                    <div class="flow-box">
                        <div class="flow-icon"><i class="fas fa-robot"></i></div>
                        <h4>Use AI<br>Chatbot</h4>
                        <p>Get instant help, clarifications, and study guidance from our intelligent chatbot</p>
                    </div>
                        </div>

                <div class="flow-item">
                    <div class="flow-box yellow">
                        <div class="flow-icon"><i class="fas fa-chart-pie"></i></div>
                        <h4>View Analytics &<br>Improve</h4>
                        <p>Analyze performance, identify weak areas, and track improvement over time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Find The Placement Portal Section -->
    <section class="find-portal-section">
        <div class="container">
            <div class="find-portal-content" style="grid-template-columns: 1fr; text-align: center;">
                <div class="portal-text">
                    <h4>Your Complete Training Solution</h4>
                    <h2>Master Placement<br>Preparation</h2>
                    <h3>Smart Training Platform</h3>
                    <p>Our comprehensive training portal combines assessments, AI chatbot assistance, and performance analytics to help you excel in placement tests. Practice, learn, improve - all in one platform.</p>
                    <div class="stat-card teal" style="display: inline-block; padding: 20px 40px; margin-top: 20px;">
                        <p style="margin: 0; font-size: 1.1rem;"><i class="fas fa-robot me-2"></i>Powered by advanced AI chatbot with RAG technology for instant, accurate answers</p>
                    </div>
                </div>
            </div>

            <div class="stats-cards">
                <div class="stat-card teal">
                    <h3>20+</h3>
                    <p>Assessment Categories Available</p>
                </div>
                <div class="stat-card teal">
                    <h3>1000+</h3>
                    <p>Practice Questions</p>
                </div>
                <div class="stat-card">
                    <h3 style="color: var(--dark-teal);">24/7</h3>
                    <p>AI Chatbot Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="training-section">
        <div class="container">
            <h4>Why Choose Our Platform</h4>
            <h2>Complete Training Ecosystem</h2>
            <p>Our advanced placement training portal combines cutting-edge AI technology with comprehensive assessment coverage. Get instant feedback, personalized learning paths, and 24/7 chatbot support to accelerate your preparation and achieve your placement goals.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="{{ asset('css/kit-logo.png') }}" alt="KIT Coimbatore Logo" width="60" height="60" loading="lazy">
                    <h4>KIT COIMBATORE</h4>
                    <p>Kalaignar Karunanidhi Institute of Technology<br>
                    Excellence Beyond Expectation<br><br>
                    Empowering students with world-class placement opportunities and comprehensive training programs.</p>
                </div>

                <div class="footer-section">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#categories">Categories</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="https://kitcbe.com/" target="_blank">KIT Official Site</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h5>Portal Features</h5>
                    <ul>
                        <li><a href="{{ route('register') }}">Register Account</a></li>
                        <li><a href="{{ route('login') }}">Login Portal</a></li>
                        <li><a href="#features">Practice Assessments</a></li>
                        <li><a href="#features">AI Chatbot</a></li>
                        <li><a href="#features">View Analytics</a></li>
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
                <p>&copy; {{ date('Y') }} KIT Coimbatore. All rights reserved. | Designed with <i class="fas fa-heart" style="color: var(--primary-red);"></i> for students</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript - Deferred for better performance -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script defer>
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
