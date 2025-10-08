<?php

namespace App\Http\Controllers\Seller;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use App\Model\ShippingAddress;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use function App\CPU\translate;
use Illuminate\Support\Facades\DB;
use App\Services\ShiprocketService;

class ProfileController extends Controller
{
    public function Signature_update(Request $request)
    {
        $seller = Seller::where('id', auth('seller')->id())->first();
         if ($request->signature) {
            $seller->signature = ImageManager::update('seller/', $seller->signature, 'png', $request->file('signature'));
        }
        $seller->save();
         Toastr::success('Seller signature updated successfully!');
        return back();
    }
    
     public function signature()
    {
        $data = Seller::where('id', auth('seller')->id())->first();
        return view('seller-views.profile.signature', compact('data'));
    }
    public function warehouse()
    {
        $data = Seller::where('id', auth('seller')->id())->first();
        return view('seller-views.profile.warehouse', compact('data'));
    }
    
    public function save_warehouse(Request $request)
    {
        $seller_id =   auth('seller')->id();
        $data = [
            'seller_id'=>$seller_id,
            'name'=>$request->name,
            'title'=>$request->title,
            'contact'=>$request->contact,
            'address'=>$request->address,
            'email'=>$request->email,
            'pincode'=>$request->pincode,
        ];
        

       // $data = Seller::where('id', auth('seller')->id())->first();
         $x =   DB::table('warehouse')->insert($data);

        if($x){
            return response()->json(['message' => 'Warehouse details saved successfully!'], 200);
        }

    }

    public function get_warehouse()
    {
       
       $data = DB::table('warehouse')->get();

       return response()->json(['data'=>$data]);    
 
    }

    public function pincode(Request $request)
    {
        // Check if the pincode exists
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
    

     public function view()
    {
        $data = Seller::where('id', auth('seller')->id())->first();
        return view('seller-views.profile.view', compact('data'));
    }


    public function edit($id)
    {
        if (auth('seller')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_profile'));
            return back();
        }
        $data = Seller::where('id', auth('seller')->id())->first();
        $shop_banner = Shop::select('banner')->where('seller_id', auth('seller')->id())->first()->banner;
        
        return view('seller-views.profile.edit', compact('data', 'shop_banner'));
    }

    public function update(Request $request, $id, ShiprocketService $shiprocketService)
    {
      
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required'
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
            'phone.required' => 'Phone number is required!',
        ]);

        $seller = Seller::find(auth('seller')->id());
        $seller->f_name = $request->f_name;
        $seller->l_name = $request->l_name;
        $seller->phone = $request->phone;
       
        if(Seller::where('email',$request->email)->whereNotIn('email', [$seller->email])->first()){
        Toastr::error('This e-mail id already registered with us. Try with different email id!');
        return back();  
        }
        $seller->email = $request->email;
        
        $seller->profile_edit_status = 0;
        if ($request->image) {
            $seller->image = ImageManager::update('seller/', $seller->image, 'png', $request->file('image'));
        }
        $seller->save();
        
       /* $shipping_address = ShippingAddress::where('customer_id',auth('seller')->id())->first();
        
        if($shipping_address){
            $full_name = $request->f_name.' '.$request->l_name;
            $shipping_address->contact_person_name = $full_name;
            $shipping_address->phone = $request->phone;
            $shipping_address->save();
        }else{
            $shipping_address_new = new ShippingAddress();
            
            $shop = Shop::where('seller_id',auth('seller')->id())->first();
            $full_name = $request->f_name.' '.$request->l_name;
            $shipping_address_new->customer_id = $seller->id;
            $shipping_address_new->contact_person_name = $full_name;
            $shipping_address_new->phone = $request->phone;
            $shipping_address_new->address = $shop->address;
            $shipping_address_new->city = $shop->city;
            $shipping_address_new->zip = $shop->pincode;
            $shipping_address_new->country = $shop->country;
            $shipping_address_new->state = $shop->state;
            $shipping_address_new->created_at = now();
            $shipping_address_new->updated_at = now();
            $shipping_address_new->save();
        }*/
         $shop = Shop::where('seller_id',$id)->first();
        // $pickupAddressId = $shop->shiprocket_seller_id;
         $address = [
                "pickup_location" => str_replace(" ", "", $shop->name),
            	"name" => $shop->name,
            	"email" => $seller->email,
            	"phone" => str_starts_with($seller->phone, "91") ? substr($seller->phone, 2, 10) : $seller->phone,
            	"address" => $shop->address,
            	"address_2" => "",
            	"city" => $shop->city,
            	"state" => $shop->state,
            	"country" => $shop->country,
            	"pin_code" => $shop->pincode
            ];
           
            try {
                //update pickup address of shiprocket
               
              $data =   $shiprocketService->createPickupLocation($address);
              //return $data;
            }catch(\Exception $e) {
                
            }

        Toastr::info('Profile updated successfully!');
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $seller = Seller::find(auth('seller')->id());
        $seller->password = bcrypt($request['password']);
        $seller->profile_edit_status = 0;
        $seller->save();
        Toastr::success('Seller password updated successfully!');
        return back();
    }

    public function bank_update(Request $request, $id)
    {
        $bank = Shop::where( "seller_id", auth('seller')->id())->first();
        
        $bank->bank_name = $request->bank_name;
        $bank->bank_branch = $request->branch;
        $bank->ifsc = $request->ifsc;
        $bank->acc_no = $request->account_no;
        if ($request->hasFile('cheque')) {
        $chequeImageFile = $request->file('cheque');
        $chequeImageExtension = strtolower($chequeImageFile->getClientOriginalExtension());
        $bank->cheque_image = ImageManager::upload('shop/', $chequeImageExtension, $chequeImageFile);
        //$bank->cheque_image = ImageManager::upload('shop/', 'png', $request->file('cheque'));
        }
        $bank->save();
        Toastr::success('Bank Info updated');
        return redirect()->route('seller.profile.view');
    }

    public function bank_edit($id)
    {
        if (auth('seller')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_info'));
            return back();
        }
        $data = Seller::where('id', auth('seller')->id())->first();
        return view('seller-views.profile.bankEdit', compact('data'));
    }

}
