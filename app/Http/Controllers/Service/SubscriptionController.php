<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Model\Membership;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class SubscriptionController extends Controller
{
    //
    public function list(Request $request)
    {

        $memberships = Membership::all();
        return view('service.subscription.list',compact('memberships'));

    }

    public function createOrder(Request $request)
    {
        $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));

        $order = $api->order->create([
            'receipt'         => 'rcpt_'.$request->plan_id,
            'amount'          => $request->amount,
            'currency'        => 'INR'
        ]);

        return response()->json([
            'razorpay_order_id' => $order['id'],
            'amount' => $order['amount']
        ]);
    }

    public function verifyPayment(Request $request)
    {
       $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));

        $attributes = [
            'razorpay_order_id'   => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature'  => $request->razorpay_signature
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // ✅ Save payment in DB here
        // Membership::activate(...)

        return response()->json(['status' => 'success']);
    }
}