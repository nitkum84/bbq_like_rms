@extends('admin.layouts.app')
@section('title','Menu Items')

@section('content')
<div class="page-header">
    <h1 class="page-title">Menu Items
        <span class="subtitle">Manage daily menu items</span>
    </h1>
    <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Item
    </a>
</div>

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter by Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.menu-items.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                    <th>Image</th><th>Name</th><th>Category</th>
                    <th>Available</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menuItems as $item)
                <tr>
                    <td><input type="checkbox" class="form-check-input item-check" value="{{ $item->id }}"></td>
                    <td>
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                            style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->name }}</div>
                        <div class="text-muted small">{{ Str::limit($item->description,60) }}</div>
                    </td>
                    <td>{{ $item->category->name ?? '—' }}</td>
                    <td>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" {{ $item->is_available ? 'checked' : '' }}
                                data-toggle-url="{{ route('admin.menu-items.toggle',$item->id) }}">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.menu-items.edit',$item) }}" class="btn btn-icon btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.menu-items.destroy',$item) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this item?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No menu items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Bulk Actions -->
    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
        <form action="{{ route('admin.menu-items.bulk-toggle') }}" method="POST" id="bulkForm">
            @csrf
            <input type="hidden" name="ids" id="bulkIds">
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted small">Bulk:</span>
                <button type="submit" name="action" value="enable" class="btn btn-sm btn-outline-success">Enable Selected</button>
                <button type="submit" name="action" value="disable" class="btn btn-sm btn-outline-warning">Disable Selected</button>
            </div>
        </form>
        @if($menuItems->hasPages())
            {{ $menuItems->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.item-check').forEach(c => c.checked = this.checked);
});
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const ids = Array.from(document.querySelectorAll('.item-check:checked')).map(c => c.value);
    if (!ids.length) { e.preventDefault(); alert('Please select at least one item.'); return; }
    document.getElementById('bulkIds').value = JSON.stringify(ids);
});
</script>
@endpush
