<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class SupabaseAuthController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Show registration form
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request): RedirectResponse
    {
        // Test session functionality before proceeding
        try {
            if (!$request->session()->isStarted()) {
                $request->session()->start();
            }
            $request->session()->put('_test', 'working');
            $test = $request->session()->get('_test');
            $request->session()->forget('_test');
            
            if ($test !== 'working') {
                throw new \RuntimeException('Session test failed');
            }
        } catch (\Exception $e) {
            \Log::error('Session initialization failed during registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'Session error. Please clear your browser cookies and try again.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,admin'],
        ]);

        try {
            // Register with Supabase Auth
            $supabaseResponse = $this->supabaseService->signUp(
                $validated['email'],
                $validated['password'],
                [
                    'name' => $validated['name'],
                    'role' => $validated['role']
                ]
            );

            // Debug: Log the response
            \Log::info('Supabase Registration Response', $supabaseResponse);
            \Log::info('Checking if ID exists', ['has_id' => isset($supabaseResponse['id']), 'id_value' => $supabaseResponse['id'] ?? 'NOT_SET']);

            if (isset($supabaseResponse['id'])) {
                \Log::info('Creating user in local database', [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'supabase_id' => $supabaseResponse['id']
                ]);
                
                // Create user in local database
                try {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'role' => $validated['role'],
                        'supabase_id' => $supabaseResponse['id'],
                        'is_verified' => false,
                        'is_approved' => false,
                        'status' => 'pending',
                    ]);
                    
                    \Log::info('User created successfully', ['user_id' => $user->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create user in database', [
                        'error' => $e->getMessage(),
                        'email' => $validated['email']
                    ]);
                    throw $e;
                }

                if ($validated['role'] === 'student') {
                    return redirect()->route('verification.notice')
                        ->with('status', 'Registration successful! Please check your email to verify your account. After email verification, an admin will review your application.');
                } else {
                    // Admin registration - auto approve
                    $user->update([
                        'email_verified_at' => now(),
                        'is_verified' => true,
                        'is_approved' => true,
                        'admin_approved_at' => now(),
                        'status' => 'approved'
                    ]);
                    
                    return redirect()->route('login')
                        ->with('status', 'Admin account created successfully! You can now login.');
                }
            }

            return back()->withErrors(['email' => 'Registration failed. Please try again.']);

        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Show login form
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle user login with local auth (Supabase disabled for performance)
     */
    public function login(Request $request): RedirectResponse
    {
        // Test session functionality before proceeding
        try {
            if (!$request->session()->isStarted()) {
                $request->session()->start();
            }
            $request->session()->put('_test', 'working');
            $test = $request->session()->get('_test');
            $request->session()->forget('_test');
            
            if ($test !== 'working') {
                throw new \RuntimeException('Session test failed');
            }
        } catch (\Exception $e) {
            \Log::error('Session initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'Session error. Please clear your browser cookies and try again.'
            ])->withInput($request->only('email'));
        }

        \Log::info('Login attempt started', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            // Use local authentication directly for better performance
            // Supabase is disabled to prevent timeouts on Render
            \Log::info('Using local authentication', ['email' => $credentials['email']]);
            
            try {
                $user = User::where('email', $credentials['email'])->first();
                \Log::info('User lookup complete', ['found' => !is_null($user)]);
            } catch (\Exception $dbError) {
                \Log::error('Database query failed', [
                    'error' => $dbError->getMessage(),
                ]);
                
                return back()->withErrors([
                    'email' => 'Database connection error. Please try again.'
                ]);
            }

            if ($user && Hash::check($credentials['password'], $user->password)) {
                \Log::info('Password verified', ['email' => $credentials['email']]);
                
                // Check if user is verified and approved
                if (!$user->is_verified) {
                    return back()->withErrors(['email' => 'Please verify your email before logging in.']);
                } elseif ($user->isAdmin() || $user->isApproved()) {
                    // Login user
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    
                    \Log::info('User logged in successfully', ['user_id' => $user->id, 'role' => $user->role]);
                    return $this->redirectToDashboard();
                } elseif ($user->isPendingApproval()) {
                    return back()->withErrors(['email' => 'Your account is pending admin approval.']);
                } elseif ($user->isRejected()) {
                    return back()->withErrors(['email' => 'Your account has been rejected. Please contact support.']);
                }
            }

            // Legacy Supabase fallback (disabled for performance)
            /*
            try {
                $supabaseResponse = $this->supabaseService->signIn($credentials['email'], $credentials['password']);

            */

            \Log::warning('Login failed', ['email' => $credentials['email']]);
            return back()->withErrors(['email' => 'Invalid credentials.']);

        } catch (\Exception $e) {
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'email' => $credentials['email'] ?? 'unknown',
            ]);
            
            return back()->withErrors([
                'email' => 'Login failed. Please try again.'
            ]);
        }
    }

    /**
     * Handle password reset request
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        try {
            $this->supabaseService->resetPasswordForEmail($request->email);
            
            return back()->with('status', 'Password reset link sent to your email address.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Show password reset form
     */
    public function showResetPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('landing');
    }

    /**
     * Show email verification notice
     */
    public function showVerificationNotice(): View
    {
        return view('auth.verify');
    }

    /**
     * Handle email verification callback from Supabase
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        $token = $request->get('token');
        $type = $request->get('type');

        if ($type === 'signup' && $token) {
            try {
                // Get user info from Supabase using the token
                $supabaseUser = $this->supabaseService->getUser($token);
                
                if ($supabaseUser && isset($supabaseUser['email_confirmed_at']) && $supabaseUser['email_confirmed_at']) {
                    // Update local user record
                    $user = User::where('supabase_id', $supabaseUser['id'])->first();
                    
                    if ($user) {
                        $user->update([
                            'email_verified_at' => now(),
                            'is_verified' => true,
                            'access_token' => $token,
                            'status' => $user->isAdmin() ? 'approved' : 'pending'
                        ]);

                        if ($user->isAdmin()) {
                            return redirect()->route('login')
                                ->with('status', 'Email verified successfully! You can now login.');
                        } else {
                            return redirect()->route('login')
                                ->with('status', 'Email verified successfully! Your account is now pending admin approval.');
                        }
                    }
                }
            } catch (\Exception $e) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Email verification failed. Please try again.']);
            }
        }

        return redirect()->route('login')
            ->withErrors(['email' => 'Invalid verification link.']);
    }

    /**
     * Handle Supabase auth callback
     */
    public function authCallback(Request $request): RedirectResponse
    {
        $accessToken = $request->get('access_token');
        $refreshToken = $request->get('refresh_token');
        $type = $request->get('type');
        $error = $request->get('error');
        $errorDescription = $request->get('error_description');

        // Log the callback parameters for debugging
        \Log::info('Supabase Callback Received', [
            'access_token' => $accessToken ? 'present' : 'missing',
            'refresh_token' => $refreshToken ? 'present' : 'missing',
            'type' => $type,
            'error' => $error,
            'error_description' => $errorDescription,
            'all_params' => $request->all()
        ]);

        // Handle errors from Supabase
        if ($error) {
            \Log::error('Supabase Callback Error', [
                'error' => $error,
                'description' => $errorDescription
            ]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Email verification failed: ' . ($errorDescription ?? $error)]);
        }

        if ($type === 'signup' && $accessToken) {
            try {
                // Get user info from Supabase using the access token
                $supabaseUser = $this->supabaseService->getUser($accessToken);
                
                \Log::info('Supabase User Data', [
                    'user_id' => $supabaseUser['id'] ?? 'missing',
                    'email' => $supabaseUser['email'] ?? 'missing',
                    'email_confirmed_at' => $supabaseUser['email_confirmed_at'] ?? 'missing',
                    'user_metadata' => $supabaseUser['user_metadata'] ?? 'missing'
                ]);
                
                if ($supabaseUser && isset($supabaseUser['email_confirmed_at']) && $supabaseUser['email_confirmed_at']) {
                    // Find or create user in local database
                    $user = User::where('supabase_id', $supabaseUser['id'])->first();
                    
                    if (!$user) {
                        // Create user if not exists
                        $user = User::create([
                            'name' => $supabaseUser['user_metadata']['name'] ?? explode('@', $supabaseUser['email'])[0],
                            'email' => $supabaseUser['email'],
                            'password' => Hash::make(Str::random(32)), // Random password since we use Supabase auth
                            'role' => $supabaseUser['user_metadata']['role'] ?? 'student',
                            'supabase_id' => $supabaseUser['id'],
                            'access_token' => $accessToken,
                            'is_verified' => true,
                            'is_approved' => false,
                            'email_verified_at' => now(),
                            'status' => 'pending'
                        ]);
                        
                        \Log::info('User created successfully', ['user_id' => $user->id]);
                    } else {
                        // Update existing user
                        $user->update([
                            'access_token' => $accessToken,
                            'is_verified' => true,
                            'email_verified_at' => now(),
                            'status' => $user->isAdmin() ? 'approved' : 'pending'
                        ]);
                        
                        \Log::info('User updated successfully', ['user_id' => $user->id]);
                    }

                    if ($user->isAdmin()) {
                        $user->update(['is_approved' => true, 'status' => 'approved']);
                        return redirect()->route('login')
                            ->with('status', 'Email verified successfully! You can now login.');
                    } else {
                        return redirect()->route('login')
                            ->with('status', 'Email verified successfully! Your account is now pending admin approval.');
                    }
                } else {
                    \Log::warning('Email not confirmed in Supabase', [
                        'email_confirmed_at' => $supabaseUser['email_confirmed_at'] ?? 'missing'
                    ]);
                    return redirect()->route('login')
                        ->withErrors(['email' => 'Email verification failed. Please try again.']);
                }
            } catch (\Exception $e) {
                \Log::error('Auth callback error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Email verification failed. Please try again.']);
            }
        }

        \Log::warning('Invalid callback parameters', [
            'type' => $type,
            'has_access_token' => !empty($accessToken)
        ]);
        
        return redirect()->route('login')
            ->withErrors(['email' => 'Invalid verification link.']);
    }

    /**
     * Show password reset form
     */
    public function showResetPasswordForm(Request $request): View
    {
        $token = $request->get('token');
        $type = $request->get('type');
        
        if ($type === 'recovery' && $token) {
            return view('auth.reset-password', compact('token'));
        }
        
        return redirect()->route('password.request')
            ->withErrors(['email' => 'Invalid reset link.']);
    }

    /**
     * Handle password reset
     */
    public function handlePasswordReset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Update password in Supabase
            $supabaseUser = $this->supabaseService->getUser($request->token);
            
            if ($supabaseUser) {
                // Update password in Supabase using the service role key
                $this->supabaseService->updateUserPassword($supabaseUser['id'], $request->password);
                
                // Update local user record
                $user = User::where('supabase_id', $supabaseUser['id'])->first();
                if ($user) {
                    $user->update([
                        'password' => Hash::make($request->password)
                    ]);
                }
                
                return redirect()->route('login')
                    ->with('status', 'Password updated successfully! You can now login with your new password.');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Password reset failed. Please try again.']);
        }

        return back()->withErrors(['password' => 'Invalid reset token.']);
    }

    /**
     * Redirect to appropriate dashboard
     */
    private function redirectToDashboard(): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}