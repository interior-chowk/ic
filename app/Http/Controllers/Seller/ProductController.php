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
 //  dd($state_city);

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

        // Generate city options HTML dynamically
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

public function getCities($stateId){
    // echo $stateId;
    // die;
    $cities = DB::table('state_city')->where('parent_id', $stateId)->pluck('name');
        return response()->json($cities);
}

public function image_get(){
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
            // 'name'                  => 'required',
            // 'category_id'           => 'required',
            //  'HSN_code'             => 'required',
            // 'Return_days'           => 'required',
            // 'Replacement_days'       => 'required',
            // 'product_type'          => 'required',
            // 'digital_product_type'  => 'required_if:product_type,==,digital',
            // 'digital_file_ready'    => 'required_if:digital_product_type,==,ready_product|mimes:jpg,jpeg,png,gif,zip,pdf',
            // 'unit'                  => 'required_if:product_type,==,physical',
            // 'image'                 => 'required',
            // 'tax'                   => 'required|min:0',
            // 'unit_price'            => 'required|numeric|gt:0',
            // 'discount'              => 'required|gt:-1',
            // 'code'                  => 'required|string|min:2|max:100|unique:products',
            // 'minimum_order_qty'     => 'required|numeric|min:1',
           

        ], [
            // 'name.required'                     => 'Product name is required!',
            // 'category_id.required'              => 'category  is required!',
            // 'image.required'                    => 'Product thumbnail is required!',
            // 'unit.required_if'                  => 'Unit is required!',
            // 'code.min'                          => 'The code must be at least 2 characters long!',
            // 'code.max'               => 'The code must not exceed 100 characters!',
            // 'minimum_order_qty.required'        => 'The minimum order quantity is required!',
            // 'minimum_order_qty.min'             => 'The minimum order quantity must be positive!',
            // 'digital_file_ready.required_if'    => 'Ready product upload is required!',
            // 'digital_file_ready.mimes'          => 'Ready product upload must be a file of type: pdf, zip, jpg, jpeg, png, gif.',
            // 'digital_product_type.required_if'  => 'Digital product type is required!',

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
        // Remove extra spaces and new lines
         $select_location = array_map('trim', explode(',', $select_location));
       //  dd(implode(', ', $select_location));
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
                if($request->file($img)){
                    $image_name = ImageManager::upload('product/', 'png', $request->file($img));
                    $product_images[] = $image_name;
                    $color_image_serial[] = [
                        'color'=>$color_,
                        'image_name'=>$image_name,
                    ];
                }
            }

            // if(count($product_images) != count($request->colors)) {
            //     $validator->after(function ($validator) {
            //         $validator->errors()->add(
            //             'images', 'Color images is required!'
            //         );
            //     });
            // }
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
        //die;
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
                //$item['options'] = explode(',', implode('|', $request[$str]));
                array_push($choice_options, $item);
            }
        }
        $product->choice_options = $request->product_type == 'physical' ? json_encode($choice_options) : json_encode([]);
        //combinations start
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

        //Generates the combinations of customer choice options
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
       
        //combinations end
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
        $product->thumbnail = ImageManager::uploadss('pdfs/', 'webp', $request->file('pdf')); 
          //dd($product->thumbnail);
        $product->length    = $request->length ?? 0;
        $product->breadth   = $request->breadth ?? 0;
        $product->height    = $request->height ?? 0;
        $product->weight    = $request->weight ?? 0;
   
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
          
            $product->save();
            
            $product_id = $product->id;
           //feature
 
          foreach ($request->sizes as $index => $skus) {
    $sizeParts = explode('-', $skus);
    $size = end($sizeParts);

    // $imageNames = [];
    // $thumbnailImage = $request->input('thumbnail_image_' . $index);
    // $imageOrderJson = $request->input('image_order_' . $index);
    // $imageOrder = $imageOrderJson ? json_decode($imageOrderJson, true) : [];

    // if ($request->hasFile('image_' . $index)) {
    //     $files = $request->file('image_' . $index);
    //     $storedFiles = [];

    //     foreach ($files as $file) {
    //         if ($file->isValid()) {
    //             $path = $file->store('images', 'public');
    //             $storedFiles[$file->getClientOriginalName()] = basename($path);

    //             if ($thumbnailImage && $file->getClientOriginalName() == $thumbnailImage) {
    //                 $thumbnailImage = basename($path);
    //             }
    //         }
    //     }

    //     foreach ($imageOrder as $originalName) {
    //         if (isset($storedFiles[$originalName])) {
    //             $imageNames[] = $storedFiles[$originalName];
    //         }
    //     }
    // }

    // $imageJson = !empty($imageNames) ? json_encode($imageNames) : null;


    $imageNames = [];
    $thumbnailImage = $request->input('thumbnail_image_' . $index);
    $imageOrderJson = $request->input('image_order_' . $index);
    $imageOrder = $imageOrderJson ? json_decode($imageOrderJson, true) : [];

    if ($request->hasFile('image_' . $index)) {
        $files = $request->file('image_' . $index);
        $storedFiles = [];

    foreach ($files as $file) {
        if ($file->isValid()) {
            // Create Intervention Image instance
            $img = Image::make($file->getRealPath())->encode('webp', 90); // 90% quality

            // Reduce size if above 80 KB
            $quality = 90;
            while (strlen($img) > 80 * 1024 && $quality > 10) {
                $quality -= 5;
                $img = Image::make($file->getRealPath())->encode('webp', $quality);
            }

            // Generate unique file name
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = \Str::slug($fileName) . '-' . uniqid() . '.webp';

            // Store in public storage
            $savePath = storage_path('app/public/images/' . $safeName);
            $img->save($savePath);

            $storedFiles[$file->getClientOriginalName()] = $safeName;

            // Update thumbnail name if matched
            if ($thumbnailImage && $file->getClientOriginalName() == $thumbnailImage) {
                $thumbnailImage = $safeName;
            }
        }
    }

    // Preserve the order from $imageOrder
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

        
        
      //  dd($skuProduct);



//dd('helo');

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
        
        
          
        


         // Features handling

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

    /**
     * Product total stock report export by excel
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function stock_limit_export(Request $request){

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
        if ($stock_count >= 0) {
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
            'view' => view('admin-views.product.partials._sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name','sub_category'))->render(),
        ]);
    }


    public function sku_combination_edit(Request $request)
    {
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
      
        return response()->json([
            'view' => view('admin-views.product.partials._sku11_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name','product_id'))->render(),
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

       
      // dd($request->all());
        $product = Product::find($id);
       
        //'purchase_price'        => 'required|numeric|gt:0',
        // $validator = Validator::make($request->all(), [
        //     'name'                  => 'required',
        //     'category_id'           => 'required',
        //     'product_type'          => 'required',
        //     'digital_product_type'  => 'required_if:product_type,==,digital',
        //     'digital_file_ready'    => 'mimes:jpg,jpeg,png,gif,zip,pdf',
        //     'unit'                  => 'required_if:product_type,==,physical',
        //     'tax'                   => 'required|min:0',
        //     'unit_price'            => 'required|numeric|gt:0',
        //     'discount'              => 'required|gt:-1',
        //     'code'                  => 'required|string|min:2|max:100|unique:products,code,'.$product->id,
        //     'minimum_order_qty'     => 'required|numeric|min:1',
        //     'length'               => 'required|numeric|min:0',
        //     'breadth'              => 'required|numeric|min:0',
        //     'height'               => 'required|numeric|min:0',
        //     'weight'               => 'required|numeric|min:0',
        // ], [
        //     'name.required'                     => 'Product name is required!',
        //     'category_id.required'              => 'Category is required!',
        //     'unit.required_if'                  => 'Unit is required!',
        //     'code.min'                          => 'The code must be at least 2 characters long!',
        //     'code.max'                          => 'The code must not exceed 100 characters!',
        //     'minimum_order_qty.required'        => 'Minimum order quantity is required!',
        //     'minimum_order_qty.min'             => 'Minimum order quantity must be positive!',
        //     'digital_file_ready.mimes'          => 'Ready product upload must be a file of type: pdf, zip, jpg, jpeg, png, gif.',
        //     'digital_product_type.required_if'  => 'Digital product type is required!',

        // ]);

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
       
        // if ($request['unit_price'] <= $dis) {
        //     $validator->after(function ($validator) {
        //         $validator->errors()->add('unit_price', 'Discount can not be more or equal to the price!');
        //     });
        // }
       
        // if (is_null($request->name[array_search('en', $request->lang)])) {
        //     $validator->after(function ($validator) {
        //         $validator->errors()->add(
        //             'name', 'Name field is required!'
        //         );
        //     });
        // }

       
      
        
        
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
      //  $product->available_instant_delivery      = $request->available_instant_delivery=='on'?1:0;

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
               // $item['options'] = explode(',', implode('|', $request[$str]));
                array_push($choice_options, $item);
            }
        }
      
       
        $product->choice_options = $request->product_type == 'physical' ? json_encode($choice_options) : json_encode([]);
        $variations = [];
        //combinations start
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
        //Generates the combinations of customer choice options
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
        //$product->tax               = $request->tax;
        $product->tax_model         = $request->tax_model ?? 'include';
        $product->code              = $request->code;
        $product->minimum_order_qty = $request->minimum_order_qty;
        // $product->tax_type          = $request->tax_type;
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
           
//19/03/2025

foreach ($request->sizes as $index => $skus) {
    $imageNames = [];

    // Get thumbnail input name
    $thumbnailImage = $request->input('thumbnail_image_' . $index);

    // Get image order from hidden input
    $imageOrder = json_decode($request->input('image_order_' . $index, '[]'), true);

    // Store new images in the correct order
    // if ($request->hasFile('image_' . $index)) {
    //     $files = $request->file('image_' . $index);
    //     $fileMap = [];

    //     foreach ($files as $file) {
    //         $fileMap[$file->getClientOriginalName()] = $file;
    //     }

    //     // Sort and store according to client-side order
    //     foreach ($imageOrder as $originalName) {
    //         if (isset($fileMap[$originalName]) && $fileMap[$originalName]->isValid()) {
    //             $path = $fileMap[$originalName]->store('images', 'public');
    //             $imageName = basename($path);
    //             $imageNames[] = $imageName;

    //             // Set thumbnail if this is the selected one
    //             if ($thumbnailImage == $originalName) {
    //                 $thumbnailImage = $imageName;
    //             }
    //         }
    //     }
    // }

    // // Get old images
    // $oldImages = $request->input('old_image_' . $index, []);
    // $finalImages = array_merge($oldImages, $imageNames);
    // $imageJson = !empty($finalImages) ? json_encode($finalImages) : null;

            if ($request->hasFile('image_' . $index)) {
            $files = $request->file('image_' . $index);
            $fileMap = [];

            // Map original names to file objects
            foreach ($files as $file) {
                $fileMap[$file->getClientOriginalName()] = $file;
            }

            // Sort & store according to client-side order
            foreach ($imageOrder as $originalName) {
                if (isset($fileMap[$originalName]) && $fileMap[$originalName]->isValid()) {

                    // Create Intervention Image instance
                    $img = Image::make($fileMap[$originalName]->getRealPath())->encode('webp', 90);

                    // Reduce size if above 80 KB
                    $quality = 90;
                    while (strlen($img) > 80 * 1024 && $quality > 10) {
                        $quality -= 5;
                        $img = Image::make($fileMap[$originalName]->getRealPath())->encode('webp', $quality);
                    }

                    // Generate safe unique filename
                    $fileBaseName = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeName = \Str::slug($fileBaseName) . '-' . uniqid() . '.webp';

                    // Save to storage/images
                    $savePath = storage_path('app/public/images/' . $safeName);
                    if (!is_dir(dirname($savePath))) {
                        mkdir(dirname($savePath), 0755, true);
                    }
                    $img->save($savePath);

                    // Add to final list
                    $imageNames[] = $safeName;

                    // Set thumbnail if this was the selected one
                    if ($thumbnailImage == $originalName) {
                        $thumbnailImage = $safeName;
                    }
                }
            }
        }

        // Merge with old images
        $oldImages = $request->input('old_image_' . $index, []);
        $finalImages = array_merge($oldImages, $imageNames);
        $imageJson = !empty($finalImages) ? json_encode($finalImages) : null;


    // Prepare SKU data
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
        'listed_gst_percent' => $request->tax1_gst[$index] ?? null,
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

    // Update SKU record in DB
    DB::table('sku_product_new')
        ->where('sizes', $skus)
        ->where('sku', $request->skues[$index])
        ->where('product_id', $id)
        ->update($skuProduct);
}
            
    //dd('helo');
    
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
        $reviews = Review::where(['product_id' => $id])->paginate(Helpers::pagination_limit());
        return view('seller-views.product.view', compact('product', 'reviews'));
    }

    public function remove_image(Request $request)
    {
        ImageManager::delete('/product/' . $request['image']);
        $product = Product::find($request['id']);
        $array = [];
        if (count(json_decode($product['images'])) < 2) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }
        $colors = json_decode($product['colors']);
        $color_image = json_decode($product['color_image']);
        $color_image_arr = [];
        if($colors && $color_image){
            foreach($color_image as $img){
                if($img->color != $request->color && $img->image_name != $request->name){
                    $color_image_arr[] = [
                        'color' =>$img->color!=null ? $img->color:null,
                        'image_name' =>$img->image_name,
                    ];
                }
            }
        }

        foreach (json_decode($product['images']) as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
            'color_image' => json_encode($color_image_arr),
        ]);
        Toastr::success('Product image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Product::find($id);
        Cart::where('product_id', $product->id)->delete();
        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/product/' . $image);
        }
        ImageManager::delete('/product/thumbnail/' . $product['thumbnail']);
        $product->delete();
        FlashDealProduct::where(['product_id' => $id])->delete();
        DealOfTheDay::where(['product_id' => $id])->delete();
        Toastr::success('Product removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('seller-views.product.bulk-import');
    }

      public function search_bulk_import_index(){
        $cat = Category::where(['position' => 0, 'home_status' => 1])->orderBy('name')->get();
        $br = Brand::active()->orderBY('name', 'ASC')->get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        return view('seller-views.product.search_bulk_import_index', compact('cat', 'br', 'brand_setting', 'digital_product_setting'));

      }

      public function search_bulk_import_category(Request $request)
{
    // Session data set karna
    session([
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'sub_sub_category_id' => $request->sub_sub_category_id,
        'brand_id' => $request->brand_id,
    'tax'=>$request->tax,
    'hsn_code'=>$request->hsn_code,
    'procurement_time'=>$request->procurement_time,
    ]);

    // Session se data retrieve karke view ko pass karna
    $category_id = session('category_id');
    $sub_category_id = session('sub_category_id');
    $sub_sub_category_id = session('sub_sub_category_id');
    $brand_id = session('brand_id');
     $tax = session('tax');
     $procurement_time = session('procurement_time');
     $hsn_code = session('hsn_code');
    // View return karna
    return view('seller-views.product.bulk-import', compact('category_id', 'sub_category_id', 'sub_sub_category_id','brand_id','tax','procurement_time','hsn_code'));
}


// public function bulk_import_data(Request $request)
// {
//     $category_id = session('category_id');
//     $sub_category_id = session('sub_category_id');
//     $sub_sub_category_id = session('sub_sub_category_id');
//     $brand_id = session('brand_id');
//     $tax1 = session('tax');
//     $hsn_code = session('hsn_code');
//     $procurement_time = session('procurement_time');

//     try {
//         $collections = (new FastExcel)->import($request->file('products_file'));
//     } catch (\Exception $exception) {
//         Toastr::error('You have uploaded a wrong format file, please upload the right file.');
//         return back();
//     }

//     $col_key = ['name','Category','Sub Category','Sub Sub Category','Brand','procurement_time','Warehouse','pdf','Tax','unit','min_qty', 'discount', 'discount_type', 'details', 'hsn_code', 'return_days', 'sku', 'free_delivery','length', 'breadth', 'height', 'weight', 'youtube_video_url', 'tags', 'replacement_days', 'colors', 'attributes', 'variant_mrp', 'rename_color_name', 'thumbnail_image', 'image', 'commission_fee','commission_type', 'quantity'];
//     $dynamic_prefixes = ['Specification:', 'Key Feature:', 'Technical:', 'Other:'];
//     $skip = ['youtube_video_url', 'thumbnail','thumbnail_image'];
//     $countRow = 0;



//     $all_colors = [];
//     $all_attributes = [];
    
//     foreach ($collections as $collection) {
//         // Colors
//         $colors = is_array($collection['colors']) 
//             ? $collection['colors'] 
//             : explode(',', $collection['colors']);
    
//         $colors = array_map('trim', array_map('strtolower', $colors));
//         $all_colors = array_merge($all_colors, $colors);
    
//         // Attributes
//         $attributes = is_array($collection['attributes']) 
//             ? $collection['attributes'] 
//             : explode(',', $collection['attributes']);
    
//         $attributes = array_map('trim', array_map('strtolower', $attributes));
//         $all_attributes = array_merge($all_attributes, $attributes);
//     }
    
//     // Remove duplicates
//     $all_colors = array_unique($all_colors);
//     $all_attributes = array_unique($all_attributes);
    
   
    
//    // dd($all_colors);
    

//     foreach ($collections as $collection) {
//         foreach ($collection as $key => $value) {
//             if ($key != "" && !in_array($key, $col_key) && !Str::startsWith($key, $dynamic_prefixes)) {
//                 Toastr::error('Please upload the correct format file.');
//                 return back();
//             }

//             if ($key != "" && $value === "" && !in_array($key, $skip)) {
//                 Toastr::error('Please fill ' . $key . ' fields');
//                 return back();
//             }
//         }

//         $thumbnail = $collection['thumbnail'] ?? '';
//         $video_link = $collection['youtube_video_url'] ?? '';

//         $tax = (($tax1 + 100) / 100);
//         $gst_percent = ($collection['variant_mrp'] / $tax);
//         $discount_percent = (($gst_percent * $tax1) / 100);

//         if ($collection['discount_type'] == 'percent') {
//             $listed_price_1 = (($collection['variant_mrp'] * $collection['discount']) / 100);
//             $listed_price = $collection['variant_mrp'] - $listed_price_1;
//         } else {
//             $listed_price = $collection['variant_mrp'] - $collection['discount'];
//         }

//         $listed_percent = ($listed_price / $tax);
//         $listed_gst_percent = (($listed_percent * $tax1) / 100);

//         if ($collection['commission_type'] == 'Transfer Price') {
//             $commission_fee = (($listed_percent - $collection['commission_fee']) / ($listed_percent)) * 100;
//         } else {
//             $commission_fee = $collection['commission_fee'];
//         }

//         // Create variation JSON from colors and attributes
//         $colors = is_array($collection['colors']) ? $collection['colors'] : explode(",", $collection['colors']);
//         $attributes = is_array($collection['attributes']) ? $collection['attributes'] : explode(",", $collection['attributes']);
//         $variations = [];

//         foreach ($all_colors as $color) {
//             foreach ($all_attributes as $attr) {
//                 $variations[] = [
//                     "type" => trim($color) . '-' . trim($attr),
//                     "price" => 0,
//                     "sku" => null,
//                     "qty" => 0
//                 ];
//             }
//         }
//        // dd($variations);
//         $colorCodes = [];

//         foreach ($all_colors as $color) {
//                $col = DB::table('colors')->where('name',$color)->first();

//             $codes = explode(',', $col->code);
//             $colorCodes = array_merge($colorCodes, $codes);
//         }
//         $colorCodes = array_unique(array_map('trim', $colorCodes));
//       // dd($colorCodes);
//         // Dynamically create the choice_option field
//         $choice_option = [];
//         if (!empty($all_attributes)) {
//             $choice_option[] = [
//                 'name' => 'choice_1',
//                 'title' => 'Size',
//                 'options' => $all_attributes  // attributes as options
//             ];
//         }
//           // dd($choice_option);

//          $warehouse = DB::table('warehouse')->where('city',$collection['Warehouse'])->first();
         

//         $productData = [
//             'name' => $collection['name'],
//             'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
//             'category_ids' => json_encode([
//                 ['id' => (string)$category_id, 'position' => 1],
//                 ['id' => (string)$sub_category_id, 'position' => 2],
//                 ['id' => (string)$sub_sub_category_id, 'position' => 3]
//             ]),
//             'category_id' => $category_id,
//             'sub_category_id' => $sub_category_id,
//             'sub_sub_category_id' => $sub_sub_category_id,
//             'brand_id' => $brand_id,
//             'unit' => $collection['unit'],
//             'min_qty' => $collection['min_qty'],
//             'tax' => $tax,
//             'add_warehouse'=>$warehouse->id,
//             'tax_model' => 'include',
//             'discount' => $collection['discount'],
//             'discount_type' => $collection['discount_type'],
//             'tax_type' => $collection['discount_type'],
//             'details' => $collection['details'],
//             'video_provider' => 'youtube',
//             'video_url' => $video_link,
//             'images' => json_encode([]),
//             'status' => 0,
//             'HSN_code' => $hsn_code,
//             'Return_days' => $collection['return_days'],
//             'Replacement_days' => $collection['replacement_days'],
//             'code' => $collection['sku'],
//             'free_delivery' => $collection['free_delivery'],
//             'length' => $collection['length'],
//             'breadth' => $collection['breadth'],
//             'height' => $collection['height'],
//             'weight' => $collection['weight'],
//             'product_type' => 'physical',
//             'shipping_cost' => 0,
//             'multiply_qty' => 0,
//             'colors' => json_encode($colorCodes),
//             'attributes' => json_encode(["1"]),
//             'choice_options' => json_encode($choice_option),  // Add dynamic choice_option here
//             'variation' => json_encode($variations),
//             'featured_status' => 0,
//             'added_by' => 'seller',
//             'user_id' => auth('seller')->id(),
//             'created_at' => now(),
//             'updated_at' => now(),
//         ];

//         $existingProduct = DB::table('products')
//             ->where('name', $collection['name'])
//             ->first();

//         if ($existingProduct) {
//             DB::table('products')->where('id', $existingProduct->id)->update($productData);
//             $product_id = $existingProduct->id;
//         } else {
//             $product_id = DB::table('products')->insertGetId($productData);
//         }

//         // Tags
//         $tags = is_array($collection['tags']) ? $collection['tags'] : explode(",", $collection['tags']);
//         $tags = array_map('trim', $tags);
//         $tag_ids = [];

//         foreach ($tags as $tagValue) {
//             $tag = Tag::firstOrNew(['tag' => $tagValue]);
//             $tag->save();
//             $tag_ids[] = $tag->id;
//         }

//         $product = Product::find($product_id);
//         $product->tags()->sync($tag_ids);

//         // SKU insert
//         $insertData = [];
//         foreach ($colors as $color) {
//             $insertData[] = [
//                 'seller_id' => auth('seller')->id(),
//                 'product_id' => $product_id,
//                 'sizes' => $collection['attributes'],
//                 'variation' => trim($color) . '-' . trim($collection['attributes']),
//                 'tax' => $tax1,
//                 'discount_percent' => $discount_percent,
//                 'gst_percent' => $gst_percent,
//                 'listed_price' => $listed_price,
//                 'listed_percent' => $listed_percent,
//                 'listed_gst_percent' => $listed_gst_percent,
//                 'sku' => $collection['sku'],
//                 'discount' => $collection['discount'],
//                 'discount_type' => $collection['discount_type'],
//                 'length' => $collection['length'],
//                 'breadth' => $collection['breadth'],
//                 'height' => $collection['height'],
//                 'weight' => $collection['weight'],
//                 'variant_mrp' => $collection['variant_mrp'],
//                 'color_name' => $collection['rename_color_name'],
//                 'thumbnail_image' => $collection['thumbnail_image'],
//                 'image' => json_encode(explode(',', $collection['image'] ?? '')),
//                 'commission_fee' => $commission_fee,
//                 'quantity' => $collection['quantity']
//             ];
//         }

//         DB::table('sku_product_new')->insert($insertData);

//         // Specifications
//         $specification_values = [];
//         $features_values = [];
//         $technical_specification_values = [];
//         $other_details_values = [];

//         foreach ($collection as $key => $value) {
//             if (stripos($key, 'Specification:') === 0) {
//                 $specification_values[] = $value;
//             } elseif (stripos($key, 'Key Feature:') === 0) {
//                 $features_values[] = $value;
//             } elseif (stripos($key, 'Technical:') === 0) {
//                 $technical_specification_values[] = $value;
//             } elseif (stripos($key, 'Other:') === 0) {
//                 $other_details_values[] = $value;
//             }
//         }

//         $post = [
//             'seller_id' => auth('seller')->id() ?? null,
//             'product_id' => $product_id,
//             'specification' => json_encode($specification_values),
//             'key_features' => json_encode($features_values),
//             'technical_specification' => json_encode($technical_specification_values),
//             'other_details' => json_encode($other_details_values),
//             'created_at' => now(),
//             'updated_at' => now()
//         ];

//         DB::table('key_specification_values')->updateOrInsert(
//             ['product_id' => $product_id],
//             $post
//         );

//         $countRow++;
//     }

//     Toastr::success($countRow . ' - Products imported successfully!');
//     return back();
// }


    // public function bulk_import_data(Request $request)
    // {
    //     $category_id = session('category_id');
    //     $sub_category_id = session('sub_category_id');
    //     $sub_sub_category_id = session('sub_sub_category_id');
    //     $brand_id = session('brand_id');
    //     $tax1 = session('tax');
    //     $hsn_code = session('hsn_code');
    //     $procurement_time = session('procurement_time');

    //     try {
    //         $collections = (new FastExcel)->import($request->file('products_file'));
    //     } catch (\Exception $exception) {
    //         Toastr::error('You have uploaded a wrong format file, please upload the right file.');
    //         return back();
    //     }

    //     $col_key = ['name','Category','Sub Category','Sub Sub Category','Brand','procurement_time','Warehouse','pdf','Tax','unit','min_qty', 'discount', 'discount_type', 'details', 'hsn_code', 'return_days', 'sku', 'free_delivery','length', 'breadth', 'height', 'weight', 'youtube_video_url', 'tags', 'replacement_days', 'colors', 'attributes', 'variant_mrp', 'rename_color_name', 'thumbnail_image', 'image', 'commission_fee','commission_type', 'quantity'];
    //     $dynamic_prefixes = ['Specification:', 'Key Feature:', 'Technical:', 'Other:'];
    //     $skip = ['youtube_video_url', 'thumbnail','thumbnail_image'];
    //     $countRow = 0;



    //     $all_colors = [];
    //     $all_attributes = [];
        
    //     foreach ($collections as $collection) {
    //         // Colors
    //         $colors = is_array($collection['colors']) 
    //             ? $collection['colors'] 
    //             : explode(',', $collection['colors']);
        
    //         $colors = array_map('trim', array_map('strtolower', $colors));
    //         $all_colors = array_merge($all_colors, $colors);
        
    //         // Attributes
    //         $attributes = is_array($collection['attributes']) 
    //             ? $collection['attributes'] 
    //             : explode(',', $collection['attributes']);
        
    //         $attributes = array_map('trim', array_map('strtolower', $attributes));
    //         $all_attributes = array_merge($all_attributes, $attributes);
    //     }
        
    //     // Remove duplicates
    //     $all_colors = array_unique($all_colors);
    //     $all_attributes = array_unique($all_attributes);
    //     // dd($collections);

    //     foreach ($collections as $collection) {
    //         // dd($collection);
    //         // dd(json_encode(explode(",", $collection['image'] ?? '')));
    //         foreach ($collection as $key => $value) {
    //             if ($key != "" && !in_array($key, $col_key) && !Str::startsWith($key, $dynamic_prefixes)) {
    //                 Toastr::error('Please upload the correct format file.');
    //                 return back();
    //             }

    //             if ($key != "" && $value === "" && !in_array($key, $skip)) {
    //                 Toastr::error('Please fill ' . $key . ' fields');
    //                 return back();
    //             }
    //         }

    //         $thumbnail = $collection['thumbnail'] ?? '';
    //         $video_link = $collection['youtube_video_url'] ?? '';

    //         $tax = (($tax1 + 100) / 100);
    //         $gst_percent = ($collection['variant_mrp'] / $tax);
    //         $discount_percent = (($gst_percent * $tax1) / 100);

    //         if ($collection['discount_type'] == 'percent') {
    //             $listed_price_1 = (($collection['variant_mrp'] * $collection['discount']) / 100);
    //             $listed_price = $collection['variant_mrp'] - $listed_price_1;
    //         } else {
    //             $listed_price = $collection['variant_mrp'] - $collection['discount'];
    //         }

    //         $listed_percent = ($listed_price / $tax);
    //         $listed_gst_percent = (($listed_percent * $tax1) / 100);

    //         if ($collection['commission_type'] == 'Transfer Price') {
    //             $commission_fee = (($listed_percent - $collection['commission_fee']) / ($listed_percent)) * 100;
    //         } else {
    //             $commission_fee = $collection['commission_fee'];
    //         }

    //         // Create variation JSON from colors and attributes
    //         $colors = is_array($collection['colors']) ? $collection['colors'] : explode(",", $collection['colors']);
    //         $attributes = is_array($collection['attributes']) ? $collection['attributes'] : explode(",", $collection['attributes']);
    //         $variations = [];

    //         foreach ($all_colors as $color) {
    //             foreach ($all_attributes as $attr) {
    //                 $variations[] = [
    //                     "type" => trim($color) . '-' . trim($attr),
    //                     "price" => 0,
    //                     "sku" => null,
    //                     "qty" => 0
    //                 ];
    //             }
    //         }
    //         $colorCodes = [];

    //         foreach ($all_colors as $color) {
    //             $col = DB::table('colors')->where('name',$color)->first();

    //             $codes = explode(',', $col->code);
    //             $colorCodes = array_merge($colorCodes, $codes);
    //         }
    //         $colorCodes = array_unique(array_map('trim', $colorCodes));
        
    //         $choice_option = [];
    //         if (!empty($all_attributes)) {
    //             $choice_option[] = [
    //                 'name' => 'choice_1',
    //                 'title' => 'Size',
    //                 'options' => $all_attributes  // attributes as options
    //             ];
    //         }
    //         // dd($choice_option);

    //         $warehouse = DB::table('warehouse')->where('city',$collection['Warehouse'])->first();
            
    //         // dd(json_encode(explode(",", $collection['image'] ?? '')));
    //         dd($collection['thumbnail']);
    //         $productData = [
    //             'name' => $collection['name'],
    //             'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
    //             'category_ids' => json_encode([
    //                 ['id' => (string)$category_id, 'position' => 1],
    //                 ['id' => (string)$sub_category_id, 'position' => 2],
    //                 ['id' => (string)$sub_sub_category_id, 'position' => 3]
    //             ]),
    //             'category_id' => $category_id,
    //             'sub_category_id' => $sub_category_id,
    //             'sub_sub_category_id' => $sub_sub_category_id,
    //             'brand_id' => $brand_id,
    //             'unit' => $collection['unit'],
    //             'min_qty' => $collection['min_qty'],
    //             'tax' => $tax,
    //             'add_warehouse' => $warehouse->id ?? null,
    //             'tax_model' => 'include',
    //             'discount' => $collection['discount'],
    //             'discount_type' => $collection['discount_type'],
    //             'tax_type' => $collection['discount_type'],
    //             'details' => $collection['details'],
    //             'video_provider' => 'youtube',
    //             'video_url' => $video_link,
    //             'images' => json_encode(explode(",", $collection['image'] ?? '')),
    //             'thumbnail' => $collection['thumbnail'],
    //             'status' => 0,
    //             'HSN_code' => $hsn_code,
    //             'Return_days' => $collection['return_days'],
    //             'Replacement_days' => $collection['replacement_days'],
    //             'code' => $collection['sku'],
    //             'free_delivery' => $collection['free_delivery'],
    //             'length' => $collection['length'],
    //             'breadth' => $collection['breadth'],
    //             'height' => $collection['height'],
    //             'weight' => $collection['weight'],
    //             'product_type' => 'physical',
    //             'shipping_cost' => 0,
    //             'multiply_qty' => 0,
    //             'colors' => json_encode($colorCodes),
    //             'attributes' => json_encode(["1"]),
    //             'choice_options' => json_encode($choice_option),
    //             'variation' => json_encode($variations),
    //             'featured_status' => 0,
    //             'added_by' => 'seller',
    //             'user_id' => auth('seller')->id(),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];

    //         $existingProduct = DB::table('products')
    //             ->where('name', $collection['name'])
    //             ->first();

    //             // dd($productData);
    //         if ($existingProduct) {
    //             Product::where('id', $existingProduct->id)->update($productData);
    //             $product_id = $existingProduct->id;
    //         } else {
    //             $product_id = DB::table('products')->insertGetId($productData);
    //         }

    //         // Tags
    //         $tags = is_array($collection['tags']) ? $collection['tags'] : explode(",", $collection['tags']);
    //         $tags = array_map('trim', $tags);
    //         $tag_ids = [];

    //         foreach ($tags as $tagValue) {
    //             $tag = Tag::firstOrNew(['tag' => $tagValue]);
    //             $tag->save();
    //             $tag_ids[] = $tag->id;
    //         }

    //         $product = Product::find($product_id);
    //         $product->tags()->sync($tag_ids);

    //         // SKU insert
    //         $insertData = [];
    //         foreach ($colors as $color) {
    //             $insertData[] = [
    //                 'seller_id' => auth('seller')->id(),
    //                 'product_id' => $product_id,
    //                 'sizes' => $collection['attributes'],
    //                 'variation' => trim($color) . '-' . trim($collection['attributes']),
    //                 'tax' => $tax1,
    //                 'discount_percent' => $discount_percent,
    //                 'gst_percent' => $gst_percent,
    //                 'listed_price' => $listed_price,
    //                 'listed_percent' => $listed_percent,
    //                 'listed_gst_percent' => $listed_gst_percent,
    //                 'sku' => $collection['sku'],
    //                 'discount' => $collection['discount'],
    //                 'discount_type' => $collection['discount_type'],
    //                 'length' => $collection['length'],
    //                 'breadth' => $collection['breadth'],
    //                 'height' => $collection['height'],
    //                 'weight' => $collection['weight'],
    //                 'variant_mrp' => $collection['variant_mrp'],
    //                 'color_name' => $collection['rename_color_name'],
    //                 'thumbnail_image' => $collection['thumbnail_image'],
    //                 'image' => json_encode(explode(',', $collection['image'] ?? '')),
    //                 'commission_fee' => $commission_fee,
    //                 'quantity' => $collection['quantity']
    //             ];
    //         }

    //         DB::table('sku_product_new')->insert($insertData);

    //         // Specifications
    //         $specification_values = [];
    //         $features_values = [];
    //         $technical_specification_values = [];
    //         $other_details_values = [];

    //         foreach ($collection as $key => $value) {
    //             if (stripos($key, 'Specification:') === 0) {
    //                 $specification_values[] = $value;
    //             } elseif (stripos($key, 'Key Feature:') === 0) {
    //                 $features_values[] = $value;
    //             } elseif (stripos($key, 'Technical:') === 0) {
    //                 $technical_specification_values[] = $value;
    //             } elseif (stripos($key, 'Other:') === 0) {
    //                 $other_details_values[] = $value;
    //             }
    //         }

    //         $post = [
    //             'seller_id' => auth('seller')->id() ?? null,
    //             'product_id' => $product_id,
    //             'specification' => json_encode($specification_values),
    //             'key_features' => json_encode($features_values),
    //             'technical_specification' => json_encode($technical_specification_values),
    //             'other_details' => json_encode($other_details_values),
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ];

    //         DB::table('key_specification_values')->updateOrInsert(
    //             ['product_id' => $product_id],
    //             $post
    //         );

    //         $countRow++;
    //     }

    //     Toastr::success($countRow . ' - Products imported successfully!');
    //     return back();
    // }


    public function bulk_import_data(Request $request)
{
    $category_id = session('category_id');
    $sub_category_id = session('sub_category_id');
    $sub_sub_category_id = session('sub_sub_category_id');
    $brand_id = session('brand_id');
    $tax1 = session('tax') ?? 0;
    $hsn_code = session('hsn_code');
    $procurement_time = session('procurement_time');

    try {
        $collections = (new FastExcel)->import($request->file('products_file'));
    } catch (\Exception $exception) {
        Toastr::error('You have uploaded a wrong format file, please upload the right file.');
        return back();
    }

    $col_key = [
        'name','Category','Sub Category','Sub Sub Category','Brand','procurement_time','Warehouse','pdf',
        'Tax','unit','min_qty','discount','discount_type','details','hsn_code','return_days','sku',
        'free_delivery','length','breadth','height','weight','youtube_video_url','tags','replacement_days',
        'colors','attributes','variant_mrp','rename_color_name','thumbnail_image','image','commission_fee',
        'commission_type','quantity'
    ];
    $dynamic_prefixes = ['Specification:', 'Key Feature:', 'Technical:', 'Other:'];
    $skip = ['youtube_video_url', 'thumbnail', 'thumbnail_image'];
    $countRow = 0;

    $all_colors = [];
    $all_attributes = [];

    // Pre-process to collect all unique colors and attributes
    foreach ($collections as $collection) {
        $colors = explode(',', $collection['colors'] ?? '');
        $colors = array_map('trim', array_map('strtolower', $colors));
        $all_colors = array_merge($all_colors, $colors);

        $attributes = explode(',', $collection['attributes'] ?? '');
        $attributes = array_map('trim', array_map('strtolower', $attributes));
        $all_attributes = array_merge($all_attributes, $attributes);
    }

    $all_colors = array_unique(array_filter($all_colors));
    $all_attributes = array_unique(array_filter($all_attributes));

    foreach ($collections as $collection) {

        // Validate columns
        foreach ($collection as $key => $value) {
            if ($key != "" && !in_array($key, $col_key) && !Str::startsWith($key, $dynamic_prefixes)) {
                Toastr::error('Please upload the correct format file.');
                return back();
            }

            if ($key != "" && ($value === "" || $value === null) && !in_array($key, $skip)) {
                Toastr::error('Please fill ' . $key . ' fields');
                return back();
            }
        }

        // Safe access for optional fields
        $thumbnail = $collection['thumbnail'] ?? '';
        $video_link = $collection['youtube_video_url'] ?? '';
        $thumbnail_image = $collection['thumbnail_image'] ?? '';
        $images = explode(',', $collection['image'] ?? '');
        $sku = $collection['sku'] ?? '';
        $variant_mrp = $collection['variant_mrp'] ?? 0;
        $discount = $collection['discount'] ?? 0;
        $discount_type = $collection['discount_type'] ?? 'amount';
        $commission_type = $collection['commission_type'] ?? '';
        $commission_fee_input = $collection['commission_fee'] ?? 0;

        $tax = (($tax1 + 100) / 100);
        $gst_percent = ($variant_mrp / $tax);
        $discount_percent = (($gst_percent * $tax1) / 100);

        // Calculate listed price
        if ($discount_type == 'percent') {
            $listed_price = $variant_mrp - (($variant_mrp * $discount) / 100);
        } else {
            $listed_price = $variant_mrp - $discount;
        }

        $listed_percent = ($listed_price / $tax);
        $listed_gst_percent = (($listed_percent * $tax1) / 100);

        // Commission calculation
        if ($commission_type == 'Transfer Price') {
            $commission_fee = (($listed_percent - $commission_fee_input) / $listed_percent) * 100;
        } else {
            $commission_fee = $commission_fee_input;
        }

        // Prepare variations
        $colors = explode(",", $collection['colors'] ?? '');
        $attributes = explode(",", $collection['attributes'] ?? '');
        $variations = [];

        foreach ($all_colors as $color) {
            foreach ($all_attributes as $attr) {
                $variations[] = [
                    "type" => trim($color) . '-' . trim($attr),
                    "price" => 0,
                    "sku" => null,
                    "qty" => 0
                ];
            }
        }

        // Color codes
        $colorCodes = [];
        foreach ($all_colors as $color) {
            $col = DB::table('colors')->where('name', $color)->first();
            if ($col && isset($col->code)) {
                $codes = explode(',', $col->code);
                $colorCodes = array_merge($colorCodes, $codes);
            }
        }
        $colorCodes = array_unique(array_map('trim', $colorCodes));

        // Choice options
        $choice_option = [];
        if (!empty($all_attributes)) {
            $choice_option[] = [
                'name' => 'choice_1',
                'title' => 'Size',
                'options' => $all_attributes
            ];
        }

        $warehouse = DB::table('warehouse')->where('city', $collection['Warehouse'] ?? '')->first();

        $productData = [
            'name' => $collection['name'],
            'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
            'category_ids' => json_encode([
                ['id' => (string)$category_id, 'position' => 1],
                ['id' => (string)$sub_category_id, 'position' => 2],
                ['id' => (string)$sub_sub_category_id, 'position' => 3]
            ]),
            'category_id' => $category_id,
            'sub_category_id' => $sub_category_id,
            'sub_sub_category_id' => $sub_sub_category_id,
            'brand_id' => $brand_id,
            'unit' => $collection['unit'] ?? '',
            'min_qty' => $collection['min_qty'] ?? 1,
            'tax' => $tax,
            'add_warehouse' => $warehouse->id ?? null,
            'tax_model' => 'include',
            'discount' => $discount,
            'discount_type' => $discount_type,
            'tax_type' => $discount_type,
            'details' => $collection['details'] ?? '',
            'video_provider' => 'youtube',
            'video_url' => $video_link,
            'images' => json_encode($images),
            'thumbnail' => $thumbnail,
            'status' => 0,
            'HSN_code' => $hsn_code,
            'Return_days' => $collection['return_days'] ?? 0,
            'Replacement_days' => $collection['replacement_days'] ?? 0,
            'code' => $sku,
            'free_delivery' => $collection['free_delivery'] ?? 0,
            'length' => $collection['length'] ?? 0,
            'breadth' => $collection['breadth'] ?? 0,
            'height' => $collection['height'] ?? 0,
            'weight' => $collection['weight'] ?? 0,
            'product_type' => 'physical',
            'shipping_cost' => 0,
            'multiply_qty' => 0,
            'colors' => json_encode($colorCodes),
            'attributes' => json_encode(["1"]),
            'choice_options' => json_encode($choice_option),
            'variation' => json_encode($variations),
            'featured_status' => 0,
            'added_by' => 'seller',
            'user_id' => auth('seller')->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert or update product
        $existingProduct = DB::table('products')->where('name', $collection['name'])->first();
        if ($existingProduct) {
            Product::where('id', $existingProduct->id)->update($productData);
            $product_id = $existingProduct->id;
        } else {
            $product_id = DB::table('products')->insertGetId($productData);
        }

        // Tags
        $tags = explode(",", $collection['tags'] ?? '');
        $tags = array_map('trim', $tags);
        $tag_ids = [];
        foreach ($tags as $tagValue) {
            if ($tagValue) {
                $tag = Tag::firstOrNew(['tag' => $tagValue]);
                $tag->save();
                $tag_ids[] = $tag->id;
            }
        }
        $product = Product::find($product_id);
        $product->tags()->sync($tag_ids);

        // SKU insert
        $insertData = [];
        foreach ($colors as $color) {
            $insertData[] = [
                'seller_id' => auth('seller')->id(),
                'product_id' => $product_id,
                'sizes' => $collection['attributes'] ?? '',
                'variation' => trim($color) . '-' . trim($collection['attributes'] ?? ''),
                'tax' => $tax1,
                'discount_percent' => $discount_percent,
                'gst_percent' => $gst_percent,
                'listed_price' => $listed_price,
                'listed_percent' => $listed_percent,
                'listed_gst_percent' => $listed_gst_percent,
                'sku' => $sku,
                'discount' => $discount,
                'discount_type' => $discount_type,
                'length' => $collection['length'] ?? 0,
                'breadth' => $collection['breadth'] ?? 0,
                'height' => $collection['height'] ?? 0,
                'weight' => $collection['weight'] ?? 0,
                'variant_mrp' => $variant_mrp,
                'color_name' => $collection['rename_color_name'] ?? '',
                'thumbnail_image' => $thumbnail_image,
                'image' => json_encode($images),
                'commission_fee' => $commission_fee,
                'quantity' => $collection['quantity'] ?? 0
            ];
        }

        DB::table('sku_product_new')->insert($insertData);

        // Specifications
        $specification_values = [];
        $features_values = [];
        $technical_specification_values = [];
        $other_details_values = [];

        foreach ($collection as $key => $value) {
            if (stripos($key, 'Specification:') === 0) {
                $specification_values[] = $value;
            } elseif (stripos($key, 'Key Feature:') === 0) {
                $features_values[] = $value;
            } elseif (stripos($key, 'Technical:') === 0) {
                $technical_specification_values[] = $value;
            } elseif (stripos($key, 'Other:') === 0) {
                $other_details_values[] = $value;
            }
        }

        DB::table('key_specification_values')->updateOrInsert(
            ['product_id' => $product_id],
            [
                'seller_id' => auth('seller')->id() ?? null,
                'product_id' => $product_id,
                'specification' => json_encode($specification_values),
                'key_features' => json_encode($features_values),
                'technical_specification' => json_encode($technical_specification_values),
                'other_details' => json_encode($other_details_values),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $countRow++;
    }

    Toastr::success($countRow . ' - Products imported successfully!');
    return back();
}







public function bulk_image(){
    return view('seller-views.product.bulk-image');
}


public function bulk_image_import(Request $request)
{
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $webpName = $originalName . '.webp';
            
            // Get the image type
            $imageType = exif_imagetype($image->getPathname());

            // Create image resource from original file
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $imgResource = imagecreatefromjpeg($image->getPathname());
                    break;
                case IMAGETYPE_PNG:
                    $imgResource = imagecreatefrompng($image->getPathname());
                    // Preserve transparency for PNG
                    imagepalettetotruecolor($imgResource);
                    imagealphablending($imgResource, true);
                    imagesavealpha($imgResource, true);
                    break;
                case IMAGETYPE_GIF:
                    $imgResource = imagecreatefromgif($image->getPathname());
                    break;
                default:
                    return back()->with('error', 'Unsupported image type: ' . $image->getClientOriginalName());
            }

            // Save image as WebP to storage
            $storagePath = storage_path('app/public/images/' . $webpName);
            imagewebp($imgResource, $storagePath, 80); // 80 = quality
            imagedestroy($imgResource);
        }

        Toastr::success('All images uploaded successfully.');
        return back();
    }

    return back()->with('error', 'No images found.');
}


////////////////
// public function bulk_export_data_category_wise()
// {
//     $category_id = session('category_id');
//     $sub_category_id = session('sub_category_id');
//     $sub_sub_category_id = session('sub_sub_category_id');
//     $product_id = session('product_id');
//     $brand_id = session('brand_id');
//     $tax = session('tax');
//     $procurement_time = session('procurement_time');
//     $hsn_code = session('hsn_code');
//       $seller_id = auth('seller')->id();
//         $commission_fee = DB::table('sellers')->where('id',$seller_id)->first();
//         if($commission_fee->commission_fee == 1){
//             $commission_type = 'Default';
//         }elseif($commission_fee->commission_fee == 2){
//             $commission_type = 'In Percent';
//         }else{
//             $commission_type = 'Transfer Price';
//         }
//        // dd($commission_fee);
      

//     $category = DB::table('categories')->where('id', $category_id)->first();
//     $product = Product::where('id', $product_id)->first();
//     $sub_category = DB::table('categories')->where('id', $sub_category_id)->first();
//     $sub_sub_category = DB::table('categories')->where('id', $sub_sub_category_id)->first();
//     $brand_name = DB::table('brands')->where('id', $brand_id)->first();

//     $specifications = [];
//     if (!empty($sub_sub_category->specification)) {
//         $specifications = explode(',', $sub_sub_category->specification);
//     }

//     $key_features = [];
//     if (!empty($sub_sub_category->key_features)) {
//         $key_features = explode(',', $sub_sub_category->key_features);
//     }

//     $technical_specifications = [];
//     if (!empty($sub_sub_category->technical_specification)) {
//         $technical_specifications = explode(',', $sub_sub_category->technical_specification);
//     }

//     $other_details = [];
//     if (!empty($sub_sub_category->other_details)) {
//         $other_details = explode(',', $sub_sub_category->other_details);
//     }

//     $colors = DB::table('colors')->pluck('name')->toArray(); // optimized
    

//     $baseData = [

//                     'name'=>'',
//                     'QC Faild Reason (if any)' => $product->qc_failed_reason ?? '',
//                     'Category' => $category->name ?? '',
//                     'Sub Category' => $sub_category->name ?? '',
//                     'Sub Sub Category' => $sub_sub_category->name ?? '',
//                     'Brand' => $brand_name->name ?? '',
//                     'hsn_code'=> $hsn_code,
//                     'procurement_time'=>$procurement_time,           
//                     'unit'=>'',
//                     'sku'=>'',
//                     'Warehouse'=>'',
//                     'pdf'=>'',
//                     'Tax' => $tax,
//                     'discount'=>'',
//                     'discount_type'=>'',
//                     'min_qty'=>'',
//                     'replacement_days'=>'',
//                     'return_days'=>'',
//                     'free_delivery'=>'',
//                     'length'=>'',
//                     'breadth'=>'',
//                     'height'=>'',
//                     'weight'=>'',
//                     'details'=>'',
//                     'youtube_video_url'=>'',
//                     'tags'=>'',
//                     'colors'=>implode(', ', $colors),
//                     'attributes'=>'',
//                     'variant_mrp'=>'',
//                     'rename_color_name'=>'',
//                     'thumbnail_image'=>'',
//                     'image'=>'',
//                     'commission_type'=>$commission_type,
//                     'commission_fee'=>$commission_fee->fee,
//                     'quantity'=>'',

//     ];

//     // Dynamic fields with prefix
//     foreach ($specifications as $spec) {
//         $baseData['Specification: ' . trim($spec)] = '';
//     }
//     foreach ($key_features as $feature) {
//         $baseData['Key Feature: ' . trim($feature)] = '';
//     }
//     foreach ($technical_specifications as $tech) {
//         $baseData['Technical: ' . trim($tech)] = '';
//     }
//     foreach ($other_details as $other) {
//         $baseData['Other: ' . trim($other)] = '';
//     }

//     return (new \Rap2hpoutre\FastExcel\FastExcel(collect([$baseData])))->download('products_bulk.xlsx');
// }


// public function bulk_export_data_category_wise()
// {
//     $category_id = session('category_id');
//     $sub_category_id = session('sub_category_id');
//     $sub_sub_category_id = session('sub_sub_category_id');
//     $product_id = session('product_id');
//     $brand_id = session('brand_id');
//     $tax = session('tax');
//     $procurement_time = session('procurement_time');
//     $hsn_code = session('hsn_code');

//     $seller_id = auth('seller')->id();
//     $commission_fee = DB::table('sellers')->where('id', $seller_id)->first();

//     $commission_type = match ($commission_fee->commission_fee) {
//         1 => 'Default',
//         2 => 'In Percent',
//         default => 'Transfer Price',
//     };

//     $category = DB::table('categories')->where('id', $category_id)->first();
//     $product = Product::where('id', $product_id)->first();
//     $sub_category = DB::table('categories')->where('id', $sub_category_id)->first();
//     $sub_sub_category = DB::table('categories')->where('id', $sub_sub_category_id)->first();
//     $brand_name = DB::table('brands')->where('id', $brand_id)->first();

//     $specifications = !empty($sub_sub_category->specification) ? explode(',', $sub_sub_category->specification) : [];
//     $key_features = !empty($sub_sub_category->key_features) ? explode(',', $sub_sub_category->key_features) : [];
//     $technical_specifications = !empty($sub_sub_category->technical_specification) ? explode(',', $sub_sub_category->technical_specification) : [];
//     $other_details = !empty($sub_sub_category->other_details) ? explode(',', $sub_sub_category->other_details) : [];

//     $colors = DB::table('colors')->pluck('name')->toArray();

//     // --- Base data
//     $baseData = [
//         'InteriorChowk Product Code' => '',
//         'Catelogue QC Status' => '',
//         'QC Faild Reason (if any)' => $product->qc_failed_reason ?? '',
//         'Product Category' => $category->name ?? '',
//         'Product Sub Category' => $sub_category->name ?? '',
//         'Product Sub Sub Category' => $sub_sub_category->name ?? '',
//         'Brands' => $brand_name->name ?? '',
//         'Seller SKU ID' => '',
//         'Commission Type' => $commission_type,
//         'Commission Fee' => $commission_fee->fee ?? '',
//         'Listing Status' => '',
//         'MRP (INR)' => '',
//         'Discount Type' => '',
//         'Discount' => '',
//         'Your selling price (INR)' => '',
//         'Product Title' => '',
//         'Product Description' => '',
//         'Colour' => implode(', ', $colors),
//         'Size' => '',
//     ];

//     foreach ($specifications as $spec) {
//         $baseData['Specification: ' . trim($spec)] = '';
//     }
//     foreach ($key_features as $feature) {
//         $baseData['Key features: ' . trim($feature)] = '';
//     }
//     foreach ($technical_specifications as $tech) {
//         $baseData['Technical specification: ' . trim($tech)] = '';
//     }
//     foreach ($other_details as $other) {
//         $baseData['Other details: ' . trim($other)] = '';
//     }

//     $baseData = array_merge($baseData, [
//         'Return Days' => '',
//         'Replacement Days' => '',
//         'Unit' => '',
//         'Free Delivery' => '',
//         'Self Delivery' => '',
//         'Warehouse' => 'Noida warehouse',
//         'Procurement SLA (DAY)' => $procurement_time,
//         'Stock' => '',
//         'MOQ' => '',
//         'Pakeging Diemensions' => '',
//         'HSN' => $hsn_code,
//         'Tax' => $tax,
//         'Thumbnail Image name' => '',
//         'Other Image name' => '',
//         'Video URL' => '',
//         'PDF URL' => '',
//         'Search Tags' => '',
//         'Excel error status' => '',
//     ]);

//     // --- Instructions (aligned with baseData keys, same count)
//     $instruction1 = array_fill(0, count($baseData), '-');
//     $instruction2 = array_fill(0, count($baseData), '-');

//     // Now manually assign instructions for the known fixed columns
//     $map = [
//         'InteriorChowk Product Code' =>['','To be filled by InteriorChowk'],
//         'Catelogue QC Status' => ['','To be filled by InteriorChowk'],
//         'QC Faild Reason (if any)' => ['','To be filled by InteriorChowk'],
//         'Product Category' => ['','To be filled by InteriorChowk'],
//         'Product Sub Category' => ['','To be filled by InteriorChowk'],
//         'Product Sub Sub Category' => ['','To be filled by InteriorChowk'],
//         'Brands' => ['','To be filled by InteriorChowk'],
//         'Seller SKU ID' => ['Text - limited to 64 characters (including spaces)', 'Seller SKU ID is the identification number maintained by seller to keep track of SKUs. This will be mapped with InteriorChowk product code.'],
//         'Commission Type' => ['','To be filled by InteriorChowk'],
//         'Commission Fee' => ['','To be filled by InteriorChowk'],
//         'Listing Status' => ['Single - Text', 'Inactive listings are not available for buyers on InteriorChowk'],
//         'MRP (INR)' => ['Single - Positive_integer', 'Maximum retail price of the product'],
//         'Discount Type' => ['-', 'Write flat or percent'],
//         'Discount' => ['-', 'In Rs. Or In %'],
//         'Your selling price (INR)' => ['Single - Positive_integer', 'Price at which you want to sell this listing'],
//         'Product Title' => ['Single - Text Used For: Title', 'Product Title is the identity of the product that helps in distinguishing it from other products.'],
//         'Product Description' => ['Single - Text', 'Please write few lines describing your product...'],
//         'Colour' => [['Colour Name','Rename Colour Name'], ['Eg. Silver','Eg. Matt Finish']],
//         'Size' => ['Add size', 'Eg. S, M, L or custom size'],
        // 'Specification' => [['Brand','Product Dimensions','Wattage','Voltage'],['-','-','-','-']],
        // 'Key Features' => [['Manufacturer','Packer','Net Quantity','Included Components'],['-','-','-','-']],
        // 'Technical Specification' => [['A','B','C','D'],['-','-','-','-']],
        // 'Other Details' => [['A','B','C','D'],['-','-','-','-']],
//         'Return Days' => ['Number', '-'],
//         'Replacement Days' => ['Number', 'Enter number of days only if you want to offer replacement only (no return).'],
//         'Unit' => ['-', 'Eg. - Kg, pc, gms, lts, set, Pair, Sqft, Sq Mtr., Box'],
//         'Free Delivery' => ['-', 'Yes / No'],
//         'Self Delivery' => ['-', 'If this product is heavy flammable fragile etc. So Please enter yes. After activating it you will get orders for this product from your city itself and you will have to deliver this product yourself.'],
//         'Warehouse' => ['Noida warehouse', "Fill your warehouse name here. If you've not added any warehouse yet then add it from your seller panel > My shop > Add warehouse"],
//         'Procurement SLA (DAY)' => ['Single - Number', 'Time required to keep the product ready for dispatch. For Instant Delivery, SLA value is in Hours and for the others, SLA is in Days'],
//         'Stock' => ['Number', 'Number of items you have in stock. Add minimum 5 quantity to ensure listing visibility'],
//         'MOQ' => ['Number', 'Write a minimum order quantity'],
//         'Pakeging Diemensions' => [['Length (CM)','Breadth (CM)','Height (CM)','Weight (KG)'],['Length of the package in cms','Breadth of the package in cms','Height of the package in cms','Weight of the final package in kgs']],
//         'HSN' => ['Single - Text', 'To be filled by InteriorChowk'],
//         'Tax' => ['Single - Text',"InteriorChowk's tax code which decides the goods and services tax for the listing"],
//         'Thumbnail Image name' => ['Image name', '1st Image (Thumbnail): Front View (main display image, required) – Minimum resolution 100x500'],
//         'Other Image name' => ['Eg.TABLELAMP01.webp,TABLELAMP02.webp).', 'Upload images in the following order (minimum resolution 100x500): 2nd – Back View, 3rd – Open View, 4th – Side View, 5th – Lifestyle View, and 6th – Detail View.'],

//         'Video URL' => ['URL', 'Please see the summary sheet for Video URL guidelines.'],
//         'PDF URL' => ['URL', 'Please see the summary sheet for PDF URL guidelines.'],
//         'Search Tags' => ['-', 'Write search keywords and tags for the product'],
//         'Excel error status' => ['-', 'If any column between A to BD is blank, it means product details are missing → shows ❌ Error. Only when all details are filled, it shows ✅ OK. Please add all product details to get the product approved.'],
//     ];

//     $keys = array_keys($baseData);
//     foreach ($keys as $i => $key) {
//         if (isset($map[$key])) {
//             $instruction1[$i] = $map[$key][0];
//             $instruction2[$i] = $map[$key][1];
//         }
//     }

//     // --- Export Excel
//     $filename = "products_bulk.xls";
//     header("Content-Type: application/vnd.ms-excel");
//     header("Content-Disposition: attachment; filename=\"$filename\"");

//     echo '<table border="1" cellspacing="0" cellpadding="5">';

//     // Row 1: Headers
//     echo '<tr style="background-color:#c4d79b; color:#000; font-weight:bold; text-align:center;">';
//     foreach ($keys as $header) {
//         echo "<td>{$header}</td>";
//     }
//     echo '</tr>';

//     // Row 2: Instruction 1
//     echo '<tr style="background-color:#ffff00; color:#000; font-weight:bold; text-align:center;">';
//     foreach ($instruction1 as $inst1) {
//         echo "<td>{$inst1}</td>";
//     }
//     echo '</tr>';

//     // Row 3: Instruction 2
//     echo '<tr style="background-color:#fcd5b4; color:#ff0000; font-weight:bold; text-align:center;">';
//     foreach ($instruction2 as $inst2) {
//         echo "<td>{$inst2}</td>";
//     }
//     echo '</tr>';

//     // Row 4: Data
//     echo '<tr>';
//     foreach ($baseData as $value) {
//         echo "<td style='text-align:center; vertical-align:middle;'>{$value}</td>";
//     }
//     echo '</tr>';

//     echo '</table>';
// }


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

    $specifications = !empty($sub_sub_category->specification) ? explode(',', $sub_sub_category->specification) : [];
    $key_features = !empty($sub_sub_category->key_features) ? explode(',', $sub_sub_category->key_features) : [];
    $technical_specifications = !empty($sub_sub_category->technical_specification) ? explode(',', $sub_sub_category->technical_specification) : [];
    $other_details = !empty($sub_sub_category->other_details) ? explode(',', $sub_sub_category->other_details) : [];

    $colors = DB::table('colors')->pluck('name')->toArray();

    //     $baseData = [
    //     'InteriorChowk Product Code' => '',
    //     'Catelogue QC Status' => '',
    //     'QC Failed Reason (if any)' => $product->qc_failed_reason ?? '',
    //     'Product Category' => $category->name ?? '',
    //     'Product Sub Category' => $sub_category->name ?? '',
    //     'Product Sub Sub Category' => $sub_sub_category->name ?? '',
    //     'Brands' => $brand_name->name ?? '',
    //     'Seller SKU ID' => '',
    //     'Commission Type' => $commission_type,
    //     'Commission Fee' => $commission_fee->fee ?? '',
    //     'Listing Status' => '',
    //     'MRP (INR)' => $skuProduct->variant_mrp,
    //     'Discount Type' => $skuProduct->discount_type,
    //     'Discount' => $skuProduct->discount,
    //     // 👇 Formula added here
    //     'Your selling price (INR)' => '=IF(M4="Flat", MAX(0, L4-N4), IF(M4="Percent", MAX(0, L4-(L4*N4/100)), ""))',
    //     'Product Title' => '',
    //     'Product Description' => '',
    //     'Colour' => implode(', ', $colors),
    //     'Size' => '',
    // ];

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
        'Your selling price (INR)' => '=IF(M2="Flat", MAX(0, L2-N2), IF(M2="Percent", MAX(0, L2-(L2*N2/100)), ""))',
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

    // --- Instructions map
    $map = [
        'InteriorChowk Product Code' => ['', 'To be filled by InteriorChowk'],
        'Catelogue QC Status' => ['', 'To be filled by InteriorChowk'],
        'QC Failed Reason (if any)' => ['', 'To be filled by InteriorChowk'],
        'Product Category' => ['', 'To be filled by InteriorChowk'],
        'Product Sub Category' => ['', 'To be filled by InteriorChowk'],
        'Product Sub Sub Category' => ['', 'To be filled by InteriorChowk'],
        'Brands' => ['', 'To be filled by InteriorChowk'],
        'Seller SKU ID' => ['Text - limited to 64 characters (including spaces)', 'Seller SKU ID is the identification number maintained by seller to keep track of SKUs. This will be mapped with InteriorChowk product code.'],
        'Commission Type' => ['', 'To be filled by InteriorChowk'],
        'Commission Fee' => ['', 'To be filled by InteriorChowk'],
        'Listing Status' => ['Single - Text', 'Inactive listings are not available for buyers on InteriorChowk'],
        'MRP (INR)' => ['Single - Positive_integer', 'Maximum retail price of the product'],
        'Discount Type' => ['-', 'Write flat or percent'],
        'Discount' => ['-', 'In Rs. Or In %'],
        'Your selling price (INR)' => ['Single - Positive_integer', 'Price at which you want to sell this listing'],
        'Product Title' => ['Single - Text Used For: Title', 'Product Title is the identity of the product that helps in distinguishing it from other products.'],
        'Product Description' => ['Single - Text', 'Please write few lines describing your product...'],
        'Colour' => [['Colour Name','Rename Colour Name'], ['Eg. Silver','Eg. Matt Finish']],
        'Size' => ['Add size', 'Eg. S, M, L or custom size'],
        'Specification' => [['Brand','Product Dimensions','Wattage','Voltage'],['-','-','-','-']],
        'Key Features' => [['Manufacturer','Packer','Net Quantity','Included Components'],['-','-','-','-']],
        'Technical Specification' => [['A','B','C','D'],['-','-','-','-']],
        'Other Details' => [['A','B','C','D'],['-','-','-','-']],
        'Return Days' => ['Number', '-'],
        'Replacement Days' => ['Number', 'Enter number of days only if you want to offer replacement only (no return).'],
        'Unit' => ['-', 'Eg. - Kg, pc, gms, lts, set, Pair, Sqft, Sq Mtr., Box'],
        'Free Delivery' => ['-', 'Yes / No'],
        'Self Delivery' => ['-', 'If this product is heavy flammable fragile etc. Please enter yes.'],
        'Warehouse' => ['Noida warehouse', "Fill your warehouse name here."],
        'Procurement SLA (DAY)' => ['Single - Number', 'Time required to keep the product ready for dispatch.'],
        'Stock' => ['Number', 'Number of items you have in stock. Add minimum 5 quantity to ensure listing visibility'],
        'MOQ' => ['Number', 'Write a minimum order quantity'],
        'Packaging Dimensions' => [['Length (CM)','Breadth (CM)','Height (CM)','Weight (KG)'], ['Length of the package in cms','Breadth of the package in cms','Height of the package in cms','Weight of the final package in kgs']],
        'HSN' => ['Single - Text', 'To be filled by InteriorChowk'],
        'Tax' => ['Single - Text',"InteriorChowk's tax code which decides the GST for the listing"],
        'Thumbnail Image name' => ['Image name', '1st Image (Thumbnail): Front View – Minimum resolution 100x500'],
        'Other Image name' => ['Eg.TABLELAMP01.webp,TABLELAMP02.webp', 'Upload images in order (2nd – Back, 3rd – Open, 4th – Side, 5th – Lifestyle, 6th – Detail).'],
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
    echo '<tr style="background-color:#c4d79b; color:#000; vertical-align:middle; font-weight:bold; text-align:center;">';
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
    echo '<tr style="background-color:#ffff00; color:#000; vertical-align:middle; font-weight:bold; text-align:center;">';
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
    echo '<tr style="background-color:#fcd5b4; color:#ff0000; font-weight:bold; text-align:center; vertical-align:middle;">';
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



    public function save_product(Request $request){
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