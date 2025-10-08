@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Sales Return'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/order_report.png')}}" alt="">
                {{\App\CPU\translate('Sales_Return')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="card mb-2">
            <div class="card-body">
                <form action="" id="form-data" method="GET">
                    <h4 class="mb-3">{{\App\CPU\translate('Filter_Data')}}</h4>
                    <div class="row gx-2 gy-3 align-items-center text-left">
                        <div class="col-sm-6 col-md-3">
                            <select class="js-select2-custom form-control text-ellipsis" name="seller_id">
                                <option value="all" {{ $seller_id == 'all' ? 'selected' : '' }}>{{\App\CPU\translate('all_sellers')}}</option>
                                <option value="inhouse" {{ $seller_id == 'inhouse' ? 'selected' : '' }}>{{\App\CPU\translate('In-House')}}</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller['id'] }}" {{ $seller_id == $seller['id'] ? 'selected' : '' }}>
                                        {{$seller->f_name}} {{$seller->l_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <select class="js-select2-custom form-control text-ellipsis1" name="product_id">
                                <option value="all" {{ $product_id == 'all' ? 'selected' : '' }}>{{\App\CPU\translate('all_Products')}}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product['id'] }}" {{ $product_id == $product['id'] ? 'selected' : '' }}>
                                       {{ \Illuminate\Support\Str::limit($product->name, 40, '...') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <select class="form-control __form-control" name="date_type" id="date_type">
                                <option value="this_year" {{ $date_type == 'this_year'? 'selected' : '' }}>{{\App\CPU\translate('This_Year')}}</option>
                                <option value="this_month" {{ $date_type == 'this_month'? 'selected' : '' }}>{{\App\CPU\translate('This_Month')}}</option>
                                <option value="this_week" {{ $date_type == 'this_week'? 'selected' : '' }}>{{\App\CPU\translate('This_Week')}}</option>
                                <option value="custom_date" {{ $date_type == 'custom_date'? 'selected' : '' }}>{{\App\CPU\translate('Custom_Date')}}</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3" id="from_div">
                            <div class="form-floating">
                                <input type="date" name="from" value="{{$from}}" id="from_date" class="form-control">
                                <label>{{\App\CPU\translate('start_date')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3" id="to_div">
                            <div class="form-floating">
                                <input type="date" value="{{$to}}" name="to" id="to_date" class="form-control">
                                <label>{{\App\CPU\translate('end_date')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 filter-btn">
                            <button type="submit" class="btn btn--primary px-4 px-md-5">
                                {{\App\CPU\translate('filter')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        

     <div class="store-report-content mb-2">
            <div class="left-content">
                <div class="left-content-card">
                    <img src="{{asset('/public/assets/back-end/img/cart.svg')}}" alt="back-end/img">
                    <div class="info">
                        <h4 class="subtitle">{{ $order_count['total_order'] }}</h4>
                        <h6 class="subtext">{{\App\CPU\translate('Total_Orders')}}</h6>
                    </div>
                    <div class="coupon__discount w-100 text-right d-flex justify-content-between">
                        <div class="text-center">
                            <strong class="text-danger">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($net_base_amount))}}</strong>
                            <div class="d-flex">
                                <span>{{\App\CPU\translate('Base Amount')}}</span>
                                <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('total_order_base_amount')}}">
                                    <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                </span>
                            </div>
                        </div>
                        <div class="text-center">
                            <strong class="text-primary">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($net_gst))}}</strong>
                            <div class="d-flex">
                                <span>{{\App\CPU\translate('Total Gst')}}</span>
                                <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('total_orders_gst_amount')}} ">
                                    <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                </span>
                            </div>
                        </div>
                        <div class="text-center">
                            <strong class="text-success">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($net_total_amount))}}</strong>
                            <div class="d-flex">
                                <span>{{\App\CPU\translate('Total Amount')}}</span>
                                <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('total_orders_net_amount')}}">
                                    <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
              
            </div>
           
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex flex-wrap w-100 gap-3 align-items-center">
                    <h4 class="mb-0 mr-auto">
                        {{\App\CPU\translate('Total_Orders')}}
                        <span class="badge badge-soft-dark radius-50 fz-14">{{ $orders->total() }}</span>
                    </h4>
                    <form action="" method="GET" class="mb-0">
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-custom">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input type="hidden" value="{{ $seller_id }}" name="seller_id">
                            <input type="hidden" value="{{ $date_type }}" name="date_type">
                            <input type="hidden" value="{{ $from }}" name="from">
                            <input type="hidden" value="{{ $to }}" name="to">
                            <input id="datatableSearch_" value="{{ $search }}" type="search" name="search" class="form-control" placeholder="{{ \App\CPU\translate('search_by_order_id')}}" aria-label="Search orders" required>
                            <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('search')}}</button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <div>
                        <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                                data-toggle="dropdown">
                            <i class="tio-download-to"></i>
                            {{\App\CPU\translate('export')}}
                            <i class="tio-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.report.order-sale-report-excel', ['date_type'=>request('date_type'), 'seller_id'=>request('seller_id'), 'from'=>request('from'), 'to'=>request('to'), 'search'=>request('search'),'product_id'=>request('product_id')]) }}">
                                    {{\App\CPU\translate('excel')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatable"
                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                       class="table __table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{\App\CPU\translate('SL')}}</th>
                        <th>{{\App\CPU\translate('Vendor Code')}}</th>
                        <th>Business Name</th>
                        <th>{{\App\CPU\translate('Order ID')}}</th>
                        <th>{{\App\CPU\translate('Order Date')}}</th>
                        <th>{{\App\CPU\translate('Return Request Date')}}</th>
                        <th>{{\App\CPU\translate('Invoice No')}}</th>
                        <!-- <th>{{\App\CPU\translate('Invoice Date')}}</th> -->
                        <th>{{\App\CPU\translate('Bill to')}}</th>
                        <th>{{\App\CPU\translate('Ship to')}}</th>
                        <th>{{\App\CPU\translate('State Of Supply')}}</th>
                       <th>{{\App\CPU\translate('State Of Delivery')}}</th>
                        <th>{{\App\CPU\translate('Product Name')}}</th>
                        <th>{{\App\CPU\translate('Variation')}}</th>
                        <th>{{\App\CPU\translate('Product SKU')}}</th>
                        <th>{{\App\CPU\translate('Product Code')}}</th>
                        <th>{{\App\CPU\translate('Qty')}}</th>
                        <th>{{\App\CPU\translate('Unit')}}</th>
                        <th>{{\App\CPU\translate('MRP Per Unit')}}<small>(Incl. GST)</small></th>
                        <th>{{\App\CPU\translate('Disc per unit in Rs')}}</th>
                       <th>{{\App\CPU\translate('Net /Listed Price per unit')}}<small>(Incl. GST)</small></th>
                       <th>{{\App\CPU\translate('GST Included in %')}}</th>
                       <th>{{\App\CPU\translate('Base Amount')}}</th>
                       <th>{{\App\CPU\translate('CGST')}}</th>
                       <th>{{\App\CPU\translate('SGST')}}</th>
                       <th>{{\App\CPU\translate('IGST')}}</th>
                       <th>{{\App\CPU\translate('Bill Amount')}}<small>(Incl. GST)</small></th>
                       <th>{{\App\CPU\translate('RTO Status')}}</th>
                       <th>{{\App\CPU\translate('Seller Approval')}}</th>
                       <th>{{\App\CPU\translate('RTO Charges')}}</th>
                       <th>{{\App\CPU\translate('Shipping Charges')}}</th>
                       <th>{{\App\CPU\translate('Admin Approval')}}</th>
                       <th>{{\App\CPU\translate('Cancellation Fee')}}</th>
                       <th>{{\App\CPU\translate('Refundable Amount')}}</th>
                       <th>{{\App\CPU\translate('Status')}}</th>
                       
                    </tr>
                    </thead>
                     @php
                        $countRow = 1;
                    @endphp
                    <tbody>
                    @foreach($orders as $key=>$order)
                    <?php 
                    
                    ?>
                    @php
                        $total_tax=0;
                        $all_coupons=0;
                        $total_mrp = 0;
                        $iteam_tax = 0;
                        $amount_without_tax = 0;
                        $shipping = 0;
                    @endphp
                    
                    <?php  $orderDetails = App\Model\Order::with('seller')->with('shipping')->with('details')->where('id', $order->id)->first(); ?>
                    @foreach($orderDetails->details as $key=>$details)
                    
                     <?php
                    //$productData = json_decode($details->product_details, true);
                $productData = !empty($details->product_details) ? json_decode($details->product_details, true) : [];
                    //$price_product = $productData['unit_price'];
                    $price_product = isset($productData['unit_price']) ? $productData['unit_price'] : 0;
                    
                    if ($productData['discount_type'] == 'percent') {
                            $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                            } else {
                            $discountPrice = $productData['unit_price'] - $productData['discount'];
                            }
                       
                     ?>
                        
                        {{-- Decode and display price if product variation exists --}}
                        @if(!empty($details['variant']))
                        @php
                            $priceData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];
                        @endphp
                        
                        @if(!empty($priceData))
                            @foreach ($priceData as $keyss => $priceValue)
                            @if($details['variant'] == $priceValue['type'])
                               
                                
                               <?php 
                                $price_product = $priceValue['price'];
                                   if ($productData['discount_type'] == 'percent') {
                                    $discountPrice = $priceValue['price'] - (($productData['discount'] / 100) * $priceValue['price']);
                                    
                                    } else {
                                    $discountPrice = $priceValue['price'] - $productData['discount'];
                                    }
                                   ?>
                              
                               @endif
                            @endforeach
                        @endif
                        @endif
                    
                        @php 
                        $item_tax = ($discountPrice-($discountPrice*100)/($details->product_all_status->tax + 100))*$details->qty;
                        $amount_without_tax = ($discountPrice*100)/($details->product_all_status->tax + 100);
                        @endphp
                    
                        <tr>
                            <td>{{ $countRow }}</td>
                            <td>VN{{ $order->seller->id }}</td>
                            <td>{{ $order->seller->shop->name }}
                            <?php $seller_state = $order->seller->shop->state ?? null; ?>
                            </td>
                            <td>
                                <a class="title-color" href="{{route('admin.orders.details',['id'=>$order->id])}}">{{$order->id}}</a>
                            </td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                            <td></td>
                            <td>IC/VN{{ $order->seller->id }}/{{$order->id}}</td>
                            <!-- <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td> -->
                            <td>{{$order->billingAddress ? $order->billingAddress['contact_person_name'] : ""}}</td>
                            <td>{{$order->shippingAddress ? $order->shippingAddress['contact_person_name'] : ""}}
                           <?php $customer_state = json_decode($order->shipping_address_data)->state ?? 'State not available'; ?>

                            </td>
                            <td></td>
                            <td></td>
                            <td>{{ \Illuminate\Support\Str::limit($productData['name'], 40, '...') }}</td>
                            <td>
                                @if(!empty($details) && !empty($details['variant']))
                                   {{ $details['variant'] }}
                                @endif
                            </td>
                            
                            <td>
                                 @php
                                    $skuData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];
                                @endphp
                                
                                @if(!empty($skuData))
                                    @foreach ($skuData as $keyss => $skuItem)
                                    @if($details['variant'] == $skuItem['type'])
                                             {{ $skuItem['sku'] ?? 'N/A' }}
                                    @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>PR{{$details['product_id']}}</td>
                            <td>{{$details->qty}}</td>
                            <td>No.</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($price_product))}}</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($price_product-$discountPrice))}}</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($discountPrice))}}</td>
                             <td>
                             {{ isset($details->product_all_status->tax)? $details->product_all_status->tax : ''}}</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($details->qty*($amount_without_tax))))}}</td>
                            @if($customer_state == $seller_state)
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item_tax/2))}}</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item_tax/2))}}</td>
                            <td>-</td>
                            @else
                            <td>-</td>
                            <td>-</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item_tax))}}</td>
                            @endif
                           
                            <td class="text-center">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($details->qty*($discountPrice))))}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency((($details->qty * $discountPrice) * 2.1) / 100)) }}
                            </td>
                            <td>{{ \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($details->qty * $discountPrice)) - (($details->qty * $discountPrice) * 2.1) / 100) }}
                            </td>
                            <td class="text-center">{{$order->order_status}}</td>
                        </tr>
                        
                @php
                    $countRow = $countRow + 1;
                    $tax_amount_item = $details->product_all_status->tax/2;
                    $all_coupons += $order->discount_amount;
                    $total_mrp+= $details->qty*($discountPrice) 
                   
                @endphp
            @endforeach
            <?php 
             $totalAll = 0;
            ?>
            
             @php($shipping=$order['shipping_cost'])
              @php($totalAll = $total_mrp+$shipping-$all_coupons)   
                <?php  
                   $toatal_ship_inst= 0;
                   $instant_delivery_amount = 0; 
                   ?>
                <?php  $shipping = $shipping + $instant_delivery_amount; 
                //$shipping_gst = $shipping*$tax_amount_item*2*0.01;
                $shipping_gst = $shipping*18*0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
               
                ?>
                      <!-- <tr>
                            <td>{{ $countRow }}</td>
                            <td>VN{{ $order->seller->id }}</td>
                            <td>{{ $order->seller->shop->name }}
                           <?php $seller_state = $order->seller->shop->state ?? null; ?>
                            </td>
                            <td>
                                <a class="title-color" href="{{route('admin.orders.details',['id'=>$order->id])}}">{{$order->id}}</a>
                            </td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                            <td>IC/VN{{ $order->seller->id }}/{{$order->id}}</td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                            <td>{{$order->billingAddress ? $order->billingAddress['contact_person_name'] : ""}}</td>
                            <td>{{$order->shippingAddress ? $order->shippingAddress['contact_person_name'] : ""}}
                            <?php $customer_state = json_decode($order->shipping_address_data)->state ?? 'State not available'; ?>
                            </td>
                            @if($order->instant_delivery_type == 1)
                            <td><b>Shipping + Instant Shipping</b></td>
                            @else
                            <td><b>shipping charges</b></td>
                            @endif
                            <td>
                               1
                            </td>
                            <td>
                                -
                            </td>
                            <td>-</td>
                            <td>1</td>
                            <td>No</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td> -->
                            <!--<td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_gst))}}</td>-->
                            <!-- <td>-</td>
                             <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td>
                             <td>18</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_amount_rate))}}</td>  
                            @if($customer_state == $seller_state)
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_gst/2))}}</td>
                            <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_gst/2))}}</td>
                            <td>-</td>
                            @else
                             <td>-</td>
                             <td>-</td>
                             <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_gst))}}</td>
                            @endif
                             <td class="text-center">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td>
                           
                        </tr> -->
                        
                        <!-- <tr>
                            <td>{{ $countRow = $countRow + 1 }}</td>
                            <td>VN{{ $order->seller->id }}</td>
                            <td>{{ $order->seller->shop->name }}
                            <?php $seller_state = $order->seller->shop->state; ?>
                            </td>
                            <td>
                                <a class="title-color" href="{{route('admin.orders.details',['id'=>$order->id])}}">{{$order->id}}</a>
                            </td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                            <td>IC/VN{{ $order->seller->id }}/{{$order->id}}</td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                            <td>{{$order->billingAddress ? $order->billingAddress['contact_person_name'] : ""}}</td>
                            <td>{{$order->shippingAddress ? $order->shippingAddress['contact_person_name'] : ""}}
                           <?php $customer_state = json_decode($order->shipping_address_data)->state ?? 'State not available'; ?>

                            </td>
                           
                            <td><b>Coupon</b></td>
                            <td>
                               1
                            </td>
                            <td>
                                -
                            </td>
                            <td>-</td>
                            <td>1</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                             <td>-</td>
                             <td>-</td>
                            <td>-</td>  
                             <td>-</td>
                             <td>-</td>
                             <td>-</td>
                            
                             <td class="text-center">-{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($all_coupons))}}</td>
                           
                        </tr> -->
                        
                       <!-- <tr>
                            <td>{{ $countRow = $countRow + 1 }}</td>
                            <td></td>
                            <td>
                            </td>
                            <td>
                                
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                            </td>
                           
                            <td></td>
                            <td>
                               
                            </td>
                            <td>
                                
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                             <td></td>
                             <td></td>
                            <td></td>  
                             <td></td>
                             <td></td>
                             <td></td>
                            
                             <td class="text-center"><b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($totalAll))}}</b></td>
                           
                        </tr>-->
              <?php $countRow = $countRow + 1; ?>
               @endforeach
                    @if($orders->total()==0)
                        <tr>
                            <td colspan="9">
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                         alt="Image Description">
                                    <p class="mb-0">{{ \App\CPU\translate('No_data_to_found')}}</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <div class="px-4 d-flex justify-content-center justify-content-md-end">
                <!-- Pagination -->
                {!! $orders->links() !!}
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <!-- Chart JS -->
    <script src="{{ asset('assets/back-end') }}/js/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('assets/back-end') }}/js/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{ asset('assets/back-end') }}/js/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <!-- Chart JS -->
    <!-- Apex Charts -->
    <script src="{{ asset('/public/assets/back-end/js/apexcharts.js') }}"></script>
    <!-- Apex Charts -->

    <!-- Dognut Pie Chart -->
    <script>
        var options = {
            series: [
                {{ \App\CPU\BackEndHelper::usd_to_currency($payment_data['cash_payment']) }},
                {{ \App\CPU\BackEndHelper::usd_to_currency($payment_data['digital_payment']) }},
                {{ \App\CPU\BackEndHelper::usd_to_currency($payment_data['wallet_payment']) }},
                {{ \App\CPU\BackEndHelper::usd_to_currency($payment_data['offline_payment']) }}
            ],
            chart: {
                width: 320,
                type: 'donut',
            },
            labels: [
                '{{\App\CPU\translate('Cash_Payments')}} ({{ \App\CPU\BackEndHelper::currency_symbol() }}{{ \App\CPU\BackEndHelper::format_currency(\App\CPU\BackEndHelper::usd_to_currency($payment_data['cash_payment'])) }})',
                '{{\App\CPU\translate('Digital_Payments')}} ({{ \App\CPU\BackEndHelper::currency_symbol() }}{{ \App\CPU\BackEndHelper::format_currency(\App\CPU\BackEndHelper::usd_to_currency($payment_data['digital_payment'])) }})',
                '{{\App\CPU\translate('Wallet_Payments')}} ({{ \App\CPU\BackEndHelper::currency_symbol() }}{{ \App\CPU\BackEndHelper::format_currency(\App\CPU\BackEndHelper::usd_to_currency($payment_data['wallet_payment'])) }})',
                '{{\App\CPU\translate('Offline_Payments')}} ({{ \App\CPU\BackEndHelper::currency_symbol() }}{{ \App\CPU\BackEndHelper::format_currency(\App\CPU\BackEndHelper::usd_to_currency($payment_data['offline_payment'])) }})',
            ],
            dataLabels: {
                enabled: false,
                style: {
                    colors: ['#004188', '#004188', '#004188', '#7b94a4']
                }
            },
            responsive: [{
                breakpoint: 1650,
                options: {
                    chart: {
                        width: 260
                    },
                }
            }],
            colors: ['#004188', '#0177CD', '#0177CD', '#7b94a4'],
            fill: {
                colors: ['#004188', '#A2CEEE', '#0177CD', '#7b94a4']
            },
            legend: {
                show: false
            },
        };

        var chart = new ApexCharts(document.querySelector("#dognut-pie"), options);
        chart.render();
    </script>
    <!-- Dognut Pie Chart -->

    <script>

        // Bar Charts
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function() {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        // all:true,
                        @if (isset($zone))
                        zone_ids: [{{ $zone->id }}],
                        @endif
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if(fr != ''){
                $('#to_date').attr('required','required');
            }
            if(to != ''){
                $('#from_date').attr('required','required');
            }
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })

        $("#date_type").change(function() {
            let val = $(this).val();
            $('#from_div').toggle(val === 'custom_date');
            $('#to_div').toggle(val === 'custom_date');

            if(val === 'custom_date'){
                $('#from_date').attr('required','required');
                $('#to_date').attr('required','required');
                $('.filter-btn').attr('class','filter-btn col-12 text-right');
            }else{
                $('#from_date').val(null).removeAttr('required')
                $('#to_date').val(null).removeAttr('required')
                $('.filter-btn').attr('class','col-sm-6 col-md-3 filter-btn');
            }
        }).change();

    </script>

@endpush
