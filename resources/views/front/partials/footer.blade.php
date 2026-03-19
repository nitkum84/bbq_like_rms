<footer class="front-footer" id="footer">
    @php
        $frontLogo = \App\Models\WebsiteSetting::get('logo');
        $facebookUrl = \App\Models\WebsiteSetting::get('facebook_url');
        $instagramUrl = \App\Models\WebsiteSetting::get('instagram_url');
        $mapsUrl = \App\Models\WebsiteSetting::get('google_maps_url');
        $socialLinks = array_filter([
            ['label' => 'Facebook', 'url' => $facebookUrl, 'icon' => 'f'],
            ['label' => 'Instagram', 'url' => $instagramUrl, 'icon' => 'i'],
            ['label' => 'Location', 'url' => $mapsUrl, 'icon' => 'm'],
        ], fn ($item) => filled($item['url']));
    @endphp

    <div class="container front-footer__frame">
        <div class="front-footer__main">
            <div class="front-footer__nav-panel">
                <div class="front-footer__brand-mark">
                    <img style="width: 100px;" src="{{ $frontLogo ? \Illuminate\Support\Facades\Storage::url($frontLogo) : asset('images/logo.svg') }}" alt="{{ $restaurantName ?? config('app.name', 'Restaurant Booking') }} logo">
                </div>

                <div class="front-footer__link-groups">
                    @foreach ($sidebarSections as $section)
                        <section class="front-footer__group" aria-label="{{ $section['title'] }}">
                            <h3>{{ $section['title'] }}</h3>
                            <div class="front-footer__links">
                                @foreach ($section['items'] as $item)
                                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>

        @if (! empty($socialLinks))
            <div class="front-footer__meta">
                <div class="front-footer__socials" aria-label="Social links">
                    @foreach ($socialLinks as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noreferrer" aria-label="{{ $social['label'] }}">
                            <span>{{ strtoupper($social['icon']) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="front-footer__bottom">
            <p>&copy; All Rights Reserved by {{ $restaurantName ?? config('app.name', 'Restaurant Booking') }}</p>
            <div class="front-footer__legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Term &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>
