<?php

namespace App\CPU;
use App\CPU\BrandManager;
use App\Model\ProductCompare;
use App\Model\Review;
use App\Model\Product;
use App\Model\HomeProduct;
use App\Model\Category;
use App\CPU\Helpers;
use App\Model\OrderDetail;
use App\Model\Translation;
use App\Model\ShippingMethod;
use App\Model\Wishlist;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Model\ShippingType;
use App\Model\CategoryShippingCost;

class ProductManager
{
    public static function get_product($id)
    {
        return Product::active()->temporary()->with(['rating', 'seller.shop','tags'])->where('id', $id)->where('request_status',1)->where('status',1)->first();
    }

    public static function get_latest_products($limit = 10, $offset = 1)
    {
        $paginator = Product::active()->with(['rating','tags','seller.shop'])->where('request_status',1)->where('status',1)->latest()->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }
    
     public static function get_home_products()
     {
         $limit = 4;
         $offset = 1;
         $result = [];
         
        //change review to ratting
        $result['featured_products'] = Helpers::product_data_formatting(Product::with(['rating','tags'])->active()->temporary()
            ->where('featured', 1)
            ->where('request_status',1)
            ->where('status',1)
            ->limit(4)->get(),true);
         
       $result['new_arriavals_products'] =  $paginator1 = Helpers::product_data_formatting(Product::active()->temporary()->with(['rating','tags','seller.shop'])->where('request_status',1)->where('status',1)->latest()->limit(4)->get(),true);
        /*$paginator->count();*/
       
        
         //change reviews to rattings
        $paginator = OrderDetail::with('product.rating')
            ->whereHas('product', function ($query) {
                $query->active()->temporary();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
           ->limit(4)->get();

        $data = [];
        foreach ($paginator as $order) {
            array_push($data, $order->product);
        }
        
        $result['best_selling_products'] = Helpers::product_data_formatting($data,true);
        
        //change review to ratting
        $result['top_products'] = Helpers::product_data_formatting(Product::with(['rating','tags'])->active()->temporary()
            ->withCount(['reviews'])->orderBy('reviews_count', 'DESC')
            ->where('request_status',1)
            ->where('status',1)
            ->limit(4)->get(),true);
            
        $result['discount_products'] = Helpers::product_data_formatting(Product::temporary()->with(['rating','tags'])->active()->where('discount', '>', 0)->where('request_status',1)->where('status',1)
            ->orderByRaw("
                CASE
                    WHEN discount_type = 'percent' THEN CAST(REPLACE(discount, '%', '') AS DECIMAL(10, 2))
                    WHEN discount_type = 'flat' THEN (CAST(discount AS DECIMAL(10, 2)) / unit_price) * 100
                    ELSE 0
                END DESC
            ")  
        
          ->limit(4)->get(),true);
        
        return $result;
        /* return [
            'products' => $result
        ];*/
     }
     


     public static function get_home_productsss($product_ids)
{
    // dd($product_ids);
    $product_ids = array_reverse($product_ids); 
  //dd(Product::withCount('reviews')->where('status', 1)->whereIn('id', $product_ids)->get());
    // Fetch products with reviews and other data
    $recently_view = Product::withCount('reviews')
        ->where('status', 1)
        ->whereIn('id', $product_ids)
        ->orderByRaw('FIELD(id, ' . implode(',', $product_ids) . ')') // Maintain order
        ->get();
    // $recently_view = Product::with(['rating', 'tags']) 
    //     ->active()
    //     ->withCount(['reviews'])
    //     ->orderBy('reviews_count', 'DESC')
    //     ->where('status', 1)
    //     ->whereIn('id', $product_ids)
    //     ->limit(10)
    //     ->get();

       // dd($recently_view);
    // Fetch SKU products with thumbnails
    $sku_products = DB::table('sku_product_new')
        ->whereIn('product_id', $product_ids)
        ->whereNotNull('thumbnail_image')
        ->get();
      
    // Convert SKU products to associative array for easy lookup
    $sku_product_map = [];
    foreach ($sku_products as $sku) {
        $sku_product_map[$sku->product_id] = $sku;
    }

    // Merge SKU thumbnail and images with recently viewed products
    $result['recently_view'] = $recently_view->map(function ($product) use ($sku_product_map) {
        if (isset($sku_product_map[$product->id])) {
            $sku = $sku_product_map[$product->id];
            $product->thumbnail = $sku->thumbnail_image; // Replace thumbnail
            $product->images = json_decode($sku->image, true); // Replace images
            $product->unit_price = $sku->variant_mrp; // ✅ Replace unit_price with listed_price
            $product->tax = $sku->tax;
            $product->discount_type = $sku->discount_type;
            $product->discount = $sku->discount;
            
        }
        return $product;
    });

    // Assign SKU products to result
   // $result['sku_product'] = $sku_products;

    return $result;
}

     
     

      public static function get_home_products_custom()
     {
        
       
         $result = [];
         
         $result['featured_products'] = Helpers::product_data_formatting(
            HomeProduct::with(['product'])
                ->where('section_type', 'feature')
                ->get()
                ->pluck('product'),
            true
        );
       
        // ✅ Sare product IDs collect karo
        $allProductIds = array_column($result['featured_products'], 'id');
        
        if (!empty($allProductIds)) {
            // ✅ SKU products fetch karo (sirf relevant IDs ke liye, optimized select)
            $sku_products = DB::table('sku_product_new')
                ->whereIn('product_id', $allProductIds)
                ->whereNotNull('thumbnail_image')
                ->select('product_id', 'thumbnail_image', 'image','listed_price','variant_mrp','discount','discount_type') // Sirf required columns fetch karein
                ->get()
                ->pluck(null, 'product_id') // Direct mapping for fast lookup
                ->toArray();
        
            // ✅ Featured products me SKU data merge karo
            $result['featured_products'] = collect($result['featured_products'])->map(function ($product) use ($sku_products) {
                if (isset($sku_products[$product['id']])) {
                    $sku = $sku_products[$product['id']];
                    $product['thumbnail'] = $sku->thumbnail_image;
                    $product['images'] = json_decode($sku->image ?? '[]', true);
                    $product['unit_price'] = $sku->variant_mrp;
                    $product['discount'] = $sku->discount;
                    $product['discount_type'] = $sku->discount_type;

                }
                return $product;
            })->toArray();
        }

       $result['new_arriavals_products'] =   Helpers::product_data_formatting(HomeProduct::with(['product'])
            ->where('section_type', 'new_arrival')->get()->pluck('product'),true);
        
        $result['best_selling_products'] = Helpers::product_data_formatting(HomeProduct::with(['product'])
            ->where('section_type', 'best_seller')->get()->pluck('product'),true);

            $allProductIds = array_column($result['best_selling_products'], 'id');

            $sku_products = DB::table('sku_product_new')
            ->whereIn('product_id', $allProductIds)
            ->whereNotNull('thumbnail_image')
            ->get();
        
        // ✅ SKU products ko ek array me map karo for easy lookup
        $sku_product_map = [];
        foreach ($sku_products as $sku) {
            $sku_product_map[$sku->product_id] = $sku;
        }
        $result['best_selling_products'] = array_map(function ($product) use ($sku_product_map) {
            if (isset($sku_product_map[$product['id']])) {
                $sku = $sku_product_map[$product['id']];
                $product['thumbnail'] = $sku->thumbnail_image; // Thumbnail replace
                $product['images'] = json_decode($sku->image, true); // Images replace
                $product['unit_price'] = $sku->variant_mrp;
                $product['discount'] = $sku->discount;
                $product['discount_type'] = $sku->discount_type;
            }
            return $product;
        }, $result['best_selling_products']);
       
        $result['top_products'] = Helpers::product_data_formatting(HomeProduct::with(['product'])
            ->where('section_type', 'top_products')->get()->pluck('product'),true);
            
            $allProductIds = array_column($result['top_products'], 'id');

            $sku_products = DB::table('sku_product_new')
    ->whereIn('product_id', $allProductIds)
    ->whereNotNull('thumbnail_image')
    ->get();

// ✅ SKU products ko ek array me map karo for easy lookup
$sku_product_map = [];
foreach ($sku_products as $sku) {
    $sku_product_map[$sku->product_id] = $sku;
}

$result['top_products'] = array_map(function ($product) use ($sku_product_map) {
    if (isset($sku_product_map[$product['id']])) {
        $sku = $sku_product_map[$product['id']];
        $product['thumbnail'] = $sku->thumbnail_image; // Thumbnail replace
        $product['images'] = json_decode($sku->image, true); // Images replace
        $product['unit_price'] = $sku->variant_mrp;
        $product['discount'] = $sku->discount;
        $product['discount_type'] = $sku->discount_type;

    }
    return $product;
}, $result['top_products']);

        $result['discount_products'] = Helpers::product_data_formatting(HomeProduct::with(['product'])
            ->where('section_type', 'discounted')->get()->pluck('product'),true);
        
        return $result;
        
     }
     
     
     
    // public static function get_featured_products($limit = 10, $offset = 1)
    // {
    //     //change review to ratting
    //     $paginator = Product::with(['rating','tags'])->active()->temporary()
    //         ->where('featured', 1)
    //         ->withCount(['order_details'])->orderBy('order_details_count', 'DESC')
    //         ->where('request_status',1)
    //         ->where('status',1)
    //         ->paginate($limit, ['*'], 'page', $offset);

    //     return [
    //         'total_size' => $paginator->total(),
    //         'limit' => (int)$limit,
    //         'offset' => (int)$offset,
    //         'products' => $paginator->items()
    //     ];
    // }


    public static function get_featured_products($limit = 10, $offset = 1)
    {
        $paginator = Product::with(['rating', 'tags'])
            ->active()
            ->temporary()
            ->where('featured', 1)
            ->where('request_status', 1)
            ->where('status', 1)
            ->withCount(['order_details'])
            ->leftJoin('sku_product_new', 'products.id', '=', 'sku_product_new.product_id') // <-- Join here
            ->select('products.*', 'sku_product_new.*') // select fields you need from sku_product_new
            ->orderBy('order_details_count', 'DESC')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => (int) $limit,
            'offset' => (int) $offset,
            'products' => $paginator->items()
        ];
    }


    // public static function get_top_rated_products($limit = 10, $offset = 1)
    // {
    //     //change review to ratting
    //     $reviews = Product::with(['rating','tags'])->active()->temporary()
    //         ->withCount(['reviews'])->orderBy('reviews_count', 'DESC')
    //         ->where('request_status',1)
    //         ->where('status',1)
    //         ->paginate($limit, ['*'], 'page', $offset);

    //     return [
    //         'total_size' => $reviews->total(),
    //         'limit' => (int)$limit,
    //         'offset' => (int)$offset,
    //         'products' => $reviews
    //     ];
    // }

    public static function get_top_rated_products($limit = 10, $offset = 1)
    {
        $reviews = Product::with(['rating', 'tags'])
            ->active()
            ->temporary()
            ->where('request_status', 1)
            ->where('status', 1)
            ->withCount(['reviews'])
            ->leftJoin('sku_product_new', 'products.id', '=', 'sku_product_new.product_id') // <-- join here
            ->select('products.*', 'sku_product_new.*') // select needed fields from sku_product_new
            ->orderBy('reviews_count', 'DESC')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $reviews->total(),
            'limit' => (int) $limit,
            'offset' => (int) $offset,
            'products' => $reviews->items()
        ];
    }

    
    
    public static function get_random_products($product_id)
    {
        //change random product
       $product = Product::find($product_id);
        return Product::active()->with(['rating','tags'])
            ->where('id', '!=', $product->id)->where('request_status',1)->where('status',1)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    public static function get_best_selling_products($limit = 10, $offset = 1)
    {
        //change reviews to rattings
        $paginator = OrderDetail::with('product.rating')
            ->whereHas('product', function ($query) {
                $query->active()->temporary();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $data = [];
        foreach ($paginator as $order) {
            array_push($data, $order->product);
        }

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $data
        ];
    }

    // public static function get_related_products($product_id)
    // {
    //     // Fetch main product
    //     $product = Product::find($product_id);
    //     if (!$product) {
    //         return collect(); // Agar product nahi mila toh empty collection return karo
    //     }
    
    //     // Fetch related products
    //     // $related_products = Product::active()
    //     //     ->where('category_ids', $product->category_ids)
    //     //     ->where('id', '!=', $product->id)
    //     //     ->where('request_status', 1)
    //     //     ->where('status', 1)
    //     //     ->limit(10)
    //     //     ->get();

    //     $categoryIds = collect(json_decode($product->category_ids, true))
    //         ->pluck('id')
    //         ->toArray();

    //     $related_products = Product::active()
    //         ->select(
    //             'products.*',
    //             'sku_product_new.discount_type',
    //             'sku_product_new.discount_percent'
    //         )
    //         ->join('sku_product_new', 'products.id', '=', 'sku_product_new.product_id')
    //         ->where('products.id', '!=', $product->id)
    //         ->where('products.request_status', 1)
    //         ->where('products.status', 1)
    //         ->where(function ($q) use ($categoryIds) {
    //             foreach ($categoryIds as $catId) {
    //                 $q->orWhereJsonContains('products.category_ids', ['id' => (string) $catId]);
    //             }
    //         })
    //         ->distinct()
    //         ->limit(10)
    //         ->get();

    
    //     // Collect all related product IDs
    //     $related_product_ids = $related_products->pluck('id');
    
    //     // Fetch SKU products for related products
    //     $sku_products = DB::table('sku_product_new')
    //         ->whereIn('product_id', $related_product_ids)
    //         ->whereNotNull('thumbnail_image')
    //         ->select('product_id', 'thumbnail_image', 'image', 'listed_price') // ✅ Fetch listed_price
    //         ->get();
    
    //     // Convert SKU products into an associative array for easy lookup
    //     $sku_product_map = [];
    //     foreach ($sku_products as $sku) {
    //         $sku_product_map[$sku->product_id] = $sku;
    //     }
    
    //     // Merge SKU product data into related products
    //     $merged_related_products = $related_products->map(function ($product) use ($sku_product_map) {
    //         if (isset($sku_product_map[$product->id])) {
    //             $sku = $sku_product_map[$product->id];
    //             $product->thumbnail = $sku->thumbnail_image; // ✅ Replace thumbnail
    //             $product->images = json_decode($sku->image, true); // ✅ Replace images
    //             $product->unit_price = $sku->listed_price; // ✅ Replace unit_price with listed_price
    //         }
    //         return $product;
    //     });
    
    //     return $merged_related_products;
    // }

    public static function get_related_products($product_id)
    {
        // Fetch main product
        $product = Product::find($product_id);
        if (!$product) {
            return collect(); // return empty collection if product not found
        }

        // Extract category IDs from JSON field
        $categoryIds = collect(json_decode($product->category_ids, true))
            ->pluck('id')
            ->toArray();

        // Fetch related products with join on sku_product_new
        $related_products = Product::active()
            ->join('sku_product_new', 'products.id', '=', 'sku_product_new.product_id')
            ->select(
                'products.*',
                'sku_product_new.thumbnail_image',
                'sku_product_new.image',
                'sku_product_new.listed_price',
                'sku_product_new.discount_type',
                'sku_product_new.discount_percent'
            )
            ->where('products.id', '!=', $product->id)
            ->where('products.request_status', 1)
            ->where('products.status', 1)
            ->where(function ($q) use ($categoryIds) {
                foreach ($categoryIds as $catId) {
                    $q->orWhereJsonContains('products.category_ids', ['id' => (string) $catId]);
                }
            })
            ->distinct()
            ->limit(10)
            ->get();

        // Merge and format fields
        $merged_related_products = $related_products->map(function ($product) {
            $product->thumbnail = $product->thumbnail_image; // ✅ Replace thumbnail
            $product->images = json_decode($product->image, true); // ✅ Decode image JSON
            $product->unit_price = $product->listed_price; // ✅ Replace unit_price with listed_price
            unset($product->thumbnail_image, $product->image); // optional: remove raw columns
            return $product;
        });

        return $merged_related_products;
    }

    


   public static function get_related_productsss($product_ids)
{
    $products = Product::whereIn('id', $product_ids)->get(); // सभी प्रोडक्ट्स लाएं

    $related_products = collect();

    foreach ($products as $product) {
        $related = Product::active()
            ->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->where('request_status', 1)
            ->where('status', 1)
            ->limit(10)
            ->get();

        $related_products = $related_products->merge($related); // संबंधित प्रोडक्ट्स जोड़ें
    }

    // Duplicate products remove karke sirf 10 rakhein
    $related_products = $related_products->unique('id')->take(10);

    // ✅ SKU products ko ek bar fetch karein (loop ke bahar)
    $sku_products = DB::table('sku_product_new')
        ->whereIn('product_id', $related_products->pluck('id')) // Collect related product IDs
        ->whereNotNull('thumbnail_image')
        ->get();

    // ✅ SKU products ka data map karna easy lookup ke liye
    $sku_product_map = [];
    foreach ($sku_products as $sku) {
        $sku_product_map[$sku->product_id] = $sku;
    }

    // ✅ Related products me SKU thumbnail aur images merge karein
    return $related_products->map(function ($product) use ($sku_product_map) {
        if (isset($sku_product_map[$product->id])) {
            $sku = $sku_product_map[$product->id];
            $product->thumbnail = $sku->thumbnail_image; // Thumbnail replace
            $product->images = json_decode($sku->image, true); // Images replace
            $product->unit_price = $sku->variant_mrp; // ✅ Replace unit_price with listed_price
            $product->discount = $sku->discount; 
            $product->discount_type = $sku->discount_type; // ✅ Replace unit_price with listed_price
            $product->tax = $sku->tax;
        }
        return $product;
    });
}

    

public static function more_items($product_id)
{
    
    $more = Product::where('id', $product_id)->first(); // प्रोडक्ट लाएं

    if (!$more) {
        return collect(); 
    }

    // पहले समान सब-कैटेगरी वाले सभी प्रोडक्ट्स प्राप्त करें
    $products = Product::where('sub_category_id', $more->sub_category_id)
        ->where('sub_sub_category_id', '!=', $more->sub_sub_category_id)
        ->where('status', 1)
        ->limit(10)
        ->get();
     
       // dd($products);
    // ✅ SKU products ko fetch karein (loop ke bahar)
    $sku_products = DB::table('sku_product_new')
        ->whereIn('product_id', $products->pluck('id')) // Corrected variable
        ->whereNotNull('thumbnail_image')
        ->get();

    // ✅ SKU products ka data map karna easy lookup ke liye
    $sku_product_map = [];
    foreach ($sku_products as $sku) {
        $sku_product_map[$sku->product_id] = $sku;
    }

    // ✅ Products me SKU thumbnail aur images merge karein
    $updated_products = $products->map(function ($product) use ($sku_product_map) {
        if (isset($sku_product_map[$product->id])) {
            $sku = $sku_product_map[$product->id];
            $product->thumbnail = $sku->thumbnail_image; // Thumbnail replace
            $product->images = json_decode($sku->image, true); // Images replace
            $product->unit_price = $sku->variant_mrp; // ✅ Replace unit_price with listed_price
            $product->discount = $sku->discount; // ✅ Replace unit_price with listed_price
            $product->discount_type = $sku->discount_type; // ✅ Replace unit_price with listed_price
            $product->tax = $sku->tax; 
        }
        return $product;
    });

    return response()->json($updated_products);
}

    
    
    public static function search_products($name, $category='all', $limit = 20, $offset = 1)
    {
       $key = explode(' ', $name);
       
        //$key = [base64_decode($name)];
       
        $paginator = Product::active()->temporary()->where('status',1)->with(['rating','tags'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('products.name', 'like', "%{$value}%");
                /*->orWhereHas('tags',function($query)use($key){
                    $query->where(function($q)use($key){
                        foreach ($key as $value) {
                            $q->where('tag', 'like', "%{$value}%");
                        }
                    });
                });*/
            }
        });
        
        if (isset($category) && $category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('products.id', $product_ids);
        }else{
            $query = $paginator;
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }
    
public static function filter_products(
    $category = 'all', $subcategory = 'all', $sub_sub_category_id = 'all', $brand = 'all', 
    $priceMin = null, $priceMax = null, $reviewMin = null, $reviewMax = null, 
    $featured_products = false, $new_arriavals_products = false, $top_products = false, 
    $discount_products = false, $high_to_low = false, $low_to_high = false, 
    $option = null, $color = null, $limit = 10, $offset = 1, $best_selling = false, 
    $tags = null,$search = null,$free_shipping = false,$Isdealoftheday = false
) {
    
    $category = json_decode($category, true) ?? 'all';
    $subcategory = json_decode($subcategory, true) ?? 'all';
    $sub_sub_category_id = json_decode($sub_sub_category_id, true) ?? 'all';
    $brand = json_decode($brand, true) ?? 'all';
    $color = json_decode($color, true) ?? null;  // Decoded color
    $priceMin = $priceMin ?? null;
    $priceMax = $priceMax ?? null;
    $reviewMin = $reviewMin ?? null;
    $reviewMax = $reviewMax ?? null;
    $featured_products = $featured_products ?? false;
    $new_arriavals_products = $new_arriavals_products ?? false;
    $top_products = $top_products ?? false;
    $discount_products = $discount_products ?? false;
    $high_to_low = $high_to_low ?? false;
    $low_to_high = $low_to_high ?? false;
    
    $option = $option ?? null;
    $tags = $tags ?? null;
    $ids = '';
    
    if ($tags && $tags != null && trim($tags,"'\"")!='') {
        $ids = array_map('intval', explode(',', $tags)); // Convert to array of integers
    }
    
     $limit = $limit ?? false;
                 
     $best_selling = $best_selling ?? false;

                // Initialize the query string
                $queryString = '';
                $queryString2 = '';
                
                if($option && $option != null && $option != 'null' && trim($option,"'\"") != ""){ 
                    $allOption = explode( ',', $option);
                     // Build the query string for options
                    foreach ($allOption as $row) {
                        if ($row !== null) {
                            $queryString .= " OR choice_options LIKE '%$row%'";
                        }
                    }
                }
               
                
                if($best_selling != 'false' && $best_selling != false){ 
                    $queryString2 = [];  
                    $bestSellingPaginator = OrderDetail::with('product.rating')
                        ->whereHas('product', function ($query) {
                            $query->active()->temporary();
                        })
                        ->select('product_id', DB::raw('COUNT(product_id) as count'))
                        ->groupBy('product_id')
                        ->orderBy("count", 'desc')
                        ->get();
                    
                    foreach ($bestSellingPaginator as $order) {
                        
                        if ($order->product !== null) {
                            $id = $order->product->id;
                            $queryString2[] = $id;
                        }
                    }
                    
                }
    

    $matchedProductIds = [];  // Initialize array to store matched product IDs
    $products = false;
    $query = Product::active()
        ->temporary()
        ->where('products.status', 1)
        ->where('products.request_status', 1)
        ->with(['rating', 'reviews', 'tags','deal_product']);
    
    // Fetch all products initially
    if($queryString2 != null){ 
        $query->whereIn('products.id', $queryString2);
    }
    elseif($Isdealoftheday != 'false' && $Isdealoftheday != false){
               $query->whereHas('deal_product', function ($query) {
                    $query->where('deal_of_the_days.status', 1)
                          ->whereDate('start_date_time', '<=', now())
                          ->whereDate('expire_date_time', '>=', now());
                });
        
    }elseif($category != 'all' || $subcategory != 'all' || $sub_sub_category_id != 'all' || $brand != 'all' || $color != null){
     $products = $query->get();     
    }

    if($products){
    foreach ($products as $product) {
        $productColors = json_decode($product->colors, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            
            $productColors = [];
        }
    
        if (
            ($category != 'all' && $category != '' && (is_array($category) ? in_array($product->category_id, $category) : $product->category_id == $category)) ||
            ($subcategory != 'all' && (is_array($subcategory) ? in_array($product->sub_category_id, $subcategory) : $product->sub_category_id == $subcategory)) ||
            ($sub_sub_category_id != 'all' && (is_array($sub_sub_category_id) ? in_array($product->sub_sub_category_id, $sub_sub_category_id) : $product->sub_sub_category_id == $sub_sub_category_id)) ||
            ($brand != 'all' && (is_array($brand) ? in_array($product->brand_id, $brand) : $product->brand_id == $brand)) ||
            ($color != null && trim($color,"'\"")!='' && $color != 'null' && (is_array($color) ? count(array_intersect($color, $productColors)) > 0 : in_array($color, $productColors)))
        ) {
            $matchedProductIds[] = $product->id;
        }
    }
    
     if(($category != 'all' || $subcategory != 'all' || $sub_sub_category_id != 'all' || $brand != 'all' || $color != null) && empty($matchedProductIds)){ 
         $query->where('products.id', 0);
         $fetched = $query->paginate($limit, ['*'], 'page', $offset);   
        return [
                'total_size' => $fetched->total(),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'products' => $fetched->items(),
            ];
    }
    
  }
   
   //return $matchedProductIds;
        if (!empty($matchedProductIds)) {
               $query->whereIn('products.id', $matchedProductIds);
        }   
            $query->when($priceMin !== null && $priceMin !='null' , function ($q) use ($priceMin) {
                        return $q->where('unit_price', '>=', $priceMin);
                    })
                    ->when($priceMax !== null && $priceMax !='null', function ($q) use ($priceMax) {
                        return $q->where('unit_price', '<=', $priceMax);
                    })
                    ->when($tags !='null' && $tags !== null, function ($q) use ($ids) {
                        foreach ($ids as $id) {
                            $q->where('service_type', 'REGEXP', '\"' . $id . '\"');
                        }
                        return $q;
                    })
                    
                    ->when($queryString != "", function ($q) use ($queryString) {
                        return $q->whereRaw(substr($queryString, 3));
                    })
                    ->when($reviewMax != null && $reviewMax !='null', function ($q) use ($reviewMax) {
                        
                        return $q->whereHas('reviews', function ($query) use ($reviewMax) {
                            $query->where('reviews.rating', '<=', $reviewMax);
                            
                        });
                    })
                    ->when($featured_products != 'false' && $featured_products != false, function ($q) use ($featured_products) {
                        return $q->where('featured', 1)->withCount(['order_details'])->orderBy('order_details_count', 'DESC');
                    })
                     ->when($new_arriavals_products != 'false' && $new_arriavals_products != false, function ($q) use ($new_arriavals_products) {
                        return $q->with(['seller.shop'])->latest();
                    })
                     ->when($top_products != 'false' && $top_products != false, function ($q) use ($top_products) {
                        return $q->withCount(['reviews'])->orderBy('reviews_count', 'DESC');
                    })
                     ->when($discount_products != 'false' && $discount_products != false, function ($q) use ($discount_products) {
                        return $q->where('discount', '!=', 0)->latest();
                    })
                     ->when($high_to_low != 'false' && $high_to_low != false, function ($q) use ($high_to_low) {
                         return $q->orderByDesc('unit_price');
                    })
                     ->when($low_to_high != 'false' && $low_to_high != false, function ($q) use ($low_to_high) {
                          return $q->orderBy('products.unit_price','asc');
                    })
                    ->when($free_shipping != 'false' && $free_shipping != false, function ($q) use ($low_to_high) {
                          return $q->where('products.free_delivery',1);
                    })
                    ->when($search !== null && $search !='null', function ($q) use ($search) {
                    return $q->where('products.name', 'like', "%{$search}%");
                });

               $fetched = $query->paginate($limit, ['*'], 'page', $offset);

            return [
                'total_size' => $fetched->total(),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'products' => $fetched->items(),
            ];
        }

  public static function filter_products_test(
    $category = 'all', $subcategory = 'all', $sub_sub_category_id = 'all', $brand = 'all', 
    $priceMin = null, $priceMax = null, $reviewMin = null, $reviewMax = null, 
    $featured_products = false, $new_arriavals_products = false, $top_products = false, 
    $discount_products = false, $high_to_low = false, $low_to_high = false, 
    $option = null, $color = null, $limit = 10, $offset = 1, $best_selling = false, 
    $tags = null,$search = null,$free_shipping = false , $Isdealoftheday = false
) {
    
    $category = json_decode($category, true) ?? 'all';
    $subcategory = json_decode($subcategory, true) ?? 'all';
    $sub_sub_category_id = json_decode($sub_sub_category_id, true) ?? 'all';
    $brand = json_decode($brand, true) ?? 'all';
    $color = json_decode($color, true) ?? null;  // Decoded color
    $priceMin = $priceMin ?? null;
    $priceMax = $priceMax ?? null;
    $reviewMin = $reviewMin ?? null;
    $reviewMax = $reviewMax ?? null;
    $featured_products = $featured_products ?? false;
    $new_arriavals_products = $new_arriavals_products ?? false;
    $top_products = $top_products ?? false;
    $discount_products = $discount_products ?? false;
    $high_to_low = $high_to_low ?? false;
    $low_to_high = $low_to_high ?? false;
    
    $option = $option ?? null;
    $tags = $tags ?? null;
    $ids = '';
    
    if ($tags && $tags != null && trim($tags,"'\"")!='') {
        $ids = array_map('intval', explode(',', $tags)); // Convert to array of integers
    }
    
     $limit = $limit ?? false;
                 
     $best_selling = $best_selling ?? false;

                // Initialize the query string
                $queryString = '';
                $queryString2 = '';
                
                if($option && $option != null && $option != 'null' && trim($option,"'\"") != ""){ 
                    $allOption = explode( ',', $option);
                     // Build the query string for options
                    foreach ($allOption as $row) {
                        if ($row !== null) {
                            $queryString .= " OR choice_options LIKE '%$row%'";
                        }
                    }
                }
               
                
                if($best_selling != 'false' && $best_selling != false){ 
                    $queryString2 = [];  
                    $bestSellingPaginator = OrderDetail::with('product.rating')
                        ->whereHas('product', function ($query) {
                            $query->active()->temporary();
                        })
                        ->select('product_id', DB::raw('COUNT(product_id) as count'))
                        ->groupBy('product_id')
                        ->orderBy("count", 'desc')
                        ->get();
                    
                    foreach ($bestSellingPaginator as $order) {
                        
                        if ($order->product !== null) {
                            $id = $order->product->id;
                            $queryString2[] = $id;
                        }
                    }
                    
                }
    

    $matchedProductIds = [];  // Initialize array to store matched product IDs
    $products = false;
    $query = Product::active()
        ->temporary()
        ->where('products.status', 1)
        ->where('products.request_status', 1)
        ->with(['rating', 'reviews', 'tags', 'deal_product']);
    
    // Fetch all products initially
    if($queryString2 != null){ 
        
        $query->whereIn('products.id', $queryString2);
    }elseif($Isdealoftheday != 'false' && $Isdealoftheday != false){
               $query->whereHas('deal_product', function ($query) {
                    $query->where('deal_of_the_days.status', 1)
                          ->whereDate('start_date_time', '<=', now())
                          ->whereDate('expire_date_time', '>=', now());
                });
        
    }elseif($category != 'all' || $subcategory != 'all' || $sub_sub_category_id != 'all' || $brand != 'all' || $color != null){
     $products = $query->get();     
    }

    if($products){
    foreach ($products as $product) {
        $productColors = json_decode($product->colors, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            
            $productColors = [];
        }
    
        if (
            ($category != 'all' && $category != '' && (is_array($category) ? in_array($product->category_id, $category) : $product->category_id == $category)) ||
            ($subcategory != 'all' && (is_array($subcategory) ? in_array($product->sub_category_id, $subcategory) : $product->sub_category_id == $subcategory)) ||
            ($sub_sub_category_id != 'all' && (is_array($sub_sub_category_id) ? in_array($product->sub_sub_category_id, $sub_sub_category_id) : $product->sub_sub_category_id == $sub_sub_category_id)) ||
            ($brand != 'all' && (is_array($brand) ? in_array($product->brand_id, $brand) : $product->brand_id == $brand)) ||
            ($color != null && trim($color,"'\"")!='' && $color != 'null' && (is_array($color) ? count(array_intersect($color, $productColors)) > 0 : in_array($color, $productColors)))
        ) {
            $matchedProductIds[] = $product->id;
        }
    }
    
     if(($category != 'all' || $subcategory != 'all' || $sub_sub_category_id != 'all' || $brand != 'all' || $color != null) && empty($matchedProductIds)){ 
         $query->where('products.id', 0);
         $fetched = $query->paginate($limit, ['*'], 'page', $offset);   
        return [
                'total_size' => $fetched->total(),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'products' => $fetched->items(),
            ];
    }
    
  }
   
   //return $matchedProductIds;
        if (!empty($matchedProductIds)) {
               $query->whereIn('products.id', $matchedProductIds);
        }   
            $query->when($priceMin !== null && $priceMin !='null' , function ($q) use ($priceMin) {
                        return $q->where('unit_price', '>=', $priceMin);
                    })
                    ->when($priceMax !== null && $priceMax !='null', function ($q) use ($priceMax) {
                        return $q->where('unit_price', '<=', $priceMax);
                    })
                    ->when($tags !='null' && $tags !== null, function ($q) use ($ids) {
                        foreach ($ids as $id) {
                            $q->where('service_type', 'REGEXP', '\"' . $id . '\"');
                        }
                        return $q;
                    })
                    
                    ->when($queryString != "", function ($q) use ($queryString) {
                        return $q->whereRaw(substr($queryString, 3));
                    })
                    ->when($reviewMax != null && $reviewMax !='', function ($q) use ($reviewMax) {
                        
                        return $q->whereHas('reviews', function ($query) use ($reviewMax) {
                            $query->where('reviews.rating', '<=', $reviewMax);
                            
                        });
                    })
                    ->when($featured_products != 'false' && $featured_products != false, function ($q) use ($featured_products) {
                        return $q->where('featured', 1)->withCount(['order_details'])->orderBy('order_details_count', 'DESC');
                    })
                     ->when($new_arriavals_products != 'false' && $new_arriavals_products != false, function ($q) use ($new_arriavals_products) {
                        return $q->with(['seller.shop'])->latest();
                    })
                     ->when($top_products != 'false' && $top_products != false, function ($q) use ($top_products) {
                        return $q->withCount(['reviews'])->orderBy('reviews_count', 'DESC');
                    })
                     ->when($discount_products != 'false' && $discount_products != false, function ($q) use ($discount_products) {
                        return $q->where('discount', '!=', 0)->latest();
                    })
                     ->when($high_to_low != 'false' && $high_to_low != false, function ($q) use ($high_to_low) {
                         return $q->orderByDesc('unit_price');
                    })
                     ->when($low_to_high != 'false' && $low_to_high != false, function ($q) use ($low_to_high) {
                          return $q->orderBy('products.unit_price','asc');
                    })
                    ->when($free_shipping != 'false' && $free_shipping != false, function ($q) use ($low_to_high) {
                          return $q->where('products.free_delivery',1);
                    })
                    ->when($search !== null && $search !='null', function ($q) use ($search) {
                    return $q->where('products.name', 'like', "%{$search}%");
                });

               $fetched = $query->paginate($limit, ['*'], 'page', $offset);

            return [
                'total_size' => $fetched->total(),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'products' => $fetched->items(),
            ];

        }


    
    public static function search_products_web($name, $category='all', $limit = 10, $offset = 1)
    {

        $key = explode(' ', $name);
        $paginator = Product::active()->temporary()->where('status',1)->with(['rating','tags'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                    ->orWhereHas('tags',function($query)use($value){
                        $query->where('tag', 'like', "%{$value}%");
                    });
            }
        });

        if (isset($category) && $category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        }else{
            $query = $paginator;
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function translated_product_search($name, $category='all', $limit = 10, $offset = 1)
    {
        $name = base64_decode($name);
        $product_ids = Translation::where('translationable_type', 'App\Model\Product')
            ->where('key', 'name')
            ->where('value', 'like', "%{$name}%")
            ->pluck('translationable_id');

        $paginator = Product::with('tags')
            ->where('status',1)
            ->WhereIn('id', $product_ids);

        $query = $paginator;
        if ($category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function translated_product_search_web($name, $category='all', $limit = 10, $offset = 1)
    {
        $key = explode(' ', $name);
        $product_ids = Translation::where('translationable_type', 'App\Model\Product')
            ->where('key', 'name')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('value', 'like', "%{$value}%");
                }
            })
            ->pluck('translationable_id');

        $paginator = Product::with('tags')
            ->where('status',1)
            ->WhereIn('id', $product_ids);

        $query = $paginator;
        if ($category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function product_image_path($image_type)
    {
        $path = '';
        if ($image_type == 'thumbnail') {
            $path = asset('storage/product/thumbnail');
        } elseif ($image_type == 'product') {
            $path = asset('storage/product');
        }
        return $path;
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)
            ->where('status', 1)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_shipping_methods($product)
    {
        if ($product['added_by'] == 'seller') {
            $methods = ShippingMethod::where(['creator_id' => $product['user_id']])->where(['status' => 1])->get();
            if ($methods->count() == 0) {
                $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
            }
        } else {
            $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
        }

        return $methods;
    }

    public static function get_seller_products($seller_id, $request)
    {
        $limit = $request['limit'];
        $offset = $request['offset'];
        $paginator = Product::active()->temporary()->where('status',1)->with(['rating','tags'])
            ->when($request->search, function ($query) use($request){
                $key = explode(' ', $request->search);
                foreach ($key as $value) {
                    $query->where('name', 'like', "%{$value}%");
                }
            })
            ->where(['user_id' => $seller_id, 'added_by' => 'seller'])
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_seller_all_products($seller_id, $limit = 10, $offset = 1)
    {
        $paginator = Product::temporary()->with(['rating','tags'])
            ->where(['user_id' => $seller_id, 'added_by' => 'seller'])->where('request_status',1)
            ->where('status',1)
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_discounted_product($limit = 10, $offset = 1)
    {
        //change review to ratting
        $paginator = Product::temporary()->with(['rating','tags'])->active()->where('discount', '!=', 0)->where('request_status',1)->where('status',1)->latest()->paginate($limit, ['*'], 'page', $offset);
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }
    public static function export_product_reviews($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'product' => $item->product['name'] ?? '',
                'customer' => isset($item->customer) ? $item->customer->f_name .' '. $item->customer->l_name : '' ,
                'comment' => $item->comment,
                'rating' => $item->rating
            ];
        }
        return $storage;
    }

//    public static function get_wishlist_status($data)
//    {
//        $wishlist = Wishlist::where('customer_id', Auth::guard('customer')->user()->id ?? 0)->where('product_id', $data)->first();
//        $result = $wishlist ? 1 : 0;
//        return $result;
//    }
//    public static function get_compare_list_status($data)
//    {
//        $wishlist = ProductCompare::where('user_id', Auth::guard('customer')->user()->id ?? 0)->where('product_id', $data)->first();
//        $result = $wishlist ? 1 : 0;
//        return $result;
//    }

    public static function get_user_total_product($added_by, $user_id)
    {
        $total_product = Product::active()->temporary()->where(['added_by'=>$added_by, 'user_id'=>$user_id])->where('request_status',1)->where('status',1)->count();
        return $total_product;
    }

    public static function get_products_rating_quantity($products)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;

        foreach ($products as $product)
        {
            $review = Review::where(['product_id'=>$product])->avg('rating');
            if($review == 5)
            {
                $rating5 += 1;
            }else if($review >= 4 && $review < 5)
            {
                $rating4 += 1;
            }else if($review >= 3 && $review < 4)
            {
                $rating3 += 1;
            }else if($review >= 2 && $review < 3)
            {
                $rating2 += 1;
            }else if($review >= 1 && $review < 2)
            {
                $rating1 += 1;
            }
        }

        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_products_delivery_charge($product, $quantity)
    {
        $delivery_cost = 0;
        $shipping_model = Helpers::get_business_settings('shipping_method');
        $shipping_type = "";

        if($shipping_model == "inhouse_shipping"){
            $shipping_type = ShippingType::where(['seller_id'=>0])->first();
            if($shipping_type->shipping_type == "category_wise"){
                $cat_id = $product->category_id;
                $CategoryShippingCost = CategoryShippingCost::where(['seller_id'=>0,'category_id'=>$cat_id])->first();
                $delivery_cost = $CategoryShippingCost ?
                        ($CategoryShippingCost->multiply_qty != 0 ? ($CategoryShippingCost->cost * $quantity) : $CategoryShippingCost->cost)
                    : 0;

            }elseif($shipping_type->shipping_type == "product_wise"){
                $delivery_cost = $product->multiply_qty != 0 ? ($product->shipping_cost * $quantity) : $product->shipping_cost;
            }
        }elseif($shipping_model == "sellerwise_shipping"){

            if($product->added_by == "admin")
            {
                $shipping_type = ShippingType::where('seller_id','=',0)->first();
            }else{
                $shipping_type = ShippingType::where('seller_id','!=',0)->where(['seller_id'=>$product->user_id])->first();
            }
            if($shipping_type)
            {
                $shipping_type = $shipping_type ?? ShippingType::where('seller_id','=',0)->first();
                if($shipping_type->shipping_type == "category_wise"){
                    $cat_id = $product->category_id;
                    if($product->added_by == "admin")
                    {
                        $CategoryShippingCost = CategoryShippingCost::where(['seller_id'=>0,'category_id'=>$cat_id])->first();
                    }else{
                        $CategoryShippingCost = CategoryShippingCost::where(['seller_id'=>$product->user_id,'category_id'=>$cat_id])->first();
                    }

                    $delivery_cost = $CategoryShippingCost ?
                        ($CategoryShippingCost->multiply_qty != 0 ? ($CategoryShippingCost->cost * $quantity) : $CategoryShippingCost->cost)
                    : 0;
                }elseif($shipping_type->shipping_type == "product_wise"){
                    $delivery_cost = $product->multiply_qty != 0 ? ($product->shipping_cost * $quantity) : $product->shipping_cost;
                }
            }
        }
        $data = [
            'delivery_cost'=>$delivery_cost,
            'shipping_type'=>$shipping_type->shipping_type ?? '',
        ];
        return $data;
    }

    public static function get_colors_form_products()
    {
        $colors_merge = [];

        $colors_collection = Product::active()->temporary()
                                        ->where('colors', '!=', '[]')
                                        ->where('request_status',1)
                                        ->where('status',1)
                                        ->pluck('colors')
                                        ->unique()
                                        ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            $colors_merge = array_merge($colors_merge, $color_array);
        }
        $colors = array_unique($colors_merge);

        return $colors;
    }
}
