<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-1">Profile</h2>
        <p class="text-muted mb-0">Manage your account details.</p>
    </x-slot>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header fw-semibold"><i class="fa-regular fa-user me-2"></i>Your Profile</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('student.profile.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="register_number" class="form-label">Register Number</label>
                                <input type="text" class="form-control" id="register_number" name="register_number" value="{{ old('register_number', $user->register_number) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password <span class="text-muted small">(leave blank to keep current)</span></label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
</x-app-layout>
