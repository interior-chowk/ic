<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    public static function send($type, $to, $mailable)
    {
        try {

            if ($type === 'seller') {
                Mail::mailer('smtp_seller')->to($to)->send($mailable);
            } elseif ($type === 'customer') {
                Mail::mailer('smtp_customer')->to($to)->send($mailable);
            } else {
                Mail::to($to)->send($mailable);
            }

            return true;

        } catch (\Exception $e) {

            Log::error("Primary mail failed ({$type}): ".$e->getMessage());

            // 🔁 FALLBACK SMTP
            try {
                Mail::mailer('smtp_customer')->to($to)->send($mailable);
                return true;
            } catch (\Exception $ex) {
                Log::critical("Fallback mail failed: ".$ex->getMessage());
                return false;
            }
        }
    }
}