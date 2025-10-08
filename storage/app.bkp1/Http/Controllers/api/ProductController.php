<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function filter(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        $page = max((int) $request->input('page', 1), 1);
        $offset = ($page - 1) * $limit;
        $showFiltersOptions = (bool) $request->input('showFiltersOptions', false);

        $baseQuery = DB::table('products as p')
            ->join('sku_product_new as sku', 'p.id', '=', 'sku.product_id')
            ->leftJoin('key_specification_values as ksv', 'ksv.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.sub_sub_category_id')
            ->leftJoin(DB::raw('(SELECT product_id, COUNT(*) AS total_orders FROM order_details GROUP BY product_id) as od'), 'p.id', '=', 'od.product_id')
            ->leftJoin(DB::raw('(SELECT product_id, ROUND(AVG(rating), 2) AS avg_rating FROM reviews WHERE status = 1 GROUP BY product_id) as r'), 'p.id', '=', 'r.product_id')
            ->leftJoin('deal_of_the_days as deal', 'p.id', '=', 'deal.product_id')
            ->leftJoin('sellers as seller', 'p.user_id', '=', 'seller.id')
            ->leftJoin('shops as shop', 'seller.id', '=', 'shop.seller_id')
            ->leftJoin(DB::raw('(SELECT pt.product_id, GROUP_CONCAT(t.tag) AS tags FROM product_tag pt JOIN tags t ON pt.tag_id = t.id GROUP BY pt.product_id) as tag_data'), 'p.id', '=', 'tag_data.product_id')
            ->select(
                'p.*',
                'ksv.specification', 'ksv.key_features', 'ksv.technical_specification',
                'c.specification as specs', 'c.technical_specification as techs', 'c.key_features as keyf',
                DB::raw('COALESCE(od.total_orders, 0) as total_orders'),
                DB::raw('COALESCE(r.avg_rating, 0) as avg_rating'),
                'deal.status as deal_status', 'deal.start_date_time', 'deal.expire_date_time',
                DB::raw("CASE 
                    WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
                    WHEN sku.discount_type = 'percent' THEN CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10, 2)) / 100)), 2) AS DECIMAL(10, 2))
                    WHEN sku.discount_type = 'flat' THEN CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
                    ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2)) 
                    END AS final_unit_price"),
                DB::raw('tag_data.tags')
            )
            ->whereNotNull('sku.variant_mrp')
            ->where('request_status', 1)
            ->where('p.status', 1)
            ->where('seller.status', 'approved')
            ->where('shop.vacation_status', 0)
            ->where('shop.temporary_close', 0);

        // Filters
        $this->applyFilters($baseQuery, $request);

        // Grouping
        $baseQuery->groupBy('p.id');

        // Having clause
        $this->applyHaving($baseQuery, $request);

        // Sorting
        $this->applySorting($baseQuery, $request);

        // Clone query for count before limit
        $total = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery)
            ->count();

        if (!$showFiltersOptions) {
            $products = $baseQuery->offset($offset)->limit($limit)->get();

            $productIds = $products->pluck('id')->toArray();

            $skuMap = DB::table('sku_product_new')
                ->whereIn('product_id', $productIds)
                ->whereNotNull('variant_mrp')
                ->orderByDesc('id')
                ->get()
                ->keyBy('product_id');

            $products->transform(function ($row) use ($skuMap) {
                foreach (['images', 'category_ids', 'color_image', 'choice_options', 'variation', 'colors', 'attributes'] as $jsonField) {
                    if (!empty($row->$jsonField)) {
                        $row->$jsonField = json_decode($row->$jsonField, true);
                    }
                }

                foreach ([
                    'id' => 'int', 'user_id' => 'int', 'Return_days' => 'int', 'min_qty' => 'int',
                    'refundable' => 'int', 'unit_price' => 'float', 'purchase_price' => 'float',
                    'tax' => 'float', 'discount' => 'float', 'current_stock' => 'int', 'free_shipping' => 'int',
                    'free_delivery' => 'int', 'minimum_order_qty' => 'int', 'status' => 'int',
                    'shipping_cost' => 'float', 'multiply_qty' => 'int', 'available_instant_delivery' => 'int',
                    'final_unit_price' => 'float'
                ] as $field => $type) {
                    if (isset($row->$field)) {
                        settype($row->$field, $type);
                    }
                }

                if (isset($skuMap[$row->id])) {
                    $sku = $skuMap[$row->id];
                    $row->images = !empty($sku->image) ? json_decode($sku->image, true) : [];
                    $row->thumbnail = $sku->thumbnail_image ?? ($row->images[0] ?? null);
                    $row->unit_price = $sku->variant_mrp;
                    $row->discount = (int) $sku->discount;
                    $row->discount_type = $sku->discount_type;
                }

                return $row;
            });
        } else {
            $products = [];
            $filters = $this->extractFilters(clone $baseQuery);
        }

        return response()->json([
            'total_size' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'current_page' => $page,
            'products' => $products,
            'filters' => $filters ?? []
        ]);
    }

    // private function applyFilters(&$query, Request $request)
    // {
    //     // Extract and apply all WHERE conditions
    //     // (category, sub-category, brand, seller, price, colors, material, choice_options etc.)
    // }

    private function applyFilters(&$query, Request $request)
    {
        // Search by product name / tags
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%$search%")
                ->orWhere('p.slug', 'like', "%$search%")
                ->orWhere('tag_data.tags', 'like', "%$search%");
            });
        }

        // Category filter (sub_sub_category_id)
        if ($categoryIds = $request->input('categoryIds')) {
            $categoryIds = is_array($categoryIds) ? $categoryIds : explode(',', $categoryIds);
            $query->whereIn('p.sub_sub_category_id', $categoryIds);
        }

        // Brand filter
        if ($brandIds = $request->input('brandIds')) {
            $brandIds = is_array($brandIds) ? $brandIds : explode(',', $brandIds);
            $query->whereIn('p.brand_id', $brandIds);
        }

        // Seller filter
        if ($sellerIds = $request->input('sellerIds')) {
            $sellerIds = is_array($sellerIds) ? $sellerIds : explode(',', $sellerIds);
            $query->whereIn('p.user_id', $sellerIds);
        }

        // Color filter (stored as JSON array)
        if ($colors = $request->input('colors')) {
            $colors = is_array($colors) ? $colors : explode(',', $colors);
            $query->where(function ($q) use ($colors) {
                foreach ($colors as $color) {
                    $q->orWhere('p.colors', 'like', '%"'.$color.'"%');
                }
            });
        }

        // Price range (handled via having due to dynamic pricing)
        if ($min_price = $request->input('min_price')) {
            $query->havingRaw('final_unit_price >= ?', [(float) $min_price]);
        }

        if ($max_price = $request->input('max_price')) {
            $query->havingRaw('final_unit_price <= ?', [(float) $max_price]);
        }
    }


    private function applyHaving(&$query, Request $request)
    {
        // Apply having conditions like avg_rating, discount
    }

    private function applySorting(&$query, Request $request)
    {
        // Handle sorting like priceHigh, priceLow, newArrivals, topProducts, etc.
    }

    private function extractFilters($query)
    {
        // Return the dynamic filters based on query result, like categories, brands, etc.
        return [];
    }
}
