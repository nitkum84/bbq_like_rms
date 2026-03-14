<footer class="front-footer" id="footer">
    <div class="container front-footer__grid">
        <div class="front-footer__nav">
            <img src="{{ asset('images/footer-logo.svg') }}" alt="Restaurant footer logo">
            <div class="front-footer__links">
                @foreach ($sidebarSections as $section)
                    @foreach ($section['items'] as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="front-footer__about">
            <p class="front-footer__eyebrow">About The Experience</p>
            <h2>A grill-first restaurant journey designed around celebrations, flexibility, and repeat-worthy value.</h2>
            <p>Phase 1 focuses on a strong homepage foundation: clear navigation, strong booking prompts, card-led rewards positioning, and a layout ready to connect to the existing Laravel admin modules later.</p>
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
