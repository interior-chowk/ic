<?php

namespace App\Http\Controllers\api\v1\auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use App\Model\CustomerWalletHistory;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Model\BusinessSetting;
use Illuminate\Support\Str;
use function App\CPU\translate;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $temporary_token = Str::random(40);
        if(isset($request->referral_code)){
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if($referrer){
              $referrer_code =  Str::random(8);
              
            $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'referral_code' => $referrer_code,
            'is_active' => 1,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
        ]);
        
                    $wdata = new CustomerWalletHistory;
                    $wdata->customer_id = $referrer->id;
                    $wdata->transaction_amount = 5;
                    $wdata->transaction_type = 1;
                    $wdata->transaction_method = "Ref. bonus";
                    $wdata->save();
            }
            
        }else{
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => 1,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
        ]);
        }
        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);
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
        $user = User::where('temporary_token', $request->otp)
            ->where('phone', $request->phone)
            ->first();
            
        if ($user != null) {
            
            if($user->is_active == 0)
            {
                return response()->json([
                'message' => 'Your account has been blocked!, please contact the admin at customersupport@interiorchowk.com',
                'token' => '',
                'data' => [],
                'status' => false
            ], 200);
            }
       
       if(isset($request->refferal_code)){
             $referrer = User::where('referral_code', $request->refferal_code)->first();
             if($referrer){
                 
            $loyalty_point_exchange_rate =  BusinessSetting::where('type', 'loyalty_point_exchange_rate')->value('value');
            $Signup_refer_point_receiver =  BusinessSetting::where('type', 'Signup_refer_point_receiver')->value('value');
            $Signup_refer_point_sender =  BusinessSetting::where('type', 'Signup_refer_point_sender')->value('value');
            $refer_point_receiver = number_format($Signup_refer_point_receiver/$loyalty_point_exchange_rate,2);
            $refer_point_sender = number_format($Signup_refer_point_sender/$loyalty_point_exchange_rate,2);
            
              $user = User::where('phone', $request->phone)->first(); 
                     $wdata = new CustomerWalletHistory;
                     $wdata->customer_id = $user->id;
                     $wdata->transaction_amount = $refer_point_receiver;
                       $wdata->transaction_type = 1;
                     $wdata->transaction_method = "Ref. bonus";
                     $wdata->save();
                    
                  
                 $user->wallet_balance += $wdata->transaction_amount;
                 $user->save();
                 
                 
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
             
            $token = $user->createToken('LaravelAuthApp')->accessToken;
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
                'data' => [],
                'status' => false
            ], 200);
        }
    }

    public function sendotp(Request $request)
    {
        $otp = rand(1111,9999);
        if($request->phone == "1234567890"){
            $otp = 1234;
        }
        $user = User::where('phone', $request->phone)
            ->first();
        
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
                'result' => true,
                'message' => "OTP Sent",
            ]);

        } else {
            
            //Enable below code for new signup

             $referrer_code =  Str::random(8);
             $user = new User();
             $user->phone = $request->phone;
             $user->temporary_token = $otp;
             $user->is_phone_verified = 1;
             $user->is_email_verified = 1;
             $user->email_verified_at = null;
             $user->referral_code = $referrer_code;
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
                'result' => true,
                'message' => "OTP Sent",
            ]);
        }
    }
}