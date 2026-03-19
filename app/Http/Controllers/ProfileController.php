<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ReservationService;
use App\Services\UserOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected UserOtpService $userOtpService
    ) {
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        $user = $request->user();
        $bookings = $user->bookings()
            ->with(['table', 'slot'])
            ->latest('booking_date')
            ->latest('id')
            ->get();

        return view('front.pages.dashboard', array_merge(
            $this->frontLayoutData(),
            [
                'user' => $user,
                'bookings' => $bookings,
                'otpPassed' => (bool) $request->session()->get('front_otp_passed', false),
                'upcomingBookings' => $bookings->whereIn('status', ['pending', 'confirmed'])->filter(
                    fn (Booking $booking) => $booking->booking_date && $booking->booking_date->greaterThanOrEqualTo(today())
                )->values(),
                'pastBookings' => $bookings->filter(
                    fn (Booking $booking) => $booking->booking_date && $booking->booking_date->lt(today())
                )->values(),
                'activeVouchers' => $user->vouchers()->latest()->take(5)->get(),
                'dashboardStats' => [
                    'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                    'pending_bookings' => $bookings->where('status', 'pending')->count(),
                    'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                    'lifetime_spend' => $bookings->where('status', '!=', 'cancelled')->sum('total_amount'),
                ],
            ]
        ));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function verifyDashboardOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if (! $this->userOtpService->verify($request->user(), $validated['otp'])) {
            return back()->withErrors([
                'dashboard_otp' => 'The OTP is invalid or has expired. Please try again.',
            ]);
        }

        $request->session()->put('front_otp_passed', true);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Your contact details have been verified.');
    }

    public function resendDashboardOtp(Request $request): RedirectResponse
    {
        $this->userOtpService->resend($request->user());

        return back()->with('status', 'A fresh OTP has been sent to your email and mobile.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function frontLayoutData(): array
    {
        $restaurantName = $this->reservationService->getRestaurantDetails()['name'];

        return [
            'restaurantName' => $restaurantName,
            'profileUrl' => route('dashboard'),
            'primaryNavigation' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Reservations', 'url' => route('dashboard').'#reservations'],
                ['label' => 'Rewards', 'url' => route('dashboard').'#rewards'],
                ['label' => 'Book Table', 'url' => route('home').'#booking-cta'],
            ],
            'sidebarSections' => [
                [
                    'title' => 'Account',
                    'items' => [
                        ['label' => 'My Profile', 'url' => route('dashboard').'#profile'],
                        ['label' => 'Reservations', 'url' => route('dashboard').'#reservations'],
                        ['label' => 'Rewards', 'url' => route('dashboard').'#rewards'],
                        ['label' => 'Book A Table', 'url' => route('home').'#booking-cta'],
                    ],
                ],
                [
                    'title' => 'Restaurant',
                    'items' => [
                        ['label' => 'Home', 'url' => route('home')],
                        ['label' => "What's On BBQ", 'url' => route('home').'#offerings'],
                        ['label' => 'Deals', 'url' => route('home').'#deals'],
                        ['label' => 'Contact', 'url' => route('home').'#footer'],
                    ],
                ],
            ],
        ];
    }
}
