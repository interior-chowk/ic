<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Seller;
use App\Model\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class OrderReportController extends Controller
{
    public function sale_register_list(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? '';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();
        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();
        $chart_data = self::order_report_chart_filter($request);
        $orders = self::all_order_table_data_filter($request);
        $orderss = $orders->get();
        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                            ->with('shipping')
                            ->with('details')
                            ->where('id', $order->id)
                            ->first();
                if($orderDetails&& !empty($orderDetails->details)){
                    foreach($orderDetails->details as $key => $details) {
                        $sku_product = DB::table('sku_product_new')->where('product_id',$details->product_id)->where('variation',$details->variant)->first();

                        //dd($sku_product);
                        $productData = json_decode($details->product_details, true);
                        $price_product = $productData['unit_price'];
                        if ($productData['discount_type'] == 'percent') {
                            $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                        } else {
                            $discountPrice = $productData['unit_price'] - $productData['discount'];
                        }

                        if (!empty($details['variant'])) {
                            $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                            if (!empty($priceData)) {
                                foreach ($priceData as $keyss => $priceValue) {
                                    if ($details['variant'] == $priceValue['type']) {
                                        $price_product = $priceValue['price'];
                                        if ($productData['discount_type'] == 'percent') {
                                            $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                        } else {
                                            $discountPrice = $priceValue['price'] - $productData['discount'];
                                        }
                                    }
                                }
                            }
                        }

                        $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                        $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                        $tax_amount_item = $details->product_all_status->tax / 2;
                        $all_coupons += $order->discount_amount;
                        $total_mrp += $details->qty * ($sku_product->listed_price);
                        $sub_total_mrp += $details->qty * ($sku_product->listed_percent);
                        $total_tax+=($details->qty*$sku_product->listed_gst_percent);
                    }
                }   
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;
                $shipping += $instant_delivery_amount;
                $shipping_gst = $shipping * 18 * 0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax;
                $net_base_amount +=  $sub_total_mrp;
                $net_gst += $total_tax;
                $net_total_amount += $total_mrp;
            }
        }   
            //dd($net_base_amount);
        $orders = $orders->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );

        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');

        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');

        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');

        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');

        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');

        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');

        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;

        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
        return view('admin-views.report.order-sale-register', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }

    public function shipping_register_list(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? '';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();
        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();
        $chart_data = self::order_report_chart_filter($request);
        $orders = self::all_order_table_data_filter($request);
        $orderss = $orders->get();
        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                                ->with('shipping')
                                ->with('details')
                                ->where('id', $order->id)
                                ->first();
            if($orderDetails&& !empty($orderDetails->details)){
                foreach($orderDetails->details as $key => $details) {
                    $productData = json_decode($details->product_details, true);
                    $price_product = $productData['unit_price'];
                    if ($productData['discount_type'] == 'percent') {
                        $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                    } else {
                        $discountPrice = $productData['unit_price'] - $productData['discount'];
                    }

                    if (!empty($details['variant'])) {
                        $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                        if (!empty($priceData)) {
                            foreach ($priceData as $keyss => $priceValue) {
                                if ($details['variant'] == $priceValue['type']) {
                                    $price_product = $priceValue['price'];
                                    if ($productData['discount_type'] == 'percent') {
                                        $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    } else {
                                        $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                }
                            }
                        }
                    }

                    $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                    $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                    $tax_amount_item = $details->product_all_status->tax / 2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp += $details->qty * ($discountPrice);
                    $sub_total_mrp += ($details->qty * $amount_without_tax);
                    $total_tax+=$item_tax;
                }
            }
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;

                $shipping += $instant_delivery_amount;
            //   echo "<br>";
                $shipping_gst = $shipping * 18 * 0.01;
            //   echo "<br>";
                $shipping_amount_rate = $shipping - $shipping_gst;
            //  echo "<br>";
            //  echo    $total_tax = $total_tax + $shipping_gst;
            //  echo "<br>";

            // echo     $net_base_amount += $shipping_amount_rate + $sub_total_mrp;
            // echo "<br>";
            //  echo    $net_gst += $total_tax;
            //  echo "<br>";
            //   echo   $net_total_amount += $total_mrp + $shipping - $all_coupons;
            //   echo "<br>";
            $net_base_amount +=$shipping_amount_rate;
            $net_gst +=$shipping_gst;
            $net_total_amount +=$shipping;
            }
        }
        $orders = $orders->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );
        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');

        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');
        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
        return view('admin-views.report.shipping-sale-register', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }

    public function coupon_register_list(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? 'this_year';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();
        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();
        $chart_data = self::order_report_chart_filter($request);
        $orders = self::all_order_table_data_filter($request);
        $orderss = $orders->get();
        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                                ->with('shipping')
                                ->with('details')
                                ->where('id', $order->id)
                                ->where('coupon_code', '!=', 0)
                                ->first();
            if($orderDetails&& !empty($orderDetails->details)){
                foreach($orderDetails->details as $key => $details) {
                    $productData = json_decode($details->product_details, true);
                    $price_product = $productData['unit_price'];
                    if ($productData['discount_type'] == 'percent') {
                        $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                    } else {
                        $discountPrice = $productData['unit_price'] - $productData['discount'];
                    }

                    if (!empty($details['variant'])) {
                        $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                        if (!empty($priceData)) {
                            foreach ($priceData as $keyss => $priceValue) {
                                if ($details['variant'] == $priceValue['type']) {
                                    $price_product = $priceValue['price'];
                                    if ($productData['discount_type'] == 'percent') {
                                        $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    } else {
                                        $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                }
                            }
                        }
                    }

                    $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                    $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                    $tax_amount_item = $details->product_all_status->tax / 2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp += $details->qty * ($discountPrice);
                    $sub_total_mrp += ($details->qty * $amount_without_tax);
                    $total_tax+=$item_tax;
                }
            }
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;

                $shipping += $instant_delivery_amount;
                $shipping_gst = $shipping * 18 * 0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax + $shipping_gst;


                $net_base_amount += $shipping_amount_rate + $sub_total_mrp;
                $net_gst += $total_tax;
                $net_total_amount += $total_mrp + $shipping - $all_coupons;
            }
        }
        $orders = $orders->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );
        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');
        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');

        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
        return view('admin-views.report.coupon-sale-register', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }
    
    public function order_list(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $date_type = $request->date_type ?? 'this_year';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to];
        $sellers = Seller::where(['status'=>'approved'])->get();
        $chart_data = self::order_report_chart_filter($request);
        $orders = self::all_order_table_data_filter($request);
        $orders = $orders->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );
        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');
        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');
        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
           //dd($orders);
        return view('admin-views.report.order-index', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to'));
    }

    public function order_sale(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? '';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();
         
        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();

        $chart_data = self::order_report_chart_filter($request);

        $orders = self::all_order_table_data_filter($request);
         
        $orderss = $orders->get();
        
        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                                ->with('shipping')
                                ->with('details')
                                ->where('id', $order->id)
                                ->first();
            if($orderDetails&& !empty($orderDetails->details)){
                foreach($orderDetails->details as $key => $details) {
                    $productData = json_decode($details->product_details, true);
                    $price_product = $productData['unit_price'];
                    if ($productData['discount_type'] == 'percent') {
                        $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                    } else {
                        $discountPrice = $productData['unit_price'] - $productData['discount'];
                    }

                    if (!empty($details['variant'])) {
                        $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                        if (!empty($priceData)) {
                            foreach ($priceData as $keyss => $priceValue) {
                                if ($details['variant'] == $priceValue['type']) {
                                    $price_product = $priceValue['price'];
                                    if ($productData['discount_type'] == 'percent') {
                                        $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    } else {
                                        $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                }
                            }
                        }
                    }

                    $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                    $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                    $tax_amount_item = $details->product_all_status->tax / 2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp += $details->qty * ($discountPrice);
                    $sub_total_mrp += ($details->qty * $amount_without_tax);
                    $total_tax+=$item_tax;
                }
            }
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;

                $shipping += $instant_delivery_amount;
                $shipping_gst = $shipping * 18 * 0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax ;


                $net_base_amount +=  $sub_total_mrp;
                $net_gst += $total_tax;
                $net_total_amount += $total_mrp;
            }
        }
        $orders = $orders->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );
        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');
        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');
        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
        return view('admin-views.report.order_sale', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }

    public function order_sale_statement(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? '';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();
        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();
        $chart_data = self::order_report_chart_filter($request);
        $orders = self::all_order_table_data_filter($request);
        $orderss = $orders->get();
       // dd($orders->get());
        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                                ->with('shipping')
                                ->with('details')
                                ->where('id', $order->id)
                                ->first();
            if($orderDetails&& !empty($orderDetails->details)){
                foreach($orderDetails->details as $key => $details) {
                    $productData = json_decode($details->product_details, true);
                    $price_product = $productData['unit_price'];
                    if ($productData['discount_type'] == 'percent') {
                        $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                    } else {
                        $discountPrice = $productData['unit_price'] - $productData['discount'];
                    }

                    if (!empty($details['variant'])) {
                        $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                        if (!empty($priceData)) {
                            foreach ($priceData as $keyss => $priceValue) {
                                if ($details['variant'] == $priceValue['type']) {
                                    $price_product = $priceValue['price'];
                                    if ($productData['discount_type'] == 'percent') {
                                        $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    } else {
                                        $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                }
                            }
                        }
                    }

                    $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                    $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                    $tax_amount_item = $details->product_all_status->tax / 2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp += $details->qty * ($discountPrice);
                    $sub_total_mrp += ($details->qty * $amount_without_tax);
                    $total_tax+=$item_tax;
                }
            }
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;

                $shipping += $instant_delivery_amount;
                $shipping_gst = $shipping * 18 * 0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax ;


                $net_base_amount +=  $sub_total_mrp;
                $net_gst += $total_tax;
                $net_total_amount += $total_mrp;
            }
        }
        $orders = $orders->where('statement','0')->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends($query_param);
        //dd($orders);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );
        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');
        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');
        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];

        return view('admin-views.report.statement', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }

    public function statement_update(Request $request)
    {
        $id = $request->id;
        $statement = $request->statement;

        // Example logic to update your DB
        Order::where('id', $id)->update([
            'statement' => $statement,
            'updated_at'=>now()
        ]);

        return response()->json(['success' => true]);
    }

    public function cancellation_list(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $date_type = $request->date_type ?? 'this_year';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to];
        $sellers = Seller::where(['status'=>'approved'])->get();

        $chart_data = self::order_report_chart_filter($request);

        $orders = self::all_order_table_data_filter($request);
        $orders = $orders->orderBy('id','desc')->where('order_status','canceled')->paginate(Helpers::pagination_limit())->appends($query_param);

        $ongoing_order_query = Order::whereIn('order_status',['canceled']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);

        $cancel_order_query = Order::whereIn('order_status',['canceled']);
        $canceled_order = self::order_count($request, $cancel_order_query);

        $delivered_order_query = Order::where('order_status','canceled');
        $delivered_order = self::order_count($request, $delivered_order_query);

        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );

        $due_amount_order_query = Order::whereNotIn('order_status', ['canceled'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');

        $settled_amount_query = Order::where('order_status','canceled')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });

        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');

        $digital_payment_query = Order::where(['order_status' => 'canceled'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');

        $cash_payment_query = Order::where(['order_status' => 'canceled'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');

        $wallet_payment_query = Order::where(['order_status' => 'canceled'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');

        $offline_payment_query = Order::where(['payment_method' => 'canceled']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');

        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;

        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];

        return view('admin-views.report.cancellation_list', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to'));
    }

    public function sale_return(Request $request)
    {
        $seller_id = $request->seller_id ?? 'all';
        $product_id = $request->product_id ?? 'all';
        $date_type = $request->date_type ?? 'this_year';
        $from = $request->from;
        $to = $request->to;
        $search = $request->search;
        $query_param = ['seller_id'=>$seller_id, 'search' => $request->search, 'date_type' => $date_type, 'from' => $from, 'to' => $to, 'product_id' => $product_id];
        $sellers = Seller::where(['status'=>'approved'])->get();

        $products = Product::active()->temporary()->where('request_status',1)->where('status',1)->get();

        $chart_data = self::order_report_chart_filter($request);

        $orders = self::all_order_table_data_filter($request);
        $orderss = $orders->get();

        $net_base_amount = 0;
        $net_gst = 0;
        $net_total_amount = 0;
        if($orderss){
            foreach($orderss as $key => $order) {
                $total_tax = 0;
                $all_coupons = 0;
                $total_mrp = 0;
                $sub_total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
                $shipping = 0;

                $orderDetails = Order::with('seller')
                                ->with('shipping')
                                ->with('details')
                                ->where('id', $order->id)
                                ->first();
            if($orderDetails&& !empty($orderDetails->details)){
                foreach($orderDetails->details as $key => $details) {
                    $productData = json_decode($details->product_details, true);
                    $price_product = $productData['unit_price'];
                    if ($productData['discount_type'] == 'percent') {
                        $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                    } else {
                        $discountPrice = $productData['unit_price'] - $productData['discount'];
                    }

                    if (!empty($details['variant'])) {
                        $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                        if (!empty($priceData)) {
                            foreach ($priceData as $keyss => $priceValue) {
                                if ($details['variant'] == $priceValue['type']) {
                                    $price_product = $priceValue['price'];
                                    if ($productData['discount_type'] == 'percent') {
                                        $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    } else {
                                        $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                }
                            }
                        }
                    }

                    $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                    $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                    $tax_amount_item = $details->product_all_status->tax / 2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp += $details->qty * ($discountPrice);
                    $sub_total_mrp += ($details->qty * $amount_without_tax);
                    $total_tax+=$item_tax;
                }
            }
                $shipping = $order['shipping_cost'];
                $toatal_ship_inst = 0;
                $instant_delivery_amount = 0;

                $shipping += $instant_delivery_amount;
                $shipping_gst = $shipping * 18 * 0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax ;


                $net_base_amount +=  $sub_total_mrp;
                $net_gst += $total_tax;
                $net_total_amount += $total_mrp;
            }
        }

        $orders = $orders->orderBy('id','desc')->where('order_status','delivered')->paginate(Helpers::pagination_limit())->appends($query_param);
        $ongoing_order_query = Order::whereIn('order_status',['out_for_delivery','processing','confirmed', 'pending']);
        $ongoing_order = self::order_count($request, $ongoing_order_query);
        $cancel_order_query = Order::whereIn('order_status',['canceled','failed','returned']);
        $canceled_order = self::order_count($request, $cancel_order_query);
        $delivered_order_query = Order::where('order_status','delivered');
        $delivered_order = self::order_count($request, $delivered_order_query);
        $order_count = array(
            'ongoing_order'=>$ongoing_order,
            'canceled_order'=>$canceled_order,
            'delivered_order'=>$delivered_order,
            'total_order'=>$canceled_order+$ongoing_order+$delivered_order,
        );

        $due_amount_order_query = Order::whereNotIn('order_status', ['delivered', 'canceled', 'returned', 'failed'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $due_amount = self::date_wise_common_filter($due_amount_order_query, $date_type, $from, $to)->sum('order_amount');
        $settled_amount_query = Order::where('order_status','delivered')
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });
        $settled_amount = self::date_wise_common_filter($settled_amount_query, $date_type, $from, $to)->sum('order_amount');
        $digital_payment_query = Order::where(['order_status' => 'delivered'])->whereNotIn('payment_method', ['cash', 'cash_on_delivery', 'pay_by_wallet', 'offline_payment']);
        $digital_payment = self::pie_chart_common_query($request, $digital_payment_query)->sum('order_amount');
        $cash_payment_query = Order::where(['order_status' => 'delivered'])->whereIn('payment_method', ['cash', 'cash_on_delivery']);
        $cash_payment = self::pie_chart_common_query($request, $cash_payment_query)->sum('order_amount');
        $wallet_payment_query = Order::where(['order_status' => 'delivered'])->where(['payment_method' => 'pay_by_wallet']);
        $wallet_payment = self::pie_chart_common_query($request, $wallet_payment_query)->sum('order_amount');
        $offline_payment_query = Order::where(['payment_method' => 'offline_payment']);
        $offline_payment = self::pie_chart_common_query($request, $offline_payment_query)->sum('order_amount');
        $total_payment = $cash_payment + $wallet_payment + $digital_payment + $offline_payment;
        $payment_data = [
            'total_payment' => $total_payment,
            'cash_payment' => $cash_payment,
            'wallet_payment' => $wallet_payment,
            'offline_payment' => $offline_payment,
            'digital_payment' => $digital_payment,
        ];
        return view('admin-views.report.sale_return', compact('orders', 'order_count', 'payment_data', 'chart_data', 'due_amount', 'settled_amount', 'sellers', 'seller_id', 'search', 'date_type', 'from', 'to','products','product_id','net_base_amount','net_gst','net_total_amount'));
    }

    public function order_report_chart_filter($request)
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

            $this_year = self::order_report_same_year($request, $current_start_year, $current_end_year, $from_year, $number, $default_inc);
            return $this_year;

        } elseif ($date_type == 'this_month') { //this month table
            $current_month_start = date('Y-m-01');
            $current_month_end = date('Y-m-t');
            $inc = 1;
            $month = date('m');
            $number = date('d', strtotime($current_month_end));

            $this_month = self::order_report_same_month($request, $current_month_start, $current_month_end, $month, $number, $inc);
            return $this_month;

        } elseif ($date_type == 'this_week') {
            $this_week = self::order_report_this_week($request);
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
                $different_year = self::order_report_different_year($request, $start_date, $end_date, $from_year, $to_year);
                return $different_year;

            } elseif ($from_month != $to_month) {
                $same_year = self::order_report_same_year($request, $start_date, $end_date, $from_year, $to_month, $from_month);
                return $same_year;

            } elseif ($from_month == $to_month) {
                $same_month = self::order_report_same_month($request, $start_date, $end_date, $from_month, $to_day, $from_day);
                return $same_month;
            }

        }
    }

    public function order_report_same_year($request, $start_date, $end_date, $from_year, $number, $default_inc)
    {
        $orders = self::order_report_chart_common_query($request, $start_date, $end_date)
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

    public function order_report_same_month($request, $start_date, $end_date, $month_date, $number, $default_inc)
    {
        $year_month = date('Y-m', strtotime($start_date));
        $month = date("F", strtotime("$year_month"));

        $orders = self::order_report_chart_common_query($request, $start_date, $end_date)
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

    public function order_report_this_week($request)
    {
        $start_date = Carbon::now()->startOfWeek();
        $end_date = Carbon::now()->endOfWeek();

        $number = 6;
        $period = CarbonPeriod::create(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $day_name = array();
        foreach ($period as $date) {
            array_push($day_name, $date->format('l'));
        }

        $orders = self::order_report_chart_common_query($request, $start_date, $end_date)
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

    public function order_report_different_year($request, $start_date, $end_date, $from_year, $to_year)
    {
        $orders = self::order_report_chart_common_query($request, $start_date, $end_date)
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

    public function order_report_chart_common_query($request, $start_date, $end_date)
    {
        $seller_id = $request['seller_id'] ?? 'all';

        $query = Order::where([ 'order_status'=>'delivered'])
            ->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            })
            ->whereDate('updated_at', '>=', $start_date)
            ->whereDate('updated_at', '<=', $end_date);

        return $query;
    }

    public function pie_chart_common_query($request, $query)
    {
        $from = $request['from'];
        $to = $request['to'];
        $seller_id = $request['seller_id'] ?? 'all';
        $date_type = $request['date_type'] ?? 'this_year';

        $query_f = $query->when($seller_id != 'all', function ($query) use ($seller_id) {
                $query->when($seller_id == 'inhouse', function ($q) {
                    $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
                })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                    $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
                });
            });

        return self::date_wise_common_filter($query_f, $date_type, $from, $to);;
    }

    public function order_report_export_excel(Request $request)
    {
        $orders = self::all_order_table_data_filter($request)->latest('updated_at')->get();

        $data = array();
        foreach ($orders as $order) {
            $data[] = array(
                'Order ID' => $order->id,
                'Total Amount' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->order_amount)),
                'Product Discount' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->details_sum_discount)),
                'Coupon Discount' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->discount_amount)),
                'Shipping Charge' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->shipping_cost)),
                'VAT/TAX' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->details_sum_tax)),
                'Commission' => BackEndHelper::set_symbol(BackEndHelper::usd_to_currency($order->admin_commission)),
                'Status' => BackEndHelper::order_status($order->order_status)
            );
        }

        return (new FastExcel($data))->download('order_report_list.xlsx');
    }

    public function seller_earning_excel_sale_export(Request $request)
    {
        $orders = self::all_order_table_data_filter($request)->latest('updated_at')->get();
        $data = array();
        foreach ($orders as $order) {
            // Fetch order details
                        $total_tax=0;
                        $all_coupons=0;
                        $total_mrp = 0;
                        $iteam_tax = 0;
                        $amount_without_tax = 0;
                        $shipping = 0;

            $orderDetails = Order::with('seller')->with('shipping')->with('details')->where('id', $order->id)->first();

            foreach ($orderDetails->details as $details) {
                $productData = json_decode($details->product_details, true);
                $price_product = $productData['unit_price'];
                $discountPrice = $productData['unit_price'];

                // Calculate discount
                if ($productData['discount_type'] == 'percent') {
                    $discountPrice -= ($productData['discount'] / 100) * $productData['unit_price'];
                } else {
                    $discountPrice -= $productData['discount'];
                }

                // Handle variant price if applicable
                if (!empty($details['variant'])) {
                    $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];

                    foreach ($priceData as $priceValue) {
                        if ($details['variant'] == $priceValue['type']) {
                            $price_product = $priceValue['price'];
                            if ($productData['discount_type'] == 'percent') {
                                $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                            } else {
                                $discountPrice = $priceValue['price'] - $productData['discount'];
                            }
                        }
                    }
                }

                // Tax and amount calculations
                $item_tax = ($discountPrice - ($discountPrice * 100) / ($details->product_all_status->tax + 100)) * $details->qty;
                $amount_without_tax = ($discountPrice * 100) / ($details->product_all_status->tax + 100);

                // Determine CGST, SGST, or IGST based on seller and customer states
                $seller_state = $order->seller->shop->state ?? NULL;
                $customer_state = json_decode($order->shipping_address_data)->state ?? 'State not available';
                $cgst = $sgst = $igst = 0;
                if ($customer_state == $seller_state) {
                    $cgst = $item_tax / 2;
                    $sgst = $item_tax / 2;
                } else {
                    $igst = $item_tax;
                }

                // Add data to array
                $data[] = array(
                    'Vendor Code' => 'VN' . $order->seller->id,
                    'Business Name' => $order->seller->shop->name,
                    'Order ID' => $order->id,
                    'Order Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Invoice No' => 'IC/VN' . $order->seller->id . '/' . $order->id,
                    'Invoice Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Bill to' => $order->billingAddress ? $order->billingAddress['contact_person_name'] : "",
                    'Ship to' => $order->shippingAddress ? $order->shippingAddress['contact_person_name'] : "",
                    'Product Name' => \Illuminate\Support\Str::limit($productData['name'], 40, '...'),
                    'Variation' => $details['variant'] ?? '',
                    'Product SKU' => $productData['variation'] ? json_decode($productData['variation'], true)[0]['sku'] ?? 'N/A' : '',
                    'Product Code' => 'PR' . $details['product_id'],
                    'Qty' => $details->qty,
                    'Unit' => 'No',
                    'MRP Per Unit (Incl. GST)' => number_format($price_product,2),
                    'Disc per unit in Rs' => number_format($price_product-$discountPrice,2),
                    'Net /Listed Price per unit (Incl. GST)' => number_format($discountPrice,2),
                    'GST Included in %' => $details->product_all_status->tax,
                    'Base Amount' => number_format($amount_without_tax,2),
                    'CGST' => number_format($cgst,2),
                    'SGST' => number_format($sgst,2),
                    'IGST' => number_format($igst,2),
                    'Bill Amount (Incl. GST)' => number_format(($details->qty * $discountPrice),2)
                );

                    $tax_amount_item = $details->product_all_status->tax/2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp+= $details->qty*($discountPrice) ;
            }
              $totalAll = 0;
              $shipping=$order['shipping_cost'];
              $totalAll = $total_mrp+$shipping-$all_coupons;

              $toatal_ship_inst= 0;
              $instant_delivery_amount = 0;

              $shipping = $shipping + $instant_delivery_amount;

              $shipping_gst = $shipping*18*0.01;
              $shipping_amount_rate = $shipping - $shipping_gst;

              $cgst = $sgst = $igst = 0;
                if ($customer_state == $seller_state) {
                    $cgst = $shipping_gst / 2;
                    $sgst = $shipping_gst / 2;
                } else {
                    $igst = $shipping_gst;
                }

                $shippingName = 'Shipping Charges';
                if($order->instant_delivery_type == 1)
                {
                  $shippingName = 'Shipping + Instant Charges';
                }



             $data[] = array(
                    'Vendor Code' => 'VN' . $order->seller->id,
                    'Business Name' => $order->seller->shop->name,
                    'Order ID' => $order->id,
                    'Order Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Invoice No' => 'IC/VN' . $order->seller->id . '/' . $order->id,
                    'Invoice Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Bill to' => $order->billingAddress ? $order->billingAddress['contact_person_name'] : "",
                    'Ship to' => $order->shippingAddress ? $order->shippingAddress['contact_person_name'] : "",
                    'Product Name' => $shippingName,
                    'Variation' => 1,
                    'Product SKU' => '-',
                    'Product Code' => '-',
                    'Qty' => 1,
                    'Unit' => 'No',
                    'MRP Per Unit (Incl. GST)' => number_format($shipping,2),
                    'Disc per unit in Rs' => '-',
                    'Net /Listed Price per unit (Incl. GST)' => number_format($shipping,2),
                    'GST Included in %' => 18,
                    'Base Amount' => number_format($shipping_amount_rate,2),
                    'CGST' => number_format($cgst,2),
                    'SGST' => number_format($sgst,2),
                    'IGST' => number_format($igst,2),
                    'Bill Amount (Incl. GST)' => number_format($shipping,2)
                );

                $data[] = array(
                    'Vendor Code' => 'VN' . $order->seller->id,
                    'Business Name' => $order->seller->shop->name,
                    'Order ID' => $order->id,
                    'Order Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Invoice No' => 'IC/VN' . $order->seller->id . '/' . $order->id,
                    'Invoice Date' => date('d-m-Y', strtotime($order['created_at'])),
                    'Bill to' => $order->billingAddress ? $order->billingAddress['contact_person_name'] : "",
                    'Ship to' => $order->shippingAddress ? $order->shippingAddress['contact_person_name'] : "",
                    'Product Name' => 'Coupon',
                    'Variation' => 1,
                    'Product SKU' => '-',
                    'Product Code' => '-',
                    'Qty' => 1,
                    'Unit' => '-',
                    'MRP Per Unit (Incl. GST)' => '-',
                    'Disc per unit in Rs' => '-',
                    'Net /Listed Price per unit (Incl. GST)' => '-',
                    'GST Included in %' => '-',
                    'Base Amount' => '-',
                    'CGST' => '-',
                    'SGST' => '-',
                    'IGST' => '-',
                    'Bill Amount (Incl. GST)' => '-'.number_format($all_coupons,2)
                );
        }
        return (new FastExcel($data))->download('order_sales_report_list.xlsx');
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

    public function order_count($request, $query)
    {
        $from = $request['from'];
        $to = $request['to'];
        $seller_id = $request['seller_id'] ?? 'all';
        $date_type = $request['date_type'] ?? 'this_year';

        $query_f = $query->when($seller_id != 'all', function ($query) use ($seller_id) {
            $query->when($seller_id == 'inhouse', function ($q) {
                $q->where(['seller_id' => 1, 'seller_is' => 'admin']);
            })->when($seller_id != 'inhouse', function ($q) use ($seller_id) {
                $q->where(['seller_id' => $seller_id, 'seller_is' => 'seller']);
            });
        });

        $count = self::date_wise_common_filter($query_f, $date_type, $from, $to)->count();
        return $count;
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

    public function made_payment(Request $request)
    {

        $selectedIds = explode(',', $request->selected_ids); // Get selected order IDs as array
        $invoice = Order::wherein('id',$selectedIds)->pluck('invoice_no');
        // dd($request);
        $data = [
            'seller_id'          => $request->seller_id,
            'payment_against'    => $request->payment_against,
            'invoice_no'        => json_encode($invoice),
            'payment_date'       => $request->payment_date,
            'payment_ref_no'     => $request->payment_ref_no,
            'amount'             => $request->total_amount,
            'payment_bank_name'  => $request->payment_bank_name,
            'narration'          => $request->narration,
            'payment_mode'       => $request->payment_mode,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];

        // Insert data
        $inserted = DB::table('made_payement')->insert($data); // ✅ correct table name
    
        if ($inserted) {
            // ✅ Update statement field of selected orders
            Order::whereIn('id', $selectedIds)->update([
                'statement' => 2
            ]);
            
            return back()->with('success', 'Payment recorded successfully.');
        } else {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function payment_record_report(Request $request)
    {
           $payment_record = DB::table('made_payement')
                            ->join('shops','made_payement.seller_id','=','shops.seller_id')
                            ->select('made_payement.*','shops.name')->get();
                            //dd($payment_record);
         
        return view('admin-views.report.payment_record', compact('payment_record'));
    }

}