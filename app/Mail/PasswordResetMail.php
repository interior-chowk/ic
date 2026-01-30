<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reset_url;

    public function __construct($reset_url)
    {
        $this->reset_url = $reset_url;
    }

   public function build()
    {
        return $this->mailer('customer')
            ->from(
                config('mail.mailers.customer.username'),
                'InteriorChowk Support'
            )
            ->subject(__('Password Reset'))
            ->view('email-templates.admin-password-reset', [
                'url' => $this->reset_url
            ]);
    }



}