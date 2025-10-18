<?php

namespace App\Http\Controllers;

use App\Mail\AccountDeletionConfirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Validate only the name field
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);
            
            $user = $request->user();
            
            // Update only the name field
            $user->name = $validated['name'];
            $user->save();
            
            \Log::info('Profile updated successfully', [
                'user_id' => $user->id,
                'name' => $user->name
            ]);

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Exception $e) {
            \Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return Redirect::route('profile.edit')->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Initiate account deletion process - sends confirmation email
     */
    public function initiateDestroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Generate a unique token
        $token = Str::random(64);
        
        // Store token in database with expiry
        DB::table('account_deletion_tokens')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Generate confirmation and cancellation URLs
        $confirmUrl = route('profile.destroy.confirm', ['token' => $token]);
        $cancelUrl = route('profile.destroy.cancel', ['token' => $token]);
        
        // Send confirmation email
        Mail::to($user->email)->send(new AccountDeletionConfirmation($user, $confirmUrl, $cancelUrl));
        
        return back()->with('status', 'deletion-email-sent');
    }
    
    /**
     * Confirm account deletion via email token
     */
    public function confirmDestroy(Request $request, string $token): RedirectResponse
    {
        // Find valid token
        $tokenRecord = DB::table('account_deletion_tokens')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$tokenRecord) {
            return Redirect::to('/')->with('error', 'Invalid or expired deletion link.');
        }
        
        // Find user
        $user = \App\Models\User::find($tokenRecord->user_id);
        
        if (!$user) {
            return Redirect::to('/')->with('error', 'User not found.');
        }
        
        // Delete token
        DB::table('account_deletion_tokens')->where('token', $token)->delete();
        
        // Log out if this is the current user
        if (Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        // Delete user account
        $user->delete();
        
        return Redirect::to('/')->with('success', 'Your account has been permanently deleted.');
    }
    
    /**
     * Cancel account deletion via email token
     */
    public function cancelDestroy(string $token): RedirectResponse
    {
        // Delete the token
        $deleted = DB::table('account_deletion_tokens')
            ->where('token', $token)
            ->delete();
            
        if ($deleted) {
            return Redirect::route('profile.edit')->with('status', 'deletion-cancelled');
        }
        
        return Redirect::to('/')->with('error', 'Invalid or expired cancellation link.');
    }
    
    /**
     * Legacy destroy method - redirects to new flow
     */
    public function destroy(Request $request): RedirectResponse
    {
        return $this->initiateDestroy($request);
    }
}
