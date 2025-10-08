<?php

namespace App\Http\Controllers;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ShippingAddress;
use App\Model\ShiprocketCourier;
use App\Model\Product;
use App\Model\Review;
use App\Model\RefundRequest;
use Razorpay\Api\Errors\SignatureVerificationError;
use App\CPU\ImageManager;
use Illuminate\Support\Facades\Validator;
use App\Model\OrderTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Redirect;
use Session;
use App\Model\WalletTransaction;
use App\User;
use App\Services\ShiprocketService;
use App\Services\ShipyaariService;
use Illuminate\Support\Facades\Log;

class RazorPayController extends Controller
{
    public function payWithRazorpay()
    {
        return view('razor-pay');
    }


    public function createRazorpayOrder(Request $request, ShipyaariService $shiprocketService)
    {


        try {
            $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));

            try {
                set_time_limit(300);
                ini_set('max_execution_time', 300);

                $cart_group_ids = CartManager::get_cart_group_ids($request);
                //dd($cart_group_ids);
                if (empty($cart_group_ids)) {
                    return response()->json(['message' => 'No cart group found.'], 400);
                }

                $carts = DB::table('new_cart')->whereIn('cart_group_id', $cart_group_ids)->get();

                if ($carts->isEmpty()) {
                    return response()->json(['message' => 'Cart is empty.'], 400);
                }

                $physical_product = $carts->contains(fn($cart) => $cart->product_type === 'physical');

                if ($physical_product) {
                    $zip_restrict = Helpers::get_business_settings('delivery_zip_code_area_restriction');
                    $country_restrict = Helpers::get_business_settings('delivery_country_restriction');


                    $billing_address_id = $request->input('billing_address_id') ?: $request->input('shipping_address_id');

                    $shipping_address_id = $request->input('shipping_address_id');
                    // dd($billing_address_id,$shipping_address_id);
                    $shipping_address = ShippingAddress::where([
                        'customer_id' => $request->user()->id,
                        'id' => $shipping_address_id
                    ])->first();
                    //dd( $shipping_address);
                    if (!$shipping_address) {
                        return response()->json(['message' => translate('address_not_found')], 404);
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
                    if ($order) {
                        $order->company_name = $request->company_name;
                        $order->gst_number = $request->gst_no;
                        $order->save();
                    }

                    if (!$order) {
                        return response()->json(['message' => "Failed to create order for cart group $group_id"], 500);
                    }

                    // Update billing address and note
                    if ($request->has('shipping_address_id')) {
                        $order->billing_address = $billing_address_id;
                        $order->billing_address_data = ShippingAddress::find($billing_address_id);
                        $order->shipping_address_data = ShippingAddress::find($request->shipping_address_id);
                        $order->shipping_address = $request->shipping_address_id;
                    }
                    if ($request->filled('order_note')) {
                        $order->order_note = $request->order_note;
                    }
                    $order->save();

                    try {
                        $details = $order->details()->with('product:id,weight')->get()->toArray();
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

                        $shippingPrice = $shiprocketService->checkForAvailability([
                            'pickup_postcode' => $order->seller->shop->pincode,
                            'delivery_postcode' => json_decode($order->shipping_address_data)->zip ?? '',
                            'weight' => $total_weight,
                            'cod' => $order->payment_method === 'cash_on_delivery' ? 1 : 0,
                            'mode' => 'Surface',
                        ]);


                        // dd($order);
                        if ($request->iscod === '1') {
                            $shiprocketService->createOrder($order);
                        }

                        // dd($shippingPrice['data']);
                        if (!empty($shippingPrice['data'])) {
                            $order->shipping_cost = $request->shipping_fee == '0.00' ? 0 : $order->shipping_cost;
                            $order->save();


                            // COD

                            //          if($request->iscod === '1'){
                            //      $shiprocketService->createOrder($order);
                            //   }

                        }



                        $order_ids[] = $order_id;
                    } catch (\Exception $innerEx) {
                        return response()->json([
                            'message' => 'Shiprocket error',
                            'error' => $innerEx->getMessage()
                        ], 500);
                    }
                }


                //dd($order_ids);
                //    CartManager::cart_clean($request);
                if ($request->iscod === '1') {
                    $responseData = [
                        'user_id' => auth()->id(),
                        'order_ids' => $order_ids,
                        'message' => translate('order_placed_successfully'),
                    ];
                    DB::table('new_cart')->whereIn('cart_group_id', $cart_group_ids)->delete();
                } elseif ($request->iscod === '0') {
                    //Razorpay
                    $totalAmount = Order::whereIn('id', $order_ids)->value('amount');
                    // dd($totalAmount);
                    $razorpayOrder = $api->order->create([
                        'receipt' => 'order_receipt_' . implode('_', $order_ids),
                        // 'receipt' => 'order_rcptid_' . $order_ids[0],
                        'amount' => $totalAmount * 100, // amount in paise
                        'currency' => 'INR',
                        'payment_capture' => 1
                    ]);

                    DB::table('new_cart')->whereIn('cart_group_id',  $cart_group_ids)->delete();
                    $responseData['amount'] = $totalAmount;
                    $responseData['online_payment'] = $razorpayOrder->toArray();
                    $responseData['order_ids'] = $order_ids;
                    $responseData['cart_group_ids'] = $cart_group_ids;
                }
                //  dd($responseData);
                return response()->json($responseData, 200);
            } catch (\Exception $ex) {
                return response()->json([
                    'message' => 'Order processing failed',
                    'error' => $ex->getMessage(),
                    'trace' => $ex->getTraceAsString()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Razorpay Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function payments(Request $request, ShipyaariService $shiprocketService)
    {
        $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        try {
            // Step 1: Verify Signature
            $api->utility->verifyPaymentSignature($attributes);

            // Step 2: Fetch Payment
            $payment = $api->payment->fetch($request->razorpay_payment_id);

            // Step 3: Capture Payment only if not already captured
            if ($payment['status'] !== 'captured') {
                $payment->capture(['amount' => (int) $payment['amount']]); // amount in paisa
                Log::info('Payment captured for ID: ' . $request->razorpay_payment_id);
            } else {
                Log::warning('Payment already captured: ' . $request->razorpay_payment_id);
            }

            // Step 4: Fetch Razorpay Order for receipt (optional)
            $razorpayOrder = $api->order->fetch($request->razorpay_order_id);
            $receipt = $razorpayOrder['receipt'] ?? null;

            // Step 5: Update Orders in DB
            if (is_array($request->order_ids) && count($request->order_ids)) {
                Order::whereIn('id', $request->order_ids)->update([
                    'Payment_status' => 'Paid',
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'transaction_ref' => $receipt,
                ]);


                // Step 6: Call Shipyaari for each order
                $orders = Order::whereIn('id', $request->order_ids)->get();

                foreach ($orders as $ord) {
                    try {
                        $shiprocketService->createOrder($ord);
                    } catch (\Exception $ex) {
                        Log::error('Shipyaari Order Error for Order ID ' . $ord->id . ': ' . $ex->getMessage());
                    }
                }
            } else {
                Log::warning('No valid order IDs found in payment request.');
                return response()->json([
                    'success' => false,
                    'message' => 'No valid order IDs provided.'
                ], 422);
            }

            // Step 7: Return success response
            return response()->json(['success' => true]);
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Signature verification failed',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Payment Processing Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment verification failed.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function get_order_list(Request $request)
    {


        $order_id = $request->input('order_id');
        //dd($order_id);
        $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->where('id', $order_id)->get();

        $orders->map(function ($data) {
            $shiprocketService =  new ShipyaariService();
            // Sabhi order_details nikaal lo
            $orderDetails = DB::table('order_details')->where('order_id', $data->id)->get();

            $skuProducts = $orderDetails->map(function ($ord) {
                //dd($ord->qty);
                return DB::table('products')
                    ->join('sku_product_new', function ($join) use ($ord) {
                        $join->on('products.id', '=', 'sku_product_new.product_id')
                            ->where('sku_product_new.variation', '=', $ord->variant);
                    })
                    ->where('products.id', $ord->product_id)
                    ->select(
                        'products.name as name',
                        'products.Return_days',
                        'sku_product_new.*',
                        DB::raw("'{$ord->qty}' as qty") // 👈 custom qty value added here
                    )
                    ->first(); // yahaan get() se multiple milenge, par agar har order_detail ka ek hi match h to first()
            });

            $data['sku_product'] = $skuProducts;


            $data['total_variant_mrp'] = $skuProducts->sum(function ($item) {
                //dd($item);
                return ($item->variant_mrp ?? 0) * ($item->qty);
            });

            $data['total_listed_price'] = $skuProducts->sum(function ($item) {
                return ($item->listed_price ?? 0) * ($item->qty);
            });

            //  dd($data['sku_product']);
            $data['shipping_address_data'] = json_decode($data['shipping_address_data']);
            $data['billing_address_data'] = json_decode($data['billing_address_data']);

            $shiproket_shiprocket_order_id = ShiprocketCourier::where('order_id', $data['id'])->value('awb_code');
            $data['awbs'] = $shiproket_shiprocket_order_id;

            if ($shiproket_shiprocket_order_id) {

                $response = $shiprocketService->trackOrder($shiproket_shiprocket_order_id);
                $data['current_status'] = $response->getData(true)['currentStatus'];


                // dd($data['current_status']); // Output: DELIVERED


            }

            if (isset($shiproket_shiprocket_order_id)) {
                $shiproket_status = ShiprocketCourier::where('order_id', $data['id'])->value('status');
                if ($shiproket_status == 'DELIVERED') {
                    $data['delivered_at'] = ShiprocketCourier::where('order_id', $data['id'])->value('delivered_at');
                    //dd($data['delivered_at']);
                }
                // dd($shiproket_status);
                $shiproket_created_at = ShiprocketCourier::where('order_id', $data['id'])->value('created_at');
                $shiproket_updated_at = ShiprocketCourier::where('order_id', $data['id'])->value('updated_at');
                if ($shiproket_status == 'NEW') {
                    $data['order_status'] =  'Confirmed';
                } else {
                    $data['order_status'] =  $shiproket_status;
                }
                $data['created_at'] =  $shiproket_created_at;
                $data['updated_at'] =  $shiproket_updated_at;
            }



            return $data;
        });
        //dd($orders);
        return view('web.get_order', compact('orders', 'order_id'));
    }


    public function order_cancel(Request $request, ShipyaariService $shiprocketService)
    {
        // Step 1: Validate
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'product_id' => 'required',
            'reason' => 'nullable',
            'remarks' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Step 2: Fetch Order & OrderDetail
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order_detail = OrderDetail::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$order_detail) {
            return response()->json(['error' => 'Order detail not found'], 404);
        }

        // Step 3: Cancel the order
        $order->order_status = 'canceled';
        $order->save();

        $order_detail->cancellation_reason = $request->reason;
        $order_detail->cancellation_remarks = $request->remarks;
        $order_detail->order_status_details = 'canceled';
        $order_detail->save();

        // Step 4: Restore stock
        OrderManager::stock_update_on_order_status_changes($order_detail, 'canceled');

        // Step 5: Refund Logic (if online and unpaid)
        if ($order->payment_method !== 'cash_on_delivery' && $order_detail->payment_status === 'unpaid') {
            $refundAmount = $order->wallet_deduction + $order->amount;

            //    $prod = DB::table('orders')
            // ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            // ->join('products', 'order_details.product_id', '=', 'products.id')
            // ->where('products.id', $order_details->product_id) // if $order_details is already defined
            // ->select('products.free_delivery', 'order_details.price', 'orders.shipping_cost')
            // ->first();

            $previousBalance = WalletTransaction::where('user_id', $order->customer_id)
                ->orderBy('id', 'desc')
                ->value('balance') ?? 0;

            $wallet_transaction = new WalletTransaction;
            $wallet_transaction->user_id = $order->customer_id;
            $wallet_transaction->transaction_id = OrderManager::gen_unique_id();
            $wallet_transaction->credit  = $refundAmount;
            $wallet_transaction->balance = $previousBalance + $refundAmount; // ✅ correct calculation
            $wallet_transaction->transaction_type = "refund";
            $wallet_transaction->reference = 'Order cancellation refund for Order #' . $order->id;
            $wallet_transaction->save();

            // dd($wallet_transaction);
            $user = User::find($order->customer_id);
            if ($user) {
                $user->wallet_balance = $user->wallet_balance + $refundAmount; // ✅
                $user->save();
            }
        }

        // Step 6: Cancel from Shipyaari/Shiprocket
        try {
            $shiprocketService->cancelOrder($order);
        } catch (\Exception $e) {
            \Log::error('Shiprocket Cancel Error for Order ID ' . $order->id . ': ' . $e->getMessage());
        }

        // Step 7: Final Response
        return response()->json(translate('order_canceled_successfully'), 200);
    }


    public function order_track(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderManager::track_order($request['order_id']), 200);
    }

    public function returns(Request $request)
    {
        // $order_id = $request->input('order_id');
        $order_id = 1000007;
        //dd($order_id);
        $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->where('id', $order_id)->get();

        $orders->map(function ($data) {

            // Sabhi order_details nikaal lo
            $orderDetails = DB::table('order_details')->where('order_id', $data->id)->get();

            $skuProducts = $orderDetails->map(function ($ord) {

                return DB::table('products')
                    ->join('sku_product_new', function ($join) use ($ord) {
                        $join->on('products.id', '=', 'sku_product_new.product_id')
                            ->where('sku_product_new.variation', '=', $ord->variant);
                    })
                    ->where('products.id', $ord->product_id)
                    ->select(
                        'products.name as name',
                        'sku_product_new.*',
                        DB::raw("'{$ord->qty}' as qty") // 👈 custom qty value added here
                    )
                    ->first(); // yahaan get() se multiple milenge, par agar har order_detail ka ek hi match h to first()
            });

            $data['sku_product'] = $skuProducts;


            $data['total_variant_mrp'] = $skuProducts->sum(function ($item) {
                return ($item->variant_mrp ?? 0) * ($item->qty);
            });

            $data['total_listed_price'] = $skuProducts->sum(function ($item) {
                return ($item->listed_price ?? 0) * ($item->qty);
            });

            //  dd($data['sku_product']);
            $data['shipping_address_data'] = json_decode($data['shipping_address_data']);
            $data['billing_address_data'] = json_decode($data['billing_address_data']);

            $shiproket_shiprocket_order_id = ShiprocketCourier::where('order_id', $data['id'])->value('shiprocket_order_id');
            if (isset($shiproket_shiprocket_order_id)) {
                $shiproket_status = ShiprocketCourier::where('order_id', $data['id'])->value('status');
                $shiproket_created_at = ShiprocketCourier::where('order_id', $data['id'])->value('created_at');
                $shiproket_updated_at = ShiprocketCourier::where('order_id', $data['id'])->value('updated_at');
                if ($shiproket_status == 'NEW') {
                    $data['order_status'] =  'Confirmed';
                } else {
                    $data['order_status'] =  $shiproket_status;
                }
                $data['created_at'] =  $shiproket_created_at;
                $data['updated_at'] =  $shiproket_updated_at;
            }



            return $data;
        });
        //dd($orders);
        return view('web.return', compact('orders', 'order_id'));
    }


    public function return_req(Request $request)
    {
        // dd($request->all());
        $order_details = OrderDetail::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();

        $user = $request->user();


        /*$loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if($loyalty_point_status == 1)
        {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($request->order_details_id);

            if($user->loyalty_point < $loyalty_point)
            {
                return response()->json(translate('you have not sufficient loyalty point to refund this order!!'), 200);
            }
        }*/

        //  if($order_details->refund_request == 0){

        // $validator = Validator::make($request->all(), [
        //     'order_details_id' => 'required',
        //     'amount' => 'required',
        //     'refund_reason' => 'required'

        // ]);
        // if ($validator->fails()) {
        //     return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        // }
        $refund_request = new RefundRequest;

        $refund_request->order_details_id = $order_details->id;

        $refund_request->customer_id = $request->user()->id;
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
        return response()->json([
            'message' => 'successfully',
            'status' => true
        ], 200);
        // }else{
        //     return response()->json(translate('already_applied_for_refund_request!!'), 302);
        // }

    }

    public function review_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'comment' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $image_array = [];
        if (!empty($request->file('fileUpload'))) {
            foreach ($request->file('fileUpload') as $image) {
                if ($image != null) {
                    array_push($image_array, ImageManager::upload('review/', 'png', $image));
                }
            }
        }

        Review::updateOrCreate(
            [
                'delivery_man_id' => null,
                'customer_id' => $request->user()->id,
                'product_id' => $request->product_id
            ],
            [
                'customer_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'comment' => $request->comment,
                'rating' => $request->rating,
                'attachment' => json_encode($image_array),
            ]
        );

        return response()->json(['message' => translate('successfully review submitted!')], 200);
    }

    public function status_return(Request $request)
    {
        $orderId = $request->order_id;
        //dd($order_id);


        $return = RefundRequest::where('order_id', $orderId)->first();

        $allStatuses = [
            "Request Submitted",
            "Request in progress",
            "Request Accepted",
            "Refund Initiated",

        ];

        return response()->json([
            'success' => true,
            'statuses' => $allStatuses,
            'current' => $return


        ]);
    }

    public function delete_return(Request $request)
    {
        $orderId = $request->order_id;


        $deleted = RefundRequest::where('order_id', $orderId)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Return deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'No return request found for this order.']);
        }
    }

    public function payment(Request $request, ShiprocketService $shiprocketService)
    {
        try {
            $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));
            $payment = $api->payment->fetch($request['razorpay_payment_id']);
            /*$api->transfer->create(array('account' => 'acc_id', 'amount' => 500, 'currency' => 'INR'));*/

            if (count($request->all()) && !empty($request['razorpay_payment_id'])) {
                $response = $api->payment->fetch($request['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $unique_id = OrderManager::gen_unique_id();
                $order_ids = [];
                foreach (CartManager::get_cart_group_ids() as $group_id) {
                    $data = [
                        'payment_method' => 'razor_pay',
                        'order_status' => 'confirmed',
                        'payment_status' => 'paid',
                        'transaction_ref' => $response['id'],
                        'order_group_id' => $unique_id,
                        'cart_group_id' => $group_id,
                        'wallet_deduct_amount' => session('wallet_deduct_amount') ?? 0,
                        'iteam_amounts' => session('iteam_amounts') ?? 0,
                        'instant_delivery' => session('instant_delivery') ?? 0,
                        'gstNumber' => session('gstNumber') ?? NULL,
                        'gstCompanyName' => session('gstCompanyName') ?? NULL
                    ];
                    $order_id = OrderManager::generate_order($data);

                    session()->forget('wallet_deduct_amount');
                    session()->forget('iteam_amounts');
                    session()->forget('instant_delivery');
                    session()->forget('gstNumber');
                    session()->forget('gstCompanyName');

                    try {
                        //create shiprocket order
                        $order = Order::find($order_id);
                        $shiprocketService->createOrder($order);
                    } catch (\Exception $e) {
                        // return response()->json($e->getMessage());
                    }

                    array_push($order_ids, $order_id);
                }
            }
            CartManager::cart_clean();
        } catch (\Exception $exception) {
            Toastr::error('Payment process failed');
            return back();
        }

        if (session()->has('payment_mode') && session('payment_mode') == 'app') {
            return redirect()->route('payment-success');
        }

        return view(VIEW_FILE_NAMES['order_complete']);
    }

    public function success()
    {
        if (auth('customer')->check()) {
            Toastr::success('Payment success.');
            return redirect('/account-oder');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (auth('customer')->check()) {
            Toastr::error('Payment failed.');
            return redirect('/account-oder');
        }

        session()->forget('wallet_deduct_amount');
        session()->forget('iteam_amounts');
        session()->forget('instant_delivery');
        session()->forget('gstNumber');
        session()->forget('gstCompanyName');

        return response()->json(['message' => 'Payment failed'], 403);
    }

    public function label(string $awb, ShipyaariService $shipyaari)
    {
        // Service direct force-download/redirect karega
        return $shipyaari->downloadLabel($awb);
    }
}
