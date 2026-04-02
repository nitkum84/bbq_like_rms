@extends('front.layouts.app')

@section('title', ($pageTitle ?? 'Catering Enquiry') . ' | ' . ($restaurantName ?? config('app.name', 'Restaurant Booking')))

@section('content')
    <section class="content-detail-page">
        <div class="container content-detail-page__grid">
            <article class="content-detail-card">
                <p class="section-kicker">Catering</p>
                <h1>Catering Enquiry</h1>
                <p class="content-detail-meta">Tell us about your event and our team will help with planning, menu, and guest counts.</p>

                @if (session('success'))
                    <div class="reservation-alert reservation-alert--visible">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('enquiries.store') }}" class="reservation-form__grid">
                    @csrf
                    <label class="reservation-field">
                        <span>Name</span>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
                    </label>
                    <label class="reservation-field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                    </label>
                    <label class="reservation-field">
                        <span>Mobile</span>
                        <input type="text" name="mobile" value="{{ old('mobile', auth()->user()?->mobile) }}" required>
                    </label>
                    <label class="reservation-field">
                        <span>Expected Guests</span>
                        <input type="number" name="party_size" min="1" value="{{ old('party_size') }}">
                    </label>
                    <label class="reservation-field">
                        <span>Enquiry Details</span>
                        <textarea name="message" rows="5" required>{{ old('message') }}</textarea>
                    </label>
                    <button class="button button--solid" type="submit">Submit Enquiry</button>
                </form>
            </article>

            <aside class="content-detail-sidebar">
                <div class="content-detail-card">
                    <h2>Ideal for</h2>
                    <div class="content-richtext">
                        <p>Corporate lunches, birthday events, festive gatherings, weddings, and private celebrations.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
