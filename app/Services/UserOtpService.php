<?php

namespace App\Services;

use App\Mail\UserOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserOtpService
{
    public function __construct(protected SmsService $smsService)
    {
    }

    public function issue(User $user): string
    {
        $otp = (string) random_int(100000, 999999);

        $user->forceFill([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $this->send($user, $otp);

        return $otp;
    }

    public function resend(User $user): string
    {
        return $this->issue($user);
    }

    public function verify(User $user, string $otp): bool
    {
        if (
            blank($user->otp)
            || blank($user->otp_expires_at)
            || $user->otp_expires_at->isPast()
            || ! hash_equals((string) $user->otp, trim($otp))
        ) {
            return false;
        }

        $user->forceFill([
            'email_verified_at' => $user->email_verified_at ?? now(),
            'mobile_verified_at' => $user->mobile_verified_at ?? now(),
            'otp' => null,
            'otp_expires_at' => null,
        ])->save();

        return true;
    }

    public function send(User $user, ?string $otp = null): void
    {
        $otp ??= $user->otp;

        if (filled($user->email) && filled($otp)) {
            try {
                Mail::to($user->email)->send(new UserOtpMail($user, $otp));
            } catch (\Throwable $exception) {
            }
        }

        if (filled($user->mobile) && filled($otp) && $this->smsService->isConfigured()) {
            $this->smsService->sendOtp($user->mobile, $otp);
        }
    }
}
