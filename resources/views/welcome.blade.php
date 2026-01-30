@extends('layouts.back-end.common_seller')

@section('content')
    <section class="c-banner-w">
        <div class="c-banner-in">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-6">
                        <div class="c-banner-con">
                            <h3 data-aos="flip-up" data-aos-duration="1000" data-aos-delay="300">WELCOME TO</h3>
                            <h1 data-aos="flip-up" data-aos-duration="1000" data-aos-delay="500">INTERIOR <span> CHOWK</span>
                            </h1>
                            <p data-aos="flip-up" data-aos-duration="1000" data-aos-delay="700"> <strong>India's first
                                    dedicated marketplace</strong> for home interior buyer‘s where a multitude of sellers,
                                interior designers, architects, contractors, workers and many more..
                            </p>
                            <h4>Download App Now</h4><br />
                            <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank"
                                class="c-downlod-btn">
                                <img src="{{ asset('public/asset/img/android.png') }}" width="150" />
                                <!--<span class="c-downlod-btn-left" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="300">Download Android App</span>-->
                                <!--<span class="c-downlod-btn-right" data-aos="flip-up" data-aos-duration="800" >Now</span>-->
                            </a>
                            <a href="https://apps.apple.com/app/interiorchowk/id6554003290" target="_blank"
                                class="c-downlod-btn">
                                <img src="{{ asset('public/asset/img/ios.png') }}" width="150" />
                                <!--<span class="c-downlod-btn-left" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="300">Download iOS App</span>-->
                                <!--<span class="c-downlod-btn-right" data-aos="flip-up" data-aos-duration="800" >Now</span>-->
                            </a>
                            <div class="c-leran-more-w">
                                <a href="#about-section" class="c-leran-more-btn">
                                    Learn more
                                    <img src="{{ asset('public/asset/img/down-arrow-new.png') }}" alt="" s>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="c-banner-img" data-aos="fade-up" data-aos-duration="1000">
                    <img src="{{ asset('public/asset/img/hand-mobile.png') }}" alt="">
                </div>

            </div>
        </div>
    </section>
    <section class="c-about-w" id="about-section">
        <div class="c-about-in">
            <div class="c-about-con">
                <h2 data-aos="zoom-in" data-aos-duration="500">About Us</h2>
                <p data-aos="fade-up" data-aos-duration="1000">Welcome to <span class="c-inte-text">
                        Interior<strong>Chowk</strong></span>, the ultimate
                    marketplace catering to all your home interior
                    needs! Our mobile app is designed to effortlessly
                    connect home interior buyers with a diverse
                    range of service providers, skilled workers, and
                    reliable suppliers. As a subsidiary of <strong>"<a href="https://sohaminfratech.com/" target="_blank"
                            style="color:lightblue">Soham
                            Infratech</a>" </strong> a reputable company with a
                    longstanding presence in the industry since 2012,
                    InteriorChowk brings years of expertise and a
                    commitment to quality to every interaction.</p>
                <p data-aos="fade-up" data-aos-duration="1000">Whether you're embarking on a full-scale home
                    renovation or simply looking to spruce up a room,
                    InteriorChowk is your go-to destination for all
                    things interior. From conceptualizing stunning
                    interior designs to sourcing high-quality furniture
                    and fixtures, our platform offers a comprehensive
                    solution tailored to your specific requirements.</p>
            </div>
        </div>
    </section>
    <section class="c-why-chose-w">
        <div class="c-why-chose-left">
            <div class="c-why-chose-left-in">
                <div class="c-why-chose-left-slide" data-aos="zoom-in" data-aos-duration="500">
                    <div class="c-why-chose-left-slide-list">
                        <div class="c-why-chose-left-slide-list-in">
                            <p>At <span class="c-inte-text">Interior<strong>Chowk</strong></span> ,</p>
                            <p> we're committed to empowering homeowners to transform their living
                                spaces into personalized havens that reflect their unique style and preferences.
                            </p>
                        </div>
                    </div>
                    <ul class="slick-dots" style="" role="tablist">
                        <li class="" aria-hidden="true" role="presentation" aria-selected="true"
                            aria-controls="navigation00" id="slick-slide00"><button type="button" data-role="none"
                                role="button" tabindex="0">1</button></li>
                        <li aria-hidden="true" role="presentation" aria-selected="false" aria-controls="navigation01"
                            id="slick-slide01" class="slick-active"><button type="button" data-role="none" role="button"
                                tabindex="0">2</button></li>
                        <li aria-hidden="false" role="presentation" aria-selected="false" aria-controls="navigation02"
                            id="slick-slide02"><button type="button" data-role="none" role="button"
                                tabindex="0">3</button></li>
                    </ul>
                </div>

                <div class="c-why-chose-left-other">
                    <p data-aos="zoom-in" data-aos-duration="500" data-aos-delay="500">
                        Join us on this journey,
                        and let's turn your
                        interior design dreams
                        into reality together!
                    </p>
                    <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank"
                        class="c-downlod-btn" data-aos="fade" data-aos-duration="500" data-aos-delay="650">
                        <span class="c-downlod-btn-left">Download APP</span>
                        <span class="c-downlod-btn-right">Now</span>
                    </a>
                </div>
            </div>

        </div>
        <div class="c-why-chose-right">
            <div class="c-why-chose-right-in">
                <h2 data-aos="zoom-in" data-aos-duration="800" data-aos-delay="500">With us, you can:</h2>
                <ul>
                    <li data-aos="fade-left" data-aos-duration="800" data-aos-delay="500">Explore a diverse marketplace:
                        Discover an extensive range of products and services
                        curated to meet every aspect of your home interior project, all in one convenient
                        location.</li>
                    <li data-aos="fade-left" data-aos-duration="800" data-aos-delay="600">Connect with trusted
                        professionals: Access a network of skilled service providers and
                        workers vetted for their expertise and reliability, ensuring peace of mind throughout
                        your project.</li>
                    <li data-aos="fade-left" data-aos-duration="800" data-aos-delay="700">Source premium materials:
                        Partner with reputable suppliers offering top-notch
                        materials and furnishings to elevate the aesthetic and functionality of your living space.</li>
                    <li data-aos="fade-left" data-aos-duration="800" data-aos-delay="800">Experience seamless
                        transactions: Enjoy a user-friendly interface and streamlined
                        processes that make browsing, booking, and purchasing hassle-free and efficient.</li>
                </ul>
            </div>
        </div>
    </section>
    <section class="c-consider-w-video">
        <div class="c-consider-in">
            <h2 class="mb-5 text-white" data-aos="fade" data-aos-duration="800"> Welcome to Interior<span>Chowk</span>
            </h2>

            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="row ">
                        <div class="col-md-6">
                            <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="200">

                                <p class="text-white">Welcome to InteriorChowk Free branding & promotion* InteriorChowk is
                                    committed to supporting your growth</p>
                                <p class="text-white">Join us on this journey, and let's turn your interior design dreams
                                    into reality together!</p>
                                <p class="text-white">India's first dedicated marketplace for home interior buyer‘s where a
                                    multitude of sellers, interior designers, architects, contractors, workers and many
                                    more..</p>
                                <h3 class="text-white">presence in the competitive market.</h3>
                            </div>
                        </div>
                        <div class="col-md-6 ml-10" style="margin-left: auto;">
                            <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="300">
                                <iframe width="100%" height="280"
                                    src="https://www.youtube.com/embed/SpsiQwxOrKw?si=1kb7LIhJyCOFxiKb?loop=1">
                                </iframe>
                                <!--  <video controls style="width: 100%; height: 100%;" data-aos="fade" data-aos-duration="800" data-aos-delay="200">-->
                                <!--<source src="https://youtu.be/" type="video/mp4">-->
                                <!--Your browser does not support the video tag.-->
                                </video>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
