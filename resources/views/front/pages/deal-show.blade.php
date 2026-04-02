@extends('front.layouts.app')

@section('title', $deal->name . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-detail-page">
        <div class="container content-detail-page__grid">
            <article class="content-detail-card">
                <p class="section-kicker">Deal Detail</p>
                <h1>{{ $deal->name }}</h1>
                <p class="content-detail-meta">{{ number_format((float) $deal->discount_percent, 2) }}% off • Valid till {{ $deal->valid_to?->format('d M Y') }}</p>
                <div class="content-richtext">
                    <p>{{ $deal->description }}</p>
                    @if ($deal->menuItems->isNotEmpty())
                        <h2>Included items</h2>
                        <ul class="content-bullet-list">
                            @foreach ($deal->menuItems as $menuItem)
                                <li>{{ $menuItem->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <a class="button button--solid" href="{{ route('home', ['deal' => $deal->id]) }}">Book Table With Offer</a>
            </article>

            <aside class="content-detail-sidebar">
                <div class="content-detail-card">
                    <h2>Other deals</h2>
                    @foreach ($relatedDeals as $relatedDeal)
                        <a class="content-detail-link" href="{{ route('deals.show', $relatedDeal) }}">{{ $relatedDeal->name }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection
