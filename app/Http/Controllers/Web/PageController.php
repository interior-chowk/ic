<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\HelpTopic;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function helpTopic()
    {
        $helps = HelpTopic::Status()->latest()->get();
        return view(VIEW_FILE_NAMES['faq'], compact('helps'));
    }

    public function contacts()
    {
        
        $recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha');
        return view(VIEW_FILE_NAMES['contacts'],compact('recaptcha'));
    }

    public function about_us()
    {
        $about_us = BusinessSetting::where('type', 'about_us')->first();
        return view(VIEW_FILE_NAMES['about_us'], [
            'about_us' => $about_us,
        ]);
    }

    public function termsand_condition()
    {
        $terms_condition = BusinessSetting::where('type', 'terms_condition')->first();
        return view('terms-and-condition', compact('terms_condition'));
    }

    public function privacy_policy()
    {
        $privacy_policy = BusinessSetting::where('type', 'privacy_policy')->first();
        return view('privacy_policy_page', compact('privacy_policy'));
    }
    

    public function refund_policy()
    {
        $refund_policy = json_decode(BusinessSetting::where('type', 'refund-policy')->first()->value,true);
        $refund_policy = $refund_policy;
        return view('refund_policy_page', compact('refund_policy'));
    }

    public function e_wallet_policy()
    {
        $e_wallet_policy = BusinessSetting::where('type', 'e_wallet_Policy')->first();
        return view('e_wallet_policy', compact('e_wallet_policy'));
    }

    public function return_policy()
    {
        $return_policy = json_decode(BusinessSetting::where('type', 'return-policy')->first()->value);
        $return_policy = $return_policy->content;
        return view(VIEW_FILE_NAMES['return_policy_page'], compact('return_policy'));
    }

    public function cancellation_policy()
    {
        $cancellation_policy = json_decode(BusinessSetting::where('type', 'cancellation-policy')->first()->value);
        if(!$cancellation_policy->status){
            return back();
        }
        $cancellation_policy = $cancellation_policy->content;
        return view(VIEW_FILE_NAMES['cancellation_policy_page'], compact('cancellation_policy'));
    }

    public function shipping_policy()
    {
        $shipping_policy = BusinessSetting::where('type', 'shipping_policy')->first();
        return view('shipping_policy_page', compact('shipping_policy'));
    }

    public function secure_payment_policy()
    {
        $secure_payment_policy = BusinessSetting::where('type', 'secure_payment_policy')->first();
        return view('secure_payment_policy', compact('secure_payment_policy'));
    }

    public function instant_delivery_policy()
    {
        $instant_delivery_policy = BusinessSetting::where('type', 'instant_delivery_policy')->first();
        return view('instant_delivery_policy', compact('instant_delivery_policy'));
    }
}
