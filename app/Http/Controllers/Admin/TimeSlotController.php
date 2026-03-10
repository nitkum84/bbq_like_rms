<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller {
    public function index() {
        $slots = TimeSlot::withCount('bookings')->orderBy('meal_type')->orderBy('start_time')->paginate(20);
        return view('admin.time-slots.index', compact('slots'));
    }
    public function create() { return view('admin.time-slots.create'); }
    public function store(Request $request) {
        $request->validate([
            'slot_label'   => 'required|string|max:50',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            'meal_type'    => 'required|in:lunch,dinner',
            'max_bookings' => 'required|integer|min:1',
        ]);
        TimeSlot::create($request->only(['slot_label','start_time','end_time','meal_type','is_active','max_bookings']));
        return redirect()->route('admin.time-slots.index')->with('success','Time slot created.');
    }
    public function edit(TimeSlot $timeSlot) { return view('admin.time-slots.edit', compact('timeSlot')); }
    public function update(Request $request, TimeSlot $timeSlot) {
        $request->validate(['slot_label'=>'required','meal_type'=>'required|in:lunch,dinner']);
        $timeSlot->update($request->only(['slot_label','start_time','end_time','meal_type','is_active','max_bookings']));
        return redirect()->route('admin.time-slots.index')->with('success','Slot updated.');
    }
    public function destroy(TimeSlot $timeSlot) {
        $timeSlot->delete();
        return redirect()->route('admin.time-slots.index')->with('success','Slot deleted.');
    }
}
