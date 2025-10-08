<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\FirebaseServices\FirebaseNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Model\ShipyaariCredential;



class CronController extends Controller
{
    public function __construct()
    {
        
    }

    // public function homemessage(FirebaseNotificationService $notificationService)
    // {
    //     $today = now()->toDateString();

    //     $sentTokens = cache()->get("homepage_notifications_{$today}", []);

    //     $users = User::whereNotNull('cm_firebase_token')
    //         ->whereBetween('updated_at', [now()->subHours(11), now()->subHours(10)])
    //         ->whereNotIn('cm_firebase_token', $sentTokens)
    //         ->get();

    //     if ($users->isEmpty()) {
    //         return response()->json([
    //             'HomePageVisitorsStatus' => 'no_users',
    //             'HomePageVisitorsCount' => 0,
    //             'HomePageVisitorsresponses' => []
    //         ]);
    //     }

    //     // ✅ Send notifications
    //     $result = $notificationService->sendNotificationToHomepageVisitors($users);

    //     // ✅ Update cache me naye sent tokens
    //     $newSentTokens = array_merge($sentTokens, $users->pluck('cm_firebase_token')->toArray());
    //     cache()->put("homepage_notifications_{$today}", $newSentTokens, now()->endOfDay());

    //     return response()->json($result);
    // }

    public function homemessage(FirebaseNotificationService $notificationService)
    {

        $users = User::whereNotNull('cm_firebase_token')
            ->get();

        $result = $notificationService->sendNotificationToHomepageVisitors($users);
        return response()->json($result);
    }

    public function categoryMessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString();

        $sentTokens = cache()->get("category_notifications_{$today}", []);

        $recentlyViewed = DB::table('recently_view as rv')
            ->join('users as u', 'u.id', '=', 'rv.user_id')
            ->join('categories as c', 'c.id', '=', 'rv.category_id')
            ->whereDate('rv.updated_at', $today)
            ->whereNotNull('u.cm_firebase_token')
            ->whereNotIn('u.cm_firebase_token', $sentTokens) 
            ->select(
                'u.id as user_id',
                'u.cm_firebase_token',
                'rv.category_id',
                'c.name as category_name',
                'c.icon as icon',
                'rv.updated_at'
            )
            ->orderBy('rv.updated_at', 'asc')
            ->get();

        if ($recentlyViewed->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No eligible category visitors found'
            ]);
        }

        $grouped = $recentlyViewed->groupBy('user_id');
        $finalData = collect();

        foreach ($grouped as $userId => $items) {
            $earliestVisit = $items->first();

            if (now()->diffInHours($earliestVisit->updated_at) >= 6) {
                $finalData->push($earliestVisit);
            }
        }

        if ($finalData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No users eligible after 6 hours'
            ]);
        }

        $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

        $newSentTokens = array_merge($sentTokens, $finalData->pluck('cm_firebase_token')->toArray());
        cache()->put("category_notifications_{$today}", $newSentTokens, now()->endOfDay());

        return response()->json($results);
    }

    public function subCategoryMessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString();

        $sentTokens = cache()->get("subcategory_notifications_{$today}", []);

        $recentlyViewed = DB::table('recently_view as rv')
            ->join('users as u', 'u.id', '=', 'rv.user_id')
            ->join('categories as c', 'c.id', '=', 'rv.sub_category_id')
            ->whereDate('rv.updated_at', $today)
            ->whereNotNull('u.cm_firebase_token')
            ->whereNotIn('u.cm_firebase_token', $sentTokens)
            ->select(
                'u.id as user_id',
                'u.cm_firebase_token',
                'rv.sub_category_id as category_id',
                'c.name as category_name',
                'c.icon as icon',
                'rv.updated_at'
            )
            ->orderBy('rv.updated_at', 'asc')
            ->get();

        if ($recentlyViewed->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No recent subcategory visitors found'
            ]);
        }

        $grouped = $recentlyViewed->groupBy('user_id');
        $finalData = collect();

        foreach ($grouped as $userId => $items) {
            $earliestVisit = $items->first();

            if (now()->diffInHours($earliestVisit->updated_at) >= 5) {
                $finalData->push($earliestVisit);
            }
        }

        if ($finalData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No users eligible after 6 hours'
            ]);
        }

        $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

        $newSentTokens = array_merge($sentTokens, $finalData->pluck('cm_firebase_token')->toArray());
        cache()->put("subcategory_notifications_{$today}", $newSentTokens, now()->endOfDay());

        return response()->json($results);
    }

    public function subsubCategoryMessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString();

        $sentTokens = cache()->get("subsubcategory_notifications_{$today}", []);

        $recentlyViewed = DB::table('recently_view as rv')
            ->join('users as u', 'u.id', '=', 'rv.user_id')
            ->join('categories as c', 'c.id', '=', 'rv.sub_sub_category_id')
            ->whereDate('rv.updated_at', $today)
            ->whereNotNull('u.cm_firebase_token')
            ->whereNotIn('u.cm_firebase_token', $sentTokens) // avoid duplicates
            ->select(
                'u.id as user_id',
                'u.cm_firebase_token',
                'rv.sub_sub_category_id as category_id',
                'c.name as category_name',
                'c.icon as icon',
                'rv.updated_at'
            )
            ->orderBy('rv.updated_at', 'asc') // earliest first
            ->get();

        if ($recentlyViewed->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No recent sub-sub-category visitors found'
            ]);
        }

        $grouped = $recentlyViewed->groupBy('user_id');
        $finalData = collect();

        foreach ($grouped as $userId => $items) {
            $earliestVisit = $items->first();

            if (now()->diffInHours($earliestVisit->updated_at) >= 4) {
                $finalData->push($earliestVisit);
            }
        }

        if ($finalData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No users eligible after 6 hours'
            ]);
        }

        $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

        $newSentTokens = array_merge($sentTokens, $finalData->pluck('cm_firebase_token')->toArray());
        cache()->put("subsubcategory_notifications_{$today}", $newSentTokens, now()->endOfDay());

        return response()->json($results);
    }

    public function cartMessage(FirebaseNotificationService $notificationService)
    {
        $cart = DB::table('new_cart as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('products as p', 'p.id', '=', 'c.product_id')
            ->leftJoin('sku_product_new as sp', 'sp.product_id', '=', 'c.product_id')
            ->whereNotNull('u.cm_firebase_token')
            ->where('c.updated_at', '<=', now()->subHour())
            ->select(
                'c.user_id',
                'u.cm_firebase_token',
                DB::raw("JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'product_id', c.product_id,
                        'product_name', p.name,
                        'image', COALESCE(JSON_UNQUOTE(JSON_EXTRACT(sp.image, '$[0]')), p.thumbnail),
                        'thumbnail', p.thumbnail,
                        'color', c.colors,
                        'quantity', c.quantity,
                        'updated_at', c.updated_at
                    )
                ) as products")
            )
            ->groupBy('c.user_id', 'u.cm_firebase_token')
            ->get();

        if ($cart->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No eligible cart items found'
            ]);
        }

        foreach ($cart as $userCart) {
            $cacheKey = "cart_notify_user_{$userCart->user_id}";

            if (Cache::has($cacheKey)) {
                continue;
            }

            $products = json_decode($userCart->products, true);

            $notificationService->sendNotificationToCartUsers([
                'token'    => $userCart->cm_firebase_token,
                'products' => $products
            ]);

            Cache::put($cacheKey, true, now()->addDay());
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifications sent successfully'
        ]);
    }

    public function wishlistMessage(FirebaseNotificationService $notificationService)
    {
        $wishlist = DB::table('wishlists as w')
            ->join('users as u', 'u.id', '=', 'w.customer_id')
            ->join('products as p', 'p.id', '=', 'w.product_id')
            ->join('sku_product_new as sp', 'sp.product_id', '=', 'p.id')
            ->whereNotNull('u.cm_firebase_token')
            ->where('w.updated_at', '<=', now()->subHours(2))
            ->select(
                'w.customer_id',
                'u.cm_firebase_token',
                'p.name as product_name',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sp.image, '$[0]')) as image"),
                'p.thumbnail as product_thumbnail',
                'w.updated_at'
            )
            ->get();

        if ($wishlist->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No wishlist items found'
            ]);
        }

        $toNotify = [];

        foreach ($wishlist as $item) {
            $cacheKey = 'wishlist_notification_sent_' . $item->customer_id . '_' . now()->format('Y-m-d');

            if (cache()->has($cacheKey)) {
                continue;
            }

            cache()->put($cacheKey, true, now()->endOfDay());

            $toNotify[] = $item;
        }

        if (empty($toNotify)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'All users already notified today'
            ]);
        }

        $results = $notificationService->sendNotificationToWishlistofCustomer(collect($toNotify));

        return response()->json($results);
    }

    public function checkoutMessage(FirebaseNotificationService $notificationService)
    {
        $checkout = DB::table('new_cart as nc')
            ->join('users as u', 'u.id', '=', 'nc.user_id')
            ->join('products as p', 'p.id', '=', 'nc.product_id')
            ->join('sku_product_new as sp', 'sp.product_id', '=', 'p.id')
            ->whereNotNull('u.cm_firebase_token')
            ->select(
                'nc.user_id',
                'u.cm_firebase_token',
                'p.name as product_name',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sp.image, '$[0]')) as image"),
                'p.thumbnail as product_thumbnail',
                'nc.updated_at'
            )
            ->get();

        if ($checkout->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No checkout items found'
            ]);
        }

        $toNotify = [];

        foreach ($checkout as $item) {
            $hoursDiff = now()->diffInHours($item->updated_at);

            if (!in_array($hoursDiff, [10, 20])) {
                continue;
            }

            $cacheKey = 'checkout_notification_count_' . $item->user_id . '_' . now()->format('Y-m-d');
            $count = cache()->get($cacheKey, 0);

            if ($count >= 2) {
                continue;
            }

            cache()->put($cacheKey, $count + 1, now()->endOfDay());

            $toNotify[] = $item;
        }

        if (empty($toNotify)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No eligible users to notify right now'
            ]);
        }

        $results = $notificationService->sendNotificationToCheckoutUsers(collect($toNotify));

        return response()->json($results);
    }

    public function productMessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString();

        $products = DB::table('recently_view as rv')
            ->join('users as u', 'u.id', '=', 'rv.user_id')
            ->join('products as p', 'p.id', '=', 'rv.product_id')
            ->join('sku_product_new as sp', 'sp.product_id', '=', 'p.id')
            ->whereNotNull('u.cm_firebase_token')
            ->whereDate('rv.created_at', $today)
            ->select(
                'rv.user_id',
                'u.cm_firebase_token',
                'p.name as product_name',
                'p.slug as product_slug',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sp.image, '$[0]')) as image"),
                'rv.updated_at'
            )
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No products found for today'
            ]);
        }

        $toNotify = [];

        foreach ($products as $item) {
            $minutesDiff = now()->diffInMinutes($item->updated_at);

            if ($minutesDiff < 45) {
                continue;
            }

            $cacheKey = 'product_notification_sent_' . $item->user_id . '_' . now()->format('Y-m-d');
            if (cache()->has($cacheKey)) {
                continue;
            }

            cache()->put($cacheKey, true, now()->endOfDay());

            $toNotify[] = $item;
        }

        if (empty($toNotify)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No eligible users to notify right now'
            ]);
        }

        $results = $notificationService->sendNotificationToProductViewUser(collect($toNotify));

        return response()->json($results);
    }


    public function signInAndSaveShipyaari()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://api-seller.shipyaari.com/api/v1/seller/signIn', [
            'email' => 'info@interiorchowk.com',
            'password' => 'IC@Shipyari1'
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Shipyaari login failed',
                'error' => $response->body()
            ], 500);
        }

        $json = $response->json();
        $data = $json['data'][0] ?? null;

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No seller data returned from Shipyaari'
            ], 500);
        }

        ShipyaariCredential::updateOrCreate(
            ['seller_id' => $data['sellerId']],
            [
                'name' => $data['name'] ?? '',
                'email' => $data['email'] ?? '',
                'contact_number' => $data['contactNumber'] ?? null,
                'token' => $data['token'] ?? null,
                'jwt' => $data['jwt'] ?? null,
                'company_uuid' => $data['companyId'] ?? null,
                'private_company_id' => $data['privateCompanyId'] ?? null,
                'brand_name' => $data['privateCompany']['brandName'] ?? null,
                'business_type' => $data['businessType'] ?? null,
                'gst_number' => $data['kycDetails']['gstNumber'] ?? null,
                'gst_verified' => $data['kycDetails']['gstVerified'] ?? false,
                'pan_number' => $data['kycDetails']['panNumber'] ?? null,
                'pan_verified' => $data['kycDetails']['panVerified'] ?? false,
                'aadhar_number' => $data['kycDetails']['aadharNumber'] ?? null,
                'aadhar_verified' => $data['kycDetails']['aadharVerified'] ?? false,
                'full_address' => $data['kycDetails']['fullAddress'] ?? null,
                'is_kyc_done' => $data['kycDetails']['isKYCDone'] ?? false,
                'is_wallet_recharge' => $data['isWalletRechage'] ?? false,
                'is_returning_user' => $data['isReturningUser'] ?? false,
                'is_migrated' => $data['isMigrated'] ?? false,
                'is_masked_user' => $data['isMaskedUser'] ?? false,
                'is_wallet_blacklisted' => $data['isWalletBlackListed'] ?? false,
                'next_step' => $data['nextStep'] ?? [],
                'private_company' => $data['privateCompany'] ?? [],
                'kyc_details' => $data['kycDetails'] ?? [],
                'raw_response' => $json
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Shipyaari credentials saved successfully'
        ]);
    }

}