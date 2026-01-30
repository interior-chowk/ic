<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ShiprocketCourier;
use App\Model\OrderTransaction;
use App\Model\Product;
use App\Model\SellerWallet;
use App\Model\Shop;
use App\Model\WithdrawalMethod;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class DashboardController extends Controller
{
    
    public function order_transaction_table_data_filter($request)
    {
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $customer_id = $request['customer_id'] ?? 'all';
        $status = $request['status'] ?? 'all';
        $date_type = $request['date_type'] ?? 'this_year';
        $product_id = $request['product_id'] ?? 'all';

        $transaction_query = OrderTransaction::with(['seller.shop', 'customer', 'order.delivery_man'])
            ->with(['order_details'=> function ($query) {
                $query->selectRaw("*, sum(qty*price) as order_details_sum_price, sum(discount) as order_details_sum_discount")
                    ->groupBy('order_id');
            }])
            ->when($search, function ($q) use ($search) {
                $q->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%");
            })
            ->when($status != 'all', function ($query) use ($status) {
                $query->where(['status' => $status]);
            })
            ->when($customer_id != 'all', function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            
            ->where(['seller_is'=>'seller', 'seller_id'=>auth('seller')->id()]);
        $transactions = self::date_wise_common_filter($transaction_query, $date_type, $from, $to);

        return $transactions;
    }

    public function date_wise_common_filter($query, $date_type, $from, $to)
    {
        return $query->when(($date_type == 'this_year'), function ($query) {
            return $query->whereYear('updated_at', date('Y'));
        })
            ->when(($date_type == 'this_month'), function ($query) {
                return $query->whereMonth('updated_at', date('m'))
                    ->whereYear('updated_at', date('Y'));
            })
            ->when(($date_type == 'this_week'), function ($query) {
                return $query->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($date_type == 'custom_date' && !is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                return $query->whereDate('updated_at', '>=', $from)
                    ->whereDate('updated_at', '<=', $to);
            });
    }

    public function order_transaction_chart_filter($request)
    {
        $from = $request['from'];
        $to = $request['to'];
        $date_type = $request['date_type'] ?? 'this_year';

        if ($date_type == 'this_year') { //this year table
            $number = 12;
            $default_inc = 1;
            $current_start_year = date('Y-01-01');
            $current_end_year = date('Y-12-31');
            $from_year = Carbon::parse($from)->format('Y');

            $this_year = self::order_transaction_same_year($request, $current_start_year, $current_end_year, $from_year, $number, $default_inc);
            return $this_year;

        } elseif ($date_type == 'this_month') { //this month table
            $current_month_start = date('Y-m-01');
            $current_month_end = date('Y-m-t');
            $inc = 1;
            $month = date('m');
            $number = date('d', strtotime($current_month_end));

            $this_month = self::order_transaction_same_month($request, $current_month_start, $current_month_end, $month, $number, $inc);
            return $this_month;

        } elseif ($date_type == 'this_week') {
            $this_week = self::order_transaction_this_week($request);
            return $this_week;

        } elseif ($date_type == 'custom_date' && !empty($from) && !empty($to)) {
            $start_date = Carbon::parse($from)->format('Y-m-d 00:00:00');
            $end_date = Carbon::parse($to)->format('Y-m-d 23:59:59');
            $from_year = Carbon::parse($from)->format('Y');
            $from_month = Carbon::parse($from)->format('m');
            $from_day = Carbon::parse($from)->format('d');
            $to_year = Carbon::parse($to)->format('Y');
            $to_month = Carbon::parse($to)->format('m');
            $to_day = Carbon::parse($to)->format('d');

            if ($from_year != $to_year) {
                $different_year = self::order_transaction_different_year($request, $start_date, $end_date, $from_year, $to_year);
                return $different_year;

            } elseif ($from_month != $to_month) {
                $same_year = self::order_transaction_same_year($request, $start_date, $end_date, $from_year, $to_month, $from_month);
                return $same_year;

            } elseif ($from_month == $to_month) {
                $same_month = self::order_transaction_same_month($request, $start_date, $end_date, $from_month, $to_day, $from_day);
                return $same_month;
            }

        }
    }

    public function order_transaction_same_year($request, $start_date, $end_date, $from_year, $number, $default_inc)
    {

        $orders = self::order_transaction_date_common_query($request, $start_date, $end_date)
            ->selectRaw('sum(order_amount) as order_amount, YEAR(updated_at) year, MONTH(updated_at) month')
            ->groupBy(DB::raw("DATE_FORMAT(updated_at, '%M')"))
            ->latest('updated_at')->get();

        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $month = date("F", strtotime("2023-$inc-01"));
            $order_amount[$month . '-' . $from_year] = 0;
            foreach ($orders as $match) {
                if ($match['month'] == $inc) {
                    $order_amount[$month . '-' . $from_year] = $match['order_amount'];
                }
            }
        }

        return array(
            'order_amount' => $order_amount,
        );
    }

    public function order_transaction_same_month($request, $start_date, $end_date, $month_date, $number, $default_inc)
    {
        $year_month = date('Y-m', strtotime($start_date));
        $month = date("F", strtotime("$year_month"));
        $orders = self::order_transaction_date_common_query($request, $start_date, $end_date)
            ->selectRaw('sum(order_amount) as order_amount, YEAR(updated_at) year, MONTH(updated_at) month, DAY(updated_at) day')
            ->groupBy(DB::raw("DATE_FORMAT(updated_at, '%D')"))
            ->latest('updated_at')->get();

        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $day = date('jS', strtotime("$year_month-$inc"));
            $order_amount[$day . '-' . $month] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $inc) {
                    $order_amount[$day . '-' . $month] = $match['order_amount'];
                }
            }
        }

        return array(
            'order_amount' => $order_amount,
        );
    }

    public function order_transaction_date_common_query($request, $start_date, $end_date)
    {
        $customer_id = $request['customer_id'] ?? 'all';
        $status = $request['status'] ?? 'all';

        $query = Order::with('order_transaction')
            ->where('payment_status', 'paid')
            ->when($status != 'all', function ($query) use ($status) {
                $query->whereHas('order_transaction', function ($query) use ($status) {
                    $query->where(['status' => $status]);
                });
            })
            ->when($customer_id != 'all', function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->where(['seller_is'=>'seller', 'seller_id'=>auth('seller')->id()])
            ->whereDate('updated_at', '>=', $start_date)
            ->whereDate('updated_at', '<=', $end_date);

        return $query;
    }

    public function order_transaction_piechart_query($request, $query)
    {
        $from = $request['from'];
        $to = $request['to'];
        $customer_id = $request['customer_id'] ?? 'all';
        $status = $request['status'] ?? 'all';
        $date_type = $request['date_type'] ?? 'this_year';

        $query_data = $query->where(['payment_status' => 'paid'])
            ->whereHas('order_transaction', function ($query) use ($status) {
                $query->when($status != 'all', function ($query) use ($status) {
                    $query->where(['status' => $status]);
                });
            })
            ->when($customer_id != 'all', function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->where(['seller_is'=>'seller', 'seller_id'=>auth('seller')->id()]);

        return self::date_wise_common_filter($query_data, $date_type, $from, $to);
    }

    public function order_transaction_this_week($request)
    {
        $start_date = Carbon::now()->startOfWeek();
        $end_date = Carbon::now()->endOfWeek();

        $number = 6;
        $period = CarbonPeriod::create(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $day_name = array();
        foreach ($period as $date) {
            array_push($day_name, $date->format('l'));
        }

        $orders = self::order_transaction_date_common_query($request, $start_date, $end_date)
            ->select(
                DB::raw('sum(order_amount) as order_amount'),
                DB::raw("(DATE_FORMAT(updated_at, '%W')) as day")
            )
            ->groupBy(DB::raw("DATE_FORMAT(updated_at, '%D')"))
            ->latest('updated_at')->get();

        for ($inc = 0; $inc <= $number; $inc++) {
            $order_amount[$day_name[$inc]] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $day_name[$inc]) {
                    $order_amount[$day_name[$inc]] = $match['order_amount'];
                }
            }
        }

        return array(
            'order_amount' => $order_amount,
        );
    }

    public function all_order_table_data_filter($request)
    {
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $seller_id = $request['seller_id'] ?? 'all';
        $product_id = $request['product_id'] ?? 'all';
        $date_type = $request['date_type'] ?? '';

        $orders_query = Order::withSum('details', 'tax')
            ->withSum('details', 'discount')
            ->when($search, function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%");
            })

            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            })
            ->when($product_id != 'all', function ($query) use ($product_id) {
                $query->whereHas('products', function ($q) use ($product_id) {
                        $q->where('order_details.product_id', $product_id);
                });
            });

        $orders = self::date_wise_common_filter($orders_query, $date_type, $from, $to);
          //dd($orders);
        return $orders;
    }

    public function dashboard(Request $request)
    {
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $customer_id = $request['customer_id'] ?? 'all';
        $status = $request['status'] ?? 'all';
        $date_type = $request['date_type'] ?? 'this_year';
        $query_param = ['search' => $search, 'status' => $status, 'customer_id' => $customer_id, 'date_type' => $date_type, 'from' => $from, 'to' => $to];

        $customers = User::whereNotIn('id', [0])->get();

        $transactions = self::order_transaction_table_data_filter($request);
        $transactions = $transactions->latest('updated_at')->paginate(5)->appends($query_param);
       
        $order_transaction_chart = self::order_transaction_chart_filter($request);

        $active_products = Product::where([
            'user_id'=>auth('seller')->id(),
            'added_by'=>'seller',
            'status'=>1,
            'request_status'=>1
        ])->count();

        $inactive_products = Product::where([
            'user_id'=>auth('seller')->id(),
            'added_by'=>'seller',
            'status'=>0,
            'request_status'=>1
        ])->count();

        $pending_products = Product::where([
            'user_id'=>auth('seller')->id(),
            'added_by'=>'seller',
            'status'=>0,
            'request_status'=>0
        ])->count();

        // $totalIncomeData = DB::table('order_details')
        //     ->where('seller_id', auth('seller')->id())
        //     ->get();  

        $totalIncomeQuery = DB::table('order_details')
            ->where('seller_id', auth('seller')->id());

        if (!empty($request->search)) {
            $totalIncomeQuery->where(function($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%')
                ->orWhere('product_name', 'like', '%' . $request->search . '%');
            });
        }

        if (!empty($request->customer_id) && $request->customer_id != 'all') {
            $totalIncomeQuery->where('customer_id', $request->customer_id);
        }

        if (!empty($request->status) && $request->status != 'all') {
            $totalIncomeQuery->where('status', $request->status);
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $totalIncomeQuery->whereBetween('created_at', [$from, $to]);
        } else {
            switch ($request->date_type) {
                case 'this_month':
                    $totalIncomeQuery->whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $totalIncomeQuery->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $totalIncomeQuery->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $totalIncomeQuery->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        $totalIncomeData = $totalIncomeQuery->get();


        $totalIncome = 0;

        foreach ($totalIncomeData as $item) {
            // dd($item);

            if ($item->discount_type == 'flat') {
                $finalPrice = ($item->price - $item->discount) * $item->qty;

            } elseif ($item->discount_type == 'percent') {
                $finalPrice = ($item->price - ($item->price * $item->discount / 100) * $item->qty );

            } else {
                $finalPrice = $item->price * $item->qty;
            }

            $totalIncome += $finalPrice;
        }

        $deliveredIncome = 0;

        // $deliveredOrders = DB::table('order_details')
        //     ->join('shiprocket_couriers', 'shiprocket_couriers.order_id', '=', 'order_details.order_id')
        //     ->where('order_details.seller_id', auth('seller')->id())
        //     ->where('shiprocket_couriers.status', 'DELIVERED') // ✅ Case fixed
        //     ->select(
        //         'order_details.*',
        //         'shiprocket_couriers.delivered_at'
        //     )
        //     ->get();

        $deliveredOrdersQuery = DB::table('order_details')
            ->join('shiprocket_couriers', 'shiprocket_couriers.order_id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', auth('seller')->id())
            ->where('shiprocket_couriers.status', 'DELIVERED') // ✅ only delivered
            ->select(
                'order_details.*',
                'shiprocket_couriers.delivered_at'
            );

        // Filter by search
        if (!empty($request->search)) {
            $deliveredOrdersQuery->where(function($q) use ($request) {
                $q->where('order_details.order_id', 'like', '%' . $request->search . '%')
                ->orWhere('order_details.product_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by customer_id
        if (!empty($request->customer_id) && $request->customer_id != 'all') {
            $deliveredOrdersQuery->where('order_details.customer_id', $request->customer_id);
        }

        // Filter by status (if needed, otherwise already DELIVERED)
        if (!empty($request->status) && $request->status != 'all') {
            $deliveredOrdersQuery->where('order_details.status', $request->status);
        }

        // Filter by date (use delivered_at)
        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $deliveredOrdersQuery->whereBetween('shiprocket_couriers.delivered_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $deliveredOrdersQuery->whereMonth('shiprocket_couriers.delivered_at', Carbon::now()->month)
                                        ->whereYear('shiprocket_couriers.delivered_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $deliveredOrdersQuery->whereYear('shiprocket_couriers.delivered_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $deliveredOrdersQuery->whereYear('shiprocket_couriers.delivered_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $deliveredOrdersQuery->where('shiprocket_couriers.delivered_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        // Get the filtered delivered orders
        $deliveredOrders = $deliveredOrdersQuery->get();

        // dd($deliveredOrders);

        foreach ($deliveredOrders as $item) {

            $productDetails = json_decode($item->product_details, true);

            $returnDays = isset($productDetails['Return_days']) 
                ? (int) $productDetails['Return_days'] 
                : 0;

            if ($returnDays <= 0) {
                continue;
            }

            $deliveredDate = Carbon::parse($item->delivered_at);

            $returnExpireDate = $deliveredDate
                ->copy()
                ->addDays($returnDays)
                ->endOfDay();

            if (now()->greaterThan($returnExpireDate)) {

                if ($item->discount_type == 'flat') {
                    $finalPrice = ($item->price - $item->discount) * $item->qty;
                } elseif ($item->discount_type == 'percent') {
                    $finalPrice = ($item->price - ($item->price * $item->discount / 100) * $item->qty);
                } else {
                    $finalPrice = $item->price * $item->qty;
                }                

                $deliveredIncome += $finalPrice;
            }
        }
        
        $sellerEarnings = [];
        $totalearning = 0;

        $totalEarningData = DB::table('order_details')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->where('order_details.seller_id', auth('seller')->id())
        ->select(
            'order_details.*',
            'orders.shipping_cost',
            'orders.shipping_cost_amt',
            'orders.discount_amount',
            'orders.order_status',
            'orders.created_at as order_date'
        )
        ->get();
        
        $earning = 0;

        foreach ($totalEarningData as $details) {

            $flat = 0;
            $percent = 0;

            $product = json_decode($details->product_details, true);

            if ($details->discount_type == 'flat') {
                $flat = $details->discount;
            } elseif ($details->discount_type == 'percent') {
                $percent = ($details->price * $details->discount) / 100;
            }

            $sku = DB::table('sku_product_new')
                ->where('product_id', $product['id'])
                ->where('variation', $details->variant)
                ->first();

            // ✅ Safe Fallbacks
            $listedPrice   = $sku->listed_price ?? ($details->price - $flat - $percent);
            $commissionFee = $sku->commission_fee ?? 0;
            $listedPercent = $sku->listed_percent ?? 0;
            $sellerId      = $details->seller_id;
            $qty           = $details->qty;
            $shippingCost  = $details->shipping_cost;
            $shippingAmt   = $details->shipping_cost_amt;
            $discountAmt   = $details->discount_amount;

            $subTotal = $listedPrice * $qty;

            $commissionBase =
                (($subTotal + $shippingAmt + $discountAmt) * 5) / 100 +
                ($commissionFee * ($listedPercent * $qty)) / 100;

            $commission =
                ($commissionBase * 0.18) +
                $commissionBase;

            if ($shippingCost != 0) {

                $earning =
                    $subTotal +
                    $shippingAmt +
                    $discountAmt -
                    $shippingAmt -
                    $commission -
                    $discountAmt;

            } else {

                $earning =
                    $subTotal -
                    $commission -
                    $shippingAmt;
            }

            $earning = round($earning, 2);
            
        }

        $totalEarningDatas = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('shiprocket_couriers', 'shiprocket_couriers.order_id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', auth('seller')->id())
            ->where('orders.statement', 2)
            ->where('shiprocket_couriers.status', 'delivered')
            ->whereRaw("
                NOW() > DATE_ADD(
                    shiprocket_couriers.delivered_at,
                    INTERVAL JSON_EXTRACT(order_details.product_details,'$.Return_days') DAY
                )
            ")
            ->select(
                'order_details.*',
                'orders.shipping_cost',
                'orders.shipping_cost_amt',
                'orders.discount_amount'
            )
            ->get();


        foreach ($totalEarningDatas as $details) {

            // ✅ Reset each loop
            $flat = 0;
            $percent = 0;

            $product = json_decode($details->product_details, true);

            // ✅ Discount Logic
            if ($details->discount_type == 'flat') {
                $flat = $details->discount;
            } elseif ($details->discount_type == 'percent') {
                $percent = ($details->price * $details->discount) / 100;
            }

            // ✅ Get SKU (Commission + Price will come from here)
            $sku = DB::table('sku_product_new')
                ->where('product_id', $product['id'])
                ->where('variation', $details->variant)
                ->first();

            // ✅ Safe Fallbacks
            $listedPrice   = $sku->listed_price ?? ($details->price - $flat - $percent);
            $commissionFee = $sku->commission_fee ?? 0;
            $listedPercent = $sku->listed_percent ?? 0;
            $sellerId      = $details->seller_id;
            $qty           = $details->qty;
            $shippingCost  = $details->shipping_cost;
            $shippingAmt   = $details->shipping_cost_amt;
            $discountAmt   = $details->discount_amount;

            $subTotal = $listedPrice * $qty;

            $commissionBase =
                (($subTotal + $shippingAmt + $discountAmt) * 5) / 100 +
                ($commissionFee * ($listedPercent * $qty)) / 100;

            $commission =
                ($commissionBase * 0.18) +
                $commissionBase;

            if ($shippingCost != 0) {

                $earningss =
                    $subTotal +
                    $shippingAmt +
                    $discountAmt -
                    $shippingAmt -
                    $commission +
                    $discountAmt;

            } else {

                $earningss=
                    $subTotal -
                    $commission -
                    $shippingAmt;
            }

            $earningss = round($earningss, 2);
            
        }

    //    $orders = DB::table('orders')
    //         ->whereNull('order_return_id')
    //         ->where('seller_id', auth('seller')->id())
    //         ->get();

        $ordersQuery = DB::table('orders')
        ->whereNull('order_return_id')
        ->where('seller_id', auth('seller')->id());

        // Filter by search (order ID or product name if available)
        if (!empty($request->search)) {
            $ordersQuery->where(function($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%')
                ->orWhere('product_name', 'like', '%' . $request->search . '%'); // if product_name exists in orders
            });
        }

        // Filter by customer_id
        if (!empty($request->customer_id) && $request->customer_id != 'all') {
            $ordersQuery->where('customer_id', $request->customer_id);
        }

        // Filter by status
        if (!empty($request->status) && $request->status != 'all') {
            $ordersQuery->where('status', $request->status);
        }

        // Filter by date using created_at
        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $ordersQuery->whereBetween('created_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $ordersQuery->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $ordersQuery->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $ordersQuery->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $ordersQuery->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        // Get the filtered orders
        $orders = $ordersQuery->get();

                
        $totalearning = [];

        foreach ($orders as $order) {

            $orderDetails = Order::with(['seller', 'shipping', 'details.product_all_status'])
                ->where('id', $order->id)
                ->first();

            if (!$orderDetails) continue;

            foreach ($orderDetails->details as $details) {

                $sku_product = DB::table('sku_product_new')
                    ->where('product_id', $details->product_id)
                    ->where('variation', $details->variant)
                    ->first();

                if (!$sku_product) continue;

                $productData = !empty($details->product_details)
                    ? json_decode($details->product_details, true)
                    : [];

                $price_product = $productData['unit_price'] ?? 0;

                // ✅ Safe discount calculation
                if (($productData['discount_type'] ?? '') == 'percent') {
                    $discountPrice = $price_product - (($productData['discount'] ?? 0) / 100) * $price_product;
                } else {
                    $discountPrice = $price_product - ($productData['discount'] ?? 0);
                }

                // ✅ Variant price override (FIXED object access)
                if (!empty($details->variant)) {

                    $priceData = isset($productData['variation'])
                        ? json_decode($productData['variation'], true)
                        : [];

                    if (!empty($priceData)) {
                        foreach ($priceData as $priceValue) {
                            if ($details->variant == $priceValue['type']) {

                                $price_product = $priceValue['price'];

                                if (($productData['discount_type'] ?? '') == 'percent') {
                                    $discountPrice = $price_product - (($productData['discount'] ?? 0) / 100) * $price_product;
                                } else {
                                    $discountPrice = $price_product - ($productData['discount'] ?? 0);
                                }
                            }
                        }
                    }
                }

                // ✅ Tax safe
                $tax = $details->product_all_status->tax ?? 0;

                $item_tax =
                    ($discountPrice -
                        ($discountPrice * 100) / ($tax + 100)) *
                    $details->qty;

                $amount_without_tax =
                    ($discountPrice * 100) / ($tax + 100);

                // ✅ ✅ YOUR ORIGINAL EARNING FORMULA (UNCHANGED)
                if ($order->shipping_cost != 0) {

                    $earning =
                        $sku_product->listed_price * $details->qty +
                        $order->shipping_cost_amt +
                        $order->discount_amount -
                        $order->shipping_cost_amt -
                        (((($sku_product->listed_price * $details->qty +
                                $order->shipping_cost_amt +
                                $order->discount_amount) *
                                5) /
                                100 +
                            ($sku_product->commission_fee *
                                ($sku_product->listed_percent * $details->qty)) /
                                100) *
                            0.18 +
                            (($sku_product->listed_price * $details->qty +
                                    $order->shipping_cost_amt +
                                    $order->discount_amount) *
                                    5) /
                                100 +
                            ($sku_product->commission_fee *
                                ($sku_product->listed_percent * $details->qty)) /
                                100) -
                        $order->discount_amount;

                } else {

                    $commission =
                        ((($sku_product->listed_price * $details->qty +
                                $order->shipping_cost +
                                $order->discount_amount) *
                                5) /
                                100 +
                            ($sku_product->commission_fee *
                                ($sku_product->listed_percent * $details->qty)) /
                                100) *
                            0.18 +
                        (($sku_product->listed_price * $details->qty +
                                $order->shipping_cost +
                                $order->discount_amount) *
                                5) /
                            100 +
                        ($sku_product->commission_fee *
                            ($sku_product->listed_percent * $details->qty)) /
                            100;

                    $earning =
                        $sku_product->listed_price * $details->qty -
                        $commission -
                        $order->shipping_cost_amt;
                }

                $earning = round($earning, 2);

                $sellerId = $sku_product->seller_id;

                if (!isset($totalearning[$sellerId])) {
                    $totalearning[$sellerId] = 0;
                }

                $totalearning[$sellerId] += $earning;
            }
        }
        
        // $productCount = DB::table('recently_view')
        // ->join('sku_product_new', 'sku_product_new.product_id', '=', 'recently_view.product_id')
        // ->where('sku_product_new.seller_id', auth('seller')->id())
        // ->sum('recently_view.counts');
        
        // $wishlistCount = DB::table('wishlists')
        // ->join('sku_product_new', 'sku_product_new.product_id', '=', 'wishlists.product_id')
        // ->where('sku_product_new.seller_id', auth('seller')->id())
        // ->distinct('wishlists.id')
        // ->count('wishlists.id');

        // $cartCount = DB::table('new_cart')
        // ->where('new_cart.seller_id', auth('seller')->id())
        // ->sum('new_cart.quantity');

        // $totalCustomers = OrderDetail::where('seller_id',auth('seller')->id())->count();
        // dd($totalCustomers);

        $productCountQuery = DB::table('recently_view')
            ->join('sku_product_new', 'sku_product_new.product_id', '=', 'recently_view.product_id')
            ->where('sku_product_new.seller_id', auth('seller')->id());

        if (!empty($request->search)) {
            $productCountQuery->where('sku_product_new.product_name', 'like', '%' . $request->search . '%');
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $productCountQuery->whereBetween('recently_view.created_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $productCountQuery->whereMonth('recently_view.created_at', Carbon::now()->month)
                                    ->whereYear('recently_view.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $productCountQuery->whereYear('recently_view.created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $productCountQuery->whereYear('recently_view.created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $productCountQuery->where('recently_view.created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        $productCount = $productCountQuery->sum('recently_view.counts');


        // ---------------------- 2️⃣ Wishlist count ----------------------
        $wishlistCountQuery = DB::table('wishlists')
            ->join('sku_product_new', 'sku_product_new.product_id', '=', 'wishlists.product_id')
            ->where('sku_product_new.seller_id', auth('seller')->id());

        if (!empty($request->search)) {
            $wishlistCountQuery->where('sku_product_new.product_name', 'like', '%' . $request->search . '%');
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $wishlistCountQuery->whereBetween('wishlists.created_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $wishlistCountQuery->whereMonth('wishlists.created_at', Carbon::now()->month)
                                    ->whereYear('wishlists.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $wishlistCountQuery->whereYear('wishlists.created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $wishlistCountQuery->whereYear('wishlists.created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $wishlistCountQuery->where('wishlists.created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        $wishlistCount = $wishlistCountQuery->distinct('wishlists.id')->count('wishlists.id');


        // ---------------------- 3️⃣ Cart count ----------------------
        $cartCountQuery = DB::table('new_cart')
            ->where('new_cart.seller_id', auth('seller')->id());

        if (!empty($request->search)) {
            $cartCountQuery->join('sku_product_new', 'sku_product_new.product_id', '=', 'new_cart.product_id')
                        ->where('sku_product_new.product_name', 'like', '%' . $request->search . '%');
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $cartCountQuery->whereBetween('new_cart.created_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $cartCountQuery->whereMonth('new_cart.created_at', Carbon::now()->month)
                                ->whereYear('new_cart.created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $cartCountQuery->whereYear('new_cart.created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $cartCountQuery->whereYear('new_cart.created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $cartCountQuery->where('new_cart.created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        $cartCount = $cartCountQuery->sum('new_cart.quantity');

        $totalCustomersQuery = \App\Model\OrderDetail::where('seller_id', auth('seller')->id());

        if (!empty($request->search)) {
            $totalCustomersQuery->where('order_id', 'like', '%' . $request->search . '%');
        }

        if (!empty($request->customer_id) && $request->customer_id != 'all') {
            $totalCustomersQuery->where('customer_id', $request->customer_id);
        }

        if (!empty($request->status) && $request->status != 'all') {
            $totalCustomersQuery->where('status', $request->status);
        }

        if (!empty($request->from) && !empty($request->to)) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $totalCustomersQuery->whereBetween('created_at', [$from, $to]);
        } else {
            switch ($request->date_type ?? 'this_year') {
                case 'this_month':
                    $totalCustomersQuery->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'this_year':
                    $totalCustomersQuery->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $totalCustomersQuery->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
                case 'last_30_days':
                    $totalCustomersQuery->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
        }

        $totalCustomers = $totalCustomersQuery->count();

        $bestSellingProducts = DB::table('order_details')
        ->join('sku_product_new', function($join) {
            $join->on('sku_product_new.product_id', '=', 'order_details.product_id')
                ->whereRaw("
                    sku_product_new.variation COLLATE utf8mb4_unicode_ci 
                    = order_details.variant COLLATE utf8mb4_unicode_ci
                ");
        })
        ->join('products', 'products.id', '=', 'order_details.product_id')
        ->where('order_details.seller_id', auth('seller')->id())
        ->select(
            'order_details.product_id',
            'products.name',
            'sku_product_new.listed_price',
            'sku_product_new.thumbnail_image',
            'sku_product_new.variation',
            DB::raw('SUM(order_details.qty) as total_quantity'),
            DB::raw('SUM(sku_product_new.listed_price * order_details.qty) as total_amount')
        )
        ->groupBy(
            'order_details.product_id',
            'products.name',
            'sku_product_new.listed_price',
            'sku_product_new.thumbnail_image',
            'sku_product_new.variation'
        )
        ->orderByDesc('total_quantity')
        ->limit(7)
        ->get();

        $bestSellingCategories = DB::table('order_details')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('order_details.seller_id', auth('seller')->id())
            ->select(
                'products.category_id',
                'categories.name as category_name',
                DB::raw('SUM(order_details.qty) as total_qty')
            )
            ->groupBy('products.category_id')
            ->orderByDesc('total_qty')
            ->limit(4) // top 4 categories
            ->get();

        $maxCategorySale = $bestSellingCategories->max('total_qty'); // for percentage bar

        $paidTotalEarning   = 0;
        $paidCollectionEarning = 0;
        $dueTotalEarning = 0;

        // $orders = self::all_order_table_data_filter($request)
        //     ->whereNull('order_return_id')
        //     ->where('seller_id', auth('seller')->id())
        //     ->get();

        $orders = $ordersQuery->get();

        foreach ($orders as $order) {

            $orderDetails = Order::with(['details.product_all_status'])
                ->where('id', $order->id)
                ->first();

            if (!$orderDetails) continue;

            foreach ($orderDetails->details as $details) {

                $sku_product = DB::table('sku_product_new')
                    ->where('product_id', $details->product_id)
                    ->where('variation', $details->variant)
                    ->first();

                if (!$sku_product) continue;

                $shiprocket = DB::table('shiprocket_couriers')
                    ->where('order_id', $order->id)
                    ->where('status', 'Delivered')
                    ->first();

                if (!$shiprocket || empty($shiprocket->delivered_at)) continue;

                $returnWindowClosed = false;

                if (!empty($details->product_details)) {

                    $productData = json_decode($details->product_details, true);

                    if (json_last_error() === JSON_ERROR_NONE) {

                        $returnDays = (int) ($productData['Return_days'] ?? 0);

                        if ($returnDays > 0) {

                            $returnLastDate = Carbon::parse($shiprocket->delivered_at)
                                ->addDays($returnDays)
                                ->endOfDay();

                            if (now('Asia/Kolkata')->gt($returnLastDate)) {
                                $returnWindowClosed = true;
                            }
                        }
                    }
                }

                if (!$returnWindowClosed) continue;

                if ($order->shipping_cost != 0) {

                    $commission =
                        ((($sku_product->listed_price * $details->qty +
                                $order->shipping_cost_amt +
                                $order->discount_amount) * 5) / 100 +
                            ($sku_product->commission_fee *
                                ($sku_product->listed_percent * $details->qty)) / 100) * 0.18 +
                        (($sku_product->listed_price * $details->qty +
                                $order->shipping_cost_amt +
                                $order->discount_amount) * 5) / 100 +
                        ($sku_product->commission_fee *
                            ($sku_product->listed_percent * $details->qty)) / 100;

                    $earning =
                        ($sku_product->listed_price * $details->qty +
                        $order->shipping_cost_amt +
                        $order->discount_amount)
                        - $commission
                        - $order->shipping_cost_amt
                        - $order->discount_amount;

                } else {

                    $commission =
                        ((($sku_product->listed_price * $details->qty +
                                $order->shipping_cost +
                                $order->discount_amount) * 5) / 100 +
                            ($sku_product->commission_fee *
                                ($sku_product->listed_percent * $details->qty)) / 100) * 0.18 +
                        (($sku_product->listed_price * $details->qty +
                                $order->shipping_cost +
                                $order->discount_amount) * 5) / 100 +
                        ($sku_product->commission_fee *
                            ($sku_product->listed_percent * $details->qty)) / 100;

                    $earning =
                        ($sku_product->listed_price * $details->qty)
                        - $commission
                        - $order->shipping_cost_amt;
                }

                $earning = round($earning, 2);

                $paidTotalEarning += $earning;
                if($order->statement == 2)
                {
                    $paidCollectionEarning += $earning;
                }else
                {
                    $dueTotalEarning += $earning;
                }
                
            }
        }
        
        $sellerId = auth('seller')->id();
        $monthlyorder = DB::table('orders')
        ->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(DISTINCT customer_id) as total_customers')
        )
        ->where('seller_id', $sellerId)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total_customers', 'month');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $monthlyorder[$i] ?? 0;
        }
        
        return view('seller-views.system.dashboard', compact(
            'monthlyData',
            'paidTotalEarning',
            'paidCollectionEarning',
            'dueTotalEarning',
            'totalearning',
            'earning',
            'deliveredIncome',
            'customers',
            'totalIncome',
            'transactions',
            'search',
            'order_transaction_chart',
            'status',
            'date_type',
            'from',
            'to',
            'customer_id',
            'bestSellingProducts',
            'maxCategorySale',
            'bestSellingCategories',
            'totalCustomers',
            'cartCount',
            'wishlistCount',
            'productCount'
        ));

    }
    
    public function order_stats(Request $request)
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::order_stats_data();

        return response()->json([
            'view' => view('seller-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function get_earning_statitics(Request $request)
    {
        $dateType = $request->type;

        $seller_data = array();
        if($dateType == 'yearEarn') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $seller_earnings = OrderTransaction::where([
                'seller_is'=>'seller',
                'seller_id'=>auth('seller')->id(),
                'status'=>'disburse'
            ])->select(
                DB::raw('IFNULL(sum(seller_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $seller_data[$inc] = 0;
                foreach ($seller_earnings as $match) {
                    if ($match['month'] == $inc) {
                        $seller_data[$inc] = $match['sums'];
                    }
                }
            }
            $key_range = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        }elseif($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d',strtotime($to));
            $key_range = range(1, $number);

            $seller_earnings = OrderTransaction::where([
                'seller_is' => 'seller',
                'seller_id' => auth('seller')->id(),
                'status' => 'disburse'
            ])->select(
                DB::raw('seller_amount'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $seller_data[$inc] = 0;
                foreach ($seller_earnings as $match) {
                    if ($match['day'] == $inc) {
                        $seller_data[$inc] = $match['seller_amount'];
                    }
                }
            }

        }elseif($dateType == 'WeekEarn') {

            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');

            $number_start =date('d',strtotime($from));
            $number_end =date('d',strtotime($to));

            $seller_earnings = OrderTransaction::where([
                'seller_is' => 'seller',
                'seller_id' => auth('seller')->id(),
                'status' => 'disburse'
            ])->select(
                DB::raw('seller_amount'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->get()->toArray();

            for ($inc = $number_start; $inc <= $number_end; $inc++) {
                $seller_data[$inc] = 0;
                foreach ($seller_earnings as $match) {
                    if ($match['day'] == $inc) {
                        $seller_data[$inc] = $match['seller_amount'];
                    }
                }
            }

            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        }

        $seller_label = $key_range;

        $seller_data_final = $seller_data;

        $commission_data = array();
        if($dateType == 'yearEarn') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $commission_earnings = OrderTransaction::where([
                'seller_is'=>'seller',
                'seller_id'=>auth('seller')->id(),
                'status'=>'disburse'
            ])->select(
                DB::raw('IFNULL(sum(admin_commission),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $commission_data[$inc] = 0;
                foreach ($commission_earnings as $match) {
                    if ($match['month'] == $inc) {
                        $commission_data[$inc] = $match['sums'];
                    }
                }
            }

            $key_range = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        }elseif($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d',strtotime($to));
            $key_range = range(1, $number);

            $commission_earnings = OrderTransaction::where([
                'seller_is' => 'seller',
                'seller_id' => auth('seller')->id(),
                'status' => 'disburse'
            ])->select(
                DB::raw('admin_commission'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $commission_data[$inc] = 0;
                foreach ($commission_earnings as $match) {
                    if ($match['day'] == $inc) {
                        $commission_data[$inc] = $match['admin_commission'];
                    }
                }
            }

        }elseif($dateType == 'WeekEarn') {

            $from = Carbon::now()->startOfWeek()->format('Y-m-d');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d');

            $number_start =date('d',strtotime($from));
            $number_end =date('d',strtotime($to));

            $commission_earnings = OrderTransaction::where([
                'seller_is' => 'seller',
                'seller_id' => auth('seller')->id(),
                'status' => 'disburse'
            ])->select(
                DB::raw('admin_commission'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->get()->toArray();

            for ($inc = $number_start; $inc <= $number_end; $inc++) {
                $commission_data[$inc] = 0;
                foreach ($commission_earnings as $match) {
                    if ($match['day'] == $inc) {
                        $commission_data[$inc] = $match['admin_commission'];
                    }
                }
            }
            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        }

        $commission_label = $key_range;

        $commission_data_final = $commission_data;

        $data = array(
            'seller_label' => $seller_label,
            'seller_earn' => array_values($seller_data_final),
            'commission_label' => $commission_label,
            'commission_earn' => array_values($commission_data_final)
        );

        return response()->json($data);
    }

    public function order_stats_data()
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $pending = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'pending'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $confirmed = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'confirmed'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $processing = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'processing'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $out_for_delivery = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'out_for_delivery'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $delivered = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])
            ->where(['order_status' => 'delivered'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $canceled = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'canceled'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $returned = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'returned'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $failed = Order::where(['seller_is' => 'seller'])->where(['seller_id' => auth('seller')->id()])->where(['order_status' => 'failed'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $data = [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $out_for_delivery,
            'delivered' => $delivered,
            'canceled' => $canceled,
            'returned' => $returned,
            'failed' => $failed
        ];

        return $data;
    }

    public function order_transaction_different_year($request, $start_date, $end_date, $from_year, $to_year)
    {

        $orders = self::order_transaction_date_common_query($request, $start_date, $end_date)
            ->selectRaw('sum(order_amount) as order_amount, YEAR(updated_at) year')
            ->groupBy(DB::raw("DATE_FORMAT(updated_at, '%Y')"))
            ->latest('updated_at')->get();

        for ($inc = $from_year; $inc <= $to_year; $inc++) {
            $order_amount[$inc] = 0;
            foreach ($orders as $match) {
                if ($match['year'] == $inc) {
                    $order_amount[$inc] = $match['order_amount'];
                }
            }
        }

        return array(
            'order_amount' => $order_amount,
        );

    }
}