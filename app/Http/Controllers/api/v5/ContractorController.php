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
use App\Model\ServiceProviderFirm;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use function App\CPU\translate;

class ContractorController extends Controller
{
    use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function update_individual_profile(Request $request)
    {
        $temporary_token = Str::random(40);
        if($request->type_of_work){
         $type_of_work = implode(', ', $request->type_of_work);
        }else{
           $type_of_work = 'NULL'; 
        }
       
         
        $user = User::find($request->id);
        
        $user->username = $request->username ?? NULL;
        $fullName = $request->full_name;
        $user->name = ucwords(strtolower($fullName));
       if ($request->full_name) {
           $fullName = ucwords(strtolower($request->full_name));
            $array = explode(' ', $fullName);
            if (count($array) == 1) {
                $user->f_name = $array[0];
                $user->l_name = ''; 
            } elseif (count($array) > 1) {
                $user->f_name = $array[0];
                $user->l_name = implode(' ', array_slice($array, 1));
            }
        }
        if($request->email){
        $user->email = $request->email ?? NULL;
        }
        if($request->phone){
        $user->phone = $request->phone;
        }
        //$user->is_active = 1;
        if($request->phone){
        $user->password =  bcrypt($request->phone);
        }
       // $user->temporary_token = $temporary_token;
        if($request->father_name){
        $user->father_name = $request->father_name ?? NULL;
        }
        if($request->dob){
        $user->dob = $request->dob;
        }
        if($request->adhaar_number){
        $user->adhaar_number = $request->adhaar_number;
        }
        if($request->current_address){
        $user->current_address = $request->current_address;
        }
        if($request->permanent_address){
        $user->permanent_address = $request->permanent_address;
        }
        if($request->business_name){
        $user->business_name = ucwords(strtolower($request->business_name));
        }
        if($request->total_project_done){
        $user->total_project_done = $request->total_project_done;
        }
        if($request->working_since){
        $user->working_since = $request->working_since;
        }
        if($request->team_strength){
        $user->team_strength = $request->team_strength;
        }
        if($request->description){
        $user->description = $request->description;
        }
        if($request->achievments){
        $user->achievments = $request->achievments;
        }
        if($request->state){
        $user->state = $request->state;
        }
        if($request->distric){
        $user->distric = $request->distric;
        }
        
        $user->city = $request->city;
        
        if($request->zip){
        $user->zip = $request->zip;
        }
        if($request->working_location){
        $user->working_location = $request->working_location;
        }
        if($request->radius_of_working_area_in_mm){
        $user->radius_of_working_area_in_mm = $request->radius_of_working_area_in_mm;
        }
        
        if($request->service_provider_role){
         if($request->service_provider_role == 3)
        {
           $user->role_name = 'Contractor';  
           $user->role = 3;  
        }elseif ($request->service_provider_role == 4) {
           $user->role_name = 'Architect';  
           $user->role = 4;  
        }elseif ($request->service_provider_role == 5) {
           $user->role_name = 'Interior Designer';  
           $user->role = 5;  
        }
        }
        if($type_of_work){
        $user->type_of_work = $type_of_work;
        } 
        if($request->otp){
        $user->remember_token = $request->otp;
        }
        if($request->longitude){
        $user->longitude = $request->longitude;
        }
        if($request->latitude){
        $user->latitude = $request->latitude;
        }
        
        if($request->serviceTypeId){
            $serviceTypeIdArray = is_array($request->serviceTypeId) ? $request->serviceTypeId : json_decode($request->serviceTypeId, true);
            if (is_array($serviceTypeIdArray)) {
                $stringServiceTypeIds = array_map('strval', $serviceTypeIdArray);
                $user->serviceTypeId = json_encode($stringServiceTypeIds);
            }
            
        }
        
        if($request->file('profile_image')){
        $user->image = ImageManager::upload('service-provider/profile/', 'png', $request->file('profile_image'));
        }
        if($request->file('adhaar_front_image')){
        $user->adhaar_front_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_front_image'));
        }
        if($request->file('adhaar_back_image')){
        $user->adhaar_back_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_back_image'));
        }
        if($request->file('banner_image')){
         $banner_image = ImageManager::upload('banner/', 'png', $request->file('banner_image'));
         $user->banner_image = asset('storage/app/public/banner/' . $banner_image);
         }
         //contact $firmDetails
         if($request->whatsapp_number){
        $user->whatsapp_number = $request->whatsapp_number;
         }
         if($request->website){
        $user->website = $request->website;
         }
         if($request->insta_link){
        $user->insta_link = $request->insta_link;
         }
         if($request->youtube_link){
        $user->youtube_link = $request->youtube_link;
         }
         if($request->facebook_link){
        $user->facebook_link = $request->facebook_link;
         }
        $user->save(); 
        
        $data = User::with('firm')->where('id',$user->id)->first();

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        
         
                $data->image = asset('storage/app/public/service-provider/profile/' . $data->image);
                $data->adhaar_front_image = asset('storage/app/public/service-provider/' . ($data->adhaar_front_image ?? ''));
                $data->adhaar_back_image = asset('storage/app/public/service-provider/' . ($data->adhaar_back_image ?? ''));
              
        
        return response()->json([
                'message' => 'record updated successfully',
                'token' => $token,
                'data' => $data,
                'status' => true
            ], 200);
           
    }
   
    public function update_firm_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'firm_name' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
    
        $temporary_token = Str::random(40);
        $type_of_work = $request->type_of_work ? implode(', ', $request->type_of_work) : null;
    
        $user = User::find($request->id);
        $user->username = $request->username;
        $user->name = $request->name;
        if ($request->name) {
            $array = explode(' ', $request->name);
            $user->f_name = $array[0];
            $user->l_name = $array[1] ?? null;
        }
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->is_active = 1;
        $user->password = bcrypt($request->phone);
        $user->temporary_token = $temporary_token;
        $user->state = $request->state;
        $user->distric = $request->distric;
        $user->city = $request->city;
        $user->zip = $request->pincode;
        $user->working_location = $request->working_location;
        $user->radius_of_working_area_in_mm = $request->radius_of_working_area_in_mm;
        $user->role_name = 'contractor';
        $user->role = 3;
        $user->type_of_work = $type_of_work;
        $user->remember_token = $request->otp;
        $user->longitude = $request->longitude;
        $user->latitude = $request->latitude;
        
            //contact $firmDetails
        $user->whatsapp_number = $request->whatsapp_number;
        $user->website = $request->website;
        $user->insta_link = $request->insta_link;
        $user->youtube_link = $request->youtube_link;
        $user->facebook_link = $request->facebook_link;
    
        $user->save();
    
        $firmDetails = ServiceProviderFirm::where('provider_id', $request->id)->first();
        if (!$firmDetails) {
            $firmDetails = new ServiceProviderFirm();
            $firmDetails->provider_id = $request->id;
        }
    
        $firmDetails->name = $request->name;
        $firmDetails->firm_name = $request->firm_name;
        $firmDetails->firm_address = $request->firm_address;
        $firmDetails->role_in_firm = $request->role_in_firm;
        $firmDetails->gstin = $request->gstin;
        $firmDetails->pan = $request->pan;
        $firmDetails->date_of_incorporation = $request->date_of_incorporation;
        $firmDetails->gst_image = ImageManager::upload('service-provider/', 'png', $request->file('gst_image'));
        $firmDetails->pan_image = ImageManager::upload('service-provider/', 'png', $request->file('pan_image'));
        $firmDetails->number_of_team = $request->number_of_team;
        $firmDetails->about_work = $request->about_work;
        $firmDetails->specialization_in = $request->specialization_in;
        $firmDetails->work_done_yet_image = ImageManager::upload('service-provider/', 'png', $request->file('work_done_yet_image'));
        $firmDetails->bank_name = $request->bank_name;
        $firmDetails->ifsc_code = $request->ifsc_code;
        $firmDetails->branch = $request->branch;
        $firmDetails->save();
    
        $data = User::with('firm')->where('id', $user->id)->first();
    
        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
    
        $token = $user->createToken('LaravelAuthApp')->accessToken;
    

        $data->image = asset('storage/app/public/service-provider/profile/' . $data->image);
        $data->adhaar_front_image = asset('storage/app/public/service-provider/' . ($data->adhaar_front_image ?? ''));
        $data->adhaar_back_image = asset('storage/app/public/service-provider/' . ($data->adhaar_back_image ?? ''));
        
        
        return response()->json([
            'message' => 'record updated successfully',
            'token' => $token,
            'data' => $data,
            'status' => true
        ], 200);
    }
   
    public function update_profile(Request $request)
    {
       $user = array();
       
        $temporary_token = Str::random(40);
        if($request->username)
        {
            $user['username'] = $request->username;
        }
         
        if($request->full_name){
            
             $user['name'] = $request->full_name;
              $array = explode(' ', $request->full_name);
             $user['f_name'] = $array[0];
             $user['l_name'] = $array[1];
        } 
         
       if($request->email){
           $user['email'] = $request->email;
       }
       
       if($request->phone){
            $user['phone'] = $request->phone;
             $user['password'] =  bcrypt($request->phone);
       }
        
        $user['temporary_token'] = $temporary_token;
        
        if($request->father_name){
             $user['father_name'] = $request->father_name;
        }
        
        if($request->dob){
             $user['dob'] = $request->dob;
        }
         
       if($request->adhaar_number){
           $user['adhaar_number'] = $request->adhaar_number;
       }
        
        if($request->current_address){
             $user['current_address'] = $request->current_address;
        }
        
        if($request->permanent_address){
             $user['permanent_address'] = $request->permanent_address;
        }
       
       if($request->work_role){
            $user['work_role'] = $request->work_role;
       }
        
       if($request->tools){
            $user['tools'] = $request->tools;
       }
       
       if($request->payout_ways){
           $user['payout_ways'] = $request->payout_ways;
       }
        
        if($request->payout_amount){
        $user['payout_amount'] = $request->payout_amount;
        }
        
        if($request->state){
        $user['state'] = $request->state;
        }
        if($request->distric){
        $user['distric'] = $request->distric;
        }
       
        $user['city'] = $request->city;
      
         if($request->zip){
        $user['zip'] = $request->zip;
        }
        if($request->working_location){
            $user['working_location'] = $request->working_location;
        }
        if($request->radius_of_working_area_in_mm){
           $user['radius_of_working_area_in_mm'] = $request->radius_of_working_area_in_mm;
        }
        
        if($request->refrence_1){
        $user['refrence_1'] = $request->refrence_1;
        }
        
        if($request->refrence_2){
        $user['refrence_2'] = $request->refrence_2;
        }
     
         if($request->type_of_work){
         $type_of_work = implode(', ', $request->type_of_work);
         $user['type_of_work'] = $type_of_work;
          }
        
        
        if($request->file('profile_image')){
        $user['image'] = ImageManager::upload('service-provider/profile/', 'png', $request->file('profile_image'));
        }
        
        if($request->file('adhaar_front_image')){
        $user['adhaar_front_image'] = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_front_image'));
        }
        
        if($request->file('adhaar_back_image')){
        $user['adhaar_back_image'] = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_back_image'));
        }
        if($request->file('banner_image')){
         $banner_image = ImageManager::upload('banner/', 'png', $request->file('banner_image'));
         $user->banner_image = asset('storage/app/public/banner/' . $banner_image);
         }
        
        User::where(['id' => $request->id])->update($user);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

}