@extends('admin.layouts.app')
@section('title','Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard
            <span class="subtitle">Welcome back, {{ auth()->user()->name }}</span>
        </h1>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('D, d M Y') }}
        </span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-layout-text-window"></i></div>
            <div>
                <div class="stat-value">{{ $totalTables }}</div>
                <div class="stat-label">Total Active Tables</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ $bookedToday }}</div>
                <div class="stat-label">Booked Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="bi bi-people"></i></div>
            <div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Registered Users</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-envelope"></i></div>
            <div>
                <div class="stat-value">{{ $newEnquiries }}</div>
                <div class="stat-label">New Enquiries</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value">{{ $pendingBookings }}</div>
                <div class="stat-label">Pending Bookings</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-graph-up"></i></div>
            <div>
                <div class="stat-value">{{ $totalBookings }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $availToday }}</div>
                <div class="stat-label">Available Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="bi bi-question-circle"></i></div>
            <div>
                <div class="stat-value">{{ $totalEnquiries }}</div>
                <div class="stat-label">Total Enquiries</div>
            </div>
        </div>
    </div>
</div>

<!-- Chart + Upcoming Bookings -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5><i class="bi bi-bar-chart me-2 text-primary"></i>Bookings Last 7 Days</h5>
            </div>
            <div class="admin-card-body">
                <canvas id="bookingsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5><i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming Bookings</h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="admin-card-body p-0">
                @forelse($upcomingBookings as $b)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="stat-icon blue flex-shrink-0" style="width:40px;height:40px;font-size:0.9rem">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate">{{ $b->user->name }}</div>
                        <div class="text-muted small">Table {{ $b->table->table_number }} • {{ $b->slot->slot_label }}</div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="small fw-bold">{{ $b->booking_date->format('d M') }}</div>
                        <span class="status-badge status-confirmed">Confirmed</span>
                    </div>
                </div>
                @empty
                <div class="px-4 py-5 text-center text-muted">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No upcoming bookings
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="bi bi-clock-history me-2 text-primary"></i>Recent Bookings</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>#Ref</th><th>Customer</th><th>Table</th>
                    <th>Date</th><th>Slot</th><th>Guests</th>
                    <th>Amount</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $b)
                <tr>
                    <td><span class="fw-semibold text-primary">{{ $b->confirmation_code ?? '—' }}</span></td>
                    <td>{{ $b->user->name }}</td>
                    <td>{{ $b->table->table_number }}</td>
                    <td>{{ $b->booking_date->format('d M Y') }}</td>
                    <td>{{ $b->slot->slot_label }}</td>
                    <td>{{ $b->total_guests }} guest(s)</td>
                    <td>₹{{ number_format($b->total_amount,2) }}</td>
                    <td><span class="status-badge status-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No bookings found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('bookingsChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData->pluck('date')) !!},
        datasets: [{
            label: 'Confirmed Bookings',
            data: {!! json_encode($chartData->pluck('count')) !!},
            backgroundColor: 'rgba(192, 57, 43, 0.8)',
            borderColor: 'rgba(192, 57, 43, 1)',
            borderWidth: 1, borderRadius: 6,
        }]
    },
    options: {
        responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush
