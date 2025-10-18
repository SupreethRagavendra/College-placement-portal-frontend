<p class="text-muted mb-4">
    <i class="fas fa-shield-alt me-1"></i>{{ __('Ensure your account is using a long, random password to stay secure.') }}
</p>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">
            <i class="fas fa-key me-1" style="color: #ffc107;"></i>{{ __('Current Password') }}
        </label>
        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="update_password_current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">
            <i class="fas fa-lock me-1" style="color: #ffc107;"></i>{{ __('New Password') }}
        </label>
        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="update_password_password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="update_password_password_confirmation" class="form-label">
            <i class="fas fa-check-circle me-1" style="color: #ffc107;"></i>{{ __('Confirm Password') }}
        </label>
        <input type="password" class="form-control" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-update-password">
            <i class="fas fa-key me-2"></i>{{ __('Update Password') }}
        </button>

        @if (session('status') === 'password-updated')
            <span class="text-success fw-semibold">
                <i class="fas fa-check-circle me-1"></i>{{ __('Password updated!') }}
            </span>
        @endif
    </div>
</form>
