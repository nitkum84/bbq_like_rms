@extends('admin.layouts.app')
@section('title','Blog Posts')

@section('content')
<div class="page-header">
    <h1 class="page-title">Blog Management</h1>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Post</a>
</div>
<div class="admin-card">
    <div class="table-responsive">
        <table class="table-admin">
            <thead><tr><th>Image</th><th>Title</th><th>Author</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($blogs as $blog)
                <tr>
                    <td><img src="{{ $blog->image_url }}" style="width:60px;height:40px;object-fit:cover;border-radius:6px"></td>
                    <td>
                        <div class="fw-semibold">{{ $blog->title }}</div>
                        <div class="text-muted small">{{ $blog->slug }}</div>
                    </td>
                    <td>{{ $blog->author->name ?? '—' }}</td>
                    <td><span class="status-badge {{ $blog->status === 'published' ? 'status-confirmed' : 'status-pending' }}">{{ ucfirst($blog->status) }}</span></td>
                    <td>{{ $blog->published_at ? $blog->published_at->format('d M Y') : '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.blogs.edit',$blog) }}" class="btn btn-icon btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.blogs.destroy',$blog) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon btn-outline-danger btn-sm" data-confirm="Delete this post?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No blog posts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blogs->hasPages())<div class="px-4 py-3 border-top">{{ $blogs->links() }}</div>@endif
</div>
@endsection
