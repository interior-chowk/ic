
@extends('layouts.back-end.common_seller_1')

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{asset('asset/css/seller-custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/responsive.css') }}">
<section class="c-India-first-w">
    <div class="c-India-first-left">
        <div class="c-India-first-left-in">
            <img src="{{ asset('asset/img/bag-design.png') }}" alt="" data-aos="fade-down" data-aos-duration="800" >
            <div class="c-India-first-left-in-btn mt-4">
              <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank" class="c-downlod-btn" >
                            <span class="c-downlod-btn-left" data-aos="zoom-in" data-aos-duration="800" >Download APP</span>
                            <span class="c-downlod-btn-right" data-aos="flip-up" data-aos-duration="800" >Now</span>
                      </a>
            </div>
        </div>
    </div>
    <div class="c-India-first-right">
        <div class="c-India-first-right-in">
            <h2 data-aos="flip-up" data-aos-duration="800" data-aos-delay="800">India's first <span>dedicated</span> <strong>Marketplace</strong>
                
            </h2>
            <br>
            <h3 data-aos="flip-left" data-aos-duration="800" data-aos-delay="900"><dfn> for </dfn> HOME <strong>INTER<b>I</b>OR</strong> <span>BUYER’S</span></h3>
        </div>
    </div>
</section>
<section class="c-discover-w">
    <div class="c-discover-con" data-aos="fade-right" data-aos-duration="800" data-aos-delay="200">
        <div class="c-discover-con-in" >
              <p>Discover a marketplace crafted exclusively for home interior enthusiasts at InteriorChowk. Unveil a world of curated excellence where every product tells a story of quality, style, and functionality. Our dedicated focus on home interior essentials ensures that you, the discerning buyer, experience a seamless journey in finding everything you need for your dream living spaces.</p>
        </div>
    </div>
</section>
<section class="c-comprehensive-w">
    <div class="c-comprehensive-in">
        <div class="c-comprehensive-con">
            <ul>
                <li data-aos="fade-right" data-aos-duration="800" data-aos-delay="200">
                    <h3>Comprehensive <strong>Selection</strong></h3>
                    <p>From exquisite home decor and premium furnishing to elegant furniture, lighting solutions, and top-notch hardware – InteriorChowk is your one-stop destination for all things home interior. We take pride in offering a diverse array of products, including safety & security solutions, high-quality paints, and the latest in kitchenware & bathware.</p>
                </li>
                <li data-aos="fade-right" data-aos-duration="800" data-aos-delay="400">
                    <h3>Convenient <strong>Shopping</strong></h3>
                    <p>Navigate our user-friendly platform with ease, discovering a seamless shopping experience tailored to your specific needs. InteriorChowk brings the joy back to online shopping, allowing you to transform your living spaces effortlessly.</p>
                </li>
                <li data-aos="fade-right" data-aos-duration="800" data-aos-delay="600">
                    <h3>End-to-End  <strong>Solutions</strong></h3>
                    <p>More than just a marketplace, InteriorChowk provides end-to-end solutions for your home interior projects. From raw materials to finished products, we've got you covered, making your interior design and decor journey hassle-free.</p>
                </li>
            </ul>
        </div>
    </div>
</section>
<section class="c-define-w">
  <div class="c-define-diwali" >
      <img src="img/diwali.png" alt="">
  </div>
  <div class="c-define-light" >
      <img src="{{ asset('asset/img/light.png') }}" data-aos="fade-down" data-aos-duration="800" >
  </div>
  <div class="c-define-box">
      <h2  >Redefine your <strong>home interior</strong> </h2>
      <h3  >experience with a <span>  marketplace</span></h3>
      <h4  >  dedicated <span>  exclusively</span> <span> to you. </span></h4>
      <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank" class="c-downlod-btn">
            <span class="c-downlod-btn-left "  >Download APP</span>
            <span class="c-downlod-btn-right "  >Now</span>
      </a>
  </div>
</section>

@endsection
