@php
    $booking = $booking ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <label class="form-label mb-0">Customer <span class="text-danger">*</span></label>
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#quickUserModal">Add User</button>
        </div>
        <select name="user_id" class="form-select searchable-select @error('user_id') is-invalid @enderror" data-searchable-select data-placeholder="Search customer by name, email, or mobile" required>
            <option value="">Select customer</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $booking?->user_id) === (string) $user->id)>{{ $user->name }}{{ $user->email ? ' - '.$user->email : '' }}{{ $user->mobile ? ' - '.$user->mobile : '' }}</option>
            @endforeach
        </select>
        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Booking Date <span class="text-danger">*</span></label>
        <input type="date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date', $booking?->booking_date?->toDateString()) }}" required>
        @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled', 'completed' => 'Completed'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $booking?->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Table <span class="text-danger">*</span></label>
        <select name="table_id" class="form-select searchable-select @error('table_id') is-invalid @enderror" data-searchable-select data-placeholder="Search table" required>
            <option value="">Select table</option>
            @foreach($tables as $table)
                <option value="{{ $table->id }}" data-capacity="{{ $table->seating_capacity }}" @selected((string) old('table_id', $booking?->table_id) === (string) $table->id)>{{ $table->table_number }} ({{ $table->seating_capacity }} seats)</option>
            @endforeach
        </select>
        @error('table_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Meal Type <span class="text-danger">*</span></label>
        <select name="meal_type" id="mealType" class="form-select @error('meal_type') is-invalid @enderror" required>
            <option value="">Select meal</option>
            <option value="lunch" @selected(old('meal_type', $booking?->meal_type) === 'lunch')>Lunch</option>
            <option value="dinner" @selected(old('meal_type', $booking?->meal_type) === 'dinner')>Dinner</option>
        </select>
        @error('meal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Time Slot <span class="text-danger">*</span></label>
        <select name="slot_id" id="slotId" class="form-select searchable-select @error('slot_id') is-invalid @enderror" data-searchable-select data-placeholder="Search slot" required>
            <option value="">Select slot</option>
            @foreach($slots as $slot)
                <option value="{{ $slot->id }}" data-meal-type="{{ $slot->meal_type }}" @selected((string) old('slot_id', $booking?->slot_id) === (string) $slot->id)>{{ $slot->slot_label }} ({{ ucfirst($slot->meal_type) }})</option>
            @endforeach
        </select>
        @error('slot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Veg Guests</label>
        <input type="number" name="veg_count" id="vegCount" class="form-control @error('veg_count') is-invalid @enderror" value="{{ old('veg_count', $booking?->veg_count ?? 0) }}" min="0" required>
        @error('veg_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Non-Veg Guests</label>
        <input type="number" name="nonveg_count" id="nonvegCount" class="form-control @error('nonveg_count') is-invalid @enderror" value="{{ old('nonveg_count', $booking?->nonveg_count ?? 0) }}" min="0" required>
        @error('nonveg_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Total Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="total_amount" id="bookingTotalAmount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $booking?->total_amount ?? 0) }}" required>
        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Voucher</label>
        <select name="voucher_id" class="form-select searchable-select @error('voucher_id') is-invalid @enderror" data-searchable-select data-placeholder="Search voucher">
            <option value="">No voucher</option>
            @foreach($vouchers as $voucher)
                <option value="{{ $voucher->id }}" @selected((string) old('voucher_id', $booking?->voucher_id) === (string) $voucher->id)>{{ $voucher->code }}</option>
            @endforeach
        </select>
        @error('voucher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Confirmation Code</label>
        <input type="text" name="confirmation_code" class="form-control @error('confirmation_code') is-invalid @enderror" value="{{ old('confirmation_code', $booking?->confirmation_code) }}" placeholder="Leave blank to auto-generate">
        @error('confirmation_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Guest Preferences</label>
        @php($selectedGuestTypes = old('guest_type', $booking?->guest_type ?? []))
        <div class="d-flex flex-wrap gap-3 mt-2">
            @foreach(['kids' => 'Kids', 'anniversary' => 'Anniversary', 'corporate' => 'Corporate'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="guest_type[]" value="{{ $value }}" id="guestType{{ $value }}" {{ in_array($value, $selectedGuestTypes, true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="guestType{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('guest_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        @error('guest_type.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Subtotal</label>
        <input type="number" step="0.01" min="0" name="subtotal" id="bookingSubtotal" class="form-control" value="{{ old('subtotal', $booking?->booking_meta['subtotal'] ?? $booking?->total_amount ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Coupon Code</label>
        <input type="text" name="coupon_code" class="form-control @error('coupon_code') is-invalid @enderror" value="{{ old('coupon_code', $booking?->booking_meta['coupon_code'] ?? '') }}" placeholder="Optional coupon code">
        @error('coupon_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Total</label>
        <input type="number" step="0.01" min="0" name="discount_total" id="bookingDiscountTotal" class="form-control" value="{{ old('discount_total', $booking?->booking_meta['discount_total'] ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">GST %</label>
        <input type="number" step="0.01" min="0" max="100" name="gst_rate" id="bookingGstRate" class="form-control @error('gst_rate') is-invalid @enderror" value="{{ old('gst_rate', $booking?->booking_meta['gst_rate'] ?? \App\Models\WebsiteSetting::get('gst_rate', 5)) }}">
        @error('gst_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">GST Amount</label>
        <input type="number" step="0.01" min="0" id="bookingGstAmount" class="form-control" value="{{ old('gst_amount', $booking?->booking_meta['gst_amount'] ?? 0) }}" readonly>
    </div>

    <div class="col-md-4">
        <label class="form-label">Final Total</label>
        <input type="number" step="0.01" min="0" id="bookingFinalTotal" class="form-control" value="{{ old('total_amount', $booking?->total_amount ?? 0) }}" readonly>
    </div>
</div>

<div class="mt-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="offer_applied" value="1" id="offerApplied" {{ old('offer_applied', $booking?->offer_applied ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="offerApplied">Offer applied</label>
    </div>
</div>

<div class="mt-3">
    <label class="form-label">Admin Notes</label>
    <textarea name="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror" rows="4" placeholder="Internal notes for this booking">{{ old('admin_notes', $booking?->admin_notes) }}</textarea>
    @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
