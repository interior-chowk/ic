<?php

namespace App\Http\Controllers\Seller\Auth;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use App\User;
use App\Model\ShippingAddress;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class RegisterController extends Controller
{
    
    
      public function seller_registeration(Request $request)
    {
       
        
        if(isset($request->id)){
           $seller = Seller::find($request->id);
          
           return view('seller-views.auth-seller.seller-registeration-1',['sellers'=>$seller]);
        }
        return view('seller-views.auth-seller.seller-registeration');
    }

    public function seller_registeration_store(Request $request)
    {
      
   
        if(!isset($request->seller_id))
        {
           $this->validate($request, [
            'email'         => 'required|unique:sellers',
            'f_name'        => 'required',
            
            'phone'        => 'required',
            'password'      => 'required|min:8',
        ]);

        if($request->phone || $request->email){
            if(Seller::where('phone',$request->phone)->where('email',$request->email)->first())
            {
                 Toastr::error('Phone and Email Already exist !');
                 return back();
            }
            
            elseif(Seller::where('phone',$request->phone)->first())
            {
                 Toastr::error('Phone Already exist !');
                 return back();
            }
            
             elseif(Seller::where('email',$request->email)->first())
            {
                 Toastr::error('Email Already exist !');
                 return back();
            }
         }

         

        
      
        $seller = new Seller();
        $seller->f_name = $request->f_name;
        $seller->l_name = $request->l_name ?? NULL;
        $seller->phone = $request->phone;
        $seller->email = $request->email;
        $seller->password = bcrypt($request->password);
        $seller->status =  $request->status == 'approved'?'approved': "pending";
        $seller->profile_edit_status =  1;
        
        $seller->save();

        $Shop = new Shop();
        $Shop->seller_id = $seller->id;
        $Shop->save();
        }else{

            

            $seller = Seller::find($request->seller_id);
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name ?? NULL;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved'?'approved': "pending";
            $seller->profile_edit_status =  1;
            
            $seller->save();

        }
       
        $data['countries'] = DB::table('countries')->orderBy('name')->get();
        $data['states'] = DB::table('states')->orderBy('name')->get();
        $data['cities'] = DB::table('cities')->orderBy('name')->get();
        $data['pincodes'] = DB::table('pincodes')->orderBy('code')->get();

        return view('seller-views.auth-seller.seller-registeration-2',['id'=>$seller->id,'data'=>$data]);
    }

    public function seller_registeration_2(Request $request)
    {

        if ($request->isMethod('get')) {
            
            $data['countries'] = DB::table('countries')->orderBy('name')->get();
            $data['states'] = DB::table('states')->orderBy('name')->get();
            $data['cities'] = DB::table('cities')->orderBy('name')->get();
            $data['pincodes'] = DB::table('pincodes')->orderBy('code')->get();
    
            return view('seller-views.auth-seller.seller-registeration-2',['id'=>$request->id,'data'=>$data]);

        } elseif ($request->isMethod('post')) {
           
            $this->validate($request, [
            'shop_name'        => 'required',
            'shop_address'        => 'required',
            'state'        => 'required',
            'city'        => 'required',
            'pincode'        => 'required',
            'bank_branch'        => 'required',
            'acc_no'        => 'required',
            'bank_name'        => 'required',
            'ifsc'        => 'required',
           ]);
            
            $shop = Shop::where('seller_id',$request->seller_id)->first();
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;

           $country =  DB::table('countries')->where('id',$request->country)->first();
           
            // $shop->country = $country->name;
            $shop->country = 'India';
            
            $state =  DB::table('states')->where('id',$request->state)->first();
            $shop->state = $state->name;
            $city =  DB::table('cities')->where('id',$request->city)->first();
            $shop->city = $city->name;
            $pincode =  DB::table('pincodes')->where('id',$request->pincode)->first();
            $shop->pincode = $pincode->code;
            $shop->bank_branch = $request->bank_branch;
            $shop->acc_no = $request->acc_no;
            $shop->bank_name = $request->bank_name;
            $shop->ifsc = $request->ifsc;
            $seller = Seller::where('id',$request->seller_id)->first();
            $shop->contact = $seller->phone;
            $shop->save();

        }
        return view('seller-views.auth-seller.seller-registeration-3',['id'=>$request->seller_id]);
    }

    public function seller_registeration_3(Request $request)
    {
        
        
       $this->validate($request, [
            'cheque' => 'required|mimes:jpg,jpeg,png,gif,pdf',
            'gst_cert_image' => 'required|mimes:jpg,jpeg,png,gif,pdf',
            'pan_image' => 'required|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $shop = Shop::where('seller_id',$request->seller_id)->first();
       // Upload GST Certificate
        $gstCertFile = $request->file('gst_cert_image');
        $gstCertExtension = strtolower($gstCertFile->getClientOriginalExtension());
        $shop->gst_cert_image = ImageManager::upload('shop/', $gstCertExtension, $gstCertFile);
        
        // Upload PAN Image
        $panImageFile = $request->file('pan_image');
        $panImageExtension = strtolower($panImageFile->getClientOriginalExtension());
        $shop->pan_image = ImageManager::upload('shop/', $panImageExtension, $panImageFile);
        
        // Upload Cheque Image
        $chequeImageFile = $request->file('cheque');
        $chequeImageExtension = strtolower($chequeImageFile->getClientOriginalExtension());
        $shop->cheque_image = ImageManager::upload('shop/', $chequeImageExtension, $chequeImageFile);
        
        $shop->save();
       
        DB::table('seller_wallets')->insert([
            'seller_id' => $request->seller_id,
            'withdrawn' => 0,
            'commission_given' => 0,
            'total_earning' => 0,
            'pending_withdraw' => 0,
            'delivery_charge_earned' => 0,
            'collected_cash' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $seller = Seller::where('id',$request->seller_id)->first();
        
        Mail::to($seller->email)->send(new \App\Mail\SellerOnboardingMail($seller)); 

        
        if($seller->status == 'approved'){
            // Toastr::success('Shop apply successfully!');
            Toastr::success('<strong>Congratulations!</strong> Your registration has been completed successfully. Now you can <strong>LOGIN</strong> and start your seller journey with us.');
             return back();
         }else{
             //Toastr::success('Shop apply successfully!');
             Toastr::success('<strong>Congratulations!</strong> Your registration has been completed successfully. Now you can <strong>LOGIN</strong> and start your seller journey with us.');
 
             return redirect()->route('seller.auth.seller-login');
         }

    }
    
    public function send_otp(Request $request)
    {
        $seller = Seller::where('phone', $request->phone)
            ->first();
            
        if ($seller == null) {
        session()->forget('otp_send');
         $otp = rand(1111,9999);
         session()->put('otp_send', $otp);
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
                echo 1;
            }else{
                echo 0;
            }
        }else{
                echo 2;
            }
          
           return ;
    }
    
      public function Verify_otp(Request $request)
    {
       if(session()->get('otp_send') == $request->number){
           session()->forget('otp_send');
           echo 1;
       }else{
           echo 0;
       }
    }
    
    public function create()
    {
        return redirect()->route('seller-home');
        $business_mode=Helpers::get_business_settings('business_mode');
        $seller_registration=Helpers::get_business_settings('seller_registration');
        if((isset($business_mode) && $business_mode=='single') || (isset($seller_registration) && $seller_registration==0))
        {
            Toastr::warning(translate('access_denied!!'));
            return redirect('/');
        }
         $data['countries'] = DB::table('countries')->get();
         $data['states'] = DB::table('states')->get();
         $data['cities'] = DB::table('cities')->get();
         $data['pincodes'] = DB::table('pincodes')->get();
        return view(VIEW_FILE_NAMES['seller_registration'],compact('data'));
    }

    public function store(Request $request)
    {
      
      if($request->phone || $request->email){
         if(Seller::where('phone',$request->phone)->where('email',$request->email)->first())
         {
              Toastr::error('Phone and Email Already exist !');
              return back();
         }
         
         elseif(Seller::where('phone',$request->phone)->first())
         {
              Toastr::error('Phone Already exist !');
              return back();
         }
         
          elseif(Seller::where('email',$request->email)->first())
         {
              Toastr::error('Email Already exist !');
              return back();
         }
      }
      
        $this->validate($request, [
           
            'logo'          => 'required|mimes: jpg,jpeg,png,gif',
            'banner'        => 'required|mimes: jpg,jpeg,png,gif',
            

            'reg_cert_image' => 'required|mimes: jpg,jpeg,png,gif',
            'gst_cert_image' => 'required|mimes: jpg,jpeg,png,gif',
            'pan_image'      => 'required|mimes: jpg,jpeg,png,gif',

            'email'         => 'required|unique:sellers',
            'shop_address'  => 'required',
            'f_name'        => 'required',

            'shop_name'     => 'required',
            'password'      => 'required|min:8',
        ]);
     
        if($request['from_submit'] != 'admin') {
            //recaptcha validation
            $recaptcha = Helpers::get_business_settings('recaptcha');
            if (isset($recaptcha) && $recaptcha['status'] == 1) {
                try {
                    $request->validate([
                        'g-recaptcha-response' => [
                            function ($attribute, $value, $fail) {
                                $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                                $response = $value;
                                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                                $response = \file_get_contents($url);
                                $response = json_decode($response);
                                if (!$response->success) {
                                    $fail(\App\CPU\translate('ReCAPTCHA Failed'));
                                }
                            },
                        ],
                    ]);
                } catch (\Exception $exception) {
                }
            } else {
                if (strtolower($request->default_recaptcha_id_seller_regi) != strtolower(Session('default_recaptcha_id_seller_regi'))) {
                    Session::forget('default_recaptcha_id_seller_regi');
                    return back()->withErrors(\App\CPU\translate('Captcha Failed'));
                }
            }
        }

        DB::transaction(function ($r) use ($request) {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name ?? NULL;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved'?'approved': "pending";
            //$seller->status =  'approved';
          
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;

           $country =  DB::table('countries')->where('id',$request->country)->first();
           
            // $shop->country = $country->name;
            $shop->country = 'India';
            
            $state =  DB::table('states')->where('id',$request->state)->first();
            $shop->state = $state->name;
            $city =  DB::table('cities')->where('id',$request->city)->first();
            $shop->city = $city->name;
            $pincode =  DB::table('pincodes')->where('id',$request->pincode)->first();
            $shop->pincode = $pincode->code;
            $shop->reg_cert = $request->reg_cert;
            $shop->company_type = $request->company_type;
           
            $shop->cheque_image = ImageManager::upload('shop/', 'png', $request->file('cheque'));
            $shop->reg_cert_image = ImageManager::upload('shop/', 'png', $request->file('reg_cert_image'));
            $shop->gst_no = $request->gst_no;
            $shop->gst_cert_image = ImageManager::upload('shop/', 'png', $request->file('gst_cert_image'));
            $shop->pan = $request->pan;
            $shop->pan_image = ImageManager::upload('shop/', 'png', $request->file('pan_image'));
            $shop->bank_branch = $request->bank_branch;
            $shop->acc_no = $request->acc_no;
            $shop->bank_name = $request->bank_name;
            $shop->ifsc = $request->ifsc;

            $shop->contact = $request->phone;
            $shop->image = ImageManager::upload('shop/', 'png', $request->file('logo'));
            $shop->banner = ImageManager::upload('shop/banner/', 'png', $request->file('banner'));
            //$shop->bottom_banner = ImageManager::upload('shop/banner/', 'png', $request->file('bottom_banner'));
            
            $shop->save();
            
            $full_name = $request->f_name.' '.$request->l_name;
            
           
      /*  $shop_address_1 =  $request->shop_address;
        $phone_1 =  $request->phone;
        $city_1 = $city->name;
        $pincode_1 = $pincode->code;
        $country_1 =  $country->name;
        $state_1  =  $state->name;
        
         DB::table('shipping_addresses')->insert([
                'customer_id' => $seller['id'],
                'contact_person_name' => $full_name,
                'address' => $shop_address_1,
                'city' => $city_1,
                'zip' => $pincode_1,
                'country' => $country_1,
                'state' => $state_1,
                'phone' => $phone_1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);*/

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });

        if($request->status == 'approved'){
           // Toastr::success('Shop apply successfully!');
           Toastr::success('<strong>Congratulations!</strong> Your registration has been completed successfully. Now you can <strong>LOGIN</strong> and start your seller journey with us.');
            return back();
        }else{
            //Toastr::success('Shop apply successfully!');
            Toastr::success('<strong>Congratulations!</strong> Your registration has been completed successfully. Now you can <strong>LOGIN</strong> and start your seller journey with us.');

            return redirect()->route('seller.auth.login');
        }


    }
    
     public function country(Request $request)
    {
       
       $country_id = $request->country_id;
        
        $found = DB::table('states')->where('country_id',$country_id)->first();
       
        
        if($found){
             $data = DB::table('states')->where('country_id',$country_id)->orderBy('name')->get();
             $select['status1'] = '1';
          $select['state'] = '<select class="form-control form-control-user" id="shop_state" name="state"  required><option> Select State </option>';
           foreach ($data as $datas) {
        $select['state'] .= '<option value="' . $datas->id . '">' . $datas->name . '</option>';
        }
         $select['state'] .= '</select>';

        }else{
             $select['status1'] = '0';
            $select['state'] ='<select class="form-control form-control-user" id="shop_state" name="state"  required><option value="0">No state found</option></select>';
             $select['city'] ='<select class="form-control form-control-user" id="shop_city" name="city"  required><option value="0">No city found</option></select>';
             $select['pincode'] ='<select class="form-control form-control-user" id="shop_pin" name="pincode"  required><option value="0">No pincode </option></select>';
        }
        
      // echo $select;  
       echo json_encode($select);
    }
    
    
     public function state(Request $request)
    {
       $state_id = $request->state_id;
        
        $found = DB::table('cities')->where('state_id',$state_id)->first();
       
        
        if($found){
             $data = DB::table('cities')->where('state_id',$state_id)->orderBy('name')->get();
          $select = '<select class="form-control form-control-user" id="shop_city" name="city"  required><option> Select City </option>';
           foreach ($data as $datas) {
        $select .= '<option value="' . $datas->id . '">' . $datas->name . '</option>';
        }
         $select .= '</select>';

        }else{
            $select ='<select class="form-control form-control-user" id="shop_city" name="city"  required><option value="0">No city found</option></select>';
        }
        
       echo $select;  
    }
    
     public function city(Request $request)
    {
       $city_id = $request->city_id;
        
        $found = DB::table('pincodes')->where('city_id',$city_id)->first();
       
        
        if($found){
             $data = DB::table('pincodes')->where('city_id',$city_id)->orderBy('code')->get();
          $select = '<select class="form-control form-control-user" id="shop_pin" name="pincode"  required><option> Select Pincode </option>';
           foreach ($data as $datas) {
        $select .= '<option value="' . $datas->id . '">' . $datas->code . '</option>';
        }
         $select .= '</select>';

        }else{
            $select ='<select class="form-control form-control-user" id="shop_pin" name="pincode"  required><option value="0">No pincode </option></select>';
        }
        
       echo $select;  
    }
}
