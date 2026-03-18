<footer class="front-footer" id="footer">
    <div class="container front-footer__grid">
        <div class="front-footer__brand">
            <img src="{{ asset('images/logo.svg') }}" alt="Restaurant logo">
            <p class="front-footer__eyebrow">Celebration-first dining</p>
            <h2>A brighter homepage experience built for reservations, rewards, and repeat visits.</h2>
            <p>Designed to feel closer to a modern restaurant brand landing page, while still connecting to your Laravel booking flow and future admin-driven content.</p>
        </div>

        <div class="front-footer__nav">
            <div class="front-footer__column">
                <p class="front-footer__eyebrow">Explore</p>
                <div class="front-footer__links">
                    @foreach ($sidebarSections as $section)
                        @foreach ($section['items'] as $item)
                            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="front-footer__column">
                <p class="front-footer__eyebrow">Plan Your Visit</p>
                <div class="front-footer__contact">
                    <p>Reservations, private dining, catering, and reward-led visits from one homepage flow.</p>
                    <button class="button button--solid front-footer__button" type="button" data-reservation-open>Reserve Table</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container front-footer__bottom">
        <div class="front-footer__legal">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
        </div>
        <p>Copyright &copy; {{ now()->year }} {{ $restaurantName ?? config('app.name', 'Restaurant Booking') }}. All rights reserved.</p>
    </div>
</footer>
