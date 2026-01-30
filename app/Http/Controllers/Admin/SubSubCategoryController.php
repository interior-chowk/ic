<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\CPU\ImageManager;
use App\Model\Category;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class SubSubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $filter = $request['filter'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $categories = Category::where(['position'=>2])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        
            
        }elseif ($request->has('filter')) {
            
             $key = explode(' ', $request['filter']);
             $categories = Category::where(['position'=>2])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('sub_parent_id', $value);
                }
            });
            $query_param = ['filter' => $request['filter']];
            
        }else{
            $categories=Category::where(['position'=>2]);
        }
        $categories = $categories->latest()->paginate(Helpers::pagination_limit())->appends($query_param);

         $url = env('CLOUDFLARE_R2_PUBLIC_URL');
        return view('admin-views.category.sub-sub-category-view',compact('categories','search','filter','url'));
    }

    public function top_view(Request $request)
    {
        $category = Category::find($request['id']);
        $category->status = $request['status'];

        if($category->save()){
            $success = 1;
        }else{
            $success = 0;
        }
        return response()->json([
            'success' => $success,
        ], 200);
        
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required'
        ], [
            'name.required' => 'Category name is required!',
            'parent_id.required' => 'Sub Category field is required!',
        ]);

        $category = new Category;
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->slug = Str::slug($request->name[array_search('en', $request->lang)]);
        $category->parent_id = $request->parent_id;
        $category->sub_parent_id = $request->sub_parent_id;
        $category->position = 2;
        $category->priority = $request->priority;
        $category->specification = $request->specification;
        $category->key_features = $request->key_features;
        $category->technical_specification = $request->technical_specification;
        $category->other_details = $request->other_details;
        // $category->icon = ImageManager::upload('category/', 'png', $request->file('image'));
        $iconPath = null;
        $cloudflareId = null;
        $cloudflareUrl = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $webpImage = Image::make($image->getRealPath())->encode('webp');
            
            $filename = 'categories/' . uniqid() . '.webp';
            
            Storage::disk('r2')->put($filename, (string) $webpImage);
            
            $iconPath =  '/' . $filename;
            
            $category->icon = $iconPath;
        }
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->page_content = $request->page_content;
       // dd($category);
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
        Toastr::success('Sub Sub Category updated successfully!');
        return back();
    }

    public function edit(Request $request)
    {
        $data = Category::where('id',$request->id)->first();
        return response()->json($data);
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required'
        ], [
            'name.required' => 'Category name is required!',
            'parent_id.required' => 'Sub Category field is required!',
        ]);

        
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->parent_id = $request->parent_id;

        $iconPath = null;
        $cloudflareId = null;
        $cloudflareUrl = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $webpImage = Image::make($image->getRealPath())->encode('webp');
            
            $filename = 'categories/' . uniqid() . '.webp';
            
            Storage::disk('r2')->put($filename, (string) $webpImage);
            
            $iconPath =  '/' . $filename;
            
            $category->icon = $iconPath;
        }
        
        $category->position = 2;
        $category->priority = $request->priority;
        $category->save();
        return response()->json();
    }
    
    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Model\Category')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        Category::destroy($request->id);
        return response()->json();
    }
    
    public function fetch(Request $request){
        if($request->ajax())
        {
            $data = Category::where('position',2)->orderBy('id','desc')->get();
            return response()->json($data);
        }
    }

    public function getSubCategory(Request $request)
    {
        $data = Category::where("parent_id",$request->id)->where(['position' => 1])->orderBy('name')->get();
        $output='<option value="" disabled selected>Select main category</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo $output;
    }

    public function getCategoryId(Request $request)
    {
        $data= Category::where('id',$request->id)->first();
        return response()->json($data);
    }
    
    public function get_sub_sub_category(Request $request)
    { 
        
        $found=Category::where(['id'=>$request->parent_id])->first();
        
         if($found)
         {
             $categories=Category::where(['sub_parent_id'=>$request->parent_id])->where(['position' => 2])->orderBy('name')->get();
            $data['status'] = 1;
            $data['sub_sub_category']= '<select class="js-example-responsive form-control w-100 sub_sub_category_cls" name="sub_sub_category"  id="sub_sub_category_id">';
           foreach ($categories as $datas) {
        $data['sub_sub_category'] .= '<option value="' . $datas->id . '" >' . $datas->name . '</option>';
        }
         $data['sub_sub_category'] .= '</select>';
         }else{
             $data['status'] = 0;
         }
         
          echo json_encode($data);
    }
    
    public function all_category_excel(Request $request)
    {
        
         $allCategory = Category::where(['position'=>2])->latest()->get();

        $data = array();
        foreach ($allCategory as $category) {
            $parent_category = Category::where('id',$category['parent_id'])->first();
            $sub_parent_category = Category::where('id',$category['sub_parent_id'])->first();
            $data[] = array(
                'Main Category Id' => $parent_category->id ?? '',
                'Main Category' => $parent_category->name ?? '',
                'Sub Category Id' => $sub_parent_category->id ?? '',
                'Sub Category' => $sub_parent_category->name ?? '',
                'Sub Sub Category Id' => $category->id ?? '',
                'Sub Sub Category' => $category->name ?? ''
            );
        }

        return (new FastExcel($data))->download('all_category_list.xlsx');
        
    }
}