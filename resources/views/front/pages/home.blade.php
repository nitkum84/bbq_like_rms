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
                                <button class="button button--solid" type="button" data-reservation-open>{{ $slide['primary_cta']['label'] }}</button>
                                <a class="button button--ghost" href="{{ $slide['secondary_cta']['url'] }}">{{ $slide['secondary_cta']['label'] }}</a>
                            </div>
                        </div>

                        <div class="hero-home__booking-card">
                            <p>Reserve Table</p>
                            <h2>Choose your slot, menu path, and pricing in one fast flow.</h2>
                            <ul>
                                @foreach ($stats as $stat)
                                    <li>
                                        <strong>{{ $stat['value'] }}</strong>
                                        <span>{{ $stat['label'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <button class="button button--solid hero-home__booking-button" type="button" data-reservation-open>Reserve Table</button>
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
                <h2>Pick your slot, food preference, and price before you commit.</h2>
                <p>Open the reservation sidebar to see live lunch and dinner availability, compare veg, non-veg, and package pricing, and confirm your booking with a clean summary.</p>
            </div>
            <button class="button button--solid" type="button" data-reservation-open>Start Reservation</button>
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
                    <article class="service-card{{ $service['title'] === 'Reserve Table' ? ' service-card--interactive' : '' }}">
                        <div class="service-card__visual">
                            <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}">
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                        @if ($service['title'] === 'Reserve Table')
                            <button class="button button--ghost-dark service-card__action" type="button" data-reservation-open>Reserve Table</button>
                        @endif
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
                <button class="button button--solid" type="button" data-reservation-open>Reserve With A Deal</button>
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

    <div class="reservation-drawer" data-reservation-drawer aria-hidden="true">
        <div class="reservation-drawer__overlay" data-reservation-close></div>
        <aside class="reservation-drawer__panel" aria-label="Reservation sidebar">
            <div class="reservation-drawer__top">
                <div>
                    <p class="section-kicker">Reserve Table</p>
                    <h2>Book {{ $reservationBootstrap['restaurant']['name'] }}</h2>
                </div>
                <button class="reservation-drawer__close" type="button" data-reservation-close>Close</button>
            </div>

            <div class="reservation-alert" data-reservation-feedback></div>

            <form class="reservation-form" method="POST" action="{{ $reservationBootstrap['storeUrl'] }}" data-reservation-form data-reservation-bootstrap='@json($reservationBootstrap)'>
                @csrf
                <input type="hidden" name="slot_id" value="">

                <div class="reservation-form__grid">
                    <section class="reservation-card">
                        <div class="reservation-card__header">
                            <h3>1. Plan your visit</h3>
                            <p>Select the date, meal, and live slot availability.</p>
                        </div>

                        <label class="reservation-field">
                            <span>Date</span>
                            <input type="date" name="date" value="{{ $reservationBootstrap['defaultDate'] }}" min="{{ $reservationBootstrap['minDate'] }}" required>
                        </label>

                        <div class="reservation-field">
                            <span>Meal</span>
                            <div class="reservation-toggle-group" data-meal-toggle>
                                <button type="button" class="is-active" data-meal-option="lunch">Lunch</button>
                                <button type="button" data-meal-option="dinner">Dinner</button>
                            </div>
                            <input type="hidden" name="meal_type" value="{{ $reservationBootstrap['defaultMealType'] }}">
                        </div>

                        <div class="reservation-field">
                            <span>Available slots</span>
                            <div class="reservation-slot-grid" data-slot-list></div>
                            <p class="reservation-field__hint">Live availability updates as you change date, meal, or guest count.</p>
                        </div>
                    </section>

                    <section class="reservation-card">
                        <div class="reservation-card__header">
                            <h3>2. Guests and food</h3>
                            <p>Choose guest count and the menu path you want priced.</p>
                        </div>

                        <label class="reservation-field">
                            <span>Guests</span>
                            <input type="number" name="guests" min="1" max="20" value="{{ $reservationBootstrap['defaultGuests'] }}" required>
                        </label>

                        <div class="reservation-field">
                            <span>Food preference</span>
                            <div class="reservation-food-grid" data-food-options></div>
                            <input type="hidden" name="food_preference" value="{{ $reservationBootstrap['defaultFoodPreference'] }}">
                        </div>

                        <label class="reservation-field reservation-package-field" data-package-field hidden>
                            <span>Package</span>
                            <select name="deals_bundle_id" data-package-select>
                                <option value="">Select a package</option>
                            </select>
                        </label>

                        <div class="reservation-price-panel" data-price-panel>
                            <div>
                                <span>Price per guest</span>
                                <strong data-price-per-guest>Rs. 0.00</strong>
                            </div>
                            <div>
                                <span>Total amount</span>
                                <strong data-total-price>Rs. 0.00</strong>
                            </div>
                            <p data-price-label>Pricing will appear here.</p>
                        </div>
                    </section>

                    <section class="reservation-card">
                        <div class="reservation-card__header">
                            <h3>3. Contact details</h3>
                            <p>We use these details for your confirmation.</p>
                        </div>

                        <label class="reservation-field">
                            <span>Name</span>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name ?? '') }}" required>
                        </label>

                        <label class="reservation-field">
                            <span>Email</span>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email ?? '') }}" required>
                        </label>

                        <label class="reservation-field">
                            <span>Mobile</span>
                            <input type="text" name="mobile" value="{{ old('mobile', auth()->user()?->mobile ?? '') }}" required>
                        </label>

                        <label class="reservation-field">
                            <span>Special request</span>
                            <textarea name="special_request" rows="3" placeholder="Birthday setup, accessibility note, seating preference...">{{ old('special_request') }}</textarea>
                        </label>
                    </section>

                    <section class="reservation-card reservation-card--summary">
                        <div class="reservation-card__header">
                            <h3>4. Booking summary</h3>
                            <p>Review everything before you confirm.</p>
                        </div>

                        <dl class="reservation-summary" data-summary>
                            <div>
                                <dt>Restaurant</dt>
                                <dd data-summary-restaurant>{{ $reservationBootstrap['restaurant']['name'] }}</dd>
                            </div>
                            <div>
                                <dt>Date & time</dt>
                                <dd data-summary-datetime>Choose a date and slot</dd>
                            </div>
                            <div>
                                <dt>Guests</dt>
                                <dd data-summary-guests>{{ $reservationBootstrap['defaultGuests'] }} guests</dd>
                            </div>
                            <div>
                                <dt>Food selection</dt>
                                <dd data-summary-food>{{ ucfirst($reservationBootstrap['defaultFoodPreference']) }}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd data-summary-total>Rs. 0.00</dd>
                            </div>
                        </dl>

                        <button class="button button--solid reservation-submit" type="submit">Confirm Booking</button>
                    </section>
                </div>
            </form>
        </aside>
    </div>
@endsection

