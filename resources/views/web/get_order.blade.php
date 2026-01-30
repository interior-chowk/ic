@extends('layouts.back-end.common_seller_1')

@section('content')
    <link rel="stylesheet" href="{{ asset('public/website/assets/css/billing.css') }}">
    <link rel="stylesheet" href="{{ asset('public/website/assets/css/step-wizard.css') }}">

    <main class="main">
        <div class="page-content">
            <div class="consent-para">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div id="orderNotiTxt">
                                <div class="article">
                                    <p>Hey! Remember, InteriorChowk or it’s team will never ask you for financial</p>
                                    <p class="moretext"> details or payment for any contest you’ve won. If you receive such
                                        request, stay alert and
                                        don’t share sensitive information through any medium. Stay secure, shop smart, and
                                        elevate your space!</p>
                                </div>
                                <a class="moreless-button" href="#">Read more</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row mt-5 recViewWrapper">
                    <div class="col-12 col-sm-12 col-md-8">
                        <div class="row background-opacity">
                            <div class="col-12 col-sm-12 col-md-7">
                                <div class="bbdLeftWrapper">
                                    <div class="prdDtlWrap">
                                        <span>
                                            <h4>Order ID</h4>
                                            <p>#{{ $order_id }}</p>
                                        </span>
                                    </div>
                                    @foreach ($orders as $order)
                                        <div class="shippingWrap">
                                            <div class="mb-3">
                                                <h3>Shipping to :</h3>
                                                <h6>{{ $order->shipping_address_data->contact_person_name }}</h6>
                                                <p>{{ $order->shipping_address_data->address }},{{ $order->shipping_address_data->landmark }},
                                                    {{ $order->shipping_address_data->city }} -
                                                    {{ $order->shipping_address_data->zip }},<br>
                                                    {{ $order->shipping_address_data->phone }}
                                                </p>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-5">
                                <div class="bbdLeftWrapper">
                                    <div class="prdDtlWrap">
                                        <span class="text-center">
                                            <h4>Date & Time</h4>
                                            <p> {{ $order->created_at->timezone('Asia/Kolkata')->format('h:i A | d-F-Y') }}
                                            </p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12">
                                <div class="dwnInvWrap">
                                    <h5>Billing Summary</h5>
                                    <a href="{{ route('generate-invoice', $order_id) }}"><button
                                            class="btn btn-dwnInvoice">Download invoice</button></a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12">
                                <div class="billSummContent">
                                    <h4>Sub Total</h4>
                                    <p>₹ {{ $order['total_variant_mrp'] }}</p>
                                </div>
                                <div class="billSummContent">
                                    <h4>Bag Savings</h4>
                                    <p>- ₹ {{ $order['total_variant_mrp'] - $order['total_listed_price'] }} </p>
                                </div>
                                <hr>
                                <div class="billSummContent">
                                    <h4>Coupon & Voucher</h4>
                                    <p> - ₹ {{ number_format(abs($order->discount_amount), 2) }}</p>
                                </div>

                                <div class="billSummContent">
                                    <h4>Delivery Charge</h4>
                                    <p>
                                        @if (empty($order->shipping_cost) || $order->shipping_cost == 0)
                                            <span style="color:#3f9339;">Free</span> <strike> ₹
                                                {{ $order->shipping_cost_amt }}</strike>
                                        @else
                                            ₹ {{ $order->shipping_cost }}
                                        @endif
                                    </p>
                                </div>
                                <div class="billSummContent">
                                    <h4>Paid By Wallet</h4>
                                    <p> ₹{{ $order->wallet_deduction }}</p>
                                </div>
                                <hr>
                                <div class="billSummContent">
                                    <h4>Total Amount</h4>
                                    <p>₹{{ $order->total_listed_price + $order->shipping_cost + $order->discount_amount - $order->wallet_deduction }}
                                    </p>
                                </div>

                                <hr class="mb-3">
                                <div class="billSummContent">
                                    <h4>Payment status</h4>
                                    <label>
                                        @if ($order->payment_method === 'cash_on_delivery')
                                            <span class="spCod">COD</span>
                                        @else
                                            <span class="spPaid">Paid</span>
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12">
                                <button type="button" class="btn btnOrdCan btnOrdCan1">
                                    <a href="https://app.shipyaari.com/tracking?trackingNo={{ $order->awbs }}"
                                        target="_blank" style="color: #ffffff;">
                                        Track Order
                                    </a>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4">
                        <div class="right-itemWrapper right-itemWrapper-bbdRight">
                            <h1 class="billBefRightHead">Ordered Products</h1>
                            <ul>
                                @foreach ($orders as $order)
                                    {{-- @dd($order); --}}
                                    @foreach ($order->sku_product as $product)
                                        @php
                                            $images = json_decode($product->image, true);

                                        @endphp
                                        <li>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div class="pro-desc">
                                                    <img 
                                                    {{-- src="{{ asset('storage/app/public/images/' . $images[0]) }}" --}}
                                                    src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($images[0] ?? 'default.jpg', '/') }}"

                                                        class="img-fluid" alt="pro-img">
                                                    <div class="ml-3" style="margin-top: -18px;">
                                                        <h6 class="proHead">
                                                            {{ strlen($product->name) > 30 ? substr($product->name, 0, 30) . '...' : $product->name }}
                                                        </h6>

                                                        <h4>₹
                                                            {{ $product->listed_price }}<span>₹{{ $product->variant_mrp }}</span>
                                                        </h4>
                                                        <p>Quantity:{{ $product->qty }}</p>
                                                    </div>
                                                </div>
                                                <div>
                                                </div>
                                            </div>
                                        </li>
                                        @if ($order->current_status == 'NOT PICKED' || $order->current_status == 'BOOKED')
                                            @if ($order->order_return_id == null)
                                                <button type="button" class="btn btnOrdCan btn-RetExcOrd"
                                                    data-toggle="modal" data-target="#cancellationModal"
                                                    data-product='@json($product)'
                                                    data-order-id="{{ $order->id }}">
                                                    Cancel Order
                                                </button>
                                            @else
                                                <button type="button" class="btn retStatu btn-RetExcOrd"
                                                    data-toggle="modal" data-target="#returnStatusModal"
                                                    data-product='@json($product)'
                                                    data-order-id="{{ $order->id }}">
                                                    Return Status
                                                </button>
                                            @endif
                                        @elseif($order->current_status == 'DELIVERED')
                                            @php
                                                $delivered_at = $order->delivered_at; // <-- use DB field

                                                $return_window_days = $product->Return_days;

                                                $can_return = false;

                                                if ($delivered_at) {
                                                    $last_return_date = \Carbon\Carbon::parse($delivered_at)->addDays(
                                                        $return_window_days,
                                                    );
                                                    $can_return = \Carbon\Carbon::now()->lte($last_return_date);
                                                }
                                            @endphp

                                            @if ($can_return)
                                                <p class="d-flex align-items-center justify-content-start"><img
                                                        src="{{ asset('storage/app/public/images/Product_Return.png') }}"
                                                        class="img-fluid" alt="pro-return" />{{ $product->Return_days }}
                                                    Days Return</p>

                                                @php
                                                    $return = App\Model\RefundRequest::where(
                                                        'order_id',
                                                        $order->id,
                                                    )->first();
                                                @endphp
                                                @if ($return)
                                                    <button type="button" class="btn retStatu btn-RetExcOrd"
                                                        data-toggle="modal" data-target="#returnStatusModal"
                                                        data-product='@json($product)'
                                                        data-order-id="{{ $order->id }}">
                                                        Return Status
                                                    </button>
                                                @else
                                                    <button type="button" class="btn retExt btn-RetExcOrd"
                                                        data-toggle="modal" data-target="#retExcModal"
                                                        data-product='@json($product)'
                                                        data-order-id="{{ $order->id }}">
                                                        Return/Exchange
                                                    </button>
                                                @endif
                                            @endif
                                            @php
                                                $review = App\Model\Review::where('customer_id', auth()->user()->id)
                                                    ->where('order_id', $order->id)
                                                    ->first();
                                            @endphp
                                            @if ($review)
                                                <span>{{ $review->comment }}</span>
                                                <div class="star-rating-display">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">
                                                            &#9733;
                                                        </span>
                                                    @endfor
                                                </div>
                                                <style>
                                                    .star-rating-display {
                                                        display: flex;
                                                        gap: 4px;
                                                    }

                                                    .star-rating-display .star {
                                                        font-size: 22px;
                                                        color: #ccc;
                                                    }

                                                    .star-rating-display .star.filled {
                                                        color: gold;
                                                    }
                                                </style>
                                            @else
                                                <button type="button" class="btn btn-revProd" data-toggle="modal"
                                                    data-target="#revProdModal" data-product='@json($product)'
                                                    data-order-id-review="{{ $order->id }}">
                                                    Review Product
                                                </button>
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End .main -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('.btnOrdCan');

            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = JSON.parse(this.getAttribute('data-product'));
                    const orderId = this.getAttribute('data-order-id');
                    document.querySelector('#cancellationModal img').src =
                        `storage/app/public/images/${product.thumbnail_image}`;
                    document.querySelector('#cancellationModal .proHead').textContent = product
                        .name;
                    document.querySelector('#cancellationModal .priceMain').textContent =
                        `₹ ${product.listed_price}`;
                    document.querySelector('#cancellationModal .priceStrike').textContent =
                        `₹ ${product.variant_mrp}`;
                    document.querySelector('#cancellationModal .proQty').textContent =
                        `Quantity: ${product.qty}`;
                    document.querySelector('#cancellationModal #order_id').value = orderId;
                    document.querySelector('#cancellationModal #product_id').value = product
                        .product_id;
                });
            });
        });
    </script>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.hoverIntent.min.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/superfish.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/bootstrap-input-spinner.js"></script>
    <script src="assets/js/jquery.elevateZoom.min.js"></script>
    <script src="assets/js/bootstrap-input-spinner.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://use.fontawesome.com/e9084ed560.js"></script>

    <script>
        // Tabs Toggler
        (function($) {
            const $tabLink = $('#tabs-section .tab-link');
            const $tabBody = $('#tabs-section .tab-body');
            let timerOpacity;

            const init = () => {
                $tabLink.off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    window.clearTimeout(timerOpacity);
                    $tabLink.removeClass('active');
                    $tabBody.removeClass('active');
                    $tabBody.removeClass('active-content');
                    $(this).addClass('active');
                    $($(this).attr('href')).addClass('active');
                    $(".tab-head-m").hide();
                    $(".bg-texture").hide();
                    timerOpacity = setTimeout(() => {
                        $($(this).attr('href')).addClass('active-content');
                    }, 50); // You can adjust the delay if needed
                });
            };
            $(function() {
                init();
            });
        }(jQuery));
    </script>

    <script>
        jQuery(".profile-menu").click(function() {
            if (jQuery(".bg-texture").is(":hidden")) {
                jQuery(".tab-head-m").show();
                jQuery(".bg-texture").show();
                jQuery('.tab-head-m').toggle('slide', {
                    direction: 'left'
                }, 1000);
            } else {
                jQuery(".bg-texture").hide();
                jQuery('.tab-head-m').toggle('slide', {
                    direction: 'left'
                }, 1000);
            }
        });
    </script>

    <div class="modal fade cancelModal" id="cancellationModal" tabindex="-1" role="dialog"
        aria-labelledby="cancellationModalLabel" aria-hidden="true">
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
                                                    <h4>
                                                        <span class="priceMain" style="text-decoration: none;"></span>
                                                        <span class="priceStrike"
                                                            style="text-decoration: line-through;"></span>
                                                    </h4>
                                                    <p class="proQty"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="d-flex align-items-center justify-content-start">
                                            <img src="assets/images/icons/product-return.png" class="img-fluid"
                                                alt="pro-return" />5 Days Return
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-8">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <label class="intCancel">Initiate Cancellation</label>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd1" name="typeAdd"
                                                    value="Ordered by Mistake" checked><label for="canOrd1"
                                                    class="mb-0">Ordered by Mistake</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd2" name="typeAdd"
                                                    value="Found a Better Price Elsewhere"><label for="canOrd2"
                                                    class="mb-0">Found a Better Price Elsewhere</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd3" name="typeAdd"
                                                    value="Delivery Time is Too Long" checked><label for="canOrd3"
                                                    class="mb-0">Delivery Time is Too Long</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd4" name="typeAdd"
                                                    value="Changed My Mind"><label for="canOrd4" class="mb-0">Changed
                                                    My Mind</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="custom_radio">
                                        <div class="form-group position-relative selected">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd5" name="typeAdd"
                                                    value="Incorrect Product Details" checked><label for="canOrd5"
                                                    class="mb-0">Incorrect Product Details</label>
                                            </div>
                                        </div>

                                        <div class="form-group position-relative">
                                            <div class="form-check pl-0">
                                                <input type="radio" id="canOrd6" name="typeAdd"
                                                    value="Need to Change Address/Details"><label for="canOrd6"
                                                    class="mb-0">Need to Change Address/Details</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mt-4">
                                    <textarea class="form-control w-85" name="w3review" cols="50"></textarea>
                                </div>
                                <div class="col-12 col-md-12 mt-4 d-flex">
                                    <button type="button" class="btn btn-canSubmit">Submit</button>
                                    <span class="canWarn">Eligible Amount for refund: ₹ 350<br>(Delivery fee not
                                        included)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementsByClassName('btn-canSubmit')[0].addEventListener('click', function() {

            cancellation_reason = document.querySelector('input[name="typeAdd"]:checked')?.value || 0;
            cancellation_remarks = document.querySelector('textarea[name="w3review"]').value;
            order_id = document.querySelector('input[name="order_id"]').value;
            product_id = document.querySelector('input[name="product_id"]').value;
            console.log("Reason:", cancellation_reason);
            console.log("Remarks:", cancellation_remarks);
            console.log("product_id:", product_id);
            console.log("order_id:", order_id);

            $.ajax({
                url: "{{ route('order_cancel') }}",
                type: 'POST',
                data: {
                    product_id: product_id,
                    order_id: order_id,
                    reason: cancellation_reason,
                    remarks: cancellation_remarks,
                    _token: '{{ csrf_token() }}' // Laravel CSRF
                },
                success: function(response) {
                    console.log("Order Cancelled:", response);
                    alert("Order cancelled successfully.");
                    location.reload();
                },
                error: function(xhr) {
                    console.error("Error:", xhr);
                    alert("Error cancelling order.");
                }
            });
        });
    </script>

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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-group input {
            position: relative;
            z-index: 10;
        }

        .form-group label {
            position: relative;
            z-index: 10;
        }

        .form-group::before,
        .form-group::after {
            pointer-events: none;
        }
    </style>
    <!-- review -->

    <div class="modal fade revProdModal" id="revProdModal" tabindex="-1" role="dialog"
        aria-labelledby="revProdModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="reviewForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-4">
                                <div class="right-itemWrapper right-itemWrapper-bbdRight">
                                    <ul>
                                        <li>
                                            <input type="hidden" id="order_id_review" name="order_id_review"
                                                value="">
                                            <input type="hidden" id="product_id_review" name="product_id_review"
                                                value="">

                                            <div class="d-flex align-items-end justify-content-between">
                                                <div class="pro-desc">
                                                    <img src="" class="img-fluid" alt="pro-img">
                                                    <div class="ml-3">
                                                        <h6 class="proHead"></h6>
                                                        <span class="proType">Product Type</span>
                                                        <h4>
                                                            <span class="priceMain"></span>
                                                            <span class="priceStrike"></span>
                                                        </h4>
                                                        <p class="proQty"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="d-flex align-items-center justify-content-start">
                                                <img src="/public/website/assets/images/icons/product-return.png"
                                                    class="img-fluid" alt="pro-return" />Review Your Product
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
                                            <div class="star-rating" style="flex-direction:row-reverse;">
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

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd1" name="typeAdd[]"
                                                    value="Product Quality">
                                                <label for="canOrd1">Product Quality</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd2" name="typeAdd[]"
                                                    value="Value for Money">
                                                <label for="canOrd2">Value for Money</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd3" name="typeAdd[]"
                                                    value="Packaging & Delivery">
                                                <label for="canOrd3">Packaging & Delivery</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd4" name="typeAdd[]"
                                                    value="Ease of Installation/Assembly">
                                                <label for="canOrd4">Ease of Installation/Assembly</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd5" name="typeAdd[]"
                                                    value="Size & Fit Accuracy">
                                                <label for="canOrd5">Size & Fit Accuracy</label>
                                            </div>

                                            <div class="form-group">
                                                <input type="checkbox" id="canOrd6" name="typeAdd[]"
                                                    value="Design & Aesthetics">
                                                <label for="canOrd6">Design & Aesthetics</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 mt-4">
                                        <textarea class="form-control w-85" name="review_remark" cols="50"></textarea>
                                    </div>

                                    <div class="col-12 col-md-12 mt-4">
                                        <div class="proRevImgUploadWrapper">
                                            <label for="retExcProdImg-file-request" class="retExcProdImg">
                                                <span class="uplImgProRevBtn">Upload Image</span>
                                                <span>Upload image or drop image in box.(Max image size 100 kb)</span>
                                            </label>
                                            <input type="file" id="retExcProdImg-file-request" class="file-upload"
                                                name="images_s[]" multiple>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 mt-4 d-flex">
                                        <!-- ✅ FIXED TYPE -->
                                        <button type="submit" class="btn submit_btn btn-canSubmit">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Return exchange Modal -->
    <div class="modal fade retExcModal" id="retExcModal" tabindex="-1" role="dialog"
        aria-labelledby="retExcModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-4">
                            <div class="right-itemWrapper right-itemWrapper-bbdRight">
                                <ul>
                                    <li>
                                        <input type="hidden" id="order_id_return" name="order_id_return"
                                            value="">
                                        <input type="hidden" id="product_id_return" name="product_id_return"
                                            value="">
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div class="pro-desc">
                                                <img src="" class="img-fluid" alt="pro-img">
                                                <div class="ml-3">
                                                    <h6 class="proHead"></h6>
                                                    <span class="proType">Product Type</span>
                                                    <h4>
                                                        <span class="priceMain" style="text-decoration: none;"></span>
                                                        <span class="priceStrike"
                                                            style="text-decoration: line-through;"></span>
                                                    </h4>
                                                    <p class="proQty"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="d-flex align-items-center justify-content-start">
                                            <img src="assets/images/icons/product-return.png" class="img-fluid"
                                                alt="pro-return" />5 Days Return
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
                                        <select class="form-control intRetExcSelect" id="retExcSelect"
                                            name="retExcSelect">
                                            <option value="" selected disabled>Return / Exchange</option>
                                            <option value="return">Return</option>
                                            <option value="exchange">Exchange</option>
                                        </select>
                                        <small class="text-danger error-retExcSelect"></small>
                                    </div>
                                </div>

                                <div class="col-12 returns col-md-12" style="display: none;">
                                    <div class="custom_radio">
                                        <div class="row">
                                            <!-- First row: 3 options -->
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="canOrds1" name="typeAdd"
                                                        value="Ordered by Mistake">
                                                    <label for="canOrds1" class="mb-0">Ordered by Mistake</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="canOrds2" name="typeAdd"
                                                        value="Product Damaged or Defective">
                                                    <label for="canOrds2" class="mb-0">Product Damaged or
                                                        Defective</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="canOrds3" name="typeAdd"
                                                        value="Product Looks Different from Image">
                                                    <label for="canOrds3" class="mb-0">Product Looks Different from
                                                        Image</label>
                                                </div>
                                            </div>

                                            <div class="col-6 col-sm-6 col-md-6 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="canOrds4" name="typeAdd"
                                                        value="Quality Not as Expected">
                                                    <label for="canOrds4" class="mb-0">Quality Not as Expected</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-6 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="canOrds5" name="typeAdd"
                                                        value="Late Delivery - No Longer Needed">
                                                    <label for="canOrds5" class="mb-0">Late Delivery - No Longer
                                                        Needed</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 exchange col-md-12" style="display: none;">
                                    <div class="custom_radio">
                                        <div class="row">
                                            <!-- First row: 3 options -->
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="exOrd1" name="typeAdd"
                                                        value="Received Wrong Product">
                                                    <label for="exOrd1" class="mb-0">Received Wrong Product</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="exOrd2" name="typeAdd"
                                                        value="Product Damaged or Defective">
                                                    <label for="exOrd2" class="mb-0">Product Damaged or
                                                        Defective</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-4 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="exOrd3" name="typeAdd"
                                                        value="Wrong Size or Dimensions">
                                                    <label for="exOrd3" class="mb-0">Wrong Size or Dimensions</label>
                                                </div>
                                            </div>

                                            <!-- Second row: 2 options -->
                                            <div class="col-6 col-sm-6 col-md-6 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="exOrd4" name="typeAdd"
                                                        value="Received Incomplete Product">
                                                    <label for="exOrd4" class="mb-0">Received Incomplete
                                                        Product</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-6 mb-2">
                                                <div class="form-check pl-0">
                                                    <input type="radio" id="exOrd5" name="typeAdd"
                                                        value="Need a Different Variant">
                                                    <label for="exOrd5" class="mb-0">Need a Different Variant</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-12 mt-4">
                                    <textarea id="remarksField" name="w3review" class="form-control w-85"></textarea>
                                    <small class="text-danger error-remarks"></small>
                                </div>
                                <div class="col-12 col-md-12 mt-4">
                                    <div class="proRevImgUploadWrapper">
                                        <label for="retExcProdImg-file-request" class="retExcProdImg">
                                            <span class="uplImgProRevBtn">Upload Image</span>
                                            <span>Upload image or drop image in box.(Max image size 100 kb)</span>
                                        </label>
                                        <input type="file" id="retExcProdImg-file-request" name="images[]" multiple>
                                        <small class="text-danger error-images"></small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 mt-4 d-flex retPopupBtnWrap">
                                    <button type="button" class="btn  retExch  btn-canSubmit">Submit</button>
                                    <span class="canWarn">Eligible Amount for refund: ₹
                                        {{ $order->amount + $order->discount_amount - $order->shipping_cost_amt }}<br>(Delivery
                                        fee not
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
        document.addEventListener('DOMContentLoaded', function() {
            const reviewButtons = document.querySelectorAll('.btn-revProd');

            reviewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = JSON.parse(this.getAttribute('data-product'));
                    const orderId = this.getAttribute('data-order-id');

                    // Set image
                    document.querySelector('#revProdModal img').src =
                        `storage/app/public/images/${product.thumbnail_image}`;

                    // Set product name
                    document.querySelector('#revProdModal .proHead').textContent = product.name;

                    // Set price
                    document.querySelector('#revProdModal .priceMain').textContent =
                        `₹ ${product.listed_price}`;
                    document.querySelector('#revProdModal .priceStrike').textContent =
                        `₹ ${product.variant_mrp}`;

                    // Set quantity
                    document.querySelector('#revProdModal .proQty').textContent =
                        `Quantity: ${product.qty}`;

                    // Set hidden input values
                    document.querySelector('#revProdModal #rev_order_id').value = orderId;
                    document.querySelector('#revProdModal #rev_product_id').value = product
                        .product_id;
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const returnButtons = document.querySelectorAll('.btn-RetExcOrd');

            returnButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = JSON.parse(this.getAttribute('data-product'));
                    const orderId = this.getAttribute('data-order-id');

                    // Set image
                    document.querySelector('#retExcModal img').src =
                        `storage/app/public/images/${product.thumbnail_image}`;

                    // Set product name
                    document.querySelector('#retExcModal .proHead').textContent = product.name;

                    // Set price
                    document.querySelector('#retExcModal .priceMain').textContent =
                        `₹ ${product.listed_price}`;
                    document.querySelector('#retExcModal .priceStrike').textContent =
                        `₹ ${product.variant_mrp}`;

                    // Set quantity
                    document.querySelector('#retExcModal .proQty').textContent =
                        `Quantity: ${product.qty}`;

                    // Set hidden input values
                    document.querySelector('#retExcModal #order_id_return').value = orderId;
                    document.querySelector('#retExcModal #product_id_return').value = product
                        .product_id;
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('.btnOrdCan');

            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = JSON.parse(this.getAttribute('data-product'));
                    const orderId = this.getAttribute('data-order-id');

                    // Set image
                    document.querySelector('#cancellationModal img').src =
                        `storage/app/public/images/${product.thumbnail_image}`;

                    // Set product name
                    document.querySelector('#cancellationModal .proHead').textContent = product
                        .name;

                    // Set price
                    document.querySelector('#cancellationModal .priceMain').textContent =
                        `₹ ${product.listed_price}`;
                    document.querySelector('#cancellationModal .priceStrike').textContent =
                        `₹ ${product.variant_mrp}`;

                    // Set quantity
                    document.querySelector('#cancellationModal .proQty').textContent =
                        `Quantity: ${product.qty}`;

                    // Set hidden input values if needed
                    document.querySelector('#cancellationModal #order_id').value = orderId;
                    document.querySelector('#cancellationModal #product_id').value = product
                        .product_id;
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

    <script>
        $('.retExch').on('click', function() {

            $('.text-danger').text("");

            let valid = true;

            let status = $('#retExcSelect').val();
            if (!status) {
                $('.error-retExcSelect').text("Please select return or exchange.");
                valid = false;
            }

            let reason = $('input[name="typeAdd"]:checked').val();
            if (!reason) {
                $('.error-typeAdd').text("Please select a reason.");
                valid = false;
            }

            let remarks = $('#remarksField').val().trim();
            if (remarks === "") {
                $('.error-remarks').text("Remarks cannot be empty.");
                valid = false;
            }

            let files = $('input[name="images[]"]')[0].files;

            if (files.length === 0) {
                $('.error-images').text("Please upload at least 1 image.");
                valid = false;
            } else {
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > 100 * 1024) {
                        $('.error-images').text("Each image must be less than 100 KB.");
                        valid = false;
                        break;
                    }
                }
            }

            // --- Hidden fields ---
            let order_id = $('input[name="order_id_return"]').val();
            let product_id = $('input[name="product_id_return"]').val();

            if (!order_id || !product_id) {
                alert("Order or Product ID missing!");
                valid = false;
            }

            // STOP FORM IF VALIDATION FAILS
            if (!valid) return;

            // -----------------------------
            // 🚀 PROCESS AJAX AFTER SUCCESS
            // -----------------------------

            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('product_id', product_id);
            formData.append('order_id', order_id);
            formData.append('reason', reason);
            formData.append('remarks', remarks);
            formData.append('status', status);

            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            $.ajax({
                url: "{{ route('return_req') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == true) {
                        location.reload();
                    }
                    alert("Request processed successfully.");
                },
                error: function(xhr) {
                    alert("Error processing request.");
                }
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $(document).on('click', '.btn-revProd', function() {

                let orderId = $(this).data('order-id-review');
                let product = $(this).data('product');

                console.log("Order:", orderId);
                console.log("Product:", product);

                // ✅ Hidden inputs set
                $('input[name="order_id_review"]').val(orderId);
                $('input[name="product_id_review"]').val(product.id);

                $('.revProdModal .proHead').text(product.name ?? '');
                $('.revProdModal .priceMain').text('₹' + (product.price ?? ''));

                let imgPath = "";

                if (product) {
                    imgPath = `storage/app/public/images/${product.thumbnail_image}`;
                } else if (product) {
                    imgPath = product.thumbnail_image; // if already full URL
                }

                $('#reviewProductImg').attr('src', imgPath);

                // ✅ Reset form
                $('#reviewForm')[0].reset();
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $(document).on('submit', '#reviewForm', function(e) {
                e.preventDefault();

                const rating = document.querySelector('input[name="rating"]:checked')?.value || '';
                const review_remark = document.querySelector('textarea[name="review_remark"]').value;

                let order_id_review = $('input[name="order_id_review"]').val();
                let product_id_review = $('input[name="product_id_review"]').val();

                const imageInput = document.querySelector('input[name="images_s[]"]');
                const files = imageInput.files;

                if (!order_id_review || !product_id_review) {
                    alert("Order ID or Product ID missing!");
                    return;
                }

                let experience = [];
                document.querySelectorAll('input[name="typeAdd[]"]:checked').forEach(el => {
                    experience.push(el.value);
                });

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('product_id_review', product_id_review);
                formData.append('order_id_review', order_id_review);
                formData.append('review_remark', review_remark);
                formData.append('rating', rating);

                experience.forEach(val => {
                    formData.append('typeAdd[]', val);
                });

                for (let i = 0; i < files.length; i++) {
                    formData.append('images_s[]', files[i]);
                }

                $.ajax({
                    url: "{{ route('review_submit') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },

                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        alert(xhr.responseJSON?.message || "Validation failed!");
                    }
                });
            });

        });
    </script>

    <script>
        function openReviewModal(productId, orderId) {
            document.getElementById('rev_product_id').value = productId;
            document.getElementById('rev_order_id').value = orderId;
            $('#revProdModal').modal('show');
        }
    </script>

    <script>
        document.querySelectorAll('.retStatu').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                console.log(orderId);

                fetch("{{ route('status_return') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            renderSteps(response.statuses, response.currentStatus, response.current,
                                response.refund_amount);
                            // renderSteps(
                            //     response.statuses,
                            //     response.currentStatus, {
                            //         ...response.current,
                            //         refund_amount: response.refund_amount
                            //     }
                            // );

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

        function renderSteps(statuses, currentStatus, retu, refundAmount) {
            const container = document.getElementById('statusStepsContainer');
            container.innerHTML = '';

            let statusReached = false;

            const statusFlow = document.createElement('div');
            statusFlow.style.marginBottom = '20px';

            statuses.forEach((status, index) => {
                let completed = !statusReached;

                if (status === currentStatus) {
                    statusReached = true;
                }

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

            const reasonRow = document.createElement('div');
            reasonRow.innerHTML = `<strong>Reason:</strong> ${retu.refund_reason || 'N/A'}`;
            reasonRow.style.marginBottom = '10px';
            container.appendChild(reasonRow);

            const remarkRow = document.createElement('div');
            remarkRow.innerHTML = `<strong>Remark:</strong> ${retu.refund_remarks || 'N/A'}`;
            remarkRow.style.marginBottom = '10px';
            container.appendChild(remarkRow);

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
                    img.src = 'storage/app/public/refund/' + imgUrl;
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

            // ✅ Refund Amount (FIXED VARIABLE NAME)
            if (currentStatus == 'Request Accepted') {
                const refundAmountDiv = document.createElement('div');
                refundAmountDiv.innerHTML = `<strong>Refundable Amount:</strong> ${refundAmount ?? 'N/A'}`;
                refundAmountDiv.style.marginBottom = '10px';
                container.appendChild(refundAmountDiv);
            }

            // ✅ Cancel Button
            const btnWrapper = document.createElement('div');
            btnWrapper.style.display = 'flex';
            btnWrapper.style.justifyContent = 'flex-end';
            btnWrapper.style.marginTop = '20px';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.setAttribute('data-id', retu.order_id);
            cancelBtn.className = 'btn retCancel btn-RetExcOrd';
            cancelBtn.textContent = 'Cancel Return';

            cancelBtn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');

                fetch("{{ route('delete_return') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        alert(response.message);
                        location.reload();
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

    <script>
        $('.moreless-button').click(function() {
            $('.moretext').slideToggle();
            if ($('.moreless-button').text() == "Read more") {
                $(this).text("Read less")
            } else {
                $(this).text("Read more")
            }
        });
    </script>
@endsection
