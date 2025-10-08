@extends('layouts.back-end.common_seller_1')

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{asset('asset/css/seller-custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/responsive.css') }}">

<section class="c-selling-journey">
    <div class="c-selling-journey-in">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 data-aos="fade" data-aos-duration="800">Begin Your <img src="{{ asset('asset/img/favicon-removebg-preview.png') }}"> <span>Selling Journey</span> </h2>
                    <p data-aos="fade" data-aos-duration="800" data-aos-delay="300"><strong>India's first dedicated marketplace</strong> for home Interior buyer’s where a multitude of buyer, interior designers, architects, contractors, workers and many more.</p>
                    <a href="{{ route('seller.auth.seller-registeration') }}" class="c-downlod-btn">
                        <span class="c-downlod-btn-left" data-aos="fade" data-aos-duration="800">start selling</span>
                        <span class="c-downlod-btn-right" data-aos="fade" data-aos-duration="800">Now</span>
                    </a>
                </div>
                <div class="col-md-5 ms-auto">
                    <div class="c-selling-journey-img" data-aos="" data-aos-duration="800" data-aos-delay="500">
                        <img src="{{ asset('asset/img/selling-journey-img.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="c-consider-w c-consider-w">
    <div class="c-consider-in">
        <h2 data-aos="fade" data-aos-duration="800"> Why consider Interior<span>Chowk</span> ?</h2>
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="row ">
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
                            <img src="{{ asset('asset/img/consider-icon-1.png') }}" alt="">
                            <h3> Selling at <span>0%</span> Commission*</h3>
                            <p> Sell your products with the added benefit of a 0% commission*</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="300">
                            <img src="{{ asset('asset/img/consider-icon-2.png') }}" alt="">
                            <h3> <span>Zero</span> waiting for payments </h3>
                            <p> Receive your payment instantly once we have received it from our payment gateway.</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="400">
                            <img src="{{ asset('asset/img/consider-icon-3.png') }}" alt="">
                            <h3> <span>Free </span>branding & promotion* </h3>
                            <p> InteriorChowk is committed to supporting your growth journey, and our free branding and promotion services aim to boost your presence in the competitive market. </p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="500">
                            <img src="{{ asset('asset/img/consider-icon-4.png') }}" alt="">
                            <h3> One stop <span>solution </span> </h3>
                            <p> InteriorChowk is not just an app; it's a platform where a multitude of architects, interior designers, and contractors actively recommend your products to their customers. </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="c-consider-w c-consider-slide-2">
    <div class="c-consider-in">

        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="row ">
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="100">
                            <img src="{{ asset('asset/img/consider-icon-5.png') }}" alt="">
                            <h3> Expand Sales <span>Nationally</span> </h3>
                            <p> We currently offer shipping services nationwide within PAN India, covering 19,000+ PIN codes, and collaborate with 25+ reliable courier partners.</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
                            <img src="{{ asset('asset/img/consider-icon-6.png') }}" alt="">
                            <h3> <span>Quick </span>support </h3>
                            <p> Our committed Seller Support Team is here to address all your queries and concerns.</p>
                        </div>
                    </div>
                    <div class="col-md-5" data-aos="fade" data-aos-duration="800" data-aos-delay="400">
                        <div class="c-consider-box">
                            <img src="{{ asset('asset/img/consider-icon-7.png') }}" alt="">
                            <h3> Seamless User <span>Interface </span> </h3>
                            <p> InteriorChowk Seller Panel. Designed with the seller's needs in mind, our platform offers an intuitive and effortlessly navigable interface. </p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="500">
                            <img src="{{ asset('asset/img/consider-icon-8.png') }}" alt="">
                            <h3> Buisness <span>Management </span> </h3>
                            <p> Dedicated Personnel to Assist Your Business on InteriorChowk who will help you on Account creation, product listing, Shipping, accounts related query etc. </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('seller.auth.seller-registeration') }}" class="c-downlod-btn">
                        <span class="c-downlod-btn-left">Start Selling</span>
                        <span class="c-downlod-btn-right">Now</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="c-start-selling-journey-w">
    <div class="c-start-selling-journey-in">
        <div class="container">
            <span class="d-flex">
                <h2 data-aos="fade" data-aos-duration="800"> How to start your selling journey?</h2>

                <a href="{{asset('storage/seller_guide/IC Seller’s Guide.pdf')}}" target="_blank" class="c-downlod-btn" style="margin-left: 12rem !important;">
                    <span> <img src="{{asset('storage/seller_guide/Seller guide tab.png')}}" alt="" width="250px" height="100px"></span>

                </a>
            </span>
            <div class="row">
                <div class="col-md-4">
                    <div class="c-start-selling-journey-box" data-aos="fade" data-aos-duration="800" data-aos-delay="300">
                        <img src="{{ asset('asset/img/selling-icon-1.png') }}" alt="">
                        <h3>Register your business</h3>
                        <p>Add your personal and business details including GST/PAN and active bank account.</p>
                        <span> <img src="{{ asset('asset/img/right-arrow-1.png') }}" alt=""></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="c-start-selling-journey-box" data-aos="fade" data-aos-duration="800" data-aos-delay="400">
                        <img src="{{ asset('asset/img/selling-icon-2.png') }}" alt="">
                        <h3>Product listing </h3>
                        <p>List your products by
                            providing complete details
                            including Brand, price,
                            features etc</p>
                        <span> <img src="{{ asset('asset/img/right-arrow-1.png') }}" alt=""></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="c-start-selling-journey-box" data-aos="fade" data-aos-duration="800" data-aos-delay="500">
                        <img src="{{ asset('asset/img/selling-icon-3.png') }}" alt="">
                        <h3>Order and shipping </h3>
                        <p>Get order from customer and shipped timely</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="c-start-selling-journey-bottom">
        <div class="c-selling-journey-tree">
            <img src="{{ asset('asset/img/tree-animation.gif') }}" alt="">
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="c-start-selling-journey-bottom-con">
                        <h4> Join our <br>Seller’s family </h4>
                        <div class="mt-4">
                            <a href="{{ route('seller.auth.seller-registeration') }}" class="c-downlod-btn">
                                <span class="c-downlod-btn-left" data-aos="" data-aos-duration="800">Start Selling</span>
                                <span class="c-downlod-btn-right" data-aos="" data-aos-duration="800">Now</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 ms-auto mt-auto">
                    <div class="c-start-selling-journey-bottom-img">
                        <span>
                            <img src="{{ asset('asset/img/bird.gif') }}">
                        </span>
                        <img class="selling-img-m" src="{{ asset('asset/img/selling-img-1.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-1"></div>
            </div>
        </div>
    </div>
</section>

@endsection