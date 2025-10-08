@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Claim Report'))

@push('css_or_js')

@endpush

@section('content')
<style>

.__table thead th {
    padding-top: 0rem;
    padding-bottom: 0rem;
}

.__table tbody td {
    padding-top: .10rem;
    padding-bottom: .10rem;
}
</style>
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/order_report.png')}}" alt="">
                {{\App\CPU\translate('Claim_Report')}}
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
                       <th>Select All<br>
                        <input type="checkbox" id="select-all">
                       </th>
                        <th>{{\App\CPU\translate('Vendor Code')}}</th>
                        <th>Seller Name</th>
                        <th>{{\App\CPU\translate('Order ID')}}</th>
                        <th>{{\App\CPU\translate('Order Date')}}</th>
                        
                       <th>{{\App\CPU\translate('Net Payble to Seller')}}</th>
                          <th>{{\App\CPU\translate('Claim Date')}}</th>
                          <th>{{\App\CPU\translate('Invoice')}}</th>

                       
                    </tr>
                    </thead>
                     @php
                        $countRow = 1;
                    @endphp
                    <tbody>
                    @foreach($orders as $key=>$order)
                    @php
                        $total_tax=0;
                        $all_coupons=0;
                        $total_mrp = 0;
                        $iteam_tax = 0;
                        $amount_without_tax = 0;
                        $shipping = 0;
                    
                    @endphp
                    
                    <?php  $orderDetails = App\Model\Order::with('seller')->with('shipping')->with('details')->where('id', $order->id)->first();
                     ?>
                    @foreach($orderDetails->details as $key=>$details)
                    
                     <?php
                            //dd($details);
                     $sku_product = DB::table('sku_product_new')->where('product_id',$details->product_id)->where('variation',$details->variant)->first();
                      //dd($sku_product);
                    //$productData = json_decode($details->product_details, true);
                $productData = !empty($details->product_details) ? json_decode($details->product_details, true) : [];
                //dd($productData);
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
                              //dd($priceValue);

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
                    @php
    $rowAmount = (
        ($sku_product->listed_price + $order->shipping_cost + $order->discount_amount)
        - $order->shipping_cost
        - (
            (
                (
                    ($sku_product->listed_price + $order->shipping_cost + $order->discount_amount) * 5 / 100
                ) +
                (
                    $orderDetails->seller->fee * $sku_product->listed_percent / 100
                )
            ) * 0.18
            +
            (
                ($sku_product->listed_price + $order->shipping_cost + $order->discount_amount) * 5 / 100
            )
            +
            (
                $orderDetails->seller->fee * $sku_product->listed_percent / 100
            )
        )
        - $order->discount_amount
    );
@endphp

                        <tr>
                            <!-- <td>{{ $countRow }}</td> -->
                             <td><input type="checkbox" class="row-check" value="{{ $order->id }}" data-amount="{{ $rowAmount }}"></td>
                            <td>VN{{ $order->seller->id }}</td>
                            <td>{{ $order->seller->shop->name }}
                            <?php $seller_state = $order->seller->shop->state ?? null; ?>
                            </td>
                            <td>
                                <a class="title-color" href="{{route('admin.orders.details',['id'=>$order->id])}}">{{$order->id}}</a>
                            </td>
                            <td>{{date('d-m-Y',strtotime($order['created_at']))}}</td>
                             
                            
                                    <td class="row-amount">{{ number_format($rowAmount, 2) }}</td>
<td>{{date('d-m-Y',strtotime($order['updated_at']))}}</td>
<td>IC/VN{{ $order->seller->id }}/{{$order->id}}</td>
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
                     <tfoot>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td><strong>Total:</strong></td>
      <td><strong id="totalAmount">{{ number_format($totalAmount ?? 0, 2) }}</strong></td>
      <td><a class="btn btn-outline-success square-btn btn-sm mr-1" target="_blank" title="Invoice" href="#">
                                                <i class="tio-file"></td>
                                                <td> <button type="button" class="btn btn-xs btn-primary toggle-status-btn" data-toggle="modal"
                            data-target="#retExcModal"
                           
                            data-order-id="">
        Made Payment
    </button></td>
    </tr>
  </tfoot>
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
     <div class="modal fade retExcModal" id="retExcModal" tabindex="-1" role="dialog" aria-labelledby="retExcModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
           
            <div class="modal-content">
                <div class="modal-body">
                     <h3 style="margin-bottom: 20px;">Payment Made</h3>
                    <form action="{{route('admin.report.made_payment')}}" method="post">
                        @csrf
                       @if(!empty($order->seller->shop->bank_name))
                        <h6>{{ $order->seller->shop->name }}</h6>
                        <input type="hidden" name="selected_ids" id="selected_ids">
                        <input type="hidden"  name="seller_id" value="{{$order->seller->shop->seller_id}}">
                <div class="form-group" style="border: 1px solid  rgb(208, 219, 233); border-radius: .3125rem;">
    &nbsp<label>Bank Name:</label>  &nbsp {{ $order->seller->shop->bank_name }}<br>
    &nbsp<label>Branch:</label>     &nbsp {{ $order->seller->shop->bank_branch }}<br>
    &nbsp<label>Account No.:</label>&nbsp  {{ $order->seller->shop->acc_no }}<br>
    &nbsp<label>IFSC:</label>       &nbsp {{ $order->seller->shop->ifsc }}<br>
  </div>
    @endif
  <div class="form-row">
     <div class="form-group col-md-6">
      <label for="inputState">Payment Against</label>
      <select id="payment_against" class="form-control" name="payment_against">
        <option selected>Choose...</option>
        <option >Against Claim</option>
         <option >On acc / adhoc</option>
      </select>
    </div>
    
    <div class="form-group col-md-6">
      <label for="inputPassword4">Payment Date</label>
      <input type="date" class="form-control" name="payment_date" placeholder="">
    </div>
  </div>
 <script>
    document.getElementById('payment_against').addEventListener('change', function () {
        let status = this.value;
        let x = document.getElementById('totalAmount').textContent;
         console.log(status);
        if (status === "Against Claim") {
            console.log(x);
            document.getElementById('total_amount').value = x;
        }else {
            document.getElementById('total_amount').value = '';
        } 
    });
</script>

  
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">Amount</label>
     <input type="text" class="form-control" id="total_amount" name="total_amount">
    </div>
   
    <div class="form-group col-md-6">
      <label for="inputZip">Payment Mode</label>
      <select id="inputState" class="form-control" name="payment_mode">
        <option selected>Choose...</option>
        <option name="neft">NEFT</option>
         <option name="rtgs">RTGS</option>
          <option name="imps">IMPS</option>
      </select>
    </div>
  </div>
  <div class="form-row">
  <div class="form-group col-md-6">
    <label for="inputAddress">Payment Ref No.</label>
    <input type="" class="form-control" id="inputAddress" placeholder="" name="payment_ref_no">
  </div>
  <div class="form-group col-md-6">
    <label for="inputAddress">Payment Bank Name</label>
    <input type="" class="form-control" id="inputAddress" placeholder="" name="payment_bank_name">
  </div>
</div>
  <div class="form-group">
    <label for="inputAddress2">Narration</label>
    <input type="text" class="form-control" id="inputAddress2" placeholder="" name="narration">
  </div>
  
  <button type="submit" class="btn btn-primary text-end">Save</button>
</form>


</div>
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


<script>
function toggleSwitchColumns(type) {
    const billingCols = document.querySelectorAll('.billing-column');
    const earningCols = document.querySelectorAll('.earning-column');

    if (type === 'billing') {
        const billingChecked = document.getElementById('billingSwitch').checked;
        billingCols.forEach(col => {
            col.classList.toggle('d-none', !billingChecked);
        });
    }

    if (type === 'earning') {
        const earningChecked = document.getElementById('earningSwitch').checked;
        earningCols.forEach(col => {
            col.classList.toggle('d-none', !earningChecked);
        });
    }
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.row-check');
    const selectAll = document.getElementById('select-all');
    const totalCell = document.getElementById('totalAmount');
    const selectedInput = document.getElementById('selected_ids'); // hidden input

    function updateTotalAndIds() {
        let total = 0;
        let anyChecked = false;
        let selectedIds = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                anyChecked = true;
                total += parseFloat(cb.getAttribute('data-amount')) || 0;
                selectedIds.push(cb.value); // get order_id
            }
        });

        if (!anyChecked) {
            checkboxes.forEach(cb => {
                total += parseFloat(cb.getAttribute('data-amount')) || 0;
            });
        }

        totalCell.textContent = total.toFixed(2);
        selectedInput.value = selectedIds.join(','); // comma separated order IDs
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateTotalAndIds();
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
        });
    });

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateTotalAndIds();
    });

    updateTotalAndIds(); // initial run
});
</script>


@endpush
