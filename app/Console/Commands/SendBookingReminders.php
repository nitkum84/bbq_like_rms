<?php
namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\SmsService;
use App\Mail\BookingReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command {
    protected $signature   = 'bookings:send-reminders';
    protected $description = 'Send 24h ahead booking reminders';

    public function handle(): void {
        $tomorrow  = now()->addDay()->toDateString();
        $bookings  = Booking::with(['user','table','slot'])
            ->whereDate('booking_date', $tomorrow)
            ->where('status','confirmed')
            ->where('sms_sent',false)
            ->get();

        $sms = app(SmsService::class);
        foreach ($bookings as $b) {
            $msg = "Reminder: Your booking #{$b->confirmation_code} at ".config('app.name')." is tomorrow {$b->booking_date->format('d M')} at {$b->slot->slot_label}.";
            $sms->send($b->user->mobile, $msg, 'Trans');
            $b->update(['sms_sent' => true]);
        }
        $this->info("Reminders sent for {$bookings->count()} bookings.");
    }
}
