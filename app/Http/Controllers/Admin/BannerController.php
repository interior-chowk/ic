<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = Banner::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('banner_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $query_param = ['search' => $request['search']];
        } else {
            $banners = Banner::orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.banner.view', compact('banners', 'search'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'url' => 'required',
    //         'image' => 'required',
    //     ], [
    //         'url.required' => 'url is required!',
    //         'image.required' => 'Image is required!',

    //     ]);

    //     $banner = new Banner;
    //     $banner->banner_type = $request->banner_type;
    //     $banner->sub_category = $request->sub_category;
    //     $banner->sub_sub_category = $request->sub_sub_category;
    //     $banner->resource_type = $request->resource_type;
    //     $banner->resource_id = $request[$request->resource_type . '_id'];
    //     $banner->title = $request->title;
    //     $banner->sub_title = $request->sub_title;
    //     $banner->button_text = $request->button_text;
    //     $banner->background_color = $request->background_color;
    //     $banner->url = $request->url;
    //     $banner->discount = $request->discount;
    //    // dd($request->file('image')->extension());

    //     if($request->file('image')->extension() == 'png'){
    //         $banner->photo = ImageManager::upload('banner/', 'png', $request->file('image'));
    //     }elseif($request->file('image')->extension() == 'jpg'){
    //         $banner->photo = ImageManager::upload('banner/', 'jpg', $request->file('image'));
    //     }elseif($request->file('image')->extension() == 'webp'){
    //         $banner->photo = ImageManager::upload('banner/', 'webp', $request->file('image'));
    //     }elseif($request->file('image')->extension() == 'webm'){
    //         $banner->photo = ImageManager::upload('banner/', 'webm', $request->file('image'));
    //     }else{
    //         $banner->photo = ImageManager::upload('banner/', 'mp4', $request->file('image'));
    //     }
        
    //     $banner->video = ImageManager::uploads('banner/', 'mp4', $request->file('video'));

    //     $banner->save();
    //     Toastr::success('Banner added successfully!');
    //     return back();
    // }


    public function store(Request $request)
    {
        $request->validate([
            'url'   => 'required',
            'image' => 'required|file|mimes:png,jpg,jpeg,webp,mp4,webm',
            'video' => 'nullable|file|mimes:mp4,webm',
        ], [
            'url.required'   => 'url is required!',
            'image.required' => 'Image is required!',
        ]);

        $banner = new Banner;
        $banner->banner_type = $request->banner_type;
        $banner->sub_category = $request->sub_category;
        $banner->sub_sub_category = $request->sub_sub_category;
        $banner->resource_type = $request->resource_type;
        $banner->resource_id = $request[$request->resource_type . '_id'];
        $banner->title = $request->title;
        $banner->sub_title = $request->sub_title;
        $banner->button_text = $request->button_text;
        $banner->background_color = $request->background_color;
        $banner->url = $request->url;
        $banner->discount = $request->discount;

        /*
        |--------------------------------------------------------------------------
        | Banner Image / Video Upload → Cloudflare R2
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $mimeType = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();

            // 📌 IMAGE → convert to WEBP
            if (str_starts_with($mimeType, 'image/')) {

                $webpImage = Image::make($file->getRealPath())
                    ->encode('webp', 90);

                $filename = 'banner/' . uniqid() . '.webp';

                Storage::disk('r2')->put($filename, (string) $webpImage);

                $banner->photo = '/' . $filename;
            }

            // 📌 VIDEO → upload directly (mp4 / webm)
            else {

                $filename = 'banner/' . uniqid() . '.' . $extension;

                Storage::disk('r2')->put(
                    $filename,
                    fopen($file->getRealPath(), 'r+')
                );

                $banner->photo = '/' . $filename;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Separate Video Field (Optional)
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('video')) {

            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();

            $filename = 'banner/' . uniqid() . '.' . $extension;

            Storage::disk('r2')->put(
                $filename,
                fopen($video->getRealPath(), 'r+')
            );

            $banner->video = '/' . $filename;
        }

        $banner->save();

        Toastr::success('Banner added successfully!');
        return back();
    }


    public function status(Request $request)
    {
        if ($request->ajax()) {
            $banner = Banner::find($request->id);
            $banner->published = $request->status;
            $banner->save();
            $data = $request->status;
            return response()->json($data);
        }
    }

    public function edit($id)
    {
        $banner = Banner::where('id', $id)->first();
        return view('admin-views.banner.edit', compact('banner'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'url' => 'required',
    //     ], [
    //         'url.required' => 'url is required!',
    //     ]);

    //     $banner = Banner::find($id);
    //     $banner->banner_type = $request->banner_type;
    //     $banner->sub_category = $request->sub_category;
    //     $banner->sub_sub_category = $request->sub_sub_category;
    //     $banner->resource_type = $request->resource_type;
    //     $banner->resource_id = $request[$request->resource_type . '_id'];
    //     $banner->title = $request->title;
    //     $banner->sub_title = $request->sub_title;
    //     $banner->button_text = $request->button_text;
    //     $banner->background_color = $request->background_color;
    //     $banner->url = $request->url;
    //     $banner->discount = $request->discount;
    //     $banner->video = ImageManager::uploads('banner/', 'mp4', $request->file('video'));
    //     if ($request->file('image')) {
    //         $banner->photo = ImageManager::update('banner/', $banner['photo'], 'png', $request->file('image'));
    //     }
    //     $banner->save();

    //     Toastr::success('Banner updated successfully!');
    //     return back();
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'url'   => 'required',
    //         'image' => 'nullable|file|mimes:png,jpg,jpeg,webp,mp4,webm',
    //         'video' => 'nullable|file|mimes:mp4,webm',
    //     ], [
    //         'url.required' => 'url is required!',
    //     ]);

    //     // 🔁 Find or Create Banner
    //     $banner = Banner::find($id) ?? new Banner();

    //     $banner->banner_type = $request->banner_type;
    //     $banner->sub_category = $request->sub_category;
    //     $banner->sub_sub_category = $request->sub_sub_category;
    //     $banner->resource_type = $request->resource_type;
    //     $banner->resource_id = $request[$request->resource_type . '_id'];
    //     $banner->title = $request->title;
    //     $banner->sub_title = $request->sub_title;
    //     $banner->button_text = $request->button_text;
    //     $banner->background_color = $request->background_color;
    //     $banner->url = $request->url;
    //     $banner->discount = $request->discount;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Banner Image / Video
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($request->hasFile('image')) {

    //         // 🧹 Delete old photo ONLY if updating
    //         if ($banner->exists && $banner->photo) {
    //             Storage::disk('r2')->delete(ltrim($banner->photo, '/'));
    //         }

    //         $file = $request->file('image');
    //         $mimeType = $file->getMimeType();
    //         $extension = $file->getClientOriginalExtension();

    //         // 📌 Image → WEBP
    //         if (str_starts_with($mimeType, 'image/')) {

    //             $webpImage = Image::make($file->getRealPath())
    //                 ->encode('webp', 90);

    //             $filename = 'banner/' . uniqid() . '.webp';

    //             Storage::disk('r2')->put($filename, (string) $webpImage);

    //             $banner->photo = '/' . $filename;
    //         }
    //         // 📌 Video → mp4 / webm
    //         else {

    //             $filename = 'banner/' . uniqid() . '.' . $extension;

    //             Storage::disk('r2')->put(
    //                 $filename,
    //                 fopen($file->getRealPath(), 'r+')
    //             );

    //             $banner->photo = '/' . $filename;
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Separate Video Field
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($request->hasFile('video')) {

    //         // 🧹 Delete old video ONLY if updating
    //         if ($banner->exists && $banner->video) {
    //             Storage::disk('r2')->delete(ltrim($banner->video, '/'));
    //         }

    //         $video = $request->file('video');
    //         $extension = $video->getClientOriginalExtension();

    //         $filename = 'banner/' . uniqid() . '.' . $extension;

    //         Storage::disk('r2')->put(
    //             $filename,
    //             fopen($video->getRealPath(), 'r+')
    //         );

    //         $banner->video = '/' . $filename;
    //     }

    //     $banner->save();

    //     Toastr::success(
    //         $banner->wasRecentlyCreated
    //             ? 'Banner created successfully!'
    //             : 'Banner updated successfully!'
    //     );

    //     return back();
    // }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'url'   => 'required',
            'image' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'video' => 'nullable|file|mimes:mp4,webm',
        ], [
            'url.required' => 'URL is required!',
        ]);

        $banner = Banner::find($id) ?? new Banner();

        $banner->banner_type = $request->banner_type;
        $banner->sub_category = $request->sub_category;
        $banner->sub_sub_category = $request->sub_sub_category;
        $banner->resource_type = $request->resource_type;
        $banner->resource_id = $request[$request->resource_type . '_id'] ?? null;
        $banner->title = $request->title;
        $banner->sub_title = $request->sub_title;
        $banner->button_text = $request->button_text;
        $banner->background_color = $request->background_color;
        $banner->url = $request->url;
        $banner->discount = $request->discount;

        // 🔹 Handle Image
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (!str_starts_with($file->getMimeType(), 'image/') || !getimagesize($file->getRealPath())) {
                Toastr::error('Invalid image file!');
                return back();
            }

            // Delete old photo
            if ($banner->exists && $banner->photo) {
                Storage::disk('r2')->delete(ltrim($banner->photo, '/'));
            }

            $webpImage = Image::make($file->getRealPath())->encode('webp', 90);
            $filename = 'banner/' . uniqid() . '.webp';
            Storage::disk('r2')->put($filename, (string) $webpImage);
            $banner->photo = '/' . $filename;
        }

        // 🔹 Handle Separate Video
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();
            $filename = 'banner/' . uniqid() . '.' . $extension;

            if ($banner->exists && $banner->video) {
                Storage::disk('r2')->delete(ltrim($banner->video, '/'));
            }

            Storage::disk('r2')->put($filename, fopen($video->getRealPath(), 'r+'));
            $banner->video = '/' . $filename;
        }

        $banner->save();

        Toastr::success(
            $banner->wasRecentlyCreated
                ? 'Banner created successfully!'
                : 'Banner updated successfully!'
        );

        return back();
    }


    public function delete(Request $request)
    {
        $br = Banner::find($request->id);
        ImageManager::delete('/banner/' . $br['photo']);
        Banner::where('id', $request->id)->delete();
        return response()->json();
    }
}