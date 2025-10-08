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
use Illuminate\Support\Facades\Log;

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
    ) {}

    public function user_profile_1()
    {
        $id = auth()->user()->id;
        //dd($ids);
        $user = User::where('id', $id)->first();
        $orders = DB::table('orders')
            ->join('order_details', function ($join) {
                $join->on('orders.id', '=', 'order_details.order_id');
            })
            ->join('sku_product_new', function ($join) {
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




        //dd($orders);

        $wallets = DB::table('wallet_transactions')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        //dd($wallet);

        $addresses = DB::table('shipping_addresses')->where('customer_id', $id)->get();

        $wishlists = DB::table('wishlists')
            ->join('sku_product_new', function ($join) {
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
            ->join('sku_product_new', function ($join) {
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
            ->join('sku_product_new', function ($join) {
                $join->on('new_cart.product_id', '=', 'sku_product_new.product_id');
            })
            ->join('products', function ($join) {
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





        // dd($cart);

        $wallet = HelpTopicSubCategory::where('sub_cat_name', 'Wallet')->first();
        // dd($wallet);
        $referandearn =  HelpTopicSubCategory::where('sub_cat_name', 'Refer & Earn')->first();
        $personaldetails =  HelpTopicSubCategory::where('sub_cat_name', 'Personal Details')->first();


        $faqwallet = HelpTopic::where('sub_cat_id', $wallet->id)->get();
        $faqreferandearn = HelpTopic::where('sub_cat_id', $referandearn->id)->get();
        $faqpersonaldetails = HelpTopic::where('sub_cat_id', $personaldetails->id)->get();

        return view('web.user_profile', compact('faqpersonaldetails', 'faqreferandearn', 'faqwallet', 'user', 'orders', 'wallet', 'wallets', 'addresses', 'wishlists', 'recently_viewed', 'cart'));
    }

    public function view_cart()
    {
        $id = auth()->user()->id;
        // dd($id);
        $cart = DB::table('new_cart')
            ->join('sku_product_new', function ($join) {
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
            ->where('type', 0)
            ->get();

        //dd($cart);

        session()->put('cart', $cart);


        $recently_viewed = DB::table('recently_view')
            ->join('sku_product_new', function ($join) {
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
        return view('web.cart', compact('cart', 'recently_viewed'));
    }

    public function view_wishlist()
    {
        $id = auth()->user()->id;
        $wishlists = DB::table('wishlists')
            ->join('sku_product_new', function ($join) {
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

        //dd($wishlists);

        $recently_viewed = DB::table('recently_view')
            ->join('sku_product_new', function ($join) {
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

        return view('web.wishlist', compact('wishlists', 'recently_viewed'));
    }


    public function checkout(Request $request)
    {
        if(isset($request->p)){
            // dd($request->p);

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

        // Ensure uniqueness based on coupon ID (or any other unique field)
        $coupon_discount = $coupon_discount->unique('id'); // Make coupons unique by 'id'
        //dd($coupon_discount[0]);
        //dd($cart_group_id);
        // Pass cart data to checkout view



        return view('web.checkout', compact('carts', 'total_variant_mrp', 'total_listed_price', 'cart_group_id', 'coupon_discount'));
    }

    public function save_address(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required|string|max:50',
            'phone'               => 'required|digits:10',
            'address_type'        => 'required|string|max:10',
            'address'             => 'required|string|max:100',
            'zip'                 => 'required|string|max:10',
            'city'                => 'required|string|max:50',
            'state'               => 'required|string|max:50',
            'landmark'            => 'nullable|string|max:100',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


        $data = [
            'customer_id' => auth()->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'zip' => $request->zip,
            'city' => $request->city,
            'state' => $request->state,
            'country' => 'India',
            'landmark' => $request->landmark,
            'phone' => $request->phone,
            'is_selected' => 0,
            'created_at' => Now(),
            'updated_at' => Now()

        ];
        //dd($data);

        $table = DB::table('shipping_addresses')->insert($data);

        return response()->json([
            'status' => true,
            'message' => 'Address saved successfully!'
        ]);
    }

    public function get_shipping_cost(Request $request)
    {
        //    dd($request->instant_delivery);
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
                $p = DB::table('products')->where('id', $seller_data->product_id)->first();
                //echo "hi"; dd($seller_data);
                if ($seller_data) {
                    //dd($p->user_id);
                    $seller = Seller::where("id", $p->user_id)->with('shop')->first();
                    $warehouse_id = DB::table('warehouse')->where('seller_id', $p->user_id)->first();
                    //dd($warehouse_id->pincode);
                    if ($seller) {
                        $weight = 0;
                        $length = 0;
                        $width = 0;
                        $height = 0;



                        //dd(CartManager::get_carts($request['cart_group_id']));
                        foreach (CartManager::get_carts($request['cart_group_id']) as $c) {
                            // dd($c);
                            $product = Product::where('id', $c->product_id)->select("id", "free_delivery")->first();
                            //dd($c->variation);
                            $prs = DB::table('sku_product_new')->where('product_id', $c->product_id)->where('id', $c->variation)->get();


                            // if($pr){
                            //     $weight += $pr->weight;

                            // }
                            foreach ($prs as $pr) {
                                $weight += $pr->weight * $c->quantity;
                                $length += $pr->length * $c->quantity;
                                $width += $pr->breadth * $c->quantity;
                                $height += $pr->height * $c->quantity;
                            }
                            //dd($pr);
                            // if($product) {
                            //    // $free_delivery = $product->free_delivery;
                            //     /*if($free_delivery == 0){*/
                            //         $weight += $product->weight;
                            //     /*}*/
                            // }
                        }

                        //dd($weight);


                        $productfp = Product::where(['id' => $seller_data->product_id])->select("id", "weight", "free_delivery")->first();
                        if ($productfp) {
                            $free_delivery = $productfp->free_delivery;
                            // dd($free_delivery);
                        }



                        if ($weight) {

                            //    dd($weight);

                            $shippingAddress = ShippingAddress::find($request['address_id']);

                            $shippingPrice = $shiprocketService->checkForAvailability([
                                'pickupPincode'  => (int) $warehouse_id->pincode,
                                'deliveryPincode'  => (int) $shippingAddress->zip ?? '',
                                'weight'  => $weight,
                                'paymentMode'  => $request->iscod === '0' ? 'PREPAID' : 'COD',
                                'orderType' => 'B2C',
                                'invoiceValue' => 10,
                                'dimension' => [
                                    'length' => $length,
                                    'width'  => $width,
                                    'height' => $height
                                ]
                            ]);


                            // dd($shippingPrice);
                            $instant_delivery = false;
                            if ($request->instant_delivery) {
                                $instant_delivery = true;
                            }


                            if ($shippingPrice['data'] !== null) {
                                if (($instant_delivery == true) && ($free_delivery == 1)) {

                                    $cost = $shippingPrice['data']->total ?? 0;
                                } else {

                                    //dd($shippingPrice['data']->total);
                                    if ($free_delivery) {

                                        //$cost = 0;
                                        $cost = $shippingPrice['data']->total ?? 0;
                                    } else {

                                        $cost = $shippingPrice['data']->total ?? 0;
                                    }
                                    // Added by satyajit
                                    // $cost = $shippingPrice['data']->rate ?? 0;
                                }

                                //  dd($cost);

                                //update in shipping_cost table
                                $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
                                if (isset($shipping) == false) {
                                    $shipping = new CartShipping();
                                }

                                $shiprocket = ShippingMethod::where("title", "Shiprocket")->first();

                                $shipping['cart_group_id'] = $request['cart_group_id'];
                                $shipping['shipping_method_id'] = $shiprocket->id ?? null;
                                if ($request->instant_delivery) {
                                    if ($cost <= 100) {
                                        $cost =   100;
                                    } else {
                                        $cost = $cost * 1.50;
                                    }
                                    $shipping['shipping_cost'] = $cost ?? 0;
                                } else {
                                    $shipping['shipping_cost'] = $cost ?? 0;
                                }

                                $shipping->save();

                                $totalShippingCost += $cost ?? 0;
                                //$costAmount[] = $cost;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'failure',
                    'shipping_cost' => 0,
                    'message' => $e->getMessage()
                ]);
            }
        }

        /* $myfile = fopen("/www/wwwroot/interiorchowk.com/response_take.txt", "w") or die("Unable to open file!");
            $txt = implode(',', $costAmount);
            fwrite($myfile, $txt);
            fclose($myfile);*/

        return response()->json([
            'cod' => $request->iscod,
            'status' => 'success',
            'free_delivery' => $free_delivery,
            'shipping_cost' => $totalShippingCost,
            'message' => 'Total shipping cost'
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
        // Validate the form data
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

    public function user_update_1(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'f_name' => 'nullable|string|max:255',
            'l_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Male,Female',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|digits:10',
        ]);

        $user->update($data);

        return redirect()
            ->back()
            ->with('success', 'Personal information updated successfully.');
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
        if (theme_root_path() == 'theme_fashion') {
            $show_order = $request->show_order ?? 'ongoing';

            $array = ['pending', 'confirmed', 'out_for_delivery', 'processing'];
            $orders = $this->order
                ->where(['customer_id' => auth('customer')->id()])
                ->when($show_order == 'ongoing', function ($query) use ($array) {
                    $query->whereIn('order_status', $array);
                })
                ->when($show_order == 'previous', function ($query) use ($array) {
                    $query->whereNotIn('order_status', $array);
                })
                ->when($request['search'], function ($query) use ($request) {
                    $query->where('id', 'like', "%{$request['search']}%");
                })
                ->orderBy('id', $order_by)->paginate(10)->appends(['show_order' => $show_order, 'search' => $request->search]);
        } else {
            $orders = Order::where('customer_id', auth('customer')->id())
                ->orderBy('id', $order_by)
                ->paginate(15);
        }

        return view(VIEW_FILE_NAMES['account_orders'], compact('orders', 'order_by'));
    }

    public function account_order_details(Request $request)
    {
        $order = $this->order->with(['details.product', 'delivery_man_review'])->where(['customer_id' => auth('customer')->id()])->find($request->id);
        $refund_day_limit = \App\CPU\Helpers::get_business_settings('refund_day_limit');
        $current_date = \Carbon\Carbon::now();
        if ($order) {
            return view(VIEW_FILE_NAMES['account_order_details'], compact('order', 'refund_day_limit', 'current_date'));
        }

        Toastr::warning('Invalid order');
        return redirect('account-oder');
    }

    public function account_order_details_seller_info(Request $request)
    {
        $order = $this->order->with(['seller.shop'])->find($request->id);
        $product_ids = $this->product->where(['added_by' => $order->seller_is, 'user_id' => $order->seller_id])->pluck('id');
        $rating = $this->review->whereIn('product_id', $product_ids);
        $avg_rating = $rating->avg('rating') ?? 0;
        $rating_percentage = round(($avg_rating * 100) / 5);
        $rating_count = $rating->count();
        $product_count = $this->product->where(['added_by' => $order->seller_is, 'user_id' => $order->seller_id])->active()->count();

        return view(VIEW_FILE_NAMES['seller_info'], compact('avg_rating', 'product_count', 'rating_count', 'order', 'rating_percentage'));
    }

    public function account_order_details_delivery_man_info(Request $request)
    {
        $order = $this->order->with(['delivery_man.rating', 'delivery_man' => function ($query) {
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
            if (theme_root_path() != 'default') {
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

        if ($product) {
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
}
