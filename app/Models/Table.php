<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model {
    protected $fillable = ['table_number','seating_capacity','location','status'];
    public function bookings() { return $this->hasMany(Booking::class); }
    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function getTodayBookingsCountAttribute() {
        return $this->bookings()->whereDate('booking_date', today())->where('status', 'confirmed')->count();
    }
}
