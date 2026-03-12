@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="page-header"><h1 class="page-title">Edit User</h1><a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
<div class="row justify-content-center"><div class="col-lg-8"><div class="admin-card"><div class="admin-card-header"><h5>User Details</h5></div><div class="admin-card-body"><form action="{{ route('admin.users.update', $user) }}" method="POST">@csrf @method('PUT') @include('admin.users._form', ['submitLabel' => 'Update User'])</form></div></div></div></div>
@endsection
