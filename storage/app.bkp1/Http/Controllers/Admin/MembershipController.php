<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Membership;
use App\Model\Seller;
use App\Model\Order;
use App\Model\BusinessSetting;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use App\Model\ServiceProviderPlan;
use Carbon\Carbon;
use function App\CPU\translate;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request->search;
        $mem = Membership::when(isset($request->search) && !empty($request->search), function ($query) use ($search) {
                $key = explode(' ', $search);
                foreach ($key as $value) {
                    $query->where('plan_name', 'like', "%{$value}%")
                        ->orWhere('reward_value', 'like', "%{$value}%")
                        ->orWhere('price', 'like', "%{$value}%");
                        // Add other fields to search here as needed
                }
            })
            ->latest()
            ->paginate(Helpers::pagination_limit())
            ->appends($query_param);
    
        return view('admin-views.membership.index', compact('mem', 'search'));
    }

      
    
     public function generate_invoice($id)
    {
        $plan = ServiceProviderPlan::with(['provider','membership'])->where('id',$id)->first();
       
        $company_phone =BusinessSetting::where('type', 'company_phone')->first()->value;
        $company_email =BusinessSetting::where('type', 'company_email')->first()->value;
        $company_name =BusinessSetting::where('type', 'company_name')->first()->value;
        $company_web_logo =BusinessSetting::where('type', 'company_web_logo')->first()->value;

       
        $mpdf_view = View::make('admin-views.membership.invoice',
            compact('company_phone', 'company_name', 'company_email', 'company_web_logo','plan')
        );
        Helpers::gen_mpdf($mpdf_view, 'plan_invoice_', $id);
    }
    
     public function store(Request $request)
        {
            $request->validate([
                'plan_name' => 'required|string',
                'plan_description' => 'required|string',
                'price' => 'required|numeric',
                'logo' => 'required|boolean',
                'trusted_partner_tag' => 'required|boolean',
                'profile_image' => 'required|boolean',
                'contact_no_show' => 'required|boolean',
                'website' => 'required|boolean',
                'social_media_link' => 'required|boolean',
                'whatapp_contact' => 'required|boolean',
                'mail_id' => 'required|integer',
                'free_2d_design' => 'required|integer',
                'free_3d_design' => 'required|integer',
                'rewards_on_self_purchase' => 'required|boolean',
                'rewards_on_client_purchase' => 'required|boolean',
                'reward_value' => 'required|numeric',
                'listing_view' => 'required|in:Posting Date Wise,Rotation Wise & Business Wise',
                'advertisement' => 'required|boolean',
                'scheme_participation' => 'required|boolean',
                'discount_on_delivery' => 'required|numeric',
                'discount_on_yearly_plan' => 'required|numeric',
            ], [
               
            ]);
        
           
        
            $member = new Membership();
            $member->plan_name = $request->plan_name;
            $member->plan_description = $request->plan_description;
            $member->price = $request->price;
            $member->validity = $request->validity;
            $member->logo = $request->logo;
            $member->trusted_partner_tag = $request->trusted_partner_tag;
            $member->profile_image = $request->profile_image;
            $member->contact_no_show = $request->contact_no_show;
            $member->website = $request->website;
            $member->social_media_link = $request->social_media_link;
            $member->whatapp_contact = $request->whatapp_contact;
            $member->mail_id = $request->mail_id;
            $member->free_2d_design = $request->free_2d_design;
            $member->free_3d_design = $request->free_3d_design;
            $member->rewards_on_self_purchase = $request->rewards_on_self_purchase;
            $member->rewards_on_client_purchase = $request->rewards_on_client_purchase;
            $member->reward_value = $request->reward_value;
            $member->listing_view = $request->listing_view;
            $member->advertisement = $request->advertisement;
            $member->scheme_participation = $request->scheme_participation;
            $member->discount_on_delivery = $request->discount_on_delivery;
            $member->discount_on_yearly_plan = $request->discount_on_yearly_plan;
        
           
            $member->save();
        
         Toastr::success('Plan added successfully!');
         return back(); 
        }


    public function edit($id)
    {
       
        $m = Membership::find($id);
        if(!$m){
            Toastr::error('Invalid Plan!');
            return redirect()->route('admin.Membership_plan.add-new');
        }
        return view('admin-views.membership.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'plan_name' => 'required|string',
                'plan_description' => 'required|string',
                'price' => 'required|numeric',
                'logo' => 'required|boolean',
                'trusted_partner_tag' => 'required|boolean',
                'profile_image' => 'required|boolean',
                'contact_no_show' => 'required|boolean',
                'website' => 'required|boolean',
                'social_media_link' => 'required|boolean',
                'whatapp_contact' => 'required|boolean',
                'mail_id' => 'required|boolean',
                'free_2d_design' => 'required|integer',
                'free_3d_design' => 'required|integer',
                'rewards_on_self_purchase' => 'required|boolean',
                'rewards_on_client_purchase' => 'required|boolean',
                'reward_value' => 'required|numeric',
                'listing_view' => 'required|in:Posting Date Wise,Rotation Wise & Business Wise',
                'advertisement' => 'required|boolean',
                'scheme_participation' => 'required|boolean',
                'discount_on_delivery' => 'required|numeric',
                'discount_on_yearly_plan' => 'required|numeric',
            ], [
               
            ]);
        
           
        
            $member =  Membership::find($id);
            $member->plan_name = $request->plan_name;
            $member->plan_description = $request->plan_description;
            $member->price = $request->price;
            $member->validity = $request->validity;
            $member->logo = $request->logo;
            $member->trusted_partner_tag = $request->trusted_partner_tag;
            $member->profile_image = $request->profile_image;
            $member->contact_no_show = $request->contact_no_show;
            $member->website = $request->website;
            $member->social_media_link = $request->social_media_link;
            $member->whatapp_contact = $request->whatapp_contact;
            $member->mail_id = $request->mail_id;
            $member->free_2d_design = $request->free_2d_design;
            $member->free_3d_design = $request->free_3d_design;
            $member->rewards_on_self_purchase = $request->rewards_on_self_purchase;
            $member->rewards_on_client_purchase = $request->rewards_on_client_purchase;
            $member->reward_value = $request->reward_value;
            $member->listing_view = $request->listing_view;
            $member->advertisement = $request->advertisement;
            $member->scheme_participation = $request->scheme_participation;
            $member->discount_on_delivery = $request->discount_on_delivery;
            $member->discount_on_yearly_plan = $request->discount_on_yearly_plan;
        
           
            $member->save();

        Toastr::success('Plan updated successfully!');
        return redirect()->route('admin.Membership_plan.add-new');

    }

    public function status(Request $request)
    {
        $coupon = Membership::find($request->id);
        $coupon->status = $request->status;
        $coupon->save();
        Toastr::success('Plan status updated!');
        return back();
    }

    public function quick_view_details(Request $request)
    {
        $coupon = Coupon::where(['added_by' => 'admin'])->find($request->id);

        return response()->json([
            'view' => view('admin-views.coupon.details-quick-view', compact('coupon'))->render(),
        ]);
    }

    public function delete($id)
    {
        $coupon = Membership::find($id);
        $coupon->delete();
        Toastr::success('plan deleted successfully!');
        return back();
    }

   
}
