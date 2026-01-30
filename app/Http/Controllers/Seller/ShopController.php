<?php

namespace App\Http\Controllers\Seller;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Shop;
use App\Model\Seller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function view()
    {
        $shop = Shop::where(['seller_id' => auth('seller')->id()])->first();
        if (isset($shop) == false) {
            DB::table('shops')->insert([
                'seller_id' => auth('seller')->id(),
                'name' => auth('seller')->user()->f_name,
                'address' => '',
                'contact' => auth('seller')->user()->phone,
                'image' => 'def.png',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $shop = Shop::where(['seller_id' => auth('seller')->id()])->first();
        }

        return view('seller-views.shop.shopInfo', compact('shop'));
    }

    public function edit($id)
    {
        $shop = Shop::where(['seller_id' =>  auth('seller')->id()])->first();
        $data = Seller::where('id', auth('seller')->id())->first();
        return view('seller-views.shop.edit', compact('shop','data'));
    }

    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        if (!empty($request->comp_name)) {
            $shop->comp_name = $request->comp_name;
        }

        if (empty($shop->brand_name) && !empty($request->brand_name)) {
            $shop->brand_name = $request->brand_name;
        }

        if (empty($shop->brand_logo) && $request->hasFile('brand_logo')) {

            $brandLogo = $request->file('brand_logo');
            $webpBrandLogo = Image::make($brandLogo->getRealPath())->encode('webp', 90);
            $brandLogoName = 'brands/logo_' . uniqid() . '.webp';

            Storage::disk('r2')->put($brandLogoName, (string) $webpBrandLogo);

            $shop->brand_logo = '/' . $brandLogoName;
        }

        if (empty($shop->trademark) && $request->hasFile('trademark')) {

            $file = $request->file('trademark');
            $extension = strtolower($file->getClientOriginalExtension());

            // Allowed formats
            $allowedImage = ['jpg', 'jpeg', 'png', 'webp'];
            $allowedPdf   = ['pdf'];

            if (in_array($extension, $allowedImage)) {

                // ✅ IMAGE HANDLE
                $webpTrademark = Image::make($file->getRealPath())->encode('webp', 90);
                $trademarkName = 'brands/trademark_' . uniqid() . '.webp';

                Storage::disk('r2')->put($trademarkName, (string) $webpTrademark);

                $shop->trademark = '/' . $trademarkName;

            } elseif (in_array($extension, $allowedPdf)) {

                // ✅ PDF HANDLE
                $pdfName = 'brands/trademark_' . uniqid() . '.pdf';

                Storage::disk('r2')->put($pdfName, file_get_contents($file));

                $shop->trademark = '/' . $pdfName;

            } else {
                return back()->with('error', 'Only Image or PDF allowed!');
            }
        }


        $shop->save();
        $seller = Seller::where('id', auth('seller')->id())->first();

        if ($request->signature) {
            $seller->signature = ImageManager::update('seller/', $seller->signature, 'png', $request->file('signature'));
        }
        $seller->save();

        Seller::where('id', $id)->update([
            'profile_edit_status' => 0
        ]);

        Toastr::success('Shop updated successfully!');
        return redirect()->route('seller.shop.view');
    }


    public function vacation_add(Request $request, $id)
    {
        $shop = Shop::find($id);
        $shop->vacation_status = $request->vacation_status == 'on' ? 1 : 0;
        $shop->vacation_start_date = $request->vacation_start_date;
        $shop->vacation_end_date = $request->vacation_end_date;
        $shop->vacation_note = $request->vacation_note;
        $shop->save();

        Toastr::success('Vacation mode updated successfully!');
        return redirect()->back();
    }

    public function temporary_close(Request $request)
    {
        $shop = Shop::find($request->id);
        $shop->temporary_close = $request->status == 'checked' ? 1 : 0;
        $shop->save();
        return response()->json(['status' => true], 200);
    }

}