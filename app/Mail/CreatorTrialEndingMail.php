<?php

namespace App\Mail;

use App\Models\CreatorPlatformSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreatorTrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CreatorPlatformSubscription $subscription)
    {
    }

    public function build(): self
    {
        return $this->subject('Your creator trial is ending soon')
            ->view('emails.creator-trial-ending');
    }
}
