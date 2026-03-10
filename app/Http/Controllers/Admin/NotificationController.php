<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, NotificationLog, Booking};
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller {
    public function index() {
        $totalUsers = User::where('status',1)->count();
        return view('admin.notifications.index', compact('totalUsers'));
    }
    public function logs() {
        $logs = NotificationLog::with('user')->latest()->paginate(30);
        return view('admin.notifications.logs', compact('logs'));
    }
    public function send(Request $request) {
        $request->validate([
            'channel'  => 'required|in:sms,email,both',
            'target'   => 'required|in:all,upcoming',
            'subject'  => 'required_if:channel,email,both',
            'message'  => 'required|string',
        ]);

        $users = $request->target === 'upcoming'
            ? User::whereHas('bookings', fn($q) => $q->where('booking_date','>=',today())->where('status','confirmed'))->get()
            : User::where('status',1)->get();

        $smsService = app(SmsService::class);
        $sent = 0;

        foreach ($users as $user) {
            if (in_array($request->channel, ['sms','both']) && $user->mobile) {
                $result = $smsService->send($user->mobile, $request->message, 'Promo');
                NotificationLog::create([
                    'user_id'  => $user->id,
                    'type'     => 'sms',
                    'template' => 'admin_broadcast',
                    'payload'  => ['message' => $request->message],
                    'status'   => isset($result['status']) ? 'sent' : 'failed',
                    'sent_at'  => now(),
                ]);
            }
            if (in_array($request->channel, ['email','both']) && $user->email) {
                try {
                    Mail::raw($request->message, fn($m) => $m->to($user->email)->subject($request->subject));
                    NotificationLog::create([
                        'user_id'  => $user->id,
                        'type'     => 'email',
                        'template' => 'admin_broadcast',
                        'payload'  => ['subject'=>$request->subject,'message'=>$request->message],
                        'status'   => 'sent',
                        'sent_at'  => now(),
                    ]);
                } catch (\Exception $e) {}
            }
            $sent++;
        }

        return back()->with('success',"Notification sent to {$sent} users.");
    }
}
