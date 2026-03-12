@extends('admin.layouts.app')
@section('title', 'Time Slots')

@section('content')
<div class="page-header">
    <h1 class="page-title">Time Slot Management
        <span class="subtitle">Manage lunch and dinner booking windows</span>
    </h1>
    <a href="{{ route('admin.time-slots.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Slot
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Slots</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">Active Slots</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-sun"></i></div>
            <div>
                <div class="stat-value">{{ $stats['lunch'] }}</div>
                <div class="stat-label">Lunch Slots</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-moon-stars"></i></div>
            <div>
                <div class="stat-value">{{ $stats['dinner'] }}</div>
                <div class="stat-label">Dinner Slots</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by slot label" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Meal Type</label>
                <select name="meal_type" class="form-select">
                    <option value="">All sessions</option>
                    <option value="lunch" @selected(request('meal_type') === 'lunch')>Lunch</option>
                    <option value="dinner" @selected(request('meal_type') === 'dinner')>Dinner</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.time-slots.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h5>All Time Slots</h5></div>
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Label</th>
                    <th>Time Range</th>
                    <th>Meal</th>
                    <th>Max Bookings</th>
                    <th>Bookings</th>
                    <th>Upcoming</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slots as $slot)
                    <tr>
                        <td>{{ $slots->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $slot->slot_label }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->end_time)->format('h:i A') }}</td>
                        <td><span class="status-badge {{ $slot->meal_type === 'lunch' ? 'status-pending' : 'status-completed' }}">{{ ucfirst($slot->meal_type) }}</span></td>
                        <td>{{ $slot->max_bookings }}</td>
                        <td>{{ $slot->bookings_count }}</td>
                        <td>{{ $slot->upcoming_bookings_count }}</td>
                        <td><span class="status-badge {{ $slot->is_active ? 'status-active' : 'status-inactive' }}">{{ $slot->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.time-slots.edit', $slot) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.time-slots.destroy', $slot) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-icon btn-outline-danger btn-sm" type="submit" data-confirm="Delete this time slot?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">No time slots found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($slots->hasPages())
        <div class="px-4 py-3 border-top">{{ $slots->links() }}</div>
    @endif
</div>
@endsection
