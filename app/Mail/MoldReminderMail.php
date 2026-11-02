<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class MoldReminderMail extends Mailable
{
    use Queueable;

    public function __construct(public Collection $molds, public string $recipientName)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder LotDate Mold: ' . $this->molds->count() . ' mold perlu diganti',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.mold-reminder');
    }
}