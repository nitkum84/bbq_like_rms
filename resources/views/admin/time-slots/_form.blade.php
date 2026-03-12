@php
    $timeSlot = $timeSlot ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Slot Label <span class="text-danger">*</span></label>
        <input
            type="text"
            name="slot_label"
            id="slotLabel"
            class="form-control @error('slot_label') is-invalid @enderror"
            value="{{ old('slot_label', $timeSlot?->slot_label) }}"
            placeholder="e.g., 12:00 PM - 1:00 PM"
            required
        >
        @error('slot_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Meal Type <span class="text-danger">*</span></label>
        <select name="meal_type" class="form-select @error('meal_type') is-invalid @enderror" required>
            <option value="">Select meal type</option>
            <option value="lunch" @selected(old('meal_type', $timeSlot?->meal_type) === 'lunch')>Lunch</option>
            <option value="dinner" @selected(old('meal_type', $timeSlot?->meal_type) === 'dinner')>Dinner</option>
        </select>
        @error('meal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Max Bookings <span class="text-danger">*</span></label>
        <input
            type="number"
            name="max_bookings"
            class="form-control @error('max_bookings') is-invalid @enderror"
            value="{{ old('max_bookings', $timeSlot?->max_bookings ?? 10) }}"
            min="1"
            max="500"
            required
        >
        @error('max_bookings')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" id="startTime" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $timeSlot?->start_time ? \Illuminate\Support\Str::of($timeSlot->start_time)->substr(0, 5) : '') }}" required>
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" id="endTime" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $timeSlot?->end_time ? \Illuminate\Support\Str::of($timeSlot->end_time)->substr(0, 5) : '') }}" required>
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $timeSlot?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive">Slot is active</label>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.time-slots.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
