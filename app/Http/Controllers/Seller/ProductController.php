<?php

namespace App\Http\Controllers\Seller;

use App\CPU\BackEndHelper;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\Color;
use App\Model\State;
use App\Model\DealOfTheDay;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\Review;
use App\Model\Tag;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Model\Cart;
use Carbon\Carbon;
use function App\CPU\translate;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    public function record_sub_category(Request $request)
    {
        $category_id = $request->category_id;

        $found = DB::table('categories')->where('parent_id',$category_id)->where('position',1)->first();
        
        if($found){
             $data = DB::table('categories')->where('parent_id',$category_id)->where('position',1)->orderBy('name')->get();
          $select = ' <select class="form-control" name="sub_category_id" id="sub-category-id">';
           foreach ($data as $datas) {
        $select .= '<option value="' . $datas->id . '">' . $datas->name . '</option>';
        }
         $select .= '</select>';

        }else{
            $select =' <select class="form-control" name="sub_category_id" id="sub-category-id"><option value="0">No data</option></select>';
        }

       echo  $select;
    }

    public function add_new()
    {
        $category_id = session('category_id');
        $sub_category_id = session('sub_category_id');
        $sub_sub_category_id = session('sub_sub_category_id');
        $cat = Category::where(['position' => 0, 'home_status' => 1])->orderBy('name')->get();
        $br = Brand::active()->orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        $state_city = DB::table('state_city')->where('parent_id',null)->get();
        $warehouse = DB::table('warehouse')->where('seller_id', auth('seller')->id())->get();
        return view('seller-views.product.add-new', compact('cat', 'br', 'brand_setting', 'digital_product_setting','category_id', 'sub_category_id', 'sub_sub_category_id','warehouse','state_city'));
    }

    public function state_city(Request $request)
    {
        try {
            $state_id = $request->state;

            if (!$state_id) {
                return response()->json(['error' => 'State ID is missing'], 400);
            }

            $cities = DB::table('state_city')->where('parent_id', $state_id)->get();

            if ($cities->isEmpty()) {
                return response()->json(['error' => 'No cities found for this state'], 404);
            }

            $options = '';
            foreach ($cities as $city) {
                $options .= '<li><button onclick="addToSelection(this, \'city\')">' . $city->name . '</button></li>';
            }

            return response()->json(['options' => $options]);

        } catch (\Exception $e) {
            \Log::error('Error in state_city: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function getCities($stateId)
    {
        $cities = DB::table('state_city')->where('parent_id', $stateId)->pluck('name');
            return response()->json($cities);
    }

    public function image_get()
    {
        $image =  DB::table('sku_products')->get();
        return $image;
    }

    public function add_search_new()
    {
        $cat = Category::where(['position' => 0, 'home_status' => 1])->orderBy('name')->get();
        $br = Brand::active()->orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        return view('seller-views.product.add-search-new', compact('cat', 'br', 'brand_setting', 'digital_product_setting'));
    }
    
    public function searchCategories_post(Request $request)
    {

        // dd($request->all());
        session([
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'sub_sub_category_id' => $request->sub_sub_category_id,
        ]);
     
        return redirect()->route('seller.product.add-new');
    }

    public function searchCategories(Request $request)
    {
        $query = $request->input('query');
        $suggestions = Category::where('name', 'like', "%$query%")
                            ->limit(5)  // Limiting the number of suggestions
                            ->get(['id', 'name']);

         $cat  = Category::where('name', 'like', "%$query%") ->first();

            if(!$cat){
            $html = '<div>
                <form id="productForm">
                    <label>Add Product* </label>
                    <input type="text" class="form-control" name="category" id="cate_name" placeholder="Type product name">
                    
                    <label class="mt-1">HSN Code* </label>
                    <input class="form-control" type="text" name="hsn" id="hsn_code" placeholder="">
                    
                    <input type="button" class="btn btn--primary mt-2" value="Save" id="saveBtn">
                </form>
            </div>';
            return response()->json([
                'html'=>$html,
            ]);
            }else{
            if($cat->parent_id==0){
                $category = $cat;
                $subcategory = "";
                $subsubcategory = "";
            }elseif($cat->parent_id !=0 && $cat->sub_parent_id==0){
                    $category = $cat->parent;
                    $subcategory = $cat;
                    if ($subcategory) {
                        $sub_sub_category = Category::where('sub_parent_id', $cat->id)->get();
                        $options = '';
                        foreach ($sub_sub_category as $sub_sub) {
                            $options .= '<option value="' . $sub_sub->id . '">' . $sub_sub->name . '</option>';
                        }
                        $subsubcategory = ['options' => $options]; // HTML options array
                    }

            }else{
                if($cat){
                    $sub = Category::where('id',$cat->sub_parent_id)->first();
                }
                $category = $cat->parent;
                $subcategory = $sub;
                $subsubcategory = $cat;
            }

            return response()->json([
                'category' => $category,  // Fetching parent category
                'subcategory' => $subcategory,  // Fetching subcategories
                'subsubcategory' => $subsubcategory,
                'suggestions' => $suggestions // Returning the subsubcategory
            ]);
        }
    }

    public function addCategories()
    {
        $cat = Category::where(['position' => 0, 'home_status' => 1])->orderBy('name')->get();
        $br = Brand::active()->orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        return view('seller-views.product.add-seller-category', compact('cat', 'br', 'brand_setting', 'digital_product_setting'));
    }

    public function status_update(Request $request)
    {
        if ($request['status'] == 0) {
            Product::where(['id' => $request['id'], 'added_by' => 'seller', 'user_id' => \auth('seller')->id()])->update([
                'status' => $request['status'],
            ]);
            return response()->json([
                'success' => 1,
            ], 200);
        } elseif ($request['status'] == 1) {
            if (Product::find($request['id'])->request_status == 1) {
                Product::where(['id' => $request['id']])->update([
                    'status' => $request['status'],
                ]);
                return response()->json([
                    'success' => 1,
                ], 200);
            } else {
                return response()->json([
                    'success' => 0,
                ], 200);
            }
        }
    }

    public function featured_status(Request $request)
    {
        if ($request->ajax()) {
            $product = Product::find($request->id);
            $product->featured_status = $request->status;
            $product->save();
            $data = $request->status;
            return response()->json($data);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        ], [
        ]);
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
       
        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }
      
        $product = new Product();
        $product->user_id = auth('seller')->id();
        $select_location = $request->cities;
        $select_location = array_map('trim', explode(',', $select_location));
        $product->cities = json_encode($select_location);
        $product->added_by = "seller";
        $product->HSN_code = $request->HSN_code;
        $product->Return_days     = $request->Return_days;
        $product->Replacement_days     = $request->Replacement_days;
        $product->name = $request->name[array_search('en', $request->lang)];
        $product->slug = Str::slug($request->name[array_search('en', $request->lang)], '-');
        $product->add_warehouse = $request->warehouse;
          
        $product_images = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            foreach ($request->colors as $color) {
                $color_ = str_replace('#','',$color);
                $img = 'color_image_'.$color_;
                if ($request->file($img)) {
                    $file = $request->file($img);
                    $ext = strtolower($file->getClientOriginalExtension());

                    $folder = 'products';
                    $fileName = uniqid() . '.webp';

                    if ($ext === 'webp') {
                        $imageContent = file_get_contents($file->getRealPath());
                    } else {
                        $imageContent = (string) Image::make($file->getRealPath())->encode('webp', 90);
                    }

                    $path = $folder . '/' . $fileName;
                    Storage::disk('r2')->put($path, $imageContent);

                    $image_name = '/' . $path;

                    $product_images[] = $image_name;
                    $color_image_serial[] = [
                        'color' => $color_,
                        'image_name' => $image_name,
                    ];
                }
            }
        }

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }
      
        $request->product_type = "physical";
        $request->product_type == 'digital';
        $product->category_ids          = json_encode($category);
        $product->category_id           = $request->category_id;
        $product->sub_category_id       = $request->sub_category_id;
        $product->sub_sub_category_id   = $request->sub_sub_category_id;
        $product->brand_id              = $request->brand_id;
        $product->unit                  = $request->product_type == 'physical' ? $request->unit : null;
        $product->digital_product_type  = $request->product_type == 'digital' ? $request->digital_product_type : null;
        $product->product_type          = $request->product_type;
        $product->code                  = $request->code;
        $product->minimum_order_qty     = $request->minimum_order_qty;
        $product->details               = $request->description[array_search('en', $request->lang)];
        
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = $request->product_type == 'physical' ? json_encode($request->colors) : json_encode([]);
        } else {
            $colors = [];
            $product->colors = $request->product_type == 'physical' ? json_encode($colors) : json_encode([]);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = array_map('trim', explode(',', implode('|', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $product->choice_options = $request->product_type == 'physical' ? json_encode($choice_options) : json_encode([]);
        
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);

        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = Convert::usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }

        } else {
            $stock_count = (integer)$request['current_stock'];
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
       
        $product->variation      = $request->product_type == 'physical' ? json_encode($variations) : json_encode([]);
        $product->unit_price     = Convert::usd($request->unit_price);
        $product->purchase_price = Convert::usd($request->purchase_price ?? 0);
        $product->discount       = $request->discount_type == 'flat' ? Convert::usd($request->discount) : $request->discount;
        $product->discount_type  = $request->discount_type;
        $product->attributes     = $request->product_type == 'physical' ? json_encode($request->choice_attributes) : json_encode([]);
        $product->current_stock  = $request->product_type == 'physical' ? abs($stock_count) : 0;
        $product->video_provider = 'youtube';
        $product->video_url      = $request->video_link;
        $product->request_status = Helpers::get_business_settings('new_product_approval')==1?0:1;
        $product->status         = 0;
        $product->shipping_cost  = $request->product_type == 'physical' ? Convert::usd($request->shipping_cost) : 0;
        $product->multiply_qty   = ($request->product_type == 'physical') ? ($request->multiplyQTY=='on'?1:0) : 0;
        $product->free_delivery      = $request->free_delivery=='on'?1:0;
        $product->available_instant_delivery      = $request->available_instant_delivery=='on'?1:0;
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfPath = 'products' .  '/' . uniqid() . '.pdf';

            Storage::disk('r2')->put($pdfPath, file_get_contents($pdf));

            $product->thumbnail = '/' . $pdfPath;
        }

        $product->length    = $request->length ?? 0;
        $product->breadth   = $request->breadth ?? 0;
        $product->height    = $request->height ?? 0;
        $product->weight    = $request->weight ?? 0;
   
        if ($request->ajax()) {
            return response()->json([], 200);
        } else 
        {
          
            $product->save();
            
            $product_id = $product->id;
    
            foreach ($request->sizes as $index => $skus) {
                $sizeParts = explode('-', $skus);
                $size = end($sizeParts);

                $imageNames = [];
                $thumbnailImage = $request->input('thumbnail_image_' . $index);
                $imageOrderJson = $request->input('image_order_' . $index);
                $imageOrder = $imageOrderJson ? json_decode($imageOrderJson, true) : [];

                if ($request->hasFile('image_' . $index)) {

                    $files = $request->file('image_' . $index);
                    $storedFiles = [];

                    foreach ($files as $file) {

                        if ($file->isValid()) {

                            $ext = strtolower($file->getClientOriginalExtension());

                            $folder = 'products';
                            $safeName = uniqid() . '.webp';

                            if ($ext === 'webp') {
                                $imageContent = file_get_contents($file->getRealPath());
                            } else {
                                $imageContent = (string) Image::make($file->getRealPath())->encode('webp', 90);
                            }

                            $r2Path = $folder . '/' . $safeName;

                            Storage::disk('r2')->put($r2Path, $imageContent);

                            $storedFiles[$file->getClientOriginalName()] = '/' . $r2Path;

                            if ($thumbnailImage && $file->getClientOriginalName() == $thumbnailImage) {
                                $thumbnailImage = '/' . $r2Path;
                            }
                        }
                    }

                    foreach ($imageOrder as $originalName) {
                        if (isset($storedFiles[$originalName])) {
                            $imageNames[] = $storedFiles[$originalName];
                        }
                    }
                }

                $imageJson = !empty($imageNames) ? json_encode($imageNames) : null;

                $skuProduct = [
                    'seller_id' => auth('seller')->id() ?? null,
                    'product_id' => $product_id,
                    'sizes' => $size,
                    'variation' => $skus,
                    'sku' => $request->skues[$index],
                    'tax' => $request->taxes[$index] ?? null,
                    'variant_mrp' => $request->unit_prices[$index] ?? null,
                    'discount_percent' => $request->var_tax[$index] ?? null,
                    'gst_percent' => $request->tax_gst[$index] ?? null,
                    'discount_type' => $request->discount_types[$index] ?? null,
                    'discount' => $request->discounts[$index] ?? null,
                    'listed_price' => $request->selling_prices[$index] ?? null,
                    'listed_percent' => $request->selling_taxs[$index] ?? null,
                    'listed_gst_percent' => $request->tax1_gst[$index] ?? null,
                    'commission_fee' => $request->commission_fee[$index] ?? null,
                    'quantity' => $request->quant[$index] ?? null,
                    'length' => $request->lengths[$index] ?? null,
                    'breadth' => $request->breadths[$index] ?? null,
                    'weight' => $request->weights[$index] ?? null,
                    'height' => $request->heights[$index] ?? null,
                    'color_name' => $request->color_names[$index] ?? null,
                    'thumbnail_image' => $thumbnailImage,
                    'image' => $imageJson,
                ];
                DB::table('sku_product_new')->insert($skuProduct);
            }

            $post = [
                'seller_id'               => auth('seller')->id() ?? null,
                'product_id'              => $product_id,
                'specification'           => json_encode($request->specification_values),
                'key_features'            => json_encode($request->features_values),
                'technical_specification' => json_encode($request->technical_specification_values),
                'other_details'           => json_encode($request->other_details_values),
                'created_at'              => now(),
                'updated_at'              => now()
            ];
            
            DB::table('key_specification_values')->insert($post);

            // $tag_ids = [];
            // if ($request->tags != null) {
            //     $tags = explode(",", $request->tags);
            // }
            // if(isset($tags)){
            //     foreach ($tags as $key => $value) {
            //         $tag = Tag::firstOrNew(
            //             ['tag' => trim($value)]
            //         );
            //         $tag->save();
            //         $tag_ids[] = $tag->id;
            //     }
            // }
            // $product->tags()->sync($tag_ids);

            $tag_ids = [];

            if ($request->tags != null) {
                $tags = explode(",", $request->tags);
            }

            if (isset($tags)) {
                foreach ($tags as $key => $value) {

                    $value = trim($value);

                    if ($value === '') {
                        continue;
                    }

                    if (mb_strlen($value) > 50) {
                        continue;
                    }

                    $tag = Tag::firstOrNew([
                        'tag' => $value
                    ]);

                    $tag->save();
                    $tag_ids[] = $tag->id;
                }
            }

            $product->tags()->sync($tag_ids);


            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
                if ($request->description[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Product',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
            Translation::insert($data);
            Toastr::success('Product added successfully!');
            return redirect()->route('seller.product.list');
        }
    }

    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {

            $products = Product::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()])
                ->where(function ($q) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                         if (preg_match('/PR\d+/', $search)) {
                        $digits = substr($search, 2);
                        $q->orWhere('id', $digits);
                    } else {
                        $q->orWhere('name', 'like', "%{$search}%")
                         ->orWhere('id',$search)
                        ->orWhere('HSN_code', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('variation', 'like', "%{$search}%");
                    }
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $products = Product::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()]);
        }

         if ($request->has('filter')) {

             $request['category_id'] = $request['category_id'] ?? false;
             $request['sub_category_id'] = $request['sub_category_id'] ?? false;
             $request['brand_id'] = $request['brand_id'] ?? false;
             $request['min_price'] = $request['min_price'] ?? false;
             $request['max_price'] = $request['max_price'] ?? false;
             $request['min_reviews'] = $request['min_reviews'] ?? false;

            $products = $products->with(['rating','reviews'])->when($request['category_id'] , function ($q) use ($request) {
                        return $q->where('category_id', $request['category_id']);
                    })
                    ->when($request['sub_category_id'] , function ($q) use ($request) {
                        return $q->where('sub_category_id',  $request['sub_category_id']);
                    })
                   ->when($request['brand_id'] , function ($q) use ($request) {
                        return $q->where('brand_id', $request['brand_id']);
                    })
                    ->when($request['min_price'] , function ($q) use ($request) {
                        return $q->where('unit_price', '>=', $request['min_price']);
                    })
                    ->when($request['max_price'] , function ($q) use ($request) {
                        return $q->where('unit_price', '<=', $request['max_price']);
                    })
                   ->when($request['min_reviews'] , function ($q) use ($request) {

                     return $q->whereHas('reviews', function ($query) use ($request) {
                        $query->where('reviews.rating', '<=', $request['min_reviews']);

                    });
                    });
             $query_param = ['filter' => $request['filter'], 'category_id' => $request['category_id'],'sub_category_id' => $request['sub_category_id'],'brand_id' => $request['brand_id'],'min_price' => $request['min_price'],'max_price' => $request['max_price'],'min_reviews' => $request['min_reviews']];
        }

        $filter         = $request['filter'];

        $products = $products->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);
        
        return view('seller-views.product.list', compact('products', 'search', 'filter'));
    }

    public function stock_limit_list(Request $request, $type)
    {
        $stock_limit = Helpers::get_business_settings('stock_limit');
        $sort_oqrderQty = $request['sort_oqrderQty'];
        $query_param = $request->all();
        $search = $request['search'];
        $pro = Product::where(['added_by' => 'seller', 'product_type'=>'physical', 'user_id' => auth('seller')->id()])
            ->where('request_status',1)
            ->when($request->has('status') && $request->status != null, function ($query) use ($request) {
                $query->where('request_status', $request->status);
            });

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];

        $pro = $pro->withCount('order_details')->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
            return $q->orderBy('current_stock', 'asc');
        })
            ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                return $q->orderBy('current_stock', 'desc');
            })
            ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'asc');
            })
            ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'desc');
            })
            ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                return $q->orderBy('id');
            })->where('current_stock', '<', $stock_limit);


        $products = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('seller-views.product.stock-limit-list', compact('products', 'search', 'request_status', 'sort_oqrderQty'));
    }

    public function stock_limit_export(Request $request)
    {
        $sort = $request['sort'] ?? 'ASC';
        $products = Product::when(empty($request['seller_id']) || $request['seller_id'] == 'all',function ($query){
            $query->whereIn('added_by', ['admin', 'seller']);
        })
            ->when($request['seller_id'] == 'in_house',function ($query){
                $query->where(['added_by' => 'admin']);
            })
            ->when($request['seller_id'] != 'in_house' && isset($request['seller_id']) && $request['seller_id'] != 'all',function ($query) use($request){
                $query->where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
            })
            ->orderBy('current_stock', $sort)->get();

        $data = array();
        foreach($products as $product){
            $data[] = array(
                'Product Name'   => $product->name,
                'Date'           => date('d M Y',strtotime($product->created_at)),
                'Total Stock'    => $product->current_stock,
            );
        }
        return (new FastExcel($data))->download('total_product_stock.xlsx');
    }

    public function update_quantity(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $product = Product::find($request['product_id']);
        if ($stock_count >= 0) 
        {
            $product->current_stock = $stock_count;
            $product->variation = json_encode($variations);
            $product->save();
            Toastr::success(\App\CPU\translate('product_quantity_updated_successfully!'));
            return back();
        } else {
            Toastr::warning(\App\CPU\translate('product_quantity_can_not_be_less_than_0_!'));
            return back();
        }
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->where('position',1)->orderBy('name')->get();

        if(count($cat)==0){
          $cat = Category::where(['sub_parent_id' => $request->parent_id])->orderBy('name')->get();
        }

        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }

    public function get_variations(Request $request)
    {
        $product = Product::find($request['id']);
        return response()->json([
            'view' => view('seller-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }
        $sub_category = $request->sub_category_id;
        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];
       

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);
        return response()->json([
            'view' => view('seller-views.product.partials._sku_combinations', compact(
                'combinations',
                'unit_price',
                'colors_active',
                'product_name',
                'sub_category'
            ))->render()
        ]);

    }

    public function sku_combination_edit(Request $request)
    {
        // dd($request->all());
        $product_id = $request->ids;
        

        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);
        // dd($combinations);
      
        return response()->json([
            'view' => view('seller-views.product.partials._sku11_combinations', compact('product_id','combinations', 'unit_price', 'colors_active', 'product_name','product_id'))->render(),
        ]);
    }

    public function edit($id)
    {
        $product = Product::withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->orderBy('name')->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        $warehouse = DB::table('warehouse')->where('seller_id',auth('seller')->id())->get();
       
        try {
            return view('seller-views.product.edit', compact('categories', 'br','warehouse', 'product', 'product_category', 'brand_setting', 'digital_product_setting'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        if ($brand_setting && empty($request->brand_id)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'brand_id', 'Brand is required!'
                );
            });
        }
       
        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }
        
        $product->name = $request->name[array_search('en', $request->lang)];
        $product->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);
        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $product->HSN_code = $request->HSN_code;
        $product->Return_days     = $request->Return_days;
        $product->Replacement_days = $request->Replacement_days;
        $product->product_type          = $request->product_type;
        $product->category_ids          = json_encode($category);
        $product->category_id          = $request->category_id;
        $product->sub_category_id      = $request->sub_category_id;
        $product->sub_sub_category_id  = $request->sub_sub_category_id;
        $product->brand_id              = isset($request->brand_id) ? $request->brand_id : null;
        $product->unit = $request->unit;
       
        $product->digital_product_type  = $request->product_type == 'digital' ? $request->digital_product_type : null;
        $product->details               = $request->description[array_search('en', $request->lang)];
        $product->free_delivery      = $request->free_delivery=='on'?1:0;

        $product->add_warehouse = $request->warehouse;
        $request->product_type = "physical";
        $request->product_type == 'digital';

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = $request->product_type == 'physical' ? json_encode($request->colors) : json_encode([]);
        } else {
            $colors = [];
            $product->colors = $request->product_type == 'physical' ? json_encode($colors) : json_encode([]);
        }
        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = array_map('trim', explode(',', implode('|', $request[$str])));
                array_push($choice_options, $item);
            }
        }
      
        $product->choice_options = $request->product_type == 'physical' ? json_encode($choice_options) : json_encode([]);
        $variations = [];
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        $combinations = Helpers::combinations($options);
       
        $variations = [];
        $stock_count = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
                            $color_name = Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                        } else {
                            $str .= str_replace(' ', '', $item);
                        }
                    }
                }
              
                $item = [];
                $item['type'] = $str;
                $item['price'] = Convert::usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
                $stock_count += $item['qty'];
            }
        } else {
            $stock_count = (integer)$request['current_stock'];
        }
      
        //combinations end
        $product->variation         = $request->product_type == 'physical' ? json_encode($variations) : json_encode([]);
        $product->unit_price        = Convert::usd($request->unit_price);
        $product->purchase_price    = Convert::usd($request->purchase_price ?? 0);
        $product->tax_model         = $request->tax_model ?? 'include';
        $product->code              = $request->code;
        $product->minimum_order_qty = $request->minimum_order_qty;
        $product->discount          = $request->discount_type == 'flat' ? Convert::usd($request->discount) : $request->discount;
        $product->attributes        = $request->product_type == 'physical' ? json_encode($request->choice_attributes) : json_encode([]);
        $product->discount_type     = $request->discount_type;
        $product->current_stock     = $request->product_type == 'physical' ? abs($stock_count) : 0;
        $product->shipping_cost     = $request->product_type == 'physical' ? (Helpers::get_business_settings('product_wise_shipping_cost_approval')==1?$product->shipping_cost:Convert::usd($request->shipping_cost)) : 0;
        $product->multiply_qty      = ($request->product_type == 'physical') ? ($request->multiplyQTY=='on'?1:0) : 0;
        $product->request_status = Helpers::get_business_settings('new_product_approval')==1?0:1;
        $product->status         = 0;
      
        if(Helpers::get_business_settings('product_wise_shipping_cost_approval')==1 && $product->shipping_cost != Convert::usd($request->shipping_cost))
        {
            $product->temp_shipping_cost = Convert::usd($request->shipping_cost);
            $product->is_shipping_cost_updated = 0;
        }

        $product->video_provider = 'youtube';
        $product->video_url = $request->video_link;
        if ($product->request_status == 2) {
            $product->request_status = 0;
        }

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
          
            $product->save();
        if($request->sizes){
            foreach ($request->sizes as $index => $skus) {
                $imageNames = [];

                $thumbnailImage = $request->input('thumbnail_image_' . $index);

                $imageOrder = json_decode($request->input('image_order_' . $index, '[]'), true);

                    if ($request->hasFile('image_' . $index)) {

                        $files = $request->file('image_' . $index);
                        $fileMap = [];

                        foreach ($files as $file) {
                            $fileMap[$file->getClientOriginalName()] = $file;
                        }

                        $productFolder = 'products';

                        foreach ($imageOrder as $originalName) {

                            if (isset($fileMap[$originalName]) && $fileMap[$originalName]->isValid()) {

                                $file = $fileMap[$originalName];
                                $ext = strtolower($file->getClientOriginalExtension());

                                $safeName = uniqid() . '.webp';

                                if ($ext === 'webp') {
                                    $imageContent = file_get_contents($file->getRealPath());
                                } else {
                                    $imageContent = (string) Image::make($file->getRealPath())
                                                        ->encode('webp', 90);
                                }

                                $r2Path = $productFolder . '/' . $safeName;

                                Storage::disk('r2')->put($r2Path, $imageContent);

                                $publicPath = '/' . $r2Path;

                                $imageNames[] = $publicPath;

                                if ($thumbnailImage == $originalName) {
                                    $thumbnailImage = $publicPath;
                                }
                            }
                        }
                    }
                    $oldImages  = $request->input('old_image_' . $index, []);
                    $finalImages = array_merge($oldImages, $imageNames);
                    $imageJson = !empty($finalImages) ? json_encode($finalImages) : null;

                $skuProduct = [
                    'seller_id' => auth('seller')->id() ?? null,
                    'product_id' => $id,
                    'sizes' => $skus,
                    'sku' => $request->skues[$index] ?? null,
                    'tax' => $request->taxes[$index] ?? null,
                    'variant_mrp' => $request->unit_prices[$index] ?? null,
                    'discount_percent' => $request->var_tax[$index] ?? null,
                    'gst_percent' => $request->tax_gst[$index] ?? null,
                    'discount_type' => $request->discount_types[$index] ?? null,
                    'discount' => $request->discounts[$index] ?? null,
                    'listed_price' => $request->selling_prices[$index] ?? null,
                    'listed_percent' => $request->selling_taxs[$index] ?? null,
                    'listed_gst_percent' => $request->tax1_gst[$index],
                    'commission_fee' => $request->commission_fee[$index] ?? null,
                    'quantity' => $request->quant[$index] ?? null,
                    'length' => $request->lengths[$index] ?? null,
                    'breadth' => $request->breadths[$index] ?? null,
                    'weight' => $request->weights[$index] ?? null,
                    'height' => $request->heights[$index] ?? null,
                    'color_name' => $request->color_names[$index] ?? null,
                    'thumbnail_image' => $thumbnailImage ?? $request->new_thumbnail_image,
                    'image' => $imageJson,
                ];
                
                DB::table('sku_product_new')
                    ->where('sizes', $skus)
                    ->where('sku', $request->skues[$index])
                    ->where('product_id', $request->ids)
                    ->update($skuProduct);
            
            }
        }
    
        $post = [
            'seller_id'               => auth('seller')->id() ?? null,
            'product_id'              => $id,
            'specification'           => json_encode($request->specification_values),
            'key_features'            => json_encode($request->features_values),
            'technical_specification' => json_encode($request->technical_specification_values),
            'other_details'           => json_encode($request->other_details_values),
            'created_at'              => now(),
            'updated_at'              => now()
        ];
            
        DB::table('key_specification_values')->where('product_id', $id)->update($post);
            
            $tag_ids = [];
            if ($request->tags != null) {
                $tags = explode(",", $request->tags);
            }
            if(isset($tags)){
                foreach ($tags as $key => $value) {
                    $tag = Tag::firstOrNew(
                        ['tag' => trim($value)]
                    );
                    $tag->save();
                    $tag_ids[] = $tag->id;
                }
            }
            $product->tags()->sync($tag_ids);

            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'name'],
                        ['value' => $request->name[$index]]
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Product',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'description'],
                        ['value' => $request->description[$index]]
                    );
                }
            }
            Toastr::success('Product updated successfully.');
            return back();
        }
    }

    public function view($id)
    {
        $product = Product::with(['reviews'])->where(['id' => $id])->first();
        $sku = DB::table('sku_product_new')->where('product_id',$product->id)->get();
        $reviews = Review::where(['product_id' => $id])->paginate(Helpers::pagination_limit());
        return view('seller-views.product.view', compact('product', 'reviews','sku'));
    }

    public function remove_image(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $images = json_decode($product->images, true);
        if (count($images) < 2) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }

        // ✅ Delete image from Cloudflare R2
        $imagePath = 'products/' . $request->name; // adjust folder if needed
        Storage::disk('r2')->delete($imagePath);

        // ✅ Update color images
        $colors = json_decode($product->colors, true);
        $color_images = json_decode($product->color_image, true);
        $color_image_arr = [];

        if ($colors && $color_images) {
            foreach ($color_images as $img) {
                if ($img['color'] != $request->color && $img['image_name'] != $request->name) {
                    $color_image_arr[] = [
                        'color' => $img['color'] ?? null,
                        'image_name' => $img['image_name'],
                    ];
                }
            }
        }

        // ✅ Remove image from product images
        $updatedImages = array_filter($images, fn($img) => $img != $request->name);

        // ✅ Update product record
        $product->update([
            'images' => json_encode(array_values($updatedImages)),
            'color_image' => json_encode($color_image_arr),
        ]);

        Toastr::success('Product image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        Cart::where('product_id', $product->id)->delete();

        // Other images
        if (!empty($product->images)) {
            $images = json_decode($product->images, true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('r2')->delete(ltrim($image, '/'));
                }
            }
        }

        if (!empty($product->thumbnail)) {
            Storage::disk('r2')->delete(ltrim($product->thumbnail, '/'));
        }

        $bulkQuery = DB::table('bulk_image')
            ->where('seller_id', auth('seller')->id());

        $paths = [];

        if (!empty($product->thumbnail)) {
            $paths[] = $product->thumbnail;
        }

        if (!empty($product->images)) {
            $images = json_decode($product->images, true);
            if (is_array($images)) {
                $paths = array_merge($paths, $images);
            }
        }

        if (!empty($paths)) {
            $bulkQuery->whereIn('image_path', $paths)->delete();
        }

        FlashDealProduct::where('product_id', $id)->delete();
        DealOfTheDay::where('product_id', $id)->delete();

        $product->delete();

        Toastr::success('Product removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('seller-views.product.bulk-import');
    }

    public function search_bulk_import_index()
    {
        $cat = Category::where(['position' => 0, 'home_status' => 1])->orderBy('name')->get();
        $br = Brand::active()->orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        return view('seller-views.product.search_bulk_import_index', compact('cat', 'br', 'brand_setting', 'digital_product_setting'));

    }

    public function search_bulk_import_category(Request $request)
    {
        session([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'sub_sub_category_id' => $request->sub_sub_category_id,
            'brand_id' => $request->brand_id,
        'tax'=>$request->tax,
        'hsn_code'=>$request->hsn_code,
        'procurement_time'=>$request->procurement_time,
        ]);

        $category_id = session('category_id');
        $sub_category_id = session('sub_category_id');
        $sub_sub_category_id = session('sub_sub_category_id');
        $brand_id = session('brand_id');
        $tax = session('tax');
        $procurement_time = session('procurement_time');
        $hsn_code = session('hsn_code');
        return view('seller-views.product.bulk-import', compact('category_id', 'sub_category_id', 'sub_sub_category_id','brand_id','tax','procurement_time','hsn_code'));
    }

    private function normalizeSpecifications(array $row, array $allowed): array
    {
        $result = [
            'specification' => [],
            'key_features' => [],
            'technical_specification' => [],
            'other_details' => [],
        ];

        // Excel se values map karo
        $excel = [
            'specification' => [],
            'key_features' => [],
            'technical_specification' => [],
            'other_details' => [],
        ];

        foreach ($row as $header => $value) {

            $header = trim((string)$header);
            $value  = trim((string)$value);

            if ($value === '' || $value === '-') {
                continue;
            }

            // SPECIFICATION
            if (stripos($header, 'Specification:') === 0) {
                $key = trim(str_ireplace('Specification:', '', $header));
                $excel['specification'][$key] = $value;
            }

            // KEY FEATURES
            elseif (stripos($header, 'Key features:') === 0) {
                $key = trim(str_ireplace('Key features:', '', $header));
                $excel['key_features'][$key] = $value;
            }

            // TECHNICAL SPECIFICATION
            elseif (stripos($header, 'Technical specification:') === 0) {
                $key = trim(str_ireplace('Technical specification:', '', $header));
                $excel['technical_specification'][$key] = $value;
            }

            // OTHER DETAILS
            elseif (stripos($header, 'Other details:') === 0) {
                $key = trim(str_ireplace('Other details:', '', $header));
                $excel['other_details'][$key] = $value;
            }
        }

        // ✅ Category order maintain + N/A fallback
        foreach ($allowed as $type => $keys) {
            foreach ($keys as $k) {
                if (isset($excel[$type][$k])) {
                    $result[$type][] = $excel[$type][$k];
                } else {
                    $result[$type][] = 'N/A';
                }
            }
        }

        return $result;
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $spreadsheet = IOFactory::load($request->file('products_file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $headers = array_map('trim', $data[1]); 
            $dimheaders = array_map('trim', $data[2]);

            $rows = [];
            $dimensions = [];

            foreach (array_slice($data, 3) as $index => $row) {
                $clean = array_map('trim', $row);

                if (count($clean) == count($headers)) {
                    $rows[$index] = array_combine($headers, $clean);
                }

                if (count($clean) == count($dimheaders)) {
                    $dimensions[$index] = array_combine($dimheaders, $clean);
                }
            }
        } catch (\Exception $e) {
            Toastr::error('File Error: ' . $e->getMessage());
            return back();
        }

        $dbColors = DB::table('colors')->pluck('code', 'name')->toArray();

        $groupedProducts = [];

        foreach ($rows as $i => $row) {
            if (empty($row['Product Title'])) continue;

            $title = trim($row['Product Title']);
            $colorName = trim($row['Colour'] ?? ''); 
            $size = trim($row['Size'] ?? '');

           

            $dimRow = $dimensions[$i] ?? [];
            $packaging_dimensions = [
                'length'  => (float)($dimRow['Length (CM)'] ?? 0),
                'breadth' => (float)($dimRow['Breadth (CM)'] ?? 0),
                'height'  => (float)($dimRow['Height (CM)'] ?? 0),
                'weight'  => (float)($dimRow['Weight (KG)'] ?? 0),
            ];
            $colorNames = [
                'Colour_Name' => ($dimRow['Colour Name'] ?? 0),
                'Rename_Colour_Name' => ($dimRow['Rename Colour Name'] ?? 0),
            ];
            
            $colorCode = $dbColors[$colorName] ?? '#000000';

            if (!isset($groupedProducts[$title])) {
                $groupedProducts[$title] = [
                    'product_data' => [
                        'title' => $title,
                        'brand' => $row['Brands'] ?? null,
                        'description' => $row['Product Description'] ?? null,
                        'packaging_dimensions' => $packaging_dimensions,
                        'RenameColour' => $colorNames,
                        'full_row' => $row
                    ],
                    'variations' => [],
                    'colors' => [],
                    'color_names' => [],
                ];
            }

            $variationType = trim($colorName . '-' . $size);
            
            $mrp = (float)($row['MRP (INR)'] ?? 0);
            $tax = (float)($row['Tax'] ?? 0);
            $discountType = $row['Discount Type'] ?? null;
            $discountVal = $row['Discount'] ?? 0;

            $discountAmount = ($mrp * $tax) / 100;

            $listedPrice = 0;
            if ($discountType === 'percent') {
                $listedPrice = $mrp - (($mrp * (float)$discountVal) / 100);
            } elseif ($discountType === 'flat') {
                $listedPrice = $mrp - (float)$discountVal;
            } else {
                $listedPrice = (float)($variation['price'] ?? 0);
            }

            $groupedProducts[$title]['variations'][] = [
                'type' => $variationType,
                'price' => $listedPrice,
                'sku' => $row['Seller SKU ID'] ?? null,
                'qty' => (int)($row['Stock'] ?? 0),
                'size' => $size,
                '_full_row' => $row,
                '_dimensions' => $packaging_dimensions,
                '_color_name' => $colorName,
                '_color_rename' => $colorNames,
                '_color_code' => $colorCode,
                '_size' => $size,
            ];

            if (!in_array($colorCode, $groupedProducts[$title]['colors'])) {
                $groupedProducts[$title]['colors'][] = $colorCode;
            }
            if (!in_array($colorName, $groupedProducts[$title]['color_names'])) {
                $groupedProducts[$title]['color_names'][] = $colorName;
            }
        }

        $countRow = 0;

        DB::beginTransaction();
        try {
            foreach ($groupedProducts as $title => $data) {
                $row = $data['product_data']['full_row'];
                $dim = $data['product_data']['packaging_dimensions'];

                $category_id = DB::table('categories')
                    ->where('name', $row['Product Category'] ?? null)
                    ->where('parent_id', 0)
                    ->value('id');

                $sub_category_id = DB::table('categories')
                    ->where('name', $row['Product Sub Category'] ?? null)
                    ->where('parent_id', $category_id)
                    ->value('id');

                $sub_sub_category_id = DB::table('categories')
                    ->where('name', $row['Product Sub Sub Category'] ?? null)
                    ->where('sub_parent_id', $sub_category_id)
                    ->value('id');

                $brand_id = DB::table('brands')
                    ->where('name', $row['Brands'] ?? null)
                    ->value('id');

                $replacement = '';
                if (!empty($row['Return Days']) && strtoupper($row['Return Days']) != 'NO') {
                    $replacement = $row['Return Days'];
                } else {
                    $replacement = $row['Replacement Days'] ?? null;
                }

                $freeDel = '';
                if($row['Free Delivery'] == 'No')
                {
                    $freeDel = 0;
                }elseif($row['Free Delivery'] == 'Yes'){
                    $freeDel =1;
                }

                $product = new Product();
                $product->added_by = 'seller';
                $product->user_id = auth('seller')->id();
                $product->name = $title;
                $product->HSN_code = $row['HSN'] ?? null;
                $product->slug = Str::slug($title);

                $product->category_ids = json_encode([
                    ['id' => (string)$category_id, 'position' => 1],
                    ['id' => (string)$sub_category_id, 'position' => 2],
                    ['id' => (string)$sub_sub_category_id, 'position' => 3],
                ]);

                $product->category_id = $category_id;
                $product->sub_category_id = $sub_category_id;
                $product->sub_sub_category_id = $sub_sub_category_id;
                $product->brand_id = $brand_id;
                $product->cities = '[""]';
                $product->product_type="physical";
                $product->add_warehouse = $row['Warehouse'] ?? null;
                $product->Return_days = $row['Return Days'] ?? null;
                $product->replacement_days = $replacement;
                $product->thumbnail = isset($row['Thumbnail Image name']) ?  $row['Thumbnail Image name'] : null;
                $product->images = isset($row['Other Image name']) ? json_encode(array_filter(explode(',', $row['Other Image name']))) : null;
                $product->details = $row['Product Description'] ?? null;
                $product->unit = $row['Unit'] ?? 'pc';
                $product->min_qty = $row['MOQ'] ?? 1;
                $product->free_shipping = (strtolower($row['Free Delivery'] ?? 'no') === 'yes') ? 1 : 0;
                $product->video_provider = 'youtube';
                $product->video_url = $row['Video URL'] ?? null;
                $product->colors = json_encode($data['colors']);
                $product->length = $dim['length'];
                $product->breadth = $dim['breadth'];
                $product->height = $dim['height'];
                $product->weight = $dim['weight'];
                $product->free_delivery = $freeDel;  

                $product->status = 1;
                $product->published = 1;

                $all_color_names = array_values(array_filter(array_unique($data['color_names'])));
                $all_sizes = array_values(array_filter(array_unique(array_column($data['variations'], '_size'))));

                $choice_options = [];
                if (count($all_color_names) > 0) {
                    $choice_options[] = [
                        'name' => 'choice_1',
                        'title' => 'Color',
                        'options' => $all_color_names
                    ];
                }
                if (count($all_sizes) > 0) {
                    $choice_options[] = [
                        'name' => 'choice_2',
                        'title' => 'Size',
                        'options' => $all_sizes
                    ];
                }

                $product->choice_options = json_encode($choice_options);
                $product->attributes = json_encode([1]);
                $options = [];
                if (count($all_color_names) > 0) $options[] = $all_color_names;
                if (count($all_sizes) > 0) $options[] = $all_sizes;

                $combinations = [];
                if (!empty($options)) {
                    if (class_exists('Helpers') && method_exists('Helpers', 'combinations')) {
                        $combinations = Helpers::combinations($options);
                    } else {
                        $combinations = [[]];
                        foreach ($options as $optionSet) {
                            $new = [];
                            foreach ($combinations as $comb) {
                                foreach ($optionSet as $opt) {
                                    $new[] = array_merge($comb, [$opt]);
                                }
                            }
                            $combinations = $new;
                        }
                    }
                }

                $finalVariations = [];
                $stock_count = 0;

                if (!empty($combinations)) {
                    foreach ($combinations as $combination) {
                        $type = implode('-', $combination);

                        $matched = null;
                        foreach ($data['variations'] as $v) {
                            if (trim($v['type']) === trim($type)) {
                                $matched = $v;
                                break;
                            }
                        }

                        if ($matched) {
                            $finalVariations[] = [
                                'type' => $matched['type'],
                                'price' => $matched['price'],
                                'sku' => $matched['sku'],
                                'qty' => $matched['qty'],
                            ];
                            $stock_count += (int)$matched['qty'];
                        } else {
                            $finalVariations[] = [
                                'type' => $type,
                                'price' => 0,
                                'sku' => null,
                                'qty' => 0,
                            ];
                        }
                    }
                } else {
                    if (!empty($data['variations'])) {
                        $sumQty = 0;
                        foreach ($data['variations'] as $v) {
                            $finalVariations[] = [
                                'type' => $v['type'],
                                'price' => $v['price'],
                                'sku' => $v['sku'],
                                'qty' => $v['qty'],
                            ];
                            $sumQty += (int)$v['qty'];
                        }
                        $stock_count = $sumQty;
                    } else {
                        $finalVariations[] = [
                            'type' => $title,
                            'price' => 0,
                            'sku' => null,
                            'qty' => 0,
                        ];
                    }
                }

                $product->variation = json_encode($finalVariations);
                $product->current_stock = (int)$stock_count;

                $product->save();
                
                foreach ($data['variations'] as $variation) {
                    $vrow = $variation['_full_row'];
                    $vdim = $variation['_dimensions'];
                    $vcol = $variation['_color_rename'];

                    $mrp = (float)($vrow['MRP (INR)'] ?? 0);
                    $tax = (float)($vrow['Tax'] ?? 0); // your column called Tax
                    $discountType = $vrow['Discount Type'] ?? null;
                    $discountVal = $vrow['Discount'] ?? 0;

                    $discountAmount = ($mrp * $tax) / 100;

                    $listedPrice = 0;
                    if ($discountType === 'percent') {
                        $listedPrice = $mrp - (($mrp * (float)$discountVal) / 100);
                    } elseif ($discountType === 'flat') {
                        $listedPrice = $mrp - (float)$discountVal;
                    } else {
                        $listedPrice = (float)($variation['price'] ?? 0);
                    }

                    $listedPercent = ($listedPrice * $tax) / 100;

                    DB::table('sku_product_new')->insert([
                        'seller_id' => auth('seller')->id(),
                        'product_id' => $product->id,
                        'commission_fee' => $vrow['Commission Fee'] ?? 0,
                        'sku' => $variation['sku'],
                        'variation' => $variation['type'],
                        'variant_mrp' => $mrp,
                        'discount_percent' => $discountAmount,
                        'gst_percent' => max(0, $mrp - $discountAmount),
                        'discount_type' => $discountType,
                        'discount' => $vrow['Discount'] ?? 0,
                        'listed_price' => $listedPrice,
                        'listed_percent' => $listedPercent,
                        'listed_gst_percent' => max(0, $listedPrice - $listedPercent),
                        'sizes' => $variation['size'],
                        'quantity' => $variation['qty'],
                        'color_name' => $vcol['Rename_Colour_Name'] ?? null,
                        'tax' => $tax,
                        'length' => $vdim['length'],
                        'breadth' => $vdim['breadth'],
                        'height' => $vdim['height'],
                        'weight' => $vdim['weight'],
                        'image' => isset($vrow['Other Image name']) ? json_encode(array_filter(explode(',', $vrow['Other Image name']))) : null,
                        'thumbnail_image' =>$vrow['Thumbnail Image name'] ?? null,
                    ]);
                }


                $category = DB::table('categories')->where('id', $sub_sub_category_id)->first();

                $allowedSpec = array_map('trim', explode(',', $category->specification ?? ''));
                $allowedFeatures = array_map('trim', explode(',', $category->key_features ?? ''));
                $allowedTech = array_map('trim', explode(',', $category->technical_specification ?? ''));
                $allowedOther = array_map('trim', explode(',', $category->other_details ?? ''));

                $allowed = [
                    'specification' => $allowedSpec,
                    'key_features' => $allowedFeatures,
                    'technical_specification' => $allowedTech,
                    'other_details' => $allowedOther,
                ];

                $normalized = $this->normalizeSpecifications($row, $allowed);


                DB::table('key_specification_values')->updateOrInsert(
                    ['product_id' => $product->id],
                    [
                        'seller_id'               => auth('seller')->id(),
                        'specification'           => json_encode($normalized['specification']),
                        'key_features'            => json_encode($normalized['key_features']),
                        'technical_specification' => json_encode($normalized['technical_specification']),
                        'other_details'           => json_encode($normalized['other_details']),
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]
                );

                // if (!empty($row['Search Tags'])) {
                //     $tagNames = array_map('trim', explode(',', $row['Search Tags']));
                //     $tag_ids = [];
                //     foreach ($tagNames as $tagName) {
                //         if ($tagName === '') continue;
                //         $tag = Tag::firstOrCreate(['tag' => $tagName]);
                //         $tag_ids[] = $tag->id;
                //     }
                //     if (!empty($tag_ids)) {
                //         if (method_exists($product, 'tags')) {
                //             $product->tags()->sync($tag_ids);
                //         }
                //     }
                // }

                if (!empty($row['Search Tags'])) {

                    $tagNames = array_map('trim', explode(',', $row['Search Tags']));
                    $tag_ids = [];

                    foreach ($tagNames as $tagName) {

                        if ($tagName === '') {
                            continue;
                        }

                        if (mb_strlen($tagName) > 50) {
                            continue;
                        }

                        $tag = Tag::firstOrCreate([
                            'tag' => $tagName
                        ]);

                        $tag_ids[] = $tag->id;
                    }

                    if (!empty($tag_ids) && method_exists($product, 'tags')) {
                        $product->tags()->sync($tag_ids);
                    }
                }

                $countRow++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Import Error: ' . $e->getMessage());
            return back();
        }

        Toastr::success($countRow . ' Products Imported Successfully!');
        return back();
    }

    public function bulk_image()
    {
        $images = DB::table('bulk_image')
            ->where('seller_id', auth('seller')->id())
            ->get();

        return view('seller-views.product.bulk-image', compact('images'));
    }

    public function bulk_image_import(Request $request)
    {
        if ($request->hasFile('images')) {

            $seller_id = auth('seller')->id(); // ✅ Seller ID

            foreach ($request->file('images') as $image) {

                $originalName = pathinfo(
                    $image->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $extension = strtolower($image->getClientOriginalExtension());

                // ✅ Seller ID Prefix added
                $webpName = $seller_id . '_' . $originalName . '.webp';

                $r2Folder = 'products';
                $r2Path = $r2Folder . '/' . $webpName;

                // ✅ If already webp
                if ($extension === 'webp') {
                    $imageContent = file_get_contents($image->getRealPath());
                } 
                else {

                    $imageType = exif_imagetype($image->getPathname());

                    switch ($imageType) {

                        case IMAGETYPE_JPEG:
                            $imgResource = imagecreatefromjpeg($image->getPathname());
                            break;

                        case IMAGETYPE_PNG:
                            $imgResource = imagecreatefrompng($image->getPathname());
                            imagepalettetotruecolor($imgResource);
                            imagealphablending($imgResource, true);
                            imagesavealpha($imgResource, true);
                            break;

                        case IMAGETYPE_GIF:
                            $imgResource = imagecreatefromgif($image->getPathname());
                            break;

                        default:
                            return back()->with('error', 'Unsupported image type');
                    }

                    ob_start();
                    imagewebp($imgResource, null, 80);
                    $imageContent = ob_get_clean();
                    imagedestroy($imgResource);
                }

                Storage::disk('r2')->put($r2Path, $imageContent);
                DB::table('bulk_image')->insert([
                    'seller_id' => auth('seller')->id(),
                    'image_path' => $r2Path   // ✅ FIXED
                ]);
            }

            Toastr::success('All images uploaded successfully.');
            return back();
        }
        return back()->with('error', 'No images found.');
    }

    public function bulk_export_data_category_wise()
    {
        $category_id = session('category_id');
        $sub_category_id = session('sub_category_id');
        $sub_sub_category_id = session('sub_sub_category_id');
        $product_id = session('product_id');
        $brand_id = session('brand_id');
        $tax = session('tax');
        $procurement_time = session('procurement_time');
        $hsn_code = session('hsn_code');

        $seller_id = auth('seller')->id();
        $commission_fee = DB::table('sellers')->where('id', $seller_id)->first();

        $commission_type = match ($commission_fee->commission_fee ?? 1) {
        1 => 'Default',
        2 => 'In Percent',
        default => 'Transfer Price',
        };

        $category = DB::table('categories')->where('id', $category_id)->first();
        $product = Product::where('id', $product_id)->first() ?? new Product();
        $skuProduct = DB::table('sku_product_new')->where('product_id', $product_id)->first();
        $sub_category = DB::table('categories')->where('id', $sub_category_id)->first();
        $sub_sub_category = DB::table('categories')->where('id', $sub_sub_category_id)->first();
        $brand_name = DB::table('brands')->where('id', $brand_id)->first();

        $specifications = !empty($sub_sub_category->specification) ? explode(',',
        $sub_sub_category->specification) : [];
        $key_features = !empty($sub_sub_category->key_features) ? explode(',',
        $sub_sub_category->key_features) : [];
        $technical_specifications = !empty($sub_sub_category->technical_specification) ? explode(',',
        $sub_sub_category->technical_specification) : [];
        $other_details = !empty($sub_sub_category->other_details) ? explode(',',
        $sub_sub_category->other_details) : [];

        $colors = DB::table('colors')->pluck('name')->toArray();

        $baseData = [
        'InteriorChowk Product Code' => '',
        'Catelogue QC Status' => '',
        'QC Failed Reason (if any)' => $product->qc_failed_reason ?? '',
        'Product Category' => $category->name ?? '',
        'Product Sub Category' => $sub_category->name ?? '',
        'Product Sub Sub Category' => $sub_sub_category->name ?? '',
        'Brands' => $brand_name->name ?? '',
        'Seller SKU ID' => '',
        'Commission Type' => $commission_type,
        'Commission Fee' => $commission_fee->fee ?? '',
        'Listing Status' => '',
        'MRP (INR)' => $skuProduct->variant_mrp ?? '',
        'Discount Type' => $skuProduct->discount_type ?? '',
        'Discount' => $skuProduct->discount ?? '',
        // 👇 Formula directly
        'Your selling price (INR)' => '=IF(M2="Flat", MAX(0, L2-N2), IF(M2="Percent", MAX(0,
        L2-(L2*N2/100)), ""))',
        'Product Title' => '',
        'Product Description' => '',
        'Colour' => implode(', ', $colors),
        'Size' => '',
        'Return Days' => '',
        'Replacement Days' => '',
        'Unit' => '',
        'Free Delivery' => '',
        'Self Delivery' => '',
        'Warehouse' => 'Noida warehouse',
        'Procurement SLA (DAY)' => $procurement_time,
        'Stock' => '',
        'MOQ' => '',
        'Packaging Dimensions' => '',
        'HSN' => $hsn_code,
        'Tax' => $tax,
        'Thumbnail Image name' => '',
        'Other Image name' => '',
        'Video URL' => '',
        'PDF URL' => '',
        'Search Tags' => '',
        'Excel error status' => '',
        ];


        foreach ($specifications as $spec) {
        $baseData['Specification: ' . trim($spec)] = '';
        }
        foreach ($key_features as $feature) {
        $baseData['Key features: ' . trim($feature)] = '';
        }
        foreach ($technical_specifications as $tech) {
        $baseData['Technical specification: ' . trim($tech)] = '';
        }
        foreach ($other_details as $other) {
        $baseData['Other details: ' . trim($other)] = '';
        }

        $baseData = array_merge($baseData, [
        'Return Days' => '',
        'Replacement Days' => '',
        'Unit' => '',
        'Free Delivery' => '',
        'Self Delivery' => '',
        'Warehouse' => 'Noida warehouse',
        'Procurement SLA (DAY)' => $procurement_time,
        'Stock' => '',
        'MOQ' => '',
        'Packaging Dimensions' => '',
        'HSN' => $hsn_code,
        'Tax' => $tax,
        'Thumbnail Image name' => '',
        'Other Image name' => '',
        'Video URL' => '',
        'PDF URL' => '',
        'Search Tags' => '',
        'Excel error status' => '',
        ]);

        $map = [
        'InteriorChowk Product Code' => ['', 'To be filled by InteriorChowk'],
        'Catelogue QC Status' => ['', 'To be filled by InteriorChowk'],
        'QC Failed Reason (if any)' => ['', 'To be filled by InteriorChowk'],
        'Product Category' => ['', 'To be filled by InteriorChowk'],
        'Product Sub Category' => ['', 'To be filled by InteriorChowk'],
        'Product Sub Sub Category' => ['', 'To be filled by InteriorChowk'],
        'Brands' => ['', 'To be filled by InteriorChowk'],
        'Seller SKU ID' => ['Text - limited to 64 characters (including spaces)', 'Seller SKU ID is the
        identification number maintained by seller to keep track of SKUs. This will be mapped with
        InteriorChowk product code.'],
        'Commission Type' => ['', 'To be filled by InteriorChowk'],
        'Commission Fee' => ['', 'To be filled by InteriorChowk'],
        'Listing Status' => ['Single - Text', 'Inactive listings are not available for buyers on
        InteriorChowk'],
        'MRP (INR)' => ['Single - Positive_integer', 'Maximum retail price of the product'],
        'Discount Type' => ['-', 'Write flat or percent'],
        'Discount' => ['-', 'In Rs. Or In %'],
        'Your selling price (INR)' => ['Single - Positive_integer', 'Price at which you want to sell this
        listing'],
        'Product Title' => ['Single - Text Used For: Title', 'Product Title is the identity of the product
        that helps in distinguishing it from other products.'],
        'Product Description' => ['Single - Text', 'Please write few lines describing your product...'],
        'Colour' => [['Colour Name','Rename Colour Name'], ['Eg. Silver','Eg. Matt Finish']],
        'Size' => ['Add size', 'Eg. S, M, L or custom size'],
        'Specification' => [['Brand','Product Dimensions','Wattage','Voltage'],['-','-','-','-']],
        'Key Features' => [['Manufacturer','Packer','Net Quantity','Included
        Components'],['-','-','-','-']],
        'Technical Specification' => [['A','B','C','D'],['-','-','-','-']],
        'Other Details' => [['A','B','C','D'],['-','-','-','-']],
        'Return Days' => ['Number', '-'],
        'Replacement Days' => ['Number', 'Enter number of days only if you want to offer replacement only
        (no return).'],
        'Unit' => ['-', 'Eg. - Kg, pc, gms, lts, set, Pair, Sqft, Sq Mtr., Box'],
        'Free Delivery' => ['-', 'Yes / No'],
        'Self Delivery' => ['-', 'If this product is heavy flammable fragile etc. Please enter yes.'],
        'Warehouse' => ['Noida warehouse', "Fill your warehouse name here."],
        'Procurement SLA (DAY)' => ['Single - Number', 'Time required to keep the product ready for
        dispatch.'],
        'Stock' => ['Number', 'Number of items you have in stock. Add minimum 5 quantity to ensure listing
        visibility'],
        'MOQ' => ['Number', 'Write a minimum order quantity'],
        'Packaging Dimensions' => [['Length (CM)','Breadth (CM)','Height (CM)','Weight (KG)'], ['Length of
        the package in cms','Breadth of the package in cms','Height of the package in cms','Weight of the
        final package in kgs']],
        'HSN' => ['Single - Text', 'To be filled by InteriorChowk'],
        'Tax' => ['Single - Text',"InteriorChowk's tax code which decides the GST for the listing"],
        'Thumbnail Image name' => ['Image name', '1st Image (Thumbnail): Front View – Minimum resolution
        100x500'],
        'Other Image name' => ['Eg.TABLELAMP01.webp,TABLELAMP02.webp', 'Upload images in order (2nd – Back,
        3rd – Open, 4th – Side, 5th – Lifestyle, 6th – Detail).'],
        'Video URL' => ['URL', 'See the summary sheet for Video URL guidelines.'],
        'PDF URL' => ['URL', 'See the summary sheet for PDF URL guidelines.'],
        'Search Tags' => ['-', 'Write search keywords and tags for the product'],
        'Excel error status' => ['-', '❌ Error if missing, ✅ OK when all details are filled.'],
        ];

        $keys = array_keys($baseData);

        // --- Export Excel
        $filename = "products_bulk.xls";
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        echo '<table border="1" cellspacing="0" cellpadding="5">';

            // Row 1: Headers
            echo '<tr
                style="background-color:#c4d79b; color:#000; vertical-align:middle; font-weight:bold; text-align:center;">
                ';
                foreach ($keys as $key) {
                if (isset($map[$key]) && is_array($map[$key][0])) {
                $colspan = count($map[$key][0]);
                echo "<td colspan=\"$colspan\">{$key}</td>";
                } else {
                echo "<td>{$key}</td>";
                }
                }
                echo '</tr>';

            // Row 2: Instruction 1
            echo '<tr
                style="background-color:#ffff00; color:#000; vertical-align:middle; font-weight:bold; text-align:center;">
                ';
                foreach ($keys as $key) {
                if (isset($map[$key])) {
                $inst1 = $map[$key][0];
                if (is_array($inst1)) {
                foreach ($inst1 as $sub) {
                echo "<td>{$sub}</td>";
                }
                } else {
                echo "<td>{$inst1}</td>";
                }
                } else {
                echo "<td>-</td>";
                }
                }
                echo '</tr>';

            // Row 3: Instruction 2
            echo '<tr
                style="background-color:#fcd5b4; color:#ff0000; font-weight:bold; text-align:center; vertical-align:middle;">
                ';
                foreach ($keys as $key) {
                if (isset($map[$key])) {
                $inst2 = $map[$key][1];
                if (is_array($inst2)) {
                foreach ($inst2 as $sub) {
                echo "<td>{$sub}</td>";
                }
                } else {
                echo "<td>{$inst2}</td>";
                }
                } else {
                echo "<td>-</td>";
                }
                }
                echo '</tr>';

            // Row 4: Data
            echo '<tr>';
                foreach ($baseData as $key => $value) {
                if (isset($map[$key]) && is_array($map[$key][0])) {
                foreach ($map[$key][0] as $sub) {
                echo "<td style='text-align:center; vertical-align:middle;'>{$value}</td>";
                }
                } else {
                echo "<td style='text-align:center; vertical-align:middle;'>{$value}</td>";
                }
                }
                echo '</tr>';

            echo '</table>';
    }

    public function bulk_export_data()
    {
        $products = Product::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()])->get();
        //export from product
        $storage = [];
        foreach ($products as $item) {
        $category_id = 0;
        $sub_category_id = 0;
        $sub_sub_category_id = 0;
        foreach (json_decode($item->category_ids, true) as $category) {
        if ($category['position'] == 1) {
        $category_id = $category['id'];
        } else if ($category['position'] == 2) {
        $sub_category_id = $category['id'];
        } else if ($category['position'] == 3) {
        $sub_sub_category_id = $category['id'];
        }
        }
        $storage[] = [
        'name' => $item->name,
        'category_id' => $category_id,
        'sub_category_id' => $sub_category_id,
        'sub_sub_category_id' => $sub_sub_category_id,
        'brand_id' => $item->brand_id,
        'unit' => $item->unit,
        'min_qty' => $item->min_qty,
        'refundable' => $item->refundable,
        'youtube_video_url' => $item->video_url,
        'unit_price' => $item->unit_price,
        'purchase_price' => $item->purchase_price,
        'tax' => $item->tax,
        'discount' => $item->discount,
        'discount_type' => $item->discount_type,
        'current_stock' => $item->current_stock,
        'details' => $item->details,
        'thumbnail' => 'thumbnail/' . $item->thumbnail

        ];
        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    public function barcode(Request $request, $id)
    {
        if ($request->limit > 270) {
        Toastr::warning(translate('You can not generate more than 270 barcode'));
        return back();
        }
        $product = Product::findOrFail($id);
        $range_data = range(1, $request->limit ?? 4);
        $array_chunk = array_chunk($range_data, 24);

        return view('seller-views.product.barcode', compact('product', 'array_chunk'));
        }

        public function save_product(Request $request)
        {
        // dd($request);
        DB::table('category_request')->insert([
        'seller_id'=>auth('seller')->id(),
        'name'=>$request->name,
        'hsn_code'=>$request->hsn_code,
        'created_at'=>now(),
        'updated_at'=>now()
        ]);

        return response()->json([
        'message' => 'Product added successfully!',
        ]);

    }
}