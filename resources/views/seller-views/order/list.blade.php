@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Order List'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="content container-fluid">
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

        <div class="card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url()->current() }}" id="form-data" method="GET">
                        <div class="row gy-3 gx-2">
                            <div class="col-12 pb-0">
                                <h4>{{\App\CPU\translate('select_date_range')}}</h4>
                            </div>
                             <input type="hidden" name="instant" value="{{$instant}}">
                            @if(request('delivery_man_id'))
                                <input type="hidden" name="delivery_man_id" value="{{ request('delivery_man_id') }}">
                            @endif

                            <div class="col-sm-6 col-md-3">
                                <select name="filter" class="form-control">
                                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>{{\App\CPU\translate('All')}}</option>
                                    @if ($seller_pos == 1 && $seller->pos_status == 1 && ($status == 'all' || $status == 'delivered') && !request()->has('delivery_man_id'))
                                    <option value="POS" {{ $filter == 'POS' ? 'selected' : '' }}>{{\App\CPU\translate('POS')}}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-floating">
                                    <input type="date" name="from" value="{{$from}}" id="from_date"
                                        class="form-control">
                                    <label>{{\App\CPU\translate('Start Date')}}</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-floating">
                                    <input type="date" value="{{$to}}" name="to" id="to_date"
                                        class="form-control">
                                    <label>{{\App\CPU\translate('End Date')}}</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <button type="submit" class="btn btn--primary btn-block">
                                    {{\App\CPU\translate('show')}} {{\App\CPU\translate('data')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if($status == 'all' && $filter != 'POS')
                <div class="row g-2 mb-20" style="display:none;">
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_pending" href="{{route('seller.orders.list', ['pending', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/pending.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('pending')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $pending }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_confirmed" href="{{route('seller.orders.list', ['confirmed', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/confirmed.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('confirmed')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $confirmed }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_packaging" href="{{route('seller.orders.list', ['processing', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/packaging.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('Packaging')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $processing }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_out-for-delivery" href="{{route('seller.orders.list', ['out_for_delivery', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/out-of-delivery.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('out_for_delivery')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $out_for_delivery }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_delivered" href="{{route('seller.orders.list', ['delivered', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/delivered.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('delivered')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $delivered }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_canceled" href="{{route('seller.orders.list', ['canceled', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/canceled.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('canceled')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $canceled }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_returned" href="{{route('seller.orders.list', ['returned', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/returned.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('returned')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $returned }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <!-- Order Stats -->
                        <a class="order-stats order-stats_failed" href="{{route('seller.orders.list', ['failed', 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search])}}">
                            <div class="order-stats__content">
                                <img width="20" src="{{asset('/public/assets/back-end/img/failed-to-deliver.png')}}" alt="">
                                <h6 class="order-stats__subtitle">{{\App\CPU\translate('failed_to_deliver')}}</h6>
                            </div>
                            <span class="order-stats__title">
                                {{ $failed }}
                            </span>
                        </a>
                        <!-- End Order Stats -->
                    </div>
                </div>
                @endif

                <!-- Data Table Top -->
                <div class="px-3 py-4 light-bg">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <!-- Search -->
                                 <input type="hidden" name="instant" value="{{$instant}}">
                                <div class="input-group input-group-merge input-group-custom">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                        placeholder="{{\App\CPU\translate('search_orders')}}" aria-label="Search orders" value="{{ $search }}" required>
                                    <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-8 justify-content-sm-end">
                            <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                <i class="tio-download-to"></i>
                                {{\App\CPU\translate('export')}}
                                <i class="tio-chevron-down"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item" href="{{ route('seller.orders.order-bulk-export', ['delivery_man_id' => request('delivery_man_id'), 'status' => $status, 'from' => $from, 'to' => $to, 'filter' => $filter, 'search' => $search]) }}">
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

                <div class="table-responsive">
                    <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th class="text-capitalize">{{\App\CPU\translate('SL')}}</th>
                                <th class="text-capitalize">{{\App\CPU\translate('Order_ID')}}</th>
                                <th class="text-capitalize">{{\App\CPU\translate('Order_Date')}}</th>
                                <th class="text-capitalize">{{\App\CPU\translate('customer_info')}}</th>
                                <th class="text-capitalize">{{\App\CPU\translate('Total_amount')}}</th>
                                <th class="text-capitalize">{{\App\CPU\translate('Order_Status')}} </th>
                                <th class="text-center">{{\App\CPU\translate('Shipping')}} {{\App\CPU\translate('Status')}} </th>
                                <th class="text-capitalize">{{\App\CPU\translate('pickup_date')}} </th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $k=>$order)
                            <tr>
                                <td>
                                    {{$orders->firstItem()+$k}}
                                </td>
                                <td>
                                    <a  class="title-color hover-c1" href="{{route('seller.orders.details',$order['id'])}}">{{$order['id']}}</a>
                                     @if($order['instant_delivery_type'] == 1)
                                         <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('instant_delivery')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span>
                                         @endif
                                         
                                         @php($order_detail=\App\Model\OrderDetail::where('order_id',$order['id'])->first())
                                          @php($product_detail=\App\Model\Product::where('id',$order_detail['product_id'])->first())
                                         
                                          @if(isset($product_detail))
                                          @if($product_detail['free_delivery'] == 1)
                                         <span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('free_delivery')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span>
                                         @endif
                                         @endif
                                         
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                    <div>{{date('H:i A',strtotime($order['created_at']))}}</div>
                                </td>
                                <td>
                                    @if($order->customer_id == 0)
                                        <strong class="title-name">Walking customer</strong>
                                    @else
                                        <div>{{$order->customer ? $order->customer['f_name'].' '.$order->customer['l_name'] : 'Customer Data not found'}}</div>
                                        <a class="d-block title-color" href="tel:{{ $order->customer ? $order->customer->phone : '' }}">{{ $order->customer ? $order->customer->phone : '' }}</a>
                                    @endif
                                </td>
                                <td>
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
                                        <span class="badge badge-soft-success">{{\App\CPU\translate('paid')}}</span>
                                    @else
                                        <span class="badge badge-soft-danger">{{\App\CPU\translate('unpaid')}}</span>
                                    @endif
                                    </td>
                                    <td class="text-capitalize ">
                                        @if($order->order_status=='pending')
                                            <label
                                                class="badge badge-soft-primary">{{$order['order_status']}}</label>
                                        @elseif($order->order_status=='processing' || $order->order_status=='out_for_delivery')
                                            <label
                                                class="badge badge-soft-warning">{{str_replace('_',' ',$order['order_status'] == 'processing' ? 'packaging' : $order['order_status'])}}</label>
                                        @elseif($order->order_status=='delivered' || $order->order_status=='confirmed')
                                            <label
                                                class="badge badge-soft-success">{{$order['order_status']}}</label>
                                        @elseif($order->order_status=='returned')
                                            <label
                                                class="badge badge-soft-danger">{{$order['order_status']}}</label>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-danger fz-12">
                                                {{$order['order_status'] == 'failed' ? 'Failed To Deliver' : ''}}
                                            </span>
                                        @else
                                            <label
                                                class="badge badge-soft-danger">{{$order['order_status']}}</label>
                                        @endif
                                    </td>
                                    
                                       <td class="text-center text-capitalize">
                                        <div id="shipping-td-{{$order['id']}}">
                                            @if(isset($order['shiprocket_courier']['shiprocket_order_id']))
                                            <span class="badge badge-soft-{{$order['shiprocket_courier']['status']=='CANCELED' ? 'danger' : 'dark'}}" id="shipping-status-{{$order['id']}}">{{$order['shiprocket_courier']['status']}}</span>
                                            @else
                                            <span class="badge badge-soft-dark" >
                                            {{ 'Shipping not created' }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                    </td>
                                    
                                    <td>
                                        @if($order['pickup_date'] != NULL)
                                        <?php 
                                        $dateTime = new DateTime($order['pickup_date']);
                                        $formattedDateTime = $dateTime->format('Y-m-d h:i:s A');
                                        ?>
                                        <div>{{$formattedDateTime}}</div>
                                        @else
                                        
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a  class="btn btn-outline--primary btn-sm square-btn"
                                                title="{{\App\CPU\translate('view')}}"
                                                href="{{route('seller.orders.details',[$order['id']])}}">
                                                <i class="tio-invisible"></i>

                                            </a>
                                            <a  class="btn btn-outline-info btn-sm square-btn" target="_blank"
                                                title="{{\App\CPU\translate('invoice')}}"
                                                href="{{route('seller.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-file"></i>
                                            </a>
                                            <?php
                                      $label = DB::table('shiprocket_couriers')->where('order_id',$order['id'])->first();
                                     // dd( $label)

                                             ?>
                                           
                                            <a class="btn btn-outline-{{ ($order->pdf_label != NULL) ? 'success' : 'primary' }} btn-sm square-btn"
   href="javascript:void(0);"
   title="{{ \App\CPU\translate('Download Label') }}"
   onclick="downloadLabel('{{ $label->awb_code }}')">
   <i class="tio-download"></i>
</a>

<script>
function downloadLabel(awb) {
    // Hard-coded path chal jayega:
    // let url = `/label/${awb}`;

    // ✅ Recommended: route helper se URL banao (domain/subfolder safe)
    let url = "{{ route('label', ['awb' => ':awb']) }}".replace(':awb', awb);
    window.location.href = url;  // server se force-download response aayega
}
</script>


                                        </div>
                                    </td>
                                </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {{$orders->links()}}
                    </div>
                </div>
                <!-- End Pagination -->

                @if(count($orders)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
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
                    toastr.error('{{\App\CPU\translate('Invalid date range')}}!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
