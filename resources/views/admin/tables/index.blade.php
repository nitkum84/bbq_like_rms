@extends('admin.layouts.app')
@section('title','Tables')

@section('content')
<div class="page-header">
    <h1 class="page-title">Table Management
        <span class="subtitle">Manage restaurant dining tables</span>
    </h1>
    <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add Table
    </a>
</div>

<!-- Visual Grid -->
<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h5><i class="bi bi-grid me-2"></i>Table Overview — Today</h5>
        <div class="d-flex gap-3 small">
            <span><span class="badge" style="background:#eafaf1;color:#27ae60">●</span> Available</span>
            <span><span class="badge" style="background:#fdf2f2;color:#c0392b">●</span> Booked</span>
            <span><span class="badge bg-secondary">●</span> Blocked/Inactive</span>
        </div>
    </div>
    <div class="admin-card-body">
        <div class="table-grid">
            @foreach($tables as $t)
            @php
                $cls = $t->status !== 'active' ? 'blocked' : ($t->today_bookings_count > 0 ? 'booked' : 'available');
            @endphp
            <a href="{{ route('admin.tables.show',$t) }}" class="table-item {{ $cls }} text-decoration-none text-dark">
                <div class="table-num">T{{ $t->table_number }}</div>
                <div class="table-cap"><i class="bi bi-people me-1"></i>{{ $t->seating_capacity }}</div>
                <div class="small mt-1 text-muted">{{ $t->location ?? 'Main Floor' }}</div>
                <div class="small mt-1 fw-semibold">
                    @if($t->status !== 'active') {{ ucfirst($t->status) }}
                    @elseif($t->today_bookings_count > 0) Booked ({{ $t->today_bookings_count }})
                    @else Available @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Table List -->
<div class="admin-card">
    <div class="admin-card-header"><h5>All Tables</h5></div>
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>#</th><th>Table No.</th><th>Capacity</th><th>Location</th><th>Status</th><th>Today's Bookings</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($tables as $table)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $table->table_number }}</td>
                    <td>{{ $table->seating_capacity }} seats</td>
                    <td>{{ $table->location ?? '—' }}</td>
                    <td><span class="status-badge status-{{ $table->status }}">{{ ucfirst($table->status) }}</span></td>
                    <td>{{ $table->today_bookings_count }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.tables.show',$table) }}" class="btn btn-icon btn-outline-info btn-sm"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.tables.edit',$table) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.tables.destroy',$table) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this table?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
