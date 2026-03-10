@extends('admin.layouts.app')
@section('title','Menu Categories')

@section('content')
<div class="page-header">
    <h1 class="page-title">Menu Categories
        <span class="subtitle">Manage food categories for the menu</span>
    </h1>
    <a href="{{ route('admin.menu-categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Category
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>#</th><th>Name</th><th>Type</th>
                    <th>Items</th><th>Order</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $cat->name }}</td>
                    <td>
                        @if($cat->type === 'veg')
                            <span class="badge" style="background:#eafaf1;color:#27ae60">🟢 Veg</span>
                        @elseif($cat->type === 'non-veg')
                            <span class="badge" style="background:#fdf2f2;color:#c0392b">🔴 Non-Veg</span>
                        @else
                            <span class="badge bg-secondary">Both</span>
                        @endif
                    </td>
                    <td>{{ $cat->menu_items_count }}</td>
                    <td>{{ $cat->display_order }}</td>
                    <td>
                        <span class="status-badge {{ $cat->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.menu-categories.edit',$cat) }}" class="btn btn-icon btn-outline-primary btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.menu-categories.destroy',$cat) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this category?" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No categories found. <a href="{{ route('admin.menu-categories.create') }}">Add one</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="px-4 py-3 border-top">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
