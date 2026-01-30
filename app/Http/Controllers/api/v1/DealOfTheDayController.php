<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\DealOfTheDay;
use App\Model\Product;
use App\CPU\Helpers;

class DealOfTheDayController extends Controller
{
    public function get_deal_of_the_day_product(Request $request)
    {
        $products = [];
        $deal_of_the_day = DealOfTheDay::where('deal_of_the_days.status', 1)
                            ->whereDate('start_date_time', '<=', now())
                            ->whereDate('expire_date_time', '>=', now())
                          ->get();
       
        
        if(isset($deal_of_the_day)){
            foreach ($deal_of_the_day as $deal_of_the_days){
               // dd($deal_of_the_days->product_id);
            $product = Product::active()->find($deal_of_the_days->product_id);
            $product['start_date_time'] = $deal_of_the_days['start_date_time'];
            $product['expire_date_time'] = $deal_of_the_days['expire_date_time'];
         
            if(!isset($product))
            {
                $product = Product::active()->inRandomOrder()->first();
            }
            $products['products'][] = Helpers::product_data_formatting($product);
        }
       
            return response()->json($products, 200);
        }else{
            $product = Product::active()->inRandomOrder()->first();
            $products['products'][] = Helpers::product_data_formatting($product);
            
            return response()->json($products, 200);
        }
        
    }
}