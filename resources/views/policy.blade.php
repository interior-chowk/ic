@extends('layouts.back-end.common_seller')

@section('content')
    <section class="c-solusation-w">
        <div class="c-solusation-left" data-aos="fade" data-aos-duration="800">
            <!--<img src="{{ asset('public/asset/img/policy.jpg') }}" alt="">-->
        </div>
        <div class="c-solusation-right" data-aos="fade" data-aos-duration="800" data-aos-delay="200">
            <!-- <img src="{{ asset('public/asset/img/privacy-logo.jpg') }}" alt="">-->
        </div>
    </section>
    <section class="c-about-product-w">
        <div class="c-about-product-con" data-aos="fade-down" data-aos-duration="800">
            <div class="container mt-5">
                @if ($policy->type == 'refund-policy')
                    <?php
                    $content = json_decode($policy->value, true);
                    
                    $status = $content['status'];
                    $htmlContent = $content['content'];
                    
                    ?>
                    <p><span class="c-inte-text">Interior<strong>Chowk</strong></span>{!! $htmlContent !!}</p>
                @else
                    <p><span class="c-inte-text">Interior<strong>Chowk</strong></span>{!! $policy->value !!}</p>
                @endif
            </div>
        </div>
        <div class="c-about-product-img" data-aos="fade" data-aos-duration="1200" data-aos-delay="200">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
