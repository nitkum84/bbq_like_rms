@extends('admin.layouts.app')
@section('title','Add Menu Item')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Menu Item</h1>
    <a href="{{ route('admin.menu-items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="admin-card"><div class="admin-card-header"><h5>Item Details</h5></div>
<div class="admin-card-body">
    <form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.menu-items._form', ['submitLabel' => 'Save Item'])
    </form>
</div></div>
</div></div>
@endsection

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
