<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {
    protected $fillable = ['code','discount_type','discount_value','assigned_to_user_id','usage_limit','used_count','expiry_date','is_active'];
    protected $casts = ['expiry_date'=>'date','is_active'=>'boolean'];
    public function user()     { return $this->belongsTo(User::class, 'assigned_to_user_id'); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function getIsExpiredAttribute(): bool { return $this->expiry_date->lt(today()); }
    public function getRemainingUsesAttribute(): int { return max(0, $this->usage_limit - $this->used_count); }
}
