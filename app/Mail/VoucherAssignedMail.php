<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Voucher $voucher, public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Voucher Assigned: '.$this->voucher->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-assigned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
