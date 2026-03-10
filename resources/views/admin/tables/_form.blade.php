<div class="row g-3">
    <div class="col-md-4">
        <label for="table_number" class="form-label">Table Number</label>
        <input
            type="text"
            name="table_number"
            id="table_number"
            class="form-control"
            value="{{ old('table_number', $table->table_number ?? '') }}"
            maxlength="20"
            required
        >
    </div>

    <div class="col-md-4">
        <label for="seating_capacity" class="form-label">Seating Capacity</label>
        <input
            type="number"
            name="seating_capacity"
            id="seating_capacity"
            class="form-control"
            value="{{ old('seating_capacity', $table->seating_capacity ?? 4) }}"
            min="1"
            max="20"
            required
        >
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'blocked' => 'Blocked'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $table->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="location" class="form-label">Location</label>
        <input
            type="text"
            name="location"
            id="location"
            class="form-control"
            value="{{ old('location', $table->location ?? '') }}"
            maxlength="100"
            placeholder="Ground Floor, Terrace, Private Room"
        >
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
