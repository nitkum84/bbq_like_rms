@extends('admin.layouts.app')
@section('title','Bookings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Booking Management
        <span class="subtitle">All table reservations</span>
    </h1>
</div>

<!-- Filters -->
<div class="admin-card mb-4"><div class="admin-card-body">
    <form class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name or mobile..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary me-2">Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div></div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>Ref</th><th>Customer</th><th>Table</th><th>Date</th><th>Slot</th><th>Guests</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td><a href="{{ route('admin.bookings.show',$b) }}" class="fw-semibold text-primary text-decoration-none">{{ $b->confirmation_code ?? '#'.$b->id }}</a></td>
                    <td>
                        <div class="fw-semibold">{{ $b->user->name }}</div>
                        <div class="text-muted small">{{ $b->user->mobile }}</div>
                    </td>
                    <td>{{ $b->table->table_number }}</td>
                    <td>{{ $b->booking_date->format('d M Y') }}</td>
                    <td>{{ $b->slot->slot_label }}</td>
                    <td>
                        @if($b->veg_count) <span class="badge" style="background:#eafaf1;color:#27ae60">V:{{ $b->veg_count }}</span> @endif
                        @if($b->nonveg_count) <span class="badge" style="background:#fdf2f2;color:#c0392b">NV:{{ $b->nonveg_count }}</span> @endif
                    </td>
                    <td>₹{{ number_format($b->total_amount,2) }}</td>
                    <td>
                        <select class="form-select form-select-sm status-select" data-id="{{ $b->id }}" style="width:120px">
                            <option value="pending" {{ $b->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $b->status=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="cancelled" {{ $b->status=='cancelled'?'selected':'' }}>Cancelled</option>
                            <option value="completed" {{ $b->status=='completed'?'selected':'' }}>Completed</option>
                        </select>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.bookings.show',$b) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.bookings.edit',$b) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
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
        fetch(`/admin/bookings/${this.dataset.id}/status`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: this.value })
        }).then(r => r.json()).then(d => {
            if (d.success) { /* optionally show toast */ }
        });
    });
});
</script>
@endpush
