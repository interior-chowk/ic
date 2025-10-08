<?php

namespace App\Http\Controllers\Admin;

use App\Model\SeoMeta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeoMetaController extends Controller
{
    public function index()
    {
        $seoMeta = SeoMeta::all();
        return view('admin-views.seo.index', compact('seoMeta'));
    }

    public function create()
    {
        return view('admin-views.seo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|unique:seo_meta,page',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
            'canonical' => 'nullable|url',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|url',
        ]);

        $seo = new SeoMeta();
        $seo->page = $request->page;
        $seo->meta_title = $request->meta_title;
        $seo->meta_description = $request->meta_description;
        $seo->meta_keywords = $request->meta_keywords;
        $seo->canonical = $request->canonical;
        $seo->content = $request->content;

        // Combine OG tags into one JSON column
        $seo->og_tags = json_encode([
            'title' => $request->og_title,
            'description' => $request->og_description,
            'image' => $request->og_image,
        ]);

        $seo->save();

        return redirect()->route('admin.seo.index')->with('success', 'SEO created successfully.');
    }


    public function edit($id)
    {
        $data = SeoMeta::findOrFail($id);
        return view('admin-views.seo.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = SeoMeta::findOrFail($id);

        $request->validate([
            'page' => 'required|unique:seo_meta,page,' . $data->id,
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keywords' => 'nullable',
            'canonical' => 'nullable|url',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|url',
        ]);

        $data->page = $request->page;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->canonical = $request->canonical;
        $data->content = $request->content;

        $data->og_tags = json_encode([
            'title' => $request->og_title,
            'description' => $request->og_description,
            'image' => $request->og_image,
        ]);

        $data->save();

        return redirect()->route('admin.seo.index')->with('success', 'SEO updated successfully.');
    }


    public function destroy(SeoMeta $seo)
    {
        $seo->delete();
        return back()->with('success', 'SEO deleted');
    }
}
