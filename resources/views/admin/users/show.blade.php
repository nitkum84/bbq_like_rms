@extends('admin.layouts.app')
@section('title', 'User Details')
@section('content')
<div class="page-header"><h1 class="page-title">User Details<span class="subtitle">{{ $user->name }}</span></h1><div class="d-flex gap-2"><a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a><a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a></div></div>
<div class="row g-4">
<div class="col-lg-4"><div class="admin-card mb-4"><div class="admin-card-header"><h5>Profile</h5></div><div class="admin-card-body"><div class="fw-semibold mb-1">{{ $user->name }}</div><div class="text-muted small">{{ $user->email }}</div><div class="text-muted small mb-3">{{ $user->mobile ?: '-' }}</div><div class="small text-muted">Status</div><div class="fw-semibold mb-3">{{ $user->status ? 'Active' : 'Inactive' }}</div><div class="small text-muted">Joined</div><div class="fw-semibold">{{ $user->created_at?->format('d M Y') }}</div></div></div>
<div class="admin-card mb-4"><div class="admin-card-header"><h5>Account Actions</h5></div><div class="admin-card-body d-grid gap-2"><form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">@csrf<button class="btn {{ $user->status ? 'btn-outline-warning' : 'btn-outline-success' }} w-100" data-confirm="Change this user status?">{{ $user->status ? 'Deactivate User' : 'Activate User' }}</button></form><a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">Edit Profile</a></div></div>
<div class="admin-card mb-4"><div class="admin-card-header"><h5>Assign Voucher</h5></div><div class="admin-card-body">@if($availableVouchers->isEmpty())<div class="text-muted small">No active unassigned vouchers available.</div>@else<form action="{{ route('admin.vouchers.assign', $availableVouchers->first()->id) }}" method="POST" id="voucherAssignForm">@csrf<input type="hidden" name="user_id" value="{{ $user->id }}"><div class="mb-3"><label class="form-label">Voucher</label><select class="form-select" id="voucherSelect" data-assign-base="{{ url('/admin-panel/vouchers') }}"><option value="">Select voucher</option>@foreach($availableVouchers as $voucher)<option value="{{ $voucher->id }}">{{ $voucher->code }}{{ $voucher->assigned_to_user_id === $user->id ? ' (already assigned)' : '' }}</option>@endforeach</select></div><button class="btn btn-primary w-100" type="submit">Assign Voucher</button></form>@endif</div></div>
<div class="admin-card"><div class="admin-card-header"><h5>Assigned Vouchers</h5></div><div class="admin-card-body">@forelse($vouchers as $voucher)<div class="d-flex justify-content-between py-2 {{ $loop->last ? '' : 'border-bottom' }}"><div><div class="fw-semibold">{{ $voucher->code }}</div><div class="small text-muted">{{ $voucher->expiry_date?->format('d M Y') }}</div></div><div class="small text-muted">{{ $voucher->used_count }}/{{ $voucher->usage_limit }}</div></div>@empty<div class="text-muted small">No vouchers assigned.</div>@endforelse</div></div></div>
<div class="col-lg-8"><div class="admin-card"><div class="admin-card-header"><h5>Booking History</h5></div><div class="table-responsive"><table class="table-admin"><thead><tr><th>Ref</th><th>Date</th><th>Table</th><th>Slot</th><th>Status</th></tr></thead><tbody>@forelse($bookings as $booking)<tr><td>{{ $booking->confirmation_code ?? '#'.$booking->id }}</td><td>{{ $booking->booking_date?->format('d M Y') }}</td><td>{{ $booking->table?->table_number ?? '-' }}</td><td>{{ $booking->slot?->slot_label ?? '-' }}</td><td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td></tr>@empty<tr><td colspan="5" class="text-center py-4 text-muted">No bookings found.</td></tr>@endforelse</tbody></table></div>@if($bookings->hasPages())<div class="px-4 py-3 border-top">{{ $bookings->links() }}</div>@endif</div></div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('voucherAssignForm')?.addEventListener('submit', function (event) {
    const select = document.getElementById('voucherSelect');

    if (!select.value) {
        event.preventDefault();
        return;
    }

    this.action = select.dataset.assignBase + '/' + select.value + '/assign';
});
</script>
@endpush
