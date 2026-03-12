@extends('admin.layouts.app')
@section('title', 'Activity Log Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Activity Log Details
        <span class="subtitle">{{ $activityLog->description ?: ucfirst(str_replace('_', ' ', $activityLog->event)) }}</span>
    </h1>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Summary</h5></div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Event</span><strong>{{ ucfirst(str_replace('_', ' ', $activityLog->event)) }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Module</span><strong>{{ $activityLog->subject_type ? class_basename($activityLog->subject_type) : '-' }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Subject ID</span><strong>{{ $activityLog->subject_id ?: '-' }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Admin</span><strong>{{ $activityLog->causer?->name ?? 'System' }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Route</span><strong>{{ $activityLog->route_name ?: '-' }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span class="text-muted">When</span><strong>{{ $activityLog->created_at?->format('d M Y, h:i A') }}</strong></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Request Metadata</h5></div>
            <div class="admin-card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="small text-muted">Method</div><div class="fw-semibold">{{ $activityLog->method ?: '-' }}</div></div>
                    <div class="col-md-6"><div class="small text-muted">IP Address</div><div class="fw-semibold">{{ $activityLog->ip_address ?: '-' }}</div></div>
                    <div class="col-12"><div class="small text-muted">URL</div><div class="fw-semibold">{{ $activityLog->url ?: '-' }}</div></div>
                    <div class="col-12"><div class="small text-muted">User Agent</div><div class="fw-semibold">{{ $activityLog->user_agent ?: '-' }}</div></div>
                </div>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h5>Properties</h5></div>
            <div class="admin-card-body">
                <pre class="mb-0 small">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</div>
@endsection
