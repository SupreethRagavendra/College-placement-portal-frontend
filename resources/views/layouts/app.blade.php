<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>College Placement Portal</title>

        <!-- Preload Critical Resources -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        
        <!-- Fonts with font-display swap -->
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
        
        <style>
            body { padding-top: 56px; }
            
            /* Page header styles */
            .page-header {
                background: linear-gradient(135deg, #7e22ce 0%, #581c87 100%);
                color: white;
                padding: 2rem 0;
                margin-bottom: 2rem;
                border-radius: 0 0 30px 30px;
                box-shadow: 0 10px 30px rgba(126, 34, 206, 0.2);
            }
            
            .page-header h1 {
                font-weight: 700;
                margin: 0;
            }
            
            /* Content area */
            .content-wrapper {
                background: white;
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
                margin-bottom: 2rem;
            }
            
            /* Footer */
            .kit-footer {
                background: linear-gradient(135deg, #581c87 0%, #7e22ce 100%);
                color: white;
                padding: 2rem 0;
                margin-top: 3rem;
            }
        </style>
        
        <!-- Page Specific Styles -->
        @yield('styles')
    </head>
    <body>
        @include('layouts.navigation')

        <!-- Page Heading -->
        @hasSection('header')
            <header class="page-header kit-pattern">
                <div class="container">
                    @yield('header')
                </div>
            </header>
        @elseif(isset($header))
            <header class="page-header kit-pattern">
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="container">
            <div class="content-wrapper">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </main>

        <!-- Footer -->
        <footer class="kit-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ asset('css/logo1-removebg-preview.png') }}" alt="KIT College Logo" style="height: 60px; margin-right: 15px; filter: brightness(0) invert(1);" width="60" height="60" loading="lazy">
                            <div>
                                <h5 class="mb-0" style="font-weight: 700;">KIT COIMBATORE</h5>
                                <small style="opacity: 0.9;">Excellence Beyond Expectation</small>
                            </div>
                        </div>
                        <p style="opacity: 0.9; font-size: 0.9rem;">
                            Kalaignar Karunanidhi Institute of Technology - Empowering students with world-class placement opportunities.
                        </p>
                    </div>
                    <div class="col-md-3">
                        <h6 style="font-weight: 600; margin-bottom: 1rem;">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" style="opacity: 0.9;">About KIT</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" style="opacity: 0.9;">Placement Cell</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" style="opacity: 0.9;">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6 style="font-weight: 600; margin-bottom: 1rem;">Connect With Us</h6>
                        <div class="d-flex gap-3">
                            <a href="#" class="text-white" style="font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-white" style="font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white" style="font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-white" style="font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 2rem 0 1rem;">
                <div class="text-center" style="opacity: 0.9;">
                    <small>&copy; {{ date('Y') }} KIT Coimbatore. All rights reserved.</small>
                </div>
            </div>
        </footer>

        <!-- JavaScript - Deferred -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
        
        <!-- Page Specific Scripts -->
        @yield('scripts')
    </body>
</html>
