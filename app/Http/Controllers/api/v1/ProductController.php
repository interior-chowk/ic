<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CategoryManager;
use App\CPU\BrandManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Model\ShippingMethod;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class ProductController extends Controller
{
    public function get_latest_products(Request $request)
    {
        $products = ProductManager::get_latest_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }
    
    public function get_home_products(Request $request)
    {
        $productss = ProductManager::get_home_products($request['limit'], $request['offset']);
        
        return response()->json(['products'=>$productss], 200); 
    }
    
    public function get_home_products_custom(Request $request)
    {
       
        $productss = ProductManager::get_home_products_custom($request['limit'], $request['offset']);
        return response()->json(['products'=>$productss], 200); 
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

    public function get_searched_products(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

       /* if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }*/

        $products = ProductManager::search_products($request['name'], 'all', $request['limit'], $request['offset']);
       
        if ($products['products'] == null) {
            $products = ProductManager::translated_product_search($request['name'], 'all', $request['limit'], $request['offset']);
        }
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }
    
    public function get_filtered_products(Request $request)
    {
        $products = ProductManager::filter_products($request['category'], $request['subcategory'] ,$request['sub_sub_category_id'] , $request['brand'] , $request['priceMin'] , $request['priceMax'], $request['reviewMin'] , $request['reviewMax'], $request['featured_products'], $request['new_arriavals_products'], $request['top_products'], $request['discount_products'] ,$request['high_to_low'] ,$request['low_to_high'], $request['option'], $request['color'] , $request['limit'], $request['offset'],$request['best_selling'],$request['tags'],$request['search'],$request['free_shipping'],$request['Isdealoftheday']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        $allProducts = $products['products']; 

        if($allProducts){
            $typeWithOptions = [];
            $brands = [];
            $categories_data = collect(); 

            foreach ($allProducts as $product) {
                
                if (!empty($product->choice_options)) {
                   
                    $choices =  $product->choice_options;
                    
                    if ($choices != "" && is_array( ($choices))) {
                            
                            foreach ( ($choices) as $choice) {
                               
                                if (isset($choice->options) && isset($choice->title)) {
                                    
                                    if (!isset($typeWithOptions[$choice->title])) {
                                        $typeWithOptions[$choice->title] = [];
                                    }
                                    
                                    $typeWithOptions[$choice->title] = array_merge($typeWithOptions[$choice->title], $choice->options);
                                }
                            }
                    }
                    
                   
                }
              
                 $brand = BrandManager::get_brand_id($product->brand_id);
                if ($brand && !in_array($brand->id, array_column($brands, 'id'))) {
                    $brands[] = $brand;
                }
                
                  if ($product->category_id) {
                        $category = Category::with(['childes' => function ($query) use ($product) {
                            // Filter the childes (subcategories) based on the sub_category_id
                            $query->where('position', 1)
                                  ->where('id', $product->sub_category_id)
                                  ->priority();
                        }])
                        ->where(['position' => 0, 'home_status' => 1])
                        ->priority()
                        ->where('id', $product->category_id) // Filter by category_id
                        ->first();
                    
                        if ($category && !$categories_data->contains('id', $category->id)) { 
                            $categories_data->push($category);
                    
                            // Map over the categories and subcategories to attach sub-subcategories
                            $categories_data = $categories_data->map(function ($category) use ($product) {
                                $category->childes = $category->childes->map(function ($subcategory) use ($product) {
                                    // Retrieve sub-subcategories based on sub_sub_category_id
                                    $subcategory->childes = Category::where('sub_parent_id', $subcategory->id)
                                        ->where('position', 2)
                                        ->where('id', $product->sub_sub_category_id) // Filter by sub_sub_category_id
                                        ->orderBy('priority', 'asc')
                                        ->get();
                                    return $subcategory;
                                });
                                return $category;
                            });
                        }
                    }
           
                $deal = $product->deal_product->first(); 
                if ($deal) { 
                    $product->start_date_time = $deal->start_date_time;
                    $product->expire_date_time = $deal->expire_date_time;
                } else {
                    $product->start_date_time = null; 
                    $product->expire_date_time = null;
                }
            }
            
            foreach ($typeWithOptions as &$options) {
                $options = array_values(array_unique($options));
            }
            unset($options); 
            
             $productColors = [];
            
            foreach ($allProducts as $product) {
               
                if (!empty($product->colors)) {
                   
                    $colors = json_decode($product->colors);
                    if ($colors === null && json_last_error() !== JSON_ERROR_NONE) {
                        continue;
                    }
            
                    if (is_array($colors)) {
                       
                        $productColors = array_merge($productColors, $colors);
                    } else {
                        
                        Log::warning('Invalid colors array:', $colors);
                    }
                }
            }
            
            $productColors = array_unique($productColors);
            $productColors = array_values($productColors);
            if (isset($typeWithOptions['Type']) && is_array($typeWithOptions['Type'])) {
            $typeWithOptions['Type'] = array_unique(array_map(function($value) {
                return strtolower(trim(preg_replace('/\s+/', ' ', $value)));  
            }, $typeWithOptions['Type']));
            } else {
                $typeWithOptions['Type'] = [];  
            }
            
            $products['allOptions'] = $typeWithOptions;
            $products['allOptions']['color'] = $productColors;
            $products['allOptions']['brand'] = $brands;
            $products['allOptions']['category'] = $categories_data;
        }
       
        return response()->json($products, 200);
    }
    
    public function get_filtered_products_Test(Request $request)
    {
      
        $products = ProductManager::filter_products_test($request['category'], $request['subcategory'] ,$request['sub_sub_category_id'] , $request['brand'] , $request['priceMin'] , $request['priceMax'], $request['reviewMin'] , $request['reviewMax'], $request['featured_products'], $request['new_arriavals_products'], $request['top_products'], $request['discount_products'] ,$request['high_to_low'] ,$request['low_to_high'], $request['option'], $request['color'] , $request['limit'], $request['offset'],$request['best_selling'],$request['tags'],$request['search'],$request['free_shipping'],$request['Isdealoftheday']);
    
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        $allProducts = $products['products']; 

        if($allProducts){
            $typeWithOptions = [];
            $brands = [];
            $categories_data = collect(); 

            foreach ($allProducts as $product) {
                
                if (!empty($product->choice_options)) {
                   
                    $choices =  $product->choice_options;
                      
                    if ($choices != "" && is_array( ($choices))) {
                            
                            foreach ( ($choices) as $choice) {
                               
                                if (isset($choice->options) && isset($choice->title)) {
                                    
                                    if (!isset($typeWithOptions[$choice->title])) {
                                        $typeWithOptions[$choice->title] = [];
                                    }
                                    
                                    $typeWithOptions[$choice->title] = array_merge($typeWithOptions[$choice->title], $choice->options);
                                }   
                            }
                    }
                   
                }
               
                $brand = BrandManager::get_brand_id($product->brand_id);
                if ($brand && !in_array($brand->id, array_column($brands, 'id'))) {
                    $brands[] = $brand;
                }
                    
                $category = CategoryManager::category_parents($product->category_id, $product->sub_category_id, $product->sub_sub_category_id);

                if ($category && !in_array($category->id, array_column($categories_data->toArray(), 'id'))) {
                    $categories_data->push($category);
                }
            
              $deal = $product->deal_product->first(); 
                if ($deal) { 
                    $product->start_date_time = $deal->start_date_time;
                    $product->expire_date_time = $deal->expire_date_time;
                } else {
                    $product->start_date_time = null; 
                    $product->expire_date_time = null;
                }
            }
           
            foreach ($typeWithOptions as &$options) {
                $options = array_values(array_unique($options));
            }
            unset($options); 
            
             $productColors = [];
            
            foreach ($allProducts as $product) {
               
                if (!empty($product->colors)) {
                   
                    $colors = json_decode($product->colors);
                    if ($colors === null && json_last_error() !== JSON_ERROR_NONE) {
                        continue;
                    }
            
                   
                    if (is_array($colors)) {
                       
                        $productColors = array_merge($productColors, $colors);
                    } else {
                        
                        Log::warning('Invalid colors array:', $colors);
                    }
                }
            }
            
            $productColors = array_unique($productColors);
            $productColors = array_values($productColors);
            if (isset($typeWithOptions['Type']) && is_array($typeWithOptions['Type'])) {
            $typeWithOptions['Type'] = array_unique(array_map(function($value) {
                return strtolower(trim(preg_replace('/\s+/', ' ', $value)));  
            }, $typeWithOptions['Type']));
            } else {
                $typeWithOptions['Type'] = [];  
            }
         
            $products['allOptions'] = $typeWithOptions;
            $products['allOptions']['color'] = $productColors;
            $products['allOptions']['brand'] = $brands;
            $products['allOptions']['category'] = $categories_data;
        }
       
        return response()->json($products, 200);
    }

    public function get_product($slug)
    {
        $product = Product::with(['reviews.customer', 'seller.shop','tags'])->where(['slug' => $slug])->first();
        $specification = Helpers::technical_specification_data($product->id,$product->sub_sub_category_id);
        $variation = Helpers::sku_product_new($product->id,$product->variation);
   
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
            $product['technical_specification'] = !empty($specification) ? $specification : null;
            $product['variationss'] = !empty($variation) ? $variation : null;
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
        $categories = Category::where('home_status', 1)->get();
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
            // dd($products);
            
            if(count($products) == NULL)
            {
              $products = ProductManager::get_random_products($id); 
            }
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