<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\User;
use App\Services\ReservationService;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected SmsService $smsService
    ) {
    }

    public function quote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'meal_type' => ['required', Rule::in(['lunch', 'dinner'])],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
            'food_preference' => ['required', Rule::in(['veg', 'nonveg', 'packages'])],
            'deals_bundle_id' => ['nullable', 'integer'],
            'ignore_booking_id' => ['nullable', 'integer'],
        ]);

        return response()->json($this->reservationService->quote($validated));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'meal_type' => ['required', Rule::in(['lunch', 'dinner'])],
            'slot_id' => ['required', 'exists:time_slots,id'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
            'food_preference' => ['required', Rule::in(['veg', 'nonveg', 'packages'])],
            'deals_bundle_id' => ['nullable', 'integer'],
            'special_request' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = DB::transaction(function () use ($validated) {
            $prepared = $this->reservationService->reserveTable($validated);
            $user = $this->resolveUser($validated);

            return Booking::create([
                'user_id' => $user->id,
                'table_id' => $prepared['table']->id,
                'slot_id' => $prepared['slot']->id,
                'booking_date' => $validated['date'],
                'meal_type' => $validated['meal_type'],
                'veg_count' => $validated['food_preference'] === 'veg' ? $validated['guests'] : 0,
                'nonveg_count' => $validated['food_preference'] === 'nonveg' ? $validated['guests'] : 0,
                'guest_type' => $this->buildGuestTypes($validated),
                'offer_applied' => filled($validated['deals_bundle_id'] ?? null),
                'total_amount' => $prepared['pricing']['total'],
                'status' => 'confirmed',
                'confirmation_code' => Booking::generateConfirmationCode(),
                'booking_meta' => $prepared['meta'],
            ])->load(['user', 'table', 'slot']);
        });

        $this->dispatchNotifications($booking, $validated['email'], $validated['mobile']);

        return redirect()
            ->route('reservations.show', $booking->confirmation_code)
            ->with('success', 'Your reservation has been confirmed.');
    }

    public function show(string $confirmationCode): View
    {
        $booking = $this->findBooking($confirmationCode)->load(['user', 'table', 'slot']);

        return view('front.pages.reservation-confirmation', array_merge(
            $this->layoutData(),
            [
                'booking' => $booking,
                'restaurant' => $this->reservationService->getRestaurantDetails(),
            ]
        ));
    }

    public function cancel(string $confirmationCode): RedirectResponse
    {
        $booking = $this->findBooking($confirmationCode);
        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->route('reservations.show', $booking->confirmation_code)
            ->with('success', 'The reservation has been cancelled.');
    }

    public function reschedule(Request $request, string $confirmationCode): RedirectResponse
    {
        $booking = $this->findBooking($confirmationCode);
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'meal_type' => ['required', Rule::in(['lunch', 'dinner'])],
            'slot_id' => ['required', 'exists:time_slots,id'],
        ]);

        $data = [
            'date' => $validated['date'],
            'meal_type' => $validated['meal_type'],
            'slot_id' => $validated['slot_id'],
            'guests' => (int) ($booking->booking_meta['guests'] ?? $booking->total_guests),
            'food_preference' => $booking->booking_meta['food_preference'] ?? ($booking->nonveg_count > 0 ? 'nonveg' : 'veg'),
            'deals_bundle_id' => $booking->booking_meta['package']['id'] ?? null,
            'name' => $booking->booking_meta['contact']['name'] ?? $booking->user?->name ?? 'Guest',
            'email' => $booking->booking_meta['contact']['email'] ?? $booking->user?->email ?? '',
            'mobile' => $booking->booking_meta['contact']['mobile'] ?? $booking->user?->mobile ?? '',
            'special_request' => $booking->booking_meta['special_request'] ?? null,
            'ignore_booking_id' => $booking->id,
        ];

        $prepared = $this->reservationService->reserveTable($data);

        $booking->update([
            'table_id' => $prepared['table']->id,
            'slot_id' => $prepared['slot']->id,
            'booking_date' => $validated['date'],
            'meal_type' => $validated['meal_type'],
            'total_amount' => $prepared['pricing']['total'],
            'booking_meta' => array_merge($booking->booking_meta ?? [], $prepared['meta']),
            'status' => 'confirmed',
        ]);

        return redirect()
            ->route('reservations.show', $booking->confirmation_code)
            ->with('success', 'The reservation has been rescheduled.');
    }

    protected function resolveUser(array $validated): User
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->forceFill([
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
            ])->save();

            return $user;
        }

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser && ! $existingUser->hasRole('super-admin')) {
            $existingUser->forceFill([
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
            ])->save();

            return $existingUser;
        }

        $email = $existingUser
            ? Str::before($validated['email'], '@').'+booking-'.Str::lower(Str::random(6)).'@'.Str::after($validated['email'], '@')
            : $validated['email'];

        return User::create([
            'name' => $validated['name'],
            'email' => $email,
            'mobile' => $validated['mobile'],
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);
    }

    protected function buildGuestTypes(array $validated): array
    {
        $types = [$validated['food_preference']];

        if (! empty($validated['deals_bundle_id'])) {
            $types[] = 'package';
        }

        return $types;
    }

    protected function dispatchNotifications(Booking $booking, string $email, string $mobile): void
    {
        try {
            Mail::to($email)->send(new BookingConfirmationMail($booking));
            $booking->update(['email_sent' => true]);
        } catch (\Throwable $exception) {
        }

        if (filled($mobile) && $this->smsService->isConfigured()) {
            $result = $this->smsService->sendBookingConfirmation(
                $mobile,
                $booking->user?->name ?? 'Guest',
                $booking->confirmation_code,
                $booking->booking_date->format('d M Y'),
                $booking->slot?->slot_label ?? ''
            );

            if (($result['success'] ?? false) === true) {
                $booking->update(['sms_sent' => true]);
            }
        }
    }

    protected function findBooking(string $confirmationCode): Booking
    {
        return Booking::where('confirmation_code', $confirmationCode)->firstOrFail();
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
                ['label' => "What's On BBQ", 'url' => route('home').'#offerings'],
                ['label' => 'Deals', 'url' => route('home').'#deals'],
                ['label' => 'Reservations', 'url' => route('home').'#booking-cta'],
            ],
            'sidebarSections' => [
                [
                    'title' => 'Reservations',
                    'items' => [
                        ['label' => 'Book A Table', 'url' => route('home').'#booking-cta'],
                        ['label' => 'My Confirmation', 'url' => route('home').'#booking-cta'],
                    ],
                ],
                [
                    'title' => 'Restaurant',
                    'items' => [
                        ['label' => 'Home', 'url' => route('home')],
                        ['label' => 'Deals', 'url' => route('home').'#deals'],
                        ['label' => 'Contact', 'url' => route('home').'#footer'],
                    ],
                ],
            ],
        ];
    }
}
