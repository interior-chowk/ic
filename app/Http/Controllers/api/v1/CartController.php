<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\CPU\ProductManager;
use App\Model\Cart;
use App\Model\Shop;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function cart(Request $request)
    {
        $user = Helpers::get_customer($request); // $user->id
        $cart = Cart::with(['product:id,name,slug,current_stock,minimum_order_qty,variation,weight,height,breadth,length,available_instant_delivery,free_delivery'])
            ->where(['customer_id' => $user->id])
            ->orderBy('carts.id', 'desc')
            ->get();
            
        $product_ids = $cart->pluck('product_id')->toArray();

        $sku_products = DB::table('sku_product_new')
            ->whereIn('product_id', $product_ids)
            ->whereNotNull('thumbnail_image')
            ->get()
            ->keyBy('product_id');
         
        if ($cart) {
            $product_ids = [];
            foreach ($cart as $carts) {
                $product_ids[] = $carts->product_id;

                $cart_update = CartManager::update_to_cart($carts->product_id, $carts->customer_id, $carts->variant);
                $carts->seller_id;
                $shop = Shop::where('seller_id', $carts->seller_id)->first();
                if ($shop) {
                    Cart::where('id', $carts->id)->update(['seller_city' => $shop->city]);
                }
            }
            //dd('hello');
            $related_products = ProductManager::get_related_productsss($product_ids);
          
            foreach ($cart as $key => $value) {
                if (!isset($value['product'])) {
                    $cart_data = Cart::find($value['id']);
                    $cart_data->delete();

                    unset($cart[$key]);
                }
            }
           
            $cart->map(function ($data) use ($sku_products) { // $sku_products को map function में पास करें
                $data['choices'] = json_decode($data['choices']);
                $data['variations'] = json_decode($data['variations']);
           
                $data['product']['total_current_stock'] = isset($data['product']['current_stock']) ? $data['product']['current_stock'] : 0;
                if (isset($data['product']['variation']) && !empty($data['product']['variation'])) {
                    $variants = json_decode($data['product']['variation']);
                    foreach ($variants as $var) {
                        if ($data['variant'] == $var->type) {
                            $data['product']['total_current_stock'] = $var->qty;
                        }
                    }
                }
                unset($data['product']['variation']);
                
                $data->product->free_shipping = 0;
                if ($data->product->free_delivery) {
                    $data->product->free_shipping = 1;
                }
            
                // SKU Product से Thumbnail Set करें
                if (isset($sku_products[$data->product_id])) {
                    $sku = $sku_products[$data->product_id];
                    $data->product->thumbnail = $sku->thumbnail_image;
                } else {
                    $data->product->thumbnail = null; // अगर thumbnail नहीं मिले तो default null set करें
                }
            
                return $data;
            });
            
            // Response format creation
            $response = [
                "checkoutlist" => $cart->toArray(),
                "related_product" => $related_products,
            ];
        } else {
            $response = [
                "checkoutlist" => [],
                "related_product" => [],
            ];
        }

        return response()->json($response, 200);
    }

    public function wishlist_carts(Request $request)
    {
        // Fetch user information
        $user = Helpers::get_customer($request);
    
        // Fetch cart data
        $cart = Cart::with([
            'product:id,name,slug,current_stock,minimum_order_qty,variation,weight,height,breadth,length,available_instant_delivery,free_delivery',
        ])
        ->where(['customer_id' => $user->id])
        ->orderBy('carts.id', 'desc')
        ->get();
    
        $productIds = [];
    
        if ($cart) {
            foreach ($cart as $carts) {
                $cart_update = CartManager::update_to_cart($carts->product_id, $carts->customer_id, $carts->variant);
                $shop = Shop::where('seller_id', $carts->seller_id)->first();
                if ($shop) {
                    Cart::where('id', $carts->id)->update(['seller_city' => $shop->city]);
                }
    
                if ($carts->product) {
                    $productIds[] = $carts->product->id;
                }
            }
    
            foreach ($cart as $key => $value) {
                if (!isset($value['product'])) {
                    $cart_data = Cart::find($value['id']);
                    $cart_data->delete();
                    unset($cart[$key]);
                }
            }
    
            $cart->map(function ($data) {
                $data['choices'] = json_decode($data['choices']);
                $data['variations'] = json_decode($data['variations']);
    
                $data['product']['total_current_stock'] = isset($data['product']['current_stock']) ? $data['product']['current_stock'] : 0;
                if (isset($data['product']['variation']) && !empty($data['product']['variation'])) {
                    $variants = json_decode($data['product']['variation']);
                    foreach ($variants as $var) {
                        if ($data['variant'] == $var->type) {
                            $data['product']['total_current_stock'] = $var->qty;
                        }
                    }
                }
                unset($data['product']['variation']);
                $data->product->free_shipping = 0;
                if ($data->product->free_delivery) {
                    $data->product->free_shipping = 1;
                }
    
                return $data;
            });
        }
    
        // Fetch wishlist data
        $wishlist = Wishlist::whereHas('wishlistProduct', function ($q) {
            return $q;
        })->with(['product'])
        ->where('customer_id', $request->user()->id)
        ->orderBy('wishlists.id', 'desc')
        ->get();
    
        foreach ($wishlist as $wishlists) {
            if ($wishlists->product) {
                $productIds[] = $wishlists->product->id;
            }
        }
    
        // SKU product data fetch karein
        $sku_products = DB::table('sku_product_new')
            ->whereIn('product_id', $productIds)
            ->whereNotNull('thumbnail_image')
            ->select('product_id', 'thumbnail_image', 'image', 'listed_price')
            ->get();
    
        // SKU product data ko map karein
        $sku_product_map = [];
        foreach ($sku_products as $sku) {
            $sku_product_map[$sku->product_id] = $sku;
        }
    
        // Cart products me SKU data merge karein
        $cart = $cart->map(function ($cartItem) use ($sku_product_map) {
            if ($cartItem->product && isset($sku_product_map[$cartItem->product->id])) {
                $sku = $sku_product_map[$cartItem->product->id];
                $cartItem->product->thumbnail = $sku->thumbnail_image;
                $cartItem->product->images = json_decode($sku->image, true);
                $cartItem->product->unit_price = $sku->listed_price;
            }
            return $cartItem;
        });
    
        // Wishlist products me SKU data merge karein
        $wishlist = $wishlist->map(function ($wish) use ($sku_product_map) {
            if ($wish->product && isset($sku_product_map[$wish->product->id])) {
                $sku = $sku_product_map[$wish->product->id];
                $wish->product->thumbnail = $sku->thumbnail_image;
                $wish->product->images = json_decode($sku->image, true);
                $wish->product->unit_price = $sku->listed_price;
            }
            return $wish;
        });
    
        // Combine cart and wishlist data in the response
        return response()->json([
            'cart' => $cart,
            'wishlist' => $wishlist,
        ], 200);
    }
    
    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ], [
            'id.required' => translate('Product ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $cart = CartManager::add_to_cart($request);
        return response()->json($cart, 200);
    }

    public function add_to_cart_new(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'colors' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
            'variation' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
    
        $user = Helpers::get_customer($request);
        $user_id = auth('api')->user()->id;
    
        $product_id = $request->product_id;
        $colors = $request->colors;
        $variation = $request->variation;
        $quantity = $request->quantity;
    
        // ✅ Get product to fetch seller_id
        $product = DB::table('products')->where('id', $product_id)->first();

        if (!$product) {
            return response()->json(['status' => 0, 'message' => 'Invalid product!'], 404);
        }
    
        $seller_id = $product->user_id;
    
        // ✅ Check if cart group already exists for same user and seller
        $existing_group_id = DB::table('new_cart')
            ->where('user_id', $user_id)
            ->where('seller_id', $seller_id)
            ->value('cart_group_id');
    
        if ($existing_group_id) {
            $cart_group_id = $existing_group_id;
        } else {
            $cart_group_id = $user_id . '-' . Str::random(5) . '-' . time();
        }
    
        // ✅ Check if same product already in cart with same variation & color
        $existing = DB::table('new_cart')
            ->where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->where('colors', $colors)
            ->where('variation', $variation)
            ->first();
    
        if ($existing) {
            // Update existing cart item
            $updated = DB::table('new_cart')
                ->where('id', $existing->id)
                ->update([
                    'quantity' => $quantity,
                    'updated_at' => now()
                ]);
    
            if ($updated) {
                return response()->json(['status' => 1, 'message' => 'Successfully updated!'], 200);
            }
        } else {
            // Insert new cart item
            $inserted = DB::table('new_cart')->insert([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'cart_group_id' => $cart_group_id,
                'colors' => $colors,
                'variation' => $variation,
                'quantity' => $quantity,
                'is_selected' => 1,
                'product_type' => 'physical',
                'seller_id' => $seller_id,
                'seller_is' => 'seller',
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            if ($inserted) 
            {
                return response()->json([
                'status' => 1,
                'message' => 'Successfully added!'
                ], 200);
            }
        }
    
        // Default fail response
        return response()->json(['status' => 0, 'message' => 'Something went wrong!'], 500);
    }

    public function cart_new(Request $request)
    {
        $user = Helpers::get_customer($request); 

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found']);
        }

        // User cart data
        $cartItems = DB::table('new_cart')
                    ->where('user_id', $user->id)
                    ->get(); 

        $variation_ids = $cartItems->pluck('variation')->toArray(); 
        $product_ids = $cartItems->pluck('product_id')->toArray(); 

        $thumbnail_image = DB::table('sku_product_new')
        ->where('product_id', $product_ids)
        ->whereNotNull('thumbnail_image')
        ->first();

        // Get product details from products and sku_product_new
        $products = DB::table('products as p')
        ->join('sku_product_new as s', 'p.id', '=', 's.product_id')
        ->join('shops as sh', 'sh.seller_id', '=', 'p.user_id')
        ->whereIn('p.id', $product_ids)
        ->whereIn('s.id', $variation_ids)
        ->select(
            's.*',
            'p.name',
            'p.slug',
            'p.min_qty',
            'p.free_shipping',
            'p.available_instant_delivery',
            'p.shipping_cost',
            'sh.address as shop_address',
            'sh.city'
        )
        ->get();

        // Map product details by variation_id (sku ID)
        $productDetailsMap = [];
        foreach ($products as $prod) {
            $productDetailsMap[$prod->id] = $prod;
        }

        // Prepare final cart list
        $cartlist = [];
        foreach ($cartItems as $item) {
            $cartlist[] = [
                'id' => $item->id,
                'is_selected' => $item->is_selected,
                'quantity' => $item->quantity,
                'product_details' => $productDetailsMap[$item->variation] ?? (object)[],
                'thumbnail_image'=>$thumbnail_image->thumbnail_image
            ];
        }

        return response()->json([
            'status' => 1,
            'cartlist' => $cartlist
        ]);
    }

    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'quantity' => 'required',
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $response = CartManager::update_cart_qty($request);
        return response()->json($response);
    }

    public function update_quantity(Request $request)
    {
    // $skuId = $request->sku_id;
        $cartId = $request->cart_id;
        $requestedType = $request->type;
    // dd( $skuId,$cartId,$requestedType);
        // पहले current quantity ले लो
        $cart = DB::table('new_cart')
            ->where('id', $cartId)
            ->first();

        if (!$cart) {
            return response()->json(['status' => 0, 'message' => 'Cart not found']);
        }

        $newQuantity = $cart->quantity;

        if ($requestedType == 'increament') {
            $newQuantity += 1;
        } else {
            $newQuantity = max(1, $newQuantity - 1); // Minimum 1 allowed
        }

        // Update new quantity
        DB::table('new_cart')
            ->where('id', $cartId)
            ->update(['quantity' => $newQuantity]);

        return response()->json(['status' => 1, 'message' => 'Quantity updated', 'quantity' => $newQuantity]);
    }

    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = Helpers::get_customer($request);
        DB::table('new_cart')->where('id',$request->id)->delete();
        return response()->json(translate('successfully_removed'));
    }

    public function remove_all_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required'
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = Helpers::get_customer($request);
        Cart::where(['customer_id' => $user->id])->delete();
        return response()->json(translate('successfully_removed'));
    }
}