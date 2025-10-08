<?php

namespace App\Http\Controllers\api\v5\auth;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use App\Model\ServiceProviderFirm;
use App\Model\ProviderWalletHistory;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;

class RegistrationController extends Controller
{
    
     
    
    
    public function register_worker(Request $request)
    {
      
       /* $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'dob' => 'required',
            'adhaar_number' => 'required',
           
            
       
        ]);*/
        
        

       /* if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }*/
        $temporary_token = Str::random(40);
        if($request->type_of_work){
         $type_of_work = implode(', ', $request->type_of_work);
        }else{
           $type_of_work = 'NULL'; 
        }
       
          $userExist = User::Where('phone', $request->phone)->first();
            
            if($userExist != true){
                $user = new User(); 
            }else{
               $user = User::Where('phone', $request->phone)->first();  
            }
        
       
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
        $user->email = $request->email ?? NULL;
        $user->phone = $request->phone;
        $user->is_active = 0;
        $user->password =  bcrypt($request->phone);
        $user->temporary_token = $temporary_token;
        $user->father_name = $request->father_name ?? NULL;
        $user->dob = $request->dob;
        $user->adhaar_number = $request->adhaar_number;
        $user->current_address = $request->current_address;
        $user->permanent_address = $request->permanent_address;
        
       
        $user->total_project_done = $request->total_project_done;
        $user->working_since = $request->working_since;
        
        $user->work_role = $request->work_role ?? NULL;
        $user->tools = $request->tools ?? NULL;
        $user->payout_ways = $request->payout_ways ?? NULL;
        $user->payout_amount = $request->payout_amount ?? NULL;
      
        
        $user->state = $request->state;
        $user->distric = $request->distric;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->working_location = $request->working_location;
        $user->radius_of_working_area_in_mm = $request->radius_of_working_area_in_mm;
    
        $user->role_name = 'Worker';  
        $user->role = 2;  
        
        
        $user->type_of_work = $type_of_work;
        $user->remember_token = $request->otp;
        $user->longitude = $request->longitude;
        $user->latitude = $request->latitude;
        
        
        if($request->serviceTypeId){
            $serviceTypeIdArray = is_array($request->serviceTypeId) ? $request->serviceTypeId : json_decode($request->serviceTypeId, true);
            if (is_array($serviceTypeIdArray)) {
                $stringServiceTypeIds = array_map('strval', $serviceTypeIdArray);
                $user->serviceTypeId = json_encode($stringServiceTypeIds);
            }
            
        }
        
        
        $user->cm_firebase_token = $request->firebase_token;
       
        
        $user->image = ImageManager::upload('service-provider/profile/', 'png', $request->file('profile_image'));
        $user->adhaar_front_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_front_image'));
        $user->adhaar_back_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_back_image'));
        $banner_image = ImageManager::upload('banner/', 'png', $request->file('banner_image'));
        $user->banner_image = asset('storage/app/public/banner/' . $banner_image);
        
        //contact $firmDetails
        $user->whatsapp_number = $request->whatsapp_number ?? NULL;
 
        
        $referrer_code =  Str::random(8);
        $user->referral_code = $referrer_code;
        
        $user->save(); 
        
        if($userExist != true){
        if(isset($request->Refferal_code)){
            $referrer = User::where('referral_code', $request->Refferal_code)->first();
            if($referrer){
            $loyalty_point_exchange_rate =  BusinessSetting::where('type', 'loyalty_point_exchange_rate')->value('value');
            $Signup_refer_point_receiver =  BusinessSetting::where('type', 'Signup_refer_point_receiver')->value('value');
            $Signup_refer_point_sender =  BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value');
            $refer_point_receiver = number_format($Signup_refer_point_receiver/$loyalty_point_exchange_rate,2);
            $refer_point_sender = number_format($Signup_refer_point_sender/$loyalty_point_exchange_rate,2);
             
                    $wdata = new ProviderWalletHistory;
                    $wdata->provider_id = $user->id;
                    $wdata->transaction_amount = $refer_point_receiver;
                    $wdata->transaction_method = "Ref. bonus";
                    $wdata->save();
                 $userup = User::find($user->id);
                 $userup->wallet_balance += $wdata->transaction_amount;
                 $userup->save();
                 
                 $ref_wdata = new CustomerWalletHistory;
                     $ref_wdata->customer_id = $referrer->id;
                     $ref_wdata->transaction_amount = $refer_point_sender;
                     $ref_wdata->transaction_type = 1;
                     $ref_wdata->transaction_method = "Ref. Earn bonus";
                     $ref_wdata->save();
                    
                  $referrer->wallet_balance += $wdata->transaction_amount;
                  $referrer->save();
            }
            
        }
        }
        
        $data = User::with('firm')->where('id',$user->id)->first();

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        
        
                $data->image = asset('storage/app/public/service-provider/profile/' . $data->image);
                $data->adhaar_front_image = asset('storage/app/public/service-provider/' . ($data->adhaar_front_image ?? ''));
                $data->adhaar_back_image = asset('storage/app/public/service-provider/' . ($data->adhaar_back_image ?? ''));
               

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        
        return response()->json([
                'message' => 'successfully registered',
                'token' => $token,
                'data' => $data,
                'status' => true
            ], 200);
           
    }
    
     public function register_individual_contructor(Request $request)
    {
      
       /* $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'dob' => 'required',
            'adhaar_number' => 'required',
           
            
       
        ]);*/
        
        

       /* if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }*/
        $temporary_token = Str::random(40);
        if($request->type_of_work){
         $type_of_work = implode(', ', $request->type_of_work);
        }else{
           $type_of_work = 'NULL'; 
        }
       
          $userExist = User::Where('phone', $request->phone)->first();
            
            if($userExist != true){
                $user = new User(); 
            }else{
               $user = User::Where('phone', $request->phone)->first();  
            }
        
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
        $user->email = $request->email ?? NULL;
        $user->phone = $request->phone;
        $user->is_active = 0;
        $user->password =  bcrypt($request->phone);
        $user->temporary_token = $temporary_token;
        $user->father_name = $request->father_name ?? NULL;
        $user->dob = $request->dob;
        $user->adhaar_number = $request->adhaar_number;
        $user->current_address = $request->current_address;
        $user->permanent_address = $request->permanent_address;
        
        $user->business_name = ucwords(strtolower($request->business_name));
        $user->total_project_done = $request->total_project_done;
        $user->working_since = $request->working_since;
        $user->team_strength = $request->team_strength;
        $user->description = $request->description;
        $user->achievments = $request->achievments;
        
        
        
        $user->state = $request->state;
        $user->distric = $request->distric;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->working_location = $request->working_location;
        $user->radius_of_working_area_in_mm = $request->radius_of_working_area_in_mm;
        
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
           
        $user->type_of_work = $type_of_work;
        $user->remember_token = $request->otp;
        $user->longitude = $request->longitude;
        $user->latitude = $request->latitude;
       
        if($request->serviceTypeId){
            $serviceTypeIdArray = is_array($request->serviceTypeId) ? $request->serviceTypeId : json_decode($request->serviceTypeId, true);
            if (is_array($serviceTypeIdArray)) {
                $stringServiceTypeIds = array_map('strval', $serviceTypeIdArray);
                $user->serviceTypeId = json_encode($stringServiceTypeIds);
            }
            
        }
        
        $user->cm_firebase_token = $request->firebase_token;
        $user->image = ImageManager::upload('service-provider/profile/', 'png', $request->file('profile_image'));
        $user->adhaar_front_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_front_image'));
        $user->adhaar_back_image = ImageManager::upload('service-provider/', 'png', $request->file('adhaar_back_image'));
        $banner_image = ImageManager::upload('banner/', 'png', $request->file('banner_image'));
        $user->banner_image = asset('storage/app/public/banner/' . $banner_image);
        
         //contact $firmDetails
        $user->whatsapp_number = $request->whatsapp_number ?? null;
        $user->website = $request->website ?? null;
        $user->insta_link = $request->insta_link ?? null;
        $user->youtube_link = $request->youtube_link ?? null;
        $user->facebook_link = $request->facebook_link ?? null;
       
        $referrer_code =  Str::random(8);
        $user->referral_code = $referrer_code;
        
        $user->save(); 
        if($userExist != true){
        if(isset($request->Refferal_code)){
            $referrer = User::where('referral_code', $request->Refferal_code)->first();
            if($referrer){
                
                $loyalty_point_exchange_rate =  BusinessSetting::where('type', 'loyalty_point_exchange_rate')->value('value');
            $Signup_refer_point_receiver =  BusinessSetting::where('type', 'Signup_refer_point_receiver')->value('value');
            $Signup_refer_point_sender =  BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value');
            $refer_point_receiver = number_format($Signup_refer_point_receiver/$loyalty_point_exchange_rate,2);
            $refer_point_sender = number_format($Signup_refer_point_sender/$loyalty_point_exchange_rate,2);
             
                    $wdata = new ProviderWalletHistory;
                    $wdata->provider_id = $user->id;
                    $wdata->transaction_amount = $refer_point_receiver;
                    $wdata->transaction_method = "Ref. bonus";
                    $wdata->save();
                 $userup = User::find($user->id);
                 $userup->wallet_balance += $wdata->transaction_amount;
                 $userup->save();
                 
                 $ref_wdata = new CustomerWalletHistory;
                     $ref_wdata->customer_id = $referrer->id;
                     $ref_wdata->transaction_amount = $refer_point_sender;
                     $ref_wdata->transaction_type = 1;
                     $ref_wdata->transaction_method = "Ref. Earn bonus";
                     $ref_wdata->save();
                    
                  $referrer->wallet_balance += $wdata->transaction_amount;
                  $referrer->save();
             
                  
            }
            
        }
        }
        
     
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
                'message' => 'successfully registered',
                'token' => $token,
                'data' => $data,
                'status' => true
            ], 200);
          
    }
    
     public function register_contractor_firm(Request $request)
    {
       
      /*  $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'firm_name' => 'required',
        ]);*/
        
        

       /* if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }*/
        $temporary_token = Str::random(40);
        if($request->type_of_work){
         $type_of_work = implode(', ', $request->type_of_work);
        }else{
           $type_of_work = 'NULL'; 
        }
       
          $userExist = User::Where('phone', $request->phone)->first();
            
           if($userExist != true){
                $user = new User(); 
            }else{
               $user = User::Where('phone', $request->phone)->first();  
            }
        
       
        $user->username = $request->username;
        $user->name = $request->name;
        if ($request->full_name) {
            $array = explode(' ', $request->full_name);
            if (count($array) == 1) {
                $user->f_name = $array[0];
                $user->l_name = ''; 
            } elseif (count($array) > 1) {
                $user->f_name = $array[0];
                $user->l_name = implode(' ', array_slice($array, 1));
            }
        }
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->is_active = 0;
        $user->password =  bcrypt($request->phone);
        $user->temporary_token = $temporary_token;
       
        $user->state = $request->state;
        $user->distric = $request->distric;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->working_location = $request->working_location;
        $user->radius_of_working_area_in_mm = $request->radius_of_working_area_in_mm;
       
        
           $user->role_name = 'contractor';  
           $user->role = 3; 
      
        $user->type_of_work = $type_of_work;
        $user->remember_token = $request->otp;
        $user->longitude = $request->longitude;
        $user->latitude = $request->latitude;
        if($request->serviceTypeId){
            $serviceTypeIdArray = is_array($request->serviceTypeId) ? $request->serviceTypeId : json_decode($request->serviceTypeId, true);
            if (is_array($serviceTypeIdArray)) {
                $stringServiceTypeIds = array_map('strval', $serviceTypeIdArray);
                $user->serviceTypeId = json_encode($stringServiceTypeIds);
            }
            
        }
         //contact $firmDetails
        $user->whatsapp_number = $request->whatsapp_number;
        $user->website = $request->website;
        $user->insta_link = $request->insta_link;
        $user->youtube_link = $request->youtube_link;
        $user->facebook_link = $request->facebook_link;
         
        
        $user->save(); 
        
        $firmDetails = new ServiceProviderFirm();
        $firmDetails->provider_id = $user->id;
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
                 $banner_image = ImageManager::upload('banner/', 'png', $request->file('banner_image'));
                $user->banner_image = asset('storage/app/public/banner/' . $banner_image);
               
        
        return response()->json([
                'message' => 'successfully registered',
                'token' => $token,
                'data' => $data,
                'status' => true
            ], 200);
           /* }else{
                return response()->json([
                'message' => 'Email or phone number already exist !',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
            }*/
    }

   
    
}
