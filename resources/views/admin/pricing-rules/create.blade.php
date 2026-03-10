@extends('admin.layouts.app')

@section('title', 'Create Pricing Rule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Create Pricing Rule</h1>
        <p class="text-muted mb-0">Add a new pricing rule for weekday or weekend bookings.</p>
    </div>
    <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.pricing-rules.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="day_type" class="form-label">Day Type</label>
                    <select name="day_type" id="day_type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="weekday" @selected(old('day_type') === 'weekday')>Weekday</option>
                        <option value="weekend" @selected(old('day_type') === 'weekend')>Weekend</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="col-md-4">
                    <label for="effective_date" class="form-label">Effective Date</label>
                    <input type="date" name="effective_date" id="effective_date" class="form-control" value="{{ old('effective_date', now()->toDateString()) }}" required>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Rule</button>
                <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
