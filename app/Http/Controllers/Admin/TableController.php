<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $query = Table::query()
            ->withCount([
                'bookings',
                'bookings as today_bookings_count' => fn ($q) => $q
                    ->whereDate('booking_date', today())
                    ->where('status', 'confirmed'),
                'bookings as upcoming_bookings_count' => fn ($q) => $q
                    ->whereDate('booking_date', '>=', today())
                    ->where('status', 'confirmed'),
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('capacity')) {
            $query->where('seating_capacity', '>=', (int) $request->capacity);
        }

        $tables = $query
            ->orderBy('table_number')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Table::count(),
            'active' => Table::where('status', 'active')->count(),
            'blocked' => Table::where('status', 'blocked')->count(),
            'booked_today' => Table::whereHas('bookings', fn ($q) => $q
                ->whereDate('booking_date', today())
                ->where('status', 'confirmed')
            )->count(),
        ];

        $locations = Table::query()
            ->whereNotNull('location')
            ->orderBy('location')
            ->pluck('location')
            ->filter()
            ->unique()
            ->values();

        return view('admin.tables.index', compact('tables', 'stats', 'locations'));
    }

    public function create(): View
    {
        return view('admin.tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables',
            'seating_capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,blocked',
        ]);

        Table::create($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table added successfully.');
    }

    public function show(Table $table): View
    {
        $table->loadCount([
            'bookings',
            'bookings as confirmed_bookings_count' => fn ($q) => $q->where('status', 'confirmed'),
            'bookings as pending_bookings_count' => fn ($q) => $q->where('status', 'pending'),
            'bookings as cancelled_bookings_count' => fn ($q) => $q->where('status', 'cancelled'),
            'bookings as today_bookings_count' => fn ($q) => $q
                ->whereDate('booking_date', today())
                ->where('status', 'confirmed'),
        ]);

        $upcomingBookings = Booking::with(['user', 'slot'])
            ->where('table_id', $table->id)
            ->whereDate('booking_date', '>=', today())
            ->orderBy('booking_date')
            ->orderBy('slot_id')
            ->paginate(10);

        $recentBookings = Booking::with(['user', 'slot'])
            ->where('table_id', $table->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.tables.show', compact('table', 'upcomingBookings', 'recentBookings'));
    }

    public function edit(Table $table): View
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables,table_number,'.$table->id,
            'seating_capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,blocked',
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table updated.');
    }

    public function destroy(Table $table): RedirectResponse
    {
        if ($table->bookings()->exists()) {
            return redirect()
                ->route('admin.tables.index')
                ->with('error', 'This table has booking history and cannot be deleted.');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')->with('success', 'Table deleted.');
    }

    public function toggle($id): RedirectResponse
    {
        $table = Table::findOrFail($id);
        $table->status = $table->status === 'active' ? 'inactive' : 'active';
        $table->save();

        return redirect()
            ->route('admin.tables.index')
            ->with('success', "Table {$table->table_number} is now {$table->status}.");
    }
}
