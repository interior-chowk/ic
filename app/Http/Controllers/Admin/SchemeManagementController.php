<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Product;
use App\Model\Membership;
use App\Model\SchemeManagement;
use App\Model\Seller;
use App\Model\Brand;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use function App\CPU\translate;

class SchemeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request->search;
        $products = Product::where('status', 1)->pluck('name', 'id');
        $plans = Membership::where('status', 1)->pluck('plan_name', 'id');
        $brands = Brand::where('status', 1)->pluck('name', 'id');
        $sellers = Seller::where('status', 'approved')->pluck('f_name' , 'id');
        $scheme = SchemeManagement::when(isset($request->search) && !empty($request->search), function ($query) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $query->where('scheme_name', 'like', "%{$value}%")
                        ->orWhere('rewards', 'like', "%{$value}%")
                        ->orWhere('puchase_target_amount', 'like', "%{$value}%");
                        // Add other fields to search here as needed
                }
            })
            ->latest()
            ->paginate(Helpers::pagination_limit())
            ->appends($query_param);
    
        return view('admin-views.scheme-management.index', compact('scheme', 'search', 'plans', 'products', 'sellers', 'brands'));
    }

    public function store(Request $request)
    {
           
        $scheme = new SchemeManagement();
        $scheme->scheme_name = $request->scheme_name;
        $scheme->scheme_type = $request->scheme_type;
        $scheme->plan_id = $request->plan_id;
        $scheme->puchase_target_amount = $request->puchase_target_amount;
        $scheme->duration = $request->duration;
        $scheme->rewards = $request->rewards;
        $scheme->isActive = $request->isActive;
        $scheme->products_id = json_encode($request->products_id);
        $scheme->brand_ids = json_encode($request->brand_ids);
        $scheme->seller_ids = json_encode($request->seller_ids);
        $scheme->Description = $request->Description;
        
        $scheme->save();
    
        Toastr::success('Scheme added successfully!');
        return back(); 
    }

    public function edit($id)
    {
        $sm = SchemeManagement::find($id);
        $products = Product::where('status', 1)->pluck('name', 'id');
        $plans = Membership::where('status', 1)->pluck('plan_name', 'id');
         $brands = Brand::where('status', 1)->pluck('name', 'id');
        $sellers = Seller::where('status', 'approved')->pluck('f_name' , 'id');
        if(!$sm){
            Toastr::error('Invalid Scheme!');
            return redirect()->route('admin.Scheme_management.add-new');
        }
        return view('admin-views.scheme-management.edit', compact('sm', 'plans', 'products','sellers', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $scheme =  SchemeManagement::find($id);
        $scheme->scheme_name = $request->scheme_name;
        $scheme->plan_id = $request->plan_id;
        $scheme->scheme_type = $request->scheme_type;
        $scheme->puchase_target_amount = $request->puchase_target_amount;
        $scheme->duration = $request->duration;
        $scheme->rewards = $request->rewards;
        $scheme->isActive = $request->isActive;
        $scheme->products_id = json_encode($request->products_id);
            $scheme->brand_ids = json_encode($request->brand_ids);
        $scheme->seller_ids = json_encode($request->seller_ids);
        $scheme->Description = $request->Description;
        
        $scheme->save();

        Toastr::success('Scheme updated successfully!');
        return redirect()->route('admin.Scheme_management.add-new');
    }

    public function status(Request $request)
    {
        $coupon = SchemeManagement::find($request->id);
        $coupon->isActive = $request->status;
        $coupon->save();
        Toastr::success('Scheme status updated!');
        return back();
    }

    public function delete($id)
    {
        $coupon = SchemeManagement::find($id);
        $coupon->delete();
        Toastr::success('Scheme deleted successfully!');
        return back();
    }

}