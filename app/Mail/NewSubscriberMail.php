<?php

namespace App\Mail;

use App\Models\Plf_subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubscriberMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Plf_subscription $subscription)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have a new subscriber'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-subscriber'
        );
    }
}
