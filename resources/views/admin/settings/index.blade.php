@extends('admin.layouts.app')
@section('title','Website Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Website Settings</h1>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <!-- General -->
        <div class="admin-card mb-4"><div class="admin-card-header"><h5>General Information</h5></div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                    <input type="text" name="restaurant_name" class="form-control" value="{{ $settings['restaurant_name'] ?? '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                    <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="contact_mobile" class="form-control" value="{{ $settings['contact_mobile'] ?? '' }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ $settings['address'] ?? '' }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Booking Confirmation Note</label>
                    <textarea name="booking_note" class="form-control" rows="2" placeholder="Displayed on booking confirmation page">{{ $settings['booking_note'] ?? '' }}</textarea>
                </div>
            </div>
        </div></div>

        <!-- Social Links -->
        <div class="admin-card mb-4"><div class="admin-card-header"><h5>Social Links</h5></div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-facebook me-1 text-primary"></i>Facebook URL</label>
                    <input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}" placeholder="https://facebook.com/...">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-instagram me-1 text-danger"></i>Instagram URL</label>
                    <input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}" placeholder="https://instagram.com/...">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-geo-alt me-1 text-success"></i>Google Maps URL</label>
                    <input type="url" name="google_maps_url" class="form-control" value="{{ $settings['google_maps_url'] ?? '' }}" placeholder="https://maps.google.com/...">
                </div>
            </div>
        </div></div>

        <!-- Maintenance -->
        <div class="admin-card"><div class="admin-card-header"><h5>Site Control</h5></div>
        <div class="admin-card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintMode" {{ ($settings['maintenance_mode'] ?? '') == '1' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="maintMode">
                    Maintenance Mode
                    <span class="text-muted fw-normal small ms-1">— Site will show maintenance page to visitors</span>
                </label>
            </div>
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card sticky-top" style="top:80px">
            <div class="admin-card-header"><h5>Logo</h5></div>
            <div class="admin-card-body text-center">
                @if($settings['logo'] ?? false)
                    <img src="{{ asset('storage/'.$settings['logo']) }}" class="img-preview mb-3" style="width:150px;height:auto">
                @else
                    <div class="mb-3 text-muted"><i class="bi bi-image fs-2"></i><div class="small mt-1">No logo uploaded</div></div>
                @endif
                <input type="file" name="logo" class="form-control mb-3" accept="image/*">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-2"></i>Save All Settings
                </button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
