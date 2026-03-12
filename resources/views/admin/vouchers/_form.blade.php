@php
    $voucher = $voucher ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Voucher Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $voucher?->code) }}" placeholder="e.g., WELCOME10" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
        <select name="discount_type" id="discountType" class="form-select @error('discount_type') is-invalid @enderror" required>
            <option value="percentage" @selected(old('discount_type', $voucher?->discount_type ?? 'percentage') === 'percentage')>Percentage</option>
            <option value="flat" @selected(old('discount_type', $voucher?->discount_type) === 'flat')>Flat</option>
        </select>
        @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text" id="discountSymbol">{{ old('discount_type', $voucher?->discount_type ?? 'percentage') === 'flat' ? 'Rs.' : '%' }}</span>
            <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', $voucher?->discount_value ?? 0) }}" required>
            @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Assign To User</label>
        <select name="assigned_to_user_id" class="form-select @error('assigned_to_user_id') is-invalid @enderror">
            <option value="">Unassigned</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('assigned_to_user_id', $voucher?->assigned_to_user_id) === (string) $user->id)>{{ $user->name }}{{ $user->email ? ' - '.$user->email : '' }}</option>
            @endforeach
        </select>
        @error('assigned_to_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Usage Limit <span class="text-danger">*</span></label>
        <input type="number" min="1" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit', $voucher?->usage_limit ?? 1) }}" required>
        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
        <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date', $voucher?->expiry_date?->toDateString() ?? now()->addMonth()->toDateString()) }}" required>
        @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $voucher?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive">Voucher is active</label>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
