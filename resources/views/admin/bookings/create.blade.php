@extends('admin.layouts.app')
@section('title', 'Add Booking')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Booking</h1>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Booking Details</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.bookings.store') }}" method="POST">
                    @csrf
                    @include('admin.bookings._form', ['submitLabel' => 'Save Booking'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const mealTypeSelect = document.getElementById('mealType');
const slotSelect = document.getElementById('slotId');

function syncSlotOptions() {
    const mealType = mealTypeSelect?.value;
    Array.from(slotSelect?.options || []).forEach(option => {
        if (!option.value) {
            option.hidden = false;
            return;
        }
        option.hidden = mealType && option.dataset.mealType !== mealType;
    });

    const selected = slotSelect.options[slotSelect.selectedIndex];
    if (selected && selected.hidden) {
        slotSelect.value = '';
    }
}

mealTypeSelect?.addEventListener('change', syncSlotOptions);
syncSlotOptions();
</script>
@endpush
