@extends('admin.layouts.app')
@section('title', 'Tables')

@section('content')
<div class="page-header">
    <h1 class="page-title">Table Management
        <span class="subtitle">Manage restaurant dining tables</span>
    </h1>
    <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Table
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-grid-3x3-gap"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Tables</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">Active Tables</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['booked_today'] }}</div>
                <div class="stat-label">Booked Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-slash-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['blocked'] }}</div>
                <div class="stat-label">Blocked Tables</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Table number or location" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Location</label>
                <select name="location" class="form-select">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Min Capacity</label>
                <input type="number" name="capacity" min="1" class="form-control" value="{{ request('capacity') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h5><i class="bi bi-grid me-2"></i>Table Overview - Today</h5>
        <div class="d-flex gap-3 small">
            <span><span class="badge" style="background:#eafaf1;color:#27ae60">.</span> Available</span>
            <span><span class="badge" style="background:#fdf2f2;color:#c0392b">.</span> Booked</span>
            <span><span class="badge bg-secondary">.</span> Blocked/Inactive</span>
        </div>
    </div>
    <div class="admin-card-body">
        <div class="table-grid">
            @forelse($tables as $t)
                @php
                    $cls = $t->status !== 'active' ? 'blocked' : ($t->today_bookings_count > 0 ? 'booked' : 'available');
                @endphp
                <a href="{{ route('admin.tables.show', $t) }}" class="table-item {{ $cls }} text-decoration-none text-dark">
                    <div class="table-num">{{ $t->table_number }}</div>
                    <div class="table-cap"><i class="bi bi-people me-1"></i>{{ $t->seating_capacity }}</div>
                    <div class="small mt-1 text-muted">{{ $t->location ?? 'Main Floor' }}</div>
                    <div class="small mt-1 fw-semibold">
                        @if($t->status !== 'active')
                            {{ ucfirst($t->status) }}
                        @elseif($t->today_bookings_count > 0)
                            Booked ({{ $t->today_bookings_count }})
                        @else
                            Available
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-muted">No tables match the current filters.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h5>All Tables</h5></div>
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Table No.</th>
                    <th>Capacity</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Today's Bookings</th>
                    <th>Upcoming</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tables as $table)
                    <tr>
                        <td>{{ $tables->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $table->table_number }}</td>
                        <td>{{ $table->seating_capacity }} seats</td>
                        <td>{{ $table->location ?? 'Main Floor' }}</td>
                        <td><span class="status-badge status-{{ $table->status }}">{{ ucfirst($table->status) }}</span></td>
                        <td>{{ $table->today_bookings_count }}</td>
                        <td>{{ $table->upcoming_bookings_count }}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.tables.show', $table) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.tables.edit', $table) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.tables.toggle', $table->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-icon btn-outline-warning btn-sm" type="submit" title="Toggle active/inactive">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-icon btn-outline-danger btn-sm" type="submit" onclick="return confirm('Delete this table?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No tables found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tables->hasPages())
        <div class="px-4 py-3 border-top">{{ $tables->links() }}</div>
    @endif
</div>
@endsection
