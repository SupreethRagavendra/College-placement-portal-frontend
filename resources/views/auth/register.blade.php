<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - KIT Placement Training Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            background: linear-gradient(135deg, var(--black) 0%, var(--dark-gray) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: var(--text-dark);
            padding: 40px 0;
        }

        .auth-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(220, 20, 60, 0.1);
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="80" r="1" fill="white" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .auth-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .auth-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .auth-header .brand-logo {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--primary-red);
            position: relative;
            z-index: 1;
        }

        .auth-body {
            padding: 3rem 2rem;
            background: var(--white);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--light-gray);
            font-family: 'Figtree', sans-serif;
        }

        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
            background: var(--white);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--primary-red) 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220, 20, 60, 0.5);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .text-danger {
            font-size: 14px;
            margin-top: 8px;
            color: var(--primary-red);
        }

        .auth-link {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-link:hover {
            color: var(--dark-red);
            text-decoration: none;
        }

        .back-to-home {
            position: absolute;
            top: 30px;
            left: 30px;
            color: var(--white);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .back-to-home:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.1) 0%, rgba(185, 28, 28, 0.1) 100%);
            color: var(--text-dark);
            border-left: 4px solid var(--primary-red);
        }

        .alert-info strong {
            color: var(--primary-red);
            font-weight: 700;
        }

        .alert-info ul {
            padding-left: 20px;
        }

        .alert-info li {
            font-weight: 500;
            margin-bottom: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-header {
                padding: 2rem 1.5rem;
            }
            
            .auth-body {
                padding: 2rem 1.5rem;
            }
            
            .back-to-home {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('landing') }}" class="back-to-home">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="brand-logo">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h2 class="mb-0">Join KIT Training</h2>
                        <p class="mb-0">Register for Placement Training Portal</p>
                    </div>
                    <div class="auth-body">
                        <!-- Registration Info -->
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Training Portal Registration:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Your registration requires admin approval for access</li>
                                <li>Email notification will be sent upon approval</li>
                                <li>Access training assessments, AI chatbot & analytics</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('register') }}" accept-charset="UTF-8">
                            @csrf
                            
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                                <input id="name" 
                                       type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="name"
                                       placeholder="Enter your full name">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Register Number -->
                            <div class="mb-3">
                                <label for="register_number" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>Register Number
                                </label>
                                <input id="register_number" 
                                       type="text" 
                                       class="form-control @error('register_number') is-invalid @enderror" 
                                       name="register_number" 
                                       value="{{ old('register_number') }}" 
                                       required 
                                       autocomplete="off"
                                       placeholder="e.g., 711524mmc043">
                                @error('register_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="username"
                                       placeholder="Enter your email address">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Create a strong password">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Must be 8+ characters with uppercase, lowercase, numbers & symbols. 
                                </small>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <input id="password_confirmation" 
                                       type="password" 
                                       class="form-control" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Confirm your password">
                            </div>

                            <!-- Hidden role field -->
                            <input type="hidden" name="role" value="student">

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-graduate me-2"></i>Register for Training Portal
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="mb-0">
                                    Already registered with KIT? 
                                    <a href="{{ route('login') }}" class="auth-link">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login to Portal
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Prevent 419 CSRF token errors
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.querySelector('form[method="POST"]');
            
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    // Get the latest CSRF token from the meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const csrfInput = registerForm.querySelector('input[name="_token"]');
                    
                    if (csrfToken && csrfInput) {
                        csrfInput.value = csrfToken.content;
                    }
                });
            }
            
            // Reload page if it's been open too long (prevent stale tokens)
            let pageLoadTime = Date.now();
            setInterval(function() {
                let timeSinceLoad = (Date.now() - pageLoadTime) / 1000 / 60; // in minutes
                if (timeSinceLoad > 60) { // If page open for more than 1 hour
                    console.log('Page has been open too long, refreshing to prevent token expiry...');
                    location.reload();
                }
            }, 60000); // Check every minute
        });
    </script>
</body>
</html>