<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $fillable = [
        'user_id','table_id','slot_id','booking_date','meal_type',
        'veg_count','nonveg_count','guest_type','booking_meta','offer_applied','voucher_id',
        'total_amount','status','confirmation_code','sms_sent','email_sent','admin_notes'
    ];
    protected $casts = [
        'booking_date' => 'date',
        'guest_type'   => 'array',
        'booking_meta' => 'array',
        'offer_applied'=> 'boolean',
        'sms_sent'     => 'boolean',
        'email_sent'   => 'boolean',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function table()   { return $this->belongsTo(Table::class); }
    public function slot()    { return $this->belongsTo(TimeSlot::class, 'slot_id'); }
    public function voucher() { return $this->belongsTo(Voucher::class); }

    public function getTotalGuestsAttribute() { return $this->veg_count + $this->nonveg_count; }

    public static function generateConfirmationCode(): string {
        do {
            $code = 'RB-'.strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('confirmation_code', $code)->exists());
        return $code;
    }
}
