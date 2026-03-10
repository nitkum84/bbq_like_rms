@extends('admin.layouts.app')
@section('title','New Blog Post')

@section('content')
<div class="page-header">
    <h1 class="page-title">New Blog Post</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4"><div class="admin-card-header"><h5>Post Content</h5></div>
        <div class="admin-card-body">
            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Content <span class="text-danger">*</span></label>
                <textarea name="content" id="blogContent" class="form-control" rows="15">{{ old('content') }}</textarea>
            </div>
        </div></div>
        <div class="admin-card"><div class="admin-card-header"><h5>SEO</h5></div>
        <div class="admin-card-body">
            <div class="mb-3">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
            </div>
            <div class="mb-0">
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description') }}</textarea>
            </div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card mb-4"><div class="admin-card-header"><h5>Publish</h5></div>
        <div class="admin-card-body">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Post</button>
        </div></div>
        <div class="admin-card"><div class="admin-card-header"><h5>Featured Image</h5></div>
        <div class="admin-card-body">
            <input type="file" name="image" class="form-control" accept="image/*" id="blogImg">
            <img id="blogImgPreview" src="{{ asset('admin/images/no-image.png') }}" class="img-preview-lg mt-2">
        </div></div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('blogImg').addEventListener('change', function() {
    if (this.files[0]) {
        const r = new FileReader();
        r.onload = e => document.getElementById('blogImgPreview').src = e.target.result;
        r.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
