@extends('front.layouts.app')

@section('title', $card->title . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-detail-page">
        <div class="container content-detail-page__grid">
            <article class="content-detail-card">
                <p class="section-kicker">Happiness Card</p>
                <h1>{{ $card->title }}</h1>
                <p class="content-detail-meta">Available till {{ $card->display_to?->format('d M Y') }}</p>
                <img class="content-detail-image" src="{{ $card->image ? asset('storage/' . $card->image) : asset('images/best-food-img-1.png') }}" alt="{{ $card->title }}">
                <div class="content-richtext">
                    <p>{{ $card->description }}</p>
                    @if ($card->link)
                        <p><a class="button button--solid" href="{{ $card->link }}">Learn More</a></p>
                    @endif
                </div>
            </article>

            <aside class="content-detail-sidebar">
                <div class="content-detail-card">
                    <h2>More benefits</h2>
                    @foreach ($relatedCards as $relatedCard)
                        <a class="content-detail-link" href="{{ route('happiness-cards.show', $relatedCard) }}">{{ $relatedCard->title }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection
