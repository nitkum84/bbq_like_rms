@extends('admin.layouts.app')
@section('title', 'Edit Table')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Table
        <span class="subtitle">Update table details and status</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tables.show', $table) }}" class="btn btn-outline-primary">View Table</a>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        <form action="{{ route('admin.tables.update', $table) }}" method="POST">
            @csrf
            @method('PUT')
            @php($submitLabel = 'Save Changes')
            @include('admin.tables._form', ['submitLabel' => $submitLabel])
        </form>
    </div>
</div>
@endsection
