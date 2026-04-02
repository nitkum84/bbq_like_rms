@extends('front.layouts.app')

@section('title', $pageTitle . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-listing-page">
        <div class="container">
            <div class="content-page-hero">
                <p class="section-kicker">Rewards</p>
                <h1>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>
            </div>

            <div class="deals__grid">
                @forelse ($cards as $card)
                    <article class="deal-card">
                        <span>Active Benefit</span>
                        <h3>{{ $card->title }}</h3>
                        <p>{{ $card->description }}</p>
                        <a href="{{ route('happiness-cards.show', $card) }}">View details</a>
                    </article>
                @empty
                    <p>No happiness cards are active right now.</p>
                @endforelse
            </div>

            <div class="content-page-pagination">
                {{ $cards->links() }}
            </div>
        </div>
    </section>
@endsection
