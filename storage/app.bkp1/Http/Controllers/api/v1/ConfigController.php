<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Color;
use App\Model\Currency;
use App\Model\HelpTopic;
use Illuminate\Http\Request;
use App\Model\ShippingType;
use App\Model\SocialMedia;
use App\Model\HelpTopicSubCategory;

class ConfigController extends Controller
{
    // public function configuration()
    // {
    //     $currency = Currency::all();
    //     $social_login = [];
    //     foreach (Helpers::get_business_settings('social_login') as $social) {
    //         $config = [
    //             'login_medium' => $social['login_medium'],
    //             'status' => (boolean)$social['status']
    //         ];
    //         array_push($social_login, $config);
    //     }

    //     $languages = Helpers::get_business_settings('pnc_language');
    //     $lang_array = [];
    //     foreach ($languages as $language) {
    //         array_push($lang_array, [
    //             'code' => $language,
    //             'name' => Helpers::get_language_name($language)
    //         ]);
    //     }
    //     $payment = [
    //         'offline_payment' => Helpers::get_business_settings('offline_payment')['status'] == 1 ?? 0,
    //         // 'ssl_commerz_payment' => Helpers::get_business_settings('ssl_commerz_payment')['status'] == 1 ?? 0,
    //         // 'paypal' => Helpers::get_business_settings('paypal')['status'] == 1 ?? 0,
    //         // 'stripe' => Helpers::get_business_settings('stripe')['status'] == 1 ?? 0,
    //         'razor_pay' => Helpers::get_business_settings('razor_pay')['status'] == 1 ?? 0,
    //         // 'senang_pay' => Helpers::get_business_settings('senang_pay')['status'] == 1 ?? 0,
    //         // 'paytabs' => Helpers::get_business_settings('paytabs')['status'] == 1 ?? 0,
    //         // 'paystack' => Helpers::get_business_settings('paystack')['status'] == 1 ?? 0,
    //         // 'paymob_accept' => Helpers::get_business_settings('paymob_accept')['status'] == 1 ?? 0,
    //         // 'fawry_pay' => Helpers::get_business_settings('fawry_pay')['status'] == 1 ?? 0,
    //         // 'mercadopago' => Helpers::get_business_settings('mercadopago')['status'] == 1 ?? 0,
    //         // 'liqpay' => Helpers::get_business_settings('liqpay')['status'] == 1 ?? 0,
    //         // 'flutterwave' => Helpers::get_business_settings('flutterwave')['status'] == 1 ?? 0,
    //         // 'paytm' => Helpers::get_business_settings('paytm')['status'] == 1 ?? 0,
    //         // 'bkash' => Helpers::get_business_settings('bkash')['status'] == 1 ?? 0
    //     ];

    //     $admin_shipping = ShippingType::where('seller_id',0)->first();
    //     $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';

    //     $company_logo = asset("storage/app/public/company/").'/'.BusinessSetting::where(['type'=>'company_web_logo'])->first()->value;

    //     return response()->json([
    //         'brand_setting' => BusinessSetting::where('type', 'product_brand')->first()->value,
    //         'digital_product_setting' => BusinessSetting::where('type', 'digital_product')->first()->value,
    //         'system_default_currency' => (int)Helpers::get_business_settings('system_default_currency'),
    //         'digital_payment' => (boolean)Helpers::get_business_settings('digital_payment')['status'] ?? 0,
    //         'cash_on_delivery' => (boolean)Helpers::get_business_settings('cash_on_delivery')['status'] ?? 0,
    //         'seller_registration' => BusinessSetting::where('type', 'seller_registration')->first()->value,
    //         'pos_active' => BusinessSetting::where('type','seller_pos')->first()->value,
    //         'company_phone' => Helpers::get_business_settings('company_phone'),
    //         'company_whatapp_number' => Helpers::get_business_settings('company_whatapp_number'),
    //         'company_email' => Helpers::get_business_settings('company_email'),
    //         'company_logo' => $company_logo,
    //         'delivery_country_restriction' => Helpers::get_business_settings('delivery_country_restriction'),
    //         'delivery_zip_code_area_restriction' => Helpers::get_business_settings('delivery_zip_code_area_restriction'),
    //         'base_urls' => [
    //             'product_image_url' => ProductManager::product_image_path('product'),
    //             'product_thumbnail_url' => ProductManager::product_image_path('thumbnail'),
    //             'digital_product_url' => asset('storage/app/public/product/digital-product'),
    //             'brand_image_url' => asset('storage/app/public/brand'),
    //             'customer_image_url' => asset('storage/app/public/profile'),
    //             'banner_image_url' => asset('storage/app/public/banner'),
    //             'category_image_url' => asset('storage/app/public/category'),
    //             'review_image_url' => asset('storage/app/public'),
    //             'seller_image_url' => asset('storage/app/public/seller'),
    //             'shop_image_url' => asset('storage/app/public/shop'),
    //             'notification_image_url' => asset('storage/app/public/notification'),
    //             'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
    //         ],
    //         'static_urls' => [
    //             'contact_us' => route('contacts'),
    //             'brands' => route('brands'),
    //             'categories' => route('categories'),
    //             'customer_account' => route('user-account'),
    //         ],
    //         'social_media_links' => SocialMedia::where('active_status',1)->get(),
    //         'about_us' => Helpers::get_business_settings('about_us'),
    //         'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
    //         'e_wallet_Policy' => Helpers::get_business_settings('e_wallet_Policy'),
    //         'shipping_policy' => Helpers::get_business_settings('shipping_policy'),
    //         'secure_payment_policy' => Helpers::get_business_settings('secure_payment_policy'),
    //         'instant_delivery_policy' => Helpers::get_business_settings('instant_delivery_policy'),
    //         'faq' => HelpTopic::all(),
    //         'terms_&_conditions' => Helpers::get_business_settings('terms_condition'),
    //         'refund_policy' => Helpers::get_business_settings('refund-policy'),
    //         'return_policy' => Helpers::get_business_settings('return-policy'),
    //         'cancellation_policy' => Helpers::get_business_settings('cancellation-policy'),
    //         'currency_list' => $currency,
    //         'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
    //         'business_mode'=> Helpers::get_business_settings('business_mode'),
    //         'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
    //         'language' => $lang_array,
    //         'colors' => Color::all(),
    //         'unit' => Helpers::units(),
    //         'shipping_method' => Helpers::get_business_settings('shipping_method'),
    //         'email_verification' => (boolean)Helpers::get_business_settings('email_verification'),
    //         'phone_verification' => (boolean)Helpers::get_business_settings('phone_verification'),
    //         'country_code' => Helpers::get_business_settings('country_code'),
    //         'social_login' => $social_login,
    //         'currency_model' => Helpers::get_business_settings('currency_model'),
    //         'forgot_password_verification' => Helpers::get_business_settings('forgot_password_verification'),
    //         'announcement'=> Helpers::get_business_settings('announcement'),
    //         'pixel_analytics'=> Helpers::get_business_settings('pixel_analytics'),
    //         'software_version'=>env('SOFTWARE_VERSION'),
    //         'decimal_point_settings'=>Helpers::get_business_settings('decimal_point_settings'),
    //         'inhouse_selected_shipping_type'=>$shipping_type,
    //         'billing_input_by_customer'=>Helpers::get_business_settings('billing_input_by_customer'),
    //         'minimum_order_limit'=>Helpers::get_business_settings('minimum_order_limit'),
    //         'wallet_status'=>Helpers::get_business_settings('wallet_status'),
    //         'loyalty_point_status'=>Helpers::get_business_settings('loyalty_point_status'),
    //         'loyalty_point_exchange_rate'=>Helpers::get_business_settings('loyalty_point_exchange_rate'),
    //         'loyalty_point_minimum_point'=>Helpers::get_business_settings('loyalty_point_minimum_point'),
    //         'payment_methods' => $payment,
    //         'ref_bonus' => BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value'),
    //         'shiprocket_token'=>Helpers::get_business_settings('shiprocket_token'),
            
    //     ]);
    // }

    public function configuration()
{
    // Cache all business settings at once
    $businessSettings = BusinessSetting::all()->pluck('value', 'type');
    $socialLogins = Helpers::get_business_settings('social_login') ?? [];

    // Social login
    $social_login = array_map(function ($social) {
        return [
            'login_medium' => $social['login_medium'],
            'status' => (bool) $social['status'],
        ];
    }, $socialLogins);

    // Languages
    $languages = Helpers::get_business_settings('pnc_language') ?? [];
    $lang_array = array_map(function ($lang) {
        return [
            'code' => $lang,
            'name' => Helpers::get_language_name($lang),
        ];
    }, $languages);

    // Payment methods
    $payment = [
        'offline_payment' => (bool) ($businessSettings['offline_payment']['status'] ?? 0),
        'razor_pay' => (bool) ($businessSettings['razor_pay']['status'] ?? 0),
    ];

    // Admin shipping type
    $shipping_type = ShippingType::where('seller_id', 0)->value('shipping_type') ?? 'order_wise';

    // Company logo
    $companyLogoSetting = $businessSettings['company_web_logo'] ?? 'default.png';
    $company_logo = asset("storage/app/public/company/" . $companyLogoSetting);
    $subcategories = HelpTopicSubCategory::with(['faqs' => function($q) {
            $q->where('status', 1);
        }])->get();

        $faqsWithoutCategory = HelpTopic::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereNull('sub_cat_id');
            })
            ->get();

    return response()->json([
        'faq' => [
        'categorized' => $subcategories,
        'uncategorized' => $faqsWithoutCategory
        ],
        'brand_setting' => $businessSettings['product_brand'] ?? '',
        'digital_product_setting' => $businessSettings['digital_product'] ?? '',
        'system_default_currency' => (int) ($businessSettings['system_default_currency'] ?? 0),
        'digital_payment' => (bool) ($businessSettings['digital_payment']['status'] ?? 0),
        'cash_on_delivery' => (bool) ($businessSettings['cash_on_delivery']['status'] ?? 0),
        'seller_registration' => $businessSettings['seller_registration'] ?? '',
        'pos_active' => $businessSettings['seller_pos'] ?? '',
        'company_phone' => $businessSettings['company_phone'] ?? '',
        'company_whatapp_number' => $businessSettings['company_whatapp_number'] ?? '',
        'company_email' => $businessSettings['company_email'] ?? '',
        'company_logo' => $company_logo,
        'delivery_country_restriction' => $businessSettings['delivery_country_restriction'] ?? '',
        'delivery_zip_code_area_restriction' => $businessSettings['delivery_zip_code_area_restriction'] ?? '',
        'base_urls' => [
            'product_image_url' => ProductManager::product_image_path('product'),
            'product_thumbnail_url' => ProductManager::product_image_path('thumbnail'),
            'digital_product_url' => asset('storage/app/public/product/digital-product'),
            'brand_image_url' => asset('storage/app/public/brand'),
            'customer_image_url' => asset('storage/app/public/profile'),
            'banner_image_url' => asset('storage/app/public/banner'),
            'category_image_url' => asset('storage/app/public/category'),
            'review_image_url' => asset('storage/app/public'),
            'seller_image_url' => asset('storage/app/public/seller'),
            'shop_image_url' => asset('storage/app/public/shop'),
            'notification_image_url' => asset('storage/app/public/notification'),
            'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
        ],
        'static_urls' => [
            'contact_us' => route('contacts'),
            'brands' => route('brands'),
            'categories' => route('categories'),
            'customer_account' => route('user-account'),
        ],
        'social_media_links' => SocialMedia::where('active_status', 1)->get(),
        'about_us' => $businessSettings['about_us'] ?? '',
        'privacy_policy' => $businessSettings['privacy_policy'] ?? '',
        'e_wallet_Policy' => $businessSettings['e_wallet_Policy'] ?? '',
        'shipping_policy' => $businessSettings['shipping_policy'] ?? '',
        'secure_payment_policy' => $businessSettings['secure_payment_policy'] ?? '',
        'instant_delivery_policy' => $businessSettings['instant_delivery_policy'] ?? '',
        'terms_&_conditions' => $businessSettings['terms_condition'] ?? '',
        'refund_policy' => json_decode($businessSettings['refund-policy']),
        'return_policy' => json_decode($businessSettings['return-policy']) ,
        'cancellation_policy' => json_decode($businessSettings['cancellation-policy']) ,
        'currency_list' => Currency::all(),
        'currency_symbol_position' => $businessSettings['currency_symbol_position'] ?? 'right',
        'business_mode' => $businessSettings['business_mode'] ?? '',
        'maintenance_mode' => (bool) ($businessSettings['maintenance_mode'] ?? 0),
        'language' => $lang_array,
        'colors' => Color::all(),
        'unit' => Helpers::units(),
        'shipping_method' => $businessSettings['shipping_method'] ?? '',
        'email_verification' => (bool) ($businessSettings['email_verification'] ?? 0),
        'phone_verification' => (bool) ($businessSettings['phone_verification'] ?? 0),
        'country_code' => $businessSettings['country_code'] ?? '',
        'social_login' => $social_login,
        'currency_model' => $businessSettings['currency_model'] ?? '',
        'forgot_password_verification' => $businessSettings['forgot_password_verification'] ?? '',
        'announcement' => json_decode($businessSettings['announcement']),
        'pixel_analytics' => $businessSettings['pixel_analytics'] ?? '',
        'software_version' => env('SOFTWARE_VERSION'),
        'decimal_point_settings' => $businessSettings['decimal_point_settings'] ?? 2,
        'inhouse_selected_shipping_type' => $shipping_type,
        'billing_input_by_customer' => $businessSettings['billing_input_by_customer'] ?? '',
        'minimum_order_limit' => $businessSettings['minimum_order_limit'] ?? '',
        'wallet_status' =>(int) $businessSettings['wallet_status'] ?? '',
        'loyalty_point_status' =>(int) $businessSettings['loyalty_point_status'] ?? '',
        'loyalty_point_exchange_rate' => $businessSettings['loyalty_point_exchange_rate'] ?? '',
        'loyalty_point_minimum_point' => $businessSettings['loyalty_point_minimum_point'] ?? '',
        'payment_methods' => $payment,
        'ref_bonus' => $businessSettings['Signup_refer_point_sender'] ?? 0,
        'shiprocket_token' => $businessSettings['shiprocket_token'] ?? '',
    ]);
}

   


}

