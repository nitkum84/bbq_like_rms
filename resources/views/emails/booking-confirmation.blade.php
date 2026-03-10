<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; }
        .header { background: #c0392b; color: white; padding: 30px; text-align: center; }
        .body { padding: 30px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Booking Confirmed!</h2>
        <p>Ref: #{{ $booking->confirmation_code }}</p>
    </div>
    <div class="body">
        <p>Dear {{ $booking->user->name }},</p>
        <p>Your table has been successfully booked. Here are your booking details:</p>
        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;background:#fafafa;border-radius:8px">
            <tr><td style="border-bottom:1px solid #eee"><strong>Date:</strong></td><td>{{ $booking->booking_date->format('D, d M Y') }}</td></tr>
            <tr><td style="border-bottom:1px solid #eee"><strong>Time Slot:</strong></td><td>{{ $booking->slot->slot_label }}</td></tr>
            <tr><td style="border-bottom:1px solid #eee"><strong>Table:</strong></td><td>{{ $booking->table->table_number }} ({{ $booking->table->location ?? 'Main Floor' }})</td></tr>
            <tr><td style="border-bottom:1px solid #eee"><strong>Guests:</strong></td><td>Veg: {{ $booking->veg_count }}, Non-Veg: {{ $booking->nonveg_count }}</td></tr>
            <tr><td><strong>Total Amount:</strong></td><td>₹{{ number_format($booking->total_amount,2) }}</td></tr>
        </table>
        <p style="margin-top:20px;background:#eafaf1;border-left:4px solid #27ae60;padding:12px;border-radius:4px">
            {{ \App\Models\WebsiteSetting::get('booking_note','We look forward to hosting you!') }}
        </p>
    </div>
    <div class="footer">
        <p>{{ \App\Models\WebsiteSetting::get('restaurant_name') }} | {{ \App\Models\WebsiteSetting::get('contact_mobile') }}</p>
        <p>{{ \App\Models\WebsiteSetting::get('address') }}</p>
    </div>
</div>
</body>
</html>
