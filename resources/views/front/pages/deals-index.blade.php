@extends('front.layouts.app')

@section('title', $pageTitle . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-listing-page">
        <div class="container">
            <div class="content-page-hero">
                <p class="section-kicker">Offers</p>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>
            </div>

            <div class="deals__grid">
                @forelse ($deals as $deal)
                    <article class="deal-card">
                        <span>{{ number_format((float) $deal->discount_percent, 2) }}% off</span>
                        <h3>{{ $deal->name }}</h3>
                        <p>{{ $deal->description }}</p>
                        <div class="deal-card__actions">
                            <a href="{{ route('deals.show', $deal) }}">View details</a>
                            <a href="{{ route('home', ['deal' => $deal->id]) }}">Book with this deal</a>
                        </div>
                    </article>
                @empty
                    <p>No deals are active right now.</p>
                @endforelse
            </div>

            <div class="content-page-pagination">
                {{ $deals->links() }}
            </div>
        </div>
    </section>
@endsection
