@extends('admin.layouts.app')
@section('title', 'Edit Voucher')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Voucher: {{ $voucher->code }}</h1>
    <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Voucher Details</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.vouchers._form', ['submitLabel' => 'Update Voucher'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const discountType = document.getElementById('discountType');
const discountSymbol = document.getElementById('discountSymbol');

function syncDiscountSymbol() {
    if (!discountType || !discountSymbol) return;
    discountSymbol.textContent = discountType.value === 'flat' ? 'Rs.' : '%';
}

discountType?.addEventListener('change', syncDiscountSymbol);
syncDiscountSymbol();
</script>
@endpush
