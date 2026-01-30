<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Model\BusinessSetting;
use App\Model\Product;
use App\Model\Seller;
use App\Model\Brand;
use App\Http\Controllers\Controller;
use App\Model\ServiceProviderPlan;
use App\Model\SchemeManagement;
use Illuminate\Support\Facades\View;
use App\Model\Membership;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ProviderWalletHistory;
use App\User;
use Carbon\Carbon;
use App\Traits\CommonTrait;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;

class MembershipController extends Controller
{
    
    use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
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
        $plan = new ServiceProviderPlan();
        $plan->provider_id = $request->user()->id;
        $plan->membership_id = $request->plan_id;
        $plan->amount = $request->amount;
        $plan->transaction_id = $request->transaction_id;
        $plan->status = 1;
        $plan->save();
        $member =  Membership::find($request->plan_id);
        if($member){
        $wdata = new ProviderWalletHistory;
                    $wdata->provider_id = $request->user()->id;
                    $wdata->transaction_amount = $member->reward_value;
                    $wdata->transaction_method = "Plan Reward";
                    $wdata->save();
                 $userup = User::find($request->user()->id);
                 $userup->wallet_balance += $member->reward_value;
                 $userup->save();
        }
    
              return response()->json([
                'message' => 'Success',
                'status' => true
            ], 200);
        
    }
    
    public function plan_list(Request $request)
    {
      $plan =  Membership::where('status',1)->get();
      $plan->makeHidden(['status']);
      if($plan)
      {
            $plan = $plan->map(function($item) {
            
            $item->logo = $item->logo == 1 ? 'Yes' : 'No';
            $item->trusted_partner_tag = $item->trusted_partner_tag == 1 ? 'Yes' : 'No';
            $item->profile_image = $item->profile_image == 1 ? 'Yes' : 'No';
            $item->contact_no_show = $item->contact_no_show == 1 ? 'Yes' : 'No';
            $item->free_2d_design = $item->free_2d_design == 1 ? 'Yes' : 'No';
            $item->free_3d_design = $item->free_3d_design == 1 ? 'Yes' : 'No';
            $item->rewards_on_self_purchase = $item->rewards_on_self_purchase == 1 ? 'Yes' : 'No';
            $item->rewards_on_client_purchase = $item->rewards_on_client_purchase == 1 ? 'Yes' : 'No';
            $item->advertisement = $item->advertisement == 1 ? 'Yes' : 'No';
            $item->scheme_participation = $item->scheme_participation == 1 ? 'Yes' : 'No';
            $item->mail_id = $item->mail_id == 1 ? 'Yes' : 'No';
            $item->whatapp_contact = $item->whatapp_contact == 1 ? 'Yes' : 'No';
            $item->social_media_link = $item->social_media_link == 1 ? 'Yes' : 'No';
            $item->website = $item->website == 1 ? 'Yes' : 'No';
            
        
            return $item;
        });
          
        return response()->json(['plan-list' => $plan],200);
        
      }else
      {
       return response()->json(['message' => 'Plan not exit !'],200);  
      }
      
    }
    
    public function scheme_progress(Request $request)
    {
        if($request->membership_id){
        $res = SchemeManagement::where('isActive',1)->where('plan_id',$request->membership_id)->get();
        }else{
        $res = SchemeManagement::where('isActive',1)->get();    
        }
        
        if($res != NULL){
            $totalOrderAmount = Order::where('customer_id', $request->user()->id)->sum('order_amount');
            if($totalOrderAmount)
            {
                $res = $res->map(function ($order) use ($totalOrderAmount) {
                    $order->total_amount_order = $totalOrderAmount;
                   
                    $order->remaining_amount = $order->puchase_target_amount - $totalOrderAmount;
                    
                    return $order;
                });
            }
          return response()->json([
                'message' => translate('service provider scheme'),
                'data' => $res,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'not found data',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }
    
    public function scheme_progress_all(Request $request)
    {
       
       $totalAmount = 0;
       $schemes = SchemeManagement::where('isActive', 1)->get();
       //schemes = SchemeManagement::where('isActive', 1)->where('id',$request->scheme_id)->get();
       $schemes_data = [];

        foreach ($schemes as $scheme) {
            if ($scheme->scheme_type == 2) {
                $seller_ids = json_decode($scheme->seller_ids);
        
                
                if (is_array($seller_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                    $orderdetails = [];
                    foreach ($seller_ids as $seller_id) {
                        $orders = Order::where('seller_id', $seller_id)->get();
                        foreach ($orders as $order) {
                            $totalAmount += $order->order_amount;
                           $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                        }
                        $details[] = ['seller_id' => $seller_id , 'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount;
                    }
                    
                    $scheme->total_amount_order = $totalAmount_progress;
                   if($scheme->puchase_target_amount - $totalAmount_progress > 0){
                    $scheme->remaining_amount = $scheme->puchase_target_amount - $totalAmount_progress;
                   }
                   
                }
                
               
            }
            
             elseif ($scheme->scheme_type == 3) {
                $products_ids = json_decode($scheme->products_id);
        
                
                if (is_array($products_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                     $orderdetails = [];
                    foreach ($products_ids as $products_id) {
                        $orderDetails = OrderDetail::where('product_id', $products_id)->get();
                        foreach ($orderDetails as $orderDetail) {
                            $order = Order::where('id', $orderDetail->order_id)->first();
                            if ($order) {
                                $totalAmount += $order->order_amount;
                                 $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                            }
                        }
                        $details[] = ['product_id' => $products_id , 'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount;
                        
                    }
                     $scheme->total_amount_order = $totalAmount_progress;
                   if($scheme->puchase_target_amount - $totalAmount_progress > 0){
                    $scheme->remaining_amount = $scheme->puchase_target_amount - $totalAmount_progress;
                   }
                  
                }
                
            }
        
            elseif ($scheme->scheme_type == 1) {
                $brand_ids = json_decode($scheme->brand_ids);
        
                
                if (is_array($brand_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                     $orderdetails = [];
                    foreach ($brand_ids as $brand_id) {
                        $orderDetails = OrderDetail::where('product_details->brand_id', $brand_id)->get();
                        foreach ($orderDetails as $orderDetail) {
                            $order = Order::where('id', $orderDetail->order_id)->first();
                            if ($order) {
                                $totalAmount += $order->order_amount;
                                 $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                            }
                        }
                        $details[] = ['brand_id' => $brand_id , 'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount; 
                        
                    }
                    $scheme->total_amount_order = $totalAmount_progress;
                   if($scheme->puchase_target_amount - $totalAmount_progress > 0){
                    $scheme->remaining_amount = $scheme->puchase_target_amount - $totalAmount_progress;
                   }
                  
                }
                
            }
             $schemes_data[] = $scheme;
        }

        if($schemes){
           
          return response()->json([
                'message' => translate('scheme transaction'),
                'data' => $schemes_data,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'not found data',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }
    
    public function scheme_transction(Request $request)
    {
       $totalAmount = 0;
       // $schemes = SchemeManagement::where('isActive', 1)->get();
       $schemes = SchemeManagement::where('isActive', 1)->where('id',$request->scheme_id)->get();
       $schemes_data = [];

        foreach ($schemes as $scheme) {
            if ($scheme->scheme_type == 2) {
                $seller_ids = json_decode($scheme->seller_ids);
        
                
                if (is_array($seller_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                    $orderdetails = [];
                    foreach ($seller_ids as $seller_id) {
                        $orders = Order::where('seller_id', $seller_id)->get();
                        $seller_info = Seller::where('id',$seller_id)->first();
                        $seller_full_name = ' ';
                        if($seller_info){
                        if($seller_info->f_name && $seller_info->l_name){
                        $seller_full_name = $seller_info->f_name.' '.$seller_info->l_name;
                        }
                        }
                        foreach ($orders as $order) {
                            $totalAmount += $order->order_amount;
                           $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                        }
                        $details[] = ['seller_id' => $seller_id ,'name' => $seller_full_name ,'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount;
                    }
                    
                    $scheme->order_details = $orderdetails;
                    $scheme->details = $details;
                    $scheme->progress_amount = number_format($totalAmount_progress, 2, '.', '');
                   
                }
            }
            
             elseif ($scheme->scheme_type == 3) {
                $products_ids = json_decode($scheme->products_id);
        
                
                if (is_array($products_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                     $orderdetails = [];
                    foreach ($products_ids as $products_id) {
                        $orderDetails = OrderDetail::where('product_id', $products_id)->get();
                        foreach ($orderDetails as $orderDetail) {
                            $order = Order::where('id', $orderDetail->order_id)->first();
                            if ($order) {
                                $totalAmount += $order->order_amount;
                                 $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                            }
                        }
                        $product_data = DB::table('products')->where('id',$products_id)->first();
                        $product_price = 0;
                        if($product_data){
                        if($product_data->discount_type == 'percent')
                        {
                            $product_price = $product_data->unit_price - (($product_data->unit_price*$product_data->discount)/100);
                        }else{
                            $product_price = $product_data->unit_price - $product_data->discount;
                        }
                        }
                        $details[] = ['product_id' => $products_id ,'name' => ($product_data) ? $product_data->name : '','slug' => ($product_data) ? $product_data->slug : '', 'price'=>$product_price ,'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount;
                        
                    }
                    
                     $scheme->order_details = $orderdetails;
                     $scheme->details = $details;
                    $scheme->progress_amount = number_format($totalAmount_progress, 2, '.', '');
                  
                }
                
            }
        
            elseif ($scheme->scheme_type == 1) {
                $brand_ids = json_decode($scheme->brand_ids);
        
                
                if (is_array($brand_ids)) {
                    $totalAmount = 0; 
                    $totalAmount_progress = 0; 
                    $details = [];
                     $orderdetails = [];
                    foreach ($brand_ids as $brand_id) {
                        $orderDetails = OrderDetail::where('product_details->brand_id', $brand_id)->get();
                        $brand_info = Brand::where('id',$brand_id)->where('status',1)->first();
                        foreach ($orderDetails as $orderDetail) {
                            $order = Order::where('id', $orderDetail->order_id)->first();
                            if ($order) {
                                $totalAmount += $order->order_amount;
                                 $orderdetails[] = ['order_id' => $order->id , 'total_amount'=>number_format($order->order_amount, 2, '.', '')];  
                            }
                        }
                        $details[] = ['brand_id' => $brand_id , 'name'=> $brand_info->name , 'total_amount'=>number_format($totalAmount, 2, '.', '')];
                        $totalAmount_progress +=   $totalAmount; 
                        
                    }
                     $scheme->order_details = $orderdetails;
                     $scheme->details = $details;
                    $scheme->progress_amount = number_format($totalAmount_progress, 2, '.', '');
                  
                }
                
            }
             $schemes_data[] = $scheme;
        }

        if($schemes){
           
          return response()->json([
                'message' => translate('scheme transaction'),
                'data' => $schemes_data,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'not found data',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }
    
    private  function unique_assoc_array($array)
    {
        $temp_array = [];
        foreach ($array as $val) {
            $temp_array[serialize($val)] = $val;
        }
        return array_values($temp_array);
    }   
}