@extends('front.layouts.app')

@section('title', 'Reservation Confirmation | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))
@section('meta_description', 'Reservation confirmation and booking summary.')

@section('content')
    <section class="reservation-confirmation-page">
        <div class="container reservation-confirmation-page__grid">
            <div class="reservation-confirmation-card reservation-confirmation-card--primary">
                <p class="section-kicker">Booking Confirmed</p>
                <h1>{{ $booking->confirmation_code }}</h1>
                <p>Your table has been reserved. Use the summary below to review, cancel, or reschedule your reservation.</p>

                @if (session('success'))
                    <div class="reservation-alert reservation-alert--visible">{{ session('success') }}</div>
                @endif

                <dl class="reservation-summary reservation-summary--confirmation">
                    <div>
                        <dt>Restaurant</dt>
                        <dd>{{ $restaurant['name'] }}</dd>
                    </div>
                    <div>
                        <dt>Date</dt>
                        <dd>{{ $booking->booking_date->format('D, d M Y') }}</dd>
                    </div>
                    <div>
                        <dt>Time</dt>
                        <dd>{{ $booking->slot?->slot_label }}</dd>
                    </div>
                    <div>
                        <dt>Guests</dt>
                        <dd>{{ $booking->booking_meta['guests'] ?? $booking->total_guests }}</dd>
                    </div>
                    <div>
                        <dt>Food</dt>
                        <dd>{{ ucfirst(str_replace('nonveg', 'non-veg', $booking->booking_meta['food_preference'] ?? 'veg')) }}</dd>
                    </div>
                    <div>
                        <dt>Total</dt>
                        <dd>Rs. {{ number_format((float) $booking->total_amount, 2) }}</dd>
                    </div>
                </dl>

                <div class="reservation-dashboard-entry">
                    <div>
                        <h2>Go to your dashboard</h2>
                        <p>We created your user dashboard from this booking. First-time users must confirm the same OTP sent to both email and SMS.</p>
                    </div>

                    @if (auth()->check() && auth()->id() === $booking->user_id && session('front_otp_passed', false))
                        <a class="button button--solid reservation-confirmation-actions__button" href="{{ route('dashboard') }}">Open Dashboard</a>
                    @else
                        <form method="POST" action="{{ route('reservations.dashboard-otp.verify', $booking->confirmation_code) }}" class="reservation-dashboard-entry__form">
                            @csrf
                            <label>
                                <span>Enter OTP</span>
                                <input type="text" name="otp" inputmode="numeric" maxlength="6" placeholder="6-digit OTP" required>
                            </label>
                            @error('reservation_otp')
                                <p class="user-dashboard-error">{{ $message }}</p>
                            @enderror
                            <div class="reservation-dashboard-entry__actions">
                                <button class="button button--solid" type="submit">Verify & Open Dashboard</button>
                                <button class="button button--ghost-dark" formaction="{{ route('reservations.dashboard-otp.resend', $booking->confirmation_code) }}" formnovalidate type="submit">Resend OTP</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="reservation-confirmation-card">
                <h2>Order summary</h2>
                <ul class="reservation-detail-list">
                    <li><span>Table</span><strong>{{ $booking->table?->table_number }}{{ $booking->table?->location ? ' - ' . $booking->table->location : '' }}</strong></li>
                    <li><span>Meal</span><strong>{{ ucfirst($booking->meal_type) }}</strong></li>
                    <li><span>Contact</span><strong>{{ $booking->booking_meta['contact']['name'] ?? $booking->user?->name }}</strong></li>
                    <li><span>Email</span><strong>{{ $booking->booking_meta['contact']['email'] ?? $booking->user?->email }}</strong></li>
                    <li><span>Mobile</span><strong>{{ $booking->booking_meta['contact']['mobile'] ?? $booking->user?->mobile }}</strong></li>
                    @if (! empty($booking->booking_meta['package']['name'] ?? null))
                        <li><span>Package</span><strong>{{ $booking->booking_meta['package']['name'] }}</strong></li>
                    @endif
                    @if (! empty($booking->booking_meta['special_request'] ?? null))
                        <li><span>Request</span><strong>{{ $booking->booking_meta['special_request'] }}</strong></li>
                    @endif
                    <li><span>Status</span><strong>{{ ucfirst($booking->status) }}</strong></li>
                </ul>

                <div class="reservation-confirmation-actions">
                    <form method="POST" action="{{ route('reservations.cancel', $booking->confirmation_code) }}">
                        @csrf
                        <button class="button button--ghost-dark reservation-confirmation-actions__button" type="submit">Cancel</button>
                    </form>

                    <form method="POST" action="{{ route('reservations.reschedule', $booking->confirmation_code) }}" class="reservation-reschedule-form" data-reschedule-form data-quote-url="{{ route('reservations.quote') }}" data-guests="{{ $booking->booking_meta['guests'] ?? $booking->total_guests }}" data-food-preference="{{ $booking->booking_meta['food_preference'] ?? ($booking->nonveg_count > 0 ? 'nonveg' : 'veg') }}" data-package-id="{{ $booking->booking_meta['package']['id'] ?? '' }}" data-ignore-booking-id="{{ $booking->id }}">
                        @csrf
                        <label>
                            <span>Reschedule date</span>
                            <input type="date" name="date" min="{{ today()->toDateString() }}" value="{{ $booking->booking_date->toDateString() }}" required>
                        </label>
                        <label>
                            <span>Meal</span>
                            <select name="meal_type" required>
                                <option value="lunch" @selected($booking->meal_type === 'lunch')>Lunch</option>
                                <option value="dinner" @selected($booking->meal_type === 'dinner')>Dinner</option>
                            </select>
                        </label>
                        <label>
                            <span>Slot</span>
                            <select name="slot_id" required data-reschedule-slot>
                                <option value="{{ $booking->slot_id }}">{{ $booking->slot?->slot_label }}</option>
                            </select>
                        </label>
                        <button class="button button--solid reservation-confirmation-actions__button" type="submit">Reschedule</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

