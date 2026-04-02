<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\DealsBundle;
use App\Models\EventsHighlight;
use App\Models\WebsiteSetting;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {
    }

    public function __invoke(): View
    {
        $restaurantName = WebsiteSetting::get('restaurant_name', config('app.name', 'Restaurant Booking'));
        $homeUrl = route('home');
        $guestAccountUrl = Route::has('login') ? route('login') : '#';
        $profileUrl = $guestAccountUrl;
        $frontDashboardUrl = null;

        if (Auth::check()) {
            $profileUrl = Auth::user()->hasRole('super-admin')
                ? route('admin.dashboard')
                : route('dashboard');

            if (! Auth::user()->hasRole('super-admin')) {
                $frontDashboardUrl = route('dashboard');
            }
        }

        $accountUrl = $frontDashboardUrl ?: $guestAccountUrl;

        $primaryNavigation = [
            ['label' => 'Happiness Cards', 'url' => $homeUrl.'#happiness-cards'],
            ['label' => "What's On {$restaurantName}", 'url' => route('blogs.index')],
            ['label' => 'Deals', 'url' => route('deals.index')],
            ['label' => 'Restaurants', 'url' => $homeUrl.'#booking-cta'],
        ];

        $sidebarSections = [
            [
                'title' => 'Main',
                'items' => [
                    ['label' => $restaurantName, 'url' => $homeUrl],
                    ['label' => 'Home', 'url' => $homeUrl],
                    ['label' => "What's On {$restaurantName}", 'url' => route('blogs.index')],
                    ['label' => 'Deals', 'url' => route('deals.index')],
                    ['label' => 'Delivery / Takeaway', 'url' => $homeUrl.'#services'],
                    ['label' => 'Restaurants', 'url' => $homeUrl.'#booking-cta'],
                    ['label' => 'Happiness Cards', 'url' => route('happiness-cards.index')],
                    ['label' => 'Catering', 'url' => route('enquiries.create')],
                ],
            ],
            [
                'title' => 'Profile',
                'items' => [
                    ['label' => 'Profile', 'url' => $accountUrl],
                    ['label' => 'My Reservations', 'url' => $frontDashboardUrl ? $frontDashboardUrl.'#bookings' : $accountUrl],
                    ['label' => 'My Profile', 'url' => $frontDashboardUrl ? $frontDashboardUrl.'#profile' : $accountUrl],
                    ['label' => 'My Smiles', 'url' => $frontDashboardUrl ? $frontDashboardUrl.'#rewards' : $accountUrl],
                    ['label' => 'My Happiness Card', 'url' => $frontDashboardUrl ? $frontDashboardUrl.'#rewards' : $accountUrl],
                    ['label' => 'Delivery History', 'url' => $frontDashboardUrl ? $frontDashboardUrl.'#rewards' : $accountUrl],
                ],
            ],
            [
                'title' => 'About',
                'items' => [
                    ['label' => 'About Us', 'url' => $homeUrl.'#footer'],
                    ['label' => 'Blogs', 'url' => route('blogs.index')],
                    ['label' => 'Smiles', 'url' => route('happiness-cards.index')],
                ],
            ],
            [
                'title' => 'Others',
                'items' => [
                    ['label' => 'Contact Us', 'url' => $homeUrl.'#footer'],
                    ['label' => 'FAQ', 'url' => $homeUrl.'#footer'],
                    ['label' => 'Corporate Enquiry', 'url' => route('enquiries.create')],
                    ['label' => 'Investor Relations', 'url' => $homeUrl.'#footer'],
                ],
            ],
        ];

        $heroSlides = [
            [
                'eyebrow' => 'Live Grill. Loud Flavour.',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'An indulgent grill-first dining experience with buffet theatre, festive add-ons, and occasion-ready tables.',
                'image' => asset('images/hero-bg.jpg'),
                'primary_cta' => ['label' => 'Reserve Table', 'url' => '#booking-cta'],
                'secondary_cta' => ['label' => 'Explore Deals', 'url' => '#deals'],
            ],
            [
                'eyebrow' => 'Celebrate Bigger',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'Curated spreads, live counters, and shareable upgrades that turn casual plans into full events.',
                'image' => asset('images/events-image.jpg'),
                'primary_cta' => ['label' => 'Reserve Table', 'url' => '#booking-cta'],
                'secondary_cta' => ['label' => "What's On {$restaurantName}", 'url' => '#offerings'],
            ],
            [
                'eyebrow' => 'Rewards That Return',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'Keep the relationship warm between visits with prepaid value, exclusive access, and smarter gifting.',
                'image' => asset('images/testimonials-image.jpg'),
                'primary_cta' => ['label' => 'Reserve Table', 'url' => '#booking-cta'],
                'secondary_cta' => ['label' => 'See Latest Updates', 'url' => '#updates'],
            ],
        ];

        $services = [
            [
                'title' => 'Reserve Table',
                'description' => 'Quick group booking with smart time windows and celebration-friendly seating.',
                'image' => asset('images/reserve-table-content-bg.png'),
            ],
            [
                'title' => 'Happiness Cards',
                'description' => 'Prepaid dining value with gifting, member perks, and repeat-visit rewards.',
                'image' => asset('images/icon-cta-counter-1.svg'),
            ],
            [
                'title' => 'Catering',
                'description' => 'Live grill counters and buffet setups for office lunches, weddings, and private events.',
                'image' => asset('images/why-choose-img-2.png'),
            ],
            [
                'title' => 'Delivery / Takeaway',
                'description' => 'Signature favourites packed for weekday cravings, family dinners, and quick celebrations.',
                'image' => asset('images/our-menu-image-4.png'),
            ],
        ];

        $offerings = [
            [
                'title' => 'BBQ Dining',
                'description' => 'Table grills, buffet counters, and chef-finished plates in one seamless dine-in journey.',
                'image' => asset('images/what-we-img-1.jpg'),
            ],
            [
                'title' => 'Catering',
                'description' => 'A scalable event format for intimate house parties or large-format corporate dining.',
                'image' => asset('images/what-we-img-2.jpg'),
            ],
            [
                'title' => 'Delivery Options',
                'description' => 'City-friendly menus designed to travel well without losing heat, texture, or personality.',
                'image' => asset('images/what-we-img-3.jpg'),
            ],
            [
                'title' => 'Special Experiences',
                'description' => 'Festive menus, themed nights, and occasion packaging that feel richer than a standard meal.',
                'image' => asset('images/gallery-6.jpg'),
            ],
        ];

        $deals = [
            ...DealsBundle::active()
                ->orderBy('valid_to')
                ->take(3)
                ->get()
                ->map(fn (DealsBundle $deal) => [
                    'id' => $deal->id,
                    'title' => $deal->name,
                    'description' => $deal->description,
                    'badge' => strtoupper($deal->discount_percent.'% OFF'),
                ])
                ->all(),
        ];

        if (empty($deals)) {
            $deals = [
                [
                    'title' => 'Weekday Lunch Rush',
                    'description' => 'Value buffet pricing for fast lunch plans without cutting the spread short.',
                    'badge' => 'Mon to Thu',
                ],
                [
                    'title' => 'Family Celebration Pack',
                    'description' => 'Dessert add-ons, decorated table styling, and photo-friendly birthday moments.',
                    'badge' => 'Best Seller',
                ],
                [
                    'title' => 'Member-Only Card Benefit',
                    'description' => 'Extra value redemptions and priority access reserved for Happiness Card holders.',
                    'badge' => 'Exclusive',
                ],
            ];
        }

        $happinessCards = EventsHighlight::active()
            ->orderBy('display_order')
            ->take(3)
            ->get();

        $blogs = collect();

        if (Schema::hasTable('blogs')) {
            $blogs = Blog::query()
                ->where('status', 'published')
                ->latest('published_at')
                ->latest()
                ->take(3)
                ->get(['title', 'slug', 'image', 'created_at']);
        }

        if ($blogs->isEmpty()) {
            $blogs = collect([
                ['title' => 'How to plan the ideal weekend grill outing for a large family', 'image' => 'post-1.jpg', 'created_at' => now()],
                ['title' => 'Why prepaid dining cards work better than generic gifting', 'image' => 'post-2.jpg', 'created_at' => now()->subDays(3)],
                ['title' => 'What makes buffet-plus-live-grill dining feel different from a standard dinner', 'image' => 'post-3.jpg', 'created_at' => now()->subWeek()],
            ]);
        }

        $stats = [
            ['value' => '120+', 'label' => 'signature dishes rotated through grill, buffet, and dessert counters'],
            ['value' => '15 min', 'label' => 'average booking flow from browse to confirmed table selection'],
            ['value' => '4 ways', 'label' => 'to experience the brand across dine-in, takeaway, catering, and cards'],
        ];

        $defaultReservationDate = today()->toDateString();
        $reservationBootstrap = [
            'restaurant' => $this->reservationService->getRestaurantDetails(),
            'foodOptions' => $this->reservationService->getFoodOptions(today()),
            'defaultDate' => $defaultReservationDate,
            'quoteUrl' => route('reservations.quote'),
            'storeUrl' => route('reservations.store'),
            'minDate' => $defaultReservationDate,
            'defaultMealType' => 'lunch',
            'defaultGuests' => 2,
            'defaultFoodPreference' => 'veg',
            'defaultDealId' => request('deal'),
            'defaultVoucherCode' => '',
        ];

        return view('front.pages.home', [
            'restaurantName' => $restaurantName,
            'primaryNavigation' => $primaryNavigation,
            'sidebarSections' => $sidebarSections,
            'heroSlides' => $heroSlides,
            'services' => $services,
            'offerings' => $offerings,
            'deals' => $deals,
            'happinessCards' => $happinessCards,
            'blogs' => $blogs,
            'stats' => $stats,
            'profileUrl' => $profileUrl,
            'reservationBootstrap' => $reservationBootstrap,
        ]);
    }
}
