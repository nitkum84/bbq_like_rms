@extends('admin.layouts.app')
@section('title','New Blog Post')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">New Blog Post</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.blogs._form', ['submitLabel' => 'Save Post'])
</form>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>
<script>
document.getElementById('blogImg').addEventListener('change', function() {
    if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('blogImgPreview').src = e.target.result;
        reader.readAsDataURL(this.files[0]);
    }
});

jQuery(function ($) {
    $('#blogContent').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['link'],
            ['insertImage'],
            ['unorderedList', 'orderedList'],
            ['justifyLeft', 'justifyCenter', 'justifyRight'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        autogrow: true,
        removeformatPasted: true
    });
});
</script>
@endpush
