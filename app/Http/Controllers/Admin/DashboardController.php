<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Table, User, Enquiry, MenuCategory, MenuItems};

class DashboardController extends Controller {
    public function index() {
        $totalTables   = Table::where('status','active')->count();
        $bookedToday   = Booking::whereDate('booking_date', today())->where('status','confirmed')->count();
        $availToday    = max(0, $totalTables - $bookedToday);
        $totalEnquiries= Enquiry::count();
        $newEnquiries  = Enquiry::where('status','new')->count();
        $totalUsers    = User::where('status',1)->count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status','pending')->count();

        $upcomingBookings = Booking::with(['user','table','slot'])
            ->where('booking_date','>=',today())
            ->where('status','confirmed')
            ->orderBy('booking_date')
            ->orderBy('slot_id')
            ->limit(10)
            ->get();

        $recentBookings = Booking::with(['user','table','slot'])
            ->latest()
            ->limit(5)
            ->get();

        // Chart data - bookings last 7 days
        $chartData = collect(range(6, 0))->map(function($days) {
            $date = today()->subDays($days);
            return [
                'date'  => $date->format('d M'),
                'count' => Booking::whereDate('booking_date', $date)->where('status','confirmed')->count(),
            ];
        });

        return view('admin.dashboard', compact(
            'totalTables','bookedToday','availToday',
            'totalEnquiries','newEnquiries','totalUsers',
            'totalBookings','pendingBookings',
            'upcomingBookings','recentBookings','chartData'
        ));
    }
}
