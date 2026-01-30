<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\CartShipping;
use App\Model\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\ShippingType;
use App\Model\Cart;
use App\Model\Seller;
use App\Model\Product;
use App\Model\ShippingAddress;
use App\Services\ShiprocketService;
use App\Services\ShipyaariService;
use App\Model\WithdrawRequest;
use App\Model\OrderDetail;
use App\Model\Order;
use Illuminate\Support\Facades\DB;

class ShippingMethodController extends Controller
{
    public function get_shipping_method_info($id)
    {
        try {
            $shipping = ShippingMethod::find($id);
            return response()->json($shipping, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function shipping_methods_by_seller($id, $seller_is)
    {
        $seller_is = $seller_is == 'admin' ? 'admin' : 'seller';
        return response()->json(Helpers::get_shipping_methods($id, $seller_is), 200);
    }

    public function choose_for_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_group_id' => 'required',
            // 'address_id' => 'required',
            'id' => 'nullable'
        ], [
            'id.required' => translate('shipping_id_is_required'),
            // 'address_id.required' => 'Shipping address is required'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids($request) as $group_id) {
                $request['cart_group_id'] = $group_id;
                self::insert_into_cart_shipping($request);
            }
        } else {
            self::insert_into_cart_shipping($request);
        }

        return response()->json(translate('successfully_added'));
    }

    public static function insert_into_cart_shipping($request)
    {
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (isset($shipping) == false) {
            $shipping = new CartShipping();
        }
        
        $shiprocket = ShippingMethod::where("title", "Shiprocket")->first();
        
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $shiprocket->id ?? null; //$request['id'];
        $shipping['shipping_cost'] = $shiprocket->cost ?? 0;
        $shipping->save();
    }
    
    public function get_shipping_cost(Request $request)
    {
       //dd($request->iscod);
        $costAmount = [];
        $validator = Validator::make($request->all(), [
            'cart_group_id' => 'required',
            'address_id' => 'required',
            'payment_method' => 'nullable'
        ]);
        
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
      
        @file_put_contents(public_path('log'), print_r($request->all(), 1));
        
        $paymentMethod = $request['payment_method'] ?? $request['payment_methodd'] ?? null;
        $totalShippingCost = 0;
        
        foreach (CartManager::get_cart_group_ids($request) as $group_id) {
            $request['cart_group_id'] = $group_id;
          //dd($request['cart_group_id']);
            try {
                $shiprocketService = new ShipyaariService();
                //shiprocket actions
                $seller_data = DB::table('new_cart')->where(['cart_group_id' => $request['cart_group_id']])->first();
                $p = DB::table('products')->where('id',$seller_data->product_id)->first();
                 //echo "hi"; dd($seller_data);
                if($seller_data) {
                    //dd($p->user_id);
                    $seller = Seller::where("id", $p->user_id)->with('shop')->first();
                     $warehouse_id = DB::table('warehouse')->where('seller_id',$p->user_id)->first();
                    //dd($warehouse_id->pincode);
                    if($seller) {
                        $weight = 0;
                          $length = 0;
                          $width = 0;
                          $height = 0;

                        //dd(CartManager::get_carts($request['cart_group_id']));
                        foreach (CartManager::get_carts($request['cart_group_id']) as $c) {
                          // dd($c);
                            $product = Product::where('id', $c->product_id)->select("id", "free_delivery")->first();
                            //dd($c->variation);
                            $prs = DB::table('sku_product_new')->where('product_id',$c->product_id)->where('id',$c->variation)->get();
                           
                            foreach ($prs as $pr) {
                                 $weight += $pr->weight * $c->quantity;
                                   $length += $pr->length * $c->quantity;
                                   $width += $pr->breadth * $c->quantity;
                                     $height +=$pr->height * $c->quantity; 
                                           }
                        } 
                         
                        $productfp = Product::where(['id' => $seller_data->product_id ])->select("id", "weight", "free_delivery")->first();
                            if($productfp) {
                                $free_delivery = $productfp->free_delivery;
                               // dd($free_delivery);
                            }
                            
                        if($weight) {
                            
                            $shippingAddress = ShippingAddress::find($request['address_id']);
                           
                            $shippingPrice = $shiprocketService->checkForAvailability([
                                'pickupPincode'  => (int) $warehouse_id->pincode,
                                'deliveryPincode'  => (int) $shippingAddress->zip ?? '',
                                'weight'  => $weight,
                                'paymentMode'  => $request['payment_method'] === 'cash_on_delivery' ? 'COD':'PREPAID',
                                'orderType' => 'B2C',
                                'invoiceValue'=> 10,
                                 'dimension' => [
                                     'length' => $length,
                                      'width'  => $width,
                                      'height' => $height
                                        ]
                            ]);
                       
                            $instant_delivery = false;
                            if($request['instant_delivery'])
                            {
                                $instant_delivery = true;
                            }
                          
                            if($shippingPrice['data'] !== null) {
                                if(($instant_delivery == true) && ($free_delivery == 1))
                                {
                                     
                                   $cost = $shippingPrice['data']->total ?? 0;   
                                }else{
                                    
                                  //dd($shippingPrice['data']->total);
                                if($free_delivery) {
                                    
                                   $cost = 0; 
                                  
                                }  else {
                                    
                                $cost = $shippingPrice['data']->total ?? 0;
                                }
                                
                                }
           
                                //update in shipping_cost table
                                $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
                                if (isset($shipping) == false) {
                                    $shipping = new CartShipping();
                                }
                                
                                $shiprocket = ShippingMethod::where("title", "Shiprocket")->first();
                                
                                $shipping['cart_group_id'] = $request['cart_group_id'];
                                $shipping['shipping_method_id'] = $shiprocket->id ?? null;
                                if($request['instant_delivery']){
                                    if($cost <= 100){
                                      $cost =   100;
                                    }else{
                                        $cost = $cost*1.25;
                                    }
                                    $shipping['shipping_cost'] = $cost ?? 0;
                                }else{
                                    $shipping['shipping_cost'] = $cost ?? 0;
                                }
                                
                                $shipping->save();
                                
                                $totalShippingCost += $cost ?? 0;
                                //$costAmount[] = $cost;
                            }
                        }
                    }
                }
                
            }catch(\Exception $e) {
                return response()->json([
                    'status' => 'failure',
                    'shipping_cost' => 0,
                    'message' => $e->getMessage()
                ]);
            }
        }
      
        return response()->json([
            'status' => 'success',
            'shipping_cost' => $totalShippingCost,
            'message' => 'Total shipping cost'
        ]);
        
    }

    public function chosen_shipping_methods(Request $request)
    {
        $group_ids = CartManager::get_cart_group_ids($request);
        return response()->json(CartShipping::whereIn('cart_group_id', $group_ids)->get(), 200);
    }

    public function check_shipping_type(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seller_is' => 'required',
            'seller_id' => 'required'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if($request->seller_is == 'admin')
        {
            $admin_shipping = ShippingType::where('seller_id',0)->first();
            $shipping_type = isset($admin_shipping)==true?$admin_shipping->shipping_type:'order_wise';
            
        }
        else{
            $seller_shipping = ShippingType::where('seller_id',$request->seller_id)->first();
            $shipping_type = isset($seller_shipping)==true? $seller_shipping->shipping_type:'order_wise';
            
        }
        return response()->json(['shipping_type'=>$shipping_type], 200);
    }
    
    //updateStatus
    public function updateShiprocketStatus(Request $request, ShiprocketService $shiprocketService)
    {
        $shiprocketService->updateStatus($request);
        $order = Order::find($request->order_id);
        if($order){
            if($request->current_status == 'delivered' && $order->withdraw_requests_status == 0)
            {
                $product_id = OrderDetail::where('order_id',$request->id)->first();
                $withdrawRequest = new WithdrawRequest();
                $withdrawRequest->seller_id = $order->seller_id;
                $withdrawRequest->product_id = $product_id->product_id;
                $withdrawRequest->order_id = $order->id;
                $withdrawRequest->amount = $order->order_amount;
                $withdrawRequest->transaction_note = $order->payment_note;
                $withdrawRequest->save();
                $order->withdraw_requests_status == 1;
            }
            $order->save();
        }
        
        return response()->json(['status' => 'success']);
    }
}