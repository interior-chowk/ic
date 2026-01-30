<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PageContent;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PageContentController extends Controller
{
    public function list()
    {
        $page_content = PageContent::all();
        return view('admin-views.page-content.list', compact('page_content'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|unique:page_content,page',
            'content' => 'required',
        ]);
        $page_content = new PageContent();
        $page_content->page = $request->page;
        $page_content->content = $request->content;
        $page_content->save();
        return back()->with('success', 'Page content added successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'page' => 'required',
            'content' => 'required',
        ]);
        $page_content = PageContent::find($request->id);
        $page_content->page = $request->page;
        $page_content->content = $request->content;
        $page_content->save();
        return back()->with('success', 'Page content updated successfully');
    }

    public function delete($id)
    {
        $page = PageContent::find($id);

        if (!$page) {
            return back()->with('error', 'Page not found');
        }

        $page->delete();

        return back()->with('success', 'Page deleted successfully');
    }

}
?>