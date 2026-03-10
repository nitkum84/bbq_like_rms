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
        <div class="mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Item Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" placeholder="e.g., Paneer Butter Masala">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the item">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Item Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
            <div class="mt-2">
                <img id="imagePreview" src="{{ asset('admin/images/no-food.png') }}" class="img-preview">
            </div>
        </div>
        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvail" checked>
                <label class="form-check-label" for="isAvail">Available Today</label>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Item</button>
            <a href="{{ route('admin.menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
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
