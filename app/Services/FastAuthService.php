<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class FastAuthService
{
    /**
     * Fast authentication method that bypasses external services
     */
    public function authenticate(Request $request): bool
    {
        // Check if fast auth is enabled
        if (!config('auth.fast_auth.enabled', true)) {
            // Fall back to standard Laravel auth
            return Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));
        }
        
        $credentials = $request->only('email', 'password');
        
        // Check if we have a cached authentication result
        $cacheKey = "auth_" . md5($credentials['email'] . $credentials['password']);
        $cachedResult = Cache::get($cacheKey);
        
        if ($cachedResult !== null) {
            if ($cachedResult === 'invalid') {
                return false;
            }
            
            // Valid cached user ID
            $user = User::find($cachedResult);
            if ($user) {
                Auth::login($user, $request->boolean('remember'));
                return true;
            }
        }
        
        // Perform database lookup
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Cache successful authentication for 5 minutes
            $cacheTtl = config('auth.fast_auth.cache_ttl', 300);
            Cache::put($cacheKey, $user->id, $cacheTtl);
            Auth::login($user, $request->boolean('remember'));
            return true;
        }
        
        // Cache failed authentication for 1 minute
        Cache::put($cacheKey, 'invalid', 60);
        return false;
    }
    
    /**
     * Check if user can login (optimized version)
     */
    public function canUserLogin(User $user): bool
    {
        // Use cached values for user status checks
        $canLoginKey = "user_can_login_check_{$user->id}";
        $cacheTtl = config('auth.fast_auth.cache_ttl', 300);
        return Cache::remember($canLoginKey, $cacheTtl, function() use ($user) {
            return $user->isAdmin() || ($user->isStudent() && $user->is_verified && $user->is_approved);
        });
    }
    
    /**
     * Check if user is rejected (optimized version)
     */
    public function isUserRejected(User $user): bool
    {
        $isRejectedKey = "user_is_rejected_check_{$user->id}";
        $cacheTtl = config('auth.fast_auth.cache_ttl', 300);
        return Cache::remember($isRejectedKey, $cacheTtl, function() use ($user) {
            return $user->isStudent() && $user->admin_rejected_at !== null;
        });
    }
    
    /**
     * Check if user is pending approval (optimized version)
     */
    public function isUserPending(User $user): bool
    {
        $isPendingKey = "user_is_pending_check_{$user->id}";
        $cacheTtl = config('auth.fast_auth.cache_ttl', 300);
        return Cache::remember($isPendingKey, $cacheTtl, function() use ($user) {
            return $user->isStudent() && $user->is_verified && !$user->is_approved && $user->admin_rejected_at === null;
        });
    }
}