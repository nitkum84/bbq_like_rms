@extends('admin.layouts.app')
@section('title', 'Table Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Table {{ $table->table_number }}
        <span class="subtitle">Capacity, status, and booking activity</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tables.edit', $table) }}" class="btn btn-primary">Edit Table</a>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-grid-3x3-gap"></i></div>
            <div>
                <div class="stat-value">{{ $table->table_number }}</div>
                <div class="stat-label">Table Number</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-value">{{ $table->seating_capacity }}</div>
                <div class="stat-label">Seats</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ $table->today_bookings_count }}</div>
                <div class="stat-label">Confirmed Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-value">{{ ucfirst($table->status) }}</div>
                <div class="stat-label">Current Status</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5>Overview</h5>
            </div>
            <div class="admin-card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Location</dt>
                    <dd class="col-7">{{ $table->location ?: 'Not specified' }}</dd>

                    <dt class="col-5">Total Bookings</dt>
                    <dd class="col-7">{{ $table->bookings_count }}</dd>

                    <dt class="col-5">Confirmed</dt>
                    <dd class="col-7">{{ $table->confirmed_bookings_count }}</dd>

                    <dt class="col-5">Pending</dt>
                    <dd class="col-7">{{ $table->pending_bookings_count }}</dd>

                    <dt class="col-5">Cancelled</dt>
                    <dd class="col-7">{{ $table->cancelled_bookings_count }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5>Recent Bookings</h5>
            </div>
            <div class="table-responsive">
                <table class="table-admin mb-0">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Slot</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->confirmation_code ?? '#'.$booking->id }}</td>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                <td>{{ $booking->slot->slot_label }}</td>
                                <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No bookings found for this table.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h5>Upcoming Schedule</h5>
    </div>
    <div class="table-responsive">
        <table class="table-admin mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Slot</th>
                    <th>Guests</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingBookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->slot->slot_label }}</td>
                        <td>{{ $booking->total_guests }}</td>
                        <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No upcoming bookings for this table.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($upcomingBookings->hasPages())
        <div class="px-4 py-3 border-top">{{ $upcomingBookings->links() }}</div>
    @endif
</div>
@endsection
