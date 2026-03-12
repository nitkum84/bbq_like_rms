<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\DynamicMailService;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller {
    public function index(): View {
        $activeUsers = User::where('status', 1);

        $stats = [
            'total_users' => (clone $activeUsers)->count(),
            'upcoming_users' => User::whereHas('bookings', fn ($q) => $q
                ->where('booking_date', '>=', today())
                ->where('status', 'confirmed')
            )->count(),
            'sms_logs' => NotificationLog::where('type', 'sms')->count(),
            'email_logs' => NotificationLog::where('type', 'email')->count(),
        ];

        return view('admin.notifications.index', compact('stats'));
    }

    public function logs(Request $request): View {
        $query = NotificationLog::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        return view('admin.notifications.logs', compact('logs'));
    }

    public function send(Request $request, DynamicMailService $mailService, ActivityLogService $activityLogService): RedirectResponse {
        $request->validate([
            'channel' => 'required|in:sms,email,both',
            'target' => 'required|in:all,upcoming',
            'subject' => 'required_if:channel,email,both|nullable|string|max:200',
            'message' => 'required|string',
        ]);

        $users = $request->target === 'upcoming'
            ? User::whereHas('bookings', fn ($q) => $q
                ->where('booking_date', '>=', today())
                ->where('status', 'confirmed')
            )->where('status', 1)->get()
            : User::where('status', 1)->get();

        $smsService = app(SmsService::class);
        $sentRecipients = 0;

        foreach ($users as $user) {
            $message = str_replace('{name}', $user->name, (string) $request->message);
            $recipientSucceeded = false;

            if (in_array($request->channel, ['sms', 'both'], true) && $user->mobile) {
                try {
                    $result = $smsService->send($user->mobile, $message, 'Promo');
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'sms',
                        'template' => 'admin_broadcast',
                        'payload' => ['message' => $message],
                        'status' => isset($result['status']) ? 'sent' : 'failed',
                        'sent_at' => now(),
                    ]);
                    $recipientSucceeded = true;
                } catch (\Throwable $e) {
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'sms',
                        'template' => 'admin_broadcast',
                        'payload' => ['message' => $message],
                        'status' => 'failed',
                        'sent_at' => now(),
                    ]);
                }
            }

            if (in_array($request->channel, ['email', 'both'], true) && $user->email) {
                try {
                    $mailService->sendRaw($user->email, (string) $request->subject, $message);
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'email',
                        'template' => 'admin_broadcast',
                        'payload' => ['subject' => $request->subject, 'message' => $message],
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                    $recipientSucceeded = true;
                } catch (\Throwable $e) {
                    NotificationLog::create([
                        'user_id' => $user->id,
                        'type' => 'email',
                        'template' => 'admin_broadcast',
                        'payload' => ['subject' => $request->subject, 'message' => $message],
                        'status' => 'failed',
                        'sent_at' => now(),
                    ]);
                }
            }

            if ($recipientSucceeded) {
                $sentRecipients++;
            }
        }

        $activityLogService->record(
            'broadcast_sent',
            null,
            'Broadcast notification processed',
            [
                'channel' => $request->channel,
                'target' => $request->target,
                'recipient_count' => $users->count(),
                'successful_recipient_count' => $sentRecipients,
            ]
        );

        return back()->with('success', "Notification processed for {$sentRecipients} users.");
    }
}
