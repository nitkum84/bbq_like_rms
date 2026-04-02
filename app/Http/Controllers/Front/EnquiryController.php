<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {
    }

    public function create(): View
    {
        return view('front.pages.catering-enquiry', array_merge(
            $this->layoutData(),
            ['pageTitle' => 'Catering Enquiry']
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'party_size' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Enquiry::create($validated);

        return redirect()
            ->route('enquiries.create')
            ->with('success', 'Your enquiry has been submitted. Our team will reach out shortly.');
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
                    ],
                ],
            ],
        ];
    }
}
