<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class BlogController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $blogs = Blog::when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin-views.blogs.list', compact('blogs'));
    }
    
    public function add()
    {
        return view('admin-views.blogs.add-new');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string',
            'description' => 'required',
        ]);

        // Clean base slug
        $slug = Str::slug($request->title);

        // Check for uniqueness
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->slug = $slug; // Now: 'the-new-post', 'the-new-post-1', etc.
        $blog->category = $request->category;
        $blog->content = $request->content;
        $blog->description = $request->description;

        // Handle image
        if ($request->hasFile('image')) {
            $imageName = time() . '-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/blog', $imageName);
            $blog->image = 'blog/' . $imageName;
        }

        // Handle banner image
        if ($request->hasFile('banner')) {
            $bannerName = time() . '-' . uniqid() . '.' . $request->banner->getClientOriginalExtension();
            $request->banner->storeAs('public/blog/banners', $bannerName);
            $blog->banner = 'blog/banners/' . $bannerName;
        }

        $blog->save();

        return redirect()->route('admin.blog.list')->with('success', 'Blog posted successfully');
    }


    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('public/blog/description_images', $filename);

            $url = asset(Storage::url($path));
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url
            ]);
        }

        return response()->json(['uploaded' => 0, 'error' => ['message' => 'Upload failed.']]);
    }



    public function edit($id)
    {
        $blog = Blog::find($id);
        return view('admin-views.blogs.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string',
            'description' => 'required|string',
        ]);

        $blog = Blog::findOrFail($id);
        $blog->title = $request->title;
        $blog->category = $request->category;
        $blog->content = $request->content;
        $blog->description = $request->description;

        if ($request->hasFile('image')) {
            $imageName = time() . '-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/blog', $imageName);
            $blog->image = 'blog/' . $imageName;
        }

        if ($request->hasFile('banner')) {
            $bannerName = time() . '-banner-' . uniqid() . '.' . $request->banner->getClientOriginalExtension();
            $request->banner->storeAs('public/blog/banners', $bannerName);
            $blog->banner = 'blog/banners/' . $bannerName;
        }

        $blog->save();

        return redirect()->route('admin.blog.list')->with('success', 'Blog updated successfully');
    }

    
    

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $career = Blog::find($request->id);
        $career->delete();

        return redirect()->back()->with('success', 'Job deleted successfully!');
    }
}
