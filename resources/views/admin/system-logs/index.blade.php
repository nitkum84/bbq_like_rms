@extends('admin.layouts.app')
@section('title', 'System Logs')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Logs
        <span class="subtitle">Laravel runtime logs for errors, warnings, and application flow diagnostics</span>
    </h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-file-earmark-text"></i></div><div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Visible Entries</div></div></div></div>
    <div class="col-md-3"><div class="stat-card red"><div class="stat-icon red"><i class="bi bi-bug"></i></div><div><div class="stat-value">{{ $stats['error'] }}</div><div class="stat-label">Errors</div></div></div></div>
    <div class="col-md-3"><div class="stat-card orange"><div class="stat-icon orange"><i class="bi bi-exclamation-triangle"></i></div><div><div class="stat-value">{{ $stats['warning'] }}</div><div class="stat-label">Warnings</div></div></div></div>
    <div class="col-md-3"><div class="stat-card green"><div class="stat-icon green"><i class="bi bi-info-circle"></i></div><div><div class="stat-value">{{ $stats['info'] }}</div><div class="stat-label">Info</div></div></div></div>
</div>

<div class="admin-card mb-4"><div class="admin-card-body">
    <form class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label">Log File</label><select name="file" class="form-select">@foreach($files as $file)<option value="{{ $file->getFilename() }}" @selected($selectedFile && $selectedFile->getFilename() === $file->getFilename())>{{ $file->getFilename() }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Level</label><select name="level" class="form-select"><option value="">All</option>@foreach(['ERROR','WARNING','INFO','DEBUG','CRITICAL','ALERT','EMERGENCY'] as $level)<option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Search</label><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Message or exception text"></div>
        <div class="col-md-2"><button class="btn btn-primary me-2" type="submit">Filter</button><a href="{{ route('admin.system-logs.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div></div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>Timestamp</th><th>Level</th><th>Message</th><th>Details</th></tr></thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry['timestamp'] ?: '-' }}</td>
                        <td><span class="status-badge {{ in_array($entry['level'], ['ERROR','CRITICAL','ALERT','EMERGENCY'], true) ? 'status-blocked' : ($entry['level'] === 'WARNING' ? 'status-pending' : 'status-active') }}">{{ $entry['level'] }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($entry['message'], 140) }}</div>
                            <div class="small text-muted">{{ $entry['environment'] ?: '-' }}</div>
                        </td>
                        <td style="min-width: 280px;">
                            <details>
                                <summary class="small text-primary" style="cursor:pointer;">View raw entry</summary>
                                <pre class="small mt-2 mb-0">{{ $entry['raw'] }}</pre>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No log entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
