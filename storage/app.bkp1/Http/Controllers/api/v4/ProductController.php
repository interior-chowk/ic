<?php

namespace App\Http\Controllers\api\v4;

use App\CPU\CategoryManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\Category;
use App\Model\FlashDeal;
use App\Model\FlashDealProduct;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Model\ShippingMethod;
use App\Model\Translation;
use Illuminate\Support\Facades\Session;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function get_latest_products(Request $request)
    {
        $products = ProductManager::get_latest_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_featured_products(Request $request)
    {
        $products = ProductManager::get_featured_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_top_rated_products(Request $request)
    {
        $products = ProductManager::get_top_rated_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function suggestion(Request $request){
   
        $suggestions = [];

        $sug = $request->has('search') ? $request['search'] : null;
        if(!empty($sug)){
            $suggestion = DB::table('tags')->where('tag','like',"%$sug%")
            ->limit(10)
            ->get(['id','tag']);
            $suggestions = $suggestion;

        }

        return response()->json(['suggestion'=>$suggestion]);


    }

    public function popular_choice(){

        $pop = Category::where('status', 1)->pluck('name');
          return response()->json(['pop'=>$pop],200);

      }



    public function recently_views(Request $request)
    {
        $user_id = $request->input('user_id');
        $product_id = $request->input('product_id');
        $type = $request->input('type');
        
    
        $existingRecord = DB::table('recently_view')
            ->where('product_id', $product_id)->where('type',$type)
            ->first();

        if ($existingRecord) {
        
            DB::table('recently_view')
                ->where('product_id', $product_id)
                ->where('type',$type)
                ->update([
                    'counts' => $existingRecord->counts + 1,
                    'updated_at' => now()
                ]);
        } else {
        
            DB::table('recently_view')->insert([
                'user_id' => $user_id, 
                'product_id' => $product_id,
                'type'=> $type,
                'counts' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return response()->json(['message'=>'insert successfully'],200);
    }


    public function category_views(Request $request)
    {
        $user_id = $request->input('user_id');
        $type = $request->input('type'); // category, subcategory, subsubcategory
        $id = $request->input('id');     // dynamic id for type

        if (!$user_id || !$type || !$id) {
            return response()->json(['message' => 'Missing required fields'], 400);
        }

        // Decide column name based on type
        $column = match ($type) {
            'category'      => 'category_id',
            'subcategory'   => 'sub_category_id',
            'subsubcategory'=> 'sub_sub_category_id',
            default         => null,
        };

        if (!$column) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        // Check if record exists
        $existingRecord = DB::table('recently_view')
            ->where($column, $id)
            ->where('type', $type)
            ->first();

        if ($existingRecord) {
            DB::table('recently_view')
                ->where($column, $id)
                ->where('type', $type)
                ->update([
                    'counts' => $existingRecord->counts + 1,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('recently_view')->insert([
                'user_id' => $user_id,
                $column   => $id,
                'type'    => $type,
                'counts'  => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json(['message' => 'Inserted successfully'], 200);
    }







public function instant_delivery_product(Request $request)
{
    $seller_id = $request->seller_id;
    $user_id = $request->user_id;
    $product_id = $request->product_id;

    // Get user address
    $userAddress = DB::table('shipping_addresses')
        ->where('customer_id', $user_id)
        ->first();

    if (!$userAddress) {
        return response()->json(['message' => 'User address not found']);
    }

    $userCity = $userAddress->city;

    // Get product details
    $pro = Product::where('id', $product_id)
        ->where('user_id', $seller_id)
        ->first();

    if (!$pro) {
        return response()->json(['message' => 'Product not found']);
    }

    // Get warehouse details
    $warehouse = DB::table('warehouse')
        ->where('id', $pro->add_warehouse)
        ->first();

    // Ensure warehouse exists
    if (!$warehouse) {
        return response()->json(['message' => 'Warehouse not found']);
    }

    // Decode cities if stored as JSON
    $productCities = is_array($pro->cities) ? $pro->cities : json_decode($pro->cities, true);

    if (!is_array($productCities)) {
        $productCities = [];
    }

    // Check if user city matches warehouse or product cities
    if ($warehouse->city == $userCity || in_array($userCity, $productCities)) {
        return response()->json(['message' => 'Instant delivery','status'=> 1]);
    } else {
        return response()->json(['message' => 'No instant delivery','status'=> 0]);
    }
}



//news

// public function search_city_products(Request $request)
// {
//     $cities = $request->cities;

//     $t = DB::table('warehouse')->where('city', $cities)->first();

//     if ($t) {
//         $products = Product::where('user_id', $t->seller_id)->get();
//     } else {
//         if (is_string($cities) && $this->isJson($cities)) {
//             $cities = json_decode($cities, true);
//         } else {
//             $cities = explode(',', $cities);
//         }

//         $cities = array_map('trim', $cities);

//         if (!is_array($cities) || empty($cities)) {
//             return response()->json(['error' => 'Invalid input format'], 400);
//         }

//         $products = Product::whereIn('add_warehouse', $cities)
//             ->orWhere(function ($query) use ($cities) {
//                 foreach ($cities as $city) {
//                     $query->orWhere('cities', 'LIKE', "%$city%");
//                 }
//             })
//             ->get();
//     }

//     // Product IDs extract karein
//     $productIds = $products->pluck('id')->toArray();

//     // SKU product data fetch karein
//     $sku_products = DB::table('sku_product_new')
//         ->whereIn('product_id', $productIds)
//         ->whereNotNull('thumbnail_image')
//         ->select('product_id', 'thumbnail_image', 'image', 'listed_price')
//         ->get();

//     // SKU product data ko map karein
//     $sku_product_map = [];
//     foreach ($sku_products as $sku) {
//         $sku_product_map[$sku->product_id] = $sku;
//     }

//     // Products me SKU data merge karein
//     $merged_products = $products->map(function ($product) use ($sku_product_map) {
//         if (isset($sku_product_map[$product->id])) {
//             $sku = $sku_product_map[$product->id];
//             $product->thumbnail = $sku->thumbnail_image;
//             $product->images = json_decode($sku->image, true);
//             $product->unit_price = $sku->listed_price;
//             $product->tax = $sku->tax;
//         }
//         return $product;
//     });

//     return response()->json($merged_products);
// }

// public function search_city_products(Request $request)
// {
//     try {
//         $cities = $request->cities;

//         $t = DB::table('warehouse')->where('city', $cities)->first();

//         if ($t) {
//             $products = Product::where('user_id', $t->seller_id)->get();
//         } else {
//             if (is_string($cities) && $this->isJson($cities)) {
//                 $cities = json_decode($cities, true);
//             } else {
//                 $cities = explode(',', $cities);
//             }

//             $cities = array_map('trim', $cities);

//             if (!is_array($cities) || empty($cities)) {
//                 return response()->json(['error' => 'Invalid input format'], 400);
//             }

//             $products = Product::whereIn('add_warehouse', $cities)
//                 ->orWhere(function ($query) use ($cities) {
//                     foreach ($cities as $city) {
//                         $query->orWhere('cities', 'LIKE', "%$city%");
//                     }
//                 })
//                 ->get();
//         }

//         // Product IDs extract karein
//         $productIds = $products->pluck('id')->toArray();
//         // print_r($productIds);die();
//         // SKU product data fetch karein
//         $sku_products = DB::table('sku_product_new')
//             ->whereIn('product_id', $productIds)
//             ->whereNotNull('thumbnail_image')
//             ->select('product_id', 'thumbnail_image', 'image', 'listed_price', 'tax')
//             ->orderBy('id', 'desc')
//             ->get();

//         // SKU product data ko map karein
//         $sku_product_map = [];
//         foreach ($sku_products as $sku) {
//             $sku_product_map[$sku->product_id] = $sku;
//         }
//         //print_r($sku_product_map);
//         // Products me SKU data merge karein
       
//         $merged_products = $products->map(function ($product) use ($sku_product_map) {
//             $existingKeys = [];
//             if (isset($sku_product_map[$product->id])) {
//                 if(!in_array($product->id, $existingKeys)) {
//                     $existingKeys[] = $product->id;
//                     $sku = $sku_product_map[$product->id];
//                     $product->thumbnail = $sku->thumbnail_image; 
//                     $product->images = json_decode($sku->image, true);
//                     $product->unit_price = $sku->listed_price;
//                     $product->tax = $sku->tax;
//                     return $product;
//                 }
//             }
            
//         });
        
//         $response = [];
//         foreach ($merged_products as $merged_product) {
//             if($merged_product != null) {
//                 $response[] = $merged_product;
//             }
//         }
//         return response()->json($response);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => 'Something went wrong',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// }

public function search_city_products(Request $request)
{
    try {
        $cities = $request->cities;

        // Normalize input
        if (is_string($cities) && $this->isJson($cities)) {
            $cities = json_decode($cities, true);
        } elseif (is_string($cities)) {
            $cities = explode(',', $cities);
        }

        $cities = array_map('trim', (array) $cities);

        if (empty($cities)) {
            return response()->json(['error' => 'Invalid input format'], 400);
        }

        // Get seller IDs for selected cities
        $warehouses = DB::table('warehouse')->whereIn('city', $cities)->get();

        if ($warehouses->isEmpty()) {
            return response()->json(['error' => 'No warehouse found'], 404);
        }

        $sellerIds = $warehouses->pluck('seller_id')->unique()->toArray();

        // Get products for those sellers
        $products = Product::whereIn('user_id', $sellerIds)->get();

        if ($products->isEmpty()) {
            return response()->json(['error' => 'No products found'], 404);
        }

        $productIds = $products->pluck('id')->toArray();

        // Get all SKU records for those products
        $sku_products = DB::table('sku_product_new')
            ->whereIn('product_id', $productIds)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('product_id');

        // Filter products with SKU data
        $filtered_products = $products->filter(function ($product) use ($sku_products) {
            return $sku_products->has($product->id);
        })->values();

        // Merge SKU data and override product fields
        $merged = $filtered_products->map(function ($product) use ($sku_products) {
            $sku_list = $sku_products[$product->id]->values();
            $first_sku = $sku_list->first();
            if ($first_sku) {
                $product->thumbnail = $first_sku->thumbnail_image;
                $product->images = json_decode($first_sku->image, true);
                $product->unit_price =  $first_sku->variant_mrp; // ✅ Combine listed_price + variant_mrp
                $product->tax = $first_sku->tax;
                $product->discount = $first_sku->discount;
                $product->discount_type = $first_sku->discount_type;
            }

            return $product;
        });

        // Sort by product ID ASC
        $sorted = $merged->sortBy('id')->values();

        return response()->json($sorted);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $e->getMessage()
        ], 500);
    }
}







// Function to check if string is JSON
private function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

public function bulk_product(Request $request){
   

    $data = [
        'product_id' => $request->product_id,
        'seller_id' => $request->seller_id,
        'product_name' => $request->product_name,
        'quantity' => $request->quantity,
        'remarks' => $request->remarks,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    try {
        $pr = DB::table('app_bulk_product')->insert($data);
        if ($pr) {
            return response()->json(['message' => 'Sent successfully', 'status' => 1]);
        } else {
            return response()->json(['message' => 'Insert failed', 'status' => 0]);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage(), 'status' => 0]);
    }
}

public function search_warehouse_city(Request $request) {
    $cities = DB::table('warehouse')
        ->where('city', 'like', "%{$request->citi}%")
        ->pluck('city')  
        ->unique();       

    return response()->json($cities->values()); 
}



public function more_item()
{
    $lastRecord = DB::table('recently_view')
        ->where('type', 'products')
        ->orderBy('id', 'desc') 
        ->first(); 

    // Return response if no record found
    if (!$lastRecord) {
        return response()->json(['error' => 'No products found'], 404);
    }

     $product_ids = [$lastRecord->product_id];
    // dd($product_ids);
//return response()->json(['hii'=>$product_ids]);
    // Fetch more items
    $moreItem = ProductManager::more_items($product_ids);

    return response()->json(['more_item' => $moreItem], 200);
}



    public function product_recently_views(){

        $recently_view = DB::table('recently_view')
        ->where('type', 'products')
        ->where('user_id',(Auth::guard('api')->user()->id))
        ->orderBy('created_at', 'desc')
        ->limit(10) 
        ->get();
    
    //  dd($recently_view);
    $product_ids = $recently_view->pluck('product_id')->toArray();
    //dd($product_ids);
    $products = ProductManager::get_home_productsss($product_ids);
    $related_products = ProductManager::get_related_productsss($product_ids);
   
    return response()->json(['products' => $products,'related_product'=> $related_products], 200);

}






    
public function cart_recently_views(){

    
    $recently_view = DB::table('recently_view')->where('type','carts')->get();

    $product_ids = $recently_view->pluck('product_id')->toArray();

    $products = ProductManager::get_home_productsss($product_ids);
    $related_products = ProductManager::get_related_productsss($product_ids);

    return response()->json(['products' => $products,'related_product'=> $related_products], 200);

}
    

public function wishlist_recently_views(){

    
    $recently_view = DB::table('recently_view')->where('type','wishlist')->get();

    $product_ids = $recently_view->pluck('product_id')->toArray();

    $products = ProductManager::get_home_productsss($product_ids);
    $related_products = ProductManager::get_related_productsss($product_ids);

    return response()->json(['products' => $products,'related_product'=> $related_products], 200);

}

    public function get_searched_products(Request $request)
    {
        $suggestion = [];

        $query = $request->has('search') ? $request['search'] : null;
        if (!empty($query)) {
            $suggestions = Category::where('name', 'like', "%$query%")
                ->limit(5)  // Limiting the number of suggestions
                ->get(['id', 'name']);
                $suggestion = $suggestions;
            }

        $porduct_data = Product::active()->with([
            'reviews','rating',
            'seller.shop',
            'wish_list'=>function($query) use($request){
                return $query->where('customer_id', $request->user()->id ?? 0);
            },
            'compare_list'=>function($query) use($request){
                return $query->where('user_id', $request->user()->id ?? 0);
            }
        ])
        ->when($request['data_from'] == 'category', function ($query) use($request){
            $query->where('category_id', $request['id'])
            ->orWhere('sub_category_id', $request['id'])
            ->orWhere('sub_sub_category_id', $request['id']);
        })
        ->when($request->has('category') && $request['category'] != 'all', function ($query) use($request){
            $query->where('category_id', $request['id'])
            ->orWhere('sub_category_id', $request['id'])
            ->orWhere('sub_sub_category_id', $request['id']);
        })
        ->when($request['data_from'] == 'brand', function ($query) use($request){
            $query->where('brand_id', $request['id']);
        })
        ->when(!$request->has('data_from') || $request['data_from'] == 'latest', function ($query){
            return $query;
        });

        $query = $porduct_data;
        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with([
                'reviews','seller.shop',
                'wish_list'=>function($query) use($request){
                    return $query->where('customer_id', $request->user()->id ?? 0);
                },
                'compare_list'=>function($query) use($request){
                    return $query->where('user_id', $request->user()->id ?? 0);
                }
            ])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with([
                'reviews','seller.shop',
                'wish_list'=>function($query) use($request){
                    return $query->where('customer_id', $request->user()->id ?? 0);
                },
                'compare_list'=>function($query) use($request){
                    return $query->where('user_id', $request->user()->id ?? 0);
                }
            ])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with([
                'reviews','seller.shop',
                'wish_list'=>function($query) use($request){
                    return $query->where('customer_id', $request->user()->id ?? 0);
                },
                'compare_list'=>function($query) use($request){
                    return $query->where('user_id', $request->user()->id ?? 0);
                }
            ])->active()->where('discount', '!=', 0);
        }

        if ($request->has('search_category') && $request['search_category'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::with([
                'seller.shop',
                'wish_list'=>function($query) use($request){
                    return $query->where('customer_id', $request->user()->id ?? 0);
                },
                'compare_list'=>function($query) use($request){
                    return $query->where('user_id', $request->user()->id ?? 0);
                }
            ])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhereHas('tags',function($query)use($value){
                                $query->where('tag', 'like', "%{$value}%");
                            });
                    }
                })->pluck('id');

            if($product_ids->count()==0)
            {
                $product_ids = Translation::where('translationable_type', 'App\Model\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }

        $fetched = $query->latest();

        $common_query = $fetched;

        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach($common_query->get() as $rating){
            if(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >0 && $rating->rating[0]['average'] <2)){
                $rating_1 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=2 && $rating->rating[0]['average'] <3)){
                $rating_2 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=3 && $rating->rating[0]['average'] <4)){
                $rating_3 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >=4 && $rating->rating[0]['average'] <5)){
                $rating_4 += 1;
            }elseif(isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)){
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1'=>$rating_1,
            'rating_2'=>$rating_2,
            'rating_3'=>$rating_3,
            'rating_4'=>$rating_4,
            'rating_5'=>$rating_5,
        ];

        $products = $common_query->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $products_final = Helpers::product_data_formatting($products, true);

        // Categories start
        $categories = Category::withCount(['product'=>function($query){
            $query->where(['status'=>'1']);
            }])->with(['childes' => function ($query) {
                $query->with(['childes' => function ($query) {
                    $query->withCount(['sub_sub_category_product'])->where('position', 2);
                }])->withCount(['sub_category_product'])->where('position', 1);
            }, 'childes.childes'])
            ->where('position', 0)->get();
        // Categories End

        $brands = Brand::active()->withCount('brandProducts')->latest()->get();

        return [
            'total_size' => $products->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'products' => $products_final,
            'brands' => $brands,
            'category' => $categories,
            'rating' => $ratings,
            'suggestion'=>$suggestion,
        ];
    }

    public function product_filter(Request $request)
    {
        $categories = $request->category ?? [];
        $category = [];
        if($request->has('category') && count($request->category)>0)
        {
            foreach($categories as $category)
            {
                $cat_info = Category::where('id', $category)->first();
                $index = $cat_info ? array_search($cat_info->parent_id, $categories) : false;
                if ($index !== false) {
                    array_splice($categories, $index, 1);
                }
            }
            $category = Category::whereIn('id', $request->category)
                ->select('id', 'name')
                ->get();
        }

        $brands = [];
        if($request->has('brand') && count($request->brand)>0)
        {
            $brands = Brand::whereIn('id', $request->brand)->select('id','name')->get();
        }
        $rating = $request->rating ?? [];

        // products search
        $products = Product::active()->with(['wish_list'=>function($query) use($request){
                    return $query->where('customer_id', $request->user()->id ?? 0);
                }, 'compare_list'=>function($query) use($request){
                    return $query->where('user_id', $request->user()->id ?? 0);
            }])
            ->when($request->has('shop_id') && !empty($request->shop_id) && $request->shop_id == '0', function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($request->has('shop_id') && !empty($request->shop_id) && $request->shop_id != '0', function ($query) use ($request) {
                return $query->where(['added_by' => 'seller', 'user_id'=> $request->shop_id]);
            })
            ->when($request->has('brand') && count($request->brand)>0, function($query) use($request){
                return $query->whereIn('brand_id', $request->brand);
            })
            ->when($request->has('category') && count($request->category)>0, function($query) use($categories){
                return $query->whereIn('category_id', $categories)
                    ->orWhereIn('sub_category_id', $categories)
                    ->orWhereIn('sub_sub_category_id', $categories);
            })
            ->when($request->has('sort_by') && !empty($request->sort_by), function($query) use($request){
                $query->when($request['sort_by'] == 'low-high', function($query){
                        return $query->orderBy('unit_price', 'ASC');
                    })
                    ->when($request['sort_by'] == 'high-low', function($query){
                        return $query->orderBy('unit_price', 'DESC');
                    })
                    ->when($request['sort_by'] == '', function($query){
                        return $query->latest();
                    });
            })
            ->when($request->has('sort_by_name') && !empty($request->sort_by_name), function($query) use($request){
                    $query->when($request['sort_by'] == 'latest', function($query){
                        return $query->latest();
                    })
                    ->when($request['sort_by'] == 'a-z', function($query){
                        return $query->orderBy('name', 'ASC');
                    })
                    ->when($request['sort_by'] == 'z-a', function($query){
                        return $query->orderBy('name', 'DESC');
                    })
                    ->when($request['sort_by'] == '', function($query){
                        return $query->latest();
                    });
            })
            ->when(!empty($request['price_min']) || !empty($request['price_max']), function($query) use($request){
                return $query->whereBetween('unit_price', [Helpers::convert_manual_currency_to_usd($request['price_min'], $request['currency']), Helpers::convert_manual_currency_to_usd($request['price_max'], $request['currency'])]);
            })
            ->when(!empty($request->colors), function($query) use($request){
                return $query->where(function($query) use ($request) {
                    foreach ($request->colors as $color) {
                        $query->orWhere('colors', 'like', '%'.$color.'%');
                    }
                });
            })
            ->when(!empty($request->rating), function($query) use($request){
                $query->with(['rating'])->whereHas('rating', function($query) use($request){
                    return $query;
                });
            });

        $products = $products->paginate($request['limit'], ['*'], 'page', $request['offset']);

        return [
            'total_size' => $products->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'products' => $products->items(),
            'selected_brands' => $brands,
            'selected_category' => $category,
        ];
    }

    public function get_product($slug)
    {
        $product = Product::with(['reviews.customer', 'seller.shop','tags'])->where(['slug' => $slug])->first();
        if (isset($product)) {
            $product = Helpers::product_data_formatting($product, false);

            if(isset($product->reviews) && !empty($product->reviews)){
                $overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews);
                $product['average_review'] = $overallRating[0];
            }else{
                $product['average_review'] = 0;
            }

            $temporary_close = Helpers::get_business_settings('temporary_close');
            $inhouse_vacation = Helpers::get_business_settings('vacation_add');
            $inhouse_vacation_start_date = $product['added_by'] == 'admin' ? $inhouse_vacation['vacation_start_date'] : null;
            $inhouse_vacation_end_date = $product['added_by'] == 'admin' ? $inhouse_vacation['vacation_end_date'] : null;
            $inhouse_temporary_close = $product['added_by'] == 'admin' ? $temporary_close['status'] : false;
            $product['inhouse_vacation_start_date'] = $inhouse_vacation_start_date;
            $product['inhouse_vacation_end_date'] = $inhouse_vacation_end_date;
            $product['inhouse_temporary_close'] = $inhouse_temporary_close;
        }
        return response()->json($product, 200);
    }

    public function get_best_sellings(Request $request)
    {
        $products = ProductManager::get_best_selling_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);

        return response()->json($products, 200);
    }

    public function get_home_categories()
    {
        $categories = Category::where('home_status', true)->get();
        $categories->map(function ($data) {
            $data['products'] = Helpers::product_data_formatting(CategoryManager::products($data['id']), true);
            return $data;
        });
        return response()->json($categories, 200);
    }

    public function get_related_products($id)
    {
        if (Product::find($id)) {
            $products = ProductManager::get_related_products($id);
            $products = Helpers::product_data_formatting($products, true);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' => translate('Product not found!')]
        ], 404);
    }

    public function get_product_reviews($id)
    {
        $reviews = Review::with(['customer'])->where(['product_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }

    public function get_product_rating($id)
    {
        try {
            $product = Product::find($id);
            $overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function counter($product_id)
    {
        try {
            $countOrder = OrderDetail::where('product_id', $product_id)->count();
            $countWishlist = Wishlist::where('product_id', $product_id)->count();
            return response()->json(['order_count' => $countOrder, 'wishlist_count' => $countWishlist], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function social_share_link($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $link = route('product', $product->slug);
        try {

            return response()->json($link, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function submit_product_review(Request $request)
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
                'delivery_man_id'=> null,
                'customer_id'=>$request->user()->id,
                'product_id'=>$request->product_id
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

    public function submit_deliveryman_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where([
                'id'=>$request->order_id,
                'customer_id'=>$request->user()->id,
                'payment_status'=>'paid'])->first();

        if(!isset($order->delivery_man_id)){
            return response()->json(['message' => translate('Invalid review!')], 403);
        }

        Review::updateOrCreate(
            [
                'delivery_man_id'=>$order->delivery_man_id,
                'customer_id'=>$request->user()->id,
                'order_id' => $order->id
            ],
            [
                'customer_id' => $request->user()->id,
                'order_id' => $order->id,
                'delivery_man_id' => $order->delivery_man_id,
                'comment' => $request->comment,
                'rating' => $request->rating,
            ]
        );

        return response()->json(['message' => translate('successfully review submitted!')], 200);
    }

    public function get_shipping_methods(Request $request)
    {
        $methods = ShippingMethod::where(['status' => 1])->get();
        return response()->json($methods, 200);
    }

    public function get_discounted_product(Request $request)
    {
        $products = ProductManager::get_discounted_product($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }
}
