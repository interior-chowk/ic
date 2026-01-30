<?php

namespace App\Http\Controllers\Web;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use App\Model\Brand;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\Coupon;
use App\Model\DealOfTheDay;
use App\Model\HelpTopic;
use App\Model\Blog;
use App\Model\HelpTopicSubCategory;
use App\Model\FlashDeal;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use Illuminate\Support\Facades\Log;
use App\Model\Review;
use App\Model\Seller;
use App\Model\Career;
use App\Model\Applicant;
use App\Model\WalletTransaction;
use App\Model\CustomerWalletHistory;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Model\Contact;
use App\Model\PageContent;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use App\Model\ProviderReviews;

class HomeController extends Controller
{
    public function __construct(
        private Product      $product,
        private Order        $order,
        private OrderDetail  $order_details,
        private Category     $category,
        private Seller       $seller,
        private Review       $review,
        private DealOfTheDay $deal_of_the_day,
        private Banner       $banner,
    ) {}
    
    public function policy(Request $request)
    {
        $policy = BusinessSetting::where('type', $request->type)->first();
        return view('policy', compact('policy'));
    }

    public function request_for_callBack_mail(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9+\- ]{7,15}$/',
            'email' => 'required|email',
            'interested' => 'required',
            'message' => 'required|string',
        ]);


        $contact = new Contact;
        $contact->name = $request->name;
        $contact->mobile_number = $request->phone;
        $contact->email = $request->email;
        $contact->subject = $request->interested;
        $contact->message = $request->message;
        $contact->save();

        $fromAddress = 'customersupport@interiorchowk.com';

        $mailMessage = new \App\Mail\CallBackRequest($request);
        $mailMessage->from($fromAddress);

        Mail::to('support@interiorchowk.com')->send($mailMessage);
        Mail::to('info.interiorchowk@gmail.com')->send($mailMessage);
        // Mail::to('esoftcode@gmail.com')->send($mailMessage);

        // $res =  Mail::to('puneet2017prajapati@gmail.com')->send(new \App\Mail\CallBackRequest($request)); 
        Session::put('for_callback', 'for_callback');
        return redirect()->back();
    }

    public function seller_dashboard()
    {
        return view('welcome');
    }

    public function shopping()
    {

        return  view('shopping');
    }

    public  function service()
    {

        return  view('service');
    }

    public function solution()
    {

        return  view('solution');
    }

    public function service_chowk()
    {

        return  view('service-chowk');
    }

    public function seller_chowk()
    {
        return  view('seller-chowk');
    }

    public function faq()
    {
        $subcategories = HelpTopicSubCategory::with('faqs')->get();
        $faqsWithoutCategory = HelpTopic::whereNull('category_id')
            ->orWhereNull('sub_cat_id')
            ->get();
        // dd($faqsWithoutCategory);

        return view('faq', compact('subcategories', 'faqsWithoutCategory'));
    }

    public function contact_us()
    {
        return view('contactus');
    }

    public function career()
    {
        $careers = Career::all();
        return view('career', compact('careers'));

    }

    // public function careerapply(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'full_name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z\s]+$/'],
    //         'city' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\s]+$/'],
    //         'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
    //         'email' => [
    //             'required',
    //             'string',
    //             'max:100',
    //             'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
    //         ],
    //         'experience' => ['nullable', 'regex:/^\d{1,2}-\d{1,2}$/'],
    //         'portfolio_links' => 'nullable|string|max:500',
    //         'resume' => 'required|file|max:100|mimes:jpg,jpeg,png,pdf,doc,docx,webp',
    //     ], [
    //         'full_name.regex' => 'Full name may only contain letters and spaces.',
    //         'city.regex' => 'City name may only contain letters and spaces.',
    //         'phone.regex' => 'Enter a valid 10-digit Indian phone number.',
    //         'email.regex' => 'Enter a valid email address (e.g. example@gmail.com).',
    //         'resume.max' => 'Resume must not exceed 100KB.',
    //         'resume.mimes' => 'Allowed types: jpg, jpeg, png, pdf, doc, docx, webp.',
    //     ]);

    //     $resumePath = null;
    //     if ($request->hasFile('resume')) {
    //         $resumePath = $request->file('resume')->store('resumes', 'public');
    //     }

    //     Applicant::create([
    //         'career_id' => $id,
    //         'full_name' => $validated['full_name'],
    //         'city' => $validated['city'],
    //         'phone' => $validated['phone'],
    //         'email' => $validated['email'],
    //         'experience' => $validated['experience'] ?? null,
    //         'portfolio_links' => $validated['portfolio_links'] ?? null,
    //         'resume' => $resumePath,
    //     ]);

    //     $career = Career::findOrFail($id);
    //     $career->increment('applicants');

    //     // AJAX response
    //     if ($request->ajax()) {
    //         return response()->json(['status' => 'success', 'message' => 'Application submitted successfully!']);
    //     }

    //     return back()->with('success', 'Application submitted successfully!');
    // }

    
    // public function careerapply(Request $request, $id)
    // {
    //     // ✅ Validate required fields
    //     $request->validate([
    //         'full_name' => 'required|string|max:255',
    //         'city' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'email' => 'required|email',
    //         'resume' => 'required|file|max:4096' // 4MB max
    //     ]);

    //     $resumePath = null;
    //     $cloudflareImageId = null;
    //     $cloudflareImageUrl = null;

    //     if ($request->hasFile('resume')) {
    //         $file = $request->file('resume');
    //         $mime = strtolower($file->getClientMimeType());
    //         $isImage = str_contains($mime, 'image');

    //         if ($isImage) {
    //             // ✅ Upload to Cloudflare Images
    //             try {
    //                 $accountId = env('CLOUDFLARE_ACCOUNT_ID');
    //                 $token = env('CLOUDFLARE_IMAGES_TOKEN');
    //                 $uploadUrl = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/images/v1";

    //                 $response = Http::withToken($token)
    //                     ->attach(
    //                         'file',
    //                         fopen($file->getRealPath(), 'r'),
    //                         $file->getClientOriginalName()
    //                     )
    //                     ->post($uploadUrl);

    //                 if ($response->successful() && $response->json('success')) {
    //                     $result = $response->json('result');
    //                     $cloudflareImageId = $result['id'] ?? null;
    //                     $cloudflareImageUrl = $result['variants'][0] ?? null;
    //                     $resumePath = $cloudflareImageUrl;
    //                 } else {
    //                     \Log::error('Cloudflare upload failed', [
    //                         'response' => $response->json()
    //                     ]);
    //                     $resumePath = $file->store('resumes', 'public');
    //                 }

    //             } catch (\Exception $e) {
    //                 \Log::error('Cloudflare exception: '.$e->getMessage());
    //                 $resumePath = $file->store('resumes', 'public');
    //             }

    //         } else {
    //             // ✅ Save PDF/DOC locally
    //             $resumePath = $file->store('resumes', 'public');
    //         }
    //     }

    //     // ✅ Store Applicant data
    //     $applicant = Applicant::create([
    //         'career_id' => $id,
    //         'full_name' => $request->full_name,
    //         'city' => $request->city,
    //         'phone' => $request->phone,
    //         'email' => $request->email,
    //         'experience' => $request->experience ?? null,
    //         'portfolio_links' => $request->portfolio_links ?? null,
    //         'resume' => $resumePath,
    //         'resume_cf_id' => $cloudflareImageId,
    //         'resume_cf_url' => $cloudflareImageUrl,
    //     ]);

    //     // ✅ Increment applicant count
    //     Career::findOrFail($id)->increment('applicants');

    //     // ✅ AJAX Response
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Application submitted successfully!',
    //             'data' => $applicant
    //         ]);
    //     }

    //     return back()->with('success', 'Application submitted successfully!');
    // }

    public function careerapply(Request $request, $id)
    {
        // ✅ Validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'resume' => 'required|file|max:4096'
        ]);

        $resumePath = null;
        $cloudflareId = null;
        $cloudflareUrl = null;

        if ($request->hasFile('resume')) 
        {
            $storedPath = $request->file('resume')->store('resumes', 'r2');
            $resumePath = env('CLOUDFLARE_R2_PUBLIC_URL') . '/' . $storedPath;
        }

        // ✅ Saving Data in DB
        $applicant = Applicant::create([
            'career_id' => $id,
            'full_name' => $request->full_name,
            'city' => $request->city,
            'phone' => $request->phone,
            'email' => $request->email,
            'experience' => $request->experience ?? null,
            'portfolio_links' => $request->portfolio_links ?? null,
            'resume' => $resumePath,
            'resume_cf_id' => $cloudflareId,
            'resume_cf_url' => $cloudflareUrl,
        ]);

        // ✅ Increment total applicants
        Career::findOrFail($id)->increment('applicants');

        // ✅ Response (AJAX or normal form)
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully!',
                'data' => $applicant
            ]);
        }

        return back()->with('success', 'Application submitted successfully!');
    }

    public function blog()
    {
        $blogs = Blog::where('category', '!=', 'trending')->get();
        $trendBlog = Blog::where('category', 'trending')->get();
        return view('blog', compact('blogs', 'trendBlog'));
    }

    public function blogDetails($slug)
    {
        $blogs = Blog::where('slug', $slug)->first();
        // dd($blogs);
        return view('blog-details', compact('blogs'));
    }

    public function index()
    {
        return view('welcome');
        $theme_name = theme_root_path();

        return match ($theme_name) {
            'default' => self::default_theme(),
            'theme_aster' => self::theme_aster(),
            'theme_fashion' => self::theme_fashion(),
            'theme_all_purpose' => self::theme_all_purpose(),
        };
    }

    public function about_us()
    {
        return view('about-us');
    }

    public function index_1()
    {
        $main_banner = Banner::where('banner_type', 'Main Banner web')->where('published', 1)->get();
        $mobile_banner = Banner::where('banner_type', 'Main Banner')->where('published', 1)->get();
        $main_banner_2 = Banner::where('banner_type', 'Main Banner 2 web')->where('published', 1)->get();
        $mob_main_banner_2 = Banner::where('banner_type', 'Main Banner 2')->where('published', 1)->get();
        $Service_Provider_Banner_1 = Banner::where('banner_type', 'Service Provider Banner 1 web')->where('published', 1)->first();
        $Service_Provider_Banner_2 = Banner::where('banner_type', 'Service Provider Banner 2 web')->where('published', 1)->first();
        $Service_Provider_Banner_3 = Banner::where('banner_type', 'Service Provider Banner 3 web')->where('published', 1)->first();
        $Mob_Provider_Banner_1 = Banner::where('banner_type', 'Service Provider Banner 1')->where('published', 1)->first();
        $Mob_Provider_Banner_2 = Banner::where('banner_type', 'Service Provider Banner 2')->where('published', 1)->first();
        $Mob_Provider_Banner_3 = Banner::where('banner_type', 'Service Provider Banner 3')->where('published', 1)->first();
        $Instant_Delivery_Banner = Banner::where('banner_type', 'Instant Delivery Banner web')->where('published', 1)->first();
        $Mob_Instant_Delivery_Banner = Banner::where('banner_type', 'Instant Delivery Banner')->where('published', 1)->first();
        $Banner_2 = Banner::where('banner_type', 'More Premium Stuff')->where('published', 1)->first();
        $mobile3 = Banner::where('banner_type', 'Mobile:3')->where('published', 1)->first();
        // $Banner_3 = Banner::where('banner_type', 'Banner 3 web')->where('published', 1)->first();
        $Banner_3 = Banner::where('banner_type', 'Desktop:2')->where('published', 1)->first();
        // $Mob_Banner_3 = Banner::where('banner_type', 'Banner 3')->where('published', 1)->first();
        $Mob_Banner_3 = Banner::where('banner_type', 'Mobile:1')->where('published', 1)->first();
        $Banner_4 = Banner::where('banner_type', 'Desktop:3')->where('published', 1)->first();
        $Banner_5 = Banner::where('banner_type', 'Desktop:4')->where('published', 1)->first();
        $mobile4 = Banner::where('banner_type', 'Mobile:4')->where('published', 1)->first();
        $Banner_6 = Banner::where('banner_type', 'Mobile:5')->where('published', 1)->first();
        $mobile6 = Banner::where('banner_type', 'Mobile:6')->where('published', 1)->first();
        $Desktop5 = Banner::where('banner_type', 'Desktop:5')->where('published', 1)->first();
        $Banner_7 = Banner::where('banner_type', 'Desktop:6')->where('published', 1)->first();
        $Banner_8 = Banner::where('banner_type', 'Desktop:7')->where('published', 1)->first();
        $desktop1 = Banner::where('banner_type', 'Desktop:1')->where('published', 1)->first();
        $Banner_9 = Banner::where('banner_type', 'Desktop:1')->where('published', 1)->first();
        $Banner_10 = Banner::where('banner_type', 'Banner 10 web')->where('published', 1)->first();
        $Seasonal_Banner = Banner::where('banner_type', 'Seasonal Banner web')->where('published', 1)->get();
        $Day_BG = Banner::where('banner_type', 'Day BG')->where('published', 1)->first();
        $Day_BG_w = Banner::where('banner_type', 'Day BG web')->where('published', 1)->first();
        $Day_BG_mobile = Banner::where('banner_type', 'Day BG')->where('published', 1)->first();
        $Discount_1 = Banner::where('banner_type', 'Discount 1 web')->where('published', 1)->first();
        $Discount_2 = Banner::where('banner_type', 'Discount 2 web')->where('published', 1)->first();
        $Discount_3 = Banner::where('banner_type', 'Discount 3 web')->where('published', 1)->first();
        $Discount_4 = Banner::where('banner_type', 'Discount 4 web')->where('published', 1)->first();
        $Luxury_BG = Banner::where('banner_type', 'Luxury BG web')->where('published', 1)->first();
        $Luxury_BG_app = Banner::where('banner_type', 'Luxury BG')->where('published', 1)->first();

        $discount_banner_1 = Banner::where('banner_type','Discount 1')->where('published', 1)->first();
        $discount_banner_2 = Banner::where('banner_type','Discount 2')->where('published', 1)->first();
        $discount_banner_3 = Banner::where('banner_type','Discount 3')->where('published', 1)->first();
        $discount_banner_4 = Banner::where('banner_type','Discount 4')->where('published', 1)->first();
        $discount_banner_5 = Banner::where('banner_type','Discount 5')->where('published', 1)->first();
       // dd($discount_banner);
        //$choice_1 = Banner::where('banner_type','Choice 1')->where('published',1)->first();

        $choice_1 = DB::table('sku_product_new')
            ->join('banners', function ($join) {
                $join->on('sku_product_new.product_id', '=', 'banners.resource_id')
                    ->where('banners.banner_type', 'Choice 1')->where('published', 1);
            })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->select(
                'banners.*',
                'sku_product_new.product_id',
                'sku_product_new.image',
                'sku_product_new.discount_type',
                'sku_product_new.discount',
                'sku_product_new.listed_price',
                'sku_product_new.variant_mrp',
                'products.name',
                'products.slug',
                'products.details',
                'products.category_ids',
                'products.free_delivery'
            )
            ->first();
        // dd($choice_1);
        $choice_2 = DB::table('sku_product_new')
            ->join('banners', function ($join) {
                $join->on('sku_product_new.product_id', '=', 'banners.resource_id')
                    ->where('banners.banner_type', 'Choice 2')->where('published', 1);
            })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->select(
                'banners.*',
                'sku_product_new.product_id',
                'sku_product_new.image',
                'sku_product_new.discount_type',
                'sku_product_new.discount',
                'sku_product_new.listed_price',
                'sku_product_new.variant_mrp',
                'products.name',
                'products.slug',
                'products.details',
                'products.category_ids',
                'products.free_delivery'
            )
            ->first();


        $choice_3 = Banner::where('banner_type', 'Choice 3')->where('published', 1)->first();
        $choice_4 = Banner::where('banner_type', 'Choice 4')->where('published', 1)->first();

        $tips_1 = Banner::where('banner_type', 'Tips 1')->where('published', 1)->first();
        $tips_2 = Banner::where('banner_type', 'Tips 2')->where('published', 1)->first();
        $tips_3 = Banner::where('banner_type', 'Tips 3')->where('published', 1)->first();
        $tips_4 = Banner::where('banner_type', 'Tips 4')->where('published', 1)->first();
        $tips_5 = Banner::where('banner_type', 'Tips 5')->where('published', 1)->first();
        $tips_6 = Banner::where('banner_type', 'Tips 6')->where('published', 1)->first();

        // dd($Instant_Delivery_Banner);

        $categories = Category::whereIn('name', [
            'Decor',
            'Furnishing',
            'Garden',
            'Furniture',
            'Kitchen',
            'Electronics',
            'Electricals',
            'Hardware & Sanitary'
        ])
            ->orderByRaw("FIELD(name, 'Decor', 'Furnishing', 'Garden', 'Furniture', 'Kitchen', 'Electronics', 'Electricals', 'Hardware & Sanitary')")
            ->get();

        $top_categories = Category::where('status', '1')->get();
        $top_brands = Brand::where('status', '1')->get();
        $top_products = DB::table('sku_product_new')
            // ->join('home_products', function ($join) {
            //     $join->on('sku_product_new.product_id', '=', 'home_products.product_id')
            //         ->where('home_products.section_type', 'top_products');
            // })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->leftjoin('sellers','sku_product_new.seller_id','=','sellers.id')
            ->where('sellers.status','approved')
            ->where('products.featured',1)
            ->where('products.status',1)
            ->select(
                'sku_product_new.product_id',
                'sku_product_new.image',
                'sku_product_new.discount_type',
                'sku_product_new.discount',
                'sku_product_new.listed_price',
                'sku_product_new.variant_mrp',
                'products.name',
                'products.slug',
                'products.category_ids',
                'products.free_delivery',
                'sellers.status',
                'products.featured',
                'products.status'
            )
            ->get();
        // dd($top_products);

        $luxe_products = DB::table('sku_product_new')
            ->join('home_products', function ($join) {
                $join->on('sku_product_new.product_id', '=', 'home_products.product_id')
                    ->where('home_products.section_type', 'feature');
            })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->select(
                'sku_product_new.*',
                'products.name',
                'products.slug',
                'products.category_ids'
            )
            ->get();


        $architects = DB::table('users')
            ->where('role_name', 'Architect')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        $contractors = DB::table('users')
            ->where('role_name', 'Contractor')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        $interior_designer = DB::table('users')
            ->where('role_name', 'Interior Designer')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        $deals = DB::table('deal_of_the_days AS d')
            ->join('sku_product_new AS p', 'd.product_id', '=', 'p.product_id')
            ->leftJoin('products AS pr', 'p.product_id', '=', 'pr.id')
            ->select([
                'd.*',
                'p.*',
                'pr.name',
                'pr.slug'

            ])
            ->where('d.status', '1')
            ->where('d.expire_date_time', '>=', Carbon::now())
            ->get();

            // dd($deals);

        $recently_viewed = DB::table('recently_view')
            ->join('sku_product_new', function ($join) {
                $join->on('recently_view.product_id', '=', 'sku_product_new.product_id');
            })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->select(
                'recently_view.*',
                'sku_product_new.product_id as product_ids',
                'sku_product_new.discount as sku_discount',
                'sku_product_new.discount_type as sku_discount_type',
                'sku_product_new.*',
                'products.*',
                'products.slug',
                'products.name',
                'products.category_ids',
                'products.free_delivery'
            )
            ->where('recently_view.user_id', Auth::id())
            ->whereNotNull('thumbnail_image')
            ->get();

        $recentlyViewedIds = $recently_viewed->pluck('category_ids')->toArray();
        $recentlyViewedIds_1 = $recently_viewed->pluck('category_id')->unique()->toArray();
        $recentlyViewedIds_2 = $recently_viewed->pluck('sub_category_id')->unique()->toArray();
        $recentlyViewedIds_3 = $recently_viewed->pluck('sub_sub_category_id')->unique()->toArray();
        $excludedProductIds = $recently_viewed->pluck('product_id')->unique()->toArray();


        $related_products = DB::table('sku_product_new')
            ->join('products', 'sku_product_new.product_id', '=', 'products.id')
            ->whereIn('products.category_ids', $recentlyViewedIds)
            ->whereNotIn('products.id', $excludedProductIds)
            ->where('products.status', 1)
            ->limit(12)
            ->select(
                'sku_product_new.*',
                'products.*',
                'products.free_delivery',
                'sku_product_new.discount as sku_discount',
                'sku_product_new.discount_type as sku_discount_type'
            )
            ->get();



        $more_related_products = DB::table('sku_product_new')
            ->join('products', 'sku_product_new.product_id', '=', 'products.id')
            ->where('products.status', 1)
            ->whereIn('products.category_id', $recentlyViewedIds_1)
            ->whereIn('products.sub_category_id', $recentlyViewedIds_2)
            ->whereNotIn('products.sub_sub_category_id', $recentlyViewedIds_3)
            ->whereNotIn('products.id', $excludedProductIds)
            ->select(
                'sku_product_new.*',
                'products.*',
                'sku_product_new.discount as sku_discount',
                'sku_product_new.discount_type as sku_discount_type'
            )
            ->limit(12)
            ->get();

        // dd($recentlyViewedIds_1,$recentlyViewedIds_2,$recentlyViewedIds_3);
        // dd($more_related_products);


         $wishlists = DB::table('wishlists')
        ->join('sku_product_new', function($join) {
            $join->on('wishlists.product_id', '=', 'sku_product_new.product_id');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->whereRaw("CONVERT(wishlists.sku USING utf8mb4) = CONVERT(sku_product_new.id USING utf8mb4)")
        ->select(
            'wishlists.*',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.slug',
            'products.name',
            'products.free_delivery',
            'sku_product_new.discount as sku_discount',
            'sku_product_new.discount_type as sku_discount_type',
            'sku_product_new.listed_price',
            'sku_product_new.variant_mrp'
        )
        ->where('wishlists.customer_id', Auth::id())
        ->get();
        $caturl = env("CLOUDFLARE_R2_PUBLIC_URL");
        return view('welcomes_1', compact('mobile6','Desktop5','mobile4','desktop1','mobile3','Mob_Banner_3', 'Mob_Provider_Banner_1', 'Mob_Provider_Banner_2', 'Mob_Provider_Banner_3', 'mobile_banner', 'main_banner', 'mob_main_banner_2', 'main_banner_2', 'Service_Provider_Banner_1', 'Service_Provider_Banner_2', 'Service_Provider_Banner_3', 'Mob_Instant_Delivery_Banner', 'Instant_Delivery_Banner', 'Banner_2', 'Banner_3', 'Banner_4', 'Banner_5', 'Banner_6', 'Banner_7', 'Banner_8', 'Banner_9', 'Banner_10', 'Seasonal_Banner', 'Day_BG', 'Day_BG_mobile', 'Day_BG_w', 'Discount_1', 'Discount_2', 'Discount_3', 'Discount_4', 'Luxury_BG', 'choice_1', 'choice_2', 'choice_3', 'choice_4', 'tips_1', 'tips_2', 'tips_3', 'tips_4', 'tips_5', 'tips_6', 'categories', 'top_categories', 'top_brands', 'top_products', 'luxe_products', 'architects', 'contractors', 'interior_designer', 'deals', 'recently_viewed', 'wishlists', 'related_products', 'more_related_products','discount_banner_1','discount_banner_2','discount_banner_3','discount_banner_4','discount_banner_5','Luxury_BG_app','caturl'));
    }


    public function instant_1()
    {

        return view('web.instantDelivery_1');
    }

    public function instant_2($pincode)
    {


        $cityPincodes = [
            'ahmedabad' => '380001',
            'bengaluru' => '560001',
            'chandigarh' => '160017',
            'chennai' => '600001',
            'coimbatore' => '641001',
            'delhi' => '110001',
            'hyderabad' => '500001',
            'goa' => '403001',
        ];


        $matchedCity = array_search($pincode, $cityPincodes);

        if ($matchedCity !== false) {

            $warehouses = DB::table('warehouse')
                ->where('city', 'like', '%' . $matchedCity . '%')
                ->get();
        } else {

            $warehouses = DB::table('warehouse')
                ->where('pincode', $pincode)
                ->get();
        }


        $warehouse_ids = $warehouses->pluck('id')->toArray();


        $instant_products = DB::table('sku_product_new')
            ->join('products', 'sku_product_new.product_id', '=', 'products.id')
            ->whereIn('products.add_warehouse', $warehouse_ids)
            ->where('products.status', 1)
            ->select(
                'sku_product_new.*',
                'products.*',
                'sku_product_new.variation as sku_variation',
                'sku_product_new.discount as sku_discount',
                'sku_product_new.discount_type as sku_discount_type'
            )
            ->paginate(20);





        return view('web.instantDelivery_2', compact('instant_products'));
    }

    public function brands()
    {

        $top_brands = Brand::where('status', '1')
            ->orderByRaw("(name = 'Other')")
            ->get();

        return view('web.brand', compact('top_brands'));
    }

    public function architects()
    {

        $architects = DB::table('users')
            ->where('role_name', 'Architect')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        return view('web.architects', compact('architects'));
    }

    public function designers()
    {

        $interior_designer = DB::table('users')
            ->where('role_name', 'Interior Designer')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        return view('web.designers', compact('interior_designer'));
    }

    public function serviceProvider($slug)
    {
        $data = DB::table('users')->get()->first(function ($user) use ($slug) {
            return Str::slug($user->name) === $slug;
        });

        if (!$data) {
            abort(404);
        }

        return view('service-provider', compact('data'));
    }

    public function contractors()
    {

        $contractors = DB::table('users')
            ->where('role_name', 'Contractor')
            ->where('is_active', 1)
            ->select('id', 'business_name', 'name', 'phone', 'image', 'email', 'current_address', 'city', 'banner_image', 'description', 'achievments', 'total_project_done', 'working_since', 'team_strength')
            ->get();

        return view('web.contractors', compact('contractors'));
    }

    public function webSendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
            'referral' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid input']);
        }

        $phone = $request->phone;
        $referral = $request->referral;
        $otp = rand(1000, 9999);

        // Store temporarily in session
        Session::put('otp', $otp);
        Session::put('phone', $phone);
        Session::put('referral', $referral);

        // Optionally save OTP to user if they already exist
        $user = User::where('phone', $phone)->first();
        if ($user) {
            $user->temporary_token = $otp;
            $user->save();
        }

        \Log::info("OTP for $phone is $otp (Referral: $referral)");

        return response()->json(['status' => true, 'message' => 'OTP sent successfully']);
    }

    public function weblogin(Request $request)
    {
        $user = User::where('temporary_token', $request->otp)
            ->where('phone', $request->phone)
            ->first();

        if ($user && isset($request->referral)) {
            if (!empty($user->reffered_by)) {
                \Log::info('User already referred by someone', ['user' => $user]);
            }else{
                $referrer = User::where('referral_code', $request->referral)->first();

                if ($referrer) {
                 
                    $loyalty_point_exchange_rate =  BusinessSetting::where('type', 'loyalty_point_exchange_rate')->value('value');
                    $Signup_refer_point_receiver =  BusinessSetting::where('type', 'Signup_refer_point_receiver')->value('value');
                    $Signup_refer_point_sender =  BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value');
                    $refer_point_receiver = number_format($Signup_refer_point_receiver/$loyalty_point_exchange_rate,2);
                    $refer_point_sender = number_format($Signup_refer_point_sender/$loyalty_point_exchange_rate,2);
            
                    $user = User::where('phone', $request->phone)->first(); 
                    $wdata = new CustomerWalletHistory;
                    $wdata->customer_id = $user->id;
                    $wdata->transaction_amount = $refer_point_receiver;
                    $wdata->transaction_type = 1;
                    $wdata->transaction_method = "Ref. bonus";
                    $wdata->save();

                    $existingTransaction = WalletTransaction::where('user_id', $user->id)
                    ->where('reference', 'Referral Bonus from ' . $referrer->id)
                    ->exists();
                    if (!$existingTransaction) {
                       
                        $wallTransaction = new WalletTransaction;
                        $wallTransaction->user_id = $user->id;
                        $wallTransaction->balance = $refer_point_receiver;
                        $wallTransaction->reference = 'Referral Bonus from ' . $referrer->id;
                        $wallTransaction->save();                    
                        
                        $user->wallet_balance += $wdata->transaction_amount;
                        $user->reffered_by = $referrer->id; 
                        $user->save();
                    }
                    
                    $referrer->wallet_balance += $wdata->transaction_amount;
                    $referrer->save();
                }
            }
         }

        if ($user) {
            Auth::guard('web')->login($user);
            \Log::info('User found', ['user' => $user]);

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not found.',
        ]);
    }


    public function logout(Request $request)
    {
        auth()->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return redirect('/'); // or route('home')
    }

    public function sendOtpweb(Request $request)
    {


        $otp = rand(1111, 9999);
        if ($request->phone == "1234567890") {
            $otp = 1234;
        }
        $user = User::where('phone', $request->phone)
            ->first();

        if ($user != null) {
            $user->temporary_token  = $otp;
            $user->save();

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://2factor.in/API/R1/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91' . $request->phone . '&from=INTCHK&msg=Dear%20customer%2C%20' . $otp . '%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            return response()->json([
                'result' => true,
                'message' => "OTP Sent",
            ]);
        } else {
            //Disable below code to remove new signup restriction
            // return response()->json([
            //     'result' => false,
            //     'message' => "Thank you for your interest. We are launching soon...",
            // ]);

            //Enable below code for new signup

            $referrer_code =  Str::random(8);
            $user = new User();
            $user->phone = $request->phone;
            $user->temporary_token = $otp;
            $user->is_phone_verified = 1;
            $user->is_email_verified = 1;
            $user->email_verified_at = null;
            $user->referral_code = $referrer_code;
            $user->save();
            /* $request->merge(["phone"=> $request->phone]);
             $request->merge(["temporary_token"=> $otp]);
             $request->merge(["is_phone_verified"=> 1]);
             $request->merge(["is_email_verified"=> 1]);
             $request->merge(["email_verified_at"=> null]);
             $request->merge(["referral_code"=> $referrer_code]);
             DB::table('users')->insert($request->all());*/

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://2factor.in/API/R1/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91' . $request->phone . '&from=INTCHK&msg=Dear%20customer%2C%20' . $otp . '%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            return response()->json([
                'result' => true,
                'message' => "OTP Sent",
            ]);
        }
    }

    // public function web_suggestion(Request $request)
    // {
    //     $suggestions = [];

    //     $sug = $request->has('search') ? $request['search'] : null;

    //     if (!empty($sug)) {
    //         $suggestion = DB::table('tags')->where('tag', 'like', "%$sug%")
    //             ->limit(10)
    //             ->get(['id', 'tag']);
    //         $suggestions = $suggestion;
    //     }

    //     return response()->json(['suggestion' => $suggestion]);
    // }

    public function web_suggestion(Request $request)
    {
        $sug = $request->input('search');

        if (empty($sug)) {
            return response()->json(['suggestion' => []]);
        }

        // Prefix search instead of %term% for index usage
        $suggestions = DB::table('tags')
            ->select('id','tag')
            ->where('tag', 'like', "$sug%")
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['suggestion' => $suggestions]);
    }


    public function recently_view(Request $request)
    {

        $product_id = $request->input('product_id');
        $type = "products";


        $existingRecord = DB::table('recently_view')
            ->where('product_id', $product_id)->where('type', $type)->where('user_id', $user_id)
            ->first();

        if ($existingRecord) {

            DB::table('recently_view')
                ->where('product_id', $product_id)
                ->where('type', $type)
                ->update([
                    'counts' => $existingRecord->counts + 1,
                    'updated_at' => now()
                ]);
        } else {

            DB::table('recently_view')->insert([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'type' => $type,
                'counts' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['message' => 'insert successfully'], 200);
    }

    public function default_theme()
    {
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $home_categories = Category::where('home_status', true)->priority()->get();
        $home_categories->map(function ($data) {
            $id = '"' . $data['id'] . '"';
            $data['products'] = Product::active()
                ->where('category_ids', 'like', "%{$id}%")
                ->inRandomOrder()->take(12)->get();
        });
        //products based on top seller
        $top_sellers = Seller::approved()->with('shop')
            ->withCount(['orders'])->orderBy('orders_count', 'DESC')->take(12)->get();
        //end

        //feature products finding based on selling
        $featured_products = Product::with(['reviews'])->active()
            ->where('featured', 1)
            ->withCount(['order_details'])->orderBy('order_details_count', 'DESC')
            ->take(12)
            ->get();
        //end

        $latest_products = Product::with(['reviews'])->active()->orderBy('id', 'desc')->take(8)->get();
        $categories = Category::with('childes.childes')->where(['position' => 0])->priority()->take(11)->get();
        $brands = Brand::active()->take(15)->get();
        //best sell product
        $bestSellProduct = OrderDetail::with('product.reviews')
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(4)
            ->get();

        //Top-rated
        $topRated = Review::with('product')
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('AVG(rating) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(4)
            ->get();

        if ($bestSellProduct->count() == 0) {
            $bestSellProduct = $latest_products;
        }

        if ($topRated->count() == 0) {
            $topRated = $bestSellProduct;
        }

        $deal_of_the_day = DealOfTheDay::join('products', 'products.id', '=', 'deal_of_the_days.product_id')->select('deal_of_the_days.*', 'products.unit_price')->where('products.status', 1)->where('deal_of_the_days.status', 1)->first();
        $main_banner = Banner::where('banner_type', 'Main Banner')->where('published', 1)->latest()->get();
        $main_section_banner = \App\Model\Banner::where('banner_type', 'Main Section Banner')->where('published', 1)->orderBy('id', 'desc')->latest()->first();

        return view(
            VIEW_FILE_NAMES['home'],
            compact(
                'featured_products',
                'topRated',
                'bestSellProduct',
                'latest_products',
                'categories',
                'brands',
                'deal_of_the_day',
                'top_sellers',
                'home_categories',
                'brand_setting',
                'main_banner',
                'main_section_banner'
            )
        );
    }

    public function theme_aster()
    {
        $current_date = date('Y-m-d H:i:s');

        $home_categories = $this->category
            ->where('home_status', true)
            ->priority()->get();

        $home_categories->map(function ($data) {
            $current_date = date('Y-m-d H:i:s');
            $data['products'] = Product::active()
                ->with([
                    'flash_deal_product',
                    'wish_list' => function ($query) {
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list' => function ($query) {
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                    }
                ])
                ->where('category_id', $data['id'])
                ->inRandomOrder()->take(12)->get();

            //for flash deal
            $data['products']?->map(function ($product) use ($current_date) {
                $flash_deal_status = 0;
                if (count($product->flash_deal_product) > 0) {
                    $flash_deal = $product->flash_deal_product[0]->flash_deal;
                    if ($flash_deal) {
                        $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                        $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                        $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    }
                }
                $product['flash_deal_status'] = $flash_deal_status;
                return $product;
            });
        });

        //products based on top seller
        $top_sellers = $this->seller->approved()->with(['shop', 'coupon', 'product' => function ($query) {
            $query->where('added_by', 'seller')->active();
        }])
            ->whereHas('product', function ($query) {
                $query->where('added_by', 'seller')->active();
            })
            ->withCount(['product' => function ($query) {
                $query->active();
            }])
            ->withCount(['orders'])->orderBy('orders_count', 'DESC')->take(12)->get();

        $top_sellers->map(function ($seller) {
            $rating = 0;
            $count = 0;
            foreach ($seller->product as $item) {
                foreach ($item->reviews as $review) {
                    $rating += $review->rating;
                    $count++;
                }
            }
            $avg_rating = $rating / ($count == 0 ? 1 : $count);
            $rating_count = $count;
            $seller['average_rating'] = $avg_rating;
            $seller['rating_count'] = $rating_count;

            $product_count = $seller->product->count();
            $random_product = Arr::random($seller->product->toArray(), $product_count < 3 ? $product_count : 3);
            $seller['product'] = $random_product;
            return $seller;
        });
        //end

        $flash_deals = FlashDeal::with(['products' => function ($query) {
            $query->with(['product.wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            }, 'product.compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }])->whereHas('product', function ($q) {
                $q->active();
            });
        }])
            ->where(['deal_type' => 'flash_deal', 'status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        //find what you need
        $find_what_you_need_categories = $this->category->where('parent_id', 0)
            ->with(['childes' => function ($query) {
                $query->withCount(['sub_category_product' => function ($query) {
                    return $query->active();
                }]);
            }])
            ->withCount(['product' => function ($query) {
                return $query->active();
            }])
            ->get()->toArray();

        $get_categories = [];
        foreach ($find_what_you_need_categories as $category) {
            $slice = array_slice($category['childes'], 0, 4);
            $category['childes'] = $slice;
            $get_categories[] = $category;
        }

        $final_category = [];
        foreach ($get_categories as $category) {
            if (count($category['childes']) > 0) {
                $final_category[] = $category;
            }
        }
        $category_slider = array_chunk($final_category, 4);
        // end find  what you need

        // more stores
        $more_seller = $this->seller->approved()->with(['shop', 'product.reviews'])
            ->withCount(['product' => function ($query) {
                $query->active();
            }])
            ->inRandomOrder()
            ->take(7)->get();
        //end more stores

        //feature products finding based on selling
        $featured_products = $this->product->with([
            'seller.shop',
            'flash_deal_product.flash_deal',
            'wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])->active()
            ->where('featured', 1)
            ->withCount(['order_details'])->orderBy('order_details_count', 'DESC')
            ->take(10)
            ->get();

        $featured_products?->map(function ($product) use ($current_date) {
            $flash_deal_status = 0;
            $flash_deal_end_date = 0;
            if (count($product->flash_deal_product) > 0) {
                $flash_deal = $product->flash_deal_product[0]->flash_deal;
                if ($flash_deal) {
                    $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                    $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    $flash_deal_end_date = $flash_deal->end_date;
                }
            }
            $product['flash_deal_status'] = $flash_deal_status;
            $product['flash_deal_end_date'] = $flash_deal_end_date;
            return $product;
        });
        //end

        //latest product
        $latest_products = $this->product->with([
            'seller.shop',
            'flash_deal_product.flash_deal',
            'wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])
            ->active()->orderBy('id', 'desc')
            ->take(10)
            ->get();
        $latest_products?->map(function ($product) use ($current_date) {
            $flash_deal_status = 0;
            $flash_deal_end_date = 0;
            if (count($product->flash_deal_product) > 0) {
                $flash_deal = $product->flash_deal_product[0]->flash_deal;
                if ($flash_deal) {
                    $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                    $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    $flash_deal_end_date = $flash_deal->end_date;
                }
            }
            $product['flash_deal_status'] = $flash_deal_status;
            $product['flash_deal_end_date'] = $flash_deal_end_date;
            return $product;
        });
        //end latest product

        //featured deal product start
        $featured_deals = Product::active()
            ->with([
                'seller.shop',
                'flash_deal_product.feature_deal',
                'flash_deal_product.flash_deal' => function ($query) {
                    return $query->whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('end_date', '>=', date('Y-m-d'));
                },
                'wish_list' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
            ->whereHas('flash_deal_product.feature_deal', function ($query) {
                $query->whereDate('start_date', '<=', date('Y-m-d'))
                    ->whereDate('end_date', '>=', date('Y-m-d'));
            })
            ->get();

        if ($featured_deals) {
            foreach ($featured_deals as $product) {
                $flash_deal_status = 0;
                $flash_deal_end_date = 0;

                foreach ($product->flash_deal_product as $deal) {
                    $flash_deal_status = $deal->flash_deal ? 1 : $flash_deal_status;
                    $flash_deal_end_date = isset($deal->flash_deal->end_date) ? date('Y-m-d H:i:s', strtotime($deal->flash_deal->end_date)) : $flash_deal_end_date;
                }

                $product['flash_deal_status'] = $flash_deal_status;
                $product['flash_deal_end_date'] = $flash_deal_end_date;
            }
        }
        //featured deal product end

        //best sell product
        $bestSellProduct = $this->order_details->with([
            'product.reviews',
            'product.flash_deal_product.flash_deal',
            'product.seller.shop',
            'product.wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'product.compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(10)
            ->get();

        $bestSellProduct?->map(function ($order) use ($current_date) {
            if (!isset($order->product)) {
                return $order;
            }
            $flash_deal_status = 0;
            $flash_deal_end_date = 0;
            if (isset($order->product->flash_deal_product) && count($order->product->flash_deal_product) > 0) {
                $flash_deal = $order->product->flash_deal_product[0]->flash_deal;
                if ($flash_deal) {
                    $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                    $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    $flash_deal_end_date = $flash_deal->end_date;
                }
            }
            $order->product['flash_deal_status'] = $flash_deal_status;
            $order->product['flash_deal_end_date'] = $flash_deal_end_date;
            return $order;
        });

        // Just for you portion
        if (auth('customer')->check()) {
            $orders = $this->order->where(['customer_id' => auth('customer')->id()])->with(['details'])->get();

            if ($orders) {
                $orders = $orders?->map(function ($order) {
                    $order_details = $order->details->map(function ($detail) {
                        $product = json_decode($detail->product_details);
                        $category = json_decode($product->category_ids)[0]->id;
                        $detail['category_id'] = $category;
                        return $detail;
                    });
                    $order['id'] = $order_details[0]->id;
                    $order['category_id'] = $order_details[0]->category_id;

                    return $order;
                });

                $categories = [];
                foreach ($orders as $order) {
                    $categories[] = ($order['category_id']);;
                }
                $ids = array_unique($categories);


                $just_for_you = $this->product->with([
                    'wish_list' => function ($query) {
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list' => function ($query) {
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                    }
                ])->active()
                    ->where(function ($query) use ($ids) {
                        foreach ($ids as $id) {
                            $query->orWhere('category_ids', 'like', "%{$id}%");
                        }
                    })
                    ->inRandomOrder()
                    ->take(8)
                    ->get();
            } else {
                $just_for_you = $this->product->with([
                    'wish_list' => function ($query) {
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list' => function ($query) {
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                    }
                ])->active()->inRandomOrder()->take(8)->get();
            }
        } else {
            $just_for_you = $this->product->with([
                'wish_list' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->inRandomOrder()->take(8)->get();
        }
        // end just for you

        $topRated = $this->review->with([
            'product.seller.shop',
            'product.wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'product.compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('AVG(rating) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(10)
            ->get();

        if ($bestSellProduct->count() == 0) {
            $bestSellProduct = $latest_products;
        }

        if ($topRated->count() == 0) {
            $topRated = $bestSellProduct;
        }

        $deal_of_the_day = $this->deal_of_the_day->join('products', 'products.id', '=', 'deal_of_the_days.product_id')->select('deal_of_the_days.*', 'products.unit_price')->where('products.status', 1)->where('deal_of_the_days.status', 1)->first();
        $random_product = $this->product->active()->inRandomOrder()->first();

        $banner_list = ['Main Banner', 'Footer Banner', 'Sidebar Banner', 'Main Section Banner', 'Top Side Banner'];
        $banners = $this->banner->whereIn('banner_type', $banner_list)->where('published', 1)->orderBy('id', 'desc')->latest('created_at')->get();

        $main_banner = [];
        $footer_banner = [];
        $sidebar_banner = [];
        $main_section_banner = [];
        $top_side_banner = [];
        foreach ($banners as $banner) {
            if ($banner->banner_type == 'Main Banner') {
                $main_banner[] = $banner;
            } elseif ($banner->banner_type == 'Footer Banner') {
                $footer_banner[] = $banner->toArray();
            } elseif ($banner->banner_type == 'Sidebar Banner') {
                $sidebar_banner[] = $banner;
            } elseif ($banner->banner_type == 'Main Section Banner') {
                $main_section_banner[] = $banner;
            } elseif ($banner->banner_type == 'Top Side Banner') {
                $top_side_banner[] = $banner;
            }
        }
        $sidebar_banner = $sidebar_banner ? $sidebar_banner[0] : [];
        $main_section_banner = $main_section_banner ? $main_section_banner[0] : [];
        $top_side_banner = $top_side_banner ? $top_side_banner[0] : [];
        $footer_banner = $footer_banner ? array_slice($footer_banner, 0, 2) : [];

        $decimal_point = Helpers::get_business_settings('decimal_point_settings');
        $decimal_point_settings = !empty($decimal_point) ? $decimal_point : 0;
        $user = Helpers::get_customer();
        $categories = Category::with('childes.childes')->where(['position' => 0])->priority()->take(11)->get();

        //order again
        $order_again = $user != 'offline' ?
            $this->order->with('details.product')->where(['order_status' => 'delivered', 'customer_id' => $user->id])->latest()->take(8)->get()
            : [];

        $random_coupon = Coupon::with('seller')
            ->where(['status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->inRandomOrder()->take(3)->get();

        return view(
            VIEW_FILE_NAMES['home'],
            compact(
                'topRated',
                'bestSellProduct',
                'latest_products',
                'featured_products',
                'deal_of_the_day',
                'top_sellers',
                'home_categories',
                'main_banner',
                'footer_banner',
                'random_product',
                'decimal_point_settings',
                'just_for_you',
                'more_seller',
                'final_category',
                'category_slider',
                'order_again',
                'sidebar_banner',
                'main_section_banner',
                'random_coupon',
                'top_side_banner',
                'featured_deals',
                'flash_deals',
                'categories'
            )
        );
    }

    public function theme_fashion()
    {
        $current_date = date('Y-m-d H:i:s');

        $main_banner = Banner::where('banner_type', 'Main Banner')->where('published', 1)->latest()->get();
        $promo_banner = Banner::where('banner_type', 'Promo Banner')->where('published', 1)->take(5)->get();
        $mega_sell_banner = Banner::where('banner_type', 'Mega Sell Banner')->where('published', 1)->first();

        //products based on top seller
        $top_sellers = Seller::approved()->with(['shop', 'coupon', 'product' => function ($query) {
            $query->where('added_by', 'seller')->active();
        }])
            ->whereHas('product', function ($query) {
                $query->where('added_by', 'seller')->active();
            })
            ->withCount(['product' => function ($query) {
                $query->active();
            }])
            ->withCount(['orders'])->orderBy('orders_count', 'DESC')->take(12)->get();

        $top_sellers->map(function ($seller) {
            $product_ids = $seller->product->pluck('id');
            $rating = Review::whereIn('product_id', $product_ids);
            $avg_rating = $rating->avg('rating');
            $rating_count = $rating->count();
            $seller['average_rating'] = $avg_rating;
            $seller['rating_count'] = $rating_count;

            $product_count = $seller->product->count();
            $random_product = Arr::random($seller->product->toArray(), $product_count < 3 ? $product_count : 3);
            $seller['product'] = $random_product;
            return $seller;
        });
        //end products based on top seller

        // more stores
        $more_seller = Seller::approved()->with(['shop', 'product.reviews'])
            ->withCount(['product' => function ($query) {
                $query->active();
            }])
            ->inRandomOrder()
            ->take(7)->get();

        // new stores
        $new_seller = Seller::approved()->with(['shop', 'product.reviews'])
            ->withCount(['product' => function ($query) {
                $query->active();
            }])
            ->latest()
            ->take(7)->get();

        $more_seller = $more_seller->map(function ($seller) {
            $review_count = 0;
            $rating = [];
            foreach ($seller->product as $product) {
                $review_count += $product->reviews_count;
                foreach ($product->reviews as $reviews) {
                    $rating[] = $reviews['rating'];
                }
            }
            $seller['reviews_count'] = $review_count;
            $seller['rating'] = collect($rating)->average() ?? 0;
            return $seller;
        });

        $new_seller = $new_seller->map(function ($seller) {
            $review_count = 0;
            $rating = [];
            foreach ($seller->product as $product) {
                $review_count += $product->reviews_count;
                foreach ($product->reviews as $reviews) {
                    $rating[] = $reviews['rating'];
                }
            }
            $seller['reviews_count'] = $review_count;
            $seller['rating'] = collect($rating)->average() ?? 0;
            return $seller;
        });
        //end more stores

        //latest product
        $latest_products = $this->product->with(['reviews', 'flash_deal_product.flash_deal', 'wish_list' => function ($query) {
            return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
        }])
            ->active()->orderBy('id', 'desc')
            ->take(15)
            ->get();
        $latest_products?->map(function ($product) use ($current_date) {
            $flash_deal_status = 0;
            $flash_deal_end_date = 0;
            if (count($product->flash_deal_product) > 0) {
                $flash_deal = $product->flash_deal_product[0]->flash_deal;
                if ($flash_deal) {
                    $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                    $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    $flash_deal_end_date = $flash_deal->end_date;
                }
            }
            $product['flash_deal_status'] = $flash_deal_status;
            $product['flash_deal_end_date'] = $flash_deal_end_date;
            return $product;
        });
        //end latest product

        //best sell product
        $bestSellProduct = $this->order_details->with(['product.reviews', 'product.flash_deal_product.flash_deal', 'product.wish_list' => function ($query) {
            return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
        }])
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(10)
            ->get();

        $bestSellProduct?->map(function ($order) use ($current_date) {
            $flash_deal_status = 0;
            $flash_deal_end_date = 0;
            if (count($order->product->flash_deal_product) > 0) {
                $flash_deal = $order->product->flash_deal_product[0]->flash_deal;
                if ($flash_deal) {
                    $start_date = date('Y-m-d H:i:s', strtotime($flash_deal->start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($flash_deal->end_date));
                    $flash_deal_status = $flash_deal->status == 1 && (($current_date >= $start_date) && ($current_date <= $end_date)) ? 1 : 0;
                    $flash_deal_end_date = $flash_deal->end_date;
                }
            }
            $order->product['flash_deal_status'] = $flash_deal_status;
            $order->product['flash_deal_end_date'] = $flash_deal_end_date;
            return $order;
        });
        //end best sell product

        $deal_of_the_day = DealOfTheDay::join('products', 'products.id', '=', 'deal_of_the_days.product_id')->select('deal_of_the_days.*', 'products.unit_price')->where('products.status', 1)->where('deal_of_the_days.status', 1)->first();
        $random_product = \App\Model\Product::active()->inRandomOrder()->first();

        $main_banner = Banner::where('banner_type', 'Main Banner')->where('published', 1)->latest()->get();

        $footer_banner = Banner::where('banner_type', 'Footer Banner')->where('published', 1)->latest()->take(2)->get();
        $sidebar_banner = Banner::where('banner_type', 'Sidebar Banner')->where('published', 1)->latest()->first();
        $main_section_banner = \App\Model\Banner::where('banner_type', 'Main Section Banner')->where('published', 1)->orderBy('id', 'desc')->latest()->first();
        $top_side_banner = \App\Model\Banner::where('banner_type', 'Top Side Banner')->where('published', 1)->orderBy('id', 'desc')->latest()->first();

        $decimal_point_settings = !empty(\App\CPU\Helpers::get_business_settings('decimal_point_settings')) ? \App\CPU\Helpers::get_business_settings('decimal_point_settings') : 0;
        $user = Helpers::get_customer();

        // theme fashion -- Shop Again From Your Recent Store
        $recent_order_shops = $user != 'offline' ?
            $this->product->with('seller.orders', 'seller.shop')
            ->whereHas('seller.orders', function ($query) {
                $query->where(['customer_id' => auth('customer')->id(), 'seller_is' => 'seller']);
            })->active()
            ->inRandomOrder()->take(12)->get()
            : [];
        //end theme fashion -- Shop Again From Your Recent Store

        $most_searching_product = Product::with(['wish_list' => function ($query) {
            return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
        }])->withSum('tags', 'visit_count')->orderBy('tags_sum_visit_count', 'desc')->get();

        $category_ids = $most_searching_product->pluck('category_id')->unique();

        $categories = Category::withCount(['product' => function ($qc1) {
            $qc1->where(['status' => '1']);
        }])->with(['childes' => function ($qc2) {
            $qc2->with(['childes' => function ($qc3) {
                $qc3->withCount(['sub_sub_category_product'])->where('position', 2);
            }])->withCount(['sub_category_product'])->where('position', 1);
        }, 'childes.childes'])
            ->where('position', 0)
            ->get();

        $product_list_category = \App\CPU\CategoryManager::get_categories_with_counting();
        $colors_in_shop = \App\CPU\ProductManager::get_colors_form_products();

        $most_searching_product = $most_searching_product->take(10);

        $all_products_info = [
            'total_products' => $this->product->active()->count(),
            'total_orders' => $this->order->count(),
            'total_delivary' => $this->order_details->where('payment_status', 'paid')->count(),
            'total_reviews' => $this->review->count(),
        ];

        $most_demanded_product = OrderDetail::select('product_id', DB::raw('COUNT(*) as count'))
            ->with(['product' => function ($query) {
                $query->withCount('wish_list', 'order_details', 'order_delivered');
            }])
            ->whereYear('created_at', '=', date('Y'))
            ->groupBy('product_id')->orderBy('count', 'desc')
            ->first();

        $most_demanded_product = isset($most_demanded_product) ? $most_demanded_product->product : $most_demanded_product;

        // Feature products
        $featured_products = $this->product->active()->where('featured', 1)->take(4)->get();
        // dd($featured_products);
        return view(
            VIEW_FILE_NAMES['home'],
            compact(
                'bestSellProduct',
                'latest_products',
                'deal_of_the_day',
                'top_sellers',
                'main_banner',
                'footer_banner',
                'random_product',
                'decimal_point_settings',
                'more_seller',
                'new_seller',
                'sidebar_banner',
                'main_section_banner',
                'top_side_banner',
                'recent_order_shops',
                'categories',
                'colors_in_shop',
                'all_products_info',
                'most_searching_product',
                'most_demanded_product',
                'featured_products',
                'promo_banner',
                'mega_sell_banner'
            )
        );
    }

    public function theme_all_purpose()
    {

        $main_banner = Banner::where('banner_type', 'Main Banner')->where('published', 1)->latest()->get();
        $footer_banner = Banner::where('banner_type', 'Footer Banner')->where('published', 1)->latest()->take(2)->get();

        // Most Searching Categories Products
        $category_ids = Product::withSum('tags', 'visit_count')->orderBy('tags_sum_visit_count', 'desc')->pluck('category_id')->unique();
        $categories = Category::withCount(['product' => function ($qc1) {
            $qc1->where(['status' => '1']);
        }])->whereIn('id', $category_ids)->orderBy('product_count', 'desc')->take(18)->get();

        $brands = Brand::active()->take(15)->get();
        //best sell product
        $bestSellProduct = OrderDetail::with(['product.category', 'product.wish_list' => function ($query) {
            return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
        }, 'product.compare_list' => function ($query) {
            return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
        }, 'product.reviews'])
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(8)
            ->get();


        //featured deal product start
        $featured_deals = Product::with([
            'seller.shop',
            'category',
            'flash_deal_product.feature_deal',
            'flash_deal_product.flash_deal' => function ($query) {
                return $query->whereDate('start_date', '<=', date('Y-m-d'))
                    ->whereDate('end_date', '>=', date('Y-m-d'));
            },
            'wish_list' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compare_list' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])->whereHas('flash_deal_product.feature_deal', function ($query) {
            $query->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'));
        })->get();
        //featured deal product end

        // Just for you portion
        if (auth('customer')->check()) {
            $orders = $this->order->where(['customer_id' => auth('customer')->id()])->with(['details'])->get();

            if ($orders) {
                $orders = $orders?->map(function ($order) {
                    $order_details = $order->details->map(function ($detail) {
                        $product = json_decode($detail->product_details);
                        $category = json_decode($product->category_ids)[0]->id;
                        $detail['category_id'] = $category;
                        return $detail;
                    });
                    $order['id'] = $order_details[0]->id;
                    $order['category_id'] = $order_details[0]->category_id;

                    return $order;
                });

                $categories = [];
                foreach ($orders as $order) {
                    $categories[] = ($order['category_id']);;
                }
                $ids = array_unique($categories);


                $just_for_you = $this->product->with([
                    'reviews',
                    'rating',
                    'wish_list' => function ($query) {
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list' => function ($query) {
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                    }
                ])->active()->where(function ($query) use ($ids) {
                    foreach ($ids as $id) {
                        $query->orWhere('category_ids', 'like', "%{$id}%");
                    }
                })->inRandomOrder()->take(4)->get();
            } else {

                $just_for_you = $this->product->with([
                    'reviews',
                    'rating',
                    'wish_list' => function ($query) {
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list' => function ($query) {
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                    }
                ])->active()->inRandomOrder()->take(4)->get();
            }
        } else {

            $just_for_you = $this->product->with([
                'reviews',
                'rating',
                'wish_list' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->inRandomOrder()->take(4)->get();
        }
        // end just for you
        $latest_products_count = Product::active()->count();
        $latest_products = Product::with(['category'])->active()->orderBy('id', 'desc')->take(8)->get();


        return view(VIEW_FILE_NAMES['home'], compact(
            'main_banner',
            'footer_banner',
            'categories',
            'bestSellProduct',
            'featured_deals',
            'just_for_you',
            'latest_products_count',
            'latest_products'
        ));
    }

    public function submitBulkEnquiry(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'remark' => 'nullable|string|max:255',
        ]);

        // Check if user already added same product
        $existing = DB::table('bulk_purchase_list')
            ->where('user_id', $data['user_id'])
            ->where('product_name', $data['product_name'])
            ->first();

        if ($existing) {
            // Update existing record
            DB::table('bulk_purchase_list')
                ->where('id', $existing->id)
                ->update([
                    'quantity' => $data['quantity'],
                    'remark' => $data['remark'] ?? $existing->remark,
                    'updated_at' => now(),
                ]);
        } else {
            // Insert new record
            DB::table('bulk_purchase_list')->insert([
                'user_id' => $data['user_id'],
                'product_name' => $data['product_name'],
                'quantity' => $data['quantity'],
                'remark' => $data['remark'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Bulk purchase request submitted successfully.');
    }

    public function ajaxCategoryProducts($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->get();
        return response()->json([
            'status'  => true,
            'message' => "fetched",
            'slug' => $slug
        ]);
    }

    public function web_search(Request $request)
    {
        $search = trim($request->search);
        $slug = Str::slug($search);

        return response()->json([
            'slug' => $slug
        ]);
    }

    public function ratingstore(Request $request)
    {
            $request->validate([
                'provider_id' => 'required|integer',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:2000',
                'attachment' => 'nullable|image|max:2048'
            ]);

            $attachment = null;
            if($request->hasFile('attachment')){
                $attachment = $request->file('attachment')->store('provider-review', 'public');
            }

            ProviderReviews::create([
                'customer_id' => Auth::id(),
                'provider_id' => $request->provider_id,
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'attachment'  => $attachment,
                'status'      => 1,
            ]);

            return back()->with('success', 'Review submitted successfully!');
    }

    public function serviceregister()
    {
          try {
           $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://production.deepvue.tech/v1/ekyc/aadhaar/connect?consent=Y&purpose=For%20KYC',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: 98e61ce59f4944f3825fc01d16b74e59',
                'client-id: 9bb8fc8484',
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            
            // 6️⃣ Decode response
            $data = json_decode($response, true);
            // dd($data);
            $captcha = $data['data']['captcha'] ?? null;
            $session_id = $data['data']['session_id'] ?? null;

        } catch (\Exception $e) {
            $captcha = null;
            $session_id = null;
            // Optional: log the error
            \Log::error('Deepvue eKYC error: '.$e->getMessage());
        }
        return view('web.serviceregister',compact('captcha','session_id'));
    }
}