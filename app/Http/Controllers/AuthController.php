<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SupabaseService;
use App\Services\FastAuthService; // Add this import
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    protected $supabaseService;
    protected $fastAuthService; // Add this property

    public function __construct(SupabaseService $supabaseService, FastAuthService $fastAuthService)
    {
        $this->supabaseService = $supabaseService;
        $this->fastAuthService = $fastAuthService; // Initialize the service
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
     * Note: Public registration is for students only.
     * Admin accounts should be created manually or through a separate admin registration process.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:student,admin'],
        ]);

        // Default to student role if not provided
        $role = $validated['role'] ?? 'student';

        try {
            // Create user in local database
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'is_verified' => true, // No email verification required
                'is_approved' => $role === 'admin' ? true : false, // Auto-approve admins
                'email_verified_at' => now(),
                'status' => $role === 'admin' ? 'approved' : 'pending',
            ]);

            // Try to create user in Supabase (optional - for future use)
            // Skip Supabase registration during registration to improve performance
            // This can be done in a background job if needed

            if ($role === 'admin') {
                $user->update([
                    'admin_approved_at' => now(),
                ]);
                
                return redirect()->route('login')
                    ->with('status', 'Admin account created successfully! You can now login.');
            } else {
                return redirect()->route('login')
                    ->with('status', 'Student registration successful! Your account is pending admin approval.');
            }

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
     * Handle user login with maximum performance optimizations
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Use our fast authentication service
        if ($this->fastAuthService->authenticate($request)) {
            $user = Auth::user();
            
            // Check approval status using optimized methods
            if ($this->fastAuthService->canUserLogin($user)) {
                $request->session()->regenerate();
                return $this->redirectToDashboard();
            } elseif ($this->fastAuthService->isUserPending($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account is pending admin approval.']);
            } elseif ($this->fastAuthService->isUserRejected($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account has been rejected. Please contact support.']);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        
        // Clear user login status cache
        if ($userId) {
            // Clear all user-related caches
            $cacheKeys = [
                "user_can_login_check_{$userId}",
                "user_is_rejected_check_{$userId}",
                "user_is_pending_check_{$userId}",
                "user_is_admin_{$userId}",
                "user_is_student_{$userId}",
                "user_is_approved_{$userId}",
                "user_can_login_{$userId}",
                "user_is_pending_{$userId}",
                "user_is_rejected_{$userId}",
                "user_dashboard_route_{$userId}",
                "user_login_status_{$userId}"
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
        }
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('landing');
    }

    /**
     * Redirect to appropriate dashboard
     */
    private function redirectToDashboard(): RedirectResponse
    {
        $user = Auth::user();
        
        // Cache dashboard route for 1 hour to reduce repeated logic
        $dashboardRouteKey = "user_dashboard_route_" . $user->id;
        $route = Cache::remember($dashboardRouteKey, 3600, function() use ($user) {
            return $user->isAdmin() ? 'admin.dashboard' : 'student.dashboard';
        });
        
        return redirect()->route($route);
    }
}