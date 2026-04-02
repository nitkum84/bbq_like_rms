@extends('front.layouts.app')

@section('title', ($blog->meta_title ?: $blog->title) . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))
@section('meta_description', $blog->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($blog->content), 155))

@section('content')
    <section class="content-detail-page">
        <div class="container content-detail-page__grid">
            <article class="content-detail-card">
                <p class="section-kicker">Blog Detail</p>
                <h1>{{ $blog->title }}</h1>
                <p class="content-detail-meta">Published {{ $blog->published_at?->format('d M Y') ?? $blog->created_at?->format('d M Y') }}{{ $blog->author ? ' • By ' . $blog->author->name : '' }}</p>
                <img class="content-detail-image" src="{{ $blog->image ? asset('storage/' . $blog->image) : asset('images/post-1.jpg') }}" alt="{{ $blog->title }}">
                <div class="content-richtext">{!! $blog->content !!}</div>
            </article>

            <aside class="content-detail-sidebar">
                <div class="content-detail-card">
                    <h2>More stories</h2>
                    @foreach ($relatedBlogs as $relatedBlog)
                        <a class="content-detail-link" href="{{ route('blogs.show', $relatedBlog->slug) }}">{{ $relatedBlog->title }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection
