<?php

namespace App\Providers;

use App\CPU\Helpers;
use App\Model\Banner;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\Currency;
use App\Model\FlashDeal;
use App\Model\Product;
use App\Model\Shop;
use App\Model\SocialMedia;
use App\Model\Tag;
use App\Model\ShipyaariCredential;
use App\Services\SeoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

ini_set('memory_limit', -1);
ini_set('upload_max_filesize', '180M');
ini_set('post_max_size', '200M');

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Amirami\Localizator\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        /**
         * Inject SEO data into all views
         */
        View::composer('*', function ($view) {
            $view->with('seo', SeoService::getSeoData());
        });

        /**
         * Force non-www redirect
         */
        if (request()->getHost() && str_starts_with(request()->getHost(), 'www.')) {
            $nonWww = preg_replace('/^www\./', '', request()->getHost());
            $url = request()->getScheme() . '://' . $nonWww . request()->getRequestUri();
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $url");
            exit();
        }

        Paginator::useBootstrap();

        /**
         * Global web configuration
         */
        try {
            if (Schema::hasTable('business_settings')) {
                $web = BusinessSetting::all();
                $colors = Helpers::get_settings($web, 'colors');
                $colorData = json_decode($colors['value'], true);

                $web_config = [
                    'primary_color'          => $colorData['primary'] ?? '',
                    'secondary_color'        => $colorData['secondary'] ?? '',
                    'primary_color_light'    => $colorData['primary_light'] ?? '',
                    'name'                   => Helpers::get_settings($web, 'company_name'),
                    'phone'                  => Helpers::get_settings($web, 'company_phone'),
                    'web_logo'               => Helpers::get_settings($web, 'company_web_logo'),
                    'mob_logo'               => Helpers::get_settings($web, 'company_mobile_logo'),
                    'fav_icon'               => Helpers::get_settings($web, 'company_fav_icon'),
                    'email'                  => Helpers::get_settings($web, 'company_email'),
                    'about'                  => Helpers::get_settings($web, 'about_us'),
                    'footer_logo'             => Helpers::get_settings($web, 'company_footer_logo'),
                    'copyright_text'          => Helpers::get_settings($web, 'company_copyright_text'),
                    'decimal_point_settings'  => Helpers::get_business_settings('decimal_point_settings') ?? 0,
                    'seller_registration'     => BusinessSetting::where('type', 'seller_registration')->value('value'),
                    'wallet_status'            => Helpers::get_business_settings('wallet_status'),
                    'loyalty_point_status'     => Helpers::get_business_settings('loyalty_point_status'),
                ];

                if (!Request::is('admin') && !Request::is('admin/*') && !Request::is('seller/*')) {
                    $flash_deals = FlashDeal::with(['products.product.reviews', 'products.product' => function ($q) {
                        $q->active();
                    }])
                        ->where(['deal_type' => 'flash_deal', 'status' => 1])
                        ->whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('end_date', '>=', date('Y-m-d'))
                        ->first();

                    $featured_deals = Product::active()
                        ->with(['seller.shop', 'flash_deal_product.feature_deal', 'flash_deal_product.flash_deal' => function ($q) {
                            $q->whereDate('start_date', '<=', date('Y-m-d'))
                                ->whereDate('end_date', '>=', date('Y-m-d'));
                        }])
                        ->whereHas('flash_deal_product.feature_deal', function ($q) {
                            $q->whereDate('start_date', '<=', date('Y-m-d'))
                                ->whereDate('end_date', '>=', date('Y-m-d'));
                        })
                        ->get();

                    foreach ($featured_deals as $product) {
                        $flash_deal_status = 0;
                        $flash_deal_end_date = 0;

                        foreach ($product->flash_deal_product as $deal) {
                            if ($deal->flash_deal) {
                                $flash_deal_status = 1;
                                $flash_deal_end_date = date('Y-m-d H:i:s', strtotime($deal->flash_deal->end_date));
                            }
                        }

                        $product['flash_deal_status'] = $flash_deal_status;
                        $product['flash_deal_end_date'] = $flash_deal_end_date;
                    }

                    $shops = Shop::whereHas('seller', fn($q) => $q->approved())->take(9)->get();
                    $recaptcha = Helpers::get_business_settings('recaptcha');
                    $socials_login = Helpers::get_business_settings('social_login');
                    $social_login_text = collect($socials_login)->contains(fn($s) => $s['status'] ?? false);

                    $popup_banner = Banner::inRandomOrder()->where(['published' => 1, 'banner_type' => 'Popup Banner'])->first();
                    $header_banner = Banner::where('banner_type', 'Header Banner')->where('published', 1)->latest()->first();

                    $payments_name_list = ['ssl_commerz_payment', 'paypal', 'stripe', 'razor_pay', 'senang_pay',
                        'paytabs', 'paystack', 'paymob_accept', 'fawry_pay', 'mercadopago', 'liqpay', 'flutterwave',
                        'paytm', 'bkash'];
                    $payments_list = BusinessSetting::whereIn('type', $payments_name_list)
                        ->whereJsonContains('value->status', '1')
                        ->pluck('type');

                    $web_config += [
                        'cookie_setting'         => Helpers::get_settings($web, 'cookie_setting'),
                        'announcement'            => Helpers::get_business_settings('announcement'),
                        'currency_model'          => Helpers::get_business_settings('currency_model'),
                        'currencies'               => Currency::where('status', 1)->get(),
                        'main_categories'          => Category::with(['childes.childes'])->where('position', 0)->priority()->get(),
                        'business_mode'             => Helpers::get_business_settings('business_mode'),
                        'social_media'               => SocialMedia::where('active_status', 1)->get(),
                        'ios'                          => Helpers::get_business_settings('download_app_apple_stroe'),
                        'android'                       => Helpers::get_business_settings('download_app_google_stroe'),
                        'refund_policy'                  => Helpers::get_business_settings('refund-policy'),
                        'return_policy'                    => Helpers::get_business_settings('return-policy'),
                        'cancellation_policy'               => Helpers::get_business_settings('cancellation-policy'),
                        'flash_deals'                          => $flash_deals,
                        'featured_deals'                         => $featured_deals,
                        'shops'                                    => $shops,
                        'brand_setting'                              => Helpers::get_business_settings('product_brand'),
                        'discount_product'                            => Product::with(['reviews'])->active()->where('discount', '!=', 0)->count(),
                        'recaptcha'                                     => $recaptcha,
                        'socials_login'                                   => $socials_login,
                        'social_login_text'                                 => $social_login_text,
                        'popup_banner'                                        => $popup_banner,
                        'header_banner'                                          => $header_banner,
                        'payments_list'                                            => $payments_list,
                    ];

                    if (theme_root_path() == "theme_fashion") {
                        $features_section = [
                            'features_section_top' => BusinessSetting::where('type', 'features_section_top')->value('value') ?? [],
                            'features_section_middle' => BusinessSetting::where('type', 'features_section_middle')->value('value') ?? [],
                            'features_section_bottom' => BusinessSetting::where('type', 'features_section_bottom')->value('value') ?? [],
                        ];

                        $tags = Tag::orderBy('visit_count', 'desc')->take(15)->get();
                        $total_discount_products = Product::with(['reviews'])->active()->where('discount', '!=', 0)->count();

                        $web_config += [
                            'tags' => $tags,
                            'features_section' => $features_section,
                            'total_discount_products' => $total_discount_products,
                        ];
                    }
                }

                $language = BusinessSetting::where('type', 'language')->first();
                \App\CPU\Helpers::currency_load();

                View::share(['web_config' => $web_config, 'language' => $language]);
                Schema::defaultStringLength(191);
            }
        } catch (\Exception $exception) {
            // Avoid breaking artisan commands
        }

        /**
         * Add collection paginate() macro
         */
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        /**
         * Inject Shipyaari API key from DB into runtime env
         */
        if (Schema::hasTable('shipyaari_credential')) {
            try {
                $token = ShipyaariCredential::orderBy('id', 'desc')->value('token');
                if ($token) {
                    config(['services.shipyaari.api_key' => $token]);
                    $_ENV['SHIPYAARI_API_KEY'] = $token;
                    putenv("SHIPYAARI_API_KEY={$token}");
                }
            } catch (\Exception $e) {
                // Ignore errors to avoid breaking artisan
            }
        }
    }
}
