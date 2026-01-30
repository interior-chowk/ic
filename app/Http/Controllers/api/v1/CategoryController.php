<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CategoryManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;

class CategoryController extends Controller
{
    public function get_categories()
    {
        try {
            $categories = Category::with([
                'childes' => function ($query) {
                    $query->orderBy('priority', 'asc')
                          ->with([
                              'subchildes' => function ($subQuery) {
                                  $subQuery->orderBy('priority', 'asc');
                              }
                          ]);
                }
            ])
            ->where('position', 0)
            ->where('home_status', 1)
            ->orderBy('priority', 'asc')
            ->get();
            
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_sub_sub_categories()
    {
        try {
            $categories = Category::with(['childes'])->where(['status' => 1])->priority()->get();
            
             $categories = $categories->map(function ($category) {
            $category->childes = $category->childes->map(function ($subcategory) {
                // Fetch sub-subcategories for each subcategory where position is 2
                $subcategory->childes = Category::where('sub_parent_id', $subcategory->id)
                                                ->where('position', 2)
                                                ->orderBy('priority', 'asc')
                                                ->get();
                return $subcategory;
            });
            return $category;
        });
            
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_products($id)
    {
        
        $data =  CategoryManager::products($id);
       
       if (!$data->isEmpty())
        {
             return response()->json(Helpers::product_data_formatting(CategoryManager::products($id), true), 200);
        }else{
           return response()->json(['message' => 'No Products found !'], 200);
        }
       
    }
}