@extends('admin.layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Profile
        <span class="subtitle">Update your admin account details without changing the login email</span>
    </h1>
</div>

<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="admin-card-header"><h5>Profile Details</h5></div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $admin->mobile) }}">
                            @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ $admin->email }}" readonly disabled>
                            <div class="form-text">Email is locked for admin profile updates.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control @error('profile_image') is-invalid @enderror" accept="image/*">
                            @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h5>Change Password</h5></div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="form-text mt-2">Leave password fields empty if you only want to update profile details.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card sticky-top" style="top:80px">
                <div class="admin-card-header"><h5>Account Preview</h5></div>
                <div class="admin-card-body text-center">
                    <img src="{{ $admin->profile_image_url }}" alt="Admin Profile" class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <div class="fw-semibold">{{ $admin->name }}</div>
                    <div class="text-muted small mb-3">{{ $admin->email }}</div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-2"></i>Save Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
