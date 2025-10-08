@extends('layouts.back-end.common_seller_1')

@section('content')


<link rel="stylesheet" href="{{ asset('website/assets/css/billing.css') }}">
<link rel="stylesheet" href="{{ asset('website/assets/css/step-wizard.css') }}">

<main class="main">
            <div class="page-content">
                <div class="consent-para">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <p>Hey! Remember, InteriorChowk or it’s team will never ask you for financial details or
                                    payment for any contest you’ve won. If you receive such request, stay alert and
                                    don’t share sensitive information through any medium. Stay secure, shop smart, and
                                    elevate your space!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row mt-5">
                        <div class="col-12 col-sm-12 col-md-8">
                            <div class="row background-opacity">
                                <div class="col-12 col-sm-12 col-md-7">
                                    <div class="bbdLeftWrapper">
                                        <div class="prdDtlWrap">
                                            <span>
                                                <h4>Order ID</h4>
                                                <p>#{{ $order_id}}</p>
                                            </span>
                                        </div>
                                         @foreach($orders as $order)
                                       
                                        <div class="shippingWrap">
                                            <div class="mb-3">
                                                <h3>Shipping to :</h3>
                                                <h6>{{$order->shipping_address_data->contact_person_name}}</h6>
                                                <p>{{$order->shipping_address_data->address}},{{$order->shipping_address_data->landmark}},
                                                    {{$order->shipping_address_data->city}} -
                                                    {{$order->shipping_address_data->zip}},<br>
                                                    {{$order->shipping_address_data->phone}}</p>
                                            </div>
                                            
                                            <!-- <div>
                                                <h3>Billing Address :</h3>
                                                <h6>Rakesh</h6>
                                                <p>A-1402 samrishi appartments, Greator Noida West Link Rd,
                                                    Techzone 4, Amrapali Dream Valley, Greator Noida
                                                    GAUTAM BUDDHA NAGAR
                                                    201318
                                                    +91-9810819806</p>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-5">
                                    <div class="bbdLeftWrapper">
                                        <div class="prdDtlWrap">
                                            <span class="text-center">
                                                <h4>Date & Time</h4>
                                                <p>09:28 PM | 27-Nov-2025</p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="dwnInvWrap">
                                        <h5>Billing Summary</h5>
                                        <button class="btn btn-dwnInvoice">Download invoice</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="billSummContent">
                                        <h4>Sub Total</h4>
                                        <p>Rs. {{ $order['total_variant_mrp']}}</p>
                                    </div>
                                    <div class="billSummContent">
                                        <h4>Bag Savings</h4>
                                        <p>- Rs.  {{$order['total_variant_mrp']- $order['total_listed_price']}} </p>
                                    </div>
                                   
                                      <hr>

                                    <div class="billSummContent">
                                        <h4>Coupon & Voucher</h4>
                                        <p> - Rs.{{$order->order_amount}}</p>
                                    </div>
                                    
                                     <div class="billSummContent">
                                        <h4>Delivery Charge</h4>
                                        <p>Rs. {{$order->shipping_cost}}</p>
                                    </div>
                                    <div class="billSummContent">
                                        <h4>Paid By Wallet</h4>
                                        <p> Rs.{{$order->wallet_deduction}}</p>
                                    </div>
                                   <hr>
                                    <div class="billSummContent">
                                        <h4>Total Amount</h4>
                                        <p>Rs.{{ ($order['total_variant_mrp']-($order['total_variant_mrp']- $order['total_listed_price']))-($order->order_amount)+($order->shipping_cost)-($order->wallet_deduction)}} </p>
                                    </div>
                                    
                                    <hr class="mb-3">
                                    <div class="billSummContent">
                                        <h4>Payment status</h4>
                                        <label>
                                            @if($order->payment_method === 'cash_on_delivery')
                                            <span class="spCod">COD</span>
                                            @else
                                            <span class="spPaid">Paid</span>
                                            @endif
                                            </label>
                                    </div>
                                </div>
                                @endforeach
                                <div class="col-12 col-sm-12 col-md-12">
                                    <button type="button" class="btn btnOrdCan btnOrdCan1" data-toggle="modal"
                                        data-target="#trackorderModal" data-id="{{ $order_id }}">
                                        Track Order
                                    </button>

                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4">
                            <div class="right-itemWrapper right-itemWrapper-bbdRight">
                                <h1 class="billBefRightHead">Ordered Products</h1>
                               <ul>
    @foreach($orders as $order)
        @foreach($order->sku_product as $product)
            <li>
                <div class="d-flex align-items-end justify-content-between">
                    <div class="pro-desc">
                        <img src="{{ asset('storage/images/'.$product->thumbnail_image) }}" class="img-fluid" alt="pro-img">
                        <div class="ml-3">
                            <h6 class="proHead">{{ $product->name }}</h6>
                            <span class="proType">Product Type</span>
                            <h4>
                                Rs. {{ $product->listed_price }}
                                <span style="text-decoration: line-through;">Rs.{{ $product->variant_mrp }}</span>
                            </h4>
                            <p>Quantity: {{ $product->qty }}</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between badRetEWrap">
                    <p class="d-flex align-items-center justify-content-start badPara">
                        <img src="assets/images/icons/product-return.png" class="img-fluid" alt="pro-return" />
                        5 Days Return
                    </p>
                    <div>
                        <button type="button" class="btn retExt btn-RetExcOrd"
                            data-toggle="modal"
                            data-target="#retExcModal"
                            data-product='@json($product)' 
                            data-order-id="{{ $order->id }}">
                            Return/Exchange
                        </button>
<button type="button" class="btn retStatu btn-RetExcOrd"
    data-toggle="modal"
    data-target="#returnStatusModal"
    data-product='@json($product)' 
    data-order-id="{{ $order->id }}" style="display:none">
    Return Status
</button>


                        <button type="button" class="btn btn-revProd" 
                        data-toggle="modal"
                            data-target="#revProdModal"
                            data-product='@json($product)' 
                            data-order-id="{{ $order->id }}"
                            >
                            Review Product
                        </button>
                    </div>
                </div>
            </li>
        @endforeach
    @endforeach
</ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main><!-- End .main -->











 <!--Return exchange Modal -->
    <div class="modal fade retExcModal" id="retExcModal" tabindex="-1" role="dialog" aria-labelledby="retExcModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-4">
                            <div class="right-itemWrapper right-itemWrapper-bbdRight">
                                <ul>
                                  <li>
                                    <input type="hidden" id="order_id" name="order_id" value="">
                                    <input type="hidden" id="product_id" name="product_id" value="">
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div class="pro-desc">
                                            <img src="" class="img-fluid" alt="pro-img">
                                            <div class="ml-3">
                                                <h6 class="proHead"></h6>
                                                <span class="proType">Product Type</span>
                                                <h4>
                                                    <span class="priceMain" style="text-decoration: none;"></span>
                                                    <span class="priceStrike" style="text-decoration: line-through;"></span>
                                                </h4>
                                                <p class="proQty"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="d-flex align-items-center justify-content-start">
                                        <img src="assets/images/icons/product-return.png" class="img-fluid" alt="pro-return" />5 Days Return
                                    </p>
                                </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-8">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <h6>Please select Exchange or Return from the dropdown as per your request</h6>
                                       <select class="form-control intRetExcSelect" id="retExcSelect" name="retExcSelect">
    <!-- <option value="">Return/Exchange</option> -->
     <option value="exchange">Exchange</option>
    <option value="return">Return</option>
    
</select>
                                    </div>
                                </div>
        
      <div class="col-12 returns col-md-12" style="display: none;">
    <div class="custom_radio">
        <div class="row">
            <!-- First row: 3 options -->
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="canOrd1" name="typeAdd" value="Ordered by Mistake" checked>
                    <label for="canOrd1" class="mb-0">Ordered by Mistake</label>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="canOrd2" name="typeAdd" value="Product Damaged or Defective">
                    <label for="canOrd2" class="mb-0">Product Damaged or Defective</label>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="canOrd3" name="typeAdd" value="Product Looks Different from Image">
                    <label for="canOrd3" class="mb-0">Product Looks Different from Image</label>
                </div>
            </div>

            <!-- Second row: 2 options -->
            <div class="col-md-6 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="canOrd4" name="typeAdd" value="Quality Not as Expected">
                    <label for="canOrd4" class="mb-0">Quality Not as Expected</label>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="canOrd5" name="typeAdd" value="Late Delivery - No Longer Needed">
                    <label for="canOrd5" class="mb-0">Late Delivery - No Longer Needed</label>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-12 exchange col-md-12" style="display: block;">
    <div class="custom_radio">
        <div class="row">
            <!-- First row: 3 options -->
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="exOrd1" name="typeAdd" value="Received Wrong Product">
                    <label for="exOrd1" class="mb-0">Received Wrong Product</label>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="exOrd2" name="typeAdd" value="Product Damaged or Defective">
                    <label for="exOrd2" class="mb-0">Product Damaged or Defective</label>
                </div>
            </div>
            <div class="col-md-4 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="exOrd3" name="typeAdd" value="Wrong Size or Dimensions">
                    <label for="exOrd3" class="mb-0">Wrong Size or Dimensions</label>
                </div>
            </div>

            <!-- Second row: 2 options -->
            <div class="col-md-6 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="exOrd4" name="typeAdd" value="Received Incomplete Product">
                    <label for="exOrd4" class="mb-0">Received Incomplete Product</label>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-2">
                <div class="form-check pl-0">
                    <input type="radio" id="exOrd5" name="typeAdd" value="Need a Different Variant">
                    <label for="exOrd5" class="mb-0">Need a Different Variant</label>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const returnButtons = document.querySelectorAll('.btn-RetExcOrd');

    returnButtons.forEach(button => {
        button.addEventListener('click', function () {
            const product = JSON.parse(this.getAttribute('data-product'));
            const orderId = this.getAttribute('data-order-id');

            // Set image
            document.querySelector('#retExcModal img').src = `storage/images/${product.thumbnail_image}`;

            // Set product name
            document.querySelector('#retExcModal .proHead').textContent = product.name;

            // Set price
            document.querySelector('#retExcModal .priceMain').textContent = `Rs. ${product.listed_price}`;
            document.querySelector('#retExcModal .priceStrike').textContent = `Rs. ${product.variant_mrp}`;

            // Set quantity
            document.querySelector('#retExcModal .proQty').textContent = `Quantity: ${product.qty}`;

            // Set hidden input values
            document.querySelector('#retExcModal #order_id').value = orderId;
            document.querySelector('#retExcModal #product_id').value = product.product_id;
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelButtons = document.querySelectorAll('.btnOrdCan');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            const product = JSON.parse(this.getAttribute('data-product'));
            const orderId = this.getAttribute('data-order-id');

            // Set image
            document.querySelector('#cancellationModal img').src = `storage/images/${product.thumbnail_image}`;

            // Set product name
            document.querySelector('#cancellationModal .proHead').textContent = product.name;

            // Set price
            document.querySelector('#cancellationModal .priceMain').textContent = `Rs. ${product.listed_price}`;
            document.querySelector('#cancellationModal .priceStrike').textContent = `Rs. ${product.variant_mrp}`;

            // Set quantity
            document.querySelector('#cancellationModal .proQty').textContent = `Quantity: ${product.qty}`;

            // Set hidden input values if needed
            document.querySelector('#cancellationModal #order_id').value = orderId;
            document.querySelector('#cancellationModal #product_id').value = product.product_id;
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reviewButtons = document.querySelectorAll('.btn-revProd');

    reviewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const product = JSON.parse(this.getAttribute('data-product'));
            const orderId = this.getAttribute('data-order-id');

            // Set image
            document.querySelector('#revProdModal img').src = `storage/images/${product.thumbnail_image}`;

            // Set product name
            document.querySelector('#revProdModal .proHead').textContent = product.name;

            // Set price
            document.querySelector('#revProdModal .priceMain').textContent = `Rs. ${product.listed_price}`;
            document.querySelector('#revProdModal .priceStrike').textContent = `Rs. ${product.variant_mrp}`;

            // Set quantity
            document.querySelector('#revProdModal .proQty').textContent = `Quantity: ${product.qty}`;

            // Set hidden input values
            document.querySelector('#revProdModal #rev_order_id').value = orderId;
            document.querySelector('#revProdModal #rev_product_id').value = product.product_id;
        });
    });
});
</script>

<script>
document.getElementById('retExcSelect').addEventListener('change', function() {
    const mode = this.value; // "return", "exchange" or ""
    const returnsDiv = document.querySelector('.returns');
    const exchangeDiv = document.querySelector('.exchange');

  
    returnsDiv.style.display = 'none';
    exchangeDiv.style.display = 'none';

   
    if (mode === 'return') {
        returnsDiv.style.display = 'block';
    } else if (mode === 'exchange') {
        exchangeDiv.style.display = 'block';
    }
});

</script>
✨ Explaination
                                
                                <div class="col-12 col-md-12 mt-4">
                                    <textarea class="form-control w-85" name="w3review" cols="50"></textarea>
                                </div>
                                <div class="col-12 col-md-12 mt-4">
                                    <label for="retExcProdImg-file-request" class="retExcProdImg">
                                        Enter
                                        <span>Upload imager or drop image in box.(Max image size 100 kb)</span>
                                    </label>
                                    <input type="file" id="retExcProdImg-file-request" name="images[]" multiple>
                                </div>
                                <div class="col-12 col-md-12 mt-4 d-flex">
                                    <button type="button" class="btn btn-canSubmit">Submit</button>
                                    <span class="canWarn">Eligible Amount for refund: Rs. 350<br>(Delivery fee not
                                        included)</span>
                                    <span class="canWarn ml-4">Delivery Charges may Apply !</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


 <div class="modal fade revProdModal" id="revProdModal" tabindex="-1" role="dialog"
        aria-labelledby="revProdModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-4">
                            <div class="right-itemWrapper right-itemWrapper-bbdRight">
                                <ul>
                                    <li>
                                    <input type="hidden" id="rev_order_id" name="order_id" value="">
                                    <input type="hidden" id="rev_product_id" name="product_id" value="">
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div class="pro-desc">
                                            <img src="" class="img-fluid" alt="pro-img">
                                            <div class="ml-3">
                                                <h6 class="proHead"></h6>
                                                <span class="proType">Product Type</span>
                                                <h4>
                                                    <span class="priceMain" style="text-decoration: none;"></span>
                                                    <span class="priceStrike" style="text-decoration: line-through;"></span>
                                                </h4>
                                                <p class="proQty"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="d-flex align-items-center justify-content-start">
                                        <img src="assets/images/icons/product-return.png" class="img-fluid" alt="pro-return" />Review Your Product
                                    </p>
                                </li>                             
                                  </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-8">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <label class="revprodLbl">Review Product</label>
                                    <div class="ratingWrapper">
                                        <span>Rate:</span>
                                        <div class="star-rating">
                                            <input type="radio" id="1-star" name="rating" value="1" />
                                            <label for="1-star" class="star">&#9733;</label>
                                            <input type="radio" id="2-stars" name="rating" value="2" />
                                            <label for="2-stars" class="star">&#9733;</label>
                                            <input type="radio" id="3-stars" name="rating" value="3" />
                                            <label for="3-stars" class="star">&#9733;</label>
                                            <input type="radio" id="4-stars" name="rating" value="4" />
                                            <label for="4-stars" class="star">&#9733;</label>
                                            <input type="radio" id="5-stars" name="rating" value="5" />
                                            <label for="5-stars" class="star">&#9733;</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <p class="badr-radioHead">Experience</p>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd1" name="typeAdd" value="Product Quality"><label
                                                    for="canOrd1" class="mb-0">Product Quality</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd2" name="typeAdd" value="Value for Money"><label for="canOrd2"
                                                    class="mb-0">Value for Money</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd3" name="typeAdd" value="Packaging & Delivery"><label
                                                    for="canOrd3" class="mb-0">Packaging & Delivery</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd4" name="typeAdd" value="Ease of Installation/Assembly"><label for="canOrd4"
                                                    class="mb-0">Ease of Installation/Assembly</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd5" name="typeAdd" value="Size & Fit Accuracy" checked><label
                                                    for="canOrd5" class="mb-0">Size & Fit Accuracy</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd6" name="typeAdd" value="Design & Aestheticst"><label for="canOrd6"
                                                    class="mb-0">Design & Aestheticst</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mt-4">
                                    <textarea class="form-control w-85" name="w3reviews" cols="50"></textarea>
                                </div>
                                <div class="col-12 col-md-12 mt-4">
                                    <label for="retExcProdImg-file-request" class="retExcProdImg">
                                        Enter
                                        <span>Upload imager or drop image in box.(Max image size 100 kb)</span>
                                    </label>
                                   <input type="file" id="retExcProdImg-file-request" class="file-upload" name="images_s[]" multiple>


                                </div>
                                <div class="col-12 col-md-12 mt-4 d-flex">
                                    <button type="button" class="btn submit_btn btn-canSubmit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade trackOrderModal" id="trackorderModal" tabindex="-1" role="dialog"
        aria-labelledby="trackorderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <h2>Order ID</h2>
                            <p class="subhead">#1000270</p>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="wizard">
                                <div class="steps">
                                    <div class="step active" data-step="1">
                                        <h6>Order Placed</h6>
                                        <p>09:28 PM | 27-Nov-2025</p>
                                        <span></span>
                                    </div>
                                    <div class="step" data-step="2">
                                        <h6>Order Dispatched</h6>
                                        <p>09:28 PM | 27-Nov-2025</p>
                                        <span></span>
                                    </div>
                                    <div class="step" data-step="3">
                                        <h6>Out for delivery</h6>
                                        <p>09:28 PM | 27-Nov-2025</p>
                                        <span></span>
                                    </div>
                                    <div class="step" data-step="4">
                                        <h6>Delivered</h6>
                                        <p>09:28 PM | 27-Nov-2025</p>
                                        <span></span>
                                    </div>
                                </div>
                                <!-- <div class="trackOrdButtons">
                                    <button id="prev" class="btn btn-primary" disabled>Previous</button>
                                    <button id="next" class="btn btn-primary">Next</button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="returnStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content p-4">
      <div class="modal-header">
        <h5 class="modal-title">Return Status</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="statusStepsContainer"></div>
    </div>
  </div>
</div>





<script>
document.getElementsByClassName('btn-canSubmit')[0].addEventListener('click', function () {
    const return_reason = document.querySelector('input[name="typeAdd"]:checked')?.value || '';
    const return_remarks = document.querySelector('textarea[name="w3review"]').value;
    const order_id = document.querySelector('input[name="order_id"]').value;
    const product_id = document.querySelector('input[name="product_id"]').value;
    const status = document.querySelector('#retExcSelect').value;
    const imageInput = document.querySelector('input[name="images[]"]');
    const files = imageInput.files;
     
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('product_id', product_id);
    formData.append('order_id', order_id);
    formData.append('reason', return_reason);
    formData.append('remarks', return_remarks);
    formData.append('status', status);

    // Append all selected files
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }

    $.ajax({
        url: "{{ route('return_req') }}",
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from setting content type
        success: function (response) {
           if (response.status == true) {
    document.querySelector('.retStatu').style.display = 'block';
    document.querySelector('.retExt').style.display = 'none';
}

            console.log("Request Successful:", response);
            alert("Request processed successfully.");
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Error processing request.");
        }
    });
});
// ratings


document.querySelector('.submit_btn').addEventListener('click', function () {

    const review_reason = document.querySelector('input[name="typeAdds"]:checked')?.value || '';
    const rating = document.querySelector('input[name="rating"]:checked')?.value || '';
    const review_remarks = document.querySelector('textarea[name="w3reviews"]').value;
    const order_id = document.querySelector('input[name="order_id"]').value;
    const product_id = document.querySelector('input[name="product_id"]').value;
    const imageInput = document.querySelector('input[name="images_s[]"]');
    const files = imageInput.files;
     
     //console.log(rating);

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('product_id', product_id);
    formData.append('order_id', order_id);
    formData.append('reason', review_reason);
    formData.append('comment', review_remarks);
    formData.append('rating',rating);

    // Append all selected files
    for (let i = 0; i < files.length; i++) {
        formData.append('fileUpload[]', files[i]);
    }

    $.ajax({
        url: "{{ route('review_submit') }}",
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from setting content type
        success: function (response) {
            console.log("Request Successful:", response);
            alert("Review processed successfully.");
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Error processing request.");
        }
    });
});

</script>

<script>
document.querySelectorAll('.retStatu').forEach(button => {
    button.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');
        console.log(orderId);

        fetch("{{ route('status_return') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                renderSteps(response.statuses, response.current.status,response.current);
                $('#returnStatusModal').modal('show');
            } else {
                alert('No return status found.');
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error processing request.");
        });
    });
});
function renderSteps(statuses, currentStatus, retu) {
    const container = document.getElementById('statusStepsContainer');
    container.innerHTML = '';

    let statusReached = false;

    // 1. Status Flow
    const statusFlow = document.createElement('div');
    statusFlow.style.marginBottom = '20px';

    statuses.forEach((status, index) => {
        let completed = !statusReached;
        if (status === currentStatus) statusReached = true;

        const color = completed ? 'green' : 'gray';
        const icon = completed ? '✔' : '○';

        const step = document.createElement('span');
        step.style.marginRight = '15px';
        step.innerHTML = `
            <span style="color:${color}; font-weight:bold;">${icon}</span>
            <span style="margin-left:5px;">${status}</span>
        `;
        statusFlow.appendChild(step);

        if (index < statuses.length - 1) {
            const arrow = document.createElement('span');
            arrow.innerHTML = `<span style="margin:0 10px;">→</span>`;
            statusFlow.appendChild(arrow);
        }
    });

    container.appendChild(statusFlow);

    // 2. Reason
    const reasonRow = document.createElement('div');
    reasonRow.innerHTML = `<strong>Reason:</strong> ${retu.refund_reason || 'N/A'}`;
    reasonRow.style.marginBottom = '10px';
    container.appendChild(reasonRow);

    // 3. Remarks
    const remarkRow = document.createElement('div');
    remarkRow.innerHTML = `<strong>Remark:</strong> ${retu.refund_remarks || 'N/A'}`;
    remarkRow.style.marginBottom = '10px';
    container.appendChild(remarkRow);

    // 4. Images
    let images = retu.images;
    if (typeof images === 'string') {
        try {
            images = JSON.parse(images);
        } catch (e) {
            images = [];
        }
    }

    if (Array.isArray(images) && images.length > 0) {
        const imgRow = document.createElement('div');
        imgRow.innerHTML = `<strong>Images:</strong><br>`;
        images.forEach(imgUrl => {
            const img = document.createElement('img');
            img.src = 'storage/refund/' + imgUrl;
            img.style.width = '100px';
            img.style.marginRight = '10px';
            img.style.marginTop = '5px';
            img.style.border = '1px solid #ccc';
            img.style.borderRadius = '5px';
            imgRow.appendChild(img);
        });
        container.appendChild(imgRow);
    } else {
        const noImg = document.createElement('div');
        noImg.innerHTML = `<strong>Images:</strong> None`;
        container.appendChild(noImg);
    }

    // 5. Cancel Button at Bottom-Right
    const btnWrapper = document.createElement('div');
    btnWrapper.style.display = 'flex';
    btnWrapper.style.justifyContent = 'flex-end';
    btnWrapper.style.marginTop = '20px';

    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.setAttribute('data-id', retu.order_id);
    cancelBtn.className = 'btn retCancel btn-RetExcOrd';
    cancelBtn.textContent = 'Cancel Return';



    // ✅ Attach event listener BEFORE appending
cancelBtn.addEventListener('click', function () {
    const orderId = this.getAttribute('data-id');
    console.log('hello ' + orderId);

    fetch("{{ route('delete_return') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(res => res.json())
    .then(response => {
       alert(response.message);
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error processing request.");
    });
});

    btnWrapper.appendChild(cancelBtn);
    container.appendChild(btnWrapper);
}








</script>





@endsection