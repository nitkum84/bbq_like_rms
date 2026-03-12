@extends('admin.layouts.app')
@section('title','Broadcast Notification')

@section('content')
<div class="page-header">
    <h1 class="page-title">Broadcast Notifications
        <span class="subtitle">Send SMS and email campaigns to active users</span>
    </h1>
    <a href="{{ route('admin.notifications.logs') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>View Logs
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-people"></i></div><div><div class="stat-value">{{ $stats['total_users'] }}</div><div class="stat-label">Active Users</div></div></div></div>
    <div class="col-md-3"><div class="stat-card green"><div class="stat-icon green"><i class="bi bi-calendar-check"></i></div><div><div class="stat-value">{{ $stats['upcoming_users'] }}</div><div class="stat-label">Upcoming Booking Users</div></div></div></div>
    <div class="col-md-3"><div class="stat-card orange"><div class="stat-icon orange"><i class="bi bi-phone"></i></div><div><div class="stat-value">{{ $stats['sms_logs'] }}</div><div class="stat-label">SMS Logs</div></div></div></div>
    <div class="col-md-3"><div class="stat-card purple"><div class="stat-icon purple"><i class="bi bi-envelope"></i></div><div><div class="stat-value">{{ $stats['email_logs'] }}</div><div class="stat-label">Email Logs</div></div></div></div>
</div>

<div class="row">
<div class="col-lg-7">
<div class="admin-card"><div class="admin-card-header"><h5>Compose Notification</h5></div>
<div class="admin-card-body">
    <form action="{{ route('admin.notifications.send') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Channel</label>
                <select name="channel" class="form-select" id="channelSelect">
                    <option value="sms">SMS Only</option>
                    <option value="email">Email Only</option>
                    <option value="both">Both SMS & Email</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Target Audience</label>
                <select name="target" class="form-select">
                    <option value="all">All Users ({{ $stats['total_users'] }})</option>
                    <option value="upcoming">Users with Upcoming Bookings</option>
                </select>
            </div>
        </div>
        <div class="mb-3" id="subjectField">
            <label class="form-label">Email Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="e.g., Special Weekend Offer!">
        </div>
        <div class="mb-3">
            <label class="form-label">Message <span class="text-danger">*</span></label>
            <textarea name="message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
            <div class="text-muted small mt-1">Use `{name}` to personalize the message for each user.</div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Send notification to selected users?')">
                <i class="bi bi-send me-2"></i>Send Now
            </button>
        </div>
    </form>
</div></div>
</div>
<div class="col-lg-5">
    <div class="admin-card"><div class="admin-card-header"><h5>Guidelines</h5></div>
    <div class="admin-card-body">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>DLT Compliance:</strong> For SMS, use only DLT-approved templates. Promotional SMS must use <code>channel=Promo</code>.
        </div>
        <ul class="list-unstyled">
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Broadcasts can target all users or upcoming-booking users</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>All sends are logged in Notification Logs</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use {name} placeholder for personalization</li>
            <li><i class="bi bi-info-circle text-info me-2"></i>Email subject is required for email and both-channel sends</li>
        </ul>
    </div></div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('channelSelect').addEventListener('change', function() {
    document.getElementById('subjectField').style.display = this.value === 'sms' ? 'none' : '';
});
</script>
@endpush
