@extends('admin.layouts.app')
@section('title', 'Deal Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Deal Details
        <span class="subtitle">{{ $dealsBundle->name }}</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.deals-bundles.edit', $dealsBundle) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('admin.deals-bundles.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Offer Summary</h5></div>
            <div class="admin-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="small text-muted">Type</div>
                        <div class="fw-semibold">{{ ucfirst($dealsBundle->type) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Discount</div>
                        <div class="fw-semibold">{{ $dealsBundle->discount_type === 'percentage' ? number_format((float) $dealsBundle->discount_percent, 2).'%' : 'Rs. '.number_format((float) $dealsBundle->discount_percent, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Validity</div>
                        <div class="fw-semibold">{{ $dealsBundle->valid_from?->format('d M Y') }} - {{ $dealsBundle->valid_to?->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Status</div>
                        <div class="fw-semibold">
                            @if($dealsBundle->is_currently_valid)
                                Current
                            @elseif($dealsBundle->is_expired)
                                Expired
                            @elseif(! $dealsBundle->is_active)
                                Inactive
                            @else
                                Scheduled
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="small text-muted">Description</div>
                    <div class="fw-semibold">{{ $dealsBundle->description ?: 'No description added.' }}</div>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Linked Menu Items</h5></div>
            <div class="admin-card-body">
                @forelse($dealsBundle->menuItems as $menuItem)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div>
                            <div class="fw-semibold">{{ $menuItem->name }}</div>
                            <div class="small text-muted">{{ $menuItem->category?->name ?? 'Uncategorized' }}</div>
                        </div>
                        <span class="status-badge {{ $menuItem->is_available ? 'status-active' : 'status-inactive' }}">{{ $menuItem->is_available ? 'Available' : 'Unavailable' }}</span>
                    </div>
                @empty
                    <div class="text-muted small">No menu items linked. This offer can still be applied at booking level.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Quick Actions</h5></div>
            <div class="admin-card-body d-grid gap-2">
                <a href="{{ route('admin.deals-bundles.edit', $dealsBundle) }}" class="btn btn-outline-primary">Edit Deal</a>
                <form action="{{ route('admin.deals-bundles.destroy', $dealsBundle) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100" data-confirm="Delete this deal?">Delete Deal</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
