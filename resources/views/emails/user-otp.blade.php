<p>Hello {{ $user->name }},</p>
<p>Your one-time password for {{ config('app.name') }} dashboard access is <strong>{{ $otp }}</strong>.</p>
<p>This OTP is valid for 10 minutes. You can use the same code from either the SMS or the email.</p>
<p>If you did not request this, you can ignore this message.</p>
