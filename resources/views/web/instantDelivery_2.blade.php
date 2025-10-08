@extends('layouts.back-end.common_seller_1')

@section('content')
    <style>
        img {
            max-width: 100% !important;
        }
    </style>
    @if ($instant_products->count())
        <main class="main">

            <div class="page-content pb-0">

                <div class="container">
                    <div class="row mt-3 mb-3">
                        <div class="col-md-8">
                            <a href="javascript:void(0);" class="instant-del">
                                <img src="{{ asset('website/new/assets/images/instant-del.png') }}" alt="" />
                                4-Hour Instant Delivery
                            </a>

                            <a href="javascript:void(0);" class="regular-del">
                                <img src="{{ asset('website/new/assets/images/regular-del.png') }}" alt="" />
                                Regular Delivery
                            </a>
                        </div>

                        <div class="col-md-4">
                            <form class="form-inline text-login mt-1" onsubmit="return false;">
                                <div class="form-group mb-0" style="width: 100%; position: relative;">
                                    <input type="text" id="pincodeInput2" class="form-control w-100"
                                        placeholder="Enter Pincode" style="padding-right: 105px;">
                                    <div style="position: absolute; right: 0; top: 0;">
                                        <i class="fa fa-map-marker tickLocation" aria-hidden="true"></i>
                                        <button type="button" class="btn" style="min-width: auto;"
                                            onclick="goToPincode()">Change</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <script>
                            function goToPincode() {
                                const pincode = document.getElementById('pincodeInput2').value.trim();
                                if (pincode) {
                                    window.location.href = `/ic/instant_2/${pincode}`;
                                } else {
                                    alert("Please enter a valid pincode.");
                                }
                            }
                        </script>

                    </div>
                </div>

                <div class="banner-head">
                    <img style="height: 250px;object-fit: cover;width: 100%;"
                        src="{{ asset('website/new/assets/images/banners/banner-3.jpg') }}" alt="" />
                </div>

                <div class="container">
                    <div class="row mt-3 mb-2">
                        <div class="col-lg-12">
                            <div class="gallery-wrap">
                                <ul id="filters" class="clearfix">
                                    <li><span class="filter" data-toggle="modal" data-target="#filterModal"><i
                                                class="fa fa-filter"></i> Filter</span></li>
                                    <li><span class="filter">Sort By</span></li>
                                    <li><span class="filter">Product Type</span></li>
                                    <li><span class="filter">Material</span></li>
                                    <li><span class="filter">Color</span></li>
                                </ul>

                                <div id="gallery">
                                    @foreach ($instant_products as $product)
                                        <div class="gallery-item logo"
                                            style="width: 250px; height: 400px; display: inline-block;">
                                            <div class="inside" style="height: 100%;">
                                                <div class="product product-3"
                                                    style="height: 100%; display: flex; flex-direction: column;">
                                                    <figure class="product-media"
                                                        style="width: 100%; aspect-ratio: 1/1; overflow: hidden;">

                                                        @if ($product->sku_discount)
                                                            <span class="product-label label-top">
                                                                @if ($product->sku_discount_type === 'flat')
                                                                    Rs. {{ number_format($product->sku_discount, 0) }} Off
                                                                @else
                                                                    {{ number_format($product->sku_discount, 0) }}% Off
                                                                @endif
                                                            </span>
                                                        @endif

                                                        <a href="{{ url('product/' . $product->slug) }}">
                                                            @php
                                                                $images = json_decode($product->image, true);
                                                                $firstImage = isset($images[0])
                                                                    ? $images[0]
                                                                    : 'default.jpg';
                                                            @endphp

                                                            <img src="{{ asset('storage/images/' . $firstImage) }}"
                                                                alt="{{ $product->name }}" class="product-image rounded-lg"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.onerror=null;this.src='{{ asset('website/new/assets/images/error-image.png') }}';">
                                                        </a>
                                                    </figure>

                                                    <div class="product-body"
                                                        style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; padding-top: 10px;">
                                                        <div>
                                                            <div class="product-cat">
                                                                <a
                                                                    href="#">{{ $product->color_name ?? 'Category' }}</a>
                                                            </div>
                                                            <a href="{{ url('product/' . $product->slug) }}">
                                                                <h5
                                                                    style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                                                    {{ $product->name }}
                                                                </h5>
                                                            </a>
                                                            <h6 class="product-type mb-0">
                                                                {{ $product->product_type ?? 'Type' }}</h6>
                                                            <p class="mb-0" style="color:#FF7373;">
                                                                {{ $product->quantity ?? 0 }} Units left</p>
                                                        </div>

                                                        <div>
                                                            <div class="d-flex ml-auto">
                                                                <div class="product-price">
                                                                    Rs. {{ $product->listed_price }}
                                                                    @if ($product->variant_mrp > $product->listed_price)
                                                                        <span class="price-cut">Rs.
                                                                            {{ $product->variant_mrp }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            {{-- <a href="javascript:void(0);" 
                                                       class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn" 
                                                       data-slug="{{ $product->slug }}">
                                                        Add to Cart
                                                    </a> --}}
                                                            <input type="hidden" name="variation" id="variation"
                                                                class="variation" value="{{ $product->sku_variation }}">

                                                            <a href="javascript:void(0);"
                                                                class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn"
                                                                data-slug="{{ $product->id }}"
                                                                style="box-shadow:0 0 5px 1px rgba(0, 0, 0, 0.1);">
                                                                Add to Cart
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="mt-4 d-flex justify-content-center">
                                        {{ $instant_products->links() }}
                                    </div>
                                </div><!--/gallery-->
                            </div><!--/gallery-wrap-->
                        </div>
                    </div>
                </div>

            </div><!-- End .page-content -->

            <div class="banner-head mb-5">
                <img style="height: 250px;object-fit: cover;width: 100%;"
                    src="{{ asset('website/new/assets/images/banners/banner-4.jpg') }}" alt="" />
            </div>

        </main><!-- End .main -->
    @else
        <div class="text-center mt-4">
            <img src="{{ asset('website/new/assets/images/coming soon to your city_1.webp') }}"
                alt="No delivery in this area" style="max-width:400px; width:33%; margin:auto;">
            {{-- <p class="mt-2 text-danger">Sorry! No instant delivery available for this pincode.</p> --}}
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        ...
    </div>

    {{-- <script>
    $(document).ready(function() {
        $('.add-to-cart-btn').on('click', function() {
            const slug = $(this).data('slug');
            const variation = $(this).closest('.product').find('.variation').val();

            $.ajax({
                url: "{{ route('cart.add_1') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    variant: variation,
                    product: slug
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
</script> --}}

    <script>
        $(document).ready(function() {
            $('.add-to-cart-btn').on('click', function() {
                const productId = $(this).data('slug');

                // Get closest product card and then find the variation input inside it
                const variation = $(this).closest('.product').find('.variation').val();

                console.log("Variation:", variation);
                console.log("Product ID:", productId);
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
    </script>

@endsection
