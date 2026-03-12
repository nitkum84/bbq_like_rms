@extends('admin.layouts.app')
@section('title','Bookings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Booking Management
        <span class="subtitle">Manage reservations, guests, and booking status</span>
    </h1>
    <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Booking
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-journal-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-calendar2-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['confirmed_today'] }}</div>
                <div class="stat-label">Confirmed Today</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="stat-value">{{ $stats['upcoming'] }}</div>
                <div class="stat-label">Upcoming Active</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-x-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['cancelled'] }}</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Meal</label>
                <select name="meal_type" class="form-select">
                    <option value="">All Meals</option>
                    <option value="lunch" @selected(request('meal_type') === 'lunch')>Lunch</option>
                    <option value="dinner" @selected(request('meal_type') === 'dinner')>Dinner</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Ref, customer, mobile, email, table" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Customer</th>
                    <th>Table</th>
                    <th>Date</th>
                    <th>Slot</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td>
                        <a href="{{ route('admin.bookings.show', $b) }}" class="fw-semibold text-primary text-decoration-none">{{ $b->confirmation_code ?? '#'.$b->id }}</a>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $b->user?->name ?? 'Deleted user' }}</div>
                        <div class="text-muted small">{{ $b->user?->mobile ?? '-' }}</div>
                    </td>
                    <td>{{ $b->table?->table_number ?? '-' }}</td>
                    <td>{{ $b->booking_date?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $b->slot?->slot_label ?? '-' }}</td>
                    <td>
                        @if($b->veg_count)<span class="badge" style="background:#eafaf1;color:#27ae60">V:{{ $b->veg_count }}</span>@endif
                        @if($b->nonveg_count)<span class="badge" style="background:#fdf2f2;color:#c0392b">NV:{{ $b->nonveg_count }}</span>@endif
                        <div class="small text-muted mt-1">Total: {{ $b->total_guests }}</div>
                    </td>
                    <td>Rs. {{ number_format((float) $b->total_amount, 2) }}</td>
                    <td>
                        <select class="form-select form-select-sm status-select" data-url="{{ route('admin.bookings.status', $b->id) }}" style="width:120px">
                            @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'completed' => 'Completed'] as $value => $label)
                                <option value="{{ $value }}" @selected($b->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.bookings.edit', $b) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.bookings.destroy', $b) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-icon btn-outline-danger btn-sm" type="submit" data-confirm="Delete this booking?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="px-4 py-3 border-top">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', function() {
        fetch(this.dataset.url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: this.value })
        }).then(r => r.json()).then(d => {
            if (!d.success) {
                location.reload();
            }
        }).catch(() => location.reload());
    });
});
</script>
@endpush
