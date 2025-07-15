<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetClientPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $newPassword;

    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->subject('رمز عبور جدید شما - ')
            ->view('emails.reset-client-password')
            ->with([
                'newPassword' => $this->newPassword,
            ]);
    }
}
