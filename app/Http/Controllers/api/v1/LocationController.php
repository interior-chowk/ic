<?php

namespace App\Http\Controllers\api\v1;


use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use App\Model\ShippingAddress;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;

class LocationController extends Controller
{
    public function all_country()
    {
        $data = DB::table('countries')->get();
        if($data){
        
        return response()->json([
            'message' => 'success',
            'token' => '',
            'data' => $data,
            'status' => true
        ], 200);
        }else{
           return response()->json([
                'message' => 'not found country',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
        }
    }
    
    public function all_state()
    {
        $data = DB::table('states')->orderBy('name')->get();
        if($data){
        
        return response()->json([
            'message' => 'success',
            'token' => '',
            'data' => $data,
            'status' => true
        ], 200);
       }else{
           return response()->json([
                'message' => 'not found state',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
       }
    }
    
    public function all_city()
    {
        $data = DB::table('cities')->orderBy('name')->get();
        if($data){
        
        return response()->json([
            'message' => 'success',
            'token' => '',
            'data' => $data,
            'status' => true
        ], 200);
       }else{
           return response()->json([
                'message' => 'not found city',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
       }
    }
    
    public function all_pincode()
    {
        $data = DB::table('pincodes')->get();
        if($data){
        
            return response()->json([
                'message' => 'success',
                'token' => '',
                'data' => $data,
                'status' => true
            ], 200);
       }else{
           return response()->json([
                'message' => 'not found pincode',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
       }
    }

    public function pincode(Request $request)
    {
        // $pincode = $request->pincode;
        $pincode = DB::table('pincodes')->where('code', $request->pincode)->first();

        if (!$pincode) {
            return response()->json(['error' => 'Pincode not found'], 404); // Return 404 if pincode doesn't exist
        }
    
        // Get the city based on the pincode's city_id
        $city = DB::table('cities')->where('id', $pincode->city_id)->first();
    
        // Get the state based on the pincode's state_id
        $state = DB::table('states')->where('id', $pincode->state_id)->first(); // Assuming 'states' table is correct
    
        // Return response with pincode, city, state, and country details
        return response()->json([
            //'pincode' => $pincode,
            'city' => $city ? $city->name : null, // Check if city is found
            'state' => $state ? $state->name : null, // Check if state is found
            'country' => 'India'
        ]);
    }

    public function get_all_pincode(Request $request)
    {
        $found = DB::table('pincodes')->where('city_id', $request->city_id)->first();
        $data = DB::table('pincodes')->where('city_id', $request->city_id)->get();
           if($found){
           
            return response()->json([
                'message' => 'success',
                'token' => '',
                'data' => $data,
                'status' => true
            ], 200);
        }else{
           return response()->json([
                'message' => 'not data found',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
        }
    }
    
    public function get_all_city(Request $request)
    {
        $found = DB::table('cities')->where('state_id', $request->state_id)->first();
        $data = DB::table('cities')->where('state_id', $request->state_id)->orderBy('name')->get();
        if($found){
        
        return response()->json([
            'message' => 'success',
            'token' => '',
            'data' => $data,
            'status' => true
        ], 200);
        }else{
            return response()->json([
                    'message' => 'not data found',
                    'token' => '',
                    'data' => (object)[],
                    'status' => false
                ], 403);
        }
    }
}