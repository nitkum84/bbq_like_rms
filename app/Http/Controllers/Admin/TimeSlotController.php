<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TimeSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class TimeSlotController extends Controller {
    public function index(Request $request): View {
        $query = TimeSlot::query()
            ->withCount([
                'bookings',
                'bookings as upcoming_bookings_count' => fn ($q) => $q
                    ->whereDate('booking_date', '>=', today())
                    ->whereIn('status', ['confirmed', 'pending']),
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('slot_label', 'like', '%'.$search.'%');
        }

        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $slots = $query
            ->orderByRaw("case when meal_type = 'lunch' then 0 else 1 end")
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => TimeSlot::count(),
            'active' => TimeSlot::where('is_active', true)->count(),
            'lunch' => TimeSlot::where('meal_type', 'lunch')->count(),
            'dinner' => TimeSlot::where('meal_type', 'dinner')->count(),
        ];

        return view('admin.time-slots.index', compact('slots', 'stats'));
    }

    public function create(): View {
        return view('admin.time-slots.create');
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateSlot($request);
        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot created.');
    }

    public function show(TimeSlot $timeSlot): RedirectResponse {
        return redirect()->route('admin.time-slots.edit', $timeSlot);
    }

    public function edit(TimeSlot $timeSlot): View {
        $bookingSummary = [
            'total' => $timeSlot->bookings()->count(),
            'upcoming' => $timeSlot->bookings()->whereDate('booking_date', '>=', today())->count(),
            'confirmed' => $timeSlot->bookings()->where('status', 'confirmed')->count(),
        ];

        $recentBookings = Booking::with(['user', 'table'])
            ->where('slot_id', $timeSlot->id)
            ->latest('booking_date')
            ->limit(5)
            ->get();

        return view('admin.time-slots.edit', compact('timeSlot', 'bookingSummary', 'recentBookings'));
    }

    public function update(Request $request, TimeSlot $timeSlot): RedirectResponse {
        $validated = $this->validateSlot($request, $timeSlot);
        $timeSlot->update($validated);

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot updated.');
    }

    public function destroy(TimeSlot $timeSlot): RedirectResponse {
        if ($timeSlot->bookings()->exists()) {
            return redirect()
                ->route('admin.time-slots.index')
                ->with('error', 'This time slot has booking history and cannot be deleted.');
        }

        $timeSlot->delete();

        return redirect()->route('admin.time-slots.index')->with('success', 'Time slot deleted.');
    }

    protected function validateSlot(Request $request, ?TimeSlot $timeSlot = null): array {
        $validated = $request->validate([
            'slot_label' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'meal_type' => 'required|in:lunch,dinner',
            'max_bookings' => 'required|integer|min:1|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        validator($validated, [])->after(function (Validator $validator) use ($validated, $timeSlot) {
            $overlapExists = TimeSlot::query()
                ->when($timeSlot, fn ($q) => $q->whereKeyNot($timeSlot->id))
                ->where('meal_type', $validated['meal_type'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($overlapExists) {
                $validator->errors()->add('start_time', 'This slot overlaps with an existing '.$validated['meal_type'].' slot.');
            }
        })->validate();

        return $validated;
    }
}
