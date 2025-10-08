<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Model\ShiprocketCourier;
use Illuminate\Support\Facades\DB;

class ShiprocketService extends Controller {
    
    const PICKUP_ADDRESS_NAME = 'Primary';
    
    //check for pincode
    public function checkForAvailability($query)
    {
       // dd(config('shiprocket.credentials.endpoint'));

        // print_r($query);
        // die;
        try {
            //get Authorization token
            $token = $this->getApiToken();
          // $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjU5NTAxMzUsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzQ5NDY0NzgyLCJqdGkiOiJNbGhxQ0N5VFNxa1ZQNHVzIiwiaWF0IjoxNzQ4NjAwNzgyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTc0ODYwMDc4MiwiY2lkIjozNTAyMTM2LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.qBiPksjUJluPdlNxz1EOz_R6br42fgBJj5EjGVpAkFo";
          
            $headers = [
              'Content-Type' => 'application/json',
              'Authorization' => "Bearer {$token}"
            ];
            
            $response = Http::withHeaders($headers)->get(config('shiprocket.credentials.endpoint'). '/courier/serviceability', $query);
            if($response->status() === 200) {
                $data = $response->object();
                // dd($data->data);
                if($data->data) {
                    $recommended = $data->data->shiprocket_recommended_courier_id;
                   
                    $selected = array_filter($data->data->available_courier_companies, function($c) use($recommended){
                       return $c->courier_company_id == $recommended; 
                    });
                    
                    return [
                        'status'    => 'success',
                        'data'      => count($selected) > 0 ? $selected[0] : $data
                    ];
                }else {
                    return [
                        'status'    => 'success',
                        'data'      => $data
                    ];
                }
            }else {
                return [
                    'status'    => 'failed',
                    'data'      => null
                ];
            }
        }catch(\Exception $e) {
            return [
                'status'    => 'failed',
                'data'      => null
            ];
        }
    }
    
   // update pickup location
   function updateShiprocketPickupAddress($pickupAddressId, $newAddressData)

    {
        if($pickupAddressId) {
            $token = $this->getApiToken();
            
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ];
            
            $response = Http::withHeaders($headers)->get(config('shiprocket.credentials.endpoint'). '/pickup/', $pickupAddressId);
           //$response = 'https://apiv2.shiprocket.in/v1/shipments/pickup/' . $pickupAddressId;
            if($response->status() === 200) {
               // $address['pickup_location'];
               return  $headers->put($response, [ 'json' => $newAddressData,]);
       
    
            }
            
            return "";
        }else {
            return "";
        }
    }
     //create pickup location
     public function createPickupLocation($address)
    {
       
        if($address) {
            $token = $this->getApiToken();
            
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ];
           
            $response = Http::withHeaders($headers)->post(config('shiprocket.credentials.endpoint'). '/settings/company/addpickup', $address);
           
            if($response->status() === 200) {
                return $address['pickup_location'];
            }
            
            return "";
        }else {
            return "";
        }
    }
    
    //createOrder
    public function createOrder($order)
    {
        //dd($order);
         
        try {
            //get Authorization token
            $token = $this->getApiToken();
           // dd($token);
         //  $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjU5NTAxMzUsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzQ5NDY0NzgyLCJqdGkiOiJNbGhxQ0N5VFNxa1ZQNHVzIiwiaWF0IjoxNzQ4NjAwNzgyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTc0ODYwMDc4MiwiY2lkIjozNTAyMTM2LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.qBiPksjUJluPdlNxz1EOz_R6br42fgBJj5EjGVpAkFo";

            
            $headers = [
              'Content-Type' => 'application/json',
              'Authorization' => "Bearer {$token}"
            ];
            
            $orderItems = [];
            $count_qty = 0;
            $total_weight = 0;
            foreach ($order->details as $orderDetail) {
                //dd($orderDetail->qty);
               $productData = json_decode($orderDetail->product_details, true);
               
               $sku_product = DB::table('sku_product_new')->where('product_id',$orderDetail->product_id)->where('variation',$orderDetail->variant)->first();
               //dd($sku_product->length);
               
            //    if ($productData['discount_type'] == 'percent') {
            //     $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
            //     } else {
            //     $discountPrice = $productData['unit_price'] - $productData['discount'];
            //     }
               
            //     if (!empty($orderDetail['variant'])) {
            //         $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];
                
            //         if (!empty($priceData)) {
            //             foreach ($priceData as $keyss => $priceValue) {
            //                 if ($orderDetail['variant'] == $priceValue['type']) {
                                
            //                     if ($productData['discount_type'] == 'percent') {
            //                         $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
            //                     } else {
            //                         $discountPrice = $priceValue['price'] - $productData['discount'];
            //                     }
                
            //                 }
            //             }
            //         }
            //     }
                
            //   $item_tax = ($discountPrice-($discountPrice*100)/($orderDetail->product_all_status->tax + 100));
            //   $amount_without_tax = ($discountPrice*100)/($orderDetail->product_all_status->tax + 100);
                //dd($item_tax);  
                  
                array_push($orderItems, [
                    "name" => $productData['name'] ?? "",
                    "sku" => $sku_product->sku,
                    "units" => $orderDetail->qty,
                    "selling_price" => $sku_product->listed_price,
                    "discount" => $sku_product->discount,
                    "tax" => '',
                    "hsn" =>$productData['HSN_code'] ?? ""
                ]);
                

                $length = $sku_product->length;
                $breadth = $sku_product->breadth;
                $height = $sku_product->height;
                $weight = $sku_product->weight;
                $sub_weight = $sku_product->weight*$orderDetail->qty;
                $total_weight += $sub_weight;
                
           
            }
            //dd($orderItems);
            $billingAddress = json_decode($order->billing_address_data);
            $shippingAddress = json_decode($order->shipping_address_data);

            //dd($shippingAddress);
           
            
             $address = [
                "pickup_location" => str_replace(" ", "", $order->seller->shop->name),
            	"name" => $order->seller->shop->name,
            	"email" => $order->seller->email,
            	"phone" => str_starts_with($order->seller->phone, "91") ? substr($order->seller->phone, 2, 10) : $order->seller->phone,
            	"address" => $order->seller->shop->address,
            	"address_2" => "",
            	"city" => $order->seller->shop->city,
            	"state" => $order->seller->shop->state,
            	"country" => $order->seller->shop->country,
            	"pin_code" => $order->seller->shop->pincode
            ];
            //dd($address);
            if(empty($order->seller->shop->pickup_location)) {
                $pickupAddress = $this->createPickupLocation($address);
            
                if(!empty($pickupAddress)) {
                    $order->seller->shop->pickup_location = $pickupAddress;
                    $order->seller->shop->save();
                }
                
                $pickupAddress = !empty($pickupAddress) ? $pickupAddress : str_replace(" ", "", $order->seller->shop->name);
               // dd($pickupAddress);
            }else {
                $pickupAddress = $order->seller->shop->pickup_location;
              
            }
            
          
     $body = [
    "order_id" => (string) $order->id,  // Shiprocket requires string
    "order_date" => date('Y-m-d H:i:s', strtotime($order->created_at)),
    "pickup_location" => $pickupAddress ?? "Default Location",

    "channel_id" => "",  // Optional
    "comment" => "",     // Optional

    // Billing Details
    "billing_customer_name" => $billingAddress->contact_person_name ?? "NA",
    "billing_last_name" => "",  // Optional
    "billing_address" => $billingAddress->address ?? "NA",
    "billing_address_2" => $billingAddress->landmark ?? "",
    "billing_city" => $billingAddress->city ?? "NA",
    "billing_pincode" => $billingAddress->zip ?? "000000",
    "billing_state" => $billingAddress->state ?? $billingAddress->city ?? "NA",
    "billing_country" => $billingAddress->country ?? "India",
    "billing_email" => filter_var($order->customer->email, FILTER_VALIDATE_EMAIL) ? $order->customer->email : "support@example.com",
    "billing_phone" => preg_replace('/[^0-9]/', '', $billingAddress->phone) ?: "9999999999",

    // Shipping Details
    "shipping_is_billing" => true,
    "shipping_customer_name" => $shippingAddress->contact_person_name ?? "NA",
    "shipping_last_name" => "",
    "shipping_address" => $shippingAddress->address ?? "NA",
    "shipping_address_2" => $shippingAddress->landmark ?? "",
    "shipping_city" => $shippingAddress->city ?? "NA",
    "shipping_pincode" => $shippingAddress->zip ?? "000000",
    "shipping_state" => $shippingAddress->state ?? $shippingAddress->city ?? "NA",
    "shipping_country" => $shippingAddress->country ?? "India",
    "shipping_email" => filter_var($order->customer->email, FILTER_VALIDATE_EMAIL) ? $order->customer->email : "support@example.com",
    "shipping_phone" => preg_replace('/[^0-9]/', '', $shippingAddress->phone) ?: "9999999999",

    // Items
    "order_items" => $orderItems, // Ensure this is a valid array of items

    // Payment & Charges
    "payment_method" => ($order->payment_method !== 'cash_on_delivery') ? "Prepaid" : "COD",
    "shipping_charges" => (float) ($order->shipping_cost ?? 0),
    "giftwrap_charges" => 0,
    "transaction_charges" => 0,
    "total_discount" => 0,
    "sub_total" => $order->subtotal ?? 600,  // Use actual subtotal if available

    // Dimensions (Required for shipping)
    "length" => (float) ($length ?? 0),
    "breadth" => (float) ($breadth ?? 0),
    "height" => (float) ($height >= 0.5 ? $height : 0.5),
    "weight" => (float) ($total_weight ?? 0.5)
];


// dd($body);
        
           
            $response = Http::withHeaders($headers)->post(config('shiprocket.credentials.endpoint').'/orders/create/adhoc',$body);
       // dd($body,$response->object());
           
            
            // $myfile = fopen("/www/wwwroot/interiorchowk.com/shiprocket_response.txt", "w") or die("Unable to open file!");
            // $txt = $response;
            // fwrite($myfile, $txt);
            // fclose($myfile);
            
            // $myfile2 = fopen("/www/wwwroot/interiorchowk.com/shiprocket_request.txt", "w") or die("Unable to open file!");
            // $txt1 = json_encode($body);
            // fwrite($myfile2, $txt1);
            // fclose($myfile2);
            
            // $myfile3 = fopen("/www/wwwroot/interiorchowk.com/shiprocket_request_status.txt", "w") or die("Unable to open file!");
            // $txt2 = $response->status();
            // fwrite($myfile3, $txt2);
            // fclose($myfile3);
            
            if($response->status() === 200) {
                $data = $response->object();
                
               //dd($data);

                $isExist = ShiprocketCourier::where("order_id", $order->id)->first();
                //dd($isExist);
                if($isExist) {
                    $isExist->update([
                        'order_id' => $order->id,
                        'shipment_id' => $data->shipment_id,
                        'shiprocket_order_id' => $data->order_id,
                        'status' => $data->status,
                        'courier_company_id' => $data->courier_company_id,
                        'courier_name' => $data->courier_name,
                        'onboarding_completed_now' => $data->onboarding_completed_now,
                        'awb_code' => $data->awb_code
                    ]);
                }else {
                    ShiprocketCourier::create([
                        'order_id' => $order->id,
                        'shipment_id' => $data->shipment_id,
                        'shiprocket_order_id' => $data->order_id,
                        'status' => $data->status,
                        'courier_company_id' => $data->courier_company_id,
                        'courier_name' => $data->courier_name,
                        'onboarding_completed_now' => $data->onboarding_completed_now,
                        'awb_code' => $data->awb_code
                    ]);
                }
            }
            
            return "success";
        }catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    
    //cancelOrder
    public function cancelOrder($order)
    {
        try {
           //get Authorization token
           $token = $this->getApiToken();
          // $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjU5NTAxMzUsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzQ5NDY0NzgyLCJqdGkiOiJNbGhxQ0N5VFNxa1ZQNHVzIiwiaWF0IjoxNzQ4NjAwNzgyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTc0ODYwMDc4MiwiY2lkIjozNTAyMTM2LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.qBiPksjUJluPdlNxz1EOz_R6br42fgBJj5EjGVpAkFo";
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ];
            
            //get shiprocket record
            $shiprocketOrder = ShiprocketCourier::where("order_id", $order->id)->first();
            if($shiprocketOrder) {
                $body = [
                    "ids" => [$shiprocketOrder->shiprocket_order_id]
                ];
                
                $response = Http::withHeaders($headers)->post(config('shiprocket.credentials.endpoint'). '/orders/cancel', $body);
                if($response->status() === 200) {
                    ShiprocketCourier::where("order_id", $shiprocketOrder->order_id)->update([
                        'status' => "CANCELED",
                    ]);
                }
            }
        }catch(\Exception $e) {
            //log
        }
    }
    
    //getOrder
    public function getOrder($shippingOrderId)
    {
        //get Authorization token
        $token = $this->getApiToken();
        
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => "Bearer {$token}"
        ];
        
        $response = Http::withHeaders($headers)->get(config('shiprocket.credentials.endpoint').'/orders/show/'.$shippingOrderId);
        
        
        if($response->status() === 200){
            return $response->object();
        }
        
        return false;
    }
    
    //getShipment
    public function getShipment($shippingOrderId)
    {
        //get Authorization token
        $token = $this->getApiToken();
        
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => "Bearer {$token}"
        ];
        
        $response = Http::withHeaders($headers)->get(config('shiprocket.credentials.endpoint'). '/shipments/'. $shippingOrderId);
        
        if($response->status() === 200){
            return $response->object();
        }
        
        return false;
    }
    
    //getAwbStatus
    public function getAwbStatus($awdCode)
    {
        
        //get Authorization token
        $token = $this->getApiToken();
        
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => "Bearer {$token}"
        ];
       
        $response = Http::withHeaders($headers)->get(config('shiprocket.credentials.endpoint') .'/courier/track/awb/'.$awdCode);
       
       
        if($response->status() === 200){
            return $response->object();
        }
        
        return false;   
    }
    
  
    
    //webhook part
    public function updateStatus($request)
    {
        file_put_contents( public_path('shiprocket.log'), $request->post());
        
        if($request->has('awb')) {
            $orderId = $request->order_id ?? null;
            
            if($orderId) {
                $shipment = ShiprocketCourier::where("shiprocket_order_id", $orderId)->first();
                if($shipment) {
                    $shipment->update([
                       'awb_code' => $request->awb,
                       'status' => $request->current_status,
                       'scans' => json_encode($request->scans)
                    ]);
                }
            }
        }
    }
    
    //get Api token
    protected function getApiToken()
    {
        try {
            
            //check for existing token
            if(config('shiprocket.token') && config('shiprocket.expires_on')) {
                if(date('Y-m-d H:i:s', strtotime(config('shiprocket.expires_on'))) > date('Y-m-d H:i:s')) {
                    return config('shiprocket.token');
                }
            }
           
            $headers = [
              'Content-Type' => 'application/json'
            ];
            
            $body = [
              "email" => config('shiprocket.credentials.email'),
              "password" => config('shiprocket.credentials.password')
            ];
            
            $url = config('shiprocket.credentials.endpoint'). '/auth/login';
            
            //curl
            // $ch = curl_init( $url );
            // # Setup request to send json via POST.
            // curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
            // curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            // # Return response instead of printing.
            // curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            // # Send request.
            // $result = curl_exec($ch);
            // curl_close($ch);
            // dd($result);

            $response = Http::withHeaders($headers)->post($url, $body);
           
            if($response->status() === 200) {
                $data = $response->object();
                $configContents = @file_get_contents(base_path(). '/config/shiprocket.php');
                
                $time = date('Y-m-d H:i:s', strtotime('+9days'));
                
                $newConfig = str_replace(config('shiprocket.token'), $data->token, $configContents);
                $newConfig = str_replace(config('shiprocket.expires_on'), $time, $newConfig);
                
                @file_put_contents(base_path(). '/config/shiprocket.php', $newConfig);
                
                return $data->token;
            }else {
                return null;
            }
        }catch(\Exception $e) {
            return null;
        }
    }
}