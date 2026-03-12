<?php
namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class DynamicMailService {
    public function settings(): array {
        return [
            'enabled' => WebsiteSetting::get('email_enabled', '0') === '1',
            'mailer' => WebsiteSetting::get('mail_mailer', env('MAIL_MAILER', 'smtp')),
            'host' => WebsiteSetting::get('mail_host', env('MAIL_HOST')),
            'port' => (int) WebsiteSetting::get('mail_port', env('MAIL_PORT', 587)),
            'username' => WebsiteSetting::get('mail_username', env('MAIL_USERNAME')),
            'password' => WebsiteSetting::get('mail_password', env('MAIL_PASSWORD')),
            'encryption' => WebsiteSetting::get('mail_encryption', env('MAIL_SCHEME', env('MAIL_ENCRYPTION'))),
            'from_address' => WebsiteSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS')),
            'from_name' => WebsiteSetting::get('mail_from_name', env('MAIL_FROM_NAME', config('app.name'))),
        ];
    }

    public function isConfigured(): bool {
        $settings = $this->settings();

        if (! $settings['enabled']) {
            return false;
        }

        return filled($settings['mailer'])
            && filled($settings['host'])
            && filled($settings['port'])
            && filled($settings['username'])
            && filled($settings['password'])
            && filled($settings['from_address']);
    }

    public function sendRaw(string $to, string $subject, string $message): void {
        $this->guardConfigured();
        $mailer = $this->mailerName();

        Mail::mailer($mailer)->raw($message, function ($mail) use ($to, $subject) {
            $mail->to($to)->subject($subject);
        });
    }

    public function sendMailable(string $to, Mailable $mailable): void {
        $this->guardConfigured();
        Mail::mailer($this->mailerName())->to($to)->send($mailable);
    }

    public function applyConfig(): void {
        $settings = $this->settings();

        config([
            'mail.default' => $settings['mailer'],
            "mail.mailers.{$settings['mailer']}.transport" => $settings['mailer'],
            "mail.mailers.{$settings['mailer']}.host" => $settings['host'],
            "mail.mailers.{$settings['mailer']}.port" => $settings['port'],
            "mail.mailers.{$settings['mailer']}.username" => $settings['username'],
            "mail.mailers.{$settings['mailer']}.password" => $settings['password'],
            "mail.mailers.{$settings['mailer']}.scheme" => blank($settings['encryption']) ? null : $settings['encryption'],
            'mail.from.address' => $settings['from_address'],
            'mail.from.name' => $settings['from_name'],
        ]);

        app('mail.manager')->purge($settings['mailer']);
        app('mail.manager')->forgetMailers();
    }

    protected function guardConfigured(): void {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Email settings are incomplete or disabled.');
        }

        $this->applyConfig();
    }

    protected function mailerName(): string {
        return (string) $this->settings()['mailer'];
    }
}
