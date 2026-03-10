<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Table, TimeSlot, User};
use Illuminate\Http\Request;

class BookingController extends Controller {
    public function index(Request $request) {
        $query = Booking::with(['user','table','slot']);
        if ($request->status)   $query->where('status', $request->status);
        if ($request->date)     $query->whereDate('booking_date', $request->date);
        if ($request->search)   $query->whereHas('user', fn($q) => $q->where('name','like','%'.$request->search.'%')
            ->orWhere('mobile','like','%'.$request->search.'%'));
        $bookings = $query->latest()->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }
    public function show(Booking $booking) {
        $booking->load(['user','table','slot','voucher']);
        return view('admin.bookings.show', compact('booking'));
    }
    public function edit(Booking $booking) {
        $booking->load(['user','table','slot']);
        $tables = Table::where('status','active')->get();
        $slots  = TimeSlot::where('is_active',true)->get();
        return view('admin.bookings.edit', compact('booking','tables','slots'));
    }
    public function update(Request $request, Booking $booking) {
        $request->validate(['status'=>'required|in:pending,confirmed,cancelled,completed','admin_notes'=>'nullable|string']);
        $booking->update($request->only(['status','admin_notes','table_id','slot_id','booking_date','veg_count','nonveg_count','total_amount']));
        return redirect()->route('admin.bookings.show',$booking)->with('success','Booking updated.');
    }
    public function updateStatus(Request $request, $id) {
        $booking = Booking::findOrFail($id);
        $request->validate(['status'=>'required|in:pending,confirmed,cancelled,completed']);
        $booking->update(['status' => $request->status]);
        return response()->json(['success'=>true,'status'=>$booking->status]);
    }
    public function destroy(Booking $booking) {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success','Booking deleted.');
    }
}
