@extends('admin.layouts.app')
@section('title', 'Edit Highlight')
@section('content')
<div class="page-header"><h1 class="page-title">Edit Highlight</h1><a href="{{ route('admin.events-highlights.show', $eventsHighlight) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a></div>
<form action="{{ route('admin.events-highlights.update', $eventsHighlight) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
    @include('admin.events-highlights._form', ['submitLabel' => 'Update Highlight'])
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
