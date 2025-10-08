<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\Model\BusinessSetting;

class GeneralController extends Controller
{
    public function faq(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }
    
    public function e_wallet_Policy()
    {
        return response()->json(BusinessSetting::where('type', 'e_wallet_Policy')->first(), 200);
    }
    
     public function shipping_policy()
    {
        return response()->json(BusinessSetting::where('type', 'shipping_policy')->first(), 200);
    }
    
     public function secure_payment_policy()
    {
        return response()->json(BusinessSetting::where('type', 'secure_payment_policy')->first(), 200);
    }
    
     public function instant_delivery_policy()
    {
        return response()->json(BusinessSetting::where('type', 'instant_delivery_policy')->first(), 200);
    }
}
