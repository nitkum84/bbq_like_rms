@php
    $eventsHighlight = $eventsHighlight ?? null;
    $previewImage = $eventsHighlight?->image ? asset('storage/'.$eventsHighlight->image) : 'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 180"><rect width="300" height="180" rx="12" fill="#f5f6fa"/><path d="M55 125l42-42 32 30 42-50 54 62H55z" fill="#d5dce3"/><circle cx="105" cy="62" r="14" fill="#c0392b"/><text x="150" y="156" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="16" fill="#7f8c8d">No banner selected</text></svg>');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header"><h5>Highlight Details</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $eventsHighlight?->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $eventsHighlight?->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">CTA Link</label>
                    <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $eventsHighlight?->link) }}" placeholder="https://example.com/event">
                    @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header"><h5>Display Window</h5></div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <label class="form-label">Display From</label>
                    <input type="date" name="display_from" class="form-control @error('display_from') is-invalid @enderror" value="{{ old('display_from', $eventsHighlight?->display_from?->toDateString() ?? now()->toDateString()) }}" required>
                    @error('display_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Display To</label>
                    <input type="date" name="display_to" class="form-control @error('display_to') is-invalid @enderror" value="{{ old('display_to', $eventsHighlight?->display_to?->toDateString() ?? now()->addWeek()->toDateString()) }}" required>
                    @error('display_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Display Order</label>
                    <input type="number" name="display_order" min="0" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', $eventsHighlight?->display_order ?? 0) }}">
                    @error('display_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $eventsHighlight?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active banner</label>
                </div>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h5>Banner Image</h5></div>
            <div class="admin-card-body">
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="highlightImg">
                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <img id="highlightImgPreview" src="{{ $previewImage }}" class="img-preview-lg mt-2" alt="Highlight preview">
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.events-highlights.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
