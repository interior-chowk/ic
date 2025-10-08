<?php

namespace App\Http\Controllers\Web;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\Banner;
use App\Model\Category;
use App\User;
use App\Model\FlashDeal;
use App\Model\FlashDealProduct;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Model\Translation;
use App\Model\Wishlist;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function App\CPU\translate;


class ProductListController extends Controller
{
    public function products_1(Request $request, $slug)
    {
        $category = DB::table('categories')->where('slug', $slug)->first();
        if (!$category) {
            abort(404);
        }

        $query1 = DB::table('categories')->where('sub_parent_id', $category->id);
        $query2 = DB::table('categories')->whereIn('sub_parent_id', function ($q) use ($category) {
            $q->select('id')->from('categories')->where('parent_id', $category->id);
        });
        $sub_sub_categories = $query1->union($query2)->get();
        if ($sub_sub_categories->isEmpty()) {
            $sub_sub_categories = collect([$category]);
        }
        $descendantIds = $sub_sub_categories->pluck('id');

        $baseQuery = DB::table('sku_product_new')
            ->join('products', 'sku_product_new.product_id', '=', 'products.id')
            ->join('categories', 'products.sub_sub_category_id', '=', 'categories.id')
            ->leftJoin('key_specification_values as ksv', 'products.id', '=', 'ksv.product_id')
            ->whereIn('products.sub_sub_category_id', $descendantIds)
            ->where('products.status', 1)
            ->whereNotNull('sku_product_new.thumbnail_image')
            ->select(
                'sku_product_new.*',
                'sku_product_new.id as ids',
                'sku_product_new.variation as variations',
                'products.*',
                'sku_product_new.discount as sku_discount',
                'sku_product_new.discount_type as sku_discount_type',
                'categories.specification',
                'categories.key_features',
                'categories.technical_specification',
                'categories.other_details',
                'ksv.specification as ksv_specification',
                'ksv.key_features as ksv_key_features',
                'ksv.technical_specification as ksv_technical_specification',
                'ksv.other_details as ksv_other_details'
            );

        $fullCollection = $baseQuery->get();

        
        $minPrice = $fullCollection->min(fn($item) => (float) $item->listed_price);
        $maxPrice = $fullCollection->max(fn($item) => (float) $item->listed_price);
        
        $priceRange = [
            'min' => floor($minPrice ?? 0),
            'max' => ceil($maxPrice ?? 0),
        ];

        $productsQuery = clone $baseQuery;

        $minPriceInput = (float) $request->input('min_price', $priceRange['min']);
        $maxPriceInput = (float) $request->input('max_price', $priceRange['max']);
        if ($minPriceInput > $maxPriceInput) {
            [$minPriceInput, $maxPriceInput] = [$maxPriceInput, $minPriceInput];
        }

        $productsQuery->whereRaw("
            (
                CASE 
                    WHEN sku_product_new.discount_type = 'flat' 
                        THEN sku_product_new.variant_mrp - sku_product_new.discount 
                    WHEN sku_product_new.discount_type = 'percent' 
                        THEN sku_product_new.variant_mrp - (sku_product_new.variant_mrp * sku_product_new.discount / 100)
                    ELSE sku_product_new.variant_mrp
                END
            ) BETWEEN ? AND ?
        ", [$minPriceInput, $maxPriceInput]);


        // Apply filters
        $filtersInput = $request->input('filters', []);
        if (!empty($filtersInput)) {
            foreach ($filtersInput as $key => $values) {
                if ($key === 'size') {
                    $normalizedSizes = array_map(fn($val) => str_replace(' ', '', $val), $values);
                    $productsQuery->whereIn('sku_product_new.sizes', $normalizedSizes);
                } elseif ($key === 'sort') {
                    foreach ($values as $sortVal) {
                        if ($sortVal === 'price_asc') {
                            $productsQuery->orderByRaw('CAST(sku_product_new.variant_mrp AS DECIMAL(10,2)) ASC');
                        } elseif ($sortVal === 'price_desc') {
                            $productsQuery->orderByRaw('CAST(sku_product_new.variant_mrp AS DECIMAL(10,2)) DESC');
                        } elseif ($sortVal === 'newest') {
                            $productsQuery->orderBy('products.created_at', 'desc');
                        } elseif ($sortVal === 'discounted') {
                            $productsQuery->where('sku_product_new.discount', '>', 0)
                                ->orderByRaw('CAST(sku_product_new.discount AS DECIMAL(10,2)) DESC');
                        }
                    }
                } else {
                    $keyToColumnMap = [
                        'specification' => 'ksv.specification',
                        'key_features' => 'ksv.key_features',
                        'technical_specification' => 'ksv.technical_specification',
                        'other_details' => 'ksv.other_details',
                    ];

                    foreach ($keyToColumnMap as $catField => $matchedCol) {
                        if (Str::contains(strtolower($catField), strtolower($key))) {
                            $productsQuery->where(function ($query) use ($matchedCol, $values) {
                                foreach ($values as $val) {
                                    $query->orWhereRaw("JSON_CONTAINS($matchedCol, '\"$val\"')");
                                }
                            });
                            break;
                        }
                    }
                }
            }
        }

        // Paginate results
        $products = $productsQuery->paginate(25);
        $collection = collect($products->items());

        // Size filters
        $sizes = $collection->pluck('sizes')->filter()->unique()->values()->map(function ($size) use ($collection) {
            $count = $collection->where('sizes', $size)->count();
            return $size . ' (' . $count . ')';
        });

        // Sort options
        $sortOptions = [
            'price_asc' => 'Price: Low → High',
            'price_desc' => 'Price: High → Low',
            'newest' => 'Newest First',
            'discounted' => 'Discounted Products',
        ];

        // Filters array
        $filters = [
            'sort' => ['title' => 'Sort By', 'options' => $sortOptions],
            'size' => ['title' => 'Size', 'options' => $sizes],
        ];

        // Dynamic spec filters
        $dynamic = [];
        foreach ($collection as $row) {
            $mapping = [
                'specification' => 'ksv_specification',
                'key_features' => 'ksv_key_features',
                'technical_specification' => 'ksv_technical_specification',
                'other_details' => 'ksv_other_details',
            ];

            foreach ($mapping as $catCol => $ksvCol) {
                $labels = array_map('trim', explode(',', $row->{$catCol} ?? ''));
                $values = json_decode($row->{$ksvCol} ?? '[]', true) ?: [];

                foreach ($labels as $i => $label) {
                    if (empty($label) || !isset($values[$i]) || in_array($values[$i], ['', 'N/A'])) continue;

                    $slug = Str::slug($label);
                    $val = $values[$i];
                    $key = $label . '|' . $val;

                    if (!isset($dynamic[$slug]['_counts'][$key])) {
                        $dynamic[$slug]['_counts'][$key] = ['label' => $label, 'value' => $val, 'count' => 0];
                    }
                    $dynamic[$slug]['_counts'][$key]['count']++;
                }
            }
        }

        foreach ($dynamic as $key => &$block) {
            $block['title'] = $block['_counts'][array_key_first($block['_counts'])]['label'] ?? $block['title'];
            $block['options'] = collect($block['_counts'])
                ->map(fn($item) => $item['value'] . ' (' . $item['count'] . ')')
                ->unique()
                ->values();
            unset($block['_counts']);
        }

        // Color filter
        $colorCounts = [];
        foreach ($collection as $product) {
            $color_keys = array_map('trim', explode(',', $product->specification));
            $color_values = json_decode($product->ksv_specification, true);

            $colorIdx = array_search('Color', $color_keys);
            $colorName = ($colorIdx !== false && isset($color_values[$colorIdx]))
                ? trim($color_values[$colorIdx])
                : '';

            $codes = json_decode($product->colors ?? '[]', true);
            $colorCode = (is_array($codes) && isset($codes[0])) ? $codes[0] : '#000000';

            if ($colorName === '') continue;

            $key = strtolower($colorName);
            if (!isset($colorCounts[$key])) {
                $colorCounts[$key] = [
                    'name' => $colorName,
                    'code' => $colorCode,
                    'count' => 0,
                ];
            }
            $colorCounts[$key]['count']++;
        }

        $dynamic['color'] = [
            'title' => 'Color',
            'options' => collect($colorCounts)->map(function ($item) {
                return "{$item['name']} ({$item['code']}) ({$item['count']})";
            })->values(),
        ];

        // Merge all filters
        $filters = array_merge($filters, $dynamic);

        // Return view
        if ($products->isEmpty()) {
            return response()->view('404', [], 404);
        }

        return view('web.productList', [
            'subCategory' => $category,
            'sub_sub_categories' => $sub_sub_categories,
            'products' => $products,
            'filters' => $filters,
            'priceRange' => $priceRange,
        ]);
    }

    public function search_product(Request $request)
    {
        $tag = $request->name;

        $products = DB::table('sku_product_new')
        ->join('products', 'sku_product_new.product_id', '=', 'products.id')
        ->whereRaw("products.name LIKE ?", ["%{$tag}%"])
        ->where('products.status', 1)
        ->select(
            'sku_product_new.*', 
            'products.*',
            'sku_product_new.discount as sku_discount', 
            'sku_product_new.discount_type as sku_discount_type'
            )
        ->get();
     
        return view('web.searchProductList',compact('tag','products'));

    }
    
    public function products_2(Request $request)
    {
        $seller_id = $request->query('seller_id'); 

        // $brand_id = $request->query('brand_id');
        $brand_name = $request->query('brand_id');
        $brand_id = Brand::where('name',$brand_name)->value('id');
       
        if($brand_name){
            $brand = DB::table('brands')->where('name',$brand_name)->first();
        
                $products = DB::table('sku_product_new')
                ->join('products', 'sku_product_new.product_id', '=', 'products.id')
                ->where('products.brand_id', $brand_id)
                ->where('products.status', 1)
                ->select(
                    'sku_product_new.*', 
                    'products.*',
                    'sku_product_new.discount as sku_discount', 
                    'sku_product_new.discount_type as sku_discount_type'
                )
                ->get()->paginate(25);
             //dd($products);

            }else{

            $products = DB::table('sku_product_new')
                ->join('products', 'sku_product_new.product_id', '=', 'products.id')
                ->where('products.user_id', $seller_id)
                ->where('products.status', 1)
                ->select(
                    'sku_product_new.*', 
                    'products.*',
                    'sku_product_new.discount as sku_discount', 
                    'sku_product_new.discount_type as sku_discount_type'
                )
                ->get()->paginate(25);

            }

        if($brand_id){
        return view('web.brandProductList',compact('brand','products'));
        }else{
            return view('web.brandProductList',compact('products'));
        }
    }

    public function top_products()
    {
        $products = DB::table('sku_product_new')
        ->join('home_products', function($join) {
            $join->on('sku_product_new.product_id', '=', 'home_products.product_id')
                ->where('home_products.section_type', 'top_products');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->where('products.status', 1)
        ->select(
            'sku_product_new.*', 
            'products.*',
            'sku_product_new.discount as sku_discount', 
            'sku_product_new.discount_type as sku_discount_type'
        )
        ->get();

        return view('web.topProductList',compact('products'));
    }
    
    public function deal_products()
    {
        $products = DB::table('deal_of_the_days AS d')
        ->join('sku_product_new AS p', 'd.product_id', '=', 'p.product_id')
        ->leftJoin('products AS pr', 'p.product_id', '=', 'pr.id')
        ->select([
            
            'p.*',
            'pr.*', 
            'p.discount as sku_discount', 
            'p.discount_type as sku_discount_type'
                
            ])
            ->where('d.status', '1')
            ->get();

        return view('web.dealProductList',compact('products'));
    }

    public function luxe_products()
    {
        $products = DB::table('sku_product_new')
        ->join('home_products', function($join) {
            $join->on('sku_product_new.product_id', '=', 'home_products.product_id')
            ->where('home_products.section_type', 'feature');
        })
        ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
        ->where('products.status', 1)
        ->select(
            'sku_product_new.*', 
            'products.*',
            'sku_product_new.discount as sku_discount', 
            'sku_product_new.discount_type as sku_discount_type'
            )
            ->get();

        return view('web.luxeProductList',compact('products'));
    }
    
    public function discount_products($id)
    {   
        $discount_banner = Banner::where('id',$id)->first();
  
        $products = DB::table('sku_product_new')
        ->join('products', 'sku_product_new.product_id', '=', 'products.id')
        ->where('products.sub_category_id', $discount_banner->sub_category)
        ->where('products.sub_sub_category_id', $discount_banner->sub_sub_category)
        ->where('sku_product_new.discount_type', 'percent')
        ->where('sku_product_new.discount','<', $discount_banner->discount)
        ->where('products.status', 1)
        ->select(
            'sku_product_new.*', 
            'products.*',
            'sku_product_new.discount as sku_discount', 
            'sku_product_new.discount_type as sku_discount_type'
        )
        ->get();

        return view('web.discountProductList',compact('products'));
    }

    public function banner_products($id)
    {   
        $banner = Banner::findOrFail($id);
        
        $query = DB::table('sku_product_new')
            ->join('products', 'sku_product_new.product_id', '=', 'products.id')
            ->where('sku_product_new.discount_type', 'percent')
            ->where('products.status', 1);
        
            if ($banner->resource_type === 'category') {
                $query->where('products.category_id', $banner->resource_id)
                ->where('products.sub_category_id', $banner->sub_category)
                ->where('products.sub_sub_category_id', $banner->sub_sub_category);
            } elseif ($banner->resource_type === 'shop' ) {
                $query->where('products.user_id', $banner->resource_id);
        } elseif ($banner->resource_type === 'brand') {
            $query->where('products.brand_id', $banner->resource_id);
        }
        
        $products = $query->select(
            'sku_product_new.*', 
            'products.*',
            'sku_product_new.discount as sku_discount', 
            'sku_product_new.discount_type as sku_discount_type'
            )->get();

            if ($banner->resource_type === 'category') {
                $name      = Category::findOrFail($banner->resource_id)->name;
                $pageTitle = $name . ' Products';
            }
            elseif ($banner->resource_type === 'brand') {
                $name      = Brand   ::findOrFail($banner->resource_id)->name;
                $pageTitle = $name . ' Products';
            }
            elseif ($banner->resource_type === 'shop') {
                $name      = User   ::findOrFail($banner->resource_id)->name;
                $pageTitle = $name . ' Products';
            }

        return view('web.bannerProductList',compact('products','pageTitle'));
    }

    public function products(Request $request)
    {
        $theme_name = theme_root_path();

        return match ($theme_name){
            'default' => self::default_theme($request),
            'theme_aster' => self::theme_aster($request),
            'theme_fashion' => self::theme_fashion($request),
            'theme_all_purpose' => self::theme_all_purpose($request),
        };
    }

    public function default_theme($request){
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews']);

        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

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
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
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

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];

        $products = $fetched->paginate(20)->appends($data);

        if ($request->ajax()) {

            return response()->json([
                'total_product'=>$products->total(),
                'view' => view('web-views.products._ajax-products', compact('products'))->render()
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if($brand_data) {
                $data['brand_name'] = $brand_data->name;
            }else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data'));
    }

    public function theme_aster($request){
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with([
            'reviews','rating',
            'seller.shop',
            'wish_list'=>function($query){
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compare_list'=>function($query){
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ]);

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

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
                'wish_list'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with([
                'reviews','seller.shop',
                'wish_list'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
                $product_ids = Product::with([
                    'seller.shop',
                    'wish_list'=>function($query){
                        return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                    },
                    'compare_list'=>function($query){
                        return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
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

        if ($request['data_from'] == 'discounted') {
            $query = Product::with([
                'reviews','seller.shop',
                'wish_list'=>function($query){
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compare_list'=>function($query){
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }

        if ($request['ratings'] != null)
        {
            $fetched->with('rating')->whereHas('rating', function($query) use($request){
                return $query;
            });
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];
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

        $products = $common_query->paginate(20)->appends($data);

        if ($request['ratings'] != null)
        {
            $products = $products->map(function($product) use($request){
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating','>=',$request['ratings'])
                ->where('rating','<',$request['ratings']+1)
                ->paginate(20)->appends($data);
        }

        if ($request->ajax()) {
            return response()->json([
                'total_product'=>$products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products','product_ids'))->render(),
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if($brand_data) {
                $data['brand_name'] = $brand_data->name;
            }else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data', 'ratings', 'product_ids'));
    }

    public function theme_fashion(Request $request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews','rating','wish_list'=>function($query){
                            return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                        },
                        'compare_list'=>function($query){
                            return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                        }]);

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

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
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
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

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $products = $common_query->paginate(20);

        if ($request['ratings'] != null)
        {
            $products = $products->map(function($product) use($request){
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating','>=',$request['ratings'])
                ->where('rating','<',$request['ratings']+1)
                ->paginate(20);
        }

        // Categories start
        $categories = [];
        $category_info_for_fashion = Category::where('position',0)->pluck('id');
        foreach ($category_info_for_fashion as $category_id) {
            $category = Category::withCount(['product'=>function($qc1){
                $qc1->where(['status'=>'1']);
            }])->with(['childes' => function ($qc2) {
                $qc2->with(['childes' => function ($qc3) {
                    $qc3->withCount(['sub_sub_category_product'])->where('position', 2);
                }])->withCount(['sub_category_product'])->where('position', 1);
            }, 'childes.childes'])
                ->where('position', 0)
                ->find($category_id);

            if ($category != null) {
                array_push($categories, $category);
            }
        }
        $categories = array_unique($categories);
        // Categories End

        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Model\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product'=>$products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products','product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if(!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products',  'product_ids','categories','colors_in_shop','banner'));
    }

    public function theme_all_purpose(Request $request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews','rating']);

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

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
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status'=>1])->where(['deal_type'=>'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id',$featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
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

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $products = $common_query->paginate(20);

        if ($request['ratings'] != null)
        {
            $products = $products->map(function($product) use($request){
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating','>=',$request['ratings'])
                ->where('rating','<',$request['ratings']+1)
                ->paginate(20);
        }

        // Categories start
        $categories = [];
        $category_info_for_fashion = Category::where('position',0)->pluck('id');
        foreach ($category_info_for_fashion as $category_id) {
            $category = Category::withCount(['product'=>function($qc1){
                $qc1->where(['status'=>'1']);
            }])->with(['childes' => function ($qc2) {
                $qc2->with(['childes' => function ($qc3) {
                    $qc3->withCount(['sub_sub_category_product'])->where('position', 2);
                }])->withCount(['sub_category_product'])->where('position', 1);
            }, 'childes.childes'])
                ->where('position', 0)
                ->find($category_id);

            if ($category != null) {
                array_push($categories, $category);
            }
        }
        $categories = array_unique($categories);
        // Categories End

        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Model\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product'=>$products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products','product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if(!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }
        return view(VIEW_FILE_NAMES['products_view_page'], compact('products',  'product_ids','categories','colors_in_shop','banner'));
    }
}
