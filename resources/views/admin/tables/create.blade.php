@extends('admin.layouts.app')
@section('title', 'Add Table')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Table
        <span class="subtitle">Create a new restaurant table entry</span>
    </h1>
    <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            @php($submitLabel = 'Create Table')
            @include('admin.tables._form', ['table' => null, 'submitLabel' => $submitLabel])
        </form>
    </div>
</div>
@endsection
