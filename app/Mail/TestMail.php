<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build(): static
    {
        return $this->subject('Laravel Mail Test')
            ->html('
                <h1>Laravel mail is working</h1>
                <p>This is a test email from your application.</p>
            ');
    }
}