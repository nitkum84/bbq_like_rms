@extends('admin.layouts.app')
@section('title', 'Edit Time Slot')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Time Slot: {{ $timeSlot->slot_label }}</h1>
    <a href="{{ route('admin.time-slots.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Slot Details</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.time-slots.update', $timeSlot) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.time-slots._form', ['submitLabel' => 'Update Slot'])
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Booking Snapshot</h5></div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Total Bookings</span>
                    <strong>{{ $bookingSummary['total'] }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Upcoming</span>
                    <strong>{{ $bookingSummary['upcoming'] }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Confirmed</span>
                    <strong>{{ $bookingSummary['confirmed'] }}</strong>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Recent Bookings</h5></div>
            <div class="admin-card-body">
                @forelse($recentBookings as $booking)
                    <div class="py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div class="fw-semibold">{{ $booking->user?->name ?? 'Guest User' }}</div>
                        <div class="small text-muted">{{ $booking->booking_date?->format('d M Y') }} | {{ $booking->table?->table_number ?? 'N/A' }}</div>
                        <div class="small text-muted text-capitalize">{{ $booking->status }}</div>
                    </div>
                @empty
                    <div class="text-muted small">No bookings linked to this slot yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatSlotLabel() {
    const start = document.getElementById('startTime')?.value;
    const end = document.getElementById('endTime')?.value;
    const label = document.getElementById('slotLabel');

    if (!start || !end || !label || label.dataset.manual === 'true') {
        return;
    }

    const formatTime = (value) => {
        const [hour, minute] = value.split(':').map(Number);
        const suffix = hour >= 12 ? 'PM' : 'AM';
        const normalizedHour = hour % 12 || 12;
        return `${normalizedHour}:${String(minute).padStart(2, '0')} ${suffix}`;
    };

    label.value = `${formatTime(start)} - ${formatTime(end)}`;
}

document.getElementById('slotLabel')?.addEventListener('input', function() {
    this.dataset.manual = this.value.trim() !== '' ? 'true' : 'false';
});
document.getElementById('startTime')?.addEventListener('change', formatSlotLabel);
document.getElementById('endTime')?.addEventListener('change', formatSlotLabel);
</script>
@endpush
