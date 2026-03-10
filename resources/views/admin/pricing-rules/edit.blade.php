@extends('admin.layouts.app')

@section('title', 'Edit Pricing Rule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Edit Pricing Rule</h1>
        <p class="text-muted mb-0">Update the selected pricing rule.</p>
    </div>
    <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('admin.pricing-rules.update', $pricingRule) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="day_type" class="form-label">Day Type</label>
                    <select name="day_type" id="day_type" class="form-select" required>
                        <option value="weekday" @selected(old('day_type', $pricingRule->day_type) === 'weekday')>Weekday</option>
                        <option value="weekend" @selected(old('day_type', $pricingRule->day_type) === 'weekend')>Weekend</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" value="{{ old('price', $pricingRule->price) }}" required>
                </div>

                <div class="col-md-4">
                    <label for="effective_date" class="form-label">Effective Date</label>
                    <input type="date" name="effective_date" id="effective_date" class="form-control" value="{{ old('effective_date', optional($pricingRule->effective_date)->toDateString()) }}" required>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Rule</button>
                <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
