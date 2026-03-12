@php
    $blog = $blog ?? null;
    $previewImage = $blog?->image_url ?? 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 180"><rect width="300" height="180" rx="12" fill="#f5f6fa"/><path d="M55 125l42-42 32 30 42-50 54 62H55z" fill="#d5dce3"/><circle cx="105" cy="62" r="14" fill="#c0392b"/><text x="150" y="156" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="16" fill="#7f8c8d">No image selected</text></svg>');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Post Content</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $blog?->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="blogContent" class="form-control @error('content') is-invalid @enderror" rows="16">{{ old('content', $blog?->content) }}</textarea>
                    @error('content')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>SEO</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $blog?->meta_title) }}">
                    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3">{{ old('meta_description', $blog?->meta_description) }}</textarea>
                    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Publish</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="draft" @selected(old('status', $blog?->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $blog?->status) === 'published')>Published</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @if($blog?->slug)
                    <div class="small text-muted mb-3">Slug: <span class="fw-semibold">{{ $blog->slug }}</span></div>
                @endif
                <button type="submit" class="btn btn-primary w-100">{{ $submitLabel }}</button>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h5>Featured Image</h5></div>
            <div class="admin-card-body">
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="blogImg">
                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <img id="blogImgPreview" src="{{ $previewImage }}" class="img-preview-lg mt-2" alt="Blog preview">
            </div>
        </div>
    </div>
</div>
