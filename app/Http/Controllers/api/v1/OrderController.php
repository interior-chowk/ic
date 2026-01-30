<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Cart;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Seller;
use App\Model\WithdrawRequest;
use App\Model\ShippingAddress;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\RefundRequest;
use App\CPU\ImageManager;
use App\Model\DeliveryMan;
use App\CPU\CustomerManager;
use App\CPU\SmsModule;
use App\User;
use App\Services\ShiprocketService;
use App\Model\ShiprocketCourier;
use Illuminate\Support\Facades\DB;
use App\Services\ShipyaariService;

class OrderController extends Controller
{
    use CommonTrait;
    public function track_shiprocket_status($order_id , Request $request ,ShiprocketService $shiprocketService)
    {
        $shipping = ShiprocketCourier::where("order_id", $order_id)->first();
         $status = $shiprocketService->getAwbStatus($shipping->awb_code);
        
        if($status !== false) {
         return response()->json($status, 200);
                   
        }

        return response()->json(['message' => 'Tracking data not found'], 200);
    }
    
    public function track_mannual_order($order_id , Request $request)
    {
       //dd($order_id);
        $shiprocketService =  new ShipyaariService();
       $shipyari =  ShiprocketCourier::where('order_id',$order_id)->value('awb_code');
       if($shipyari){
           $response = $shiprocketService->trackOrder($shipyari);
            $status = $response->getData(true)['currentStatus'];
       }
      // dd($status);
       $orderTrack = [];
        $shipping_status = Order::where("id", $order_id)->first();
        if($status == "BOOKED" || $status == "NOT PICKED" ){
            $orderTrack[] = [
                           'eventname' => 'Order Placed',
                           'isdone' => 1,
                            'time' => $shipping_status->updated_at
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Order Dispatched',
                           'isdone' => 0,
                            'time' => ''
                          ];
          
            $orderTrack[] = [
                           'eventname' => 'Out for Delivery',
                           'isdone' => 0,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Delivered',
                           'isdone' => 0,
                            'time' => ''
                          ];
          
        }
        
        if($status == 'IN TRANSIT'){
            
            $orderTrack[] = [
                           'eventname' => 'Order Placed',
                           'isdone' => 1,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Order Dispatched',
                           'isdone' => 1,
                            'time' => $shipping_status->updated_at
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Out for Delivery',
                           'isdone' => 0,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Delivered',
                           'isdone' => 0,
                            'time' => ''
                          ];             
                             
        }
        
        if($status == 'out_for_delivery'){
            $orderTrack[] = [
                           'eventname' => 'Order Placed',
                           'isdone' => 1,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Order Dispatched',
                           'isdone' => 1,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Out for Delivery',
                           'isdone' => 1,
                            'time' => $shipping_status->updated_at
                          ];
             
            $orderTrack[] = [
                           'eventname' => 'Delivered',
                           'isdone' => 0,
                            'time' => ''
                          ];                
        }
        
        if($status == "DELIVERED"){
            
            $orderTrack[] = [
                           'eventname' => 'Order Placed',
                           'isdone' => 1,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Order Dispatched',
                           'isdone' => 1,
                            'time' => ''
                          ];
                          
            $orderTrack[] = [
                           'eventname' => 'Out for Delivery',
                           'isdone' => 1,
                            'time' => ''
                          ];
            
            $orderTrack[] = [
                           'eventname' => 'Delivered',
                           'isdone' => 1,
                            'time' => $shipping_status->updated_at
                          ];
        }
        
        return response()->json($orderTrack, 200);
    }
      
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderManager::track_order($request['order_id']), 200);
    }
    
    public function track_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(ShiprocketCourier::where('order_id',$request['order_id'])->get(), 200);
    }
    
    public function gst_orderid_details(Request $request)
    {
       $data = OrderDetail:: join('shops', function ($join) {
            $join->on('shops.seller_id', '=', 'order_details.seller_id');
        })->select('order_details.order_id as order_id','shops.name as company_name','shops.gst_no as gst_number')->orderByDesc('order_details.id')->get();
         
        if($data){
             return response()->json($data, 200);
        }else{
             return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        
    }
    
    public function order_cancel(Request $request, ShipyaariService $shiprocketService)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'cancel_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::where(['id' => $request->order_id])->first();
        //dd($order->payment_method);
        //&& $order['order_status'] == 'pending'
    
        if ($order->payment_method == 'cash_on_delivery') {
            //dd($request->order_id);
             OrderManager::stock_update_on_order_status_changes($order_detail, 'canceled');
            Order::where(['id' => $request->order_id])->update([
                'order_status' => 'canceled'
            ]);
            OrderDetail::where(['order_id' => $request->order_id])->update([
                'cancellation_reason' => $request->cancel_reason,
                'cancellation_remarks' => $request->cancel_remark
            ]);
            
            try {
               
                //cancel order for shiprocket
                $shiprocketService->cancelOrder($order);
            }catch(\Exception $e) {
                
            }
        
            return response()->json(translate('order_canceled_successfully'), 200);
        }else{
            
            OrderManager::stock_update_on_order_status_changes($order, 'canceled');
            Order::where(['id' => $request->order_id])->update([
                'order_status' => 'canceled'
            ]);
            OrderDetail::where(['order_id' => $request->order_id])->update([
                'cancellation_reason' => $request->cancel_reason,
                'cancellation_remarks' => $request->cancel_remark
            ]);
            
            try {
                //cancel order for shiprocket
                $shiprocketService->cancelOrder($order);
            }catch(\Exception $e) {
                
            }
        
            return response()->json(translate('order_canceled_successfully'), 200);
            
        }

        return response()->json(translate('status_not_changable_now'), 302);
    }

    public function order_return(Request $request)
    {
                 
        $order_details = OrderDetail::where('order_id',$request->order_id)->first();
    
        $user = auth('api')->id();
        //dd($order_details,$user);
        $refund_request = new RefundRequest;

        $refund_request->order_details_id = $order_details->id;
        $refund_request->customer_id = $user;
        $refund_request->status = 'pending';
        $refund_request->requested_for = $request->status;
        $refund_request->amount = $order_details->price;
        $refund_request->product_id = $order_details->product_id;
        $refund_request->order_id = $order_details->order_id;
        $refund_request->refund_reason = $request->reason;
        $refund_request->refund_remarks = $request->remarks;
               
        if ($request->file('images')) {
            foreach ($request->file('images') as $img) {
                $product_images[] = ImageManager::upload('refund/', 'png', $img);
            }
            $refund_request->images = json_encode($product_images);
        }
            
        $refund_request->save();

        $order_details->refund_request = 1;
        $order_details->save();
        //dd($order_details);
        return response()->json(['message'=> 'successfully',
        'status'=> true], 200);
    }
    
    public function place_order(Request $request, ShipyaariService $shiprocketService)
    {
        try {
            set_time_limit(300);
            ini_set('max_execution_time', 300);

            $cart_group_ids = CartManager::get_cart_group_ids($request);

            if (empty($cart_group_ids)) {
                return response()->json(['message' => 'No cart group found.'], 400);
            }

            $carts = DB::table('new_cart')->whereIn('cart_group_id', $cart_group_ids)->get();

            if ($carts->isEmpty()) {
                return response()->json(['message' => 'Cart is empty.'], 400);
            }

            // Check if any physical product exists
            $physical_product = $carts->contains(fn($cart) => $cart->product_type === 'physical');

            if ($physical_product) {
                $zip_restrict = Helpers::get_business_settings('delivery_zip_code_area_restriction');
                $country_restrict = Helpers::get_business_settings('delivery_country_restriction');

                $billing_address_id = $request->input('billing_address_id');
                $shipping_address = ShippingAddress::where([
                    'customer_id' => $request->user()->id,
                    'id' => $billing_address_id
                ])->first();

                if (!$shipping_address) {
                    return response()->json(['message' => translate('address_not_found')], 404);
                }

                if ($country_restrict && !self::delivery_country_exist_check($shipping_address->country)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);
                }

                if ($zip_restrict && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                }
            }

            $unique_id = $request->user()->id . '-' . rand(100000, 999999) . '-' . time();
            $order_ids = [];

            foreach ($cart_group_ids as $group_id) {
                $data = [
                    'payment_method' => $request->iscod ? 'cash_on_delivery' : 'online_payment',
                    'order_status' => 'confirmed',
                    'payment_status' => $request->iscod ? 'unpaid' : 'paid',
                    'transaction_ref' => $request->transaction_id ?? '',
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id,
                    'request' => $request,
                ];

                $order_id = OrderManager::generate_order($data);
                //dd($order_id);
                $order = Order::find($order_id);

                if (!$order) {
                    return response()->json(['message' => "Failed to create order for cart group $group_id"], 500);
                }

                // Update billing and note
                if ($request->has('billing_address_id')) {
                    $order->billing_address = $request->billing_address_id;
                    $order->billing_address_data = ShippingAddress::find($request->billing_address_id);
                }
                if ($request->filled('order_note')) {
                    $order->order_note = $request->order_note;
                }
                $order->save();

                try {
                    // Calculate weight
                    $details = $order->details()->with('product:id,weight')->get()->toArray();
                    $warehouse = DB::table('warehouse')->where('seller_id',$order->details()->first()->seller_id)->first();
                // dd($warehouse->pincode);
                // dd($order->details()->first()->seller_id);
                    $total_weight = array_reduce($details, function ($carry, $item) {
                        $sku = DB::table('sku_product_new')
                            ->where('product_id', $item['product_id'])
                            ->where('variation', $item['variant'])
                            ->first();

                        if (!$sku) {
                            throw new \Exception("SKU not found for product ID {$item['product_id']} and variation {$item['variant']}");
                        }

                        return $carry + ($sku->weight * $item['qty']);
                    }, 0);

                    // Check shipping cost
                    $shippingPrice = $shiprocketService->checkForAvailability([
                        'pickupPincode'  => (int) $warehouse->pincode,
                        'deliveryPincode' => (int) json_decode($order->shipping_address_data)->zip ?? '',
                        'weight' => $total_weight,
                        'paymentMode' => $order->payment_method === 'cash_on_delivery' ? 'COD' : 'PREPAID',
                        'orderType' => 'B2C',
                        'invoiceValue'=> 10,
                        'dimension' => [
                                        'length' => 10,
                                        'width'  => 10,
                                        'height' => 10
                            ]
                    ]);
        // dd($shippingPrice['data']);
                    if (!empty($shippingPrice['data'])) {
                        $order->shipping_cost = $request->shipping_fee == '0.00' ? 0 : $order->shipping_cost;
                        $order->save();
                
                        if ($request->instant_delivery == '0') {
                    // dd('hello');
                            $shiprocketService->createOrder($order);
                        }
                    }

                    $order_ids[] = $order_id;
                } catch (\Exception $innerEx) {
                    return response()->json([
                        'message' => 'Shiprocket error',
                        'error' => $innerEx->getMessage()
                    ], 500);
                }
            }

        //  CartManager::cart_clean($request);

            return response()->json([
                'order_ids' => $order_ids,
                'message' => translate('order_placed_successfully')
            ], 200);

        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Order processing failed',
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString() // remove in production
            ], 500);
        }
    }

    public function place_order_online(Request $request, ShiprocketService $shiprocketService)
    {
        $cart_group_ids = CartManager::get_cart_group_ids($request);
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $physical_product = false;
        foreach($carts as $cart){
            if($cart->product_type == 'physical'){
                $physical_product = true;
            }
        }

        if($physical_product) {
            $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
            $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

            if ($request->has('billing_address_id')) {
                $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->input('billing_address_id')])->first();

                if (!$shipping_address) {
                    return response()->json(['message' => translate('address_not_found')], 200);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping_address->country)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

                } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                }
            }
        }

        $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
        $order_ids = [];
        foreach ($cart_group_ids as $group_id) {
            $data = [
                'payment_method' => 'online_payment',
                'order_status' => 'confirmed',
                'payment_status' => 'paid',
                'transaction_ref' => $request->transaction_id,
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id,
                'request' => $request,
            ];
            
            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ?  ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();
            
            try {
                //shiprocket actions
                $weight = array_reduce($order->details()->with('product:weight,id')->get()->toArray(), function($prev, $c){
                    return $prev + ($c['product']['weight'] * $c['qty']);    
                }, 0);
                
                $shippingPrice = $shiprocketService->checkForAvailability([
                    'pickup_postcode'  => $order->seller->shop->pincode,
                    'delivery_postcode'  => json_decode($order->shipping_address_data)->zip ?? '',
                    'weight'  => $weight,
                    'cod'  => $order->payment_method == 'cash_on_delivery' ? 1 : 0,
                    'mode'  => 'Surface',
                ]);
            
                if($shippingPrice['data'] !== null) {
                    //$cost = $shippingPrice['data']->rate;
                    $cost = $order->shipping_cost;
                    
                    $order->shipping_cost = $cost;
                    $order->save();
                    if($request['instant_delivery'] == '0') 
                    {
                    $shiprocketService->createOrder($order);
                    }
                }
            }catch(\Exception $e) {
                // return response()->json($e->getMessage());
            }
            
            array_push($order_ids, $order_id);
        }

        CartManager::cart_clean($request);

        return response()->json(['order_id' => $order_id, 'message' => translate('order_placed_successfully')], 200);
    }

    public function place_order_by_online_payment(Request $request)
    {
        $cart_group_ids = CartManager::get_cart_group_ids($request);
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $physical_product = false;
        foreach($carts as $cart){
            if($cart->product_type == 'physical'){
                $physical_product = true;
            }
        }

        if($physical_product) {
            $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
            $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

            if ($request->has('billing_address_id')) {
                $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->input('billing_address_id')])->first();

                if (!$shipping_address) {
                    return response()->json(['message' => translate('address_not_found')], 200);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping_address->country)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

                } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                }
            }
        }

        $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
        $order_ids = [];
        foreach ($cart_group_ids as $group_id) {
            $data = [
                'payment_method' => 'online_payment',
                'order_status' => 'confirmed',
                'payment_status' => 'paid',
                'transaction_ref' => $request->transaction_ref,
                'payment_by' => $request->payment_by,
                'payment_note' => $request->payment_note,
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id,
                'request' => $request,
            ];
            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ?  ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();
            
            try {
                //shiprocket actions
                $weight = array_reduce($order->details()->with('product:weight,id')->get()->toArray(), function($prev, $c){
                    return $prev + $c['product']['weight'];    
                }, 0);
                
                $shippingPrice = $shiprocketService->checkForAvailability([
                    'pickup_postcode'  => $order->seller->shop->pincode,
                    'delivery_postcode'  => json_decode($order->shipping_address_data)->zip ?? '',
                    'weight'  => $weight,
                    'cod'  => 1,
                    'mode'  => 'Surface',
                ]);
            
                if($shippingPrice['data'] !== null) {
                    //$cost = $shippingPrice['data']->rate;
                    $cost = $order->shipping_cost;
                    
                    $order->shipping_cost = $cost;
                    $order->save();
                    if($request['instant_delivery'] == '0') 
                    {
                    $shiprocketService->createOrder($order);
                    }
                }
            }catch(\Exception $e) {
                return response()->json($e->getMessage());
            }

            array_push($order_ids, $order_id);
        }

        CartManager::cart_clean($request);

        return response()->json(['order_id' => $order_id, 'message' => translate('order_placed_successfully')], 200);
    }
    
    public function place_order_by_offline_payment(Request $request)
    {
        $cart_group_ids = CartManager::get_cart_group_ids($request);
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $physical_product = false;
        foreach($carts as $cart){
            if($cart->product_type == 'physical'){
                $physical_product = true;
            }
        }

        if($physical_product) {
            $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
            $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

            if ($request->has('billing_address_id')) {
                $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->input('billing_address_id')])->first();

                if (!$shipping_address) {
                    return response()->json(['message' => translate('address_not_found')], 200);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping_address->country)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

                } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                }
            }
        }

        $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
        $order_ids = [];
        foreach ($cart_group_ids as $group_id) {
            $data = [
                'payment_method' => 'offline_payment',
                'order_status' => 'confirmed',
                'payment_status' => 'unpaid',
                'transaction_ref' => $request->transaction_ref,
                'payment_by' => $request->payment_by,
                'payment_note' => $request->payment_note,
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id,
                'request' => $request,
            ];
            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ?  ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();
            
            try {
                //shiprocket actions
                $weight = array_reduce($order->details()->with('product:weight,id')->get()->toArray(), function($prev, $c){
                    return $prev + $c['product']['weight'];    
                }, 0);
                
                $shippingPrice = $shiprocketService->checkForAvailability([
                    'pickup_postcode'  => $order->seller->shop->pincode,
                    'delivery_postcode'  => json_decode($order->shipping_address_data)->zip ?? '',
                    'weight'  => $weight,
                    'cod'  => 1,
                    'mode'  => 'Surface',
                ]);
            
                if($shippingPrice['data'] !== null) {
                    //$cost = $shippingPrice['data']->rate;
                    $cost = $order->shipping_cost;
                    
                    $order->shipping_cost = $cost;
                    $order->save();
                    if($request['instant_delivery'] == '0')
                    {
                    $shiprocketService->createOrder($order);
                    }
                }
            }catch(\Exception $e) {
                return response()->json($e->getMessage());
            }

            array_push($order_ids, $order_id);
        }

        CartManager::cart_clean($request);

        return response()->json(['order_id' => $order_id, 'message' => translate('order_placed_successfully')], 200);
    }

    public function place_order_by_wallet(Request $request, ShiprocketService $shiprocketService)
    {
        
        $cart_group_ids = CartManager::get_cart_group_ids($request);
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $cartTotal = 0;
        foreach($cart_group_ids as $cart_group_id){
            $cartTotal += CartManager::cart_grand_total($cart_group_id);
        }
        $user = Helpers::get_customer($request);
        if( $cartTotal > $user->wallet_balance)
        {
            return response()->json('inefficient balance in your wallet to pay for this order', 403);
        }else{
            $physical_product = false;
            foreach($carts as $cart){
                if($cart->product_type == 'physical'){
                    $physical_product = true;
                }
            }

            if($physical_product) {
                $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
                $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

                if ($request->has('billing_address_id')) {
                    $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->input('billing_address_id')])->first();

                    if (!$shipping_address) {
                        return response()->json(['message' => translate('address_not_found')], 200);
                    }
                    elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping_address->country)) {
                        return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

                    } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                        return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                    }
                }
            }

            $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
            $order_ids = [];
            foreach ($cart_group_ids as $group_id) {
                $data = [
                    'payment_method' => 'pay_by_wallet',
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                    'transaction_ref' => '',
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id,
                    'request' => $request,
                ];
                $order_id = OrderManager::generate_order($data);

                $order = Order::find($order_id);
                $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
                $order->billing_address_data = ($request['billing_address_id'] != null) ?  ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
                $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
                $order->save();
                
                try {
                    //shiprocket actions
                    $weight = array_reduce($order->details()->with('product:weight,id')->get()->toArray(), function($prev, $c){
                        return $prev + $c['product']['weight'];    
                    }, 0);
                    
                    $shippingPrice = $shiprocketService->checkForAvailability([
                        'pickup_postcode'  => $order->seller->shop->pincode,
                        'delivery_postcode'  => json_decode($order->shipping_address_data)->zip ?? '',
                        'weight'  => $weight,
                        'cod'  => 0,
                        'mode'  => 'Surface',
                    ]);
                
                    if($shippingPrice['data'] !== null) {
                        //$cost = $shippingPrice['data']->rate;
                        $cost = $order->shipping_cost;
                        
                        $order->shipping_cost = $cost;
                        $order->save();
                        
                        if($request['instant_delivery'] == '0')   
                    {
                        $shiprocketService->createOrder($order);
                    }
                    }
                }catch(\Exception $e) {
                    return response()->json($e->getMessage());
                }

                array_push($order_ids, $order_id);
            }

            CustomerManager::create_wallet_transaction($user->id, Convert::default($cartTotal), 'order_place','order payment');

            CartManager::cart_clean($request);

            return response()->json(['order_id' => $order_id, 'message' => translate('order_placed_successfully')], 200);
        }
    }

    public function refund_request(Request $request)
    {
        
        $order_details = OrderDetail::find($request->order_details_id);

        $user = $request->user();

        if($order_details->delivery_status == 'delivered')
        {
            $order = Order::find($order_details->order_id);
            $total_product_price = 0;
            $refund_amount = 0;
            $data = [];
            foreach ($order->details as $key => $or_d) {
                $total_product_price += ($or_d->qty*$or_d->price) + $or_d->tax - $or_d->discount;
            }

            $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

            $coupon_discount = ($order->discount_amount*$subtotal)/$total_product_price;

            $refund_amount = $subtotal - $coupon_discount;

            $data['product_price'] = $order_details->price;
            $data['quntity'] = $order_details->qty;
            $data['product_total_discount'] = $order_details->discount;
            $data['product_total_tax'] = $order_details->tax;
            $data['subtotal'] = $subtotal;
            $data['coupon_discount'] = $coupon_discount;
            $data['refund_amount'] = $refund_amount;

            $refund_day_limit = Helpers::get_business_settings('refund_day_limit');
            $order_details_date = $order_details->created_at;
            $current = \Carbon\Carbon::now();
            $length = $order_details_date->diffInDays($current);
            $expired = false;
            $already_requested = false;
            if($order_details->refund_request != 0)
            {
                $already_requested = true;
            }
            if($length > $refund_day_limit )
            {
                $expired = true;
            }
            return response()->json(['status' => true,'message'=>translate('Your_order_record'),'already_requested'=>$already_requested,'expired'=>$expired,'refund'=>$data,], 200);
        }else{
            return response()->json(['status' => false,'message'=>translate('You_can_request_for_refund_after_order_delivered')], 200);
        }

    }
    
    public function store_refund(Request $request)
    {

        $order_details = OrderDetail::find($request->order_details_id);

        $user = $request->user();

        if($order_details->refund_request == 0){

            $validator = Validator::make($request->all(), [
                'order_details_id' => 'required',
                'amount' => 'required',
                'refund_reason' => 'required'

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $refund_request = new RefundRequest;
            $refund_request->order_details_id = $request->order_details_id;
            $refund_request->customer_id = $request->user()->id;
            $refund_request->status = 'pending';
            $refund_request->amount = $request->amount;
            $refund_request->product_id = $order_details->product_id;
            $refund_request->order_id = $order_details->order_id;
            $refund_request->refund_reason = $request->refund_reason;

            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('refund/', 'png', $img);
                }
                $refund_request->images = json_encode($product_images);
            }
            $refund_request->save();

            $order_details->refund_request = 1;
            $order_details->save();

            return response()->json(translate('refunded_request_updated_successfully!!'), 200);
        }else{
            return response()->json(translate('already_applied_for_refund_request!!'), 302);
        }

    }
    
    public function refund_details(Request $request)
    {
        $order_details = OrderDetail::find($request->id);
        $refund = RefundRequest::where('customer_id',$request->user()->id)
                                ->where('order_details_id',$order_details->id )->get();
        $refund = $refund->map(function($query){
            $query['images'] = json_decode($query['images']);
            return $query;
        });

        $order = Order::find($order_details->order_id);

            $total_product_price = 0;
            $refund_amount = 0;
            $data = [];
            foreach ($order->details as $key => $or_d) {
                $total_product_price += ($or_d->qty*$or_d->price) + $or_d->tax - $or_d->discount;
            }

            $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

            $coupon_discount = ($order->discount_amount*$subtotal)/$total_product_price;

            $refund_amount = $subtotal - $coupon_discount;

            $data['product_price'] = $order_details->price;
            $data['quntity'] = $order_details->qty;
            $data['product_total_discount'] = $order_details->discount;
            $data['product_total_tax'] = $order_details->tax;
            $data['subtotal'] = $subtotal;
            $data['coupon_discount'] = $coupon_discount;
            $data['refund_amount'] = $refund_amount;
            $data['refund_request']=$refund;

        return response()->json($data, 200);
    }

    public function digital_product_download(Request $request, $id)
    {
        $order_data = OrderDetail::with('order.customer')->find($id);
        $customer_id = $request->user()->id;
        if($order_data->order->customer->id != $customer_id){
            return response()->json(['message'=>translate('Invalid customer')], 202);
        }

        if( $order_data->product->digital_product_type == 'ready_product' && $order_data->product->digital_file_ready) {
            $file_path = storage_path('app/public/product/digital-product/' .$order_data->product->digital_file_ready);
        }else{
            $file_path = storage_path('app/public/product/digital-product/' . $order_data->digital_file_after_sell);
        }

        return \response()->download($file_path);
    }
    
    //cron to update the shiprocket status
    public function updateShiprocketStatus(ShiprocketService $shiprocketService)
    {
        $allShiprocketOrders = ShiprocketCourier::whereNotIn("status", ["CANCELED", "DELIVERED"])->with('order')->get();
        
        foreach ($allShiprocketOrders as $shiprocket) {
            if(isset($shiprocket->order) && !empty($shiprocket->order)) {
                if($shiprocket && empty($shiprocket->awb_code)) {
                    $status = $shiprocketService->getOrder($shiprocket->shiprocket_order_id);
                    
                    if($status !== false) {
                        $shiprocket->status = $status->data->status;
                        $shiprocket->awb_code = $status->data->awb_data->awb ?? '';
                        $shiprocket->save();
                        
                         $order = Order::where('id',$shiprocket->order_id)->first();
                            if($order){
                                if($shipping->status == 'NEW'){
                                    $order->order_status = 'confirmed';
                                }elseif ($shipping->status == 'PICKUP SCHEDULED') {
                                    $order->order_status = 'processing';
                                }elseif ($shipping->status == 'Out for Delivery') {
                                    $order->order_status = 'out_for_delivery';
                                }elseif ($shipping->status == 'Delivered') {
                                    $order->order_status = 'delivered';
                                    $order->payment_status = 'paid';
                                    
                                 if($order->withdraw_requests_status == 0)
                                    {
                                    $product_id = OrderDetail::where('order_id',$shiprocket->order_id)->first();
                                    $withdrawRequest = new WithdrawRequest();
                                    $withdrawRequest->seller_id = $order->seller_id;
                                    $withdrawRequest->product_id = $product_id->product_id;
                                    $withdrawRequest->order_id = $order->id;
                                    $withdrawRequest->amount = $order->order_amount;
                                    $withdrawRequest->transaction_note = $order->payment_note;
                                    $withdrawRequest->save();
                                    $order->withdraw_requests_status = 1;
                                  }
                                }
                                $order->save();
                            }
                    }
                    
                    return response()->json(['status' => true, 'data' => 'updated', 'run_at' => time()]);
                }else {
                    $status = $shiprocketService->getAwbStatus($shipping->awb_code);
                        
                        if($status !== false && isset($status->tracking_data->shipment_track[0])) {
                        $shiprocket->status = $status->tracking_data->shipment_track[0]->current_status;
                        $shiprocket->courier_company_id = $status->tracking_data->shipment_track[0]->courier_company_id;
                        $shiprocket->courier_name = $status->tracking_data->shipment_track[0]->courier_name;
                        $shiprocket->scans = json_encode($status->tracking_data->shipment_track_activities);
                        $shiprocket->save();
                        
                        $order = Order::where('id',$shiprocket->order_id)->first();
                            if($order){
                                if($shipping->status == 'NEW'){
                                    $order->order_status = 'confirmed';
                                }elseif ($shipping->status == 'PICKUP SCHEDULED') {
                                    $order->order_status = 'processing';
                                }elseif ($shipping->status == 'Out for Delivery') {
                                    $order->order_status = 'out_for_delivery';
                                }elseif ($shipping->status == 'Delivered') {
                                    $order->order_status = 'delivered';
                                    $order->payment_status = 'paid';
                                    if($order->withdraw_requests_status == 0)
                                    {
                                    $product_id = OrderDetail::where('order_id',$shiprocket->order_id)->first();
                                    $withdrawRequest = new WithdrawRequest();
                                    $withdrawRequest->seller_id = $order->seller_id;
                                    $withdrawRequest->product_id = $product_id->product_id;
                                    $withdrawRequest->order_id = $order->id;
                                    $withdrawRequest->amount = $order->order_amount;
                                    $withdrawRequest->transaction_note = $order->payment_note;
                                    $withdrawRequest->save();
                                    $order->withdraw_requests_status = 1;
                                  }
                                    
                                }
                                $order->save();
                            }
                    }
                    }
                    
                    return response()->json(['status' => true, 'data' => 'updated', 'run_at' => time()]);
                }
            }
    }
}