<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use Illuminate\Http\Request;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function get_seller_info(Request $request)
    {
        $data=[];
        $seller = Seller::with(['shop'])->where(['id' => $request['seller_id']])->first(['id', 'f_name', 'l_name', 'phone', 'image']);

        $product_ids = Product::where(['added_by' => 'seller', 'user_id' => $request['seller_id']])->active()->pluck('id')->toArray();

        $avg_rating = Review::whereIn('product_id', $product_ids)->avg('rating');
        $total_review = Review::whereIn('product_id', $product_ids)->count();
        $total_order = OrderDetail::whereIn('product_id', $product_ids)->groupBy('order_id')->count();
        $total_product = Product::active()->where(['added_by' => 'seller', 'user_id' => $request['seller_id']])->count();

        $data['seller']= $seller;
        $data['avg_rating']= round($avg_rating);
        $data['total_review']=  $total_review;
        $data['total_order']= $total_order;
        $data['total_product']= $total_product;

        return response()->json($data, 200);
    }

    public function get_seller_products($seller_id, Request $request)
    {
        $data = ProductManager::get_seller_products($seller_id, $request);
           $related_products = collect($data['products']);

    // ✅ Product IDs nikaalo
    $related_product_ids = $related_products->pluck('id');

    // ✅ SKU products fetch karo
    $sku_products = DB::table('sku_product_new')
        ->whereIn('product_id', $related_product_ids)
        ->whereNotNull('thumbnail_image')
        ->select('product_id', 'thumbnail_image', 'image', 'listed_price')
        ->get();

    // ✅ SKU product map banao for quick lookup
    $sku_product_map = [];
    foreach ($sku_products as $sku) {
        $sku_product_map[$sku->product_id] = $sku;
    }

    // ✅ Merge SKU data into products
    $merged_related_products = $related_products->map(function ($product) use ($sku_product_map) {
        if (isset($sku_product_map[$product->id])) {
            $sku = $sku_product_map[$product->id];
            $product->thumbnail = $sku->thumbnail_image; 
            $product->images = json_decode($sku->image, true); 
            $product->unit_price = $sku->listed_price; 
        }
        return $product;
    });


        $data['products'] = Helpers::product_data_formatting($merged_related_products, true);
       // dd($data['products']);
        return response()->json($data, 200);
    }

    public function get_seller_all_products($seller_id, Request $request)
    {
        $products = Product::active()
            ->with(['rating','tags'])
            ->where(['user_id' => $seller_id, 'added_by' => 'seller'])
            ->when($request->search, function ($query) use($request){
                $key = explode(' ', $request->search);
                foreach ($key as $value) {
                    $query->where('name', 'like', "%{$value}%");
                }
            })
            ->latest()
            ->paginate($request->limit, ['*'], 'page', $request->offset);


        $products_final = Helpers::product_data_formatting($products->items(), true);

        return [
            'total_size' => $products->total(),
            'limit' => (int)$request->limit,
            'offset' => (int)$request->offset,
            'products' => $products_final
        ];
    }

    public function get_top_sellers()
    {
        $top_sellers = Shop::whereHas('seller',function ($query){return $query->approved();})
                            ->take(15)->get();
        $top_sellers = $top_sellers->map(function($data){
            $data['seller_id'] = (int)$data['seller_id'];
            return $data;
        });
        return response()->json($top_sellers, 200);
    }

    public function get_all_sellers()
    {
        $top_sellers = Shop::whereHas('seller',function ($query){return $query->approved();})->get();
        return response()->json($top_sellers, 200);
    }
}
