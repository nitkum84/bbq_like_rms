<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $restaurantName = WebsiteSetting::get('restaurant_name', config('app.name', 'Restaurant Booking'));
        $profileUrl = Route::has('login') ? route('login') : '#';

        if (Auth::check()) {
            $profileUrl = Auth::user()->hasRole('super-admin')
                ? route('admin.dashboard')
                : route('dashboard');
        }

        $primaryNavigation = [
            ['label' => 'Happiness Cards', 'url' => '#happiness-cards'],
            ['label' => "What's On BBQ", 'url' => '#offerings'],
            ['label' => 'Deals', 'url' => '#deals'],
            ['label' => 'Restaurants', 'url' => '#booking-cta'],
        ];

        $sidebarSections = [
            [
                'title' => 'Main',
                'items' => [
                    ['label' => $restaurantName, 'url' => route('home')],
                    ['label' => 'Home', 'url' => route('home')],
                    ['label' => "What's On BBQN", 'url' => '#offerings'],
                    ['label' => 'Deals', 'url' => '#deals'],
                    ['label' => 'Delivery / Takeaway', 'url' => '#services'],
                    ['label' => 'Restaurants', 'url' => '#booking-cta'],
                    ['label' => 'Happiness Cards', 'url' => '#happiness-cards'],
                    ['label' => 'Catering', 'url' => '#services'],
                ],
            ],
            [
                'title' => 'Profile',
                'items' => [
                    ['label' => 'Profile', 'url' => $profileUrl],
                    ['label' => 'My Reservations', 'url' => '#booking-cta'],
                    ['label' => 'My Profile', 'url' => $profileUrl],
                    ['label' => 'My Smiles', 'url' => '#happiness-cards'],
                    ['label' => 'My Happiness Card', 'url' => '#happiness-cards'],
                    ['label' => 'Delivery History', 'url' => '#services'],
                ],
            ],
            [
                'title' => 'About',
                'items' => [
                    ['label' => 'About Us', 'url' => '#footer'],
                    ['label' => 'Blogs', 'url' => '#updates'],
                    ['label' => 'Smiles', 'url' => '#happiness-cards'],
                    ['label' => 'News', 'url' => '#updates'],
                    ['label' => 'Nutrition Information', 'url' => '#offerings'],
                ],
            ],
            [
                'title' => 'Others',
                'items' => [
                    ['label' => 'Contact Us', 'url' => '#footer'],
                    ['label' => 'FAQ', 'url' => '#footer'],
                    ['label' => 'Corporate Enquiry', 'url' => '#services'],
                    ['label' => 'Investor Relations', 'url' => '#footer'],
                    ['label' => 'Barbeque Nation Partnership', 'url' => '#services'],
                ],
            ],
        ];

        $heroSlides = [
            [
                'eyebrow' => 'Live Grill. Loud Flavour.',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'An indulgent grill-first dining experience with buffet theatre, festive add-ons, and occasion-ready tables.',
                'image' => asset('images/hero-bg.jpg'),
                'primary_cta' => ['label' => 'Book A Table', 'url' => '#booking-cta'],
                'secondary_cta' => ['label' => 'Explore Deals', 'url' => '#deals'],
            ],
            [
                'eyebrow' => 'Celebrate Bigger',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'Curated spreads, live counters, and shareable upgrades that turn casual plans into full events.',
                'image' => asset('images/events-image.jpg'),
                'primary_cta' => ['label' => 'Plan A Celebration', 'url' => '#services'],
                'secondary_cta' => ['label' => "What's On BBQ", 'url' => '#offerings'],
            ],
            [
                'eyebrow' => 'Rewards That Return',
                'title' => 'Best Restaurant for Celebrations in Town',
                'description' => 'Keep the relationship warm between visits with prepaid value, exclusive access, and smarter gifting.',
                'image' => asset('images/testimonials-image.jpg'),
                'primary_cta' => ['label' => 'View Happiness Cards', 'url' => '#happiness-cards'],
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

        $blogs = collect();

        if (Schema::hasTable('blogs')) {
            $blogs = Blog::query()
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

        return view('front.pages.home', [
            'restaurantName' => $restaurantName,
            'primaryNavigation' => $primaryNavigation,
            'sidebarSections' => $sidebarSections,
            'heroSlides' => $heroSlides,
            'services' => $services,
            'offerings' => $offerings,
            'deals' => $deals,
            'blogs' => $blogs,
            'stats' => $stats,
            'profileUrl' => $profileUrl,
        ]);
    }
}
