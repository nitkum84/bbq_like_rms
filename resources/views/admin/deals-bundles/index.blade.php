@extends('admin.layouts.app')
@section('title', 'Deals & Bundles')

@section('content')
<div class="page-header">
    <h1 class="page-title">Deals & Bundles
        <span class="subtitle">Manage promotional offers, validity windows, and linked menu items</span>
    </h1>
    <a href="{{ route('admin.deals-bundles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Deal
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-tags"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Deals</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">Active Deals</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-calendar2-week"></i></div>
            <div>
                <div class="stat-value">{{ $stats['currently_valid'] }}</div>
                <div class="stat-label">Currently Valid</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-value">{{ $stats['expired'] }}</div>
                <div class="stat-label">Expired</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by deal name" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All</option>
                    <option value="veg" @selected(request('type') === 'veg')>Veg</option>
                    <option value="non-veg" @selected(request('type') === 'non-veg')>Non-Veg</option>
                    <option value="mixed" @selected(request('type') === 'mixed')>Mixed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select">
                    <option value="">All</option>
                    <option value="percentage" @selected(request('discount_type') === 'percentage')>Percentage</option>
                    <option value="flat" @selected(request('discount_type') === 'flat')>Flat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.deals-bundles.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Validity</th>
                    <th>Linked Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $deal)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $deal->name }}</div>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($deal->description, 70) }}</div>
                        </td>
                        <td><span class="status-badge status-{{ $deal->type === 'veg' ? 'active' : ($deal->type === 'non-veg' ? 'cancelled' : 'completed') }}">{{ ucfirst($deal->type) }}</span></td>
                        <td>{{ $deal->discount_type === 'percentage' ? number_format((float) $deal->discount_percent, 2).'%' : 'Rs. '.number_format((float) $deal->discount_percent, 2) }}</td>
                        <td>{{ $deal->valid_from?->format('d M Y') }} - {{ $deal->valid_to?->format('d M Y') }}</td>
                        <td>{{ $deal->menu_items_count }}</td>
                        <td>
                            @if($deal->is_currently_valid)
                                <span class="status-badge status-active">Current</span>
                            @elseif($deal->is_expired)
                                <span class="status-badge status-inactive">Expired</span>
                            @elseif(! $deal->is_active)
                                <span class="status-badge status-blocked">Inactive</span>
                            @else
                                <span class="status-badge status-pending">Scheduled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.deals-bundles.show', $deal) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.deals-bundles.edit', $deal) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.deals-bundles.destroy', $deal) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-icon btn-outline-danger btn-sm" type="submit" data-confirm="Delete this deal?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No deals found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deals->hasPages())
        <div class="px-4 py-3 border-top">{{ $deals->links() }}</div>
    @endif
</div>
@endsection
