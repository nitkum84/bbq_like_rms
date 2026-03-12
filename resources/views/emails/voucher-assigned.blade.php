<div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 12px;">Voucher Assigned</h2>
    <p>Hello {{ $user->name }},</p>
    <p>A new voucher has been assigned to your account.</p>
    <table style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Code</strong></td>
            <td style="padding: 6px 0;">{{ $voucher->code }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Discount</strong></td>
            <td style="padding: 6px 0;">
                @if($voucher->discount_type === 'percentage')
                    {{ rtrim(rtrim(number_format($voucher->discount_value, 2), '0'), '.') }}% off
                @else
                    Flat {{ rtrim(rtrim(number_format($voucher->discount_value, 2), '0'), '.') }} off
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 12px 6px 0;"><strong>Expiry</strong></td>
            <td style="padding: 6px 0;">{{ $voucher->expiry_date?->format('d M Y') ?? 'N/A' }}</td>
        </tr>
    </table>
    <p>Regards,<br>{{ \App\Models\WebsiteSetting::get('restaurant_name', config('app.name')) }}</p>
</div>
