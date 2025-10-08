<?php

namespace App\CPU;

use App\Model\Brand;
use App\Model\Product;

class BrandManager
{
    public static function get_brands()
    {
        return Brand::withCount('brandProducts')->latest()->get();
    }

    public static function get_products($brand_id)
    {
        return Helpers::product_data_formatting(Product::active()->temporary()->where(['brand_id' => $brand_id])->get(), true);
    }
    
    public static function get_brand_id($brand_id)
    {
        $brand =  Brand::active()->withCount('brandProducts')->where('id',$brand_id)->first();
        
        $brand->brand_products_count = Product::active()->temporary()->where(['brand_id' => $brand->id])->count();   
       
        return $brand;
    }

    public static function get_active_brands(){
        
        $brand =  Brand::active()->withCount('brandProducts')->latest()->get();
        $count =  Brand::active()->withCount('brandProducts')->latest()->count(); 
        foreach ($brand as $brands)
        {
         $brands->brand_products_count = Product::active()->temporary()->where(['brand_id' => $brands->id])->count();   
        }
        
        return $brand;
    }
}
