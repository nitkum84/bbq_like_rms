@extends('admin.layouts.app')
@section('title', 'Add User')
@section('content')
<div class="page-header"><h1 class="page-title">Add User</h1><a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
<div class="row justify-content-center"><div class="col-lg-8"><div class="admin-card"><div class="admin-card-header"><h5>User Details</h5></div><div class="admin-card-body"><form action="{{ route('admin.users.store') }}" method="POST">@csrf @include('admin.users._form', ['submitLabel' => 'Save User'])</form></div></div></div></div>
@endsection
