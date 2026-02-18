<?php

namespace App\Mail\WatchTrend;

use App\Models\WatchtrendWatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WatchtrendDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly WatchtrendWatch $watch,
        public readonly array $analyses
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "WatchTrend - Votre digest {$this->watch->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'watchtrend.emails.digest',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
