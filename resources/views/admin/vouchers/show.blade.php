@extends('admin.layouts.app')
@section('title', 'Voucher Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Voucher Details
        <span class="subtitle">{{ $voucher->code }}</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Voucher Summary</h5></div>
            <div class="admin-card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="small text-muted">Discount</div>
                        <div class="fw-semibold">{{ $voucher->discount_type === 'percentage' ? number_format((float) $voucher->discount_value, 2).'%' : 'Rs. '.number_format((float) $voucher->discount_value, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Usage</div>
                        <div class="fw-semibold">{{ $voucher->used_count }}/{{ $voucher->usage_limit }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Remaining Uses</div>
                        <div class="fw-semibold">{{ $voucher->remaining_uses }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Assigned To</div>
                        <div class="fw-semibold">{{ $voucher->user?->name ?? 'Unassigned' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Expiry</div>
                        <div class="fw-semibold">{{ $voucher->expiry_date?->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Status</div>
                        <div class="fw-semibold">
                            @if($voucher->is_expired)
                                Expired
                            @elseif(! $voucher->is_active)
                                Inactive
                            @else
                                Active
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Booking Usage</h5></div>
            <div class="admin-card-body">
                @forelse($voucher->bookings as $booking)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div>
                            <div class="fw-semibold">{{ $booking->confirmation_code ?? '#'.$booking->id }}</div>
                            <div class="small text-muted">{{ $booking->user?->name ?? 'Guest User' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">{{ $booking->booking_date?->format('d M Y') }}</div>
                            <div class="small text-muted text-capitalize">{{ $booking->status }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">This voucher has not been used in any bookings yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Assign Voucher</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.vouchers.assign', $voucher->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected($voucher->assigned_to_user_id === $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">Assign Voucher</button>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Quick Actions</h5></div>
            <div class="admin-card-body d-grid gap-2">
                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-outline-primary">Edit Voucher</a>
                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Delete this voucher?">Delete Voucher</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
