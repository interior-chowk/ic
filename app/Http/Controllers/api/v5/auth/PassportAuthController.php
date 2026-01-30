<?php

namespace App\Http\Controllers\api\v5\auth;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Model\ServiceProviderPlan;
use function App\CPU\translate;

class PassportAuthController extends Controller
{
    
    public function email_check(Request $request)
    {
        $userCustomer = User::where('email', $request->email)->where('role', NULL)->first();
        
        $userProvider = User::where('email', $request->email)->whereIn('role', [2,3,4,5])->first();
       
        if ($userProvider != null) {
             return response()->json([
                'message' => translate('your email already registered with us.'),
                'isCustomerExist' => false,
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
            
        } elseif($userCustomer != null){
       
            return response()->json([
                'message' => translate('You already registered as a customer. By filling this form, your profile will be converted into Service Provider. Please confirm.'),
                'isCustomerExist' => true,
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
        }
        else{
           return response()->json([
                'message' => '',
                'isCustomerExist' => false,
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
           
        }
          
    }
    
    public function send_otp(Request $request)
    {
        $userCustomer = User::where('phone', $request->phone)->where('role', NULL)->first();
        
        $userProvider = User::where('phone', $request->phone)->whereIn('role', [2,3,4,5])->first();
       
        if ($userProvider != null) {
             return response()->json([
                'message' => translate('you already registered with us. Login now.'),
                'isCustomerExist' => false,
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
            
        } elseif($userCustomer != null){
        session()->forget('otp_send');
         $otp = rand(1111,9999);
         session()->put('otp_send', $otp);
         session()->put('phone', $request->phone);
         \Log::info('OTP sent: ' . $otp);
         \Log::info('Phone stored in session: ' . session('phone'));
         
         $userCustomer->temporary_token  = $otp;  
            $userCustomer->save();
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://2factor.in/API/R1/',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91'.$request->phone.'&from=INTCHK&msg=Dear%20customer%2C%20'.$otp.'%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            
            return response()->json([
                'message' => translate('You already registered as a customer. By filling this form, your profile will be converted into Service Provider. Please confirm.'),
                'isCustomerExist' => true,
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
        }
        else{
        session()->forget('otp_send');
         $otp = rand(1111,9999);
         session()->put('otp_send', $otp);
         session()->put('phone', $request->phone);
         \Log::info('OTP sent: ' . $otp);
         \Log::info('Phone stored in session: ' . session('phone'));
          $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://2factor.in/API/R1/',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91'.$request->phone.'&from=INTCHK&msg=Dear%20customer%2C%20'.$otp.'%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
          
            if($response){
               
                 return response()->json([
                'message' => 'otp has been send on mobile number',
                'isCustomerExist' => false,
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
            }else{
               
                 return response()->json([
                'message' => 'there is some issues ! otp is not sending',
                'isCustomerExist' => false,
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 403);
            }
        }
          
    }
    
    public function Verify_otp(Request $request)
    {
        $storedOtp = session()->get('otp_send');
        $storedPhone = session()->get('phone');
    
        \Log::info('Stored OTP: ' . $storedOtp);
        \Log::info('Stored Phone: ' . $storedPhone);
        \Log::info('Request OTP: ' . $request->otp);
           if ($storedOtp == $request->otp) {
               session()->forget('otp_send');
                return response()->json([
                    'message' => 'otp has been  verified successfully',
                    'token' => '',
                    'data' => (object)[],
                    'status' => true
                ], 200);
           }else{
               return response()->json([
                    'message' => 'entering wrong otp',
                    'token' => '',
                    'data' => (object)[],
                    'status' => false
                ], 403);
           }
    }
    

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request['email'];
        if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
            $medium = 'email';
        } else {
            $count = strlen(preg_replace("/[^\d]/", "", $user_id));
            if ($count >= 9 && $count <= 15) {
                $medium = 'phone';
            } else {
                $errors = [];
                array_push($errors, ['code' => 'email', 'message' => 'Invalid email address or phone number']);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $data = [
            $medium => $user_id,
            'password' => $request->password
        ];

        $user = User::where([$medium => $user_id])->first();
        $max_login_hit = Helpers::get_business_settings('maximum_login_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_login_block_time') ?? 5; //minute

        if (isset($user)) {
            $user->temporary_token = Str::random(40);
            $user->save();

            $phone_verification = Helpers::get_business_settings('phone_verification');
            $email_verification = Helpers::get_business_settings('email_verification');
            if ($phone_verification && !$user->is_phone_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }
            if ($email_verification && !$user->is_email_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }

            if(isset($user->temp_block_time ) && Carbon::parse($user->temp_block_time)->diffInSeconds() <= $temp_block_time){
                $time = $temp_block_time - Carbon::parse($user->temp_block_time)->diffInSeconds();

                $errors = [];
                array_push($errors, ['code' => 'auth-001', 'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()]);
                return response()->json([
                    'errors' => $errors
                ], 401);
            }

            if($user->is_active && auth()->attempt($data)){
                $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;

                $user->login_hit_count = 0;
                $user->is_temp_blocked = 0;
                $user->temp_block_time = null;
                $user->updated_at = now();
                $user->save();

                return response()->json(['token' => $token], 200);
            }else{
                //login attempt check start
                if(isset($user->temp_block_time ) && Carbon::parse($user->temp_block_time)->diffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($user->temp_block_time)->diffInSeconds();

                    $errors = [];
                    array_push($errors, ['code' => 'auth-001', 'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()]);
                    return response()->json([
                        'errors' => $errors
                    ], 401);

                }elseif($user->is_temp_blocked == 1 && Carbon::parse($user->temp_block_time)->diffInSeconds() >= $temp_block_time){

                    $user->login_hit_count = 0;
                    $user->is_temp_blocked = 0;
                    $user->temp_block_time = null;
                    $user->updated_at = now();
                    $user->save();

                    $errors = [];
                    array_push($errors, ['code' => 'auth-001', 'message' => translate('credentials_do_not_match_or_account_has_been_suspended')]);
                    return response()->json([
                        'errors' => $errors
                    ], 401);

                }elseif($user->login_hit_count >= $max_login_hit &&  $user->is_temp_blocked == 0){
                    $user->is_temp_blocked = 1;
                    $user->temp_block_time = now();
                    $user->updated_at = now();
                    $user->save();

                    $time= $temp_block_time - Carbon::parse($user->temp_block_time)->diffInSeconds();

                    $errors = [];
                    array_push($errors, ['code' => 'auth-001', 'message' => translate('too_many_attempts. please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()]);
                    return response()->json([
                        'errors' => $errors
                    ], 401);

                }else{

                    $user->login_hit_count += 1;
                    $user->save();

                    $errors = [];
                    array_push($errors, ['code' => 'auth-001', 'message' => translate('credentials_do_not_match_or_account_has_been_suspended')]);
                    return response()->json([
                        'errors' => $errors
                    ], 401);
                }
                //login attempt check end
            }
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Customer_not_found_or_Account_has_been_suspended')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    public function mobilelogin(Request $request)
    {
        $user = User::with('firm')->where('temporary_token', $request->otp)
            ->where('phone', $request->phone)
            ->first();
            
        if ($user != null) {
            
            if($user->is_active == 0)
            {
                return response()->json([
                'message' => 'Your account has not approved ! , please contact with admin .',
                'token' => '',
                'data' => [],
                'status' => false
            ], 200);
            }
            $user->cm_firebase_token = $request->firebase_token; 
            $user->save();
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            
            $user->image = asset('storage/app/public/service-provider/profile/' . $user->image);
            $user->adhaar_front_image = asset('storage/app/public/service-provider/' . ($user->adhaar_front_image ?? ''));
            $user->adhaar_back_image = asset('storage/app/public/service-provider/' . ($user->adhaar_back_image ?? ''));
            
            /* here we apply membership options */
            
             
            $membership_user_plan = ServiceProviderPlan::with('membership')->where('provider_id',$user->id)->where('status',1)->latest()->first();
              if($membership_user_plan && $membership_user_plan->membership){
                if($membership_user_plan->membership->profile_image == 0)
                {
                    $user->image = asset('storage/app/public/service-provider/def.png');
                }
                if($membership_user_plan->membership->contact_no_show == 0)
                {
                    $user->phone = NULL;
                }
                if($membership_user_plan->membership->mail_id == 0)
                {
                    $user->email = NULL;
                }
                if($membership_user_plan->membership->whatapp_contact == 0)
                {
                    $user->whatsapp_number = NULL;
                }
                 if($membership_user_plan->membership->social_media_link == 0)
                {
                    $user->insta_link = NULL;
                    $user->youtube_link = NULL;
                    $user->facebook_link = NULL;
                }
                 if($membership_user_plan->membership->website == 0)
                {
                    $user->website = NULL;
                }
              }else{
                 $user->image = asset('storage/app/public/service-provider/def.png');
                 $user->phone = NULL;
                 $user->email = NULL;
                 $user->whatsapp_number = NULL;
                 $user->insta_link = NULL;
                 $user->youtube_link = NULL;
                 $user->facebook_link = NULL;
                 $user->website = NULL;
              }
            
            return response()->json([
                'message' => translate('OTP_verified'),
                'token' => $token,
                'data' => $user,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid phone number or OTP',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }
    
    public function check_register_mobile(Request $request)
    {
       
        $user = User::where('phone', $request->phone)->first();
            
        if ($user != null) {
            return response()->json([
                'message' => translate('you already registered with us. Login now.'),
                'isCustomerExist' => true,
                'status' => true
            ], 200);
        } else {
           
        }
    }
    
    public function user_data(Request $request , $id)
    {
        $user = User::with('firm')->where('id',$id)
            ->first();
            
        if ($user != null) {
             
            
            return response()->json([
                'message' => translate('OTP_verified'),
                'token' => ' ',
                'data' => $user,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid phone number or OTP',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }

    public function sendotp(Request $request)
    {
        
       if($request->phone != 1234567899){
        
         $otp = rand(1111,9999);  
      
        
        $user = User::where('phone', $request->phone)->whereIn('role', [2,3,4,5])
            ->first();
        sleep(5);
        if ($user != null) {
            $user->temporary_token  = $otp;  
            $user->save();
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://2factor.in/API/R1/',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => 'module=TRANS_SMS&apikey=28ec74ef-f955-11ed-addf-0200cd936042&to=91'.$request->phone.'&from=INTCHK&msg=Dear%20customer%2C%20'.$otp.'%20is%20your%20OTP%20for%20login%2Fsignup.%20Thanks.%C2%A0Interior%C2%A0Chowk.',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        
             return response()->json([
                'message' => 'OTP Sent',
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);

           

        } else {

             return response()->json([
                'message' => 'OTP Not Sent , Service Provider not available',
                'token' => '',
                'data' => (object)[],
                'status' => false
            ], 200);
           
        }
        
        }
             return response()->json([
                'message' => 'OTP Sent',
                'token' => '',
                'data' => (object)[],
                'status' => true
            ], 200);
    }
}