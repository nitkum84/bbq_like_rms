<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Table;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class BookingController extends Controller {
    public function index(Request $request): View {
        $query = Booking::with(['user', 'table', 'slot', 'voucher']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('confirmation_code', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('mobile', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('table', fn ($tableQuery) => $tableQuery
                        ->where('table_number', 'like', '%'.$search.'%'));
            });
        }

        $bookings = $query
            ->latest('booking_date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Booking::count(),
            'confirmed_today' => Booking::whereDate('booking_date', today())->where('status', 'confirmed')->count(),
            'upcoming' => Booking::whereDate('booking_date', '>=', today())->whereIn('status', ['pending', 'confirmed'])->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function create(): View {
        return view('admin.bookings.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateBooking($request);
        $validated['confirmation_code'] = $validated['confirmation_code'] ?: Booking::generateConfirmationCode();
        $validated['guest_type'] = $this->normalizeGuestTypes($request);
        $validated['offer_applied'] = $request->boolean('offer_applied');

        $booking = Booking::create($validated);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking created.');
    }

    public function show(Booking $booking): View {
        $booking->load(['user', 'table', 'slot', 'voucher']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View {
        $booking->load(['user', 'table', 'slot', 'voucher']);
        return view('admin.bookings.edit', array_merge($this->formData(), compact('booking')));
    }

    public function update(Request $request, Booking $booking): RedirectResponse {
        $validated = $this->validateBooking($request, $booking);
        $validated['guest_type'] = $this->normalizeGuestTypes($request);
        $validated['offer_applied'] = $request->boolean('offer_applied');

        $booking->update($validated);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated.');
    }

    public function updateStatus(Request $request, $id) {
        $booking = Booking::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $booking->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $booking->status]);
    }

    public function destroy(Booking $booking): RedirectResponse {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
    }

    protected function formData(): array {
        return [
            'users' => User::orderBy('name')->get(),
            'tables' => Table::where('status', 'active')->orderBy('table_number')->get(),
            'slots' => TimeSlot::where('is_active', true)
                ->orderByRaw("case when meal_type = 'lunch' then 0 else 1 end")
                ->orderBy('start_time')
                ->get(),
            'vouchers' => Voucher::where('is_active', true)->orderBy('code')->get(),
        ];
    }

    protected function validateBooking(Request $request, ?Booking $booking = null): array {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'table_id' => 'required|exists:tables,id',
            'slot_id' => 'required|exists:time_slots,id',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'booking_date' => 'required|date',
            'meal_type' => 'required|in:lunch,dinner',
            'veg_count' => 'required|integer|min:0',
            'nonveg_count' => 'required|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'admin_notes' => 'nullable|string',
            'confirmation_code' => 'nullable|string|max:20|unique:bookings,confirmation_code'.($booking ? ','.$booking->id : ''),
            'guest_type' => 'nullable|array|max:3',
            'guest_type.*' => 'string|max:50',
        ]);

        validator($validated, [])->after(function (Validator $validator) use ($validated, $booking) {
            $slot = TimeSlot::find($validated['slot_id']);
            $table = Table::find($validated['table_id']);

            if (! $slot || ! $table) {
                return;
            }

            if ($slot->meal_type !== $validated['meal_type']) {
                $validator->errors()->add('slot_id', 'Selected slot does not belong to the chosen meal type.');
            }

            $totalGuests = (int) $validated['veg_count'] + (int) $validated['nonveg_count'];
            if ($totalGuests <= 0) {
                $validator->errors()->add('veg_count', 'At least one guest is required.');
            }

            if ($totalGuests > $table->seating_capacity) {
                $validator->errors()->add('table_id', 'Selected table capacity is lower than the total guest count.');
            }

            $activeStatuses = ['pending', 'confirmed'];
            if (in_array($validated['status'], $activeStatuses, true)) {
                $tableConflict = Booking::query()
                    ->when($booking, fn ($q) => $q->whereKeyNot($booking->id))
                    ->where('table_id', $validated['table_id'])
                    ->where('slot_id', $validated['slot_id'])
                    ->whereDate('booking_date', $validated['booking_date'])
                    ->whereIn('status', $activeStatuses)
                    ->exists();

                if ($tableConflict) {
                    $validator->errors()->add('table_id', 'This table is already booked for the selected date and slot.');
                }

                $slotBookingCount = Booking::query()
                    ->when($booking, fn ($q) => $q->whereKeyNot($booking->id))
                    ->where('slot_id', $validated['slot_id'])
                    ->whereDate('booking_date', $validated['booking_date'])
                    ->whereIn('status', $activeStatuses)
                    ->count();

                if ($slotBookingCount >= $slot->max_bookings) {
                    $validator->errors()->add('slot_id', 'This slot has reached its maximum concurrent bookings.');
                }
            }
        })->validate();

        return $validated;
    }

    protected function normalizeGuestTypes(Request $request): array {
        return collect($request->input('guest_type', []))
            ->map(fn ($type) => trim((string) $type))
            ->filter()
            ->unique()
            ->take(3)
            ->values()
            ->all();
    }
}
