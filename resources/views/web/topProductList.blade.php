@extends('layouts.back-end.common_seller_1')



@section('content')


    <style>
        .banner-head img {
            max-width: 100% !important;
        }
    </style>
    <main class="main">
        <div class="page-content pb-0">
            <div class="container">
                {{-- Brand title --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="fw-bold">
                            Top Products
                        </h5>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row mt-3 mb-2">
                    <div class="col-lg-12">
                        <div class="gallery-wrap">
                            <ul id="filters" class="clearfix">
                                <li>
                                    <span class="filter" data-toggle="modal" data-target="#filterModal">
                                        <i class="fa fa-filter"></i> Filter
                                    </span>
                                </li>
                                <li><span class="filter">Sort By</span></li>
                                <li><span class="filter">Product Type</span></li>
                                <li><span class="filter">Material</span></li>
                                <li><span class="filter">Color</span></li>
                            </ul>

                            <div id="gallery">
                                @if ($products->count())
                                    @foreach ($products as $product)
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

                                                            <img src="{{ asset('storage/app/public/images/' . $firstImage) }}"
                                                                alt="{{ $product->name }}" class="product-image rounded-lg"
                                                                style="width: 100%; height: 100%; object-fit: cover;">

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

                                                            <a href="javascript:void(0);"
                                                                class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn"
                                                                data-slug="{{ $product->slug }}">

                                                                Add to Cart

                                                            </a>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-center">No products found for this Brand.</p>
                                @endif
                            </div><!--/gallery-->
                        </div><!--/gallery-wrap-->
                    </div>
                </div>
            </div>

        </div><!-- End .page-content -->

        <div class="banner-head mb-5">
            <img style="height: 250px; object-fit: cover; width: 100%;"src="{{ asset('public/website/new/assets/images/banners/banner-4.jpg') }}"
                alt="" />
        </div>
    </main><!-- End .main -->

    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">FILTERS</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body pl-0 pt-0 pb-0">

                    <div class="row">

                        <div class="col-4">

                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">

                                <button class="nav-link active" id="v-pills-size-tab" data-toggle="pill"
                                    data-target="#v-pills-size" type="button" role="tab" aria-controls="v-pills-size"
                                    aria-selected="true">Size</button>

                                <button class="nav-link" id="v-pills-sort-tab" data-toggle="pill"
                                    data-target="#v-pills-sort" type="button" role="tab" aria-controls="v-pills-sort"
                                    aria-selected="false">Sort By</button>

                                <button class="nav-link" id="v-pills-type-tab" data-toggle="pill"
                                    data-target="#v-pills-type" type="button" role="tab" aria-controls="v-pills-type"
                                    aria-selected="false">Product Type Style</button>

                                <button class="nav-link" id="v-pills-material-tab" data-toggle="pill"
                                    data-target="#v-pills-material" type="button" role="tab"
                                    aria-controls="v-pills-material" aria-selected="false">Material</button>

                                <button class="nav-link" id="v-pills-sub-tab" data-toggle="pill"
                                    data-target="#v-pills-sub" type="button" role="tab" aria-controls="v-pills-sub"
                                    aria-selected="false">Sub Type</button>

                                <button class="nav-link" id="v-pills-color-tab" data-toggle="pill"
                                    data-target="#v-pills-color" type="button" role="tab"
                                    aria-controls="v-pills-color" aria-selected="false">Color</button>



                            </div>

                        </div>

                        <div class="col-8">

                            <div class="tab-content" id="v-pills-tabContent">

                                <div class="tab-pane fade show active" id="v-pills-size" role="tabpanel"
                                    aria-labelledby="v-pills-size-tab">

                                    <ul class="list-group">

                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center border-0">

                                            <input type="checkbox" class="form-check-input ml-0 mt-0"> <span
                                                class="d-inline-block pl-5">A list item</span>

                                            <span class="">(14)</span>

                                        </li>

                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center border-0">

                                            <input type="checkbox" class="form-check-input ml-0 mt-0"> <span
                                                class="d-inline-block pl-5">A list item</span>

                                            <span class="">(2)</span>

                                        </li>

                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center border-0">

                                            <input type="checkbox" class="form-check-input ml-0 mt-0"> <span
                                                class="d-inline-block pl-5">A list item</span>

                                            <span class="">(1)</span>

                                        </li>

                                    </ul>

                                </div>

                                <div class="tab-pane fade" id="v-pills-sort" role="tabpanel"
                                    aria-labelledby="v-pills-sort-tab">B</div>

                                <div class="tab-pane fade" id="v-pills-type" role="tabpanel"
                                    aria-labelledby="v-pills-type-tab">C</div>

                                <div class="tab-pane fade" id="v-pills-material" role="tabpanel"
                                    aria-labelledby="v-pills-material-tab">D</div>

                                <div class="tab-pane fade" id="v-pills-sub" role="tabpanel"
                                    aria-labelledby="v-pills-sub-tab">E</div>

                                <div class="tab-pane fade" id="v-pills-color" role="tabpanel"
                                    aria-labelledby="v-pills-color-tab">F</div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer text-login">

                    <button type="button" class="btn btn-info" data-dismiss="modal">Reset</button>

                    <button type="button" class="btn btn-primary">View Details</button>

                </div>

            </div>

        </div>

    </div>

    <script>
        $(document).ready(function() {

            $('.add-to-cart-btn').on('click', function() {

                const slug = $(this).data('slug');

                $.ajax({

                    url: "{{ route('cart.add_1') }}",

                    type: "POST",

                    data: {

                        _token: '{{ csrf_token() }}',

                        product: {
                            slug: slug
                        }

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
