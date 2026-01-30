<?php

namespace App\Http\Controllers\Web;

use App\CPU\CustomerManager;
use App\CPU\CartManager;
use App\Model\CartShipping;
use App\Model\ShippingMethod;
use App\Model\HelpTopic;
use App\Model\ShippingType;
use App\Model\WithdrawRequest;
use App\Model\OrderDetail;
use App\Model\Cart;
use App\Services\ShiprocketService;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCountryCode;
use App\Model\DeliveryMan;
use App\Model\DeliveryZipCode;
use App\Model\Order;
use Illuminate\Support\Facades\Validator;
use App\Model\Product;
use App\Model\Review;
use App\Model\Seller;
use App\Model\ShippingAddress;
use App\Model\SupportTicket;
use App\Model\Wishlist;
use App\Model\Coupon;
use App\Model\RefundRequest;
use App\Traits\CommonTrait;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use function App\CPU\translate;
use App\CPU\Convert;
use App\Model\HelpTopicSubCategory;
use App\Model\ProductCompare;
use App\Services\ShipyaariService;
use Illuminate\Support\Facades\Http;

use function React\Promise\all;

class UserProfileController extends Controller
{
    use CommonTrait;

    public function __construct(
        private Order $order,
        private Seller $seller,
        private Product $product,
        private Review $review,
        private DeliveryMan $deliver_man,
        private ProductCompare $compare,
        private Wishlist $wishlist,
    )
    {

    }

    public function user_profile_1()
    {
             $id = auth()->user()->id;
             //dd($ids);
        $user = User::where('id', $id )->first();
        $orders = DB::table('orders')
        ->join('order_details', function($join) {
            $join->on('orders.id', '=', 'order_details.order_id');
        })
        ->join('sku_product_new', function($join) {
            $join->on('order_details.product_id', '=', 'sku_product_new.product_id');
        })
        ->whereRaw("CONVERT(order_details.variant USING utf8mb4) = CONVERT(sku_product_new.variation USING utf8mb4)")
        ->where('orders.customer_id', $id)
        ->select(
            'orders.*',
            'order_details.order_id',
            'order_details.product_id as product_ids',
            'sku_product_new.image'
        )
        ->get();

        $walletCustomer = DB::table('wallet_transactions')
        ->where('user_id', $id)
        ->get();

        $withdrawable = DB::table('wallet_transactions')
        ->where('user_id', $id)
        ->sum('credit');

        $wallets = DB::table('customer_wallet_histories')
        ->where('customer_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();
           //dd($wallet);

        $addresses = DB::table('shipping_addresses')->where('customer_id', $id)->get();

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
            'sku_product_new.discount_type',
            'sku_product_new.discount',
            'sku_product_new.listed_price',
            'sku_product_new.variant_mrp'
        )
        ->where('wishlists.customer_id', $id)
        ->get();

        $recently_viewed = DB::table('recently_view')
        ->join('sku_product_new', function($join) {
            $join->on('recently_view.product_id', '=', 'sku_product_new.product_id');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->select(
            'recently_view.*',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.slug',
            'products.name'
        )
        ->where('recently_view.user_id', $id)
        ->whereNotNull('thumbnail_image')
        ->get();

        $cart = DB::table('new_cart')
        ->join('sku_product_new', function($join) {
            $join->on('new_cart.product_id', '=', 'sku_product_new.product_id');
        })
        ->join('products', function($join) {
            $join->on('sku_product_new.product_id', '=', 'products.id');
        })
        ->whereRaw("CONVERT(new_cart.variation USING utf8mb4) = CONVERT(sku_product_new.id USING utf8mb4)")
        ->where('new_cart.user_id', $id)
        ->select(
            'new_cart.*',
            'new_cart.id as cart_id',
            'new_cart.quantity as cart_qty',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.name',
            'products.slug'
        )
        ->get();

        $wallet = HelpTopicSubCategory::where('sub_cat_name','Wallet')->first();
        // dd($wallet);
        $referandearn =  HelpTopicSubCategory::where('sub_cat_name','Refer & Earn')->first();
        $personaldetails =  HelpTopicSubCategory::where('sub_cat_name','Personal Details')->first();
        $faqwallet = HelpTopic::where('sub_cat_id',$wallet->id)->get();
        $faqreferandearn = HelpTopic::where('sub_cat_id',$referandearn->id)->get();
        $faqpersonaldetails = HelpTopic::where('sub_cat_id',$personaldetails->id)->get();

        return view('web.user_profile', compact('withdrawable','walletCustomer','faqpersonaldetails','faqreferandearn','faqwallet','user','orders','wallet','wallets','addresses','wishlists','recently_viewed','cart'));
    }

    public function view_cart()
    {
        $id = auth()->user()->id;
           // dd($id);
        $cart = DB::table('new_cart')
        ->join('sku_product_new', function($join) {
            $join->on('new_cart.variation', '=', 'sku_product_new.id');

        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->select(
            'new_cart.*',
            'new_cart.id as cart_id',
            'new_cart.quantity as cart_qty',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.name',
            'products.Return_days',
            'products.free_delivery',
            'products.slug'
        )
        ->where('new_cart.user_id', $id)

        ->get();

        session()->put('cart', $cart);

        $recently_viewed = DB::table('recently_view')
        ->join('sku_product_new', function($join) {
            $join->on('recently_view.product_id', '=', 'sku_product_new.product_id');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->select(
            'recently_view.*',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.slug',
            'products.name',
            'products.free_delivery'
        )
        ->where('recently_view.user_id', $id)
        ->get();
      //dd($cart);
        return view('web.cart',compact('cart','recently_viewed'));
    }

    public function view_wishlist()
    {
       $id = auth()->user()->id;
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
            'sku_product_new.discount_type',
            'sku_product_new.discount',
            'sku_product_new.listed_price',
            'sku_product_new.variant_mrp'
        )
        ->where('wishlists.customer_id', $id)
        ->get();

        $recently_viewed = DB::table('recently_view')
        ->join('sku_product_new', function($join) {
            $join->on('recently_view.product_id', '=', 'sku_product_new.product_id');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->select(
            'recently_view.*',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.slug',
            'products.name'
        )
        ->where('recently_view.user_id', $id)
        ->get();

        return view('web.wishlist',compact('wishlists','recently_viewed'));
    }

    public function checkout(Request $request)
    {
        if(isset($request->p)){

            $carts = collect(session()->get('product_checkout_directly', []))
            // ->where('is_selected', 1)
            ->values();
        }
        else{
            $carts = collect(session()->get('cart', []))
            ->where('is_selected', 1)
            ->values();
        }
        $total_variant_mrp = 0;
        $total_listed_price = 0;
        $cart_group_id = [];

        foreach ($carts as $item) {
            $total_variant_mrp += ($item->variant_mrp ?? 0) * ($item->cart_qty);
            $total_listed_price += ($item->listed_price ?? 0) * ($item->cart_qty);
            $cart_group_id[] = $item->cart_group_id;
        }
        $request_seller_ids_string = $request->input('seller_id', '266');
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

        // Loop through seller IDs and add corresponding coupons
        foreach ($request_seller_ids as $seller_id) {
            $seller_id = isset($seller_id) ? $seller_id : 0;
            // dd($seller_id);
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
        $coupon_discount = $coupon_discount->unique('id'); 
        return view('web.checkout', compact('carts', 'total_variant_mrp', 'total_listed_price', 'cart_group_id', 'coupon_discount'));
    }

    public function save_address(Request $request)
    {

        $data = [
            'customer_id'=>auth()->user()->id,
            'contact_person_name'=>$request->contact_person_name,
            'address_type'=>$request->address_type,
            'address'=>$request->address,
            'zip'=>$request->zip,
            'city'=>$request->city,
            'state'=>$request->state,
            'country'=>'India',
            'landmark'=>$request->landmark,
            'phone'=>$request->contact,
            'is_selected'=>0,
            'created_at'=>Now(),
            'updated_at'=>Now()

        ];
        // dd($data);

        $table = DB::table('shipping_addresses')->insert( $data);

        return response()->json([
            'status' => 'success'
        ], 200);

    }


    // public function get_shipping_cost(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'cart_group_id' => 'required|array',
    //         'address_id' => 'required',
    //         'payment_method' => 'nullable'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => Helpers::error_processor($validator)]);
    //     }

    //     $selected_group_ids = $request->cart_group_id;
    //     $totalShippingCost = 0;
    //     $free_delivery = 0;

    //         foreach ($selected_group_ids as $group_id) {

    //             $shiprocketService = new ShipyaariService();

    //             $seller_data = DB::table('new_cart')
    //                 ->where(['cart_group_id' => $group_id])
    //                 ->first();

    //             if (!$seller_data) continue;

    //             $product = Product::select("id", "weight", "free_delivery", "user_id")
    //                 ->where('id', $seller_data->product_id)
    //                 ->first();

    //             if (!$product) continue;

    //             $warehouse = DB::table('warehouse')
    //                 ->where('seller_id', $product->user_id)
    //                 ->first();

    //             if (!$warehouse) continue;

    //             $weight = 0; $length = 0; $width = 0; $height = 0;

    //             foreach (CartManager::get_carts($group_id) as $c) {
    //                 $prs = DB::table('sku_product_new')
    //                     ->where('product_id', $c->product_id)
    //                     ->where('id', $c->variation)
    //                     ->get();

    //                 foreach ($prs as $pr) {

    //                     $w = is_numeric($pr->weight) ? floatval($pr->weight) : 0;
    //                     $l = is_numeric($pr->length) ? floatval($pr->length) : 0;
    //                     $b = is_numeric($pr->breadth) ? floatval($pr->breadth) : 0;
    //                     $h = is_numeric($pr->height) ? floatval($pr->height) : 0;

    //                     $q = is_numeric($c->quantity) ? intval($c->quantity) : 0;

    //                     $weight += $w * $q;
    //                     $length += $l * $q;
    //                     $width  += $b * $q;
    //                     $height += $h * $q;

    //                 }
    //             }

    //             $free_delivery = $product->free_delivery ?? 0;

    //             if ($weight > 0) {

    //                 $shippingAddress = ShippingAddress::find($request['address_id']);
    //                 $shippingPrice = $shiprocketService->checkForAvailability([
    //                     'pickupPincode' => (int) $warehouse->pincode,
    //                     'deliveryPincode' => (int) ($shippingAddress->zip ?? 0),
    //                     'weight' => $weight,
    //                     'paymentMode' => ($request->iscod === '0' ? 'PREPAID' : 'COD'),
    //                     'orderType' => 'B2C',
    //                     'invoiceValue' => 10,
    //                     'mobileNo' => $shippingAddress->phone, // ⭐ REQUIRED ⭐
    //                     'dimension' => [
    //                         'length' => $length,
    //                         'width'  => $width,
    //                         'height' => $height
    //                     ]
    //                 ]);
                    
    //                 if (
    //                     empty($shippingPrice) ||
    //                     empty($shippingPrice['data']) ||
    //                     !isset($shippingPrice['data']->total) ||
    //                     !is_numeric($shippingPrice['data']->total)
    //                 ) {
    //                     // No valid price
    //                     continue;
    //                 }
                    
    //                 $instant_delivery = !empty($request->instant_delivery);

    //                 $rawTotal = $shippingPrice['data']->total ?? 0;

    //                 // CLEAN VALUE – FIXES THE ERROR
    //                 $cost = (is_numeric($rawTotal) ? floatval($rawTotal) : 0);


    //                 if ($instant_delivery) {
    //                     $cost = ($cost <= 100) ? 100 : ($cost * 1.50);
    //                 }

    //                 $shipping = CartShipping::firstOrNew(['cart_group_id' => $group_id]);
    //                 $shiprocket = ShippingMethod::where("title", "Shiprocket")->first();

    //                 $shipping->shipping_method_id = $shiprocket->id ?? null;
    //                 $shipping->shipping_cost = $cost;
    //                 $shipping->save();

    //                 $totalShippingCost += $cost;
    //             }
    //         }

    //         return response()->json([
    //             'cod' => $request->iscod,
    //             'status' => 'success',
    //             'free_delivery' => $free_delivery,
    //             'shipping_cost' => $totalShippingCost,
    //             'edt' => $shippingPrice['data']->EDT,
    //             'message' => 'Total shipping cost applied only to selected checkout products'
    //         ]);
    // }

    public function get_shipping_cost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_group_id' => 'required|array',
            'address_id' => 'required',
            'payment_method' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $selected_group_ids = $request->cart_group_id;
        $totalShippingCost = 0;
        $free_delivery = 0;

        // ✅ PRODUCT / GROUP WISE EDT STORE
        $productWiseEdt = [];

        foreach ($selected_group_ids as $group_id) {

            $shiprocketService = new ShipyaariService();

            $seller_data = DB::table('new_cart')
                ->where('cart_group_id', $group_id)
                ->first();

            if (!$seller_data) continue;

            $product = Product::select("id", "weight", "free_delivery", "user_id")
                ->where('id', $seller_data->product_id)
                ->first();

            if (!$product) continue;

            $warehouse = DB::table('warehouse')
                ->where('seller_id', $product->user_id)
                ->first();

            if (!$warehouse) continue;

            $weight = 0; 
            $length = 0; 
            $width = 0; 
            $height = 0;

            foreach (CartManager::get_carts($group_id) as $c) {

                $prs = DB::table('sku_product_new')
                    ->where('product_id', $c->product_id)
                    ->where('id', $c->variation)
                    ->get();

                foreach ($prs as $pr) {

                    $w = is_numeric($pr->weight) ? (float)$pr->weight : 0;
                    $l = is_numeric($pr->length) ? (float)$pr->length : 0;
                    $b = is_numeric($pr->breadth) ? (float)$pr->breadth : 0;
                    $h = is_numeric($pr->height) ? (float)$pr->height : 0;
                    $q = is_numeric($c->quantity) ? (int)$c->quantity : 0;

                    $weight += $w * $q;
                    $length += $l * $q;
                    $width  += $b * $q;
                    $height += $h * $q;
                }
            }

            $free_delivery = $product->free_delivery ?? 0;

            if ($weight <= 0) continue;

            $shippingAddress = ShippingAddress::find($request->address_id);
            if (!$shippingAddress) continue;

            $shippingPrice = $shiprocketService->checkForAvailability([
                'pickupPincode'   => (int)$warehouse->pincode,
                'deliveryPincode' => (int)$shippingAddress->zip,
                'weight'          => $weight,
                'paymentMode'     => ($request->iscod === '0' ? 'PREPAID' : 'COD'),
                'orderType'       => 'B2C',
                'invoiceValue'    => 10,
                'mobileNo'        => $shippingAddress->phone,
                'dimension' => [
                    'length' => $length,
                    'width'  => $width,
                    'height' => $height
                ]
            ]);

            // ❌ Invalid API response
            if (
                empty($shippingPrice) ||
                empty($shippingPrice['data']) ||
                !isset($shippingPrice['data']->total)
            ) {
                continue;
            }

            /* ---------------- COST ---------------- */
            $cost = is_numeric($shippingPrice['data']->total)
                    ? (float)$shippingPrice['data']->total
                    : 0;

            if (!empty($request->instant_delivery)) {
                $cost = ($cost <= 100) ? 100 : ($cost * 1.5);
            }

            /* ---------------- EDT ---------------- */
            $edt = isset($shippingPrice['data']->EDT) && is_numeric($shippingPrice['data']->EDT)
                    ? (int)$shippingPrice['data']->EDT
                    : null;

            // ✅ SAVE EDT PER CART GROUP
            $productWiseEdt[$group_id] = $edt;

            /* ---------------- SAVE SHIPPING ---------------- */
            $shipping = CartShipping::firstOrNew(['cart_group_id' => $group_id]);
            $shiprocket = ShippingMethod::where('title', 'Shiprocket')->first();

            $shipping->shipping_method_id = $shiprocket->id ?? null;
            $shipping->shipping_cost = $cost;
            $shipping->estimated_delivery_days = $edt; // optional DB column
            $shipping->save();

            $totalShippingCost += $cost;
        }

        return response()->json([
            'status' => 'success',
            'cod' => $request->iscod,
            'free_delivery' => $free_delivery,
            'shipping_cost' => $totalShippingCost,
            'edt' => $productWiseEdt, // ✅ PRODUCT WISE EDT
            'message' => 'Shipping & EDT calculated successfully'
        ]);
    }

    public function address_store_1(Request $request)
    {
        $request->validate([
            'contact_person_name' => 'required|string|max:255',
            'address_type' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        $shippingAddress = new ShippingAddress();
        $shippingAddress->customer_id = Auth::id();
        $shippingAddress->contact_person_name = $request->contact_person_name;
        $shippingAddress->address_type = $request->address_type;
        $shippingAddress->address = $request->address;
        $shippingAddress->city = $request->city;
        $shippingAddress->state = $request->state;
        $shippingAddress->zip = $request->zip;
        $shippingAddress->landmark = $request->landmark;
        $shippingAddress->phone = $request->phone;
        $shippingAddress->country = 'India';
        $shippingAddress->created_at = now();
        $shippingAddress->updated_at = now();
        $shippingAddress->save();

        return redirect()->back()->with('success', 'Address added successfully!');
    }

    public function address_update_1(Request $request)
    {
        $request->validate([
            'contact_person_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:10',
            'address_type' => 'required|string',
        ]);

        // Get the address ID from the hidden input
        $addressId = $request->address_id;

        // Fetch the address
        $shippingAddress = ShippingAddress::find($addressId);

        // Check if address exists
        if (!$shippingAddress) {
            Toastr::error('Address not found!');
            return redirect()->route('address.index');
        }

        // Update the address
        $shippingAddress->contact_person_name = $request->contact_person_name;
        $shippingAddress->phone = $request->phone;
        $shippingAddress->address = $request->address;
        $shippingAddress->city = $request->city;
        $shippingAddress->state = $request->state;
        $shippingAddress->zip = $request->zip;
        $shippingAddress->address_type = $request->address_type;
        $shippingAddress->landmark = $request->landmark;
        $shippingAddress->updated_at = now();

        // Save the changes
        $shippingAddress->save();

        return redirect()->back()->with('success', 'Address updated successfully!');
    }

    public function address_delete_1(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:shipping_addresses,id',
        ]);

        $address = ShippingAddress::find($request->address_id);

        if (!$address) {
            Toastr::error('Address not found!');
            return back();
        }

        $address->delete();

        Toastr::success('Address removed successfully!');
        return back();
    }

    // public function user_update_1(Request $request)
    // {
    //     \Log::info('User update triggered', [
    //         'user' => Auth::id(),
    //         'data' => $request->all()
    //     ]);

    //     $user = User::find(Auth::id());

    //     if (!$user) {
    //         \Log::error('Auth::id() returned null');
    //         abort(403, 'Not logged in');
    //     }

    //     $data = $request->only(['f_name', 'l_name', 'email', 'phone', 'gender']);
        
    //     $user->update($data);

    //     return redirect()->back();
    // }

    public function user_update_1(Request $request)
    {
        \Log::info('User update triggered', [
            'user' => Auth::id(),
            'data' => $request->all()
        ]);

        $user = User::find(Auth::id());

        if (!$user) {
            \Log::error('Auth::id() returned null');
            abort(403, 'Not logged in');
        }

        // VALIDATION ------------------------------
        $request->validate([
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'phone'  => 'required|digits:10',
            'gender' => 'required|in:Male,Female',
        ]);
        // -----------------------------------------

        $data = $request->only(['f_name', 'l_name', 'email', 'phone', 'gender']);

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function user_profile(Request $request)
    {
        $wishlists = $this->wishlist->whereHas('wishlistProduct', function ($q) {
            return $q;
        })->where('customer_id', auth('customer')->id())->count();
        $total_order = $this->order->where('customer_id', auth('customer')->id())->count();
        $total_loyalty_point = auth('customer')->user()->loyalty_point;
        $total_wallet_balance = auth('customer')->user()->wallet_balance;
        $addresses = ShippingAddress::where('customer_id', auth('customer')->id())->get();
        $customer_detail = User::where('id', auth('customer')->id())->first();

        return view(VIEW_FILE_NAMES['user_profile'], compact('customer_detail', 'addresses', 'wishlists', 'total_order', 'total_loyalty_point', 'total_wallet_balance'));
    }

    public function user_account(Request $request)
    {
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $customerDetail = User::where('id', auth('customer')->id())->first();
        return view(VIEW_FILE_NAMES['user_account'], compact('customerDetail'));

    }
    
    public function user_update(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
        ], [
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
        ]);
        if ($request->password) {
            $request->validate([
                'password' => 'required|min:6|same:confirm_password'
            ]);
        }

        $image = $request->file('image');

        if ($image != null) {
            $imageName = ImageManager::update('profile/', auth('customer')->user()->image, 'png', $request->file('image'));
        } else {
            $imageName = auth('customer')->user()->image;
        }

        User::where('id', auth('customer')->id())->update([
            'image' => $imageName,
        ]);

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'password' => strlen($request->password) > 5 ? bcrypt($request->password) : auth('customer')->user()->password,
        ];
        if (auth('customer')->check()) {
            User::where(['id' => auth('customer')->id()])->update($userDetails);
            Toastr::info(translate('updated_successfully'));
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function account_address_add()
    {
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $default_location = Helpers::get_business_settings('default_location');

        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;

        $zip_codes = $zip_restrict_status ? DeliveryZipCode::all() : 0;

        return view(VIEW_FILE_NAMES['account_address_add'], compact('countries', 'zip_restrict_status', 'zip_codes', 'default_location'));
    }

    public function account_delete($id)
    {
        if (auth('customer')->id() == $id) {
            $user = User::find($id);
            auth()->guard('customer')->logout();

            ImageManager::delete('/profile/' . $user['image']);
            session()->forget('wish_list');

            $user->delete();
            Toastr::info(translate('Your_account_deleted_successfully!!'));
            return redirect()->route('home');
        } else {
            Toastr::warning('access_denied!!');
        }

    }

    public function account_address()
    {
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;
        $zip_codes = $zip_restrict_status ? DeliveryZipCode::all() : 0;

        if (auth('customer')->check()) {
            $shippingAddresses = \App\Model\ShippingAddress::where('customer_id', auth('customer')->id())->get();
            return view('web-views.users-profile.account-address', compact('shippingAddresses', 'country_restrict_status', 'zip_restrict_status', 'countries', 'zip_codes'));
        } else {
            return redirect()->route('home');
        }
    }

    public function address_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'address' => 'required',
        ]);

        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        $country_exist = self::delivery_country_exist_check($request->country);
        $zipcode_exist = self::delivery_zipcode_exist_check($request->zip);

        if ($country_restrict_status && !$country_exist) {
            Toastr::error(translate('Delivery_unavailable_in_this_country!'));
            return back();
        }

        if ($zip_restrict_status && !$zipcode_exist) {
            Toastr::error(translate('Delivery_unavailable_in_this_zip_code_area!'));
            return back();
        }

        $address = [
            'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
            'contact_person_name' => $request->name,
            'address_type' => $request->addressAs,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'phone' => $request->phone,
            'is_billing' => $request->is_billing,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('shipping_addresses')->insert($address);

        Toastr::success(translate('address_added_successfully!'));
        return back();
    }

    public function address_edit(Request $request, $id)
    {
        $shippingAddress = ShippingAddress::where('customer_id', auth('customer')->id())->find($id);
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        if ($country_restrict_status) {
            $delivery_countries = self::get_delivery_country_array();
        } else {
            $delivery_countries = 0;
        }
        if ($zip_restrict_status) {
            $delivery_zipcodes = DeliveryZipCode::all();
        } else {
            $delivery_zipcodes = 0;
        }
        if (isset($shippingAddress)) {
            return view(VIEW_FILE_NAMES['account_address_edit'], compact('shippingAddress', 'country_restrict_status', 'zip_restrict_status', 'delivery_countries', 'delivery_zipcodes'));
        } else {
            Toastr::warning(translate('access_denied'));
            return back();
        }
    }

    public function address_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'address' => 'required',
        ]);

        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        $country_exist = self::delivery_country_exist_check($request->country);
        $zipcode_exist = self::delivery_zipcode_exist_check($request->zip);

        if ($country_restrict_status && !$country_exist) {
            Toastr::error(translate('Delivery_unavailable_in_this_country!'));
            return back();
        }

        if ($zip_restrict_status && !$zipcode_exist) {
            Toastr::error(translate('Delivery_unavailable_in_this_zip_code_area!'));
            return back();
        }

        $updateAddress = [
            'contact_person_name' => $request->name,
            'address_type' => $request->addressAs,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'phone' => $request->phone,
            'is_billing' => $request->is_billing,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (auth('customer')->check()) {
            ShippingAddress::where('id', $request->id)->update($updateAddress);
            Toastr::success(translate('Data_updated_successfully!'));
            return redirect()->back();
        } else {
            Toastr::error(translate('Insufficient_permission!'));
            return redirect()->back();
        }
    }

    public function address_delete(Request $request)
    {
        if (auth('customer')->check()) {
            ShippingAddress::destroy($request->id);
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function account_payment()
    {
        if (auth('customer')->check()) {
            return view('web-views.users-profile.account-payment');

        } else {
            return redirect()->route('home');
        }

    }

    public function account_oder(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        if(theme_root_path() == 'theme_fashion'){
            $show_order = $request->show_order ?? 'ongoing';

            $array = ['pending','confirmed','out_for_delivery','processing'];
            $orders = $this->order
                ->where(['customer_id'=> auth('customer')->id()])
                ->when($show_order == 'ongoing', function($query) use($array){
                    $query->whereIn('order_status',$array);
                })
                ->when($show_order == 'previous', function($query) use($array){
                    $query->whereNotIn('order_status',$array);
                })
                ->when($request['search'], function($query) use($request){
                        $query->where('id', 'like', "%{$request['search']}%");
                })
                ->orderBy('id', $order_by)->paginate(10)->appends(['show_order'=>$show_order, 'search'=>$request->search]);
        }else{
            $orders = Order::where('customer_id', auth('customer')->id())
                ->orderBy('id', $order_by)
                ->paginate(15);
        }

        return view(VIEW_FILE_NAMES['account_orders'], compact('orders', 'order_by'));
    }

    public function account_order_details(Request $request)
    {
        $order = $this->order->with(['details.product', 'delivery_man_review'])->where(['customer_id'=>auth('customer')->id()])->find($request->id);
        $refund_day_limit = \App\CPU\Helpers::get_business_settings('refund_day_limit');
        $current_date = \Carbon\Carbon::now();
        if($order){
            return view(VIEW_FILE_NAMES['account_order_details'], compact('order', 'refund_day_limit', 'current_date'));
        }

        Toastr::warning('Invalid order');
        return redirect('account-oder');
    }

    public function account_order_details_seller_info(Request $request)
    {
        $order = $this->order->with(['seller.shop'])->find($request->id);
        $product_ids = $this->product->where(['added_by' => $order->seller_is , 'user_id'=>$order->seller_id])->pluck('id');
        $rating = $this->review->whereIn('product_id', $product_ids);
        $avg_rating = $rating->avg('rating') ?? 0 ;
        $rating_percentage = round(($avg_rating * 100) / 5);
        $rating_count = $rating->count();
        $product_count = $this->product->where(['added_by' => $order->seller_is , 'user_id'=>$order->seller_id])->active()->count();

        return view(VIEW_FILE_NAMES['seller_info'], compact('avg_rating', 'product_count', 'rating_count', 'order', 'rating_percentage'));
    }

    public function account_order_details_delivery_man_info(Request $request)
    {
        $order = $this->order->with(['delivery_man.rating', 'delivery_man'=>function($query){
                return $query->withCount('review');
            }])
            ->find($request->id);
        $delivered_count = $this->order->where(['order_status' => 'delivered', 'delivery_man_id' => $order->delivery_man_id, 'delivery_type' => 'self_delivery'])->count();

        return view(VIEW_FILE_NAMES['delivery_man_info'], compact('delivered_count', 'order'));
    }

    public function account_wishlist()
    {
        if (auth('customer')->check()) {
            $wishlists = Wishlist::where('customer_id', auth('customer')->id())->get();
            return view('web-views.products.wishlist', compact('wishlists'));
        } else {
            return redirect()->route('home');
        }
    }

    public function account_tickets()
    {
        if (auth('customer')->check()) {
            $supportTickets = null;
            if(theme_root_path() != 'default') {
                $supportTickets = SupportTicket::where('customer_id', auth('customer')->id())->latest()->paginate(10);
            }
            return view(VIEW_FILE_NAMES['account_tickets'], compact('supportTickets'));
        } else {
            return redirect()->route('home');
        }
    }

    public function ticket_submit(Request $request)
    {
        $ticket = [
            'subject' => $request['ticket_subject'],
            'type' => $request['ticket_type'],
            'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
            'priority' => $request['ticket_priority'],
            'description' => $request['ticket_description'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('support_tickets')->insert($ticket);
        return back();
    }

    public function single_ticket(Request $request)
    {
        $ticket = SupportTicket::where('id', $request->id)->first();
        return view(VIEW_FILE_NAMES['ticket_view'], compact('ticket'));
    }

    public function comment_submit(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required',
        ], [
            'comment.required' => 'Type something',
        ]);

        DB::table('support_tickets')->where(['id' => $id])->update([
            'status' => 'open',
            'updated_at' => now(),
        ]);

        DB::table('support_ticket_convs')->insert([
            'customer_message' => $request->comment,
            'support_ticket_id' => $id,
            'position' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back();
    }

    public function support_ticket_close($id)
    {
        DB::table('support_tickets')->where(['id' => $id])->update([
            'status' => 'close',
            'updated_at' => now(),
        ]);
        Toastr::success('Ticket closed!');
        return redirect('/account-tickets');
    }

    public function account_transaction()
    {
        $customer_id = auth('customer')->id();
        $customer_type = 'customer';
        if (auth('customer')->check()) {
            $transactionHistory = CustomerManager::user_transactions($customer_id, $customer_type);
            return view('web-views.users-profile.account-transaction', compact('transactionHistory'));
        } else {
            return redirect()->route('home');
        }
    }

    public function support_ticket_delete(Request $request)
    {

        if (auth('customer')->check()) {
            $support = SupportTicket::find($request->id);
            $support->delete();
            return redirect()->back();
        } else {
            return redirect()->back();
        }

    }

    public function account_wallet_history($user_id, $user_type = 'customer')
    {
        $customer_id = auth('customer')->id();
        if (auth('customer')->check()) {
            $wallerHistory = CustomerManager::user_wallet_histories($customer_id);
            return view('web-views.users-profile.account-wallet', compact('wallerHistory'));
        } else {
            return redirect()->route('home');
        }

    }

    public function track_order()
    {
        return view(VIEW_FILE_NAMES['tracking-page']);
    }
    
    public function track_order_wise_result(Request $request)
    {
        if (auth('customer')->check()) {
            $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) {
                $query->where('customer_id', (auth('customer')->id()));
            })->first();
            return view(VIEW_FILE_NAMES['track_order_wise_result'], compact('orderDetails'));
        }
    }

    public function track_order_result(Request $request)
    {

        $user = auth('customer')->user();
        if (!isset($user)) {
            $user_id = User::where('phone', $request->phone_number)->first()->id;
            $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) use ($user_id) {
                $query->where('customer_id', $user_id);
            })->first();

        } else {
            if ($user->phone == $request->phone_number) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) {
                    $query->where('customer_id', auth('customer')->id());
                })->first();
            }
            if ($request->from_order_details == 1) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) {
                    $query->where('customer_id', auth('customer')->id());
                })->first();
            }

        }

        if (isset($orderDetails)) {
            return view(VIEW_FILE_NAMES['track_order'], compact('orderDetails'));
        }

        Toastr::error(translate('Invalid Order Id or Phone Number'));
        return redirect()->route('track-order.index');
    }

    public function track_last_order()
    {
        $orderDetails = OrderManager::track_order(Order::where('customer_id', auth('customer')->id())->latest()->first()->id);

        if ($orderDetails != null) {
            return view('web-views.order.tracking', compact('orderDetails'));
        } else {
            return redirect()->route('track-order.index')->with('Error', \App\CPU\translate('Invalid Order Id or Phone Number'));
        }

    }

    public function order_cancel($id)
    {
        $order = Order::where(['id' => $id])->first();
        if ($order['payment_method'] == 'cash_on_delivery' && $order['order_status'] == 'pending') {
            OrderManager::stock_update_on_order_status_change($order, 'canceled');
            Order::where(['id' => $id])->update([
                'order_status' => 'canceled'
            ]);
            Toastr::success(translate('successfully_canceled'));
            return back();
        }
        Toastr::error(translate('status_not_changable_now'));
        return back();
    }

    public function refund_request(Request $request, $id)
    {
        $order_details = OrderDetail::find($id);
        $user = auth('customer')->user();

        $wallet_status = Helpers::get_business_settings('wallet_status');
        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($id);

            if ($user->loyalty_point < $loyalty_point) {
                Toastr::warning(translate('you have not sufficient loyalty point to refund this order!!'));
                return back();
            }
        }

        return view('web-views.users-profile.refund-request', compact('order_details'));
    }

    public function store_refund(Request $request)
    {
        $request->validate([
            'order_details_id' => 'required',
            'amount' => 'required',
            'refund_reason' => 'required'

        ]);
        $order_details = OrderDetail::find($request->order_details_id);
        $user = auth('customer')->user();


        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($request->order_details_id);

            if ($user->loyalty_point < $loyalty_point) {
                Toastr::warning(translate('you have not sufficient loyalty point to refund this order!!'));
                return back();
            }
        }
        $refund_request = new RefundRequest;
        $refund_request->order_details_id = $request->order_details_id;
        $refund_request->customer_id = auth('customer')->id();
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

        Toastr::success(translate('refund_requested_successful!!'));
        return redirect()->route('account-order-details', ['id' => $order_details->order_id]);
    }

    public function generate_invoice($id)
    {
        $order = Order::with('seller')->with('shipping')->where('id', $id)->first();
        $data["email"] = $order->customer["email"];
        $data["order"] = $order;

        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice'], compact('order'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function refund_details($id)
    {
        $order_details = OrderDetail::find($id);
        $refund = RefundRequest::where('customer_id', auth('customer')->id())
            ->where('order_details_id', $order_details->id)->first();
        $product = $this->product->find($order_details->product_id);
        $order = $this->order->find($order_details->order_id);

        if($product) {
            return view(VIEW_FILE_NAMES['refund_details'], compact('order_details', 'refund', 'product', 'order'));
        }

        Toastr::error(translate('product_not_found'));
        return redirect()->back();
    }

    public function submit_review(Request $request, $id)
    {
        $order_details = OrderDetail::where(['id' => $id])->whereHas('order', function ($q) {
            $q->where(['customer_id' => auth('customer')->id(), 'payment_status' => 'paid']);
        })->first();

        if (!$order_details) {
            Toastr::error(translate('Invalid order!'));
            return redirect('/');
        }

        return view('web-views.users-profile.submit-review', compact('order_details'));

    }    

    public function serviceregisterstore(Request $request)
    {
        // dd($request->all());

        /* ---------------- GST → PAN Extraction ---------------- */
        $pan = null;

        if (!empty($request->gstin)) {
            $gstin = strtoupper(trim($request->gstin));

            if (strlen($gstin) === 15) {
                $pan = substr($gstin, 2, 10);
            } else {
                return back()->withErrors(['gstin' => 'Invalid GSTIN']);
            }
        }

        /* ---------------- Image Upload ---------------- */
        $imagePath = null;
        $bannerPath = null;

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $webpImage = Image::make($image->getRealPath())->encode('webp');
            $filename = 'serviceProvider/profileImage/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $webpImage);
            $imagePath = '/' . $filename;
        }

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $webpImage = Image::make($image->getRealPath())->encode('webp');
            $filename = 'serviceProvider/bannerImage/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $webpImage);
            $bannerPath = '/' . $filename;
        }

        /* ---------------- JSON Fields ---------------- */
        $cities = $request->city ? json_encode($request->city) : null;
        $serviceTypes = $request->serviceTypeId ? json_encode($request->serviceTypeId) : null;

        /* ---------------- Fetch Existing User (if any) ---------------- */
        $existingUser = User::where('phone', $request->phone)->first();

        /* ---------------- Name Handling (IMPORTANT) ---------------- */
        $nameToSave = $existingUser?->name;

        $firstName = $existingUser?->f_name;
        $lastName  = $existingUser?->l_name;

        if (!empty($request->name)) {

            $nameParts = array_values(array_filter(explode(' ', trim($request->name))));

            $firstName = array_shift($nameParts);   
            $lastName  = implode(' ', $nameParts);  
        }

        if (!empty($request->name)) {
            $nameToSave = $request->name;
        }

        /* ---------------- Save User ---------------- */
        User::updateOrCreate(
            [
                'phone' => $request->phone,
            ],
            [
                'role'              => $request->role,
                'role_name'         => $this->roleName($request->role),
                'remember_token'    => $request->otp,
                'temporary_token'   => $request->otp,

                'business_name'     => $request->business_name,
                'name'              => $nameToSave, // ✅ protected
                'f_name'            => $firstName,
                'l_name'            => $lastName,

                'email'             => $request->email,
                'referral_code'     => $request->referral_code,

                'dob'               => $request->dob ? Carbon::createFromFormat('d-m-Y', $request->dob)->format('Y-m-d') : null,
                'adhaar_number'     => $request->adhaar_number,
                'gender'            => $request->aadhaar_gender,

                'image'             => $imagePath ?? $existingUser?->image,
                'banner_image'      => $bannerPath ?? $existingUser?->banner_image,

                'permanent_address' => $request->address,
                'state'             => $request->state,
                'zip'               => $request->pincode,

                'gst'               => $request->gstin,
                'pan'               => $pan, // ✅ extracted PAN

                'working_since'     => $request->working_since,
                'team_strength'     => $request->team_strength,
                'total_project_done'=> $request->total_project_done,

                'description'       => $request->about_company,
                'achievments'       => $request->achievements,

                'whatsapp_number'   => $request->whatsapp,
                'website'           => $request->website,
                'insta_link'        => $request->insta_link,
                'facebook_link'     => $request->facebook_link,
                'youtube_link'      => $request->youtube_link,
                'father_name'       => $request->father,

                'city'              => $cities,
                'serviceTypeId'     => $serviceTypes,

                'is_active'         => 0,
            ]
        );

        return back()->with('success', 'Registration submitted successfully!');
    }
    
    private function roleName($role)
    {
        return match ((int)$role) {
            3 => 'Contractor',
            4 => 'Architect',
            5 => 'Interior Designer',
            default => null,
        };
    }

    public function send_otp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10'
        ]);

        $mobile = $request->mobile;

        $user = User::where('phone', $mobile)->first();
        if ($user) {
            return response()->json([
                'status' => 'already_customer'
            ]);
        }
        session()->forget('otp_send');
        $otp = rand(1111, 9999);
        session()->put('otp_send', $otp);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://2factor.in/API/R1/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91'.$mobile.'&from=INTCHK&msg=Dear%20customer%2C%20'.$otp.'%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if (!empty($data['Status']) && $data['Status'] === 'Success') {
            return response()->json(['status' => 'otp_sent','otp' => $otp]);
        }

        return response()->json(['status' => 'error', 'api_response' => $data]);

    }

    public function verify_otp(Request $request)
    {
        // dd($request->all());
       if(session()->get('otp_send') == $request->otp){
           session()->forget('otp_send');
           return response()->json(['status' => 'otp_verified']);
       }else{
           echo 0;
       }
    }

    public function getToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://production.deepvue.tech/v1/authorize',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => '9bb8fc8484',
                'client_secret' => '98e61ce59f4944f3825fc01d16b74e59',
                'grant_type' => 'client_credentials',
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if (isset($result['access_token'])) {
            return $result['access_token'];
        }

        throw new \Exception('Unable to get token from Deepvue API');
    }

    public function verifyGst(Request $request)
    {
        $request->validate([
            'gst' => 'required|string'
        ]);

        $gst = trim($request->input('gst'));

        $apiKey = '98e61ce59f4944f3825fc01d16b74e59';
        $endpoint = 'https://production.deepvue.tech/v1/verification/gstinlite';

        try {
            $token = $this->getToken(); // <-- Get fresh token here

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(10)->get($endpoint, [
                'gstin_number' => $gst
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API call failed: ' . ($response->status() . ' ' . $response->body())
                ], 400);
            }

            $body = $response->json();

            if (!isset($body['data']) || empty($body['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data returned from GST API'
                ], 404);
            }

            $d = $body['data'];
            $tradeName = $d['tradeNam'] ?? $d['lgnm'] ?? null;
            $addr = $d['pradr']['addr'] ?? [];

            $addrParts = [];
            foreach (['bno','flno','st','loc','dst','pncd','landMark'] as $k) {
                if (!empty($addr[$k])) $addrParts[] = $addr[$k];
            }
            $fullAddress = implode(', ', $addrParts);

            $result = [
                'gstin' => $d['gstin'] ?? $gst,
                'business_name' => $tradeName,
                'legal_name' => $d['lgnm'] ?? null,
                'address' => $fullAddress,
                'state' => $addr['stcd'] ?? $d['stj'] ?? null,
                'city' => $addr['loc'] ?? $addr['dst'] ?? null,
                'pincode' => $addr['pncd'] ?? null,
                'status' => $d['sts'] ?? null,
                'other' => $d,
            ];

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reloadCaptcha(Request $request)
    {
        $request->validate(['session_id' => 'required|string']);

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL =>
                    'https://production.deepvue.tech/v1/ekyc/aadhaar/reload-captcha'
                    . '?consent=Y&purpose=For%20KYC'
                    . '&session_id=' . $request->session_id,

                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'x-api-key: 98e61ce59f4944f3825fc01d16b74e59',
                    'client-id: 9bb8fc8484',
                    'Content-Type: application/json'
                ],
            ]);

            $res = curl_exec($curl);
            curl_close($curl);

            $data = json_decode($res, true);

            return response()->json([
                'success' => true,
                'captcha' => $data['data']['captcha'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateAadhaarOtp(Request $request)
    {
        try {

            $query = http_build_query([
                'aadhaar_number' => $request->aadhaar_number,
                'captcha'        => $request->captcha,
                'session_id'     => $request->session_id,
                'consent'        => 'Y',
                'purpose'        => 'For KYC',
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://production.deepvue.tech/v1/ekyc/aadhaar/generate-otp?$query",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'x-api-key: 98e61ce59f4944f3825fc01d16b74e59',
                    'client-id: 9bb8fc8484',
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                throw new \Exception(curl_error($curl));
            }

            curl_close($curl);

            $result = json_decode($response, true);

            if (!isset($result['transaction_id'])) {
                return response()->json([
                    'success'  => false,
                    'message'  => $result['message'] ?? 'OTP generation failed',
                    'response' => $result,
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function verifyAadhaarOtp(Request $request)
    {
        // dd($request->all());

        $otp = $request->otp;
        $session_id = $request->session_id;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://production.deepvue.tech/v1/ekyc/aadhaar/verify-otp'
                    . '?otp=' . $otp
                    . '&session_id=' . $session_id
                    . '&consent=Y'
                    . '&purpose=For%20KYC'
                    . '&mobile_number=9999999999',

                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'x-api-key: 98e61ce59f4944f3825fc01d16b74e59',
                    'client-id: 9bb8fc8484',
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                throw new \Exception($error);
            }

            curl_close($curl);

            $result = json_decode($response, true);

            if (!isset($result['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification failed',
                    'response' => $result
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}