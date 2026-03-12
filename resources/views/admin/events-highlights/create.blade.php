@extends('admin.layouts.app')
@section('title', 'Add Highlight')
@section('content')
<div class="page-header"><h1 class="page-title">Add Highlight</h1><a href="{{ route('admin.events-highlights.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
<form action="{{ route('admin.events-highlights.store') }}" method="POST" enctype="multipart/form-data">@csrf
    @include('admin.events-highlights._form', ['submitLabel' => 'Save Highlight'])
</form>
@endsection
@push('scripts')
<script>
document.getElementById('highlightImg').addEventListener('change', function() {
    if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('highlightImgPreview').src = e.target.result;
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
