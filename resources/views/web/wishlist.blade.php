@extends('layouts.back-end.common_seller_1')
@section('content')
    <style>
        .img-fluid {
            max-width: 44% !important;
            height: auto;
            margin: auto;
        }
    </style>
    <div class="container featured wishListRespo mt-4 pb-2">
        <div class="row">
            @if ($wishlists->isEmpty())
                <div class="col-md-12">
                    <img src="{{ asset('public/website/assets/images/Wishlistnew.png') }}" class="img-fluid empWishListimg"
                        alt="Empty Wishlist">
                </div>
            @else
                <div class="col-md-12 mb-1">
                    <h4>My Wishlist</h4>
                </div>

                @foreach ($wishlists as $wishlist)
                    @php
                        $images = json_decode($wishlist->image, true);
                    @endphp
                    <div class="col-md-3">
                        <div class="product product-7">
                            <input type="hidden" class="variation" name="variation" value="{{ $wishlist->variation }}">
                            <input type="hidden" class="product_id" name="product_id" value="{{ $wishlist->product_id }}">
                            <figure class="product-media">
                                @if ($wishlist->discount_type == 'percent' && $wishlist->discount > 0)
                                    <span class="product-label label-new">{{ round($wishlist->discount, 0) }}% off</span>
                                @elseif($wishlist->discount_type == 'flat' && $wishlist->discount > 0)
                                    <span class="product-label label-new">₹{{ number_format($wishlist->discount, 0) }}
                                        off</span>
                                @endif
                                @if ($wishlist->free_delivery == 1)
                                    <span class="product-label product-label-two label-sale">Free Delivery</span>
                                @endif
                                <a href="{{ url('product/' . $wishlist->slug) }}">
                                    <img 
                                    {{-- src="{{ asset('storage/app/public/images/' . $images[0]) }}" --}}
                                    src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($images[0] ?? 'default.jpg') }}"
                                        alt="{{ $wishlist->name }}" class="product-image rounded-lg"
                                        style="max-width: 100%">
                                </a>
                            </figure>
                            <div class="product-body">
                                <div class="product-cat">
                                    <a href="#">{{ $wishlist->category ?? 'Uncategorized' }}</a>
                                </div>
                                <h5 style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;"
                                    class="mb-1" title="{{ $wishlist->name }}">
                                    <a href="{{ url('product/' . $wishlist->slug) }}">{{ $wishlist->name }}</a>
                                </h5>
                                <h6 class="product-type mb-0">{{ $wishlist->variation }}</h6>
                                <!-- <p class="mb-0" style="color:#FF7373;">{{ $wishlist->quantity }} Units left</p> -->
                                @if ($wishlist->quantity == 0)
                                    <p class="text-red-500">Out of Stock</p>
                                @elseif ($wishlist->quantity <= 10)
                                    <p class="mb-0" style="color:#FF7373;">{{ $wishlist->quantity }} Units Left</p>
                                @endif
                                <div class="d-flex ml-auto">
                                    <div class="product-price">
                                        ₹ {{ $wishlist->listed_price }}
                                        @if ($wishlist->discount_percent)
                                            <span class="price-cut">₹ {{ $wishlist->variant_mrp }}</span>
                                        @endif
                                    </div>
                                    <div class="action-buttons">
                                        <button type="button" class="btnAddToCart" title="Add to cart"
                                            data-slug="{{ $wishlist->slug }}">
                                            <i class="fa fa-shopping-cart"></i>
                                        </button>
                                        <button type="button" class="btnDelete" title="Remove from wishlist"
                                            data-slug="{{ $wishlist->slug }}">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        @if ($recently_viewed->isNotEmpty())
            <div class="row mt-5 recViewWrapper">
                <div class="col-md-12">
                    <div class="product-details-top">
                        <div class="heading heading-flex mb-3">
                            <div class="heading-left">
                                <h2 class="title">Recently Viewed Items</h2>
                            </div>
                        </div>
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                            data-owl-options='{
                                        "nav": false, 
                                        "dots": true,
                                        "margin": 20,
                                        "loop": false,
                                        "responsive": {
                                            "0": {"items":2},
                                            "480": {"items":2},
                                            "768": {"items":3},
                                            "992": {"items":4},
                                            "1200": {"items":4, "nav": true, "dots": false}
                                        }
                                    }'>
                            @foreach ($recently_viewed as $item)
                                <div class="product product-7">
                                    <figure class="product-media">
                                        @if ($item->discount_type == 'percent' && $item->discount > 0)
                                            <span class="product-label label-new">{{ round($item->discount, 0) }}%
                                                off</span>
                                        @elseif($item->discount_type == 'flat' && $item->discount > 0)
                                            <span class="product-label label-new">₹{{ number_format($item->discount, 0) }}
                                                off</span>
                                        @endif



                                        <a href="{{ url('product/' . $item->slug) }}">
                                            <img src="{{ asset('storage/app/public/images/' . $item->thumbnail_image) }}"
                                                alt="{{ $item->name }}" class="product-image"
                                                style="max-width: 100%!important;">
                                        </a>
                                    </figure>
                                    <div class="product-body">
                                        <div class="product-cat">
                                            <a href="#">{{ $item->category ?? 'Uncategorized' }}</a>
                                        </div>
                                        <h3 class="product-title">
                                            <a href="{{ url('product/' . $item->slug) }}">{{ $item->name }}</a>
                                        </h3>
                                        <div class="product-price">
                                            ₹ {{ $item->listed_price }}
                                            @if ($item->discount)
                                                <span class="price-cut">₹ {{ $item->variant_mrp }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
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

        .btnAddToCart {
            width: 35px;
            height: 26px;
            line-height: 23px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-left: auto;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            /* vertical align */
            margin-left: auto;
        }

        .action-buttons>* {
            margin-right: 10px;
            /* 20px space between buttons */
        }

        .action-buttons>*:last-child {
            margin-right: 0;
            /* no margin on the last button */
        }
    </style>
    <script>
        $(document).ready(function() {
            // Using class selector for multiple Add to Cart buttons
            $(document).ready(function() {
                $(".btnAddToCart").on("click", function() {
                    // Get parent product card
                    const parent = $(this).closest('.product');
                    // Get variation and product ID from the current product card
                    const variation = parent.find('.variation').val();
                    const productId = parent.find('.product_id').val();
                    console.log(variation);
                    console.log(productId);
                    $.ajax({
                        url: "{{ route('cart.add_1') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            variant: variation,
                            product: productId
                        },
                        success: function(response) {
                            alert(response.message);
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                alert("You must be logged in to add to cart.");
                            } else {
                                alert("Something went wrong.");
                            }
                        }
                    });
                });
            });
            $(".btnDelete").on("click", function() {
                const slug = $(this).data('slug');
                if (!slug) {
                    alert('Product slug missing.');
                    return;
                }
                if (confirm('Are you sure you want to remove this item from wishlist?')) {
                    $.ajax({
                        url: "{{ route('delete-wishlist-1') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            slug: slug
                        },
                        success: function(response) {
                            alert(response.message);
                            // Optionally remove the wishlist item from DOM
                            // $(this).closest('.product').remove();  <-- careful with "this" inside success
                            location.reload(); // simple way: refresh after delete
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                alert("You must be logged in to remove from wishlist.");
                            } else {
                                alert("Something went wrong while removing from wishlist.");
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
