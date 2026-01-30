<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Model\ShiprocketCourier;
use App\Model\Order;
use Illuminate\Support\Facades\DB;

class ShipyaariService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.shipyaari.api_key');
        $this->baseUrl = config('services.shipyaari.base_url');
    }

    private function headers()
    {
        return [
             'Authorization' => 'Bearer ' . $this->apiKey,
             'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    // 1. Check Serviceability
//     public function checkForAvailability(array $data)
//     {
//         //dd($data);
//         try {
//             $response = Http::withHeaders($this->headers())
//                 ->post($this->baseUrl .'checkServiceabilityV2',$data);

//                 //dd($response);
//     if ($response->status() === 200) {
//         $data = $response->object();
// //dd($data->data);
// if (is_array($data->data) && count($data->data) > 0) {
//     // Sort by lowest total shipping cost
//     usort($data->data, function ($a, $b) {
//         return $a->total <=> $b->total;
//     });

//     // Lowest cost courier (first item after sort)
//     $recommended = $data->data[0];
//      //dd($recommended);
//     return [
//         'status' => 'success',
//         'data'   => $recommended
//     ];
// } else {
//     return [
//         'status' => 'failed',
//         'message' => 'No couriers available'
//     ];
// }

//     }
//             return $this->formatResponse($response);
//         } catch (\Exception $e) {
//             Log::error('Shipyaari serviceability error: ' . $e->getMessage());
//             return $this->errorResponse($e);
//         }
//     }



        public function checkForAvailability(array $data)
        {
            try {
                $response = Http::withHeaders($this->headers())
                    ->post($this->baseUrl . 'checkServiceabilityV2', $data);

                if ($response->status() !== 200) {
                    return $this->formatResponse($response);
                }

                $data = $response->object();

                if (!isset($data->data) || !is_array($data->data) || count($data->data) == 0) {
                    return [
                        'status' => 'failed',
                        'message' => 'No couriers available'
                    ];
                }

                // FILTER only valid numeric totals
                $couriers = array_filter($data->data, function ($courier) {
                    return isset($courier->total) && is_numeric($courier->total);
                });

                // If none have numeric totals → fail
                if (empty($couriers)) {
                    return [
                        'status'  => 'failed',
                        'message' => 'No valid courier cost found'
                    ];
                }

                // Sort clean couriers
                usort($couriers, function ($a, $b) {
                    return $a->total <=> $b->total;
                });

                // Lowest cost
                $recommended = $couriers[0];
                // dd($recommended);
                return [
                    'status' => 'success',
                    'data'   => $recommended
                ];

            } catch (\Exception $e) {
                Log::error('Shipyaari serviceability error: ' . $e->getMessage());
                return $this->errorResponse($e);
            }
        }






    // 2. Create Pickup Address
    public function createPickupLocation(array $pickupData)
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . 'pickup-address', $pickupData);

            return $this->formatResponse($response);
        } catch (\Exception $e) {
            Log::error('Shipyaari pickup creation error: ' . $e->getMessage());
            return $this->errorResponse($e);
        }
    }

    // 3. Create Order
// public function createOrder($order)
// {
//    // dd($order->payment_method);
//     $orderItems = [];
//     $total_weight = 0;

//     foreach ($order->details as $orderDetail) {
//         $productData = json_decode($orderDetail->product_details, true);

//         $sku_product = DB::table('sku_product_new')
//             ->where('product_id', $orderDetail->product_id)
//             ->where('variation', $orderDetail->variant)
//             ->first();

//         $length = $sku_product->length ?? 1;
//         $breadth = $sku_product->breadth ?? 1;
//         $height = $sku_product->height ?? 1;
//         $weight = $sku_product->weight ?? 1;
//         $selling_amount = (($sku_product->listed_price ?? 0)*($orderDetail->qty)) + (float) ($order->shipping_cost ?? 0);



//         $sub_weight = $weight * $orderDetail->qty;
//         $total_weight += $sub_weight;

//         $orderItems[] = [
//             "name" => $productData['name'] ?? "",
//             "sku" => $sku_product->sku ?? '',
//             "hsnCode" => $productData['HSN_code'] ?? "",
//             "category" => "Electronic",
//             "qty" => $orderDetail->qty,
//             "unitPrice" => $sku_product->variant_mrp,
//             "discount" => $sku_product->discount,
//             "unitTax" => 1,
//             "sellingPrice" => $sku_product->listed_price,
//             "totalDiscount" => $sku_product->discount,
//             "totalPrice" => $sku_product->listed_price,
//             "weightUnit" => "kg",
//             "deadWeight" => (float)$total_weight,
//             "length" => $length,
//             "breadth" => $breadth,
//             "height" => $height,
//             "measureUnit" => "cm",
//             "images" => []
//         ];
//     }

//     $billingAddress = json_decode($order->billing_address_data);
//     $shippingAddress = json_decode($order->shipping_address_data);
//     $warehouse = DB::table('warehouse')->where('seller_id', $order->seller_id)->first();

//     try {
//         $payload = [
//             "pickupDetails" => [
//                 "addressType" => $order->seller->shop->name, // keep as-is with spaces
//                 "fullAddress" => $warehouse->address,
//                 "pincode" => $warehouse->pincode,
//                 "startTime" => "08",
//                 "endTime" => "09",
//                 "latitude" => "12.567",
//                 "longitude" => "17.687",
//                 "contact" => [
//                     "name" => $warehouse->name,
//                     "mobileNo" => (string) $warehouse->contact,
//                     "alternateMobileNo" => "9950680690" // dummy, or blank
//                 ]
//             ],
//             "deliveryDetails" => [
//                 "addressType" => $shippingAddress->address_type ?? "residence",
//                 "fullAddress" => $shippingAddress->address ?? '',
//                 "pincode" => $shippingAddress->zip ?? '',
//                 "startTime" => "10",
//                 "endTime" => "11",
//                 "latitude" => "45.0798",
//                 "longitude" => "12.567",
//                 "contact" => [
//                     "name" => $shippingAddress->contact_person_name ?? '',
//                     "mobileNo" => (string) $shippingAddress->phone ?? '',
//                     "alternateMobileNo" => "9950680690"
//                 ],
//                 "gstNumber" => "27AALCA5307N1ZC"
//             ],
//             "boxInfo" => [[
//                 "name" => "box_2",
//                 "type" => "Documents",
//                 "weightUnit" => "Kg",
//                 "deadWeight" =>(float) $total_weight,
//                 "length" => 1,
//                 "breadth" => 1,
//                 "height" => 1,
//                 "qty" => 1,
//                 "discount" => 1,
//                 "measureUnit" => "cm",
//                 "products" => $orderItems,
//                 "codInfo" => [
//                     "isCod" => $order->payment_method === "cash_on_delivery" ? true : false ,
//                     "collectableAmount" =>  $selling_amount,
//                     "invoiceValue" => $selling_amount
//                 ],
//                 "podInfo" => [
//                     "isPod" => $order->payment_method === "online_payment" ? true : false
//                 ],
//                 "insurance" => false
//             ]],
//             "orderType" => "B2C",
//             "transit" => "FORWARD",
//             "courierPartner" => "",
//             "courierPartnerServices" => "",
//             "serviceMode" => "Surface",
//             "giftCharges" => 2,
//             "shippingCharges" => 3,
//             "transactionCharges" => 2,
//             "advanceAmountPaid" => 1,
//             "servicePriority" => "cheapest",
//             "source" => "", // <-- blank as per your example JSON
//             "qcType" => "DoorStep",
//             "returnReason" => "Test",
//             "orderFutureDate" => "2025-05-09",
//             "pickupDate" => round(microtime(true) * 1000),
//             "gstNumber" => "27AALCA5307N1ZC",
//             "childGstNumber" => "27AALCA5307N1ZC",
//             "parentId" => 1,
//             "childId" => 2,
//             "orderId" => (string) $order->id,
//             "eWayBillNo" => "",
//             "brandName" => "Google",
//             "brandLogo" => "https://dev.interiorchowk.com/ic/public/website/assets/images/logoic.png"
//         ];

//         $response = Http::withHeaders($this->headers())
//             ->post($this->baseUrl . 'placeOrderApiV3', $payload);
                
//             if ($response->status() === 200) {
//     $data = $response->object(); // stdClass
//     $orderData = $data->data[0] ?? null;

//     if ($orderData) {
//         $awbCode = $orderData->awbs[0]->tracking->awb ?? null;
//         $status = $orderData->awbs[0]->tracking->status[0]->currentStatus ?? null;
//         $courierName = $orderData->awbs[0]->charges->partnerName ?? null;

       

//         $isExist = ShiprocketCourier::where("order_id", $order->id)->first();

//         if ($isExist) {
//             $isExist->update([
//                 'order_id' => $order->id,
//                 'awb_code' => $awbCode,
//                 'courier_name' => $courierName,
//                 'status' => $status,
//                 'shiprocket_order_id' => $orderData->orderId, // optional
//                 'shipment_id' => $orderData->shipyaariId,     // optional
//             ]);
//         } else {
//             ShiprocketCourier::create([
//                 'order_id' => $order->id,
//                 'awb_code' => $awbCode,
//                 'courier_name' => $courierName,
//                 'status' => $status,
//                 'shiprocket_order_id' => $orderData->orderId,
//                 'shipment_id' => $orderData->shipyaariId,
//             ]);
//         }

//          Order::where('id', $order->id)->update([
//     'third_party_delivery_tracking_id' => $awbCode
// ]);
//     }
// }

// return "success";


//        // return $this->formatResponse($response);
//     } catch (\Exception $e) {
//         Log::error('Shipyaari order creation error: ' . $e->getMessage());
//         return $this->errorResponse($e);
//     }
// }


    public function createOrder($order)
    {
        // ------------------------------------
        // BUILD PRODUCT LIST + TOTAL WEIGHT
        // ------------------------------------
        $orderItems = [];
        $total_weight = 0;

        foreach ($order->details as $orderDetail) {

            $productData = json_decode($orderDetail->product_details, true);

            $sku = DB::table('sku_product_new')
                ->where('product_id', $orderDetail->product_id)
                ->where('variation', $orderDetail->variant)
                ->first();

            if (!$sku) {
                throw new \Exception("SKU missing for product {$orderDetail->product_id}");
            }

            $length = floatval($sku->length ?? 1);
            $breadth = floatval($sku->breadth ?? 1);
            $height = floatval($sku->height ?? 1);
            $weight = floatval($sku->weight ?? 0.5);

            // Sub-weight per product qty
            $sub_weight = $weight * $orderDetail->qty;
            $total_weight += $sub_weight;

            $selling_price = floatval($sku->listed_price ?? 0);
            $total_price = $selling_price * $orderDetail->qty;

            $orderItems[] = [
                "name" => $productData['name'] ?? "",
                "sku" => $sku->sku ?? '',
                "hsnCode" => $productData['HSN_code'] ?? "0000",
                "category" => "General",
                "qty" => $orderDetail->qty,
                "unitPrice" => floatval($sku->variant_mrp ?? 0),
                "discount" => floatval($sku->discount ?? 0),
                "unitTax" => 0,
                "sellingPrice" => $selling_price,
                "totalDiscount" => floatval($sku->discount ?? 0),
                "totalPrice" => $total_price,
                "weightUnit" => "kg",
                "deadWeight" => (float)$sub_weight, // IMPORTANT FIX
                "length" => $length,
                "breadth" => $breadth,
                "height" => $height,
                "measureUnit" => "cm",
                "images" => []
            ];
        }

        // --------------------------------------------------
        // BILLING / SHIPPING ADDRESSES
        // --------------------------------------------------
        $billingAddress = json_decode($order->billing_address_data);
        // dd($order);
        $shippingAddress = json_decode($order->shipping_address_data);
        $warehouse = DB::table('warehouse')->where('seller_id', $order->seller_id)->first();

        if (!$warehouse) {
            throw new \Exception("Warehouse not found for seller {$order->seller_id}");
        }

        // --------------------------------------------------
        // CALCULATE FINAL COD AMOUNT
        // --------------------------------------------------
        $productTotal = array_sum(array_column($orderItems, 'totalPrice'));
        $codAmount = $productTotal + (float) $order->shipping_cost;
        $finalWeight = max(0.5, $total_weight);

        // --------------------------------------------------
        // BUILD PAYLOAD
        // --------------------------------------------------


        $payload = [
            "pickupDetails" => [
                "addressType" => "warehouse",
                "fullAddress" => $warehouse->address ?? "",
                "pincode" => (int) ($warehouse->pincode ?? 0),
                "startTime" => "08",
                "endTime" => "09",
                "latitude" => "19.0760",
                "longitude" => "21.0760",
                "contact" => [
                    "name" => $warehouse->name ?? "",
                    
                    "mobileNo" => isset($warehouse->contact) && strlen($warehouse->contact) >= 10 
                        ? (int)$warehouse->contact 
                        : "",

                    "alternateMobileNo" => isset($warehouse->contact) && strlen($warehouse->contact) >= 10
                        ? (int)$warehouse->contact
                        : ""
                ]
            ],

            "deliveryDetails" => [
                "addressType" => "home",
                "fullAddress" => $shippingAddress->address ?? "Unknown",
                "pincode" => (int) ($shippingAddress->zip ?? 0),
                "startTime" => "10",
                "endTime" => "11",
                "latitude" => "0",
                "longitude" => "0",
                "contact" => [
                    "name" => $shippingAddress->contact_person_name ?? "Customer",

                    // FIX: ensure valid 10-digit shipping phone
                    "mobileNo" => isset($shippingAddress->phone) && strlen($shippingAddress->phone) >= 10
                        ? (int)$shippingAddress->phone
                        : "9999999999",

                    "alternateMobileNo" => ""
                ],
                "gstNumber" => ""
            ],

            "boxInfo" => [[
                "name" => "box_1",
                "type" => "parcel",
                "weightUnit" => "Kg",
                "deadWeight" => (float)$total_weight,
                "length" => 10,
                "breadth" => 10,
                "height" => 10,
                "qty" => 1,
                "discount" => 0,
                "measureUnit" => "cm",
                "products" => $orderItems,

                "codInfo" => [
                    "isCod" => $order->payment_method === "cash_on_delivery",
                    "collectableAmount" => $codAmount,
                    "invoiceValue" => $codAmount
                ],

                "podInfo" => [
                    "isPod" => $order->payment_method === "online_payment"
                ],

                "insurance" => false
            ]],

            "orderType" => "B2C",
            "transit" => "FORWARD",
            "courierPartner" => "",
            "courierPartnerServices" => "",
            "serviceMode" => "SURFACE",
            "giftCharges" => 0,
            "shippingCharges" => 0,
            "transactionCharges" => 0,
            "advanceAmountPaid" => 0,
            "servicePriority" => "cheapest",
            "source" => "",
            "qcType" => "DoorStep",
            "returnReason" => "Not Applicable",
            "orderFutureDate" => date("Y-m-d"),
            "pickupDate" => strtotime('+1 day'),
            "gstNumber" => "27AALCA5307N1ZC",
            "childGstNumber" => "27AALCA5307N1ZC",
            "parentId" => 1,
            "childId" => 2,
            "orderId" => (int) $order->id,
            "eWayBillNo" => "",
            "brandName" => "Google",
            "brandLogo" => "https://dev.interiorchowk.com/ic/public/website/assets/images/logoic.png"
        ];



        // dd($payload);


        // --------------------------------------------------
        // API CALL
        // --------------------------------------------------
        try {

            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . 'placeOrderApiV3', $payload);

            if ($response->status() !== 200) {
                Log::error("Shipyaari Response Error", ['body' => $response->body()]);
                return "Shipyaari Error";
            }

            $data = $response->object();
            $orderData = $data->data[0] ?? null;

            if (!$orderData) {
                Log::error("Shipyaari Missing Data", ['response' => $data]);
                return "Shipyaari Missing Data";
            }

            $awb = $orderData->awbs[0]->tracking->awb ?? null;
            $courier = $orderData->awbs[0]->charges->partnerName ?? null;
            $status = $orderData->awbs[0]->tracking->status[0]->currentStatus ?? null;

            // Save to DB
            ShiprocketCourier::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'awb_code' => $awb,
                    'courier_name' => $courier,
                    'status' => $status,
                    'shiprocket_order_id' => $orderData->orderId,
                    'shipment_id' => $orderData->shipyaariId,
                ]
            );

            Order::where('id', $order->id)->update([
                'third_party_delivery_tracking_id' => $awb
            ]);

            return "success";

        } catch (\Exception $e) {
            Log::error("Shipyaari order creation failed", ['error' => $e->getMessage()]);
            return "Shipyaari Error: " . $e->getMessage();
        }
    }



    // 4. Track Order
  public function trackOrder($awb)
{
   // $awb = '3113221071211';

    try {
        $response = Http::withHeaders($this->headers())
            ->get("https://api-seller.shipyaari.com/api/v1/tracking/getTracking?trackingNo=$awb");

        $data = $response->json(); // convert JSON response to array
          //dd($data);
        // extract values safely
        $statusCode = $data['statusCode'] ?? null;
        $currentStatus = $data['data'][0]['trackingInfo'][0]['currentStatus'] ?? null;
        if($statusCode === 200){
            ShiprocketCourier::where('awb_code',$awb)->update([
                        'status'=>$currentStatus
            ]);


            /////////
            if ($currentStatus === 'DELIVERED') {
    $scans = $data['data'][0]['trackingInfo'][0]['processedLog'][0]['Scans'] ?? [];

    $deliveredTime = null;

    foreach ($scans as $scan) {
        if (strtoupper($scan['status']) === 'DELIVERED') {
            $timestampMs = $scan['time']; // Example: 1752221382002
            $timestampSec = $timestampMs / 1000;

            $deliveredTime = \Carbon\Carbon::createFromTimestamp($timestampSec)->toDateTimeString();
            break;
        }
    }

    if ($deliveredTime) {
        ShiprocketCourier::where('awb_code',$awb)->update([
                        'delivered_at'=>$deliveredTime
            ]);
    }
}
//////////
        }


        return response()->json([
            'statusCode' => $statusCode,
            'currentStatus' => $currentStatus,
        ]);
    } catch (\Exception $e) {
        Log::error('Shipyaari tracking error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}



    // 5. Cancel Order
   public function cancelOrder($order)
{
    //dd($order->id);

    $awbNumber = ShiprocketCourier::where('order_id',$order->id)->value('awb_code');

    try {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . "cancelAWBs", [
                'awbs' => [$awbNumber] // ← Shipyaari expects array of AWBs
            ]);

                    if($response->status() === 200) {
                    ShiprocketCourier::where("order_id", $order->id)->update([
                        'status' => "CANCELED",
                    ]);
                }       
        //  dd($response->json());

        // return $this->formatResponse($response);

    } catch (\Exception $e) {
        Log::error('Shipyaari cancel error: ' . $e->getMessage());
        return $this->errorResponse($e);
    }
}


    // 6. Return Order
    public function returnOrder($data)
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl . 'return-order', $data);

            return $this->formatResponse($response);
        } catch (\Exception $e) {
            Log::error('Shipyaari return error: ' . $e->getMessage());
            return $this->errorResponse($e);
        }
    }

    // Helper for standard response
    private function formatResponse($response)
    {
        if ($response->successful()) {
            return [
                'status' => 'success',
                'data' => $response->json(),
            ];
        }

        return [
            'status' => 'failed',
            'message' => $response->body(),
        ];
    }

    private function errorResponse($e)
    {
        return [
            'status' => 'error',
            'message' => $e->getMessage(),
        ];
    }

      

  public function downloadLabel($awb)
{
    try {
        $response = Http::withHeaders($this->headers())
            ->post('https://api-seller.shipyaari.com/api/v1/labels/fetchLabels', [
                'awbs'   => [$awb],
                'source' => 'API',
            ]);

        // Agar API se direct PDF aa rahi hai
        if ($response->header('Content-Type') === 'application/pdf') {
            return response($response->body(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="shipyaari-label-'.$awb.'.pdf"',
            ]);
        }

        return back()->with('error', 'Unexpected response format from Shipyaari');
    } catch (\Exception $e) {
        return back()->with('error', 'API Error: '.$e->getMessage());
    }
}

}