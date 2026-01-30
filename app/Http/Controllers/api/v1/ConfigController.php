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