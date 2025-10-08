<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WalletTransaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Http;

class UserWalletController extends Controller
{

    public function index()
    {
        $wallet_status = Helpers::get_business_settings('wallet_status');
        if($wallet_status == 1)
        {
            $total_wallet_balance = auth('customer')->user()->wallet_balance;
            $wallet_transactio_list = WalletTransaction::where('user_id',auth('customer')->id())
                                                        ->latest()
                                                        ->paginate(15);

            return view(VIEW_FILE_NAMES['user_wallet'],compact('total_wallet_balance','wallet_transactio_list'));
        }else{
            Toastr::warning(\App\CPU\translate('access_denied!'));
            return back();
        }
    }

    public function my_wallet_account(){
        return view(VIEW_FILE_NAMES['wallet_account']);
    }


  public function createFundAccount(Request $request)
{
    $user = Auth::user();

    // Step 1: Create Razorpay Contact
    if (!$user->razorpay_contact_id) {
        $response = Http::withBasicAuth(
            config('razor.razor_key'),
            config('razor.razor_secret')
        )->post('https://api.razorpay.com/v1/contacts', [
            'name' => $user->name,
            'email' => $user->email,
            'contact' => $user->phone,
            'type' => 'customer',
            'reference_id' => 'USER_' . $user->id,
            'notes' => [
                'notes_key_1' => 'Created via HTTP client',
                'notes_key_2' => now()->toDateTimeString(),
            ],
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Failed to create Razorpay contact.');
        }

        $contact = $response->json();
        $user->razorpay_contact_id = $contact['id'];
        $user->save();
    }

    // Step 2: Create Fund Account
   // dd($request->ifsc, $request->account_number);
    $fundAccountResponse = Http::withBasicAuth(
        config('razor.razor_key'),
        config('razor.razor_secret')
    )->post('https://api.razorpay.com/v1/fund_accounts', [
        'contact_id' => $user->razorpay_contact_id,
        'account_type' => 'bank_account',
        'bank_account' => [
            'name' => $user->name,
            'ifsc' => $request->ifsc ?? 'AUBL0002485',
            'account_number' => $request->account_number ?? '2401248557528316',
        ],
    ]);

    if ($fundAccountResponse->failed()) {
        return back()->with('error', 'Failed to create Razorpay Fund Account.');
    }

    $fund = $fundAccountResponse->json();
    $user->razorpay_fund_account_id = $fund['id'];
    $user->save();

    return redirect('/withdraw')->with('success', 'Bank details saved successfully.');
}

public function withdraw(Request $request)
{
    $user = Auth::user();
    $amount = $request->amount;

    // Check wallet balance
    if ($user->wallet_balance < $amount) {
        return back()->with('error', 'Insufficient wallet balance');
    }

    // Create RazorpayX Payout
    $response = Http::withBasicAuth(
        config('razor.razor_key'),
        config('razor.razor_secret')
    )->post('https://api.razorpay.com/v1/payouts', [
        "account_number" => "K4OAIEOU2MXcbJ", // RazorpayX virtual account number
        "fund_account_id" => $user->razorpay_fund_account_id,
        "amount" => $amount * 100, // in paise
        "currency" => "INR",
        "mode" => "IMPS",
        "purpose" => "payout",
        "queue_if_low_balance" => true,
    ]);

    if ($response->failed()) {
        return back()->with('error', 'Failed to create RazorpayX payout.');
    }

    // Parse payout ID from response
    $payout = $response->json();

    // Deduct from wallet
    $user->wallet_balance -= $amount;
    $user->save();

    return redirect('/withdraw')->with('success', 'Withdrawal requested. Payout ID: ' . $payout['id']);
}


 public function pincode(Request $request)
    {
        // Check if the pincode exists
     // dd($request->pincode);
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


}
