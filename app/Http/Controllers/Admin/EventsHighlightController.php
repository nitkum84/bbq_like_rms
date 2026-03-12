<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventsHighlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventsHighlightController extends Controller {
    public function index(Request $request): View {
        $query = EventsHighlight::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.trim((string) $request->search).'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $highlights = $query
            ->orderBy('display_order')
            ->latest('display_to')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => EventsHighlight::count(),
            'active' => EventsHighlight::where('is_active', true)->count(),
            'current' => EventsHighlight::active()->count(),
            'expired' => EventsHighlight::whereDate('display_to', '<', today())->count(),
        ];

        return view('admin.events-highlights.index', compact('highlights', 'stats'));
    }

    public function create(): View {
        return view('admin.events-highlights.create');
    }

    public function store(Request $request): RedirectResponse {
        $validated = $this->validateHighlight($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('highlights', 'public');
        }

        $highlight = EventsHighlight::create($validated);

        return redirect()->route('admin.events-highlights.show', $highlight)->with('success', 'Highlight created.');
    }

    public function show(EventsHighlight $eventsHighlight): View {
        return view('admin.events-highlights.show', compact('eventsHighlight'));
    }

    public function edit(EventsHighlight $eventsHighlight): View {
        return view('admin.events-highlights.edit', compact('eventsHighlight'));
    }

    public function update(Request $request, EventsHighlight $eventsHighlight): RedirectResponse {
        $validated = $this->validateHighlight($request);

        if ($request->hasFile('image')) {
            if ($eventsHighlight->image) {
                \Storage::disk('public')->delete($eventsHighlight->image);
            }
            $validated['image'] = $request->file('image')->store('highlights', 'public');
        }

        $eventsHighlight->update($validated);

        return redirect()->route('admin.events-highlights.show', $eventsHighlight)->with('success', 'Highlight updated.');
    }

    public function destroy(EventsHighlight $eventsHighlight): RedirectResponse {
        if ($eventsHighlight->image) {
            \Storage::disk('public')->delete($eventsHighlight->image);
        }
        $eventsHighlight->delete();

        return redirect()->route('admin.events-highlights.index')->with('success', 'Highlight deleted.');
    }

    protected function validateHighlight(Request $request): array {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:255',
            'display_from' => 'required|date',
            'display_to' => 'required|date|after_or_equal:display_from',
            'display_order' => 'nullable|integer|min:0|max:999',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
