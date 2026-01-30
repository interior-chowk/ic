@extends('layouts.back-end.common_seller_1')
@section('content')
<style>
.custom_radio .addTag {
    top: 12px !important;
}
</style>
<link rel="stylesheet" href="{{ asset('public/website/assets/css/billing.css') }}">
<main class="main">
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="mobileAddrWrapper">
                        <div class="addrMainWrap">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0">Primary Address</h6>
                                    <button type="button" class="btn edit-btn"><i class="fas fa-edit"></i></button>
                                </div>
                                <button type="button" class="btn btnEdit">Change Address</button>
                            </div>
                            <p>Vivek - 9859658741</p>
                            <p>Near sardar ji ki chakki, Milan vihar, MORADABAD - 244001, Uttar Pradesh</p>
                        </div>

                        <div class="addrMainWrap">
                            <h6 class="mb-1">Billing Address</h6>
                            <div class="custom_radio">
                                <div class="form-group position-relative border-bottom-0 selected">
                                    <div class="form-check pl-0">
                                        <input type="radio" name="shipAdd" id="shipAdd1" checked required>
                                        <label for="shipAdd1" class="mb-0">Same as Shipping address</label>
                                    </div>
                                </div>

                                <div class="form-group position-relative">
                                    <div class="form-check pl-0">
                                        <input type="radio" name="shipAdd" id="shipAdd2">
                                        <label for="shipAdd2" class="mb-0">Use a different billing address</label>
                                    </div>

                                    <!-- Dropdown Wrapper -->
                                    <div class="selAddWrappers mt-2" style="display:none;">
                                        <select class="form-control">
                                            <option value="">-- Select Address --</option>
                                            <option value="1">
                                                Vivek - New York
                                            </option>
                                            <option value="2">
                                                Anita - New Delhi
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="addrMainWrap">
                            <div class="form-group form-check d-flex align-items-center gstinChkBox mb-0">
                                <input type="checkbox" class="form-check-input" name="gstCheck">
                                <label class="form-check-label font-weight-bold ml-2" for="gstCheck">
                                    Use GSTIN details for this order
                                </label>
                            </div>
                        </div>
                        
                        <div class="addrMainWrap">
                            <div class="form-group form-check d-flex align-items-center mb-0">
                                <input type="checkbox" class="form-check-input" name="instant_delivery">
                                <label class="form-check-label font-weight-bold ml-2" for="instant_delivery">
                                    Instant Delivery — Get it in 4 hours!
                                </label>
                            </div>
                        </div>

                        <div class="addrMainWrap">
                            <div class="mobProDetails">
                                <h6 class="proHeading">Product Details</h6>
                               <div class="d-flex align-items-start">
                                 <img src="{{ asset('storage/app/public/images/0btyluPOSHE3bm3kqMVO2d9awVwlDZF5oYDOlZAp.jpg') }}"
                                    class="img-fluid" alt="pro-img">
                                <div class="ml-3">
                                    <a href="#">
                                        <p class="proName">
                                            Elegent table lamp for home and office inclui...
                                        </p>
                                    </a>
                                    <p>₹ 1900<span class="text-decoration-line-through">₹ 3000</span></p>
                                    <label class="offValue">
                                        25% off
                                    </label>
                                </div>
                               </div>
                            </div>
                            <div class="mobProExtDetails">
                                <p>Non Returnable</p>
                                <p>Quantity - 1</p>
                                <p>Size - Small</p>
                                <p>Colour - Blue</p>
                                <p>Variation - Blue</p>
                                <p>Delivered by - 20th Jan, 2025</p>
                            </div>
                        </div>

                        <div class="addrMainWrap">
                            <h6 class="proHeading">Product Details</h6>
                            <div class="offerCoupWrapper">
                                <div class="offCoupLeftWrap">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <img src="{{ asset('storage/app/public/images/discount_87.png') }}"
                                            class="img-fluid" alt="discount">
                                        <div>
                                            <h5>abs1234<span>Best offer for
                                                    you</span></h5>
                                            <p style="font-size: 14px">
                                                100% off</p>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btnApply"
                                            data-code="">Apply
                                            now</button>
                                        <p style="font-size: 11px">Valid till :
                                            28 jan 26
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <button class="btn viewOffBtn collapsed" type="button"
                                data-toggle="collapse" data-target="#collapseOne"
                                aria-expanded="false" aria-controls="collapseOne">
                                View More Coupon codes<i class="fa fa-arrow-right"
                                    aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-6">
                    <div class="mobileAddrWrapper">
                        <!-- edit address modal start here------------------------------------------------------  -->
                        <div class="modal fade" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="">
                                        <input type="hidden" name="">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Address</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <input type="text" name="contact_person_name" class="form-control"
                                                        placeholder="Enter Name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="phone" class="form-control"
                                                        placeholder="Phone No." required>
                                                </div>

                                                <div class="col-12">
                                                    <textarea name="address" class="form-control" placeholder="Address"
                                                        required></textarea>
                                                </div>

                                                <div class="col-12">
                                                    <input type="text" name="landmark" class="form-control"
                                                        placeholder="Landmark (optional)">
                                                </div>

                                                <div class="col-md-4">
                                                    <input type="text" name="zip" class="form-control"
                                                        placeholder="Pincode" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="city" class="form-control"
                                                        placeholder="City" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="state" class="form-control"
                                                        placeholder="State" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- edit address modal ended here------------------------------------------------------  -->

                        <div class="saveaddress" style="width: 50%; margin: auto;">
                            <img src="{{ asset('public/website/assets/images/nosavedaddress.webp') }}" alt="">
                        </div>

                        <div class="boxBor">
                            <h6>Billing Address</h6>
                            <div class="custom_radio">
                                <div class="form-group position-relative selected">
                                    <div class="form-check pl-0">
                                        <input type="radio" name="shipAdd" required>
                                        <label for="shipAdd1" class="mb-0">Same as Shipping
                                            Address</label>
                                    </div>
                                </div>

                                <div class="form-group position-relative">
                                    <div class="form-check pl-0">
                                        <input type="radio" name="shipAdd">
                                        <label for="shipAdd2" class="mb-0">Use a Different Billing
                                            Address</label>
                                    </div>

                                    <!-- Dropdown Wrapper -->
                                    <div class="selAddWrappers" style="display:none; margin-top:10px;">
                                        <select class="form-control">
                                            <option value="">-- Select Address --</option>
                                            <option value="hi">
                                                Vivek - New York
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Display Selected Address -->
                                    <div class="mt-3"
                                        style="display:none; border:1px solid #ddd; padding:10px; border-radius:5px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-none">
                            <div class="col-12 col-md-9">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Company Name">
                                </div>
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="GSTIN">
                                </div>
                            </div>
                        </div>
                        <div class="billAddWrapper paymentWrapper">
                            <h3>Payment</h3>
                            <p>All transactions are encrypted and secure</p>
                            <div class="custom_radio walletsss">
                                <div class="form-group walletss position-relative d-flex align-items-start" selected>
                                    <div class="form-check pl-0">
                                        <input type="radio" name="iscod" value="0" checked><label for="payWay1"
                                            class="mb-0">Razorpay Secure (UPI,
                                            Cards,
                                            Wallets, Netbanking)</label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between"
                                        style="margin-left: 2px;margin-top: -7px;">
                                        <img src="{{ asset('storage/app/public/images/upi.png') }}" class="img-fluid"
                                            alt="upi" style="margin-left: 3px;">
                                        <img src="{{ asset('storage/app/public/images/visa.png') }}" class="img-fluid"
                                            alt="visa">
                                        <img src="{{ asset('storage/app/public/images/mastercard.png') }}"
                                            class="img-fluid" alt="master">
                                        <img src="{{ asset('storage/app/public/images/rupay.png') }}" class="img-fluid"
                                            alt="rupay">
                                        <span style="margin-left: 3px;">+16</span>
                                    </div>
                                </div>
                                <div class="form-group wallets position-relative">
                                    <div class="form-check pl-0">
                                        <input type="radio" name="iscod" value="1"><label for="payWay2"
                                            class="mb-0">Cash
                                            on
                                            Delivery (COD)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="d-none d-md-block">
                            <button type="submit" class="btn btnPayNow" style="width:100%;">
                                <span class="btn-text">Pay Now</span>
                                <span class="btn-loader" style="display:none;">Processing...</span>
                            </button>

                        </form>
                        <div class="tab-pane fade p-0" role="tabpanel" aria-labelledby="pills-chngAdd-tab">
                            <div class="selAddWrapper">
                                <div class="custom_radio">
                                    <div class="form-group position-relative">
                                        <div class="form-check text-right">
                                            <input type="radio" name="" class="address">
                                            <label for=""></label>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <p class="mt-1">vivek - 7895465235
                                            </p>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis, commodi.</p>
                                        <span class="addTag addTagCol1">Home</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-0" role="tabpanel" aria-labelledby="pills-addNewAdd-tab">
                            <div class="addNewAddWrapper mt-2">
                                <h3>
                                    <button class="btn locBtn">Use My
                                        Location<i class="fa fa-map-marker" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <form>
                                    <div class="row">
                                        <!-- Contact Person -->
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="contact_person_name"
                                                    placeholder="Enter Name" required>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="contact"
                                                    placeholder="Phone No." required>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="col-12 col-md-12 mb-2">
                                            <textarea class="form-control w-100" name="address" cols="20"></textarea>
                                            <span class="text-danger"></span>
                                        </div>

                                        <!-- Landmark -->
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="landmark"
                                                    placeholder="Landmark (optional)">
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- Zip -->
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="zip" placeholder="Pincode"
                                                    required>
                                                <span class="text-danger"></span>

                                            </div>
                                        </div>

                                        <!-- City -->
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="city" placeholder="City">
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- State -->
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="state"
                                                    placeholder="State">
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- Address Type -->
                                        <div class="col-12 col-md-12">
                                            <div class="addTypeWrapper">
                                                <h5>Address Type</h5>
                                                <div class="custom_radio">
                                                    <div class="form-check">
                                                        <input type="radio" name="address_type" value="home" checked>
                                                        <label for="addtype1" class="mb-0">Home</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" name="address_type" value="work">
                                                        <label for="addtype2" class="mb-0">Work</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" name="address_type" value="other">
                                                        <label for="addtype3" class="mb-0">Other</label>
                                                    </div>
                                                </div>
                                                <span class="text-danger"></span>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-12 mt-3">
                                            <button class="btn btn-primary" type="submit">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="right-itemWrapper">
                        <ul>
                            <div class="scroll" style="overflow-y: scroll; position: relative; right: 0px;">
                                <li>
                                    <div class="prod-head">
                                        <h5>Product Details</h5>
                                    </div>
                                    <div class="pro-desc">
                                        <img src="{{ asset('storage/app/public/images/Product_Return.png') }}"
                                            class="img-fluid" alt="pro-img">
                                        <div class="ml-3">
                                            <a href="#">
                                                <h4 class="proHead">
                                                    Lamyyyyyyyyyy
                                                </h4>
                                            </a>
                                            <h4 class="subProHead">₹ 450<span>₹
                                                    890</span></h4>
                                            <label>
                                                off
                                            </label>
                                        </div>
                                    </div>
                                    <p class="d-flex align-items-center justify-content-start"><img
                                            src="{{ asset('storage/app/public/images/Product_Return.png') }}"
                                            class="img-fluid" alt="pro-return" />
                                        Delivery by - 31 Jan 2026
                                    </p>
                                </li>
                            </div>
                            <li>
                                <div
                                    class="offerCoupWrapper offerCoupWrapperboxShadw bg-white cashWrapper mt-2 d-flex ">
                                    <input type="checkbox" name="instant_delivery" value="">
                                    <h6 class="m-2">Instant Delivery — Get it in 4 hours! </h6>

                                </div>
                            </li>
                            <li class="offerListContent">
                                <div class="offerCoupWrapper">
                                    <div class="offCoupLeftWrap">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <img src="{{ asset('storage/app/public/images/discount_87.png') }}"
                                                class="img-fluid" alt="discount">
                                            <div>
                                                <h5>abc1234<span>Best offer for you</span></h5>
                                                <p class="offParaCnt">100% free product</p>
                                            </div>
                                        </div>
                                        <div class="appBtnAndValid">
                                            <button type="submit" class="btn btnApply" data-code="">Apply now</button>
                                            <button type="submit" class="btn btnApply" disabled>Apply
                                                now</button>
                                            <div><span style="color: red; font-size: 12px; margin-left: 5px;">(Min
                                                    purchase
                                                    ₹745)</span></div>
                                            <p>Valid till :
                                                28 jan 26
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion" id="accordionExample">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn viewOffBtn collapsed" type="button"
                                                    data-toggle="collapse" data-target="#collapseOne"
                                                    aria-expanded="false" aria-controls="collapseOne">
                                                    View More Coupon codes<i class="fa fa-arrow-right"
                                                        aria-hidden="true"></i>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                            data-parent="#accordionExample">
                                            <div class="card-body p-0">
                                                <div class="offerCoupWrapper">
                                                    <div class="offCoupLeftWrap">
                                                        <div class="d-flex align-items-start justify-content-between">
                                                            <img src="{{ asset('storage/app/public/images/discount_87.png') }}"
                                                                class="img-fluid" alt="discount">
                                                            <div>
                                                                <h5>abs1234<span>Best offer for
                                                                        you</span></h5>
                                                                <p style="font-size: 14px">
                                                                    100% off</p>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <button type="submit" class="btn btnApply"
                                                                data-code="">Apply
                                                                now</button>
                                                            <p style="font-size: 11px">Valid till :
                                                                28 jan 26
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="offerCoupWrapper offerCoupWrapperboxShadw bg-white cashWrapper mt-2 d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6>Use InteriorChowk Wallet cash</h6>
                                        <p>Available Balance: <span>₹47851</span></p>
                                    </div>
                                    <div>
                                        <button class="btn btnCash" disabled>Use Cash</button>
                                    </div>
                                    <div>
                                        <button class="btn btnCash">Use Cash</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="billBreakdownWrapper">
                            <h4>Total Bill Breakdown</h4>
                            <div class="amtBrkdownWrapper">
                                <div class="cillBrkCnt">
                                    <label>Bag Amount</label>
                                    <span class="d-flex">₹16857.00</span>
                                </div>
                                <div class="cillBrkCnt">
                                    <label>Bag Saving</label>
                                    <span class="d-flex">-₹4571 - 7584</span>
                                </div>
                                <hr>
                                <div class="cillBrkCnt">
                                    <label class="d-flex align-items-center justify-content-start">Coupon & Voucher<img
                                            src="{{ asset('storage/app/public/images/discount_87.png') }}"
                                            class="img-fluid" alt="voucher" /></label>
                                    <span class="d-flex">₹0.00</span>
                                </div>
                                <div class="cillBrkCnt">
                                    <label>Delivery Charge</label>
                                    <span class="d-flex">₹0.00</span>
                                </div>
                                <div class="cillBrkCnt">
                                    <label>Paid By Wallet</label>
                                    <span class="d-flex">₹ 0.00</span>
                                </div>
                                <hr>
                                <div class="cillBrkCnt">
                                    <label>Total Amount</label>
                                    <span class="d-flex">₹4571 - 7584</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12">
                    <form class="d-md-none">
                        <button type="submit" class="btn btnPayNow">Pay Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
</body>

</html>
@endsection