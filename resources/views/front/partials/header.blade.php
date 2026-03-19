<header class="front-header" id="top">
    <div class="front-header__topbar">
        <div class="container front-header__topbar-inner">
            <p>Unlimited grill experiences, celebration tables, and curated buffet moments.</p>
            <button class="front-header__topbar-cta" type="button" data-reservation-open>Book Your Table</button>
        </div>
    </div>

    <div class="container front-header__inner">
        <a class="front-header__brand" href="{{ route('home') }}" aria-label="Restaurant homepage">
            <img src="{{ ($frontLogo = \App\Models\WebsiteSetting::get('logo')) ? \Illuminate\Support\Facades\Storage::url($frontLogo) : asset('images/logo.svg') }}" alt="{{ $restaurantName ?? config('app.name', 'Restaurant Booking') }} logo">
        </a>

        <nav class="front-header__nav" aria-label="Primary navigation">
            @foreach ($primaryNavigation as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="front-header__actions">
            <a class="front-header__profile" href="{{ $profileUrl }}" aria-label="Profile">
                <span></span>
            </a>
            <button class="front-header__menu-toggle" type="button" data-sidebar-open aria-controls="front-sidebar" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
