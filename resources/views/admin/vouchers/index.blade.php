@extends('admin.layouts.app')
@section('title', 'Vouchers')

@section('content')
<div class="page-header">
    <h1 class="page-title">Voucher Management
        <span class="subtitle">Create, assign, and track promotional vouchers</span>
    </h1>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Voucher
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-ticket-perforated"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Vouchers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="stat-value">{{ $stats['active'] }}</div>
                <div class="stat-label">Active</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-person-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['assigned'] }}</div>
                <div class="stat-label">Assigned</div>
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
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Code, user, or email" value="{{ request('search') }}">
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
            <div class="col-md-2">
                <label class="form-label">Assignment</label>
                <select name="assignment" class="form-select">
                    <option value="">All</option>
                    <option value="assigned" @selected(request('assignment') === 'assigned')>Assigned</option>
                    <option value="unassigned" @selected(request('assignment') === 'unassigned')>Unassigned</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header"><h5>Bulk Generate</h5></div>
    <div class="admin-card-body">
        <form action="{{ route('admin.vouchers.bulk-generate') }}" method="POST" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-2">
                <label class="form-label">Count</label>
                <input type="number" min="1" max="100" name="count" class="form-control" value="10" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prefix</label>
                <input type="text" name="prefix" class="form-control" value="VCH" maxlength="10">
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" class="form-select" required>
                    <option value="percentage">Percentage</option>
                    <option value="flat">Flat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount Value</label>
                <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control" value="10" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Usage Limit</label>
                <input type="number" min="1" name="usage_limit" class="form-control" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Expiry</label>
                <input type="date" name="expiry_date" class="form-control" value="{{ now()->addMonth()->toDateString() }}" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary">Generate Vouchers</button>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Assigned User</th>
                    <th>Usage</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Assign</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                    <tr>
                        <td class="fw-semibold">{{ $voucher->code }}</td>
                        <td>{{ $voucher->discount_type === 'percentage' ? number_format((float) $voucher->discount_value, 2).'%' : 'Rs. '.number_format((float) $voucher->discount_value, 2) }}</td>
                        <td>
                            <div class="fw-semibold">{{ $voucher->user?->name ?? 'Unassigned' }}</div>
                            <div class="small text-muted">{{ $voucher->user?->email ?? '-' }}</div>
                        </td>
                        <td>{{ $voucher->used_count }}/{{ $voucher->usage_limit }}</td>
                        <td>{{ $voucher->expiry_date?->format('d M Y') }}</td>
                        <td>
                            @if($voucher->is_expired)
                                <span class="status-badge status-inactive">Expired</span>
                            @elseif(! $voucher->is_active)
                                <span class="status-badge status-blocked">Inactive</span>
                            @else
                                <span class="status-badge status-active">Active</span>
                            @endif
                        </td>
                        <td style="min-width: 220px;">
                            <form action="{{ route('admin.vouchers.assign', $voucher->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <select name="user_id" class="form-select form-select-sm searchable-select" data-searchable-select data-placeholder="Search user">
                                    <option value="">Assign user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected($voucher->assigned_to_user_id === $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Assign</button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.vouchers.show', $voucher) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this voucher?"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No vouchers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
        <div class="px-4 py-3 border-top">{{ $vouchers->links() }}</div>
    @endif
</div>
@endsection
