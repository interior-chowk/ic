<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function App\CPU\translate;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
            
    public function all_coupon(Request $request)
    {
        $coupon_f = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
            ->get();
        if($coupon_f){
            return response()->json([
                'coupon_discount' => $coupon_f
            ], 200);
        }else{
            return response()->json([
                'coupon_list' => 'record not found'
            ], 202); 
        } 
    }
              
    public function all_coupon_seeller(Request $request)
    {
        $request_seller_ids_string = $request->input('seller_id', ''); 
        $request_seller_ids_string = str_replace(['[', ']'], '', $request_seller_ids_string);
        $request_seller_ids = explode(',', $request_seller_ids_string);
    
        $coupon_discount = collect(); // Using collection for easier manipulation
        $admin_coupon_added = false; // Flag to track if admin coupons have been added
    
        // Add admin coupons only if they haven't been added yet
        if (!$admin_coupon_added) {
            $admin_coupon = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->where('added_by', 'admin')
                ->where('seller_id', 0)
                ->where('customer_id', 0)
                ->orWhere('seller_id', null)
                ->orWhere('customer_id', null)
                ->get();
    
            if ($admin_coupon->isNotEmpty()) {
                $coupon_discount = $coupon_discount->merge($admin_coupon); // Merge admin coupons
                $admin_coupon_added = true; // Update flag to indicate admin coupons have been added
            }
        }
        
        foreach ($request_seller_ids as $seller_id) {
            $seller_id = isset($seller_id) ? $seller_id : 0;
    
            $seller_coupon = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->where('seller_id', $seller_id)
                ->get();
    
            if ($seller_coupon->isNotEmpty()) {
                $coupon_discount = $coupon_discount->merge($seller_coupon); // Merge seller coupons
            }
        }
    
        // Add customer coupons only if they haven't been added yet
        $customer_coupon_added = false;
        if (!$customer_coupon_added) {
            $customer_coupon = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->where('customer_id', $request->user()->id)
                ->get();
    
            if ($customer_coupon->isNotEmpty()) {
                $coupon_discount = $coupon_discount->merge($customer_coupon); // Merge customer coupons
                $customer_coupon_added = true; // Update flag to indicate customer coupons have been added
            }
        }
    
        // Ensure uniqueness based on coupon ID (or any other unique field)
        $coupon_discount = $coupon_discount->unique('id'); // Make coupons unique by 'id'
    
        if ($coupon_discount->isNotEmpty()) {
            return response()->json([
                'coupon_discount' => $coupon_discount
            ], 200);
        } else {
            return response()->json([
                'coupon_list' => 'record not found'
            ], 202);
        }
    }
             
    public function all_coupon_seeller11(Request $request)
    {
        $request_seller_ids_string = $request->input('seller_id', ''); 

        $request_seller_ids_string = str_replace(['[', ']'], '', $request_seller_ids_string);
    
    
        $request_seller_ids = explode(',', $request_seller_ids_string);
    
        $coupon_discount = [];
        $admin_coupon_retrieved = false; 
        
        if (!$admin_coupon_retrieved) {
            
            $admin_coupon = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->where('added_by', 'admin')
                ->get();

            if ($admin_coupon->isNotEmpty()) {
                $coupon_discount[] = $admin_coupon;
                $admin_coupon_retrieved = true; 
            }
        }

        foreach ($request_seller_ids as $seller_id) {
        
            $seller_id = isset($seller_id) ? $seller_id : 0;
    
            $seller_coupon = Coupon::where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now())
                ->where('seller_id', $seller_id)
                ->get();
    
            if ($seller_coupon->isNotEmpty()) {
                $coupon_discount[] = $seller_coupon;
            }
        }

        if (!empty($coupon_discount)) {
            return response()->json([
                'coupon_discount' => $coupon_discount
            ], 200);
        } else {
            return response()->json([
                'coupon_list' => 'record not found'
            ], 202);
        }
    }

    public function all_coupon_all($seller_id)
    {
        if(isset($seller_id))
        {
            $seller_id = $seller_id;
        }else{
            $seller_id = 0;
        }

        $coupon_f = Coupon::where('status', 1)
        ->where(function ($query) use ($seller_id) {
            $query->whereDate('start_date', '<=', now())
                ->whereDate('expire_date', '>=', now());
        })
        ->where(function ($query) use ($seller_id) {
            $query->where('added_by', 'admin')
                ->orWhere('seller_id', $seller_id);
        })
        ->get();
        if($coupon_f){
                return response()->json([
                'coupon_discount' => $coupon_f
            ], 200);
        }else{
            return response()->json([
                'coupon_list' => 'record not found'
            ], 202); 
        }
    }
    
    public function apply(Request $request)
    {
        $couponLimit = Order::where(['customer_id'=> $request->user()->id, 'coupon_code'=> $request['code']])
            ->groupBy('order_group_id')->get()->count();

        $coupon_f = Coupon::where(['code' => $request['code']])
            ->where('status',1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('expire_date', '>=', now())->first(); //date('Y-m-d')

        if(!$coupon_f){
          
            return response()->json(translate('invalid_coupon'), 202);
        }
        if($coupon_f && $coupon_f->coupon_type == 'first_order'){
            $coupon = $coupon_f;
        }else{
            $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
        }
         
        if($coupon && $coupon->coupon_type == 'first_order'){
            $orders = Order::where(['customer_id'=> $request->user()->id])->count();
            if($orders>0){
                return response()->json(translate('sorry_this_coupon_is_not_valid_for_this_user'), 202);
            }
        }
      
        if ($coupon && (($coupon->coupon_type == 'first_order') || ($coupon->coupon_type == 'discount_on_purchase' && ($coupon->customer_id == '0' || $coupon->customer_id == $request->user()->id)))) {
          
            $total = 0;
           
            foreach (CartManager::get_cart_for_api($request) as $cart) {
              //  dd($cart);
                 $price = DB::table('sku_product_new')->where('id',$cart->variation)->first();
                // dd($price);
              //  $cart['discount'] = $cart['discount'] ?? 0;
                if((is_null($coupon->seller_id) && $cart->seller_is=='admin') || $coupon->seller_id == '0' || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')){
                     
                    // $product_subtotal = ($cart['price']-$cart['discount']) * $cart['quantity'];
                    $product_subtotal = ($price->listed_price*$cart->quantity);

                    $total += $product_subtotal;
                }
               // dd($total);
            }
         
            if ($total >= $coupon['min_purchase']) {

                if ($coupon['discount_type'] == 'percentage') {
                    $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                } else {
                    $discount = $coupon['discount'];
                }
                
                return response()->json([
                    'coupon_discount' => $discount
                ], 200);
            }
        }elseif($coupon && $coupon->coupon_type == 'free_delivery' && ($coupon->customer_id == '0' || $coupon->customer_id == $request->user()->id)){
            $total = 0;
            $shipping_fee = 0;
          
            $shippingMethod=Helpers::get_business_settings('shipping_method');
          
            $admin_shipping = \App\Model\ShippingType::where('seller_id', 0)->first();
          
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
           
            foreach (CartManager::get_cart_for_api($request) as $cart) {
                //dd($cart);
                
                $price = DB::table('sku_product_new')->where('id',$cart->variation)->first();

                if($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is=='admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is=='seller')) {
                    $product_subtotal = ($price->listed_price * $cart->quantity);
                    $total += $product_subtotal;
                    
                }
            }
           
                if(isset($request['shipping_fee'])){
                   $shipping_fee = round((float) $request['shipping_fee'], 2);
                }
          
            if ($total >= $coupon['min_purchase']) {
                return response()->json([
                    'coupon_discount' => $shipping_fee
                ], 200);
            }
        }

        return response()->json(translate('invalid_coupon'), 202);
    }
}