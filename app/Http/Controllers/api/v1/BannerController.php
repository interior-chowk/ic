<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function get_banners(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $banner_type = $request->input('banner_type');
        $query = Banner::where('published', 1);

        if ($banner_type !== 'all') {
            $banner_map = [

                'main_banner' => 'Main Banner',
                'main_section_banner' => 'Main Section Banner',
                'instant_delivery_banner' => 'Instant Delivery Banner',
                'footer_banner' => 'Footer Banner',
                'main_banner_2' => 'Main Banner 2',
                'service_provider_banner_1' => 'Service Provider Banner 1',
                'service_provider_banner_2' => 'Service Provider Banner 2',
                'service_provider_banner_3' => 'Service Provider Banner 3',
                'banner_2' => 'Banner 2',
                'banner_3' => 'Banner 3',
                'banner_4' => 'Banner 4',
                'banner_5' => 'Banner 5',
                'banner_6' => 'Banner 6',
                'banner_7' => 'Banner 7',
                'banner_8' => 'Banner 8',
                'banner_9' => 'Banner 9',
                'banner_10' => 'Banner 10',
                'discount_1' => 'Discount 1',
                'discount_2' => 'Discount 2',
                'discount_3' => 'Discount 3',
                'discount_4' => 'Discount 4',
                'discount_5' => 'Discount 5',
                'product_page' => ['product_page_1' => 'Product page banner 1', 'product_page_2' => 'Product page banner 2'],
                'bg' => ['luxury_bg' => 'Luxury BG', 'day_bg' => 'Day BG', 'Discount_bg' => 'Discount BG'],
                'tips' => ['tips_1' => 'Tips 1', 'tips_2' => 'Tips 2', 'tips_3' => 'Tips 3', 'tips_4' => 'Tips 4', 'tips_5' => 'Tips 5', 'tips_6' => 'Tips 6'],
                'choice' => ['choice_1' => 'Choice 1', 'choice_2' => 'Choice 2', 'choice' => 'Choice 3', 'choice_4' => 'Choice 4'],
                'service_provider' => [
                    'service_provider_banner_1' => 'Service Provider Banner 1',
                    'service_provider_banner_2' => 'Service Provider Banner 2',
                    'service_provider_banner_3' => 'Service Provider Banner 3'
                ],
                'seasonal_banner' => 'Seasonal Banner',


            ];

            if (isset($banner_map[$banner_type]) && is_array($banner_map[$banner_type])) {
                // Agar `choice` ya `service_provider` ke liye request aayi hai, toh `whereIn` ka use karein
                $query->whereIn('banner_type', $banner_map[$banner_type]);
            } else {
                $mapped_type = $banner_map[$banner_type] ?? null;
                if ($mapped_type) {
                    $query->where('banner_type', $mapped_type);
                } else {
                    return response()->json(['errors' => 'Invalid banner type'], 400);
                }
            }
        }

        $banners = $query->get();
        $pro_ids = [];
        $data = [];

        foreach ($banners as $banner) {
            if ($banner['resource_type'] == 'product' && !in_array($banner['resource_id'], $pro_ids)) {
                array_push($pro_ids, $banner['resource_id']);
                $product = Product::find($banner['resource_id']);
                $banner['product'] = Helpers::product_data_formatting($product);
            }
            $data[] = $banner;
        }

        return response()->json($data, 200);
    }

    public function get_all_banners()
    {
        $grouped_types = [
            'main_banner' => ['Main Banner'],
            'instant_delivery_banner' => ['Instant Delivery Banner'],
            'main_banner_2' => ['Main Banner 2'],
            'seasonal_banner' => ['Seasonal Banner'],
            'banner' => [
                'Banner 2',
                'Banner 3',
                'Banner 4',
                'Banner 5',
                'Banner 6',
                'Banner 7',
                'Banner 8',
                'Banner 9',
                'Banner 10'
            ],
            'discount' => [
                'Discount 1',
                'Discount 2',
                'Discount 3',
                'Discount 4',
                'Discount 5'
            ],
            'service_provider' => [
                'Service Provider Banner 1',
                'Service Provider Banner 2',
                'Service Provider Banner 3'
            ],
            'tips' => [
                'Tips 1',
                'Tips 2',
                'Tips 3',
                'Tips 4',
                'Tips 5',
                'Tips 6'
            ],
            'bg' => [
                'Luxury BG',
                'Day BG',
                'Discount BG'
            ],
        ];
        $all_banner_types = collect($grouped_types)->flatten()->toArray();
        $banners = Banner::whereIn('banner_type', $all_banner_types)
            ->where('published', 1)
            ->get();

        $product_ids = $banners
            ->where('resource_type', 'product')
            ->pluck('resource_id')
            ->unique()
            ->values();

        $products = Product::whereIn('id', $product_ids)->get()->keyBy('id');
        
        foreach ($banners as $banner) {
            if ($banner->resource_type === 'product') {
                $product = $products->get($banner->resource_id);
                $banner->product = $product ? Helpers::product_data_formatting($product) : null;
            }
        }

        $response = [];

        foreach ($grouped_types as $group_key => $types) {
            $grouped_banners = $banners->whereIn('banner_type', $types);

            $sorted_banners = collect($types)->flatMap(function ($type) use ($grouped_banners) {
                return $grouped_banners->where('banner_type', $type)->values();
            });

            $response[$group_key] = $sorted_banners;
        }

        return response()->json(
        $response
            , 200);
    }
   
}