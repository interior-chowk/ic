<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Brand;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Translation;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    public function add_new()
    {
        $br = Brand::latest()->paginate(Helpers::pagination_limit());
        $language=\App\Model\BusinessSetting::where('type','pnc_language')->first();
        $language = $language->value ?? null;
        $default_lang = 'en';

        return view('admin-views.brand.add-new', compact('br', 'language', 'default_lang'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name.0' => 'required|unique:brands,name',
    //     ], [
    //         'name.0.required'   => 'Brand name is required!',
    //         'name.0.unique'     => 'The brand has already been taken.',
    //     ]);

    //     $brand = new Brand;
    //     $brand->name = $request->name[array_search('en', $request->lang)];
    //     // $brand->image = ImageManager::upload('brand/', 'png', $request->file('image'));

    //     if ($request->hasFile('image')) {

    //         $file = $request->file('image');
    //         $mimeType = $file->getMimeType();
    //         $extension = $file->getClientOriginalExtension();

    //         // 📌 IMAGE → Convert to WEBP
    //         if (str_starts_with($mimeType, 'image/')) {

    //             $webpImage = Image::make($file->getRealPath())
    //                 ->encode('webp', 90);

    //             $filename = 'brand/' . uniqid() . '.webp';

    //             Storage::disk('r2')->put($filename, (string) $webpImage);

    //             $brand->image = '/' . $filename;
    //         }

    //         // 📌 OPTIONAL: If someday brand supports video/files
    //         else {

    //             $filename = 'brand/' . uniqid() . '.' . $extension;

    //             Storage::disk('r2')->put(
    //                 $filename,
    //                 fopen($file->getRealPath(), 'r+')
    //             );

    //             $brand->image = '/' . $filename;
    //         }
    //     }
        
    //     $brand->status = 1;
    //     $brand->save();

    //     foreach($request->lang as $index=>$key)
    //     {
    //         if($request->name[$index] && $key != 'en')
    //         {
    //             Translation::updateOrInsert(
    //                 ['translationable_type'  => 'App\Model\Brand',
    //                     'translationable_id'    => $brand->id,
    //                     'locale'                => $key,
    //                     'key'                   => 'name'],
    //                 ['value'                 => $request->name[$index]]
    //             );
    //         }
    //     }
    //     Toastr::success('Brand added successfully!');
    //     return back();
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name.0' => 'required|unique:brands,name',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.0.required' => 'Brand name is required!',
            'name.0.unique'   => 'The brand has already been taken.',
        ]);

        $brand = new Brand();

        // 🌐 English name
        $enIndex = array_search('en', $request->lang);
        $brand->name = $request->name[$enIndex] ?? $request->name[0];

        // 📌 Brand Image Upload → Cloudflare R2
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $mimeType = $file->getMimeType();

            // 🖼 IMAGE → Convert to WEBP
            if (str_starts_with($mimeType, 'image/')) {

                $webpImage = Image::make($file->getRealPath())
                    ->encode('webp', 90);

                $filename = 'brand/' . uniqid() . '.webp';

                Storage::disk('r2')->put($filename, (string) $webpImage);

                $brand->image = '/' . $filename;
            }
        }

        $brand->status = 1;
        $brand->save();

        // 🌍 Translations
        foreach ($request->lang as $index => $locale) {
            if (!empty($request->name[$index]) && $locale !== 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => Brand::class,
                        'translationable_id'   => $brand->id,
                        'locale'               => $locale,
                        'key'                  => 'name',
                    ],
                    [
                        'value' => $request->name[$index],
                    ]
                );
            }
        }

        Toastr::success('Brand added successfully!');
        return back();
    }
    
    /**
     * Brand list show, search
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function list(Request $request)
    {
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $br = Brand::withCount('brandAllProducts')
            ->with(['brandAllProducts'=> function($query){
                $query->withCount('order_details');
            }])
            ->when($request['search'], function ($q) use($request){
                $key = explode(' ', $request['search']);
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%")
                      ->orWhere('id', $value);
                }
            })
            ->latest()->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.brand.list', compact('br','search'));
    }

    /**
     * Export brand list by excel
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export()
    {
        $brands = Brand::withCount('brandAllProducts')
            ->with(['brandAllProducts'=> function($query){
                $query->withCount('order_details');
            }])->orderBy('id', 'DESC')->get();

        $data = array();
        foreach($brands as $brand){
            $data[] = array(
                'Brand Name'      => $brand->name,
                'Total Product'   => $brand->brand_all_products_count,
                'Total Order' => $brand->brandAllProducts->sum('order_details_count'),
            );
        }

        return (new FastExcel($data))->download('brand_list.xlsx');
    }

    public function edit($id)
    {
        $b = Brand::where(['id' => $id])->withoutGlobalScopes()->first();
        return view('admin-views.brand.edit', compact('b'));
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name.0' => 'required|unique:brands,name,'.$id,
    //     ], [
    //         'name.0.required'   => 'Brand name is required!',
    //         'name.0.unique'     => 'The brand has already been taken.',
    //     ]);

    //     $brand = Brand::find($id);
    //     $brand->name = $request->name[array_search('en', $request->lang)];
    //     if ($request->has('image')) {
    //         $brand->image = ImageManager::update('brand/', $brand['image'], 'png', $request->file('image'));
    //      }
    //     $brand->save();
    //     foreach ($request->lang as $index => $key) {
    //         if ($request->name[$index] && $key != 'en') {
    //             Translation::updateOrInsert(
    //                 ['translationable_type' => 'App\Model\Brand',
    //                     'translationable_id' => $brand->id,
    //                     'locale' => $key,
    //                     'key' => 'name'],
    //                 ['value' => $request->name[$index]]
    //             );
    //         }
    //     }

    //     Toastr::success('Brand updated successfully!');
    //     return back();
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name.0' => 'required|unique:brands,name,' . $id,
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'name.0.required' => 'Brand name is required!',
            'name.0.unique'   => 'The brand has already been taken.',
        ]);

        $brand = Brand::findOrFail($id);

        // 🌐 English name
        $enIndex = array_search('en', $request->lang);
        $brand->name = $request->name[$enIndex] ?? $request->name[0];

        // 📌 Image Update (Cloudflare R2)
        if ($request->hasFile('image')) {

            // ❌ Delete old image from R2
            if (!empty($brand->image)) {
                $oldPath = ltrim($brand->image, '/');
                Storage::disk('r2')->delete($oldPath);
            }

            $file = $request->file('image');
            $mimeType = $file->getMimeType();

            // 🖼 Convert IMAGE → WEBP
            if (str_starts_with($mimeType, 'image/')) {

                $webpImage = Image::make($file->getRealPath())
                    ->encode('webp', 90);

                $filename = 'brand/' . uniqid() . '.webp';

                Storage::disk('r2')->put($filename, (string) $webpImage);

                $brand->image = '/' . $filename;
            }
        }

        $brand->save();

        // 🌍 Update translations
        foreach ($request->lang as $index => $locale) {
            if (!empty($request->name[$index]) && $locale !== 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => Brand::class,
                        'translationable_id'   => $brand->id,
                        'locale'               => $locale,
                        'key'                  => 'name',
                    ],
                    [
                        'value' => $request->name[$index],
                    ]
                );
            }
        }

        Toastr::success('Brand updated successfully!');
        return back();
    }

    public function status_update(Request $request)
    {
        $brand = Brand::find($request['id']);
        $brand->status = $request['status'];

        if($brand->save()){
            $success = 1;
        }else{
            $success = 0;
        }
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Model\Brand')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        $brand = Brand::find($request->id);
        ImageManager::delete('/brand/' . $brand['image']);
        $brand->delete();
        return response()->json();
    }
}