@extends('admin.layouts.app')
@section('title','Edit Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Category: {{ $menuCategory->name }}</h1>
    <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row justify-content-center"><div class="col-lg-6">
<div class="admin-card"><div class="admin-card-header"><h5>Category Details</h5></div>
<div class="admin-card-body">
    <form action="{{ route('admin.menu-categories.update',$menuCategory) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$menuCategory->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="veg" {{ old('type',$menuCategory->type) === 'veg' ? 'selected' : '' }}>🟢 Veg</option>
                <option value="non-veg" {{ old('type',$menuCategory->type) === 'non-veg' ? 'selected' : '' }}>🔴 Non-Veg</option>
                <option value="both" {{ old('type',$menuCategory->type) === 'both' ? 'selected' : '' }}>Both</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description',$menuCategory->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Display Order</label>
            <input type="number" name="display_order" class="form-control" value="{{ old('display_order',$menuCategory->display_order) }}">
        </div>
        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active',$menuCategory->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive">Active</label>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div></div>
</div></div>
@endsection
