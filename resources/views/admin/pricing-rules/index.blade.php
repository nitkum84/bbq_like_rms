@extends('admin.layouts.app')

@section('title', 'Pricing Rules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Pricing Rules</h1>
        <p class="text-muted mb-0">Manage weekday and weekend pricing.</p>
    </div>
    <a href="{{ route('admin.pricing-rules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Rule
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Day Type</th>
                        <th>Price</th>
                        <th>Effective Date</th>
                        <th>Created By</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pricingRules as $pricingRule)
                        <tr>
                            <td class="text-capitalize">{{ $pricingRule->day_type }}</td>
                            <td>Rs. {{ number_format((float) $pricingRule->price, 2) }}</td>
                            <td>{{ $pricingRule->effective_date?->format('d M Y') }}</td>
                            <td>{{ $pricingRule->creator?->name ?? 'Unknown' }}</td>
                            <td>{{ $pricingRule->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.pricing-rules.edit', $pricingRule) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                                <form action="{{ route('admin.pricing-rules.destroy', $pricingRule) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this pricing rule?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No pricing rules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $pricingRules->links() }}
</div>
@endsection
