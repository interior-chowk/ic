@extends('layouts.back-end.common_seller_1')

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{asset('asset/css/seller-custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/responsive.css') }}">


<section class="c-solusation-w">
    <div class="c-solusation-left" data-aos="fade" data-aos-duration="800">
        <img src="{{ asset('asset/img/solution-left-bg.jpg') }}" alt="">
    </div>
    <div class="c-solusation-right" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
        <img src="{{ asset('asset/img/solution-right-bg.jpg') }}" alt="">
    </div>
</section>
<section class="c-about-product-w">
    <div class="c-about-product-con" data-aos="fade-down" data-aos-duration="800">
        <div class="container">
            <p><span class="c-inte-text">Interior<strong>Chowk</strong></span> is not just about products; it's about solutions. We understand that bringing your vision to life requires skilled hands and expertise. That's why we provide a platform to connect you with a range of skilled workers related to interior designing and construction. Whether you <strong> need a carpenter, painter, electrician, mason, plumber, POP mistri, Fabricator, or any other professional,</strong> InteriorChowk has got you covered. Our vetted and experienced workers ensure that your projects are completed with precision and excellence.</p>
        </div>
    </div>
    <div class="c-about-product-img" data-aos="fade" data-aos-duration="1200" data-aos-delay="200">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <img src="{{ asset('asset/img/about-product-bottom.png') }}" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

@endsection