@php
    $menuItem = $menuItem ?? null;
    $previewImage = $menuItem?->image_url ?? 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160"><rect width="160" height="160" rx="18" fill="#f5f6fa"/><path d="M40 106l22-24 18 18 26-31 22 37H40z" fill="#d5dce3"/><circle cx="61" cy="57" r="11" fill="#c0392b"/><text x="80" y="138" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="14" fill="#7f8c8d">No image</text></svg>');
@endphp

<div class="mb-3">
    <label class="form-label">Category <span class="text-danger">*</span></label>
    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">Select Category</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string) old('category_id', $menuItem?->category_id) === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Item Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $menuItem?->name) }}" placeholder="e.g., Paneer Butter Masala" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief description of the item">{{ old('description', $menuItem?->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Item Image</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="imageInput">
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="mt-2">
        <img id="imagePreview" src="{{ $previewImage }}" class="img-preview" alt="Item preview">
    </div>
</div>

<div class="mb-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_available" value="1" id="isAvail" {{ old('is_available', $menuItem?->is_available ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="isAvail">Available Today</label>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

