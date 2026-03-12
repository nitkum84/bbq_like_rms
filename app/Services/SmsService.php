<?php
namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService {
    public function settings(): array {
        return [
            'enabled' => WebsiteSetting::get('sms_enabled', '0') === '1',
            'user' => WebsiteSetting::get('sms_user', config('sms.user')),
            'password' => WebsiteSetting::get('sms_password', config('sms.password')),
            'sender_id' => WebsiteSetting::get('sms_sender_id', config('sms.sender_id')),
            'base_url' => WebsiteSetting::get('sms_base_url', config('sms.base_url')),
            'delivery_url' => WebsiteSetting::get('sms_delivery_url', config('sms.delivery_url')),
            'route' => WebsiteSetting::get('sms_route', config('sms.route')),
            'pe_id' => WebsiteSetting::get('sms_pe_id', config('sms.pe_id')),
        ];
    }

    public function isConfigured(): bool {
        $settings = $this->settings();

        if (! $settings['enabled']) {
            return false;
        }

        return filled($settings['user'])
            && filled($settings['password'])
            && filled($settings['sender_id'])
            && filled($settings['base_url']);
    }

    public function send(string $mobile, string $message, string $channel = 'Trans'): array {
        $settings = $this->settings();

        if (! $this->isConfigured()) {
            throw new RuntimeException('SMS settings are incomplete or disabled.');
        }

        try {
            $response = Http::timeout(10)->get($settings['base_url'], [
                'user'     => $settings['user'],
                'password' => $settings['password'],
                'senderid' => $settings['sender_id'],
                'channel'  => $channel,
                'DCS'      => 0,
                'flashsms' => 0,
                'number'   => '91'.$mobile,
                'text'     => $message,
                'route'    => $settings['route'],
                'PEId'     => $settings['pe_id'],
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
        $settings = $this->settings();

        if (blank($settings['delivery_url'])) {
            return [];
        }

        $response = Http::get($settings['delivery_url'], [
            'user'     => $settings['user'],
            'password' => $settings['password'],
            'jobid'    => $jobId,
        ]);
        return $response->json() ?? [];
    }
}
