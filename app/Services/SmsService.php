<?php
namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService {
    public function send(string $mobile, string $message, string $channel = 'Trans'): array {
        try {
            $response = Http::timeout(10)->get(config('sms.base_url'), [
                'user'     => config('sms.user'),
                'password' => config('sms.password'),
                'senderid' => config('sms.sender_id'),
                'channel'  => $channel,
                'DCS'      => 0,
                'flashsms' => 0,
                'number'   => '91'.$mobile,
                'text'     => $message,
                'route'    => config('sms.route'),
                'PEId'     => config('sms.pe_id'),
            ]);

            $result = $response->body();
            Log::info("SMS Sent to {$mobile}: {$result}");
            return ['success' => true, 'response' => $result];
        } catch (\Exception $e) {
            Log::error("SMS Failed to {$mobile}: ".$e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendBookingConfirmation(string $mobile, string $name, string $ref, string $date, string $time): array {
        $message = "Dear {$name}, Your table booking #{$ref} for {$date} at {$time} is confirmed. Team ".config('app.name').".";
        return $this->send($mobile, $message, 'Trans');
    }

    public function sendOtp(string $mobile, string $otp): array {
        $message = "Your OTP for ".config('app.name')." is {$otp}. Valid for 10 minutes. Do not share.";
        return $this->send($mobile, $message, 'Trans');
    }

    public function checkDelivery(string $jobId): array {
        $response = Http::get(config('sms.delivery_url'), [
            'user'     => config('sms.user'),
            'password' => config('sms.password'),
            'jobid'    => $jobId,
        ]);
        return $response->json() ?? [];
    }
}
