<aside class="front-sidebar" id="front-sidebar" aria-hidden="true">
    <div class="front-sidebar__overlay" data-sidebar-close></div>
    <div class="front-sidebar__panel">
        <div class="front-sidebar__top">
            <img src="{{ ($frontLogo = \App\Models\WebsiteSetting::get('logo')) ? \Illuminate\Support\Facades\Storage::url($frontLogo) : asset('images/footer-logo.svg') }}" alt="{{ $restaurantName ?? config('app.name', 'Restaurant Booking') }} logo">
            <button type="button" class="front-sidebar__close" data-sidebar-close aria-label="Close menu">Close</button>
        </div>

        <div class="front-sidebar__body">
            @foreach ($sidebarSections as $section)
                <section class="front-sidebar__section">
                    <p>{{ $section['title'] }}</p>
                    <ul>
                        @foreach ($section['items'] as $item)
                            <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    </div>
</aside>
