@extends('layouts.back-end.common_seller_1')
@section('content')
    <link rel="stylesheet" href="{{ asset('public/website/assets/css/product.css') }}">
    <style>
        a.btn.w-100.border.radius-1.rounded-lg.mt-1.add-to-cart-btn {
            background: #e46725;
            color: #fff;
            width: 135px !important;
            border-radius: 51px;
            height: 36px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <main class="main">
        <div class="page-content pb-0">
            <div class="container">
                {{-- Brand title --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="fw-bold">
                            @if (isset($brand))
                                Top Bronds
                            @else
                                More From Store
                            @endif
                            <span class="h6 text-dark ml-3 font-weight-light">
                                {{ $products->total() }} Products
                            </span>
                        </h5>
                    </div>
                </div>
                {{-- Scroll wrapper for card --}}
                @if (isset($brand))
                    <div class="overflow-auto mt-2 pb-2"
                        style="border-bottom: 1px dashed #999; scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="d-flex" style="gap: 1rem; min-width: 100%;">
                            <div class="card text-center border-0" style="width: 115px;">
                                <a href="{{ url('products_2/' . $brand->id) }}" class="text-decoration-none text-dark">
                                    <img 
                                    {{-- src="{{ asset('storage/app/public/brand/' . $brand->image) }}" --}}
                                    src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . ($brand->image ?? 'default.jpg') }}"
                                        alt="{{ $brand->name }}" class="card-img-top rounded-circle border"
                                        style="aspect-ratio: 1/1; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <p class="mb-0">{{ $brand->name }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
            </div>
            @endif
            {{-- @if (!isset($brand))
                <img src="{{ asset('public/website/new/assets/images/banners/banner-3.jpg') }}" style="max-width: 100%;">
            @endif --}}
            {{-- Hide scrollbar (WebKit only) --}}
            <style>
                div[style*="overflow-x: auto"]::-webkit-scrollbar {
                    display: none;
                }
            </style>
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
                            @if ($products->total())
                                @foreach ($products as $product)
                                    <div class="gallery-item logo">
                                        <div class="inside" style="height: 100%;">
                                            <div class="product product-3"
                                                style="height: 100%; display: flex; flex-direction: column;">
                                                <figure class="product-media"
                                                    style="width: 100%; aspect-ratio: 1/1; overflow: hidden;">
                                                    @if ($product->sku_discount)
                                                        <span class="product-label label-top">
                                                            @if ($product->sku_discount_type === 'flat')
                                                                ₹ {{ number_format($product->sku_discount, 0) }} Off
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
                                                        <img 
                                                        {{-- src="{{ asset('storage/app/public/images/' . $firstImage) }}" --}}
                                                        src="{{ rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($firstImage ?? 'default.jpg', '/') }}"
                                                            alt="{{ $product->name }}" class="product-image rounded-lg"
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    </a>
                                                </figure>
                                                <div class="product-body"
                                                    style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; padding-top: 10px;">
                                                    <div>
                                                        <div class="product-cat">
                                                            <a href="#">{{ $product->color_name ?? 'Category' }}</a>
                                                        </div>
                                                        <a href="{{ url('product/' . $product->slug) }}">
                                                            <h5
                                                                style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                                                {{ $product->name }}
                                                            </h5>
                                                        </a>
                                                        <p class="mb-0" style="color:#FF7373;">
                                                            {{ $product->quantity ?? 0 }} Units left</p>
                                                    </div>
                                                    <div>
                                                        <div class="d-flex ml-auto">
                                                            <div class="product-price">
                                                                ₹ {{ $product->listed_price }}
                                                                @if ($product->variant_mrp > $product->listed_price)
                                                                    <span class="price-cut">₹
                                                                        {{ $product->variant_mrp }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        {{-- <a href="javascript:void(0);"
                                                            class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn"
                                                            data-slug="{{ $product->slug }}">
                                                            Add to Cart
                                                        </a> --}}
                                                        @php
                                                            $alreadyAddeds = \App\Model\Cart::where(
                                                                'user_id',
                                                                auth()->id(),
                                                            )
                                                                ->where('product_id', $product->id)
                                                                ->exists();
                                                        @endphp
                                                        {{-- @dd($alreadyAddeds); --}}
                                                        @if ($alreadyAddeds)
                                                            <button id="cart" class="view-cart">
                                                                View Cart
                                                            </button>
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn"
                                                                data-id="{{ $product->id }}"
                                                                data-variant="{{ $product->variation ?? '' }}">
                                                                Add to Cart
                                                            </a>
                                                        @endif
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
        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
        <div class="banner-head mb-5">
            <center>
                <img style="height: 250px; object-fit: cover; max-width: 100%;"src="{{ asset('public/website/new/assets/images/banners/banner-4.jpg') }}"
                    alt="" />
            </center>
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
                                    data-target="#v-pills-sort" type="button" role="tab"
                                    aria-controls="v-pills-sort" aria-selected="false">Sort By</button>
                                <button class="nav-link" id="v-pills-type-tab" data-toggle="pill"
                                    data-target="#v-pills-type" type="button" role="tab"
                                    aria-controls="v-pills-type" aria-selected="false">Product Type Style</button>
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

            // ADD TO CART BUTTON HANDLER
            $('.add-to-cart-btn').on('click', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const product_id = $btn.data('id');
                const variant = $btn.data('variant');

                // if button already converted to View Cart
                if ($btn.hasClass('view-cart')) {
                    window.location.href = "{{ url('/cart') }}";
                    return;
                }

                $.ajax({
                    url: "{{ route('cart.add_1') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        product: product_id,
                        variant: variant,
                        type: 0
                    },
                    beforeSend: function() {
                        $btn.text('Adding...').prop('disabled', true);
                    },
                    success: function(response) {
                        location.reload();
                        toastr.success('Added to cart successfully');
                        $btn.text('View Cart')
                            .addClass('view-cart')
                            .removeClass('add-to-cart-btn')
                            .prop('disabled', false);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).text('Add to Cart');
                        if (xhr.status === 401) {
                            alert("You must be logged in to add to cart.");
                        } else {
                            alert("Something went wrong. Please try again.");
                        }
                    }
                });
            });

            // VIEW CART REDIRECT (for dynamically updated button)
            $(document).on('click', '.view-cart', function() {
                window.location.href = "{{ url('/cart') }}";
            });
        });
    </script>

@endsection
