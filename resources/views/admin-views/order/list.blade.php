@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div>
            <!-- Page Title -->
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0">
                    <img src="{{asset('/public/assets/back-end/img/all-orders.png')}}" class="mb-1 mr-1" alt="">
                    <span class="page-header-title">
                        @if($status =='processing')
                            {{\App\CPU\translate('packaging')}}
                        @elseif($status =='failed')
                            {{\App\CPU\translate('Failed_to_Deliver')}}
                        @elseif($status == 'all')
                            {{\App\CPU\translate('all')}}
                        @else
                            {{\App\CPU\translate(str_replace('_',' ',$status))}}
                        @endif
                    </span>
                    {{\App\CPU\translate('Orders')}}
                </h2>
                <span class="badge badge-soft-dark radius-50 fz-14">{{$orders->total()}}</span>
            </div>
            <!-- End Page Title -->

            <!-- Order States -->
            <div class="card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" id="form-data" method="GET">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 pb-0">
                                    <h4>{{\App\CPU\translate('select')}} {{\App\CPU\translate('date')}} {{\App\CPU\translate('range')}}</h4>
                                </div>
                                 <input type="hidden" name="instant" value="{{$instant}}">
                                @if(request('delivery_man_id'))
                                    <input type="hidden" name="delivery_man_id" value="{{ request('delivery_man_id') }}">
                                @endif

                                <div class="col-sm-6 col-md-3">
                                    <select name="filter" class="form-control">
                                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>{{\App\CPU\translate('all')}}</option>
                                        <option value="admin" {{ $filter == 'admin' ? 'selected' : '' }}>{{\App\CPU\translate('In_House')}}</option>
                                        <option value="seller" {{ $filter == 'seller' ? 'selected' : '' }}>{{\App\CPU\translate('Seller')}}</option>
                                        <option value="instant_delivery" {{ $filter == 'instant_delivery' ? 'selected' : '' }}>{{\App\CPU\translate('instant_delivery')}}</option>
                                        <!-- @if(($status == 'all' || $status == 'delivered') && !request()->has('delivery_man_id'))
                                        <option value="POS" {{ $filter == 'POS' ? 'selected' : '' }}>POS</option>
                                        @endif -->
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="date" name="from" value="{{$from}}" id="from_date"
                                            class="form-control">
                                        <label>{{\App\CPU\translate('Start_Date')}}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mt-2 mt-sm-0">
                                    <div class="form-floating">
                                        <input type="date" value="{{$to}}" name="to" id="to_date"
                                            class="form-control">
                                        <label>{{\App\CPU\translate('End_Date')}}</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mt-2 mt-sm-0  ">
                                    <button type="submit" class="btn btn--primary btn-block" onclick="formUrlChange(this)" data-action="{{ url()->current() }}">
                                        {{\App\CPU\translate('show')}} {{\App\CPU\translate('data')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Order stats -->
                    @if($status == 'all' && $filter != 'POS')
                    <div class="row g-2 mb-20" style="display:none;">
                        <div class="col-sm-6 col-lg-3">
                            <!-- Card -->
                            <a class="order-stats order-stats_pending" href="{{route('admin.orders.list',['pending'])}}">
                                <div class="order-stats__content">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/pending.png')}}" class="svg" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('pending')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $pending_count }}
                                </span>
                            </a>
                            <!-- End Card -->
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <!-- Card -->
                            <a class="order-stats order-stats_confirmed" href="{{route('admin.orders.list',['confirmed'])}}">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/confirmed.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('confirmed')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $confirmed_count }}
                                </span>
                            </a>
                            <!-- End Card -->
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <!-- Card -->
                            <a class="order-stats order-stats_packaging" href="{{route('admin.orders.list',['processing'])}}">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/packaging.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('Packaging')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $processing_count }}
                                </span>
                            </a>
                            <!-- End Card -->
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <!-- Card -->
                            <a class="order-stats order-stats_out-for-delivery" href="{{route('admin.orders.list',['out_for_delivery'])}}">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/out-of-delivery.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('out_for_delivery')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $out_for_delivery_count }}
                                </span>
                            </a>
                            <!-- End Card -->
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="order-stats order-stats_delivered cursor-pointer"
                                onclick="location.href='{{route('admin.orders.list',['delivered'])}}'">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/delivered.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('delivered')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $delivered_count }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="order-stats order-stats_canceled cursor-pointer"
                                onclick="location.href='{{route('admin.orders.list',['canceled'])}}'">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/canceled.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('canceled')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $canceled_count }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="order-stats order-stats_returned cursor-pointer"
                                onclick="location.href='{{route('admin.orders.list',['returned'])}}'">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/returned.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('returned')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $returned_count }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="order-stats order-stats_failed cursor-pointer"
                                onclick="location.href='{{route('admin.orders.list',['failed'])}}'">
                                <div class="order-stats__content" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                    <img width="20" src="{{asset('/public/assets/back-end/img/failed-to-deliver.png')}}" alt="">
                                    <h6 class="order-stats__subtitle">{{\App\CPU\translate('Failed_To_Delivery')}}</h6>
                                </div>
                                <span class="order-stats__title">
                                    {{ $failed_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- End Order stats -->

                    

                    <!-- Data Table Top -->
                    <div class="px-3 py-4 light-bg">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-5">
                                <form action="" method="GET">
                                    <!-- Search -->
                                     <input type="hidden" name="instant" value="{{$instant}}">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('Search by Order ID or Order Status')}}" aria-label="Search by Order ID" value="{{ $search }}"
                                            required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            
                            
                     <div class="col-sm-8 col-md-6 col-lg-4">
                        <!-- filter -->
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-merge input-group-custom">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                 <input type="hidden" name="instant" value="{{$instant}}">
                                <select class="form-control" name="provider_type">
                                    <option value="" selected disabled> {{\App\CPU\translate('provider_type')}} </option>
                                    <option value=""> {{\App\CPU\translate('all')}} </option>
                                    <option value="2" {{ ($provider_type == 2) ? 'selected' : '' }}> {{\App\CPU\translate('Worker')}} </option>
                                    <option value="3" {{ ($provider_type == 3) ? 'selected' : '' }}> {{\App\CPU\translate('Contractor')}} </option>
                                <option value="4" {{ ($provider_type == 4) ? 'selected' : '' }}> {{\App\CPU\translate('Architect')}} </option>
                                  <option value="5" {{ ($provider_type == 5) ? 'selected' : '' }}> {{\App\CPU\translate('Interior Designer')}} </option>
                                </select>
                                <button type="submit" class="btn btn--primary">{{\App\CPU\translate('filter')}}</button>
                            </div>
                        </form>
                        <!-- End filter -->
                    </div>
                            
                            <div class="col-sm-4 col-md-6 col-lg-3 justify-content-sm-end" style="text-align: end;">
                                <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                    <i class="tio-download-to"></i>
                                    {{\App\CPU\translate('export')}}
                                    <i class="tio-chevron-down"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.orders.order-bulk-export', ['delivery_man_id' => request('delivery_man_id'), 'status' => $status, 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search]) }}">
                                            <img width="14" src="{{asset('/public/assets/back-end/img/excel.png')}}" alt="">
                                            {{\App\CPU\translate('Excel')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Data Table Top -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100"
                            style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th class="">{{\App\CPU\translate('SL')}}</th>
                                    <th>{{\App\CPU\translate('Order')}} {{\App\CPU\translate('ID')}}</th>
                                    <th>{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Date')}}</th>
                                    <th>{{\App\CPU\translate('customer')}} {{\App\CPU\translate('info')}}</th>
                                    <th>{{\App\CPU\translate('Store')}}</th>
                                    <th class="text-right">{{\App\CPU\translate('Total')}} {{\App\CPU\translate('Amount')}}</th>
                                    <th class="text-center">{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Status')}} </th>
                                    <th class="text-center">{{\App\CPU\translate('Shipping')}} {{\App\CPU\translate('Status')}} </th>
                                    <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)

                                <tr class="status-{{$order['order_status']}} class-all">
                                    <td class="">
                                        {{$orders->firstItem()+$key}}
                                    </td>
                                    

                                    <td >
                                        <a class="title-color" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                        
                                        @if($order['instant_delivery_type'] == 1)
                                         <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('instant_delivery')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span>
                                         @endif
                                        
                                         @php
                                        $order_detail = \App\Model\OrderDetail::where('order_id', $order['id'])->first();
                                        $product_detail = null;

                                        if ($order_detail && isset($order_detail['product_id'])) {
                                            $product_detail = \App\Model\Product::where('id', $order_detail['product_id'])->first();
                                        }
                                    @endphp

                                    @if($product_detail && $product_detail->free_delivery == 1)
                                        <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{ \App\CPU\translate('free_delivery') }}">
                                            <img class="info-img" src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}" alt="img">
                                        </span>
                                    @endif

                                    </td>
                                    <td>
                                        <div>{{date('d M Y',strtotime($order['created_at']))}},</div>
                                        <div>{{ date("h:i A",strtotime($order['created_at'])) }}</div>
                                    </td>
                                    
                                    <td>
                                        @if($order->customer_id == 0)
                                            <strong class="title-name">{{\App\CPU\translate('walking_customer')}}</strong>
                                        @else
                                            @if($order->customer)
                                                <a class="text-body text-capitalize" href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                    <strong class="title-name">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong>
                                                </a>
                                                <a class="d-block title-color" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                            @else
                                                <label class="badge badge-danger fz-12">{{\App\CPU\translate('invalid_customer_data')}}</label>
                                            @endif
                                        @endif
                                    </td>
                                    

                                    <td>
                                        <span class="store-name font-weight-medium">
                                            @if($order->seller_is == 'seller')
                                                {{ isset($order->seller->shop) ? $order->seller->shop->name : 'Store not found' }}
                                            @elseif($order->seller_is == 'admin')
                                                {{\App\CPU\translate('In-House')}}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div>
                                            @php($total=0)
                                             @php($shipping=$order['shipping_cost'])
                                             @php($coupon_discount=$order['discount_amount'])
                                            @php($discount = 0)
                                            @if($order->coupon_discount_bearer == 'inhouse' && !in_array($order['coupon_code'], [0, NULL]))
                                                @php($discount = $order->discount_amount)
                                            @endif
                                            
                                            @foreach($order->details as $key=>$detail)
                                            @php($subtotal=$detail['price']*$detail->qty+$detail['tax']-$detail['discount'])
                                            @php($total+=$subtotal)
                                            @endforeach
                                            
                                            @php($toatal_ship_inst= $shipping)       
                                             @if($order->instant_delivery_type == 1)
                                            <?php 
                                            //$ship = App\Model\CartShipping::where('shipping_method_id',$order->instant_delivery_type)
                                                      if($shipping <= 150){
                                                          $toatal_ship_inst =   $shipping/3;
                                                        }elseif($shipping > 150 && $shipping <= 500){
                                                             $toatal_ship_inst =   $shipping/2;
                                                        }elseif($shipping > 500){
                                                             $toatal_ship_inst =   $shipping/1.56;
                                                        }else{
                                                            $toatal_ship_inst = $shipping;
                                                        }
                                                        $toatal_ship_inst_charge =  $shipping - $toatal_ship_inst;
                                                        
                                            ?>
                                            
                                           @endif
                                            
                                            {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total+$shipping-$coupon_discount))}}
                                        </div>

                                        @if($order->payment_status=='paid')
                                            <span class="badge text-success fz-12 px-0">
                                                {{\App\CPU\translate('paid')}}
                                            </span>
                                        @else
                                            <span class="badge text-danger fz-12 px-0">
                                                {{\App\CPU\translate('unpaid')}}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-capitalize">
                                        @if($order['order_status']=='pending')
                                            <span class="badge badge-soft-info fz-12">
                                                {{\App\CPU\translate($order['order_status'])}}
                                            </span>

                                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                            <span class="badge badge-soft-warning fz-12">
                                                {{str_replace('_',' ',$order['order_status'] == 'processing' ? \App\CPU\translate('packaging'):\App\CPU\translate($order['order_status']))}}
                                            </span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge badge-soft-success fz-12">
                                                {{\App\CPU\translate($order['order_status'])}}
                                            </span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-danger fz-12">
                                                {{\App\CPU\translate('failed_to_deliver')}}
                                            </span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge badge-soft-success fz-12">
                                                {{\App\CPU\translate($order['order_status'])}}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger fz-12">
                                                {{\App\CPU\translate($order['order_status'])}}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-capitalize">
                                        <div id="shipping-td-{{$order['id']}}">
                                            @if(isset($order['shiprocket_courier']['shiprocket_order_id']))
                                            <span class="badge badge-soft-{{$order['shiprocket_courier']['status']=='CANCELED' ? 'danger' : 'dark'}}" id="shipping-status-{{$order['id']}}">{{$order['shiprocket_courier']['status']}}</span>
                                            <button type="button" title="Get Current Status" onclick="getCurrentShippingStatus({{$order['id']}})" class="btn btn-xs btn-primary"><i class="tio-refresh"></i></button>
                                            @if($order['shiprocket_courier']['status']!='CANCELED')
                                            <button type="button" title="Cancel Shiprocket Order" onclick="cancelShiprocketOrder({{$order['id']}})" class="btn btn-xs btn-danger"><i class="tio-clear"></i></button>
                                            @endif
                                            @else
                                            <button class="btn btn-xs btn-primary" onclick="addToShipping({{$order['id']}})" type="button">Create shipping</button>
                                            @endif
                                        </div>
                                        <div id="shipping-td-load-{{$order['id']}}"></div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline--primary square-btn btn-sm mr-1" title="{{\App\CPU\translate('view')}}"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                <img src="{{asset('/public/assets/back-end/img/eye.svg')}}" class="svg" alt="">
                                            </a>
                                            <a class="btn btn-outline-success square-btn btn-sm mr-1" target="_blank" title="{{\App\CPU\translate('invoice')}}"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-file"></i>
                                            </a>
                                            <button class="btn btn-xs  <?php echo ($order->pdf_label != NULL) ? 'btn-success' : 'btn-primary'; ?>"
                                                data-toggle="modal"
                                               data-target="#addModal" data-id="{{$order['id']}}">
                                                 <span class="text">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('Labels')}}  </span>
                                               </button>
                                               
                                 <a class="btn btn-outline-danger btn-sm square-btn" href="javascript:"
                                            title="{{\App\CPU\translate('Delete')}}"
                                            onclick="form_alert('product-{{$order['id']}}','Want to delete this item ?')">
                                           <i class="tio-delete"></i>
                                    </a>
                                        </div>
                                        
                     <form action="{{route('admin.orders.delete',[$order['id']])}}"
                                            method="post" id="product-{{$order['id']}}">
                                        @csrf @method('delete')
                     </form>                    
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Pagination -->
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {!! $orders->links() !!}
                        </div>
                    </div>
                    <!-- End Pagination -->
                </div>
            </div>
            <!-- End Order States -->

            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal d-none">
                <span class="hs-nav-scroller-arrow-prev d-none">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-left"></i>
                    </a>
                </span>

                <span class="hs-nav-scroller-arrow-next d-none">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-right"></i>
                    </a>
                </span>

                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">{{\App\CPU\translate('order_list')}}</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->
    </div>
    
           {{-- add modal --}}
        <div class="modal fade" tabindex="-1" role="dialog" id="addModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{\App\CPU\translate('Upload Labels')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                                aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.orders.upload-label') }}" method="post" id="addForm" enctype="multipart/form-data">
                        @csrf
                        <input type ="hidden" id="modalIdPlaceholder" name="order_id" value ="">
                        <div class="modal-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">

                            <div class="form-group">
                                <label>{{\App\CPU\translate('PDF Formate Only')}}</label>
                                <input type="file" class="form-control" name="pdf_file" accept=".pdf"
                                       placeholder="{{\App\CPU\translate('Type Question')}}">
                            </div>
                            
                             <div class="form-group">
                                <label>{{\App\CPU\translate('Pickup  Date & Time')}}</label>
                                <input type="datetime-local" class="form-control" name="pickup_date" 
                                       placeholder="{{\App\CPU\translate('Type Question')}}">
                            </div>



                        </div>
                        <div class="modal-footer bg-whitesmoke br">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{\App\CPU\translate('Close')}}</button>
                            <button class="btn btn--primary">{{\App\CPU\translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- edit modal --}}
    
@endsection

@push('script_2')

<script>
    $('#addModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var id = button.data('id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('#modalIdPlaceholder').val(id); // Update the ID placeholder in the modal
    });
</script>


    <script>
        function filter_order() {
            $.get({
                url: '{{route('admin.orders.inhouse-order-filter')}}',
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success('{{\App\CPU\translate('order_filter_success')}}');
                    location.reload();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        };
    </script>
    <script>
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
                    toastr.error('{{\App\CPU\translate('Invalid date range')}}!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
        
        function getCurrentShippingStatus(id)
        {
            const container = document.querySelector("#shipping-td-"+id);
            const loader = document.querySelector("#shipping-td-load-"+id);
            
            if(id) {
                $.post({
                    url: '{{route('admin.orders.shipping-status')}}',
                    data: JSON.stringify({order_id: id}),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    processData: false,
                    beforeSend: function () {
                        container.style.display = 'none';
                        loader.style.display = 'block';
                        loader.innerHTML = 'loading...';
                    },
                    success: function (data) {
                        if(data.status) {
                            document.querySelector("#shipping-status-"+id).innerHTML = data.data?.status ?? '';
                              location.reload();
                        }else {
                            toastr.error('Something went wrong! Please try again later.');
                        }
                    },
                    complete: function () {
                        container.style.display = 'block';
                        loader.style.display = 'none';
                    },
                });
            }
        }
        
        function addToShipping(id)
        {
            const container = document.querySelector("#shipping-td-"+id);
            const loader = document.querySelector("#shipping-td-load-"+id);
            
            if(id) {
                $.post({
                    url: '{{route('admin.orders.add-to-shiprocket')}}',
                    data: JSON.stringify({order_id: id}),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    processData: false,
                    beforeSend: function () {
                        container.style.display = 'none';
                        loader.style.display = 'block';
                        loader.innerHTML = 'adding...';
                    },
                    success: function (data) {
                        if(data.status) {
                            toastr.success(data.message);
                            location.reload()
                            container.innerHTML = 'Added';
                        }else {
                            toastr.error(data.message ?? 'Something went wrong! Please try again later.');
                        }
                    },
                    complete: function () {
                        container.style.display = 'block';
                        loader.style.display = 'none';
                    },
                });
            }
        }
        
        function cancelShiprocketOrder(id)
        {
            const container = document.querySelector("#shipping-td-"+id);
            const loader = document.querySelector("#shipping-td-load-"+id);
            
            if(id) {
                $.post({
                    url: '{{route('admin.orders.cancel-shiprocket-order')}}',
                    data: JSON.stringify({order_id: id}),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    processData: false,
                    beforeSend: function () {
                        container.style.display = 'none';
                        loader.style.display = 'block';
                        loader.innerHTML = 'canceling...';
                    },
                    success: function (data) {
                        if(data.status) {
                            toastr.success(data.message);
                            location.reload()
                            container.innerHTML = 'Canceled';
                        }else {
                            toastr.error(data.message ?? 'Something went wrong! Please try again later.');
                        }
                    },
                    complete: function () {
                        container.style.display = 'block';
                        loader.style.display = 'none';
                    },
                });
            }
        }
    </script>
@endpush
