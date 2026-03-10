<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Table, Booking};
use Illuminate\Http\Request;

class TableController extends Controller {
    public function index() {
        $tables = Table::withCount(['bookings as today_bookings_count' => fn($q) =>
            $q->whereDate('booking_date', today())->where('status','confirmed')
        ])->paginate(15);
        return view('admin.tables.index', compact('tables'));
    }
    public function create() { return view('admin.tables.create'); }
    public function store(Request $request) {
        $request->validate([
            'table_number'    => 'required|string|max:20|unique:tables',
            'seating_capacity'=> 'required|integer|min:1|max:20',
            'location'        => 'nullable|string|max:100',
            'status'          => 'required|in:active,inactive,blocked',
        ]);
        Table::create($request->only(['table_number','seating_capacity','location','status']));
        return redirect()->route('admin.tables.index')->with('success','Table added successfully.');
    }
    public function show(Table $table) {
        $bookings = Booking::with(['user','slot'])
            ->where('table_id',$table->id)
            ->whereDate('booking_date','>=',today())
            ->where('status','confirmed')
            ->orderBy('booking_date')
            ->get();
        return view('admin.tables.show', compact('table','bookings'));
    }
    public function edit(Table $table) { return view('admin.tables.edit', compact('table')); }
    public function update(Request $request, Table $table) {
        $request->validate([
            'table_number'    => 'required|string|max:20|unique:tables,table_number,'.$table->id,
            'seating_capacity'=> 'required|integer|min:1|max:20',
        ]);
        $table->update($request->only(['table_number','seating_capacity','location','status']));
        return redirect()->route('admin.tables.index')->with('success','Table updated.');
    }
    public function destroy(Table $table) {
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success','Table deleted.');
    }
    public function toggle($id) {
        $table = Table::findOrFail($id);
        $table->status = $table->status === 'active' ? 'inactive' : 'active';
        $table->save();
        return response()->json(['success'=>true]);
    }
}
