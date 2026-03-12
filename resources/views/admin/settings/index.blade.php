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

            <div class="admin-card mb-4"><div class="admin-card-header"><h5>Email Settings</h5></div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-semibold">SMTP Configuration</div>
                        <div class="text-muted small">Stored in settings so the client can update email delivery without editing environment files.</div>
                    </div>
                    <span class="status-badge {{ $statuses['email'] ? 'status-active' : 'status-inactive' }}">{{ $statuses['email'] ? 'Configured' : 'Incomplete' }}</span>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="email_enabled" value="1" id="emailEnabled" {{ ($settings['email_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="emailEnabled">Enable email sending</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mailer</label>
                        <select name="mail_mailer" class="form-select">
                            <option value="smtp" @selected(($settings['mail_mailer'] ?? 'smtp') === 'smtp')>SMTP</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp-relay.brevo.com">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Port</label>
                        <input type="number" name="mail_port" class="form-control" value="{{ $settings['mail_port'] ?? '587' }}" placeholder="587">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Username</label>
                        <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="text" name="mail_password" class="form-control" value="{{ $settings['mail_password'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Encryption</label>
                        <select name="mail_encryption" class="form-select">
                            <option value="tls" @selected(($settings['mail_encryption'] ?? 'tls') === 'tls')>TLS</option>
                            <option value="ssl" @selected(($settings['mail_encryption'] ?? '') === 'ssl')>SSL</option>
                            <option value="none" @selected(blank($settings['mail_encryption'] ?? null))>None</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From Address</label>
                        <input type="email" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ $settings['mail_from_name'] ?? ($settings['restaurant_name'] ?? '') }}">
                    </div>
                </div>
            </div></div>

            <div class="admin-card mb-4"><div class="admin-card-header"><h5>SMS Settings</h5></div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-semibold">Gateway Configuration</div>
                        <div class="text-muted small">Used for OTP, reminders, and admin broadcasts.</div>
                    </div>
                    <span class="status-badge {{ $statuses['sms'] ? 'status-active' : 'status-inactive' }}">{{ $statuses['sms'] ? 'Configured' : 'Incomplete' }}</span>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="sms_enabled" value="1" id="smsEnabled" {{ ($settings['sms_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="smsEnabled">Enable SMS sending</label>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Gateway User</label>
                        <input type="text" name="sms_user" class="form-control" value="{{ $settings['sms_user'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gateway Password</label>
                        <input type="text" name="sms_password" class="form-control" value="{{ $settings['sms_password'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sender ID</label>
                        <input type="text" name="sms_sender_id" class="form-control" value="{{ $settings['sms_sender_id'] ?? '' }}" placeholder="RESTRO">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Route</label>
                        <input type="text" name="sms_route" class="form-control" value="{{ $settings['sms_route'] ?? '4' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PE ID</label>
                        <input type="text" name="sms_pe_id" class="form-control" value="{{ $settings['sms_pe_id'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Send API URL</label>
                        <input type="url" name="sms_base_url" class="form-control" value="{{ $settings['sms_base_url'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Delivery API URL</label>
                        <input type="url" name="sms_delivery_url" class="form-control" value="{{ $settings['sms_delivery_url'] ?? '' }}">
                    </div>
                </div>
            </div></div>

            <div class="admin-card"><div class="admin-card-header"><h5>Site Control</h5></div>
            <div class="admin-card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" id="maintMode" {{ ($settings['maintenance_mode'] ?? '') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="maintMode">
                        Maintenance Mode
                        <span class="text-muted fw-normal small ms-1">- Site will show maintenance page to visitors</span>
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

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Test Email</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.settings.test-email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" name="test_email_to" class="form-control" value="{{ old('test_email_to', $settings['contact_email'] ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="test_email_subject" class="form-control" value="{{ old('test_email_subject', 'Test Email from Admin Settings') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="test_email_message" class="form-control" rows="4">{{ old('test_email_message', 'This is a test email sent from the admin settings module.') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">Send Test Email</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Test SMS</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.settings.test-sms') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="test_sms_mobile" class="form-control" value="{{ old('test_sms_mobile', $settings['contact_mobile'] ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="test_sms_message" class="form-control" rows="4">{{ old('test_sms_message', 'This is a test SMS sent from the admin settings module.') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">Send Test SMS</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
