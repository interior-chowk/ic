@extends('layouts.back-end.common_seller_1')


@section('content')

<div class="container featured mt-4 pb-2">
<center> <h2 class="title" style="text-align:center;">Top Brands</h2></center><!-- End .title -->



    <div class="row" style="margin-left:30px; margin-top:30px;margin-right:30px">
        @foreach($top_brands as $tb)
        <div class="col-lg-2" style="margin-top:10px;margin-bottum:10px;">
            <div class="product product-7" style="background:white!important; width: 150px; height: 150px; border-radius: 50%!important;">
                <figure class="product-media" style="width: 100%; height: 100%; overflow: hidden;">
                    <a href="{{ route('products_2') }}?brand_id={{ $tb->id }}">
                        <img src="{{ asset('storage/brand/' . $tb->image) }}" alt="Product image" class="product-image" 
                             style="width: 100%; height: 100%; object-fit: cover;aspect-ratio: 1 / 1; border-radius: 50%;">
                    </a>
                </figure><!-- End .product-media -->
            </div><!-- End .product -->
        </div>
        @endforeach
    </div><!-- End .row -->
</div><!-- End .container -->



<style>


.widget-title1{
color: #878787!important;
font-weight: 500;
font-size: 12px!important;
letter-spacing: -.01em;
margin-top: 0;
margin-bottom: 1.9rem;
}
.widget-list1{
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
    color: #000000!important;
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
	color:black;
}
.header-search-visible .header-search-wrapper {
    position: static;
    left: auto;
    right: auto;
    top: auto;
    margin-top: 0;
    display: flex
;
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
    -webkit-line-clamp: 1; /* Limit to 2 lines */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
    max-height: 3em; /* Adjust based on line height */
    line-height: 1.5em; /* Adjust line height for proper spacing */
}

.intro-slider-container, .intro-slide, .banner {
    background-color: transparent!important;
}
.header .container, .header .container-fluid {
    position: static!important;
    display: flex
;
    align-items: center;
}

.btndgn{
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
    margin-top: -6px!important;
}

figure {
    margin: 1.7rem;
}
</style>


@endsection