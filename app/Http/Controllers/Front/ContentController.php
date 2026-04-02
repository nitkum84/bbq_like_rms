<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\DealsBundle;
use App\Models\EventsHighlight;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {
    }

    public function blogs(): View
    {
        return view('front.pages.blogs-index', array_merge(
            $this->layoutData(),
            [
                'pageTitle' => 'Blogs',
                'pageDescription' => 'Latest stories, updates, and dining guides.',
                'blogs' => Blog::query()
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->latest()
                    ->paginate(9),
            ]
        ));
    }

    public function blogShow(Blog $blog): View
    {
        abort_unless($blog->status === 'published', 404);

        return view('front.pages.blog-show', array_merge(
            $this->layoutData(),
            [
                'blog' => $blog->load('author'),
                'relatedBlogs' => Blog::query()
                    ->where('status', 'published')
                    ->whereKeyNot($blog->id)
                    ->latest('published_at')
                    ->take(3)
                    ->get(),
            ]
        ));
    }

    public function happinessCards(): View
    {
        return view('front.pages.happiness-cards-index', array_merge(
            $this->layoutData(),
            [
                'pageTitle' => 'Happiness Cards',
                'pageDescription' => 'Card benefits, gifting, and member-ready dining perks.',
                'cards' => EventsHighlight::active()
                    ->orderBy('display_order')
                    ->paginate(9),
            ]
        ));
    }

    public function happinessCardShow(EventsHighlight $eventsHighlight): View
    {
        abort_unless($eventsHighlight->is_active, 404);

        return view('front.pages.happiness-card-show', array_merge(
            $this->layoutData(),
            [
                'card' => $eventsHighlight,
                'relatedCards' => EventsHighlight::active()
                    ->whereKeyNot($eventsHighlight->id)
                    ->orderBy('display_order')
                    ->take(3)
                    ->get(),
            ]
        ));
    }

    public function deals(): View
    {
        return view('front.pages.deals-index', array_merge(
            $this->layoutData(),
            [
                'pageTitle' => 'Deals',
                'pageDescription' => 'Active dining bundles and offer-led table bookings.',
                'deals' => DealsBundle::active()
                    ->orderBy('valid_to')
                    ->paginate(9),
            ]
        ));
    }

    public function dealShow(DealsBundle $dealsBundle): View
    {
        abort_unless($dealsBundle->is_currently_valid, 404);

        return view('front.pages.deal-show', array_merge(
            $this->layoutData(),
            [
                'deal' => $dealsBundle->load('menuItems'),
                'relatedDeals' => DealsBundle::active()
                    ->whereKeyNot($dealsBundle->id)
                    ->orderBy('valid_to')
                    ->take(3)
                    ->get(),
            ]
        ));
    }

    protected function layoutData(): array
    {
        $restaurantName = $this->reservationService->getRestaurantDetails()['name'];
        $profileUrl = route('login');

        if (Auth::check()) {
            $profileUrl = Auth::user()->hasRole('super-admin')
                ? route('admin.dashboard')
                : route('dashboard');
        }

        return [
            'restaurantName' => $restaurantName,
            'profileUrl' => $profileUrl,
            'primaryNavigation' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Blogs', 'url' => route('blogs.index')],
                ['label' => 'Happiness Cards', 'url' => route('happiness-cards.index')],
                ['label' => 'Deals', 'url' => route('deals.index')],
                ['label' => 'Catering', 'url' => route('enquiries.create')],
            ],
            'sidebarSections' => [
                [
                    'title' => 'Explore',
                    'items' => [
                        ['label' => 'Home', 'url' => route('home')],
                        ['label' => 'Blogs', 'url' => route('blogs.index')],
                        ['label' => 'Happiness Cards', 'url' => route('happiness-cards.index')],
                        ['label' => 'Deals', 'url' => route('deals.index')],
                        ['label' => 'Catering', 'url' => route('enquiries.create')],
                    ],
                ],
                [
                    'title' => 'Account',
                    'items' => [
                        ['label' => 'Profile', 'url' => $profileUrl],
                        ['label' => 'My Bookings', 'url' => Auth::check() && ! Auth::user()->hasRole('super-admin') ? route('dashboard').'#bookings' : $profileUrl],
                        ['label' => 'My Rewards', 'url' => Auth::check() && ! Auth::user()->hasRole('super-admin') ? route('dashboard').'#rewards' : $profileUrl],
                    ],
                ],
            ],
        ];
    }
}
