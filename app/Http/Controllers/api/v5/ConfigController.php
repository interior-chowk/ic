<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Color;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Model\ServiceProviderSocialMedia;
use App\Model\HelpTopic;
use Illuminate\Http\Request;
use App\Model\ShippingType;
use App\Model\ServiceProviderPlan;
use App\Traits\CommonTrait;
use App\User;

class ConfigController extends Controller
{
    
    use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }
    
    public function handleConfiguration(Request $request)
    {
        
        if (auth('api')->check()) {
            return $this->configuration($request); 
        } else {
            return $this->public_configuration($request); 
        }
    }
    
    protected  function configuration(Request $request)
    {
        $user = auth('api')->user();
        $membership_user = true;
       if($user)
        {
            $membership_user_plan = ServiceProviderPlan::with('membership')->where('provider_id',$user->id)->where('status',1)->latest()->first();
            if($membership_user_plan && $membership_user_plan->membership){
                if($membership_user_plan->membership->social_media_link)
                {
                    $membership_user = true;
                }
            }
        }
        
        $currency = Currency::all();
        $social_login = [];
        foreach (Helpers::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (boolean)$social['status']
            ];
            array_push($social_login, $config);
        }

        $languages = Helpers::get_business_settings('pnc_language');
        $lang_array = [];
        foreach ($languages as $language) {
            array_push($lang_array, [
                'code' => $language,
                'name' => Helpers::get_language_name($language)
            ]);
        }
        $payment = [
            'offline_payment' => Helpers::get_business_settings('offline_payment')['status'] == 1 ?? 0,
            'razor_pay' => Helpers::get_business_settings('razor_pay')['status'] == 1 ?? 0,
        ];

        $admin_shipping = ShippingType::where('seller_id',0)->first();
        $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';

        $company_logo = asset("storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_web_logo'])->first()->value;

        return response()->json([
            'about_us' => Helpers::get_business_settings('about_us'),
            'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
            'terms_&_conditions' => Helpers::get_business_settings('provider_terms_condition'),
            'social_medias' => ($membership_user) ? ServiceProviderSocialMedia::where('active_status',1)->get() : [],
            'ref_bonus' => BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value'),
        ]);
    }
    
    protected  function public_configuration(Request $request)
    {
        $membership_user = true;
        $currency = Currency::all();
        $social_login = [];
        foreach (Helpers::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (boolean)$social['status']
            ];
            array_push($social_login, $config);
        }

        $languages = Helpers::get_business_settings('pnc_language');
        $lang_array = [];
        foreach ($languages as $language) {
            array_push($lang_array, [
                'code' => $language,
                'name' => Helpers::get_language_name($language)
            ]);
        }
        $payment = [
            'offline_payment' => Helpers::get_business_settings('offline_payment')['status'] == 1 ?? 0,
            'razor_pay' => Helpers::get_business_settings('razor_pay')['status'] == 1 ?? 0,
        ];

        $admin_shipping = ShippingType::where('seller_id',0)->first();
        $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';

        $company_logo = asset("storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_web_logo'])->first()->value;

        return response()->json([
            'about_us' => Helpers::get_business_settings('about_us'),
            'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
            'terms_&_conditions' => Helpers::get_business_settings('provider_terms_condition'),
            'social_medias' => ($membership_user) ? ServiceProviderSocialMedia::where('active_status',1)->get() : [],
            'ref_bonus' => BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value'),
        ]);
    }
}