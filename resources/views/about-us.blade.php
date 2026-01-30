@extends('layouts.back-end.common_seller_1')

@section('content')
    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/responsive.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

    <!-- About Section -->
    <section class="c-about-w" id="about-section">
        <div class="c-about-in">
            <div class="c-about-con">
                <h2 data-aos="fade-up" data-aos-duration="500">About Us</h2>
                <p data-aos="fade-up" data-aos-duration="1000">
                    Welcome to <span class="c-inte-text">Interior<strong>Chowk</strong></span>, the ultimate marketplace
                    catering to all your home interior needs! Our mobile app is designed to effortlessly connect home
                    interior buyers with a diverse range of service providers, skilled workers, and reliable suppliers.
                    As a subsidiary of <strong>"<a href="https://sohaminfratech.com/" target="_blank"
                            style="color:lightblue">Soham Infratech</a>"</strong>,
                    a reputable company with a longstanding presence in the industry since 2012, InteriorChowk brings years
                    of expertise and a commitment to quality to every interaction.
                </p>
                <p data-aos="fade-up" data-aos-duration="1000">
                    Whether you're embarking on a full-scale home renovation or simply looking to spruce up a room,
                    InteriorChowk
                    is your go-to destination for all things interior. From conceptualizing stunning interior designs to
                    sourcing
                    high-quality furniture and fixtures, our platform offers a comprehensive solution tailored to your
                    specific
                    requirements.
                </p>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="c-why-chose-w">
        <div class="c-why-chose-left">
            <div class="c-why-chose-left-in">
                <div class="c-why-chose-left-slide">
                    <div class="c-why-chose-left-slide-list">
                        <div class="c-why-chose-left-slide-list-in">
                            <p>At <span class="c-inte-text">Interior<strong>Chowk</strong></span>,</p>
                            <p>we're committed to empowering homeowners to transform their living spaces into personalized
                                havens that reflect their unique style and preferences.
                            </p>
                        </div>
                    </div>
                    <!-- Slick dots will be generated automatically -->
                </div>

                <div class="c-why-chose-left-other">
                    <p>
                        Join us on this journey, and let's turn your interior design dreams into reality together!
                    </p>
                    <a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app" target="_blank"
                        class="c-downlod-btn">
                        <span class="c-downlod-btn-left">Download APP</span>
                        <span class="c-downlod-btn-right">Now</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="c-why-chose-right">
            <div class="c-why-chose-right-in">
                <h2>With us, you can:</h2>
                <ul>
                    <li data-aos="fade-top" data-aos-duration="800" data-aos-delay="500">
                        Explore a diverse marketplace: Discover an extensive range of products and services curated to meet
                        every
                        aspect of your home interior project, all in one convenient location.
                    </li>
                    <li data-aos="fade-top" data-aos-duration="800" data-aos-delay="600">
                        Connect with trusted professionals: Access a network of skilled service providers and workers vetted
                        for their
                        expertise and reliability, ensuring peace of mind throughout your project.
                    </li>
                    <li data-aos="fade-top" data-aos-duration="800" data-aos-delay="700">
                        Source premium materials: Partner with reputable suppliers offering top-notch materials and
                        furnishings to
                        elevate the aesthetic and functionality of your living space.
                    </li>
                    <li data-aos="fade-top" data-aos-duration="800" data-aos-delay="800">
                        Experience seamless transactions: Enjoy a user-friendly interface and streamlined processes that
                        make browsing,
                        booking, and purchasing hassle-free and efficient.
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="c-consider-w-video">
        <div class="c-consider-in">
            <h2 class="mb-5 text-white" data-aos="fade" data-aos-duration="800">
                Welcome to Interior<span>Chowk</span>
            </h2>
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
                                <p class="text-white">
                                    Welcome to InteriorChowk Free branding & promotion* InteriorChowk is committed to
                                    supporting your growth
                                </p>
                                <p class="text-white">
                                    Join us on this journey, and let's turn your interior design dreams into reality
                                    together!
                                </p>
                                <p class="text-white">
                                    India's first dedicated marketplace for home interior buyer‘s where a multitude of
                                    sellers, interior designers,
                                    architects, contractors, workers and many more..
                                </p>
                                <h3 class="text-white">presence in the competitive market.</h3>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-left: auto;">
                            <div class="c-consider-box" data-aos="fade" data-aos-duration="800" data-aos-delay="300">
                                <iframe width="100%" height="280"
                                    src="https://www.youtube.com/embed/SpsiQwxOrKw?loop=1&playlist=SpsiQwxOrKw"
                                    frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Slick slider
            $('.c-why-chose-left-slide-list').slick({
                dots: true,
                arrows: false
            });

            // Initialize AOS
            AOS.init({
                duration: 800,
                once: true
            });
        });
    </script>
@endsection
