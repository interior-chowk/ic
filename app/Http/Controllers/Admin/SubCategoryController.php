<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\ServiceCategory;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    
    public function record(Request $request)
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
    
    public function index( Request $request )
    {
        $query_param = [];
        $search = $request['search'];
        $filter = $request['filter'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $categories = Category::where(['position'=>1])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }elseif ($request->has('filter')) {
            
             $key = explode(' ', $request['filter']);
             $categories = Category::where(['position'=>1])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('parent_id', $value);
                }
            });
            $query_param = ['filter' => $request['filter']];
        
        }else{
            $categories=Category::where(['position'=>1]);
        }
        $categories = $categories->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.category.sub-category-view',compact('categories','search','filter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'priority'=>'required',
            'parent_id'=>'required'
        ], [
            'name.required' => 'Category name is required!',
            'priority.required' => 'Category priority is required!',
            'parent_id.required' => 'Main Category is required!',
        ]);
        $category = new Category;
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->slug = Str::slug($request->name[array_search('en', $request->lang)]);
        if ($request->image){
            $category->icon = ImageManager::upload('category/', 'png', $request->file('image'));
        }
        $category->parent_id = $request->parent_id;
        $category->position = 1;
        $category->priority = $request->priority;
        $category->commission = $request->commission;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->page_content = $request->page_content;
      //  dd($category);
        $category->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Model\Category',
                        'translationable_id'    => $category->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success('Category updated successfully!');
        return back();
    }

    public function edit(Request $request)
    {
        $data = Category::where('id', $request->id)->first();

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->image) {
            $category->icon = ImageManager::update('category/', $category->icon, 'png', $request->file('image'));
        }
        $category->parent_id = $request->parent_id;
        $category->position = 1;
        $category->priority = $request->priority;
        $category->commission = $request->commission;
        $category->save();
        return response()->json();
    }

    public function delete(Request $request)
    {
        $categories = Category::where('parent_id', $request->id)->get();
        if (!empty($categories)) {

            foreach ($categories as $category) {
                $translation = Translation::where('translationable_type','App\Model\Category')
                                    ->where('translationable_id',$category->id);
                $translation->delete();
                Category::destroy($category->id);
            }
        }
        $translation = Translation::where('translationable_type','App\Model\Category')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        Category::destroy($request->id);
        return response()->json();
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::where('position', 1)->orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }
    
    // start service provider 
    
    public function service_index( Request $request )
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $categories = ServiceCategory::where(['position'=>1])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $categories=ServiceCategory::where(['position'=>1]);
        }
        $categories = $categories->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
       
        return view('admin-views.service-category.sub-category-view',compact('categories','search'));
    }

    public function service_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'priority'=>'required',
            'parent_id'=>'required'
        ], [
            'name.required' => 'Category name is required!',
            'priority.required' => 'Category priority is required!',
            'parent_id.required' => 'Main Category is required!',
        ]);
        $category = new ServiceCategory;
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->slug = Str::slug($request->name[array_search('en', $request->lang)]);
        if ($request->image){
            $category->icon = ImageManager::upload('category/', 'png', $request->file('image'));
        }
        $category->parent_id = $request->parent_id;
        $category->position = 1;
        $category->priority = $request->priority;
        $category->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Model\Category',
                        'translationable_id'    => $category->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success('Category updated successfully!');
        return back();
    }

    public function service_edit(Request $request)
    {
        $data = ServiceCategory::where('id', $request->id)->first();

        return response()->json($data);
    }

    public function service_update(Request $request)
    {
        $category = ServiceCategory::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->image) {
            $category->icon = ImageManager::update('category/', $category->icon, 'png', $request->file('image'));
        }
        $category->parent_id = $request->parent_id;
        $category->position = 1;
        $category->priority = $request->priority;
        $category->save();
        return response()->json();
    }

    public function service_delete(Request $request)
    {
        $categories = ServiceCategory::where('parent_id', $request->id)->get();
        if (!empty($categories)) {

            foreach ($categories as $category) {
                $translation = Translation::where('translationable_type','App\Model\ServiceCategory')
                                    ->where('translationable_id',$category->id);
                $translation->delete();
                ServiceCategory::destroy($category->id);
            }
        }
        $translation = Translation::where('translationable_type','App\Model\ServiceCategory')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        ServiceCategory::destroy($request->id);
        return response()->json();
    }

    public function service_fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = ServiceCategory::where('position', 1)->orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }
    
     

    public function get_sub_category(Request $request)
    {
         $categories=Category::where(['parent_id'=>$request->position])->get();
        //  print_r($categories);
        //  die;
         if($categories)
         {
            $data['status'] = 1;
            $data['sub_category']= '<select class="js-example-responsive form-control w-100 sub_category_cls" name="sub_category"  id="sub_sub_category_id">';
           foreach ($categories as $datas) {
        $data['sub_category'] .= '<option value="' . $datas->id . '" onchange="sub_sub_category(' . $datas->id . ')">' . $datas->name . '</option>';
        }
         $data['sub_category'] .= '</select>';
         }else{
             $data['status'] = 0;
         }
         
          echo json_encode($data);
    }
    
    public function get_selected_category(Request $request)
    {
       if($request->sub_category_id !=' ' && $request->sub_sub_category_id != ' ')
       {
        
         $categories=Category::where(['position'=>$request->category_id])->get();
          $data['status'] = 1;
            $data['sub_category']= '<select class="js-example-responsive form-control w-100 sub_category_cls" name="sub_category"  id="sub_sub_category_id">';
           foreach ($categories as $datas) {
       $data['sub_category'] .= '<option value="' . $datas->id . '" ' . ($request->sub_category_id == $datas->id ? 'selected' : '') . ' onchange="sub_sub_category(' . $datas->id . ')">' . $datas->name . '</option>';
        }
         $data['sub_category'] .= '</select>';
         
         
         
             $sub_sub_categories=Category::where(['parent_id'=>$request->sub_category_id])->get();
            
            $data['sub_sub_category']= '<select class="js-example-responsive form-control w-100 sub_sub_category_cls" name="sub_sub_category"  id="sub_sub_category_id">';
           foreach ($sub_sub_categories as $datass) {
       $data['sub_sub_category'] .= '<option value="' . $datass->id . '" ' . ($request->sub_sub_category_id == $datass->id ? 'selected' : '') . ' onchange="sub_sub_category(' . $datass->id . ')">' . $datass->name . '</option>';
        }
         $data['sub_sub_category'] .= '</select>';
         
         
       }else{
           
             $categories=Category::where(['position'=>$request->category_id])->get();
          $data['status'] = 0;
            $data['sub_category']= '<select class="js-example-responsive form-control w-100 sub_category_cls" name="sub_category"  id="sub_sub_category_id">';
           foreach ($categories as $datas) {
       $data['sub_category'] .= '<option value="' . $datas->id . '" ' . ($request->sub_category_id == $datas->id ? 'selected' : '') . ' onchange="sub_sub_category(' . $datas->id . ')">' . $datas->name . '</option>';
        }
         $data['sub_category'] .= '</select>';
       }
       
         echo json_encode($data);
    }
    
    // end the sevice provider 
}
