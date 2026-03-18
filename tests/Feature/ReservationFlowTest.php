<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DealsBundle;
use App\Models\PricingRule;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_endpoint_returns_live_pricing_and_slots(): void
    {
        $this->seedReservationData();

        $response = $this->getJson(route('reservations.quote', [
            'date' => now()->addDay()->toDateString(),
            'meal_type' => 'lunch',
            'guests' => 2,
            'food_preference' => 'veg',
        ]));

        $response->assertOk()
            ->assertJsonPath('pricing.total', 1000)
            ->assertJsonPath('slots.0.available', true);
    }

    public function test_guest_can_create_cancel_and_reschedule_reservation(): void
    {
        $data = $this->seedReservationData();
        $bookingDate = now()->addDay()->toDateString();

        $createResponse = $this->post(route('reservations.store'), [
            'name' => 'Guest Booker',
            'email' => 'guest@example.com',
            'mobile' => '9999999999',
            'date' => $bookingDate,
            'meal_type' => 'lunch',
            'slot_id' => $data['lunchSlot']->id,
            'guests' => 2,
            'food_preference' => 'packages',
            'deals_bundle_id' => $data['bundle']->id,
            'special_request' => 'Window seat',
        ]);

        $booking = Booking::first();

        $createResponse->assertRedirect(route('reservations.show', $booking->confirmation_code));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'meal_type' => 'lunch',
        ]);

        $this->post(route('reservations.cancel', $booking->confirmation_code))
            ->assertRedirect(route('reservations.show', $booking->confirmation_code));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);

        $this->post(route('reservations.reschedule', $booking->confirmation_code), [
            'date' => now()->addDays(2)->toDateString(),
            'meal_type' => 'dinner',
            'slot_id' => $data['dinnerSlot']->id,
        ])->assertRedirect(route('reservations.show', $booking->confirmation_code));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'meal_type' => 'dinner',
            'slot_id' => $data['dinnerSlot']->id,
        ]);
    }

    protected function seedReservationData(): array
    {
        $admin = User::factory()->create();

        PricingRule::create([
            'day_type' => 'weekday',
            'price' => 500,
            'effective_date' => today()->subDay(),
            'created_by' => $admin->id,
        ]);

        PricingRule::create([
            'day_type' => 'weekend',
            'price' => 700,
            'effective_date' => today()->subDay(),
            'created_by' => $admin->id,
        ]);

        $lunchSlot = TimeSlot::create([
            'slot_label' => '12:30 PM',
            'start_time' => '12:30:00',
            'end_time' => '14:00:00',
            'meal_type' => 'lunch',
            'is_active' => true,
            'max_bookings' => 5,
        ]);

        $dinnerSlot = TimeSlot::create([
            'slot_label' => '08:00 PM',
            'start_time' => '20:00:00',
            'end_time' => '22:00:00',
            'meal_type' => 'dinner',
            'is_active' => true,
            'max_bookings' => 5,
        ]);

        Table::create([
            'table_number' => 'T1',
            'seating_capacity' => 4,
            'location' => 'Main Hall',
            'status' => 'active',
        ]);

        $bundle = DealsBundle::create([
            'name' => 'Family Feast',
            'type' => 'mixed',
            'description' => 'Family package with grill and dessert',
            'discount_type' => 'percentage',
            'discount_percent' => 10,
            'valid_from' => today()->subDay(),
            'valid_to' => today()->addWeek(),
            'is_active' => true,
        ]);

        return compact('lunchSlot', 'dinnerSlot', 'bundle');
    }
}

