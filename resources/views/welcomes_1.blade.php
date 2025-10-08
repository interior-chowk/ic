@extends('layouts.back-end.common_seller_1')
@section('content')
@push('style')
  <link rel="stylesheet" href="{{asset('website/assets/css/home.css')}}">
  <!-- Font Awesome 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://interiorchowk.com/#organization",
      "name": "InteriorChowk",
      "url": "https://interiorchowk.com",
      "logo": {
        "@type": "ImageObject",
        "url": "https://interiorchowk.com/public/website/assets/images/logoic.png",
        "width": 100,
        "height": 32
      },
      "sameAs": [
        "https://www.facebook.com/people/InteriorChowk/61554788270651/",
        "https://www.instagram.com/interiorchowk/",
        "https://www.linkedin.com/company/interiorchowk/",
        "https://www.youtube.com/channel/UCLXmVanINf5oL1gNVHpCmbQ/"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+91-9955680690",
        "contactType": "Customer Support",
        "email": "customersupport@interiorchowk.com",
        "availableLanguage": ["English", "Hindi"],
        "areaServed": "IN"
      },
      "description": "InteriorChowk offers premium home interior products online. Shop stylish furniture, decor, kitchenware, lighting & more. Trusted by homeowners across India.",
      "foundingDate": "2023",
      "founder": {
        "@type": "Person",
        "name": "Vivek Singh"
      },
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Greater Noida",
        "addressRegion": "Uttar Pradesh",
        "addressCountry": "IN"
      }
    },
    {
      "@type": "WebSite",
      "@id": "https://interiorchowk.com/#website",
      "url": "https://interiorchowk.com",
      "name": "InteriorChowk",
      "publisher": {
        "@id": "https://interiorchowk.com/#organization"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://interiorchowk.com/search?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
@endpush

<main class="main">
    <div class="intro-section pt-0 pb-3 mb-2">
        <div class="row">
            <div class="col-lg-12">
                <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section2 slider_desktop">
                    @foreach($main_banner as $banner)
                    <div class="intro-slide">
                        <figure class="slide-image">
                            <picture>
                                <source media="(max-width: 480px)" srcset="{{ asset('storage/banner/' . $banner->photo) }}">
                                <a href="{{  $banner->url  }}">
                                    <img src="{{ asset('storage/banner/' . $banner->photo) }}" alt="Banner Image">
                                </a>
                            </picture>
                        </figure>
                    </div>
                    @endforeach
                </div>

                 <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section2 slider_mobile">
                    @foreach($mobile_banner as $banner)
                    <div class="intro-slide">
                        <figure class="slide-image">
                            <picture>
                                <source media="(max-width: 480px)" srcset="{{ asset('storage/banner/' . $banner->photo) }}">
                                <a href="{{  $banner->url  }}">
                                    <img src="{{ asset('storage/banner/' . $banner->photo) }}" alt="Banner Image">
                                </a>
                            </picture>
                        </figure>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="section-3 card_design">
        <div class="slide_mob">
            <div class="container">
                <div class="row">
                    @foreach($categories as $ca)
                    <div class="product product-4 col-3">
                        <figure class="product-media">
                            <a href="{{ url('category/' . $ca->slug) }}">
                                <img src="{{ asset('storage/category/' . $ca->icon) }}" alt="Product image" class="product-image">
                            </a>
                        </figure>
                        <div class="product-footer">
                            <center>
                                <p>
                                    <a href="{{ url('category/' . $ca->slug) }}">{{ $ca->name }}</a>
                                </p>
                            </center>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="slider_desktop">
            <div class="container">
                <div class="row">
                    <div class="owl-carousel category-carousel owl-simple">
                        @foreach($categories as $ca)
                        <div class="product product-4">
                            <figure class="product-media">
                                <a href="{{ url('category/' . $ca->slug) }}">
                                    <img src="{{ asset('storage/category/' . $ca->icon) }}" alt="Product image" class="product-image">
                                </a>
                            </figure>
                            <div class="product-footer">
                                <center>
                                    <p>
                                        <a href="{{ url('category/' . $ca->slug) }}">{{ $ca->name }}</a>
                                    </p>
                                </center>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(isset($Service_Provider_Banner_3->photo))
    <div class="container custom_banner web-service-provider">
        <div class="row service-provider-banners">
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper tall-banner">
                    <a href="{{ $Service_Provider_Banner_3->url }}">
                        <img src="{{ asset('storage/banner/' . $Service_Provider_Banner_3->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper short-banner">
                    <a href="{{ $Service_Provider_Banner_1->url }}">
                        <img src="{{ asset('storage/banner/' . $Service_Provider_Banner_1->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper short-banner">
                    <a href="{{ $Service_Provider_Banner_2->url }}">
                        <img src="{{ asset('storage/banner/' . $Service_Provider_Banner_2->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="container mt-5 pt-5 custom_banner service-mobile-provider d-none">
        <div class="row service-provider-banners">
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper tall-banner">
                    <a href="{{ $Service_Provider_Banner_3->url }}">
                        <img src="{{ asset('storage/banner/' . $Mob_Provider_Banner_3->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper short-banner">
                    <a href="{{ $Service_Provider_Banner_1->url }}">
                        <img src="{{ asset('storage/banner/' . $Mob_Provider_Banner_1->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-4 short-banner">
                <div class="banner-wrapper short-banner">
                    <a href="{{ $Service_Provider_Banner_2->url }}">
                        <img src="{{ asset('storage/banner/' . $Mob_Provider_Banner_2->photo) }}" alt="Banner">
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($main_banner_2))
    <div class="intro-section pt-1 pb-3 mb-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mb-2 web-service-provider">
                    <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section3">
                        @foreach($main_banner_2 as $banner)
                        <div class="intro-slide">
                            <figure class="slide-image">
                                <picture>
                                    <source media="(max-width: 480px)" srcset="{{ asset('storage/banner/' . $banner->photo) }}">
                                    <a href="{{  $banner->url  }}">
                                        <img src="{{ asset('storage/banner/' . $banner->photo) }}" alt="Banner Image">
                                    </a>
                                </picture>
                            </figure>
                        </div>
                        @endforeach
                    </div>
                </div>
                 <div class="col-lg-12 mb-2 d-none service-mobile-provider">
                    <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section3 ">
                        @foreach($mob_main_banner_2 as $banner)
                        <div class="intro-slide">
                            <figure class="slide-image">
                                <picture>
                                    <source media="(max-width: 480px)" srcset="{{ asset('storage/banner/' . $banner->photo) }}">
                                    <a href="{{  $banner->url  }}">
                                        <img src="{{ asset('storage/banner/' . $banner->photo) }}" alt="Banner Image">
                                    </a>
                                </picture>
                            </figure>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @auth
            @if($recently_viewed->isNotEmpty())
                <div class="page-content">
                    <div class="recently-viewed-section">
                        <div class="container">
                            <div class="product-details-top">
                                <div class="heading heading-flex mb-3 pt-5">
                                    <div class="heading-left">
                                        <h2 class="title">Recently Items You've Viewed</h2>
                                    </div>
                                </div>

                                <div class="owl-carousel recently-viewed-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                    @foreach($recently_viewed as $item)
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            @if($item->sku_discount_type == 'percent' && $item->sku_discount > 0)
                                            <span class="product-label label-new">{{ round($item->sku_discount, 0) }}% off</span>
                                            @elseif($item->sku_discount_type == 'flat' && $item->sku_discount > 0)
                                            <span class="product-label label-new">₹{{ number_format($item->sku_discount, 0) }} off</span>
                                            @endif

                                            @if($item->free_delivery == 1)
                                            <span class="product-label product-label-two label-sale">Free Delivery</span>
                                            @endif

                                            <a href="{{ url('product/' . ($item->slug ?? '#')) }}">
                                                <img src="{{ !empty($item->thumbnail_image) 
                                                            ? asset('storage/images/' . $item->thumbnail_image) 
                                                            : asset('website/assets/images/products/product-placeholder.jpg') }}"
                                                    alt="{{ $item->name ?? 'Product' }}"
                                                    class="product-image">
                                            </a>
                                        </figure>

                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="#">Planters</a>
                                            </div>
                                            <h3 class="product-title">
                                                <a href="{{ url('product/' . ($item->slug ?? '#')) }}">
                                                    {{ Str::limit($item->name ?? 'Unnamed Product', 60) }}
                                                </a>
                                            </h3>
                                            <div class="product-price" style="font-size: 2rem;">
                                                ₹ {{ $item->listed_price ?? '0.00' }}
                                                @if(!empty($item->variant_mrp) && $item->variant_mrp > $item->listed_price)
                                                <span class="price-cut">₹ {{ $item->variant_mrp }}</span>
                                                @endif
                                            </div>

                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 40%;"></div>
                                                </div>
                                                <span class="ratings-text">( 1 Review )</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        @if(!empty($Instant_Delivery_Banner->photo))
        <div class="container instant-delivery-banner-container mt-2 web-service-provider">
            <a href="{{ url('instant-delivery-products') }}">
                <div class="video-banner video-banner-bg text-right pt-3 instant-delivery-banner"
                    style="background-image: url('{{ asset('storage/banner/' . $Instant_Delivery_Banner->photo) }}');">
                </div>
            </a>
        </div>
        <div class="container instant-delivery-banner-container mt-2 d-none service-mobile-provider">
            <a href="{{ url('instant-delivery-products') }}">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image: url('{{ asset('storage/banner/' . $Mob_Instant_Delivery_Banner->photo) }}');">
                </div>
            </a>
        </div>
        @endif
        <br>
        @auth
            @if($related_products->isNotEmpty())
                <div class="page-content">
                    <div class="related-products-section" style="background-image: url('{{ asset('storage/banner/' . $Seasonal_Banner[1]->photo) }}');">
                        <div class="container">
                            <div class="product-details-top">
                                <div class="heading heading-flex mb-3 pt-5">
                                    <div class="heading-left">
                                        <h2 class="title">Related Item You've Viewed</h2>
                                    </div>
                                </div>

                                <div class="owl-carousel related-products-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                    @foreach($related_products as $rp)
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            @if($rp->sku_discount_type === 'percent' && $rp->sku_discount > 0)
                                            <span class="product-label label-new">{{ round($rp->sku_discount) }}% off</span>
                                            @elseif($rp->sku_discount_type === 'flat' && $rp->sku_discount > 0)
                                            <span class="product-label label-new">₹{{ number_format($rp->sku_discount) }} off</span>
                                            @endif

                                            @if($rp->free_delivery)
                                            <span class="product-label product-label-two label-sale">Free Delivery</span>
                                            @endif

                                            <a href="{{ url('product/' . $rp->slug) }}">
                                                <img src="{{ asset('storage/images/' . ($rp->thumbnail_image ?? 'default.jpg')) }}"
                                                    alt="{{ $rp->name }}"
                                                    class="product-image">
                                            </a>
                                        </figure>

                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="{{ url('category/' . $rp->category_id) }}">{{ $rp->category_id }}</a>
                                            </div>

                                            <h3 class="product-title">
                                                <a href="{{ url('product/' . $rp->slug) }}" class="truncate-line-1">{{ $rp->name }}</a>
                                            </h3>

                                            <div class="product-price">
                                                ₹{{ number_format($rp->listed_price, 0) }}
                                                @if($rp->variant_mrp > $rp->listed_price)
                                                <span class="price-cut">₹{{ number_format($rp->variant_mrp, 0) }}</span>
                                                @endif
                                            </div>

                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: {{ rand(20, 100) }}%;"></div>
                                                </div>
                                                <span class="ratings-text">({{ rand(1, 10) }} Reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        @auth
            @if($more_related_products->isNotEmpty())
                <div class="page-content more-items-section mt-4">
                    <div class="more-related-products-banner"
                        style="background-image: url('{{ asset('storage/banner/' . $Seasonal_Banner[0]->photo) }}');">
                        <div class="container">
                            <div class="product-details-top">
                                <div class="heading heading-flex mb-3">
                                    <div class="heading-left">
                                        <h2 class="title">More Items to Consider</h2>
                                    </div>
                                </div>

                                <div class="owl-carousel more-items-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                    @foreach($more_related_products as $mp)
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            @if($mp->sku_discount_type === 'percent' && $mp->sku_discount > 0)
                                            <span class="product-label label-new">{{ round($mp->sku_discount) }}% off</span>
                                            @elseif($mp->sku_discount_type === 'flat' && $mp->sku_discount > 0)
                                            <span class="product-label label-new">₹{{ number_format($mp->sku_discount) }} off</span>
                                            @endif

                                            <a href="{{ url('product/' . $mp->slug) }}">
                                                <img src="{{ asset('storage/images/' . ($mp->thumbnail_image ?? 'default.jpg')) }}"
                                                    alt="{{ $mp->name }}"
                                                    class="product-image">
                                            </a>
                                        </figure>

                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="{{ url('category/' . $mp->category_id) }}">{{ $mp->category_id }}</a>
                                            </div>
                                            <h3 class="product-title">
                                                <a href="{{ url('product/' . $mp->slug) }}" class="truncate-line-1">
                                                    {{ $mp->name }}
                                                </a>
                                            </h3>
                                            <div class="product-price">
                                                ₹{{ number_format($mp->listed_price, 0) }}
                                                @if($mp->variant_mrp > $mp->listed_price)
                                                <span class="price-cut">₹{{ number_format($mp->variant_mrp, 0) }}</span>
                                                @endif
                                            </div>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: {{ rand(20, 100) }}%;"></div>
                                                </div>
                                                <span class="ratings-text">({{ rand(1, 10) }} Reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        
        @php
        $extension = strtolower(pathinfo($Banner_3->photo, PATHINFO_EXTENSION));
        $isVideo = in_array($extension, ['mp4', 'webm']);
        $fileUrl = asset('storage/banner/' . $Banner_3->photo);
        $Mobextension = strtolower(pathinfo($Mob_Banner_3->photo, PATHINFO_EXTENSION));
        $MobisVideo = in_array($Mobextension, ['mp4', 'webm']);
        $MobfileUrl = asset('storage/banner/' . $Mob_Banner_3->photo);
        @endphp
        <div class="mt-3 video-banner-image web-service-provider">
            <a href="{{ $Banner_3->url  }}">
                @if($isVideo)
                <video autoplay muted loop playsinline class="banner-video" style="margin-top: 60px;">
                    <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                    Your browser does not support the video tag.
                </video>
                @else
                <div class="banner-image" style="background-image: url({{ $fileUrl }});"></div>
                @endif
            </a>
        </div>

        <div class="mt-3 video-banner-image d-none service-mobile-provider">
            <a href="{{ $Mob_Banner_3->resource_type == 'category' ? $Mob_Banner_3->url : url('banner_products/' .    $Mob_Banner_3->id) }}">
                @if($MobisVideo)
                <video autoplay muted loop playsinline class="banner-video" style="margin-top: 60px;">
                    <source src="{{ $MobfileUrl }}" type="video/{{ $Mobextension }}">
                    Your browser does not support the video tag.
                </video>
                @else
                <div class="banner-image" style="background-image: url({{ $MobfileUrl }});"></div>
                @endif
            </a>
        </div>
        @auth
        @if($wishlists->isNotEmpty())
        <div class="wishlist-section" style="background-image: url({{ asset('storage/banner/' . $Seasonal_Banner[1]->photo) }});">
            <div class="container">
                <div class="wishlist-header">
                    <h2 class="title">Love it? Get it.</h2>
                    <a class="btn explore-more-btn blue-btn" href="{{ url('wishlist') }}">
                        Your Wishlist <span class="circle-arrow"><i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <div class="owl-carousel wishlist-carousel owl-simple carousel-equal-height carousel-with-shadow">
                    @foreach($wishlists as $wishlist)
                    <div class="product product-7 love-it">
                        <div class="media">
                            @if($wishlist->sku_discount_type === 'percent' && $wishlist->sku_discount > 0)
                            <span class="product-label label-new">{{ round($wishlist->sku_discount) }}% off</span>
                            @elseif($wishlist->sku_discount_type === 'flat' && $wishlist->sku_discount > 0)
                            <span class="product-label label-new">₹{{ number_format($wishlist->sku_discount) }} off</span>
                            @endif

                            <img src="{{ asset('storage/images/' . $wishlist->thumbnail_image) }}"
                                alt="{{ $wishlist->name }}"
                                class="product-image loveitgetit">

                            <div class="media-body">
                                <h5 class="product-title truncate-line-2">
                                    <a href="{{ url('product/' . $wishlist->slug) }}">{{ \Illuminate\Support\Str::limit($wishlist->name, 20, '..') }}</a>
                                </h5>
                                <div class="product-cat">
                                    <a href="#">{{ $wishlist->category ?? 'Uncategorized' }}</a>
                                </div>
                                <h6 class="product-type">{{ $wishlist->variation }}</h6>
                                <div class="product-price">
                                    ₹ {{ number_format($wishlist->listed_price, 0) }}
                                    @if($wishlist->discount_percent)
                                    <span class="price-cut">₹ {{ number_format($wishlist->variant_mrp, 0) }}</span>
                                    @endif
                                </div>
                                @if($wishlist->free_delivery == 1)
                                <button type="button" class="btndgn mt-2">Free Delivery</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endauth
        <div class="pt-5 mt-4 trending-banner" style="background-image: url({{ asset('storage/banner/' . $Seasonal_Banner[0]->photo) }}); height:350px;">
            <div class="container">
                <div class="heading heading-flex mb-3">
                    <div class="heading-left">
                        <h2 class="title">Trending Now</h2>
                    </div>
                    <div class="heading-right">
                        <button class="btn explore-more-btn orange-btn d-flex align-items-center" onclick="window.location.href='{{ url('top-products') }}'">
                            Explore More
                            <span class="circle-arrow ms-3">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tab-content tab-content-carousel">
                            <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel">
                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow trend-media" id="top-products-carousel">
                                    @foreach($top_products as $tp)
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            @if($tp->discount_type == 'percent' && $tp->discount > 0)
                                            <span class="product-label label-new">{{ round($tp->discount, 0) }}% off</span>
                                            @elseif($tp->discount_type == 'flat' && $tp->discount > 0)
                                            <span class="product-label label-new">₹{{ number_format($tp->discount, 0) }} off</span>
                                            @endif
                                            @if($tp->free_delivery == 1)
                                            <span class="product-label product-label-two label-sale">Free Delivery</span>
                                            @endif
                                            @php
                                            $images = json_decode($tp->image, true);
                                            $productImage = (!empty($images) && isset($images[0]))
                                            ? asset('storage/images/' . $images[0])
                                            : asset('storage/images/default.jpg');
                                            @endphp
                                            <a href="{{ url('product/' . $tp->slug) }}" onclick="setRecentlyViewed({{ $tp->product_id }})">
                                                <img src="{{ $productImage }}" alt="{{ $tp->name }}" class="product-image">
                                            </a>
                                        </figure>
                                        <div class="product-body">
                                            <div class="product-cat">
                                                @php
                                                $categories = json_decode($tp->category_ids, true);
                                                @endphp
                                                <a href="#">
                                                    @if(!empty($categories)) Category {{ $categories[0]['id'] }} @else Unspecified @endif
                                                </a>
                                            </div>
                                            <h3 class="product-title">
                                                <a href="{{ url('product/' . $tp->slug) }}" onclick="setRecentlyViewed({{ $tp->product_id }})">{{ $tp->name }}</a>
                                            </h3>
                                            <div class="product-price">
                                                ₹{{ number_format($tp->listed_price, 0) }}
                                                @if($tp->discount > 0)
                                                <span class="price-cut">₹{{ number_format($tp->variant_mrp, 0) }}</span>
                                                @endif
                                            </div>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 20%;"></div>
                                                </div>
                                                <span class="ratings-text">(2 Reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <script>
                                function setRecentlyViewed(productId) {
                                $.ajax({
                                    url: "{{ route('recently_view') }}",
                                    type: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                    },
                                    data: {
                                        product_id: productId
                                    },
                                    success: function (response) {
                                    // alert("Successfully added to recently viewed!");
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText); // log actual error
                                    //  alert("Something went wrong while adding the item.");
                                    }
                                });
                            }
                            </script>
                            <div class="tab-pane p-0 fade" id="trending-men-tab" role="tabpanel">
                                <div class="banner-group mb-2">
                                    <div class="container"><br><br>
                                        <div class="row justify-content-center">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="banner banner-overlay">
                                                    <a href="#">
                                                        <img src="{{ asset('website/assets/images/demos/demo-20/banners/banner-6.jpg') }}" alt="Banner">
                                                    </a>
                                                    <div class="banner-content">
                                                        <h4 class="banner-subtitle text-white"><a href="#">INTERIOR CHOWK</a></h4>
                                                        <h3 class="banner-title text-white"><a href="#">Arcitech</a></h3>
                                                        <a href="#" class="btn btn-outline-white-3 banner-link">Discover Now
                                                            <i class="icon-long-arrow-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container featured topcategory pb-2">
            <center>
                <h2 class="title text-center">Top Categories</h2>
            </center>

            <div class="row justify-content-center px-3">
                @foreach($top_categories as $t_ca)
                <div class="col-lg-2 col-md-3 col-sm-4 col-3 mb-4">
                    <div class="category-card text-center">
                        <figure class="product-media">
                            <a href="{{ url('category/' . $t_ca->slug) }}">
                                <img src="{{ asset('storage/category/' . $t_ca->icon) }}"
                                    alt="{{ $t_ca->name }}"
                                    class="product-image img-fluid">
                            </a>
                        </figure>
                        <div class="category-name-wrapper">
                            <a href="{{ url('category/' . $t_ca->slug) }}" class="category-name">
                                {{ $t_ca->name }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="container featured pb-2">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Top Brands</h2>
                </div>
                <div class="heading-right">
                    <button class="btn explore-more-btn d-flex align-items-center" type="button" onclick="window.location.href='{{ url('brands') }}'">
                        Explore More
                        <span class="circle-arrow ms-3">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="featured-brands-tab" role="tabpanel">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" id="top-brands-carousel">
                                @foreach($top_brands as $tb)
                                <div class="brand-card">
                                    <figure class="brand-logo-wrapper">
                                        <a href="{{ url('products_2',['brand_id' => $tb->name]) }}">
                                            <img src="{{ asset('storage/brand/' . $tb->image) }}"
                                                alt="{{ $tb->name }}"
                                                class="brand-logo-img">
                                        </a>
                                    </figure>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                @if(!empty($Banner_4->photo))
                    <div class="container architect-banner-container mt-5">
                        <a href="{{ $Banner_4->url }}">
                            <!-- <div class="architect-banner video-banner-bg text-end pt-3"
                                style="background-image: url('{{ asset('storage/banner/' . $Banner_4->photo) }}');">

                            </div> -->
                            <img src="{{ asset('storage/banner/' . $Banner_4->photo) }}" alt="">
                        </a>
                    </div>
               @endif
                @if (!empty($architects) && count($architects) > 0)
          <div class="section-6 mt-5 pt-5 top-interior-section"
            style="background-image: url('{{ asset('storage/banner/' . $Seasonal_Banner[1]->photo) }}');">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading heading-flex mb-3">
                            <div class="heading-left">
                                <h2 class="title">Top Architects</h2>
                            </div>
                            <div class="heading-right">
                                <button class="btn explore-more-btn orange-btn d-flex align-items-center" onclick="window.location.href='{{ url('architects') }}'">
                                    Explore More
                                    <span class="circle-arrow ms-3">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" id="architects-carousel">
                            @foreach($architects as $arch)
                            <div class="interior-card">
							<a href="{{ url('/interior-designers'.'/' . Str::slug($arch->name)) }}">
                                <div class="card text-center border-0">
                                    <img src="{{ asset($arch->banner_image) }}" class="card-img-top interior-banner-img" alt="Designer Banner">
                                    <div class="interior-profile-img">
                                        <img src="{{ asset('storage/service-provider/profile/' . $arch->image) }}" alt="Architect Profile">
                                    </div>
                                    <div class="card-body p-3">
                                        <h5 class="card-title">{{ $arch->name ?? 'Unknown' }}</h5>
                                        <p class="card-text text-dark truncate-text">
                                            {{ $arch->city ? str_replace(['[', ']', '"'], '', $arch->city) : 'Location not available' }}
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
                </div>
            </div>
        </div>
        @endif
        @if (!empty($interior_designer) && count($interior_designer) > 0)
        <div class="container banner-container mt-10">
            <a href="{{ $Banner_5->url  }}">
                <div class="video-banner video-banner-bg text-end pt-3 interior-banner"
                    style="background-image: url('{{ asset('storage/banner/' . $Banner_5->photo) }}');">
                </div>
            </a>
        </div>
        <div class="section-6 mt-5 pt-5 top-interior-section"
            style="background-image: url('{{ asset('storage/banner/' . $Seasonal_Banner[0]->photo) }}');">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading heading-flex mb-3">
                            <div class="heading-left">
                                <h2 class="title">Top Interior Designers</h2>
                            </div>
                            <div class="heading-right">
                                <button class="btn explore-more-btn orange-btn d-flex align-items-center" onclick="window.location.href='{{ url('interior-designers') }}'">
                                    Explore More
                                    <span class="circle-arrow ms-3">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" id="interior-designers-carousel">
                            @foreach($interior_designer as $designer)
                            <div class="interior-card">
                                <a href="{{ url('/interior-designers' .'/'. Str::slug($designer->name)) }}">
                                <div class="card text-center border-0">
                                    <img src="{{ asset($designer->banner_image) }}" class="card-img-top interior-banner-img" alt="Designer Banner">
                                    <div class="interior-profile-img">
                                        <img src="{{ asset('storage/service-provider/profile/' . $designer->image) }}" alt="Designer Profile" style="width:unset;">
                                    </div>
                                    <div class="card-body p-3">
                                        <h5 class="card-title">{{ $designer->name ?? 'Unknown' }}</h5>
                                        <p class="card-text text-dark truncate-text">
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
                </div>
            </div>
        </div>
        @endif
        @if(!empty($Banner_7->photo))
        @php
        $extension = strtolower(pathinfo($Banner_7->photo, PATHINFO_EXTENSION));
        $isVideo = in_array($extension, ['mp4', 'webm']);
        $fileUrl = asset('storage/banner/' . $Banner_7->photo);
        $bannerLink = $Banner_7->resource_type === 'category' ? $Banner_7->url : url('banner_products/' . $Banner_7->id);
        @endphp
        <div class="media-banner-section">
            <a href="{{ $bannerLink }}">
                @if($isVideo)
                <div class="media-banner video">
                    <video autoplay muted loop playsinline>
                        <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
                @else
                <div class="media-banner image" style="background-image: url('{{ $fileUrl }}');"></div>
                @endif
            </a>
        </div>
        @endif
        @if (!empty($contractors) && count($contractors) > 0)
         <div class="section-6 mt-5 pt-5 top-interior-section"
            style="background-image: url('{{ asset('storage/banner/' . $Seasonal_Banner[0]->photo) }}');">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading heading-flex mb-3">
                            <div class="heading-left">
                                <h2 class="title">Top Contractor</h2>
                            </div>
                            <div class="heading-right">
                                <button class="btn explore-more-btn orange-btn d-flex align-items-center" onclick="window.location.href='{{ url('contractors') }}'">
                                    Explore More
                                    <span class="circle-arrow ms-3">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" id="contractor-carousel">
                            @foreach($contractors as $contr)
                            <div class="interior-card">
                                <a href="{{ url('/interior-designers'.'/' . Str::slug($contr->name)) }}">
                                <div class="card text-center border-0">
                                    <img src="{{ asset($contr->banner_image) }}" class="card-img-top interior-banner-img" alt="Contractor Banner">
                                    <div class="interior-profile-img">
                                        <img src="{{ asset('storage/service-provider/profile/' . $contr->image) }}" alt="Contractor Profile" style="width:35%;">
                                    </div>
                                    <div class="card-body p-3">
                                        <h5 class="card-title">{{ $contr->name ?? 'Unknown' }}</h5>
                                        <p class="card-text text-dark truncate-text">
                                            {{ $contr->city ? str_replace(['[', ']', '"'], '', $contr->city) : 'Location not available' }}
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
                </div>
            </div>
        </div>
        @endif
        @php
        $extension = strtolower(pathinfo($banner->photo, PATHINFO_EXTENSION));
        $isVideo = in_array($extension, ['mp4', 'webm']);
        $fileUrl = asset('storage/banner/' . $banner->photo);
        $linkUrl = $banner->resource_type === 'brand' ? $banner->url : url('banner_products/' . $banner->id);
    
        @endphp
        <div class="container mt-10 video-link">
            <a href="{{ $linkUrl }}">
                @if ($isVideo)
                <video autoplay muted loop playsinline class="media-banner-video">
                    <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                    Your browser does not support the video tag.
                </video>
                @else
                <div class="media-banner-image" style="background-image: url('{{ $fileUrl }}');"></div>
                @endif
            </a>
        </div>
    </div>
    
        <div class="deal-banner-wrapper" style="background-image: url('{{ asset('storage/banner/' . $Day_BG_w->photo) }}');">
        

<style>

    @media (max-width: 991px) {
        .deal-banner-wrapper {
            background-image: url('{{ asset('storage/banner/' . $Day_BG_mobile->photo) }}') !important;
            
        }
       .luxe-bg {
        background-image: url('{{ asset('storage/banner/' . $Luxury_BG_app->photo) }}') !important;
        background-size: cover !important;      /* Image proportion maintain */
        background-position: center !important; /* Center me focus */
        background-repeat: no-repeat !important;
        width: 100% !important;                  /* Container jitna width */                         /* Mobile ke liye fix height */
        border-radius: 10px;                      /* Agar container me rounded hai to */
    }

        .owl-carousel .owl-item {
    position: relative;
    min-height: 1px;
    float: right;
        }
    }
</style>

        <div class="container">
           
                <div class="col-lg-9">
                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" id="deals-carousel">
                                @foreach($deals as $deal)
                                @php
                                $current_time = now();
                                $end_time = \Carbon\Carbon::parse($deal->expire_date_time);
                                $expired = $current_time->greaterThan($end_time);
                                $time_left = $end_time->diff($current_time);
                                $time_display = $expired ? 'Expired' : (
                                $time_left->d > 0 ? "{$time_left->d}d {$time_left->h}h..." :
                                ($time_left->h > 0 ? "{$time_left->h}h {$time_left->i}m..." :
                                ($time_left->i > 0 ? "{$time_left->i}m {$time_left->s}s" :
                                "{$time_left->s}s"))
                                );
                                $image = json_decode($deal->image, true);
                                $first_image = isset($image[0]) ? asset('storage/images/'.$image[0]) : asset('storage/images/'.$deal->thumbnail_image);
                                @endphp
                                <div class="product product-7" style="position: relative;
                                                                                left: 260px;">
                                    <figure class="product-media">
                                        @if($deal->discount > 0)
                                        <span class="product-label label-new">
                                            {{ $deal->discount_type == 'percent' ? round($deal->discount, 0).'%' : '₹'.number_format($deal->discount, 0) }} OFF
                                        </span>
                                        @endif
                                        <span class="product-label product-label-two label-sale">Free Delivery</span>
                                        <a href="{{ url('product/'.$deal->slug) }}">
                                            <img src="{{ $first_image }}" alt="{{ $deal->name }}" class="product-image">
                                        </a>
                                    </figure>
                                    <div class="product-body">
                                        <h3 class="product-title">
                                            <a href="{{ url('product/'.$deal->slug) }}">{{ $deal->name }}</a>
                                        </h3>
                                        <div class="product-price">
                                            ₹{{ number_format($deal->listed_price, 0) }}
                                            @if($deal->variant_mrp > $deal->listed_price)
                                            <span class="price-cut">₹{{ number_format($deal->variant_mrp, 0) }}</span>
                                            @endif
                                        </div>
                                        <div class="deal-timer">
                                            <span class="{{ $expired ? 'timer-expired' : 'timer-text' }}">
                                                {{ $expired ? 'Offer Expired' : 'Offer ends in: ' . $time_display }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                @if(count($deals) > 1)
                                <a href="{{ url('deals') }}" class="d-block w-100 h-100">
                                    <div>
                                        <img src="{{ asset('storage/banner/' . $Banner_2->photo) }}" alt="banner image" class="deal-banner-image">
                                    </div>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="intro-section pt-lg-4">
        <div class="container">
          

            <div class="row display-d">
                <div class="col-md-12 col-lg-6">
                    <div class="banner banner-big banner-overlay">
                        <a href="{{ $Discount_1->url }}">
                            <img src="{{ asset('storage/banner/' . $Discount_1->photo) }}" alt="Banner">
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="banner banner-overlay">
                        <a href="{{ $Discount_2->url }}">
                            <img src="{{ asset('storage/banner/' . $Discount_2->photo) }}" alt="Banner">
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    @foreach([$Discount_3, $Discount_4] as $discount)
                    <div class="banner banner-small banner-overlay">
                        <a href="{{ $discount->url }}">
                            <img src="{{ asset('storage/banner/' . $discount->photo) }}" alt="Banner">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
			
			<div class="row display-m">
                <div class="col-md-12 col-lg-6 col-6">
                    <div class="banner banner-big banner-overlay">
                        <a href="{{ $Discount_1->url }}">
                            <img src="{{ asset('storage/banner/' . $Discount_1->photo) }}" alt="Banner">
                        </a>
                    </div>
					<div class="banner banner-overlay">
                        <a href="{{ $Discount_2->url }}">
                            <img src="{{ asset('storage/banner/' . $Discount_2->photo) }}" alt="Banner">
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 col-6">
                    <div class="banner banner-overlay">
                        <a href="{{ $Discount_3 }}">
                            <img src="{{ asset('storage/banner/' . $Discount_2->photo) }}" alt="Banner">
                        </a>
                    </div>
					<div class="banner banner-big banner-overlay">
                        <a href="{{ url('discount_products/' . $Discount_1->id) }}">
                            <img src="{{ asset('storage/banner/' . $Discount_1->photo) }}" alt="Banner">
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <div class="container d-block d-lg-none"> <!-- Show only on mobile/tablet -->
    <div class="row" style="height: 600px;"> <!-- Fixed height to maintain equal division -->

        <!-- Right Column: Banner 2, 3, 5 -->
        <div class="col-6 d-flex flex-column gap-2"> <!-- gap between banners -->
            <div class="banner banner-overlay flex-fill">
                <a href="{{ $discount_banner_2->url }}">
                    <img src="{{ asset('storage/banner/' . $discount_banner_2->photo) }}" class="img-fluid" alt="Banner 2">
                </a>
            </div>
            <div class="banner banner-overlay flex-fill">
                <a href="{{ $discount_banner_3->url }}">
                    <img src="{{ asset('storage/banner/' . $discount_banner_3->photo) }}" class="img-fluid" alt="Banner 3">
                </a>
            </div>
            <div class="banner banner-overlay flex-fill">
                <a href="{{ url('discount_products/' . ($discount_banner_5->id ?? 0)) }}">
                    <img src="{{ asset('storage/banner/' . $discount_banner_5->photo) }}" class="img-fluid" alt="Banner 5">
                </a>
            </div>
        </div>

        <!-- Left Column: Banner 1 and 4 -->
        <div class="col-6 d-flex flex-column gap-2">
            <div class="banner banner-overlay flex-fill">
                <a href="{{ url('discount_products/' . ($discount_banner_1->id ?? 0)) }}">
                    <img src="{{ asset('storage/banner/' . $discount_banner_1->photo) }}" class="img-fluid" alt="Banner 1">
                </a>
            </div>
            <div class="banner banner-overlay flex-fill">
                <a href="{{ url('discount_products/' . ($discount_banner_4->id ?? 0)) }}">
                    <img src="{{ asset('storage/banner/' . $discount_banner_4->photo) }}" class="img-fluid" alt="Banner 4">
                </a>
            </div>
        </div>

    </div>
</div>

<style>
    .banner {
        padding: 0; /* Remove internal padding */
        margin: 0;
        border-radius: 12px; /* Rounded corners */
        overflow: hidden;
        /* box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);  */
    }

    .banner img {
        object-fit: cover;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        display: block;
    }

    @media (max-width: 991px) {
        .row > .col-6 {
            height: 100%;
        }

        .banner-overlay {
            height: 0;
            flex-grow: 1;
        }

        .d-flex.flex-column {
            gap: 8px; /* Consistent gap between banners */
        }
    }
</style>


 @if(!empty($Banner_6->photo))
    <div class="container architect-banner-container mt-1 d-block d-lg-none"> <!-- Show only on mobile/tablet -->
        <a href="{{ $Banner_6->url }}">
            <img src="{{ asset('storage/banner/' . $Banner_6->photo) }}" alt="" class="img-fluid w-100 rounded" style="height:80px">
        </a>
    </div>
@endif



        @if(!empty($Banner_9->photo))
        <div class="container mt-3">
            @php
            $ext = strtolower(pathinfo($Banner_9->photo, PATHINFO_EXTENSION));
            $isVideo = in_array($ext, ['mp4', 'webm']);
            $fileUrl = asset('storage/banner/' . $Banner_9->photo);
            
            @endphp

            <a href="{{ $Banner_9->url  }}">
                @if($isVideo)
                <video autoplay muted loop playsinline class="banner-video">
                    <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                </video>
                @else
                <div class="video-banner image-banner" style="background-image: url({{ $fileUrl }});"></div>
                @endif
            </a>
        </div>
        @endif

 <style>

/* Slider-like behavior for mobile view */
@media (max-width: 991px) {
    .responsive-slider {
        flex-wrap: nowrap !important;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        gap: 8px;
        padding-bottom: 8px;
    }

    .responsive-slider .choice-slide {
        flex: 0 0 auto;
        width: 48%; /* 2 in one row */
        scroll-snap-align: start;
    }

    .choice-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        padding: 0px;
    }

    .choice-video,
    .choice-card img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .choice-thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-top: 10px;
    }

    .choice-title {
        font-weight: bold;
        font-size: 14px;
        margin: 5px 0;
    }

    .price-display span {
        font-size: 14px;
        margin-right: 4px;
    }

    .price-display del {
        color: #888;
    }
    
  
   
  
}



</style>
              <div class="container creator-choice-container">
    <h2 class="title text-center">Creator's Choice</h2>

    <!-- Responsive scrollable row -->
    <div class="row justify-content-center flex-nowrap overflow-auto responsive-slider">
        @for ($i = 1; $i <= 2; $i++)
            @php
                $item=${"choice_$i"};
                $videoPath="storage/banner/" . $item->video;
                $photoPath = "storage/banner/" . $item->photo;
                $images = json_decode($item->image, true);
                $firstImage = asset('/storage/images/' . ($images[0] ?? 'default.jpg'));
                $isVideo = pathinfo($videoPath, PATHINFO_EXTENSION) == 'mp4';
            @endphp

            <div class="col-lg-2 col-6 choice-slide">
                <div class="banner product-banner choice-card">
                    <a href="javascript:void(0);"
                        data-video="{{ asset($videoPath) }}"
                        data-name="{{ $item->name }}"
                        data-listed="{{ $item->listed_price }}"
                        data-mrp="{{ $item->variant_mrp }}"
                        data-slug="{{ $item->slug }}"
                        data-description='{!! $item->details !!}'
                        data-images='@json($images)'
                        onclick="openProductModal(this)">
                        @if($isVideo)
                        <video autoplay muted loop class="choice-video">
                            <source src="{{ asset($photoPath) }}" type="video/mp4">
                        </video>
                        @else
                        <img src="{{ asset($photoPath) }}" alt="Choice Image">
                        @endif
                    </a>

                    <img src="{{ $firstImage }}" class="choice-thumb">
                    <p class="choice-title">{{ strlen($item->name) > 15 ? substr($item->name, 0, 15) . '...' : $item->name }}</p>
                    <div class="price-display">
                        <span>₹{{ number_format($item->listed_price, 0) }}</span>
                        <span><del>₹{{ number_format($item->variant_mrp, 0) }}</del></span>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>


                    <div class="col-lg-9">
                        <div class="tab-content tab-content-carousel">
                            <div class="tab-pane p-0 fade show active" id="bags-women-tab" role="tabpanel" aria-labelledby="bags-women-link">
                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                                    data-owl-options='{
                                        "nav": false, 
                                        "dots": true,
                                        "margin": 20,
                                        "loop": false,
                                        "responsive": {
                                            "0": {
                                                "items":2
                                            },
                                            "480": {
                                                "items":2
                                            },
                                            "768": {
                                                "items":3
                                            },
                                            "1200": {
                                                "items":3,
                                                "nav": true,
                                                "dots": false
                                            }
                                        }
                                    }'>
                                </div><!-- End .product-body -->
                            </div><!-- End .product -->
                        </div><!-- End .owl-carousel -->
                    </div><!-- .End .tab-pane -->
                    <div class="tab-pane p-0 fade" id="bags-men-tab" role="tabpanel" aria-labelledby="bags-men-link">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                            data-owl-options='{
                                        "nav": false,
                                        "dots": true,
                                        "margin": 20,
                                        "loop": false,
                                        "responsive": {
                                            "0": {
                                                "items":2
                                            },
                                            "480": {
                                                "items":2
                                            },
                                            "768": {
                                                "items":3
                                            },
                                            "992": {
                                                "items":4
                                            },
                                            "1200": {
                                                "items":4,
                                                "nav": true,
                                                "dots": false
                                            }
                                        }
                                    }'>
                        </div><!-- End .owl-carousel -->
                    </div><!-- .End .tab-pane -->
            </div><!-- End .tab-content -->
        </div>
    </div>
    </div><!-- End .container -->
    <script>
        
        function openProductModal(el) {
            const videoUrl = el.getAttribute('data-video');
            const productName = el.getAttribute('data-name');
            const listedPrice = el.getAttribute('data-listed');
            const mrp = el.getAttribute('data-mrp');
            const description = el.getAttribute('data-description');
            const images = JSON.parse(el.getAttribute('data-images'));
             slug = el.getAttribute('data-slug');
           
            // Set video
            const video = document.getElementById('popupVideo');
            const source = document.getElementById('popupVideoSource');
            source.src = videoUrl;
            video.load();
            video.play();
                        
            // Set product info
            document.getElementById('popupProductName').innerHTML = '<a href="product/' + slug + '">' + productName + '</a>';
            document.getElementById('popupListedPrice').innerText = listedPrice;
            document.getElementById('popupMRP').innerText = mrp;
            document.getElementById('popupDescription').innerHTML = description;
           document.getElementById('more_cart').innerHTML =
        '<button id="btn" class="bt btn-primary mr-2" style="background-color: #2E6CB2; border:1px solid #2E6CB2;">' +
        '<a href="product/' + slug + '" style="color: white; text-decoration: none;">More Info</a>' +
        '</button>';

            thumbnail = '<div><img src="/storage/images/' + images[0] + '" width="70px">';
            document.getElementById('thumb').innerHTML = thumbnail;
            const container = document.getElementById('popupImageContainer');
            container.innerHTML = '';
            images.forEach((img, index) => {
                const active = index === 0 ? 'active' : '';
                container.innerHTML += `
            <div class="carousel-item ${active}">
                <img src="/storage/images/${img}" class="d-block w-100" alt="Image ${index + 1}">
            </div>
        `;
            });
            var myModal = new bootstrap.Modal(document.getElementById('videoModal'));
            myModal.show();
        }

       
    </script>
    <style>
/* Overall modal height scroll fix */
.custom-modal-content {
    max-height: 90vh;
    overflow-y: auto;
    padding: 15px;
    border-radius: 10px;
}

/* Two-column layout */
@media (min-width: 768px) {
    .modal-body .col-md-6 {
        flex: 1;
        padding: 10px;
    }
}

/* Video section */
.video-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    padding-top: 100%; /* Makes it square */
    background-color: #f2f2f2;
    overflow: hidden;
    border: 2px solid #007bff;
    border-radius: 6px;
}

.video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Product title */
#popupProductName {
    font-weight: bold;
    font-size: 1.2rem;
}

/* Price styling */
#popupListedPrice {
    font-weight: bold;
}

#popupMRP {
    color: #888;
}

/* Carousel image sizing */
.carousel-inner img {
    width: 100%;
    height: 200px;
    object-fit: contain;
    background: #f7f7f7;
    border-radius: 6px;
}

/* Description scroll */
#popupDescription {
    max-height: 120px;
    overflow-y: auto;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

/* Button section */
#more_cart {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 10px;
}

#more_cart .btn {
    flex: 1;
    padding: 10px;
    font-weight: bold;
    font-size: 14px;
}

/* Responsive stack on small devices */
@media (max-width: 767px) {
    .modal-body .row {
        flex-direction: column;
    }

    .video-wrapper {
        padding-top: 56.25%; /* 16:9 aspect ratio on mobile */
    }

    #more_cart {
        flex-direction: column;
    }

}
 @media (max-width: 576px) {
        .tips-col {
            max-width: 48%;
            flex: 0 0 48%;
            margin: 1%;
        }
    }
    
@media (min-width: 1200px) {
    .modal-xl {
        max-width: 715px;
    }
}
</style>
   <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content custom-modal-content">
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Video -->
                    <div class="col-md-6">
                        <div class="video-wrapper">
                            <video id="popupVideo" class="w-100 h-100" controls autoplay muted loop>
                                <source id="popupVideoSource" src="" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>

                    <!-- Text + Image Carousel -->
                    <div class="col-md-6">
                        <div class="content-wrapper">
                           <div class="d-flex align-items-center mb-2">
    <!-- Thumb -->
    <div id="thumb" style="width: 60px; height: 60px; flex-shrink: 0;"></div>

    <!-- Product Info -->
    <div class="ms-2 ml-1">
        <h5 id="popupProductName" class="text-dark mb-1"></h5>
        <div>
            <h6 class="d-inline text-success mb-0">₹<span id="popupListedPrice"></span></h6>
            <span class="text-danger ms-2">₹<del id="popupMRP"></del></span>
        </div>
    </div>
</div>

                            <!-- Bootstrap Carousel -->
                            <div id="imageCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                                <div class="carousel-inner" id="popupImageContainer">
                                    <!-- Images inserted via JS -->
                                </div>
                            </div>

                            <h6>Description</h6>
                            <div id="popupDescription" class="descriptions mb-2"></div>

                            <div id="more_cart">
                                <!-- Additional cart elements -->
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <!-- <hr class="mt-0 mt-xl-1 mb-0"> -->
    </div><!-- End .container -->

    <main>
        <div class="section-9 pb-4 d-none d-md-block">
    <div class="luxe-border mb-0">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading heading-flex mb-0 flex-column">
                        <div class="heading-left py-4 text-center">
                            <img class="luxe-img" src="{{ asset('website/assets/images/luxe-img.png') }}" alt="luxury" />
                            <h2 class="title d-inline-block mx-4">The Luxe Vault</h2>
                            <img class="luxe-img" src="{{ asset('website/assets/images/luxe-img.png') }}" alt="luxury" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="luxe-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow mt-5" id="luxe-carousel">
                                @foreach($luxe_products as $lp)
                                @php
                                $images = json_decode($lp->image, true);
                                $productImage = (!empty($images) && isset($images[0]))
                                ? asset('storage/images/' . $images[0])
                                : asset('storage/images/default.jpg');
                                $categories = json_decode($lp->category_ids, true);
                                @endphp
                                <div class="product product-7">
                                    <figure class="product-media">
                                        <span class="product-label label-new">New</span>
                                        <a href="{{ url('product/' . $lp->slug) }}">
                                            <img src="{{ $productImage }}" alt="{{ $lp->name }}" class="product-image">
                                        </a>
                                    </figure>
                                    <div class="product-body">
                                        <div class="product-cat">
                                            <a href="#">
                                                @if(!empty($categories)) Category {{ $categories[0]['id'] }} @else Unspecified @endif
                                            </a>
                                        </div>
                                        <h3 class="product-title">
                                            <a href="{{ url('product/' . $lp->slug) }}">{{ $lp->name }}</a>
                                        </h3>
                                        <div class="product-price">
                                            ₹{{ number_format($lp->listed_price, 0) }}
                                            <span class="price-cut">₹{{ number_format($lp->variant_mrp, 0) }}</span>
                                        </div>
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" style="width: 20%;"></div>
                                            </div>
                                            <span class="ratings-text">(2 Reviews)</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @if(count($luxe_products) > 1)
                                <a href="{{ url('luxury-products') }}" class="luxury-banner-link">
                                    <img src="{{ asset('storage/banner/' . $Banner_2->photo) }}"
                                        alt="luxury banner"
                                        class="luxury-banner-img">
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @if(!empty($Banner_8->photo))
     <div class="container architect-banner-container mt-5">
                        <a href="{{ $Banner_8->url }}">
                            <!-- <div class="architect-banner video-banner-bg text-end pt-3"
                                style="background-image: url('{{ asset('storage/banner/' . $Banner_4->photo) }}');">

                            </div> -->
                            <img src="{{ asset('storage/banner/' . $Banner_8->photo) }}" alt="">
                        </a>
                    </div>
                    @endif
    <br><br>


     
    <div class="container">
        <center>
            <h2 class="title" style="text-align:center;">Tips</h2>
        </center>
        <br>
        <div class="row justify-content-center tips-row">
    @for ($i = 1; $i <= 2; $i++)
        @php
            $photoVar = "tips_{$i}->photo";
            $photoPath = "storage/banner/" . ${"tips_$i"}->photo;
        @endphp
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 tips-col">
            <div class="banner banner-overlay product-banner text-center">
                <a href="#">
                    <video width="100%" autoplay muted loop playsinline>
                        <source src="{{ asset($photoPath) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </a>
                <p class="tip-label">Tips</p>
            </div>
        </div>
    @endfor


                <div class="tab-pane p-0 fade" id="bags-men-tab" role="tabpanel" aria-labelledby="bags-men-link">
                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow tips-carousel-2">
                        <div class="item">
                            <h5>Other Tip 1</h5>
                        </div>
                        <div class="item">
                            <h5>Other Tip 2</h5>
                        </div>
                        <div class="item">
                            <h5>Other Tip 3</h5>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    @if(!empty($Banner_10->photo))
    <div class="container">
        @php
        $extension = strtolower(pathinfo($Banner_10->photo, PATHINFO_EXTENSION));
        $isVideo = in_array($extension, ['mp4', 'webm']);
        $fileUrl = asset('storage/banner/' . $Banner_10->photo);
        $linkUrl = $Banner_10->resource_type == 'category'
        ? $Banner_10->url
        : url('banner_products/' . $Banner_10->id);

        @endphp

        <a href="{{ $linkUrl }}">
            @if ($isVideo)
            <video class="custom-banner-video" autoplay muted loop playsinline>
                <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                Your browser does not support the video tag.
            </video>
            @else
            <div class="custom-banner-image" style="background-image: url('{{ $fileUrl }}');"></div>
            @endif
        </a>
    </div>
    @endif
    <div class="welcome-section">
        <div class="container">
            <center>
                <h2 class="title welcome-title">Welcome to InteriorChowk</h2>
            </center>

            <br>

            <div class="row">
                <div class="col-lg-6 d-flex align-items-stretch subscribe-div">
                    <div class="cta cta-box">
                        <div class="cta-content">
                            <p class="welcome-description">
                                Welcome to InteriorChowk Free branding & promotion* InteriorChowk is committed to supporting your growth.

                                <br>Join us on this journey, and let's turn your interior design dreams into reality together!

                                <br>India's first dedicated marketplace for home interior buyer‘s where a multitude of sellers, interior designers, architects, contractors, workers and many more..

                                <br><b>presence in the competitive market.</b>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 banner-overlay-div">
                    <div class="banner banner-overlay">
                        <iframe
                            width="100%"
                            height="315"
                            src="https://www.youtube.com/embed/SpsiQwxOrKw?si=g-mFog-fUqXbTuAQ"
                            title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container featured-section mt-4 pb-2" style="display: none;">
        <div class="heading heading-flex mb-3">
            <div class="heading-left">
                <h2 class="title">Featured in</h2>
            </div>
            <div class="heading-right">
                <button class="btn explore-more-btn d-flex align-items-center">
                    Explore More
                    <span class="circle-arrow ms-3">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content tab-content-carousel">
                    <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel" aria-labelledby="featured-women-link">
                        <div class="owl-carousel owl-simple featured-carousel">
                            @for ($i = 0; $i < 10; $i++)
                                <div class="product product-7 featured-item">
                                <figure class="product-media">
                                    <a href="product.html">
                                        <img src="{{ asset('website/assets/images/logot.png') }}" alt="Product image" class="product-image">
                                    </a>
                                </figure>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="tab-pane p-0 fade" id="featured-men-tab" role="tabpanel" aria-labelledby="featured-men-link">
                    <div class="owl-carousel owl-simple featured-carousel-alt">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="section-8 customer-carousel-section" style="display:none;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading heading-flex mb-3">
                        <div class="heading-left">
                            <h2 class="title">Happy Customers</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="owl-carousel owl-simple happy-customer-carousel">
                        @for ($i = 1; $i <= 8; $i++)
                            <article class="entry">
                            <figure class="entry-media mb-2">
                                <a href="javascript:void(0);">
                                    <img src="{{ asset('website/assets/images/team/about-2/member-' . $i . '.jpg') }}" alt="Customer {{ $i }}">
                                </a>
                            </figure>

                            <div class="entry-body">
                                <div class="entry-meta text-dark">
                                    <a href="#">Dec 12, 2025</a>
                                </div>

                                <h3 class="entry-title">
                                    <a href="javascript:void(0);">Aman Bhatnagar</a>
                                </h3>

                                <div class="ratings-container d-block">
                                    <div class="ratings">
                                        <div class="ratings-val" style="width: 20%;"></div>
                                    </div>
                                </div>

                                <div class="entry-content">
                                    <p>Hey! Remember, InteriorChowk or its team will never ask you for financial details or...</p>
                                </div>
                            </div>
                            </article>
                            @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="blog-posts blog-carousel-section" style="display: none;">
        <div class="container">
            <h2 class="title">From Our Blog</h2>

            <div class="owl-carousel owl-simple blog-carousel">
                @php
                $posts = [
                ['img' => 'post-2.jpg', 'title' => 'Vivamus vestibulum ntulla.', 'desc' => 'Phasellus hendrerit. Pelletesque aliquet nibh necurna In nisi neque, aliquet vel, dapibus id ...'],
                ['img' => 'post-3.jpg', 'title' => 'Praesent placerat risus.', 'desc' => 'Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc ...'],
                ['img' => 'post-4.jpg', 'title' => 'Fusce pellentesque suscipit.', 'desc' => 'Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero augue.'],
                ['img' => 'post-1.jpg', 'title' => 'Sed adipiscing ornare.', 'desc' => 'Lorem ipsum dolor consectetuer adipiscing elit. Phasellus hendrerit. Pelletesque aliquet nibh ...'],
                ];
                @endphp

                @foreach ($posts as $post)
                <article class="entry">
                    <figure class="entry-media">
                        <a href="single.html">
                            <img src="{{ asset('website/assets/images/demos/demo-13/blog/' . $post['img']) }}" alt="Blog image">
                        </a>
                    </figure>

                    <div class="entry-body">
                        <div class="entry-meta text-dark">
                            <a href="#">Dec 12, 2023</a>, 0 Comments
                        </div>

                        <h3 class="entry-title">
                            <a href="single.html">{{ $post['title'] }}</a>
                        </h3>

                        <div class="entry-content">
                            <p>{{ $post['desc'] }}</p>
                            <a href="single.html" class="read-more">Read More</a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <br>
            <center>
                <a href="#" class="btn btn-outline-lightgray btn-more btn-round">
                    <span>View more articles</span><i class="icon-long-arrow-right"></i>
                </a>
            </center>
        </div>
    </div>
    <div class="cta pb-5 pb-lg-3 mb-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="cta-heading">
                        {!! $seo->content ?? '' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('script')
<script src="{{ asset('website/assets/js/home.js') }}"></script>
@endpush
@endsection