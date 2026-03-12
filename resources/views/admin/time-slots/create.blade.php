@extends('admin.layouts.app')
@section('title', 'Add Time Slot')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Time Slot</h1>
    <a href="{{ route('admin.time-slots.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Slot Details</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.time-slots.store') }}" method="POST">
                    @csrf
                    @include('admin.time-slots._form', ['submitLabel' => 'Save Slot'])
                </form>
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
