@extends('admin.layouts.app')
@section('title','Add Menu Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Menu Category</h1>
    <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="admin-card">
    <div class="admin-card-header"><h5>Category Details</h5></div>
    <div class="admin-card-body">
        <form action="{{ route('admin.menu-categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="e.g., Starters, Main Course">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select">
                    <option value="veg" {{ old('type') === 'veg' ? 'selected' : '' }}>🟢 Veg</option>
                    <option value="non-veg" {{ old('type') === 'non-veg' ? 'selected' : '' }}>🔴 Non-Veg</option>
                    <option value="both" {{ old('type','both') === 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Optional description">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="{{ old('display_order',0) }}" min="0">
            </div>
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active',1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Category</button>
                <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
