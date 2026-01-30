<?php

namespace App\Services;

use App\CPU\SMS_module;
use App\Model\PhoneOrEmailVerification;
use Illuminate\Support\Facades\Mail;

class OTPService
{
    /**
     * Generate OTP
     *
     * @return int
     */
    public function generateOTP()
    {
        return rand(100000, 999999);
    }

    /**
     * Send OTP via Email
     *
     * @param string $email
     * @param int $otp
     * @return bool
     */
    public function sendEmailOTP($email, $otp)
    {
        try {
            Mail::send('emails.otp', ['otp' => $otp], function($message) use ($email) {
                $message->to($email)
                        ->subject('Your OTP Verification Code');
            });
            
            // Store OTP in database
            $verification = new PhoneOrEmailVerification();
            $verification->email = $email;
            $verification->token = $otp;
            $verification->expires_at = now()->addMinutes(10);
            $verification->save();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send OTP via SMS
     *
     * @param string $phone
     * @param int $otp
     * @return mixed
     */
    public function sendSMSOTP($phone, $otp)
    {
        try {
            $response = SMS_module::send($phone, $otp);
            
            if ($response !== 'not_found') {
                // Store OTP in database
                $verification = new PhoneOrEmailVerification();
                $verification->phone = $phone;
                $verification->token = $otp;
                $verification->expires_at = now()->addMinutes(10);
                $verification->save();
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verify OTP
     *
     * @param string $identifier Email or Phone
     * @param int $otp
     * @return bool
     */
    public function verifyOTP($identifier, $otp)
    {
        $verification = PhoneOrEmailVerification::where(function($query) use ($identifier) {
                $query->where('email', $identifier)
                      ->orWhere('phone', $identifier);
            })
            ->where('token', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            $verification->delete();
            return true;
        }

        return false;
    }
}