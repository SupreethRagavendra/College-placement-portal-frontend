<p class="text-muted mb-4">
    <i class="fas fa-info-circle me-1"></i>{{ __("Update your account's profile information and email address.") }}
</p>

<form method="post" action="{{ route('profile.update') }}" id="profile-update-form">
    @csrf
    @method('patch')

    <div class="mb-4">
        <label for="name" class="form-label">
            <i class="fas fa-user me-1" style="color: #DC143C;"></i>{{ __('Name') }}
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="fas fa-envelope me-1" style="color: #DC143C;"></i>{{ __('Email') }}
        </label>
        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled readonly style="background-color: #f8f9fa;">
        <small class="form-text text-muted">
            <i class="fas fa-lock me-1"></i>Email address cannot be changed for security reasons.
        </small>
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-save" id="save-profile-btn">
            <span class="btn-text"><i class="fas fa-save me-2"></i>{{ __('Save Changes') }}</span>
            <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
        </button>

        @if (session('status') === 'profile-updated')
            <span class="text-success fw-semibold">
                <i class="fas fa-check-circle me-1"></i>{{ __('Saved successfully!') }}
            </span>
        @endif

        @if (session('error'))
            <span class="text-danger fw-semibold">
                <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
            </span>
        @endif
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-update-form');
    const saveBtn = document.getElementById('save-profile-btn');
    
    if (form && saveBtn) {
        form.addEventListener('submit', function(e) {
            // Show loading state
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.btn-loading').classList.remove('d-none');
            
            // Allow form to submit normally
            return true;
        });
    }
});
</script>
