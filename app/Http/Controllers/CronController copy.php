<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\FirebaseServices\FirebaseNotificationService;


class CronController extends Controller
{
    public function __construct()
    {
        
    }

    public function homemessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString();

        // cache me pehle se bheje gaye tokens
        $sentTokens = cache()->get("homepage_notifications_{$today}", []);

        // ✅ Sirf un users ko pick karo jinka updated_at exactly 10–11 hours pehle hai
        $users = User::whereNotNull('cm_firebase_token')
            ->whereBetween('updated_at', [now()->subHours(11), now()->subHours(10)])
            ->whereNotIn('cm_firebase_token', $sentTokens)
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'HomePageVisitorsStatus' => 'no_users',
                'HomePageVisitorsCount' => 0,
                'HomePageVisitorsresponses' => []
            ]);
        }

        // ✅ Send notifications
        $result = $notificationService->sendNotificationToHomepageVisitors($users);

        // ✅ Update cache me naye sent tokens
        $newSentTokens = array_merge($sentTokens, $users->pluck('cm_firebase_token')->toArray());
        cache()->put("homepage_notifications_{$today}", $newSentTokens, now()->endOfDay());

        return response()->json($result);
    }

    // public function categoryMessage(FirebaseNotificationService $notificationService)
    // {
    //     $recentlyViewed = DB::table('recently_view as rv')
    //         ->join('users as u', 'u.id', '=', 'rv.user_id')
    //         ->join('categories as c', 'c.id', '=', 'rv.category_id')
    //         ->whereDate('rv.updated_at', today())
    //         ->whereNotNull('u.cm_firebase_token')
    //         ->select(
    //             'u.id as user_id',
    //             'u.cm_firebase_token',
    //             'rv.category_id',
    //             'c.name as category_name',
    //             'c.icon as icon',
    //             'rv.updated_at'
    //         )
    //         ->orderBy('rv.updated_at', 'asc')
    //         ->get();

    //     if ($recentlyViewed->isEmpty()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'No recent category visitors found'
    //         ]);
    //     }

    //     $grouped = $recentlyViewed->groupBy('user_id');

    //     $finalData = collect();

    //     foreach ($grouped as $userId => $items) {
    //         $earliestVisit = $items->first(); // sabse pehla record
    //         $finalData->push($earliestVisit);
    //     }

    //     $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

    //     return response()->json($results);
    // }

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

    // public function subCategoryMessage(FirebaseNotificationService $notificationService)
    // {
    //     $recentlyViewed = DB::table('recently_view as rv')
    //         ->join('users as u', 'u.id', '=', 'rv.user_id')
    //         ->join('categories as c', 'c.id', '=', 'rv.sub_category_id')
    //         ->whereDate('rv.updated_at', today())
    //         ->whereNotNull('u.cm_firebase_token')
    //         ->select(
    //             'u.id as user_id',
    //             'u.cm_firebase_token',
    //             'rv.sub_category_id as category_id',
    //             'c.name as category_name',
    //             'c.icon as icon',
    //             'rv.updated_at'
    //         )
    //         ->orderBy('rv.updated_at', 'asc')
    //         ->get();

    //     if ($recentlyViewed->isEmpty()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'No recent category visitors found'
    //         ]);
    //     }

    //     $grouped = $recentlyViewed->groupBy('user_id');

    //     $finalData = collect();

    //     foreach ($grouped as $userId => $items) {
    //         $earliestVisit = $items->first();
    //         $finalData->push($earliestVisit);
    //     }

    //     $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

    //     return response()->json($results);
    // }

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
        cache()->put("subcategory_notifications_{$today}", $newSentTokens, now()->endOfDay());

        return response()->json($results);
    }

    // public function subsubCategoryMessage(FirebaseNotificationService $notificationService)
    // {
    //     $recentlyViewed = DB::table('recently_view as rv')
    //         ->join('users as u', 'u.id', '=', 'rv.user_id')
    //         ->join('categories as c', 'c.id', '=', 'rv.sub_sub_category_id')
    //         ->whereDate('rv.updated_at', today())
    //         ->whereNotNull('u.cm_firebase_token')
    //         ->select(
    //             'u.id as user_id',
    //             'u.cm_firebase_token',
    //             'rv.sub_sub_category_id as category_id',
    //             'c.name as category_name',
    //             'c.icon as icon',
    //             'rv.updated_at'
    //         )
    //         ->orderBy('rv.updated_at', 'asc') // oldest first
    //         ->get();

    //     if ($recentlyViewed->isEmpty()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'No recent category visitors found'
    //         ]);
    //     }

    //     $grouped = $recentlyViewed->groupBy('user_id');

    //     $finalData = collect();

    //     foreach ($grouped as $userId => $items) {
    //         $earliestVisit = $items->first();
    //         $finalData->push($earliestVisit);
    //     }

    //     $results = $notificationService->sendNotificationToCategoryVisitors($finalData);

    //     return response()->json($results);
    // }

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
                'message' => 'No cart items found'
            ]);
        }

        // Har user ke liye notification bhejna
        foreach ($cart as $userCart) {
            $products = json_decode($userCart->products, true);

            $notificationService->sendNotificationToCartUsers([
                'token'    => $userCart->cm_firebase_token,
                'products' => $products
            ]);
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
            ->select(
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

        $results = $notificationService->sendNotificationToWishlistofCustomer($wishlist);
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
        $results = $notificationService->sendNotificationToCheckoutUsers($checkout);
        return response()->json($results);
            
    }

    public function productMessage(FirebaseNotificationService $notificationService)
    {
        $today = now()->toDateString(); // e.g. "2025-08-13"

        $products = DB::table('recently_view as rv')
            ->join('users as u', 'u.id', '=', 'rv.user_id')
            ->join('products as p', 'p.id', '=', 'rv.product_id')
            ->join('sku_product_new as sp', 'sp.product_id', '=', 'p.id')
            ->whereNotNull('u.cm_firebase_token')
            ->whereDate('rv.created_at', $today) // ✅ filter for same date
            ->select(
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

        $results = $notificationService->sendNotificationToProductViewUser($products);
        return response()->json($results);
    }

}