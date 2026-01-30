<?php

namespace App\Http\Controllers\Web;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $couponLimit = Order::where(['customer_id'=> auth('customer')->id(), 'coupon_code'=> $request['code']])
            ->groupBy('order_group_id')->get()->count();

        $coupon_f = Coupon::where(['code' => $request['code']])
            ->where('status',1)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))->first();

        if(!$coupon_f){
            return response()->json([
                'status' => 0,
                'messages' => ['0' => 'Invalid Coupon']
            ]);
        }
        if($coupon_f && $coupon_f->coupon_type == 'first_order'){
            $coupon = $coupon_f;
        }else{
            $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
        }

        if($coupon && $coupon->coupon_type == 'first_order'){
            $orders = Order::where(['customer_id'=> auth('customer')->id()])->count();
            if($orders>0){
                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => "Sorry this coupon is not valid for this user!"]
                ]);
            }
        }

        if ($coupon && (($coupon->coupon_type == 'first_order') || ($coupon->coupon_type == 'discount_on_purchase' && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())))) {
            $total = 0;
            foreach (CartManager::get_cart() as $cart) {
                if($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is=='admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')){
                    $product_subtotal = $cart['price'] * $cart['quantity'];
                    $total += $product_subtotal;
                }
            }
            if ($total >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] == 'percentage') {
                    $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                } else {
                    $discount = $coupon['discount'];
                }

                session()->put('coupon_code', $request['code']);
                session()->put('coupon_type', $coupon->coupon_type);
                session()->put('coupon_discount', $discount);
                session()->put('coupon_bearer', $coupon->coupon_bearer);
                session()->put('coupon_seller_id', $coupon->seller_id);

                return response()->json([
                    'status' => 1,
                    'discount' => Helpers::currency_converter($discount),
                    'total' => Helpers::currency_converter($total - $discount),
                    'messages' => ['0' => 'Coupon Applied Successfully!']
                ]);
            }
        }elseif($coupon && $coupon->coupon_type == 'free_delivery' && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())){
            $total = 0;
            $shipping_fee = 0;
            foreach (CartManager::get_cart() as $cart) {
                if($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is=='admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')) {
                    $product_subtotal = $cart['price'] * $cart['quantity'];
                    $total += $product_subtotal;
                    if (is_null($coupon->seller_id) || $coupon->seller_id == '0' || $coupon->seller_id == $cart->seller_id) {
                        $shipping_fee += $cart['shipping_cost'];
                    }
                }
            }

            if ($total >= $coupon['min_purchase']) {
                session()->put('coupon_code', $request['code']);
                session()->put('coupon_type', $coupon->coupon_type);
                session()->put('coupon_discount', $shipping_fee);
                session()->put('coupon_bearer', $coupon->coupon_bearer);
                session()->put('coupon_seller_id', $coupon->seller_id);

                return response()->json([
                    'status' => 1,
                    'discount' => Helpers::currency_converter($shipping_fee),
                    'total' => Helpers::currency_converter($total - $shipping_fee),
                    'messages' => ['0' => 'Coupon Applied Successfully!']
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'messages' => ['0' => 'Invalid Coupon']
        ]);
    }

    // public function applys(Request $request)
    // {
       
    //   // dd($request->user()->id);
    //     $couponLimit = Order::where(['customer_id'=> $request->user()->id, 'coupon_code'=> $request['code']])
    //         ->groupBy('order_group_id')->get()->count();

    //     $coupon_f = Coupon::where(['code' => $request['code']])
    //         ->where('status',1)
    //         ->whereDate('start_date', '<=', now())
    //         ->whereDate('expire_date', '>=', now())->first(); //date('Y-m-d')
    //     //dd($coupon_f);
    //     if(!$coupon_f){
          
    //         return response()->json(translate('invalid_coupon'), 202);
    //     }
    //     if($coupon_f && $coupon_f->coupon_type == 'first_order'){
    //         $coupon = $coupon_f;
    //     }else{
    //         $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
    //     }
         
    //     if($coupon && $coupon->coupon_type == 'first_order'){
    //         $orders = Order::where(['customer_id'=> $request->user()->id])->count();
    //         if($orders>0){
    //             return response()->json(translate('sorry_this_coupon_is_not_valid_for_this_user'), 202);
    //         }
    //     }
    //     // dd('hekki');
    //     if ($coupon && (($coupon->coupon_type == 'first_order') || ($coupon->coupon_type == 'discount_on_purchase' && ($coupon->customer_id == '0' || $coupon->customer_id == $request->user()->id)))) {
          
    //         $total = 0;
           
    //         foreach (CartManager::get_cart_for_api($request) as $cart) {
    //           //  dd($cart);
    //              $price = DB::table('sku_product_new')->where('id',$cart->variation)->first();
    //             // dd($price);
    //           //  $cart['discount'] = $cart['discount'] ?? 0;
    //             if((is_null($coupon->seller_id) && $cart->seller_is=='admin') || $coupon->seller_id == '0' || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')){
                     
    //                 // $product_subtotal = ($cart['price']-$cart['discount']) * $cart['quantity'];
    //                 $product_subtotal = ($price->listed_price*$cart->quantity);

    //                 $total += $product_subtotal;
    //             }
    //         }
         
    //         if ($total >= $coupon['min_purchase']) {
    //             dd($total);
    //             if ($coupon['discount_type'] === 'percentage') {
    //                 $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
    //             } else {
    //                 $discount = $coupon['discount'];
    //             }
                
    //             return response()->json([
    //                 'coupon_discount' => $discount,
    //                 'coupon_code'=> $request['code']
    //             ], 200);
    //         }
    //     }elseif($coupon && $coupon->coupon_type == 'free_delivery' && ($coupon->customer_id == '0' || $coupon->customer_id == $request->user()->id)){
    //         $total = 0;
    //         $shipping_fee = 0;
          
    //         $shippingMethod=Helpers::get_business_settings('shipping_method');
          
    //         $admin_shipping = \App\Model\ShippingType::where('seller_id', 0)->first();
          
    //         $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
           
    //         foreach (CartManager::get_cart_for_api($request) as $cart) {
    //             //dd($cart);

                
    //             $price = DB::table('sku_product_new')->where('id',$cart->variation)->first();

    //             if($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is=='admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')) {
    //                 $product_subtotal = ($price->listed_price * $cart->quantity);
    //                 $total += $product_subtotal;
                  
                   
    //                 // if (is_null($coupon->seller_id) || $coupon->seller_id == '0' || $coupon->seller_id == $cart->seller_id) {
    //                 //     $shipping_fee += $cart['shipping_cost'];
    //                 // }
                    
    //             }
    //             // if($shipping_type == 'order_wise' && ($coupon->seller_id=='0' || (is_null($coupon->seller_id) && $cart->seller_is=='admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller'))) {
    //             //     $shipping_fee += CartManager::get_shipping_cost($cart->cart_group_id);
    //             // }
    //         }
           
    //             if(isset($request['shipping_fee'])){
    //                $shipping_fee = round((float) $request['shipping_fee'], 2);
    //             }
          
    //         if ($total >= $coupon['min_purchase']) {
    //             return response()->json([
    //                 'coupon_discount' => $shipping_fee,
    //                 'coupon_code'=> $request['code']
    //             ], 200);
    //         }
    //     }

    //     return response()->json(translate('invalid_coupon'), 202);
    // }

    public function applys(Request $request)
{
    $userId = $request->user()->id;

    // Count how many times the coupon has been used by this customer
    $couponLimit = Order::where(['customer_id'=> $userId, 'coupon_code'=> $request['code']])
        ->groupBy('order_group_id')
        ->count();

    // Validate coupon existence
    $coupon_f = Coupon::where(['code' => $request['code']])
        ->where('status',1)
        ->whereDate('start_date', '<=', now())
        ->whereDate('expire_date', '>=', now())
        ->first();

    if (!$coupon_f) {
        return response()->json(translate('invalid_coupon'), 202);
    }

    // Apply limit check
    if ($coupon_f->coupon_type != 'first_order') {
        if ($coupon_f->limit <= $couponLimit) {
            return response()->json(translate('coupon_limit_exceeded'), 202);
        }
    }

    // First order check
    if ($coupon_f->coupon_type == 'first_order') {
        $orders = Order::where(['customer_id'=> $userId])->count();
        if ($orders > 0) {
            return response()->json(translate('sorry_this_coupon_is_not_valid_for_this_user'), 202);
        }
    }

    // ✅ Fetch checkout page carts directly from session
    $carts = collect(session()->get('cart', []))
        ->where('is_selected', 1)
        ->values();

    $total = 0;

    foreach ($carts as $cart) {
        // Validate seller match
        if (
            $coupon_f->seller_id == '0' ||
            (is_null($coupon_f->seller_id) && $cart->seller_is == 'admin') ||
            ($coupon_f->seller_id == $cart->seller_id && $cart->seller_is == 'seller')
        ) {
            $subtotal = ($cart->listed_price ?? 0) * $cart->cart_qty;
            $total += $subtotal;
        }
    }

    // Apply discount if purchase requirement met
    if ($total >= $coupon_f->min_purchase) {
        if ($coupon_f->coupon_type == 'discount_on_purchase' || $coupon_f->coupon_type == 'first_order') {
            if ($coupon_f->discount_type === 'percentage') {
                $discount = min(($total * $coupon_f->discount) / 100, $coupon_f->max_discount);
            } else {
                $discount = $coupon_f->discount;
            }

            return response()->json([
                'coupon_discount' => $discount,
                'coupon_code' => $coupon_f->code
            ], 200);
        } elseif ($coupon_f->coupon_type == 'free_delivery') {
            // Free delivery – refund shipping fee
            $shipping_fee = $request->has('shipping_fee') ? round((float)$request['shipping_fee'], 2) : 0;

            return response()->json([
                'coupon_discount' => $shipping_fee,
                'coupon_code' => $coupon_f->code
            ], 200);
        }
    }

    return response()->json(translate('invalid_coupon'), 202);
}

}