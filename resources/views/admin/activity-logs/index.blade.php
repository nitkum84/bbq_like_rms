@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')
<div class="page-header">
    <h1 class="page-title">Activity Logs
        <span class="subtitle">Admin-only audit trail for booking, enquiry, content, and system actions</span>
    </h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-journal-text"></i></div><div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Logs</div></div></div></div>
    <div class="col-md-3"><div class="stat-card green"><div class="stat-icon green"><i class="bi bi-calendar-check"></i></div><div><div class="stat-value">{{ $stats['today'] }}</div><div class="stat-label">Today</div></div></div></div>
    <div class="col-md-3"><div class="stat-card orange"><div class="stat-icon orange"><i class="bi bi-plus-circle"></i></div><div><div class="stat-value">{{ $stats['created'] }}</div><div class="stat-label">Created</div></div></div></div>
    <div class="col-md-3"><div class="stat-card purple"><div class="stat-icon purple"><i class="bi bi-pencil-square"></i></div><div><div class="stat-value">{{ $stats['updated'] }}</div><div class="stat-label">Updated</div></div></div></div>
</div>

<div class="admin-card mb-4"><div class="admin-card-body">
    <form class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Description or route"></div>
        <div class="col-md-2"><label class="form-label">Event</label><select name="event" class="form-select"><option value="">All</option>@foreach($events as $event)<option value="{{ $event }}" @selected(request('event') === $event)>{{ ucfirst(str_replace('_', ' ', $event)) }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Module</label><select name="subject_type" class="form-select"><option value="">All</option>@foreach($subjectTypes as $subjectType)<option value="{{ $subjectType }}" @selected(request('subject_type') === $subjectType)>{{ class_basename($subjectType) }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Admin</label><select name="causer_id" class="form-select"><option value="">All</option>@foreach($admins as $admin)<option value="{{ $admin->id }}" @selected(request('causer_id') == $admin->id)>{{ $admin->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-primary me-2" type="submit">Filter</button><a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div></div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>When</th><th>Event</th><th>Module</th><th>Description</th><th>Admin</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                        <td><span class="status-badge status-{{ $log->event === 'deleted' ? 'blocked' : ($log->event === 'created' ? 'active' : 'pending') }}">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</span></td>
                        <td>{{ $log->subject_type ? class_basename($log->subject_type) : '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $log->description ?: '-' }}</div>
                            <div class="small text-muted">{{ $log->route_name ?: $log->url }}</div>
                        </td>
                        <td>{{ $log->causer?->name ?? 'System' }}</td>
                        <td><a href="{{ route('admin.activity-logs.show', $log) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No activity logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="px-4 py-3 border-top">{{ $logs->links() }}</div>@endif
</div>
@endsection
