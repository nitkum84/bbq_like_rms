<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name','email','mobile','password','profile_image',
        'email_verified_at','mobile_verified_at','status','otp','otp_expires_at'
    ];

    protected $hidden = ['password','remember_token','otp'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function bookings() { return $this->hasMany(Booking::class); }
    public function vouchers() { return $this->hasMany(Voucher::class, 'assigned_to_user_id'); }
    public function blogs() { return $this->hasMany(Blog::class, 'author_id'); }

    public function getProfileImageUrlAttribute() {
        return $this->profile_image
            ? asset('storage/'.$this->profile_image)
            : asset('admin/images/default-avatar.png');
    }
}
