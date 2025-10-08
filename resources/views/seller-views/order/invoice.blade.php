<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{\App\CPU\translate('invoice')}}</title>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <style media="all">
        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: sans-serif;
            color: #333542;
        }


        /* IE 6 */
        * html .footer {
            position: absolute;
            top: expression((0-(footer.offsetHeight)+(document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight)+(ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop))+'px');
        }

        body {
            font-size: .75rem;
        }

        img {
            max-width: 100%;
        }

        .customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        table {
            width: 100%;
        }
        
       

        table thead th {
            padding: 6px;
            font-size: 11px;
            text-align: left;
            border: 1px solid black;
        }

        table tbody th,
        table tbody td {
            padding: 6px;
            font-size: 11px;
            border: 1px solid black;
           
        }

        table.fz-12 thead th {
            font-size: 12px;
        }

        table.fz-12 tbody th,
        table.fz-12 tbody td {
            font-size: 12px;
        }

        table.customers thead th {
            background-color: #0177CD;
            color: #fff;
        }

        table.customers tbody th,
        table.customers tbody td {
            background-color: #FAFCFF;
        }

        table.calc-table th {
            text-align: left;
        }

        table.calc-table td {
            text-align: right;
        }
        table.calc-table td.text-left {
            text-align: left;
        }

        .table-total {
            font-family: Arial, Helvetica, sans-serif;
        }


        .text-left {
            text-align: left !important;
        }

        .pb-2 {
            padding-bottom: 6px !important;
        }

        .pb-3 {
            padding-bottom: 14px !important;
        }

        .text-right {
            text-align: right;
        }

        table th.text-right {
            text-align: right !important;
        }

        .content-position {
            padding: 10px 10px;
        }

        .content-position-y {
            padding: 0px 0px;
        }

        .text-white {
            color: white !important;
        }

        .bs-0 {
            border-spacing: 0;
        }
        .text-center {
            text-align: center;
        }
        .mb-1 {
            margin-bottom: 4px !important;
        }
        .mb-2 {
            margin-bottom: 8px !important;
        }
        .mb-4 {
            margin-bottom: 24px !important;
        }
        .mb-30 {
            margin-bottom: 30px !important;
        }
        .px-10 {
            padding-left: 10px;
            padding-right: 10px;
        }
        .fz-14 {
            font-size: 14px;
        }
        .fz-12 {
            font-size: 12px;
        }
        .fz-10 {
            font-size: 10px;
        }
        .font-normal {
            font-weight: 400;
        }
        .border-dashed-top {
            border-top: 1px dashed #ddd;
        }
        .font-weight-bold {
            font-weight: 700;
        }
        .bg-light {
            background-color: #F7F7F7;
        }
        .py-30 {
            padding-top: 30px;
            padding-bottom: 30px;
        }
        .py-4 {
            padding-top: 24px;
            padding-bottom: 24px;
        }
        .d-flex {
            display: flex;
        }
        .gap-2 {
            gap: 8px;
        }
        .flex-wrap {
            flex-wrap: wrap;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        a {
            color: rgba(0, 128, 245, 1);
        }
        .p-1 {
            padding: 4px !important;
        }
        .h2 {
            font-size: 1.5em;
            margin-block-start: 0.83em;
            margin-block-end: 0.83em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }

        .h4 {
            margin-block-start: 1.33em;
            margin-block-end: 1.33em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }

    </style>
</head>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>
<div class="first">
    <table class="content-position " >
        <tr>
            <td class="p-0 text-left" style="font-size: 26px; " >
                {{\App\CPU\translate('Order_Invoice')}}
            </td>
            <td>
                <img height="70" src="{{asset("storage/company/$company_web_logo")}}" alt="">
            </td>
        </tr>
    </table>

    <table class="bs-0 mb-1 px-10">
        <tr>
            <th class="content-position-y text-left">
               <!-- <h4 class="text-uppercase mb-1 fz-14">
                    {{\App\CPU\translate('invoice')}} #{{ $order->id }}
                </h4><br>-->
                <h4 class="text-uppercase mb-1 fz-14">
                    {{\App\CPU\translate('sold_by')}}
                    : {{ $order->seller_is == 'admin' ? $company_name : (isset($order->seller->shop) ? $order->seller->shop->name : \App\CPU\translate('not_found')) }}
                </h4>
                <p style=" margin-top: 6px; margin-bottom:0px;">{{ $order->seller->shop->billing_address }}, {{ $order->seller->shop->city }}, {{ $order->seller->shop->state }}, {{ $order->seller->shop->country }}, {{ $order->seller->shop->pincode }}</p>
                <?php $seller_state = $order->seller->shop->state ?? null; ?>
                @if($order['seller_is']!='admin' && isset($order['seller']) && $order['seller']->gst != null)
                <h4 class="text-capitalize fz-12">{{\App\CPU\translate('GST')}}
                        : {{ $order['seller']->gst }}</h4>
                @endif
                
                <p style=" margin-top: 6px; margin-bottom:0px;" ><b>GSTIN</b> : {{ $order->seller->shop->gst_no }}</p> 
                <p style=" margin-top: 6px; margin-bottom:0px;" ><b>PAN</b> : {{ $order->seller->shop->pan }}</p> 
            </th>
            <th class="content-position-y text-right">
               <p style=" margin-top: 6px; margin-bottom:0px;" ><b>Order No</b> #{{ $order->id }}</p> 
                 <p style=" margin-top: 6px; margin-bottom:0px;" ><b>Order Date</b> {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}</p> 
                 <br>
                 <!--<p style=" margin-top: 6px; margin-bottom:0px;" ><b>Invoice No.</b> VN{{$order->seller->shop->seller_id }}</p> 
               <p style=" margin-top: 6px; margin-bottom:0px;" ><b>Invoice Date & time</b> {{date('d-m-Y h:i a')}}</p>-->
               <!-- <h4 class="fz-14">{{\App\CPU\translate('date')}} : {{date('d-m-Y h:i:s a')}}</h4>-->
               <br>
                <p style=" margin-top: 6px; margin-bottom:0px;" ><b>State/UT code: </b>{{ $stateId = DB::table('states')->where('name', $order->seller->shop->state)->value('id') }}</p>
               <p style=" margin-top: 6px; margin-bottom:0px;" ><b>Place of supply: </b> {{ $order->seller->shop->state }}</p>
               <p style=" margin-top: 6px; margin-bottom:0px;" ><b>Place of delivery: </b> {{ $order->seller->shop->state }}</p>
            </th>
        </tr>
    </table>
</div>
<div class="">
    <section>
        <table class="content-position-y fz-12">
            <tr>
                <td class="font-weight-bold ">
                    <table>
                        <tr>
                            <td>
                                @if ($order->shippingAddress)
                                
                                    <span class="h2" style="margin: 0px;">{{\App\CPU\translate('shipping_to')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                       <!-- <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>-->
                                       <p style=" margin-top: 6px; margin-bottom:0px;">{!! $order->company_name ? '<b>Company name  : </b>' .$order->company_name : "" !!}</p>
                                       <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->shippingAddress ? $order->shippingAddress['contact_person_name'] : ""}}</p>
                                        <!--<p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>-->
                                        <!--<p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>-->
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->shippingAddress ? $order->shippingAddress['phone'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->shippingAddress ? $order->shippingAddress['address'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->shippingAddress ? $order->shippingAddress['city'] : ""}} {{$order->shippingAddress ? $order->shippingAddress['zip'] : ""}}</p>
                                         <p style=" margin-top: 6px; margin-bottom:0px;">{!! $order->gst_number ? '<b>GST  : </b>' . $order->gst_number : '' !!}</p>
                                        <?php $customer_state = json_decode($order->shipping_address_data)->state ?? 'State not available'; ?>
                                    </div>
                                @else
                                    <span class="h2" style="margin: 0px;">{{\App\CPU\translate('customer_info')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>
                                        @if (isset($order->customer) && $order->customer['id']!=0)
                                            <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                                            <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
                                        @endif
                                    </div>
                                @endif
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>

                <td>
                    <table>
                        <tr>
                            <td class="text-right">
                                @if ($order->billingAddress)
                                    <span class="h2" >{{\App\CPU\translate('billing_address')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->billingAddress ? $order->billingAddress['contact_person_name'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->billingAddress ? $order->billingAddress['phone'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->billingAddress ? $order->billingAddress['address'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->billingAddress ? $order->billingAddress['city'] : ""}} {{$order->billingAddress ? $order->billingAddress['zip'] : ""}}</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


    </section>
</div>

<br>

<div class="">
    <div class="content-position-y">
        <table class="customers bs-0">
            <thead>
                <tr>
                    <th>{{\App\CPU\translate('SL')}}</th>
                    <th>{{\App\CPU\translate('item_description')}}</th>
                     <th>
                        {{\App\CPU\translate('qty')}}
                    </th>
                     <th>
                        {{\App\CPU\translate('unit')}}
                    </th>
                     <th>
                        {{\App\CPU\translate('Rate')}}
                    </th>
                     <th>
                        {{\App\CPU\translate('amount')}}
                    </th>
                     <th>
                        {{\App\CPU\translate('GST_in_tax')}}
                    </th>
                   
                    
                    
                   
                    <th class="text-right">
                        {{\App\CPU\translate('total')}}
                    </th>
                </tr>
            </thead>
            @php
                $subtotal=0;
                $total=0;
                $total_qty = 0;
                $amount = 0;
                $sub_total=0;
                $total_tax=0;
                $total_shipping_cost=0;
                $total_discount_on_product=0;
                $ext_discount=0;
                $sub_total_mrp =0;
                $all_coupons=0;
                $sub_total_price=0;
                $discountPrice = 0;
                $count = 1;
                $tax_amount_item = 0;
                $total_mrp = 0;
                $iteam_tax = 0;
                $amount_without_tax = 0;
            @endphp
            <tbody>
            @foreach($order->details as $key=>$details)
            
                <?php
                $productData = json_decode($details->product_details, true);
                if ($productData['discount_type'] == 'percent') {
                $discountPrice = $productData['unit_price'] - (($productData['discount'] / 100) * $productData['unit_price']);
                } else {
                $discountPrice = $productData['unit_price'] - $productData['discount'];
                }
                //$discountPrice = $details['price'];
                
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
            
                @php $subtotal=(($discountPrice)*$details->qty);
                $item_tax = ($discountPrice-($discountPrice*100)/($details->product_all_status->tax + 100))*$details->qty;
                $amount_without_tax = ($discountPrice*100)/($details->product_all_status->tax + 100);
                @endphp
               
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                 {{-- Display product name if available --}}
                {{ $productData['name'] ?? '' }}
                
                {{-- Check if variant exists and display it --}}
                @if(!empty($details['variant']))
                    <br>
                    {{ \App\CPU\translate('variation') }} : {{ $details['variant'] }}
                @endif
                
                {{-- Decode and display SKU code if product variation exists --}}
                @php
                    $skuData = isset($productData['variation']) ? json_decode($productData['variation'], true) : [];
                @endphp
                
                @if(!empty($skuData))
                    @foreach ($skuData as $keyss => $skuItem)
                    @if($details['variant'] == $skuItem['type'])
                       
                            {{ \App\CPU\translate('SKU_code') }}: {{ $skuItem['sku'] ?? 'N/A' }}
                       
                    @endif
                    @endforeach
                @endif


                       <br>
                {{\App\CPU\translate('HSN_code')}} : {{$productData['HSN_code']}}
                        
                    </td>
                      <td>{{$details->qty}}</td>
                    <td>No.</td>
                    <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($discountPrice))}}</td>
                     <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($details->qty*($amount_without_tax))))}}</td>
                    
                    <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($item_tax))}} [{{ isset($details->product_all_status->tax)? $details->product_all_status->tax : ''}}{{ isset($details->product_all_status->tax_type)? '%' : ''}}]</td>
                   
                    
                    
                  
                    <td class="text-right">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(($details->qty*($discountPrice))))}}</td>
                </tr>

               
                
                 @php
                    $count +=$count;
                    $tax_amount_item = $details->product_all_status->tax/2;
                    $total_qty += $details->qty;
                    $sub_total_mrp+=(($details->qty*$amount_without_tax));
                    $all_coupons = $order->discount_amount;
                    $sub_total_price+=$discountPrice;
                    $sub_total+=$discountPrice*$details['qty'];
                    $total_tax+=$item_tax;
                    $total_shipping_cost+=$details->shipping ? $details->shipping->cost :0;
                    $total_discount_on_product+=$details['discount'];
                    $total+=$subtotal;
                    $total_mrp+= $details->qty*($discountPrice) 
                @endphp
            @endforeach
            </tbody>
            
                    <?php
                    
                        if ($order['extra_discount_type'] == 'percent') {
                        $ext_discount = ($sub_total / 100) * $order['extra_discount'];
                        } else {
                        $ext_discount = $order['extra_discount'];
                        }
                ?>

                @php($shipping=$order['shipping_cost'])
                
                <?php  
                   $toatal_ship_inst= 0;
                   $instant_delivery_amount = 0; 
                   ?>
                
                 @if($order->instant_delivery_type == 1)
                                        <?php 
                                        
                                                 /* if($shipping <= 150){
                                                      $toatal_ship_inst =   $shipping*3;
                                                    }elseif($shipping > 150 && $shipping <= 500){
                                                         $toatal_ship_inst =   $shipping*2;
                                                    }elseif($shipping > 500){
                                                         $toatal_ship_inst =   $shipping*1.56;
                                                    }else{
                                                        $toatal_ship_inst;
                                                    }
                                           $instant_delivery_amount = $toatal_ship_inst - $shipping;*/
                                        ?>
                                        
                @endif        
                <?php  $shipping = $shipping + $instant_delivery_amount;  
                $shipping_gst = $shipping*18*0.01;
                $shipping_amount_rate = $shipping - $shipping_gst;
                $total_tax = $total_tax + $shipping_gst;
                ?>
            <tbody>
                <tr>
                  <td> </td>  
                  <td>shipping charges </td>  
                  <td>1 </td>  
                  <td>No. </td>  
                  <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td>  
                   
                  <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_amount_rate))}}</td>  
                 <td>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_gst))}} [18%]</td> 
                  <td class="text-right">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td>  
                 
                </tr>
            </tbody>
            
            <tbody>
                <tr>
                 
                  <td class="text-center" colspan="2" ><b>Total</b></td>
                  <td>{{ $total_qty }} </td>  
                  <td> </td>  
                  <td><b></b></td>
                  
                  <td><b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping_amount_rate + $sub_total_mrp))}}</b></td>  
                   <td><b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</b></td> 
                  <td class="text-right"><b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_mrp+$shipping))}}</b></td> 
                 
                </tr>
                
                    <tr>
                        <td class="text-center" style=" background-color: darkgray;" colspan="2" ><b>{{\App\CPU\translate('TOTAL_BILL_AMOUNT')}}</b></td>
                        <td class="text-center" style=" background-color: darkgray;" colspan="6">
                             {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_mrp+$shipping))}}</td>
                    </tr>
                
            <!-- @if($order->instant_delivery_type == 1)
                <tr>
                     <td class="text-center" colspan="2"><b>{{\App\CPU\translate('instant_delivery_amount')}}</b></td>
                    <td class="text-center" colspan="6">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($instant_delivery_amount))}}</td>
             </tr>
             @endif-->
                
                <tr>
                 
                  <td class="text-center" colspan="2" ><b>{{\App\CPU\translate('Less:_Coupon_Discount')}}</b></td>
                  <td class="text-center" colspan="6" >{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($all_coupons))}} </td>  
                
                </tr>
                
                 <tr>
                        <td class="text-center" colspan="2" ><b>{{\App\CPU\translate('Less:_Wallet_Payment')}}</b></td>
                        <td class="text-center" colspan="6">
                             {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->wallet_deduction))}}  </td>
                    </tr>
                    <!--@if ($order->order_type=='POS')
                        <tr>
                            <td class="text-center" colspan="2" ><b>{{\App\CPU\translate('extra_discount')}}</b></td>
                            <td class="text-center" colspan="6">
                                 {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($ext_discount))}} </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-center" colspan="2" ><b>{{\App\CPU\translate('discount_on_product')}}</b></td>
                        <td class="text-center" colspan="6">
                             {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_discount_on_product))}} </td>
                    </tr>-->
                    
                    <tr>
                        <td class="text-center" style=" background-color: cadetblue;" colspan="2" ><b>{{\App\CPU\translate('Net_Payable_Amount')}}</b></td>
                        <td class="text-center"  style=" background-color: cadetblue;" colspan="6">
                             <b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_mrp+$shipping - $all_coupons - $order->wallet_deduction))}}</b></td>
                    </tr>
                
            </tbody>
        </table>
    </div>
</div>

<div class="">
    <div class="content-position-y">
        <table class="customers bs-0">
            <thead>
                <tr>
                    <th>{{\App\CPU\translate('Tax Type')}}</th>
                    <th>{{\App\CPU\translate('Taxable Amount')}}</th>
                    <!-- <th>
                        {{\App\CPU\translate('Rate')}}
                    </th>-->
                     <th colspan="2" >
                        {{\App\CPU\translate('Tax Amount')}}
                    </th>
                     <th class="text-center" colspan="5">
                        {{\App\CPU\translate('Invoice_Amount_in_words')}}
                    </th>
                   
                </tr>
            </thead>
           
            <tbody>
                <?php $total_tax_amount = 0; 
                
               // $tax_amount = $tax_amount_item*0.01 *($sub_total_mrp + $shipping_amount_rate)
                $tax_amount = $total_tax/2;
                ?>
                <tr>
                   
                  <td style=" border-right: none; border-top: none;border-bottom: none;">SGST </td>  
                  @if($customer_state == $seller_state)
                  <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</td>  
                 <!-- <td  style="border: none;">{{ isset($details->product_all_status->tax)? $tax_amount_item : ''}}{{ isset($details->product_all_status->tax_type)? '%' : ''}}</td> --> 
                  <td colspan="2" style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($tax_amount))}}</td> 
                  <?php $total_tax_amount = $total_tax_amount + $tax_amount; ?>
                  @else
                 <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(0))}}</td>  
                 <!-- <td  style="border: none;"> 0% </td>  -->
                  <td colspan="2" style="border: none;"> - </td>  
                  @endif
                 
                 
                  <td colspan="5" style="border-top: none;border-bottom: none;"></td>  
                 
                 
                </tr>
                
                   <tr>
                    
                  <td style=" border-right: none; border-top: none;border-bottom: none;">CGST </td>  
                  @if($customer_state == $seller_state)
                  <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</td>  
                 <!-- <td  style="border: none;">{{ isset($details->product_all_status->tax)? $tax_amount_item : ''}}{{ isset($details->product_all_status->tax_type)? '%' : ''}}</td> --> 
                  <td colspan="2" style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($tax_amount))}}</td>  
                  <?php $total_tax_amount = $total_tax_amount + $tax_amount; ?>
                  @else
                 <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(0))}}</td>  
                 <!-- <td  style="border: none;"> 0% </td>  -->
                  <td colspan="2" style="border: none;"> - </td>  
                  @endif
                 
                  <?php
                  $locale = 'en_US';
                    $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);
                    
                    $number = $total_mrp + $shipping;
                    $roundedValue = round($number, 2);
                    $words = $formatter->format($roundedValue);
                    
                    // Check if there are any unintended line breaks
                    $words = ucwords(nl2br(htmlspecialchars($words)));
                  ?>
                  <td colspan="5" style="border-top: none;border-bottom: none;">{{$words}} Only .</td>  
                 
                 
                </tr>
                
                <tr>
                    
                  <td style=" border-right: none; border-top: none;border-bottom: none;">IGST </td>  
                  @if($customer_state != $seller_state)
                  <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</td>  
                 <!-- <td  style="border: none;">{{ isset($details->product_all_status->tax)? $tax_amount_item : ''}}{{ isset($details->product_all_status->tax_type)? '%' : ''}}</td> --> 
                  <td colspan="2" style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</td>  
                  <?php 
                 // $total_tax_amount = $total_tax_amount + $tax_amount;
                  $total_tax_amount = $total_tax;
                  ?>
                  @else
                  <td style="border: none;">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency(0))}}</td>  
                 <!-- <td  style="border: none;"> 0% </td> --> 
                  <td colspan="2" style="border: none;"> - </td>  
                  @endif
                  
                 
                  <td colspan="5" style="border-top: none;border-bottom: none;"></td>  
                 
                 
                </tr>
                
                 <tr>
                  <td style=" border-right: none; border-top: none;border-bottom: none;"></td>  
                  <td style="border: none;"></td>  
                  <td  style="border: none;"></td>  
                  <td  style="border: none;"></td>  
                 
                  <td  style="background-color: #0177CD;color: #fff;" class="text-center" colspan="5">Payment Mode</td>  
                </tr>
                
                <tr >
                   <td style=" border-right: none; border-top: none;border-bottom: none;"></td>  
                  <td style="border: none;"></td>  
                  <td  style="border: none;"></td>  
                  <td  style="border: none;"></td>   
                  
                 <td class="text-center" colspan="5">
                        @if ($order['payment_method'] === 'online_payment')
                            {{ \App\CPU\translate('Prepaid') }}
                        @else
                            {{ \App\CPU\translate('COD') }}
                        @endif
                    </td>
                </tr>
                
                 <tr >
                  <td style="border-right: none;"></td>  
                  <td style="border-right: none; border-left: none;"><b>TOTAL TAX AMOUNT</b></td>  
                  <td  style="border-right: none;border-left: none;"></td>  
                  <td  style="border-right: none; border-left: none;"><b>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax_amount))}}</b></td>    
                  <td   class="text-center" colspan="5" style="border-bottom: none;"><span style="margin-right: 10px;"><b>For</b></span>&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ $order->seller_is == 'admin' ? $company_name : (isset($order->seller->shop) ? $order->seller->shop->name : \App\CPU\translate('not_found')) }}</b></td>  
                </tr>
                
                <tr >
                  <td style="border-right: none;" colspan="4">
                      <p class="text-center"><b>Thank You ! For shopping with us.</b></p>
                      If you require any assistance or have feedback or suggestions about our app, You can connect us at <a href="mail::to({{ $company_email }})">{{ $company_email }}</a> or {{ $company_phone }}
                  </td>  
                  
                  <td  class="text-center" colspan="5">
                      @php($data = \App\Model\seller::where('id',$order->seller->id)->first())
                      <small>
                          @if($data->signature)
                          <img src="{{asset('storage/seller/'.$data->signature)}}"
                                         height="40"  alt="">
                          @else
                          
                          @endif
                                         
                    </small>
                    <br>
                      <b>Authorized Signatory</b>
                      
                      </td>  
                </tr>
                
            </tbody>
            
           
        </table>
        
         <table class="customers bs-0">
           <thead>
                <tr>
                   
                     <td class="text-center" colspan="9" style="background-color: #0177CD;color: #fff;">
                      <b>Return: </b>While we at interiorchowk strive for flawless deliveries every time, in the unlikely event that you need to return an item, please do so with the original brand box or price tag, original packing, and invoice. Without these, it will be very difficult for us to fulfill your request. Plese help us in supporting you. <i>Terms and Condition apply</i>
                    </td>
                   
                </tr>
            </thead>
           
        </table>
      
    </div>
</div>


<div class="row">
    <section>
        <table class="">
           <!--<tr>
                <th class="fz-12 font-normal pb-3">
                    {{\App\CPU\translate('If_you_require_any_assistance_or_have_feedback_or_suggestions_about_our_site,_you')}} <br /> {{\App\CPU\translate('can_email_us_at')}} <a href="mail::to({{ $company_email }})">{{ $company_email }}</a>
                </th>
            </tr>-->
            <tr>
                <th class="content-position-y bg-light py-4">
                   <!-- <div class="d-flex justify-content-center gap-2">
                        <div class="mb-2">
                            <i class="fa fa-phone"></i>
                            {{\App\CPU\translate('phone')}}
                            : {{ $company_phone }}
                        </div>
                        <div class="mb-2">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            {{\App\CPU\translate('email')}}
                            : {{$company_email}}
                        </div>
                    </div>
                    <div class="mb-2">
                        {{url('/')}}
                    </div>-->
                    <div>
                        {{\App\CPU\translate('All_copy_right_reserved_©_'.date('Y').'_').$company_name}}
                    </div>
                </th>
            </tr>
        </table>
    </section>
</div>

</body>
</html>
