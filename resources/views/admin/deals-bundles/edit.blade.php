@extends('admin.layouts.app')
@section('title', 'Edit Deal')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Deal: {{ $dealsBundle->name }}</h1>
    <a href="{{ route('admin.deals-bundles.show', $dealsBundle) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Deal Details</h5></div>
            <div class="admin-card-body">
                <form action="{{ route('admin.deals-bundles.update', $dealsBundle) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.deals-bundles._form', ['submitLabel' => 'Update Deal'])
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
    if (!discountType || !discountSymbol) {
        return;
    }
    discountSymbol.textContent = discountType.value === 'flat' ? 'Rs.' : '%';
}

discountType?.addEventListener('change', syncDiscountSymbol);
syncDiscountSymbol();
</script>
@endpush
