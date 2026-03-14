@extends('front.layouts.app')

@section('title', ($restaurantName ?? config('app.name', 'Restaurant Booking')) . ' | Homepage')
@section('meta_description', 'A modern restaurant homepage with grill dining, deals, catering, rewards, and booking flows.')

@section('content')
    <section class="hero-home">
        <div class="hero-home__backdrop">
            @foreach ($heroSlides as $index => $slide)
                <article class="hero-home__slide{{ $index === 0 ? ' is-active' : '' }}" data-hero-slide style="background-image: linear-gradient(100deg, rgba(28, 13, 5, 0.88), rgba(28, 13, 5, 0.28)), url('{{ $slide['image'] }}')">
                    <div class="container hero-home__content">
                        <div>
                            <p class="section-kicker">{{ $slide['eyebrow'] }}</p>
                            <h1>{{ $slide['title'] }}</h1>
                            <p class="hero-home__description">{{ $slide['description'] }}</p>
                            <div class="hero-home__cta">
                                <a class="button button--solid" href="{{ $slide['primary_cta']['url'] }}">{{ $slide['primary_cta']['label'] }}</a>
                                <a class="button button--ghost" href="{{ $slide['secondary_cta']['url'] }}">{{ $slide['secondary_cta']['label'] }}</a>
                            </div>
                        </div>

                        <div class="hero-home__booking-card">
                            <p>Table booking CTA</p>
                            <h2>Find the right dining slot before it disappears.</h2>
                            <ul>
                                @foreach ($stats as $stat)
                                    <li>
                                        <strong>{{ $stat['value'] }}</strong>
                                        <span>{{ $stat['label'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="hero-home__pagination container" aria-label="Hero slider controls">
            @foreach ($heroSlides as $index => $slide)
                <button class="{{ $index === 0 ? 'is-active' : '' }}" type="button" data-hero-dot aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    </section>

    <section class="booking-cta" id="booking-cta">
        <div class="container booking-cta__panel">
            <div>
                <p class="section-kicker">Book Your Table</p>
                <h2>Dining plans should feel immediate, not administrative.</h2>
                <p>Move guests from discovery to reservation with a prominent action, visible offer hooks, and a cleaner brand journey than the current placeholder welcome page.</p>
            </div>
            <a class="button button--solid" href="#services">Start Reservation Flow</a>
        </div>
    </section>

    <section class="services-grid" id="services">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Service Cards</p>
                <h2>Four clear entry points for the most valuable user intents.</h2>
            </div>

            <div class="services-grid__items">
                @foreach ($services as $service)
                    <article class="service-card">
                        <div class="service-card__visual">
                            <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}">
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="feature-strip" id="happiness-cards">
        <div class="container feature-strip__grid">
            <div class="feature-strip__content">
                <p class="section-kicker">Happiness Cards</p>
                <h2>Position prepaid dining as a premium reward product, not a buried utility page.</h2>
                <p>Use the homepage to sell the benefit stack: gifting, stored value, member pricing, faster repeat visits, and better occasion planning.</p>
                <div class="feature-strip__actions">
                    <a class="button button--solid" href="#">Explore Card Options</a>
                    <a class="button button--ghost button--ghost-dark" href="#">See Member Benefits</a>
                </div>
            </div>

            <div class="feature-strip__media">
                <img src="{{ asset('images/best-food-img-1.png') }}" alt="Happiness card meal visual">
                <div class="feature-strip__badge">
                    <strong>Gift-ready</strong>
                    <span>Ideal for birthdays, teams, and festive dining credits.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="offerings" id="offerings">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Our Offerings</p>
                <h2>The homepage flow mirrors a BBQN-style decision path without copying its design language.</h2>
            </div>

            <div class="offerings__grid">
                @foreach ($offerings as $offering)
                    <article class="offering-card">
                        <img src="{{ $offering['image'] }}" alt="{{ $offering['title'] }}">
                        <div>
                            <h3>{{ $offering['title'] }}</h3>
                            <p>{{ $offering['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="deals" id="deals">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <p class="section-kicker">Sizzling Deals</p>
                    <h2>Promotions should look current, selective, and easy to scan.</h2>
                </div>
                <a class="button button--solid" href="#">See All Offers</a>
            </div>

            <div class="deals__grid">
                @foreach ($deals as $deal)
                    <article class="deal-card">
                        <span>{{ $deal['badge'] }}</span>
                        <h3>{{ $deal['title'] }}</h3>
                        <p>{{ $deal['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="updates" id="updates">
        <div class="container">
            <div class="section-heading section-heading--split">
                <div>
                    <p class="section-kicker">Latest Updates</p>
                    <h2>Use the blog layer to keep the homepage feeling active between campaigns.</h2>
                </div>
                <a class="button button--ghost button--ghost-dark" href="#">Visit Blog</a>
            </div>

            <div class="updates__grid">
                @foreach ($blogs as $blog)
                    <article class="update-card">
                        <img src="{{ asset('images/' . ($blog['image'] ?? $blog->image ?? 'post-1.jpg')) }}" alt="{{ $blog['title'] ?? $blog->title }}">
                        <div>
                            <p>{{ optional($blog['created_at'] ?? $blog->created_at)->format('d M Y') }}</p>
                            <h3>{{ $blog['title'] ?? $blog->title }}</h3>
                            <a href="#">Read update</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
