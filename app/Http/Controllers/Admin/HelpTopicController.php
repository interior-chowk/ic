<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\Model\HelpTopicCategory;
use App\Model\HelpTopicSubCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class HelpTopicController extends Controller
{
    public function add_new()
    {
        return view('admin-views.help-topics.add-new');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer'   => 'required',
            'category_id' => 'required',
            'sub_cat_id' => 'required',
            'ranking'   => 'required',
        ], [
            'question.required' => 'Question name is required!',
            'answer.required'   => 'Question answer is required!',
            'category_id.required' => 'Category Id is required!',
            'sub_cat_id.required' => 'Subcategory Id is required!',
        ]);
        $helps = new HelpTopic;
        $helps->question = $request->question;
        $helps->answer = $request->answer;
        $helps->category_id = $request->category_id;
        $helps->sub_cat_id = $request->sub_cat_id;
        $request->has('status') ? $helps->status = 1 : $helps->status = 0;
        $helps->ranking = $request->ranking;
        $helps->save();

        Toastr::success('FAQ added successfully!');
        return back();
    }

    public function status($id)
    {
        $helps = HelpTopic::findOrFail($id);
        if ($helps->status == 1) {
            $helps->update(["status" => 0]);
        } else {
            $helps->update(["status" => 1]);
        }
        return response()->json(['success' => 'Status Change']);
    }

    public function category()
    {
        $categories = HelpTopicCategory::all();
        return view('admin-views.help-topics.category', compact('categories'));
    }
    
    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Category name is required!',
        ]);
        $helps = new HelpTopicCategory;
        $helps->name = $request->name;
        $helps->save();
        Toastr::success('Category added successfully!');
        return back();
    }

    public function category_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Category name is required!',
        ]);
        $helps = HelpTopicCategory::find($id);
        $helps->name = $request->name;
        $helps->update();
        Toastr::success('Category Update successfully!');
        return back();
    }

    public function category_delete(Request $request)
    {
        $category = HelpTopicCategory::findOrFail($request->id);
        $category->delete();
    
        return response()->json(['success' => true]);
    }

    public function subcategory()
    {
        $subcategories = HelpTopicSubCategory::all();
        $categories = HelpTopicCategory::all();
        return view('admin-views.help-topics.subcategory', compact('subcategories', 'categories'));
    }

    public function subcategory_store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'sub_cat_name' => 'required',
        ], [
            'category_id.required' => 'Category name is required!',
            'sub_cat_name.required' => 'Subcategory name is required!',
        ]);
    
        $helps = new HelpTopicSubCategory;
        $helps->cat_id = $request->category_id;
        $helps->sub_cat_name = $request->sub_cat_name;
        $helps->link = $request->link;
        $helps->link_name = $request->link_name;
        $helps->link_short_description = $request->link_short_description;
        $helps->save();
    
        Toastr::success('Subcategory added successfully!');
        return back();
    }

    public function subcategory_update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'sub_cat_name' => 'required',
        ], [
            'category_id.required' => 'Category name is required!',
            'sub_cat_name.required' => 'Subcategory name is required!',
        ]);
        $helps = HelpTopicSubCategory::find($id);
        $helps->cat_id = $request->category_id;
        $helps->sub_cat_name = $request->sub_cat_name;
        $helps->link = $request->link;
        $helps->link_name = $request->link_name;
        $helps->link_short_description = $request->link_short_description;
        $helps->update();
        Toastr::success('Subcategory Update successfully!');
        return back();
    }

    public function subcategory_delete(Request $request)
    {
        $subcategory = HelpTopicSubcategory::find($request->id);

        if (!$subcategory) {
            return response()->json(['error' => 'Subcategory not found'], 404);
        }
    
        $subcategory->delete();
    
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $helps = HelpTopic::findOrFail($id);
        return response()->json($helps);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'answer'   => 'required',
            'category_id' => 'required',
            'sub_cat_id' => 'required',
        ], [
            'question.required' => 'Question name is required!',
            'answer.required'   => 'Question answer is required!',
            'category_id.required' => 'Category Id is required!',
            'sub_cat_id.required' => 'Subcategory Id is required!',
        ]);
        $helps = HelpTopic::find($id);
        $helps->category_id = $request->category_id;
        $helps->sub_cat_id = $request->sub_cat_id;
        $helps->question = $request->question;
        $helps->answer = $request->answer;
        $helps->ranking = $request->ranking;
        $helps->update();
        Toastr::success('FAQ Update successfully!');
        return back();
    }

    function list()
    {
        $helps = HelpTopic::latest()->get();
        $categories = HelpTopicCategory::all();
        return view('admin-views.help-topics.list', compact('helps', 'categories'));
    }

    public function destroy(Request $request)
    {
        $helps = HelpTopic::find($request->id);
        $helps->delete();
        return response()->json();
    }

    public function getSubcategories($category_id)
    {
        $subcategories = HelpTopicSubcategory::where('cat_id', $category_id)->get();
        return response()->json($subcategories);
    }

}
