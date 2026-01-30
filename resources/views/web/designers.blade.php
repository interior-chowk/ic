@extends('layouts.back-end.common_seller_1')





@section('content')
    <style>
        img {
            max-width: 100% !important;
        }
    </style>
    <div class="container featured mt-4 pb-2">

        <center>

            <h2 class="title" style="text-align:center;">Top Interior Designers</h2>

        </center>

        <div class="d-flex flex-wrap justify-content-center" style="margin: 30px;">

            @foreach ($interior_designer as $designer)
            <div style="flex: 0 0 20%; max-width: 20%; padding: 10px; box-sizing: border-box;">
                <a href="{{ url('/interior_designers'.'/' . Str::slug($designer->name)) }}">
                    <div class="card text-center border-0"
                        style="box-shadow: 0 6px 12px rgba(0,0,0,0.1); transition: box-shadow 0.3s ease;"
                        onmouseover="this.style.boxShadow='none'"
                        onmouseout="this.style.boxShadow='0 6px 12px rgba(0,0,0,0.1)'">

                        <img src="{{ asset($designer->banner_image) }}" class="card-img-top topCon" alt="Designer Banner">

                        <div class="overlap_img">

                            <img src="{{ asset('storage/app/public/service-provider/profile/' . $designer->image) }}"
                                class="card-img-top" alt="Designer Profile"
                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">

                        </div>

                        <div class="card-body p-3">

                            <h5 class="card-title">{{ $designer->name ?? 'Unknown' }}</h5>

                            <p class="card-text text-dark"
                                style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">

                                {{ $designer->city ? str_replace(['[', ']', '"'], '', $designer->city) : 'Location not available' }}

                            </p>

                            <div class="ratings-container d-block">

                                <div class="ratings">

                                    <div class="ratings-val" style="width: 20%;"></div>

                                </div>

                                <span class="ratings-text text-dark d-block ml-0 mt-1">( 2 Reviews )</span>

                            </div>

                        </div>

                    </div>

                </a>
                </div>
            @endforeach

        </div>

    </div>











    <style>
        .widget-title1 {

            color: #878787 !important;

            font-weight: 500;

            font-size: 12px !important;

            letter-spacing: -.01em;

            margin-top: 0;

            margin-bottom: 1.9rem;

        }

        .widget-list1 {

            line-height: 2;

            font-size: 13px;

            font-weight: 500;

            color: #fff;

            display: block;

            font-weight: 400;

            font-size: 12px;

        }





        .banner video {

            display: block;

            max-width: none;

            width: 100%;

            height: auto;

            border-radius: 15px;

        }





        .header-3 .wishlist a {

            color: #000000 !important;

        }

        @media screen and (min-width: 992px) {

            .video-banner-bg {

                padding-top: 6rem;

                padding-bottom: 6rem;

            }

        }

        .header-3 .header-search-extended .form-control {

            border-top-right-radius: 3rem;

            border-bottom-right-radius: 3rem;

            padding-left: 0;

            height: 34px;

            padding: 1rem 2.4rem 1rem .5rem;

            background: #cce4f3;

            color: black;

        }

        .header-search-visible .header-search-wrapper {

            position: static;

            left: auto;

            right: auto;

            top: auto;

            margin-top: 0;

            display: flex;

            background: #baddf2;

        }

        .cart-dropdown .dropdown-toggle i {

            display: inline-block;

            margin-top: -3px;

            color: black;

        }



        .product {

            position: relative;

            margin-bottom: 1rem;

            transition: box-shadow .35s ease;

            background-color: #ffffff !important;

        }

        .product-title a {

            display: -webkit-box;

            -webkit-line-clamp: 1;
            /* Limit to 2 lines */

            -webkit-box-orient: vertical;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: normal;

            max-height: 3em;
            /* Adjust based on line height */

            line-height: 1.5em;
            /* Adjust line height for proper spacing */

        }



        .intro-slider-container,
        .intro-slide,
        .banner {

            background-color: transparent !important;

        }

        .header .container,
        .header .container-fluid {

            position: static !important;

            display: flex;

            align-items: center;

        }



        .btndgn {
            background: #ffc107;
            color: black;
            border: none;
            border-radius: 8px;
        }

        .header-3 .header-search-extended .btn {

            max-width: 40px;

            margin-left: 1rem;

            height: 46px;

            font-size: 2.2rem;

            background-color: transparent;

            color: #333;

            margin-top: -6px !important;

        }
    </style>
@endsection
