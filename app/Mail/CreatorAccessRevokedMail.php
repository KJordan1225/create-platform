<?php

namespace App\Mail;

use App\Models\CreatorPlatformSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreatorAccessRevokedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CreatorPlatformSubscription $subscription)
    {
    }

    public function build(): self
    {
        return $this->subject('Your creator access has been revoked')
            ->view('emails.creator-access-revoked');
    }
}
