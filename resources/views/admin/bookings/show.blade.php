@extends('admin.layouts.app')
@section('title', 'Booking Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Booking Details
        <span class="subtitle">{{ $booking->confirmation_code ?? '#'.$booking->id }}</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Reservation Summary</h5></div>
            <div class="admin-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="small text-muted">Customer</div>
                        <div class="fw-semibold">{{ $booking->user?->name ?? 'Deleted user' }}</div>
                        <div class="text-muted small">{{ $booking->user?->email ?? '-' }}</div>
                        <div class="text-muted small">{{ $booking->user?->mobile ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Booking Status</div>
                        <div><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></div>
                        <div class="small text-muted mt-2">Created {{ $booking->created_at?->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Date</div>
                        <div class="fw-semibold">{{ $booking->booking_date?->format('d M Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Meal</div>
                        <div class="fw-semibold">{{ ucfirst($booking->meal_type) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Slot</div>
                        <div class="fw-semibold">{{ $booking->slot?->slot_label ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Table</div>
                        <div class="fw-semibold">{{ $booking->table?->table_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Guests</div>
                        <div class="fw-semibold">{{ $booking->total_guests }}</div>
                        <div class="text-muted small">Veg: {{ $booking->veg_count }} | Non-Veg: {{ $booking->nonveg_count }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Amount</div>
                        <div class="fw-semibold">Rs. {{ number_format((float) $booking->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Notes & Preferences</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <div class="small text-muted">Guest Preferences</div>
                    <div class="fw-semibold">
                        @if(!empty($booking->guest_type))
                            {{ implode(', ', $booking->guest_type) }}
                        @else
                            None
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">Offer Applied</div>
                    <div class="fw-semibold">{{ $booking->offer_applied ? 'Yes' : 'No' }}</div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">Coupon / GST</div>
                    <div class="fw-semibold">
                        {{ $booking->booking_meta['coupon_code'] ?? 'No coupon' }}
                        | GST {{ number_format((float) ($booking->booking_meta['gst_rate'] ?? 0), 2) }}%
                    </div>
                </div>
                <div>
                    <div class="small text-muted">Admin Notes</div>
                    <div class="fw-semibold">{{ $booking->admin_notes ?: 'No notes added.' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Voucher & Notifications</h5></div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Voucher</span>
                    <strong>{{ $booking->voucher?->code ?? 'None' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">SMS Sent</span>
                    <strong>{{ $booking->sms_sent ? 'Yes' : 'No' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Email Sent</span>
                    <strong>{{ $booking->email_sent ? 'Yes' : 'No' }}</strong>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Quick Actions</h5></div>
            <div class="admin-card-body d-grid gap-2">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-outline-primary">Edit Booking</a>
                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Delete this booking?">Delete Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
