<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WalletTransaction;
use App\Model\CustomerWalletHistory;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class UserWalletController extends Controller
{
    public function list_old(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $user = $request->user();
            
            $total_wallet_balance = $user->wallet_balance;
            
            $wallet_transactio_list = CustomerWalletHistory::where('customer_id',$user->id)
                                                    ->latest()
                                                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);
                                                    
            $wallet_transactio_list_added_by_admin = WalletTransaction::select('user_id','credit','debit','transaction_id','created_at','updated_at','transaction_type')->where('user_id',$user->id)
                                                    ->latest()
                                                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);
                                                    
           $wallet_transactio_list_added_by_admin->map(function ($transaction) {
                $transaction->transaction_type = 0;
               if($transaction->transaction_type == 'add_fund_by_admin')
               {
                $transaction->transaction_typewwww = 1;
               }
                return $transaction;
            });
                                                    
             $total_wallet_transaction = CustomerWalletHistory::where('customer_id', $user->id)->count()
                           + WalletTransaction::where('user_id', $user->id)->count();                                        
           
            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'total_wallet_balance'=>floatval($total_wallet_balance),
                'total_wallet_transactio'=>$total_wallet_transaction,
                'wallet_transactio_list'=>$wallet_transactio_list->items(),
                'wallet_transactio_list_added_by_admin'=>$wallet_transactio_list_added_by_admin->items(),
            ],200);
            
        }else{
            
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }
    
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $wallet_status = Helpers::get_business_settings('wallet_status');
    
        if ($wallet_status == 1) {
            $user = $request->user();
            
            $total_wallet_balance = $user->wallet_balance;
            
            $wallet_transactio_list = CustomerWalletHistory::where('customer_id', $user->id)
                                                ->latest()
                                                ->paginate($request->limit, ['*'], 'page', $request->offset);
            
            $wallet_transactio_list_added_by_admin = WalletTransaction::select('id','user_id','credit','debit','transaction_id','created_at','updated_at','transaction_type')->where('user_id', $user->id)
                                                ->latest()
                                                ->paginate($request->limit, ['*'], 'page', $request->offset);
              $wallet_transactio_list_added_by_admin->map(function ($transaction) {
              $transaction->customer_id = $transaction->user_id;
              $transaction->transaction_amount = $transaction->credit ? $transaction->credit : $transaction->debit;
            
             $transaction_type = $transaction->transaction_type;
             $transaction->transaction_type = ($transaction_type == 'add_fund_by_admin') ? 1 : $transaction_type;
            
             $transaction->transaction_method = $transaction_type;
            
            // Remove original attributes if they are no longer needed
            unset($transaction->user_id, $transaction->credit, $transaction->debit);
            
            return $transaction;
            });                                    
            
            $combined_transaction_list = array_merge($wallet_transactio_list->items(), $wallet_transactio_list_added_by_admin->items());
            
            $total_wallet_transaction = count($wallet_transactio_list) + count($wallet_transactio_list_added_by_admin);
            
            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'total_wallet_balance' => $total_wallet_balance,
                'total_wallet_transactio' => $total_wallet_transaction,
                'wallet_transactio_list' => $combined_transaction_list,
            ], 200);
            
        } else {
            
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }

}