<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CustomerManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCountryCode;
use App\Model\DeliveryZipCode;
use App\Model\Order;
use App\Model\Product;
use App\Model\Membership;
use App\Model\Review;
use App\Model\CustomerWalletHistory;
use App\Model\OrderDetail;
use App\Model\ShippingAddress;
use App\Model\SupportTicket;
use App\Model\SupportTicketConv;
use App\Model\Wishlist;
use App\Model\ShiprocketCourier;
use App\Traits\CommonTrait;
use App\CPU\ProductManager;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Model\Contact;
use Illuminate\Support\Facades\Mail;
use function App\CPU\translate;


class CustomerController extends Controller
{
    use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function contact_store(Request $request)
    {
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile_number = $request->mobile_number;
        $contact->business_name = $request->business_name;
        $contact->message = $request->message;
        $contact->save();

        Mail::to('customersupport@interiorchowk.com')->send(new \App\Mail\CallBackRequest($request));

        return response()->json(['message' => 'Your Message Send Successfully'], 200);
    }

    public function status_update(Request $request)
    {
        User::where(['id' => $request->user()->id])->update([
            'is_active' => 0
        ]);

        DB::table('oauth_access_tokens')
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'account has blocked'], 200);
    }

    public function getuser(Request $request)
    {
        $user = User::where('id', $request->user()->id)
            ->first();

        if ($user != null) {

            if ($user->is_active == 0) {
                return response()->json([
                    'message' => 'Your account has been blocked!, please contact the admin at customersupport@interiorchowk.com',
                    'data' => null,
                    'status' => false
                ], 200);
            }

            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json([
                'message' => translate(''),
                'data' => $user,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'This account does not  exist!',
                'data' => null,
                'status' => false
            ], 200);
        }
    }

    public function get_invoice(Request $request)
    {
        $company_name = $request->company_name;
        $company_name = urlencode($company_name);
        $download_url = url('/') . '/seller/order-invoice-gst/' . $request->order_id;

        return response()->json(['link' => $download_url], 200);
    }

    public function wallet_apply(Request $request)
    {
        $user = User::Where('id', $request->user_id)->first();
        if ($user->wallet_balance) {
            $amount =   $request->product_amount;

            if ($user->wallet_balance - $amount > 0) {
                $amount = number_format((float)$amount, 2, '.', '');
            } else {
                $amount = number_format($user->wallet_balance, 2);
            }
            return response()->json(['wallet_deduct_amount' => $amount, 'message' => 'apply amount'], 200);
        } else {
            $amount = 0;
            return response()->json(['wallet_deduct_amount' => $amount, 'message' => 'no amount in your wallet'], 200);
        }
    }

    public function plan_list(Request $request)
    {

        $plan =  Membership::where('status', 1)->get();
        if ($plan) {
            return response()->json(['plan-list' => $plan], 200);
        } else {
            return response()->json(['message' => 'Plan not exit !'], 403);
        }
    }

    public function add_gst_details(Request $request)
    {
        $order =  Order::where('id', $request->order_id)->first();
        if ($order) {
            $order->company_name = $request->company_name;
            $order->gst_number = $request->gst_number;
            $order->save();
            return response()->json(['message' => 'successfully submitted'], 200);
        } else {
            return response()->json(['message' => 'order id not exit !'], 403);
        }
    }

    public function create_support_ticket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $request['customer_id'] = $request->user()->id;
        $request['priority'] = 'low';
        $request['status'] = 'pending';

        try {
            CustomerManager::create_support_ticket($request);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'failed',
                    'message' => 'Something went wrong',
                ],
            ], 422);
        }
        return response()->json(['message' => 'Support ticket created successfully.'], 200);
    }
    
    public function account_delete(Request $request, $id)
    {
        if ($request->user()->id == $id) {
            $user = User::find($id);

            ImageManager::delete('/profile/' . $user['image']);

            $user->delete();
            return response()->json(['message' => translate('Your_account_deleted_successfully!!')], 200);
        } else {
            return response()->json(['message' => 'access_denied!!'], 403);
        }
    }

    public function reply_support_ticket(Request $request, $ticket_id)
    {
        $support = new SupportTicketConv();
        $support->support_ticket_id = $ticket_id;
        $support->admin_id = 1;
        $support->customer_message = $request['message'];
        $support->save();
        return response()->json(['message' => 'Support ticket reply sent.'], 200);
    }

    public function get_support_tickets(Request $request)
    {
        return response()->json(SupportTicket::where('customer_id', $request->user()->id)->get(), 200);
    }

    public function get_support_ticket_conv($ticket_id)
    {
        return response()->json(SupportTicketConv::where('support_ticket_id', $ticket_id)->get(), 200);
    }

    public function add_to_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();
        if (empty($wishlist)) {
            $wishlist = new Wishlist;
            $wishlist->customer_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();

            return response()->json(['message' => translate('successfully added!')], 200);
        }

         $product = Product::find($request->product_id);
         if ($product) {
               $fcm = new \App\FirebaseServices\FirebaseNotificationService;
               $fcmResponse = $fcm->sendNotificationToWishlistofCustomer($product);
        }

        return response()->json(['message' => translate('Already in your wishlist'),
        'fcmresponse' => $fcmResponse ], 409);
    }

    public function remove_from_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            Wishlist::where(['customer_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => translate('successfully removed!')], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function wish_list(Request $request)
    {
        $wishlist = Wishlist::where('customer_id', $request->user()->id)->get();
        $data = [];
        $productIds = [];
        // print_r($wishlist);die();
        foreach ($wishlist as $wishlists) {
            if ($wishlists->product) {
                $productIds[] = $wishlists->product->id;
                $data[] = $wishlists->product;
            }
        }

        // SKU product data fetch karein
        $sku_products = DB::table('sku_product_new')
            ->whereIn('product_id', $productIds)
            ->whereNotNull('thumbnail_image')
            ->select('product_id', 'thumbnail_image', 'image', 'listed_price', 'discount', 'discount_type', 'variant_mrp')
            ->get();

        // SKU product data ko map karein
        $sku_product_map = [];
        foreach ($sku_products as $sku) {
            $sku_product_map[$sku->product_id] = $sku;
        }

        // Wishlist products me SKU data merge karein
        $wishlist = $wishlist->map(function ($wish) use ($sku_product_map) {
            if ($wish->product && isset($sku_product_map[$wish->product->id])) {
                $sku = $sku_product_map[$wish->product->id];
                $wish->product->thumbnail = $sku->thumbnail_image;
                $wish->product->images = json_decode($sku->image, true);
                $wish->product->unit_price = $sku->variant_mrp;
                $wish->product->discount = $sku->discount;
                $wish->product->discount_type = $sku->discount_type;
            }
            return $wish;
        });

        // Related products fetch karein
        $related_products = ProductManager::get_related_productsss($productIds);

        return response()->json([
            'wishlist' => $wishlist,
            'related_products' => $related_products
        ], 200);
    }

    public function address_list(Request $request)
    {
        $address = ShippingAddress::where('customer_id', $request->user()->id)->latest()->get();

        return response()->json([
            'status' => "success",
            'data'   => $address
        ], 200);
    }

    public function get_address(Request $request, $id)
    {
        $address = ShippingAddress::where('id', $id)->where('customer_id', $request->user()->id)->first();

        return response()->json([
            'status' => "success",
            'data'   => $address
        ], 200);
    }

    public function add_new_address(Request $request)
    {


        /* 'latitude' => 'required',
            'longitude' => 'required',
            'latitude' => $request->latitude ?? NULL,
            'longitude' => $request->longitude ?? NULL,
            */
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'phone' => 'required',
            'is_billing' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

        /* if ($country_restrict_status && !self::delivery_country_exist_check($request->input('country'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

        }*/
        if ($zip_restrict_status && !self::delivery_zipcode_exist_check($request->input('zip'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
        }

        $address = [
            'customer_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'city' => $request->city,
            'zip' => $request->zip,
            'state' => $request->state,
            'phone' => $request->phone,
            'landmark' => $request->landmark ?? NULL,
            'is_billing' => $request->is_billing,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        ShippingAddress::insert($address);

        return response()->json(['message' => translate('successfully added!')], 200);
    }

    public function update_address(Request $request)
    {
        $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->id])->first();
        if (!$shipping_address) {
            return response()->json(['message' => translate('not_found')], 200);
        }

        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

        if ($zip_restrict_status && !self::delivery_zipcode_exist_check($request->input('zip'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
        }

        $shipping_address->update([
            'customer_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'city' => $request->city,
            'zip' => $request->zip,
            'state' => $request->state,
            'phone' => $request->phone,
            'landmark' => $request->landmark ?? NULL,
            'is_billing' => $request->is_billing,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => translate('update_successful')], 200);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->first()) {
            DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function get_order_list(Request $request)
    {
        $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->get();
        $orders->map(function ($data) {

            $orderDetails = DB::table('order_details')->where('order_id', $data->id)->get();

            $skuProducts = $orderDetails->map(function ($ord) {

                return DB::table('products')
                    ->join('sku_product_new', function ($join) use ($ord) {
                        $join->on('products.id', '=', 'sku_product_new.product_id')
                            ->where('sku_product_new.variation', '=', $ord->variant);
                    })
                    ->where('products.id', $ord->product_id)
                    ->select('products.name as name', 'sku_product_new.*')
                    ->first();
            });

            $data['sku_product'] = $skuProducts;
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
        return response()->json($orders, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::with('seller.shop')
            ->whereHas('order', function ($query) use ($request) {
                $query->where(['customer_id' => $request->user()->id]);
            })
            ->where(['order_id' => $request['order_id']])
            ->get();

        $order = Order::where("id", $request->order_id)->select("id", "shipping_cost", "updated_at", "order_status")->first();

        $product_id = OrderDetail::where("order_id", $request->order_id)->select("product_id", "variant")->first();
        $variant_name = OrderDetail::where("order_id", $request->order_id)->select("variant")->first();
        //dd($request->order_id);
        $return_day = DB::table('products')->where('id', $product_id->product_id)->first();
        $return_day->Return_days;
        
        $givenDate = Carbon::parse($order->updated_at)->toDateString();
        $futureDate = Carbon::parse($givenDate)->addDays($return_day->Return_days)->toDateString();

        $currentDate = Carbon::now()->toDateString();
        if ($currentDate <= $futureDate) {
        }

        $diffInDays = Carbon::parse($givenDate)->diffInDays($currentDate);

        $reviewable = 0;
        $reviewExist = Review::where('order_id', $request->order_id)->first();

        $shiproket_status = ShiprocketCourier::where('order_id', $request->order_id)->value('status');
        if ($shiproket_status) {
            if ($shiproket_status == 'Delivered') {
                $order->payment_status = 'paid';
                $order->save();
            }
            $shipping = ShiprocketCourier::where("order_id", $request->order_id)->first();
            if ($shipping) {
                $order = Order::where('id', $request->order_id)->first();
                if ($order) {
                    if ($order->order_status != 'canceled') {
                        if ($shipping->status == 'NEW') {
                            $order->order_status = 'confirmed';
                        } elseif ($shipping->status == 'PICKUP SCHEDULED') {
                            $order->order_status = 'processing';
                        } elseif ($shipping->status == 'Out for Delivery') {
                            $order->order_status = 'out_for_delivery';
                        } elseif ($shipping->status == 'Delivered') {
                            $order->order_status = 'delivered';
                        }
                        $order->save();
                    }
                }
            }

            if ($shiproket_status == 'BOOKED' || $shiproket_status == 'NOT PICKUP') {
                $order->isCancelable = 1;
                $order->return_timeout = 0;
            }

               else if ($shiproket_status == 'DELIVERED') {
                    //$order->payment_status = 'paid';
                    //$order->save();
                    $shiproket_status_date = ShiprocketCourier::where('order_id', $request->order_id)->value('delivered_at');
                    $givenDate = Carbon::parse($shiproket_status_date)->toDateString();
                    $futureDate = Carbon::parse($givenDate)->addDays($return_day->Return_days)->toDateString();
                    if ($currentDate <= $futureDate) {
                        $order->return_timeout = 1;
                    } else {
                        $order->return_timeout = 0;
                    }
                    $order->isCancelable = 0;
                } 
                
                else {
                    $order->return_timeout = 1;
                    $order->isCancelable = 0;
                }
        }

        if ($order->order_status == 'canceled') {
            $order->isCancelable = 0;
        }

        if ($shiproket_status == 'CANCELED') {
            $order->isCancelable = 0;
        }

        $details = $details->map(function ($query) use ($order, $request) {
            // dd($query->variant);

            $query['variation'] = json_decode($query['variation'], true);
            //$query['product_details'] = Helpers::product_data_formatting(json_decode($query['product_details'], true));
            $productDetails = json_decode($query['product_details'], true);
            // dd($productDetails);
            $productDetails = Helpers::product_data_formatting($productDetails);
            // dd($productDetails['id']);
            //$productDetails['refundable']
            $return_day = DB::table('products')->where('id', $productDetails['id'])->first();
            $return_day->Return_days;
            $sku_product_new  = DB::table('sku_product_new')->where('product_id', $query->product_id)->where('variation', $query->variant)->select('image as thumbnail_image')->first();
            $thumbnail_image = json_decode($sku_product_new->thumbnail_image, true);
            $query['sku_product_new'] = [
                'thumbnail_image' => $thumbnail_image[0] ?? null
            ];

            $givenDate = Carbon::parse($order->updated_at)->toDateString();
            $futureDate = Carbon::parse($givenDate)->addDays($return_day->Return_days)->toDateString();
            $currentDate = Carbon::now()->toDateString();
            $diffInDays = Carbon::parse($givenDate)->diffInDays($currentDate);
            $reviewable = 0;
            $reviewExist = Review::where('order_id', $request->order_id)->where('product_id', $productDetails['id'])->first();
            $shiproket_status = ShiprocketCourier::where('order_id', $request->order_id)->value('status');

            if ($shiproket_status) {
                /* if($shiproket_status == 'NEW' || $shiproket_status == 'PICKUP SCHEDULED' || $shiproket_status == 'ARCHIVED' || $shiproket_status == 'Delivered' || $shiproket_status == 'Out for Delivery')*/
                if ($shiproket_status) {
                    if ($shiproket_status == 'Delivered' && $productDetails['refundable']) {
                        $shiproket_status_date = ShiprocketCourier::where('order_id', $request->order_id)->value('updated_at');
                        $givenDate = Carbon::parse($shiproket_status_date)->toDateString();
                        $futureDate = Carbon::parse($givenDate)->addDays($return_day->Return_days)->toDateString();
                        if ($currentDate <= $futureDate) {
                            $productDetails['refundable'] = 1;
                        } else {
                            $productDetails['refundable'] = 0;
                        }
                    } else {
                        $productDetails['refundable'] = 0;
                    }

                    if ($shiproket_status == 'Delivered' && $reviewExist == false) {
                        $reviewable = 1;
                    }
                    // $order->isCancelable = 1;
                } else {
                    //$order->isCancelable = 0;
                }
            } else {

                if ($order->order_status == 'delivered' && $productDetails['refundable']) {

                    if ($currentDate <= $futureDate) {
                        $productDetails['refundable'] = 1;
                    } else {
                        $productDetails['refundable'] = 0;
                    }
                } else {
                    $productDetails['refundable'] = 0;
                }

                if ($order->order_status == 'delivered' && $reviewExist == false) {
                    $reviewable = 1;
                }

                if ($order->order_status == 'confirmed' || $order->order_status == 'processing' || $order->order_status == 'pending' || $order->order_status == 'out_for_delivery') {

                    // $order->isCancelable = 1;
                } else {
                    //$order->isCancelable = 0;
                }
            }

            if ($order->order_status == 'canceled') {
                $productDetails['refundable'] = 0;
            }

            if ($shiproket_status == 'CANCELED') {
                $productDetails['refundable'] = 0;
            }

            $productDetails['reviewable'] = $reviewable;
            $query['product_details'] = $productDetails; // Ensure it's an array here CANCELED
            $query['tax'] = number_format($query['tax'], 2);
            return $query;
        });

        // $order = Order::where(['id' => $request['order_id']])->first();
        // $order['shipping_address_data'] = json_decode($order['shipping_address_data']);
        // $order['billing_address_data'] = json_decode($order['billing_address_data']);

        return response()->json(['details' => $details, 'order' => $order], 200);
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
            'l_name.required' => translate('Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = ImageManager::update('profile/', $request->user()->image, 'png', $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $user = User::find($request->user()->id);

        if (User::where('email', $request->email)->whereNotIn('email', [$user->email])->first()) {
            return response()->json(['message' => translate('This e-mail id already registered with us. Try with different email id!')], 200);
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now(),
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        DB::table('users')->where('id', $request->user()->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token'],
        ]);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function get_restricted_country_list(Request $request)
    {
        $stored_countries = DeliveryCountryCode::orderBy('country_code', 'ASC')->pluck('country_code')->toArray();
        $country_list = COUNTRIES;

        $countries = array();

        foreach ($country_list as $country) {
            if (in_array($country['code'], $stored_countries)) {
                $countries[] = $country['name'];
            }
        }

        if ($request->search) {
            $countries = array_values(preg_grep('~' . $request->search . '~i', $countries));
        }

        return response()->json($countries, 200);
    }

    public function get_restricted_zip_list(Request $request)
    {
        $zipcodes = DeliveryZipCode::orderBy('zipcode', 'ASC')
            ->when($request->search, function ($query) use ($request) {
                $query->where('zipcode', 'like', "%{$request->search}%");
            })
            ->get();

        return response()->json($zipcodes, 200);
    }
}