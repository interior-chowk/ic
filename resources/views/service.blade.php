@extends('layouts.back-end.common_seller_1')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/seller-custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/responsive.css') }}">

    <section class="c-service-w">
        <div class="c-service-in">
            <div class="c-service-con">
                <div class="c-service-con-img" data-aos="fade-down" data-aos-duration="800">
                    <h1>INTERIOR<span>CHOWK</span></h1>
                </div>
                <h2 data-aos="flip-up" data-aos-duration="800" data-aos-delay="200">SERVICES</h2>
                <h3 data-aos="flip-down" data-aos-duration="800" data-aos-delay="400"> <span>by</span> Architects
                    <strong>Interior Designers</strong></h3>
                <h4 data-aos="flip-left" data-aos-duration="800" data-aos-delay="400"> <span>&</span> Contractors</h4>
                <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank"
                    class="c-downlod-btn">
                    <span class="c-downlod-btn-left" data-aos="zoom-in" data-aos-duration="800">Download APP</span>
                    <span class="c-downlod-btn-right" data-aos="flip-up" data-aos-duration="800">Now</span>
                </a>
            </div>
        </div>
    </section>
    <section class="c-one-stop-w">
        <div class="c-one-stop-in">
            <h2 data-aos="fade" data-aos-duration="800">One stop solution</h2>
            <p data-aos="fade" data-aos-duration="800" data-aos-delay="200">Embark on a journey of transformation with our
                expert interior designing and architecture services. Our team of seasoned professionals is dedicated to
                turning your vision into reality. From conceptualization to execution, we are with you every step of the
                way. Experience the joy
                of a well-designed and thoughtfully crafted living space that reflects your personality and style.</p>
        </div>
    </section>
    <section class="c-architectural-w">
        <div class="c-architectural-left" data-aos="fade" data-aos-duration="800">
            <div class="c-architectural-left-in">
                <h2>Architectural Brilliance</h2>
                <p>Beyond being a marketplace, InteriorChowk proudly offers top-notch architecture services led by a
                    multitude of skilled architects. Transform your space with expert guidance and personalized design
                    solutions, ensuring your home becomes a masterpiece of architectural brilliance.</p>
            </div>
        </div>
        <div class="c-architectural-right" data-aos="fade" data-aos-duration="800" data-aos-delay="200"></div>
    </section>
    <section class="c-interior-w">
        <div class="c-interior-left" data-aos="fade" data-aos-duration="800"></div>
        <div class="c-interior-right" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
            <div class="c-interior-right-in">
                <h2> Interior Design <br> Excellence</h2>
                <p>Unleash the full potential of your home with our interior design services. Collaborate with a multitude
                    of talented interior designers, each bringing a unique perspective and expertise to craft spaces that
                    reflect your personality.</p>
            </div>
        </div>
    </section>
@endsection
