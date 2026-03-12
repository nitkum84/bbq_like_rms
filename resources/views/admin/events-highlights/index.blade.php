@extends('admin.layouts.app')
@section('title', 'Events & Highlights')

@section('content')
<div class="page-header">
    <h1 class="page-title">Events & Highlights
        <span class="subtitle">Manage homepage banners, specials, and display periods</span>
    </h1>
    <a href="{{ route('admin.events-highlights.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Highlight</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon blue"><i class="bi bi-images"></i></div><div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total</div></div></div></div>
    <div class="col-md-3"><div class="stat-card green"><div class="stat-icon green"><i class="bi bi-check2-circle"></i></div><div><div class="stat-value">{{ $stats['active'] }}</div><div class="stat-label">Active</div></div></div></div>
    <div class="col-md-3"><div class="stat-card orange"><div class="stat-icon orange"><i class="bi bi-calendar2-check"></i></div><div><div class="stat-value">{{ $stats['current'] }}</div><div class="stat-label">Current</div></div></div></div>
    <div class="col-md-3"><div class="stat-card purple"><div class="stat-icon purple"><i class="bi bi-hourglass-split"></i></div><div><div class="stat-value">{{ $stats['expired'] }}</div><div class="stat-label">Expired</div></div></div></div>
</div>

<div class="admin-card mb-4"><div class="admin-card-body">
    <form class="row g-3 align-items-end">
        <div class="col-md-5"><label class="form-label">Search</label><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Title"></div>
        <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></div>
        <div class="col-md-4"><button type="submit" class="btn btn-primary me-2">Filter</button><a href="{{ route('admin.events-highlights.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
</div></div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>Title</th><th>Period</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($highlights as $highlight)
                    <tr>
                        <td><div class="fw-semibold">{{ $highlight->title }}</div><div class="text-muted small">{{ \Illuminate\Support\Str::limit($highlight->description, 70) }}</div></td>
                        <td>{{ $highlight->display_from?->format('d M Y') }} - {{ $highlight->display_to?->format('d M Y') }}</td>
                        <td>{{ $highlight->display_order }}</td>
                        <td>
                            @if($highlight->is_active && $highlight->display_from <= today() && $highlight->display_to >= today())
                                <span class="status-badge status-active">Current</span>
                            @elseif(! $highlight->is_active)
                                <span class="status-badge status-blocked">Inactive</span>
                            @elseif($highlight->display_to < today())
                                <span class="status-badge status-inactive">Expired</span>
                            @else
                                <span class="status-badge status-pending">Scheduled</span>
                            @endif
                        </td>
                        <td><div class="d-flex gap-1"><a href="{{ route('admin.events-highlights.show', $highlight) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a><a href="{{ route('admin.events-highlights.edit', $highlight) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a><form action="{{ route('admin.events-highlights.destroy', $highlight) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this highlight?"><i class="bi bi-trash"></i></button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No highlights found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($highlights->hasPages())<div class="px-4 py-3 border-top">{{ $highlights->links() }}</div>@endif
</div>
@endsection
