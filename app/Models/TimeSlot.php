<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model {
    protected $fillable = ['slot_label','start_time','end_time','meal_type','is_active','max_bookings'];
    protected $casts = ['is_active' => 'boolean'];
    public function bookings() { return $this->hasMany(Booking::class, 'slot_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
