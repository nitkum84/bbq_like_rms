@extends('admin.layouts.app')
@section('title','Broadcast Notification')

@section('content')
<div class="page-header">
    <h1 class="page-title">Broadcast Notifications
        <span class="subtitle">Send SMS / Email to users</span>
    </h1>
    <a href="{{ route('admin.notifications.logs') }}" class="btn btn-outline-secondary">
        <i class="bi bi-clock-history me-1"></i>View Logs
    </a>
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
                    <option value="sms">📱 SMS Only</option>
                    <option value="email">📧 Email Only</option>
                    <option value="both">📱📧 Both SMS & Email</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Target Audience</label>
                <select name="target" class="form-select">
                    <option value="all">All Users ({{ $totalUsers }})</option>
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
            <div class="text-muted small mt-1">For SMS: Keep under 160 chars for single SMS. Use {name} as placeholder.</div>
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
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>SMS limited to 160 chars per message</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>All sends are logged in Notification Logs</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use {name} placeholder for personalization</li>
            <li><i class="bi bi-info-circle text-info me-2"></i>Large sends are processed in background queue</li>
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
