<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\CustomerManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCountryCode;
use App\Model\DeliveryZipCode;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ShippingAddress;
use App\Model\SupportTicket;
use App\Model\SupportTicketConv;
use App\Model\Wishlist;
use App\Traits\CommonTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use function App\CPU\translate;

class LocationController extends Controller
{
    use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

   public function get_all_city(Request $request)
    {
        
       
               $results = DB::table('cities')->get();
            
        if ($results) {
             
           $record = DB::table('cities')->orderBy('name')->get();
            return response()->json([
                'message' => 'Success',
                'token' => '',
                'data' => $record,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Record not found',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
        
    }

    public function get_city(Request $request)
    {
        
       
               $results = DB::table('cities')->where('state_id',$request->state_id)->first();
            
        if ($results) {
             
           $record = DB::table('cities')->where('state_id',$request->state_id)->get();
            return response()->json([
                'message' => 'Success',
                'token' => '',
                'data' => $record,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Record not found',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
        
    }
        
    public function get_pincode(Request $request)
    {
        
       
               $results = DB::table('pincodes')->where('city_id',$request->city_id)->first();
            
        if ($results) {
             
           $record = DB::table('pincodes')->where('city_id',$request->city_id)->get();
            return response()->json([
                'message' => 'Success',
                'token' => '',
                'data' => $record,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Record not found',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
     

    }
    
    public function get_all_state(Request $request)
    {
        
       
               $results = DB::table('states')->get();
            
        if ($results) {
             
           
            return response()->json([
                'message' => 'Success',
                'token' => '',
                'data' => $results,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Record not found',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
     

    }
    

  
}
