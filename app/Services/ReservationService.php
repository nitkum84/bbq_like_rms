<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\DealsBundle;
use App\Models\PricingRule;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\Voucher;
use App\Models\WebsiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    protected array $activeStatuses = ['pending', 'confirmed'];

    public function getRestaurantDetails(): array
    {
        return [
            'name' => WebsiteSetting::get('restaurant_name', config('app.name', 'Restaurant Booking')),
            'address' => WebsiteSetting::get('address', 'Main dining room'),
            'email' => WebsiteSetting::get('contact_email', config('mail.from.address')),
            'mobile' => WebsiteSetting::get('contact_mobile', ''),
            'booking_note' => WebsiteSetting::get('booking_note', 'We look forward to hosting you.'),
            'gst_rate' => (float) WebsiteSetting::get('gst_rate', 5),
        ];
    }

    public function getFoodOptions(?Carbon $date = null): array
    {
        $date ??= today();
        $basePrice = $this->basePriceForDate($date);

        return [
            'base_price' => round($basePrice, 2),
            'options' => [
                [
                    'value' => 'veg',
                    'label' => 'Veg',
                    'description' => 'Classic vegetarian buffet selection.',
                    'price_per_guest' => round($basePrice, 2),
                    'modifier_label' => 'Base price',
                ],
                [
                    'value' => 'nonveg',
                    'label' => 'Non-Veg',
                    'description' => 'Grill and buffet with non-veg additions.',
                    'price_per_guest' => round($basePrice * 1.2, 2),
                    'modifier_label' => '+20% premium',
                ],
                [
                    'value' => 'packages',
                    'label' => 'Packages',
                    'description' => 'Bundle pricing powered by active dining packages.',
                    'price_per_guest' => round($basePrice, 2),
                    'modifier_label' => 'Bundle discount varies',
                ],
            ],
            'packages' => $this->activePackages($date)->all(),
        ];
    }

    public function quote(array $data): array
    {
        $date = Carbon::parse($data['date'])->startOfDay();
        $mealType = $data['meal_type'];
        $guests = (int) $data['guests'];
        $foodPreference = $data['food_preference'];
        $bundleId = $data['deals_bundle_id'] ?? null;
        $ignoreBookingId = $data['ignore_booking_id'] ?? null;

        $slots = $this->availableSlots($date, $mealType, $guests, $ignoreBookingId);
        $pricing = $this->pricingBreakdown($date, $guests, $foodPreference, $bundleId, $data['voucher_code'] ?? null);

        return [
            'restaurant' => $this->getRestaurantDetails(),
            'date' => $date->toDateString(),
            'formatted_date' => $date->format('D, d M Y'),
            'meal_type' => $mealType,
            'guests' => $guests,
            'food_preference' => $foodPreference,
            'deals_bundle_id' => $bundleId ? (int) $bundleId : null,
            'slots' => $slots->values()->all(),
            'pricing' => $pricing,
            'has_availability' => $slots->contains(fn (array $slot) => $slot['available']),
        ];
    }

    public function reserveTable(array $data): array
    {
        $date = Carbon::parse($data['date'])->startOfDay();
        $slot = TimeSlot::active()->findOrFail($data['slot_id']);
        $guests = (int) $data['guests'];
        $ignoreBookingId = $data['ignore_booking_id'] ?? null;

        if ($slot->meal_type !== $data['meal_type']) {
            throw ValidationException::withMessages([
                'slot_id' => 'Selected slot does not belong to the chosen meal type.',
            ]);
        }

        $table = $this->findAvailableTable($date, $slot, $guests, $ignoreBookingId);

        if (! $table) {
            throw ValidationException::withMessages([
                'slot_id' => 'No table is currently available for that slot and guest count.',
            ]);
        }

        $pricing = $this->pricingBreakdown(
            $date,
            $guests,
            $data['food_preference'],
            $data['deals_bundle_id'] ?? null,
            $data['voucher_code'] ?? null
        );

        return [
            'table' => $table,
            'slot' => $slot,
            'pricing' => $pricing,
            'meta' => [
                'guests' => $guests,
                'food_preference' => $data['food_preference'],
                'package' => $pricing['package'],
                'contact' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'],
                ],
                'special_request' => $data['special_request'] ?? null,
                'price_per_guest' => $pricing['price_per_guest'],
                'pricing_label' => $pricing['pricing_label'],
                'discount_label' => $pricing['discount_label'],
                'voucher' => $pricing['voucher'],
                'coupon_code' => $pricing['voucher']['code'] ?? null,
                'gst_rate' => $pricing['gst_rate'],
                'gst_amount' => $pricing['gst_amount'],
                'subtotal' => $pricing['subtotal'],
                'discount_total' => $pricing['discount_total'],
                'pre_tax_total' => $pricing['pre_tax_total'],
            ],
        ];
    }

    public function availableSlots(Carbon $date, string $mealType, int $guests, ?int $ignoreBookingId = null): Collection
    {
        return TimeSlot::active()
            ->where('meal_type', $mealType)
            ->orderBy('start_time')
            ->get()
            ->map(function (TimeSlot $slot) use ($date, $guests, $ignoreBookingId) {
                $bookingCount = Booking::query()
                    ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
                    ->where('slot_id', $slot->id)
                    ->whereDate('booking_date', $date->toDateString())
                    ->whereIn('status', $this->activeStatuses)
                    ->count();

                $remaining = max($slot->max_bookings - $bookingCount, 0);
                $table = $this->findAvailableTable($date, $slot, $guests, $ignoreBookingId);

                return [
                    'id' => $slot->id,
                    'label' => $slot->slot_label,
                    'start_time' => Carbon::parse($slot->start_time)->format('h:i A'),
                    'end_time' => Carbon::parse($slot->end_time)->format('h:i A'),
                    'meal_type' => $slot->meal_type,
                    'remaining' => $remaining,
                    'available' => $remaining > 0 && $table !== null,
                    'status_label' => $remaining > 0 && $table !== null ? 'Available' : 'Fully booked',
                ];
            });
    }

    public function findAvailableTable(Carbon $date, TimeSlot $slot, int $guests, ?int $ignoreBookingId = null): ?Table
    {
        if ($guests <= 0) {
            return null;
        }

        $slotBookingCount = Booking::query()
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->where('slot_id', $slot->id)
            ->whereDate('booking_date', $date->toDateString())
            ->whereIn('status', $this->activeStatuses)
            ->count();

        if ($slotBookingCount >= $slot->max_bookings) {
            return null;
        }

        return Table::active()
            ->where('seating_capacity', '>=', $guests)
            ->whereDoesntHave('bookings', function ($query) use ($date, $slot, $ignoreBookingId) {
                $query->when($ignoreBookingId, fn ($bookingQuery) => $bookingQuery->whereKeyNot($ignoreBookingId))
                    ->where('slot_id', $slot->id)
                    ->whereDate('booking_date', $date->toDateString())
                    ->whereIn('status', $this->activeStatuses);
            })
            ->orderBy('seating_capacity')
            ->orderBy('table_number')
            ->first();
    }

    public function pricingBreakdown(
        Carbon $date,
        int $guests,
        string $foodPreference,
        ?int $bundleId = null,
        ?string $voucherCode = null
    ): array
    {
        $basePrice = $this->basePriceForDate($date);
        $pricePerGuest = match ($foodPreference) {
            'veg' => $basePrice,
            'nonveg' => $basePrice * 1.2,
            'packages' => $basePrice,
            default => throw ValidationException::withMessages([
                'food_preference' => 'Unsupported food preference selected.',
            ]),
        };

        $subtotal = $guests * $pricePerGuest;
        $packageSummary = null;
        $packageDiscountAmount = 0;
        $voucherDiscountAmount = 0;
        $voucherSummary = null;

        if ($foodPreference === 'packages') {
            $package = DealsBundle::active()->find($bundleId);

            if (! $package) {
                throw ValidationException::withMessages([
                    'deals_bundle_id' => 'Please select an active package for package bookings.',
                ]);
            }

            $discountPercent = (float) ($package->discount_percent ?? 0);
            $packageDiscountAmount = round($subtotal * ($discountPercent / 100), 2);
            $packageSummary = [
                'id' => $package->id,
                'name' => $package->name,
                'discount_percent' => $discountPercent,
                'description' => $package->description,
            ];
        }

        $subtotalAfterPackage = max($subtotal - $packageDiscountAmount, 0);
        $voucher = $this->resolveVoucher($voucherCode);

        if ($voucher) {
            $voucherDiscountAmount = $voucher->discount_type === 'flat'
                ? min((float) $voucher->discount_value, $subtotalAfterPackage)
                : round($subtotalAfterPackage * (((float) $voucher->discount_value) / 100), 2);

            $voucherSummary = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'discount_amount' => round($voucherDiscountAmount, 2),
            ];
        }

        $discountTotal = round($packageDiscountAmount + $voucherDiscountAmount, 2);
        $preTaxTotal = max($subtotal - $discountTotal, 0);
        $gstRate = (float) WebsiteSetting::get('gst_rate', 5);
        $gstAmount = round($preTaxTotal * ($gstRate / 100), 2);
        $total = round($preTaxTotal + $gstAmount, 2);
        $dayType = $date->isWeekend() ? 'weekend' : 'weekday';

        return [
            'day_type' => $dayType,
            'base_price' => round($basePrice, 2),
            'price_per_guest' => round($pricePerGuest, 2),
            'subtotal' => round($subtotal, 2),
            'package_discount_amount' => round($packageDiscountAmount, 2),
            'voucher_discount_amount' => round($voucherDiscountAmount, 2),
            'discount_amount' => $discountTotal,
            'discount_total' => $discountTotal,
            'pre_tax_total' => round($preTaxTotal, 2),
            'gst_rate' => $gstRate,
            'gst_amount' => $gstAmount,
            'total' => $total,
            'pricing_label' => ucfirst($dayType).' '.ucfirst(str_replace('nonveg', 'non-veg', $foodPreference)).' pricing',
            'discount_label' => $discountTotal > 0 ? 'Discounts applied before GST' : 'No discount applied',
            'package' => $packageSummary,
            'voucher' => $voucherSummary,
        ];
    }

    protected function resolveVoucher(?string $voucherCode): ?Voucher
    {
        if (blank($voucherCode)) {
            return null;
        }

        return Voucher::query()
            ->whereRaw('LOWER(code) = ?', [strtolower(trim($voucherCode))])
            ->where('is_active', true)
            ->whereDate('expiry_date', '>=', today())
            ->first();
    }

    protected function basePriceForDate(Carbon $date): float
    {
        return PricingRule::getCurrentPrice($date->isWeekend() ? 'weekend' : 'weekday');
    }

    protected function activePackages(Carbon $date): Collection
    {
        return DealsBundle::query()
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', $date->toDateString())
            ->whereDate('valid_to', '>=', $date->toDateString())
            ->orderBy('name')
            ->get()
            ->map(fn (DealsBundle $bundle) => [
                'id' => $bundle->id,
                'name' => $bundle->name,
                'description' => $bundle->description,
                'discount_percent' => (float) ($bundle->discount_percent ?? 0),
                'discount_type' => $bundle->discount_type,
            ]);
    }
}
