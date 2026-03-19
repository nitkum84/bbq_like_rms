@extends('front.layouts.app')

@section('title', 'My Dashboard | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))
@section('meta_description', 'Manage your profile, reservations, and rewards in one place.')

@section('content')
    <section class="user-dashboard-page">
        <div class="container user-dashboard-page__stack">
            <div class="user-dashboard-hero">
                <div>
                    <p class="section-kicker">Front User Dashboard</p>
                    <h1>Welcome back, {{ $user->name }}</h1>
                    <p>Track your upcoming tables, verify your contact details, and keep your dining profile ready for the next booking.</p>
                </div>
                <div class="user-dashboard-stats">
                    <article>
                        <span>Confirmed</span>
                        <strong>{{ $dashboardStats['confirmed_bookings'] }}</strong>
                    </article>
                    <article>
                        <span>Pending</span>
                        <strong>{{ $dashboardStats['pending_bookings'] }}</strong>
                    </article>
                    <article>
                        <span>Spend</span>
                        <strong>Rs. {{ number_format((float) $dashboardStats['lifetime_spend'], 2) }}</strong>
                    </article>
                </div>
            </div>

            @if (session('status'))
                <div class="reservation-alert reservation-alert--visible">{{ session('status') }}</div>
            @endif

            @if (! $user->hasVerifiedContact())
                <div class="user-dashboard-panel user-dashboard-panel--warning">
                    <div>
                        <h2>Confirm your OTP before using the dashboard</h2>
                        <p>We sent the same 6-digit OTP to your email and mobile number. You can enter either one here.</p>
                    </div>

                    <form method="POST" action="{{ route('dashboard.otp.verify') }}" class="user-dashboard-otp-form">
                        @csrf
                        <label>
                            <span>Enter OTP</span>
                            <input type="text" name="otp" inputmode="numeric" maxlength="6" placeholder="6-digit OTP" required>
                        </label>
                        @error('dashboard_otp')
                            <p class="user-dashboard-error">{{ $message }}</p>
                        @enderror
                        <div class="user-dashboard-otp-form__actions">
                            <button class="button button--solid" type="submit">Verify & Continue</button>
                            <button class="button button--ghost-dark" formaction="{{ route('dashboard.otp.resend') }}" formnovalidate type="submit">Resend OTP</button>
                        </div>
                    </form>
                </div>

                <div class="user-dashboard-panel">
                    <p class="section-kicker">Locked</p>
                    <h2>Reservations, profile details, and rewards unlock after OTP verification</h2>
                    <p>Once the OTP is confirmed, this dashboard will show your booking history, verified profile details, and available rewards in one place.</p>
                </div>
            @else
                <div class="user-dashboard-grid">
                    <aside class="user-dashboard-sidebar" data-dashboard-tabs role="tablist" aria-label="Dashboard sections">
                        <button type="button" id="dashboard-tab-profile" data-dashboard-tab="profile" role="tab" aria-controls="profile" aria-selected="true">My Profile</button>
                        <button type="button" id="dashboard-tab-reservations" data-dashboard-tab="reservations" role="tab" aria-controls="reservations" aria-selected="false">Reservations</button>
                        <button type="button" id="dashboard-tab-rewards" data-dashboard-tab="rewards" role="tab" aria-controls="rewards" aria-selected="false">Rewards</button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </aside>

                    <div class="user-dashboard-content" data-dashboard-panels>
                        <section class="user-dashboard-panel" id="profile" data-dashboard-panel="profile" role="tabpanel" aria-labelledby="dashboard-tab-profile">
                            <div class="user-dashboard-panel__header">
                                <div>
                                    <p class="section-kicker">Profile</p>
                                    <h2>Contact and account snapshot</h2>
                                </div>
                                <span class="user-dashboard-badge is-verified">Verified</span>
                            </div>

                            <div class="user-dashboard-profile-card">
                                <div class="user-dashboard-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <h3>{{ $user->name }}</h3>
                                    <p>{{ $user->email }}</p>
                                    <p>{{ $user->mobile ?: 'Mobile not provided' }}</p>
                                </div>
                            </div>

                            <dl class="user-dashboard-info-grid">
                                <div>
                                    <dt>Email</dt>
                                    <dd>{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt>Mobile Number</dt>
                                    <dd>{{ $user->mobile ?: 'Not provided' }}</dd>
                                </div>
                                <div>
                                    <dt>Email Status</dt>
                                    <dd>Verified</dd>
                                </div>
                                <div>
                                    <dt>Mobile Status</dt>
                                    <dd>Verified</dd>
                                </div>
                                <div>
                                    <dt>Member Since</dt>
                                    <dd>{{ $user->created_at?->format('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt>Total Bookings</dt>
                                    <dd>{{ $bookings->count() }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="user-dashboard-panel" id="reservations" data-dashboard-panel="reservations" role="tabpanel" aria-labelledby="dashboard-tab-reservations" hidden>
                            <div class="user-dashboard-panel__header">
                                <div>
                                    <p class="section-kicker">Reservations</p>
                                    <h2>Your booking history</h2>
                                </div>
                                <a class="button button--ghost-dark" href="{{ route('home') }}#booking-cta">Book another table</a>
                            </div>

                            <div class="user-dashboard-bookings">
                                @forelse ($bookings as $booking)
                                    <article class="user-dashboard-booking-card">
                                        <div class="user-dashboard-booking-card__top">
                                            <div>
                                                <strong>{{ $booking->confirmation_code }}</strong>
                                                <p>{{ $booking->booking_date?->format('D, d M Y') }} &middot; {{ $booking->slot?->slot_label }}</p>
                                            </div>
                                            <span class="user-dashboard-status is-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                                        </div>
                                        <dl>
                                            <div>
                                                <dt>Guests</dt>
                                                <dd>{{ $booking->booking_meta['guests'] ?? $booking->total_guests }}</dd>
                                            </div>
                                            <div>
                                                <dt>Meal</dt>
                                                <dd>{{ ucfirst($booking->meal_type) }}</dd>
                                            </div>
                                            <div>
                                                <dt>Table</dt>
                                                <dd>{{ $booking->table?->table_number ?? 'Assigned on arrival' }}</dd>
                                            </div>
                                            <div>
                                                <dt>Total</dt>
                                                <dd>Rs. {{ number_format((float) $booking->total_amount, 2) }}</dd>
                                            </div>
                                        </dl>
                                        <a class="button button--solid" href="{{ route('reservations.show', $booking->confirmation_code) }}">Open booking</a>
                                    </article>
                                @empty
                                    <p class="user-dashboard-empty">No reservations yet. Your next booking will appear here.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="user-dashboard-panel" id="rewards" data-dashboard-panel="rewards" role="tabpanel" aria-labelledby="dashboard-tab-rewards" hidden>
                            <div class="user-dashboard-panel__header">
                                <div>
                                    <p class="section-kicker">Rewards</p>
                                    <h2>Cards, vouchers, and activity</h2>
                                </div>
                            </div>

                            <div class="user-dashboard-rewards">
                                <article>
                                    <span>Active vouchers</span>
                                    <strong>{{ $activeVouchers->count() }}</strong>
                                    <p>{{ $activeVouchers->count() > 0 ? 'Ready to be redeemed on your next visit.' : 'No vouchers assigned yet.' }}</p>
                                </article>
                                <article>
                                    <span>Upcoming visits</span>
                                    <strong>{{ $upcomingBookings->count() }}</strong>
                                    <p>Confirmed future reservations waiting on your calendar.</p>
                                </article>
                                <article>
                                    <span>Past visits</span>
                                    <strong>{{ $pastBookings->count() }}</strong>
                                    <p>Completed dining history available for quick reference.</p>
                                </article>
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection


