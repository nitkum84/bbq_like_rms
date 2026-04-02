@extends('front.layouts.app')

@section('title', $pageTitle . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-listing-page">
        <div class="container">
            <div class="content-page-hero">
                <p class="section-kicker">Stories</p>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>
            </div>

            <div class="updates__grid">
                @forelse ($blogs as $blog)
                    <a class="update-card update-card--link" href="{{ route('blogs.show', $blog) }}">
                        <img src="{{ $blog->image ? asset('storage/' . $blog->image) : asset('images/post-1.jpg') }}" alt="{{ $blog->title }}">
                        <div>
                            <p>{{ $blog->published_at?->format('d M Y') ?? $blog->created_at?->format('d M Y') }}</p>
                            <h3>{{ $blog->title }}</h3>
                            <span class="update-card__cta">Read article</span>
                        </div>
                    </a>
                @empty
                    <p>No blogs have been published yet.</p>
                @endforelse
            </div>

            <div class="content-page-pagination">
                {{ $blogs->links() }}
            </div>
        </div>
    </section>
@endsection
