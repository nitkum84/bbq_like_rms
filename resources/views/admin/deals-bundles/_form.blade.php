@php
    $dealsBundle = $dealsBundle ?? null;
    $selectedMenuItems = collect(old('menu_item_ids', $dealsBundle?->menuItems?->pluck('id')->all() ?? []))
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Deal / Bundle Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $dealsBundle?->name) }}" placeholder="e.g., Weekend Family Feast" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">Select type</option>
            <option value="veg" @selected(old('type', $dealsBundle?->type) === 'veg')>Veg</option>
            <option value="non-veg" @selected(old('type', $dealsBundle?->type) === 'non-veg')>Non-Veg</option>
            <option value="mixed" @selected(old('type', $dealsBundle?->type) === 'mixed')>Mixed</option>
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
        <select name="discount_type" id="discountType" class="form-select @error('discount_type') is-invalid @enderror" required>
            <option value="percentage" @selected(old('discount_type', $dealsBundle?->discount_type ?? 'percentage') === 'percentage')>Percentage</option>
            <option value="flat" @selected(old('discount_type', $dealsBundle?->discount_type) === 'flat')>Flat</option>
        </select>
        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text" id="discountSymbol">{{ old('discount_type', $dealsBundle?->discount_type ?? 'percentage') === 'flat' ? 'Rs.' : '%' }}</span>
            <input type="number" step="0.01" min="0.01" name="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror" value="{{ old('discount_percent', $dealsBundle?->discount_percent ?? 0) }}" required>
            @error('discount_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Valid From <span class="text-danger">*</span></label>
        <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" value="{{ old('valid_from', $dealsBundle?->valid_from?->toDateString() ?? now()->toDateString()) }}" required>
        @error('valid_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Valid To <span class="text-danger">*</span></label>
        <input type="date" name="valid_to" class="form-control @error('valid_to') is-invalid @enderror" value="{{ old('valid_to', $dealsBundle?->valid_to?->toDateString() ?? now()->addWeek()->toDateString()) }}" required>
        @error('valid_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Explain what the deal includes and when it should be applied">{{ old('description', $dealsBundle?->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mt-4">
    <label class="form-label">Linked Menu Items</label>
    <div class="admin-card border">
        <div class="admin-card-body" style="max-height: 260px; overflow-y: auto;">
            <div class="row g-2">
                @forelse($menuItems as $menuItem)
                    <div class="col-md-6">
                        <label class="d-flex align-items-start gap-2 border rounded p-2">
                            <input class="form-check-input mt-1" type="checkbox" name="menu_item_ids[]" value="{{ $menuItem->id }}" {{ in_array((string) $menuItem->id, $selectedMenuItems, true) ? 'checked' : '' }}>
                            <span>
                                <span class="fw-semibold d-block">{{ $menuItem->name }}</span>
                                <span class="small text-muted">{{ $menuItem->category?->name ?? 'Uncategorized' }}</span>
                            </span>
                        </label>
                    </div>
                @empty
                    <div class="text-muted small">No menu items available to link.</div>
                @endforelse
            </div>
        </div>
    </div>
    @error('menu_item_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    @error('menu_item_ids.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="mt-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $dealsBundle?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive">Active deal</label>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.deals-bundles.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
