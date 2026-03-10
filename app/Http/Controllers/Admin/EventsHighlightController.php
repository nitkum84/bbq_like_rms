<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventsHighlight;
use Illuminate\Http\Request;

class EventsHighlightController extends Controller {
    public function index() {
        $highlights = EventsHighlight::latest()->paginate(15);
        return view('admin.events-highlights.index', compact('highlights'));
    }
    public function create() { return view('admin.events-highlights.create'); }
    public function store(Request $request) {
        $request->validate([
            'title'        => 'required|string|max:200',
            'display_from' => 'required|date',
            'display_to'   => 'required|date|after_or_equal:display_from',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
        $data = $request->only(['title','description','link','display_from','display_to','display_order','is_active']);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('highlights','public');
        EventsHighlight::create($data);
        return redirect()->route('admin.events-highlights.index')->with('success','Highlight created.');
    }
    public function edit(EventsHighlight $eventsHighlight) { return view('admin.events-highlights.edit', compact('eventsHighlight')); }
    public function update(Request $request, EventsHighlight $eventsHighlight) {
        $data = $request->only(['title','description','link','display_from','display_to','display_order','is_active']);
        if ($request->hasFile('image')) {
            if ($eventsHighlight->image) \Storage::disk('public')->delete($eventsHighlight->image);
            $data['image'] = $request->file('image')->store('highlights','public');
        }
        $eventsHighlight->update($data);
        return redirect()->route('admin.events-highlights.index')->with('success','Highlight updated.');
    }
    public function destroy(EventsHighlight $eventsHighlight) {
        if ($eventsHighlight->image) \Storage::disk('public')->delete($eventsHighlight->image);
        $eventsHighlight->delete();
        return redirect()->route('admin.events-highlights.index')->with('success','Highlight deleted.');
    }
}
