@extends('layouts.back-end.common_seller_1')
@section('content')
    @push('head')
        @php
            // Ensure variables are at least defined (in case controller misses them)
            $category = $category ?? null;
            $subCategory = $subCategory ?? null;
            $subSubCategory = $subSubCategory ?? null;

            $titleParts = [];
            $descriptionParts = [];
            $metaTitleParts = [];
            $images = [];

            foreach ([$category, $subCategory, $subSubCategory] as $cat) {
                if (!empty($cat?->name)) {
                    $titleParts[] = $cat->name;
                }
                if (!empty($cat?->meta_description)) {
                    $descriptionParts[] = $cat->meta_description;
                }
                if (!empty($cat?->meta_title)) {
                    $metaTitleParts[] = $cat->meta_title;
                }
                if (!empty($cat?->icon)) {
                    $images[] = secure_url('storage/category/' . $cat->icon);
                }
            }

            $pageTitle = implode(' - ', $titleParts);
            $metaDescription = \Illuminate\Support\Str::limit(strip_tags(implode(' ', $descriptionParts)), 160);
            $metaTitle = implode(' - ', $metaTitleParts);
            $images = array_unique($images);
            $canonicalUrl = url()->current();
            $defaultImage = secure_url('storage/category/default.png');
        @endphp

        <title>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">

        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:image" content="{{ $images[0] ?? $defaultImage }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        <!-- Twitter Meta -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $images[0] ?? $defaultImage }}">

        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "{{ $pageTitle }}",
            "itemListElement": [
                @foreach($products as $index => $product)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "url": "{{ url('product/' . $product->slug) }}"
                }@if(!$loop->last),@endif
                @endforeach
            ]
            }
        </script>
        <style>
            .banner-head.mb-5 img {
                min-width: 100% !important;
            }

            figure.product-media {
                border-radius: 20px;
                /* height: 280px; */
            }

            .card-body {
                padding: unset !important;
            }
        </style>
      
    @endpush


    <main class="main">
        <div class="page-content pb-0">
            <div class="container">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5 class="font-weight-bold">
                            {{ $subCategory->name ?? 'Products' }}
                            <span class="h6 text-dark ml-3 font-weight-light">
                                {{ $products->total() }} Products
                            </span>
                        </h5>
                    </div>
                </div>
                {{-- Scroll wrapper (fixed width for 6 cards) --}}
                <div class="borbtm">
                    {{-- <div class="row mt-1"> --}}
                        <div class="scrool-auto">
                             @foreach ($sub_sub_categories as $category)
                            <div class="sc-item">
                                <a href="{{ url('category/' . $category->slug) }}" class="text-decoration-none text-dark">
                                    <div class="card border-0" style="align-items: center;">
                                        {{-- Ensure icon exists before trying to display it --}}
                                        <img src="{{ asset('storage/category/' . $category->icon) }}"
                                            alt="{{ $category->name }}"
                                            style="width: 100%; aspect-ratio: 1/1; object-fit: cover;"
                                            class="card-img-top rounded-circle border">
                                        <div class="card-body" style="text-align:center;">
                                            <p>{{ $category->name }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            
                             @endforeach
                        </div>
                        {{-- <div>
                            @foreach ($sub_sub_categories as $category)
                                <div class="col-md-2" style="flex: 0 0 10.66%; max-width: 10.66%;">
                                    <a href="{{ url('category/' . $category->slug) }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card border-0" style="align-items: center;"> 
                                            <img src="{{ asset('storage/category/' . $category->icon) }}"
                                                alt="{{ $category->name }}"
                                                style="width: 100%; aspect-ratio: 1/1; object-fit: cover;"
                                                class="card-img-top rounded-circle border">
                                            <div class="card-body" style="text-align:center;">
                                                <p>{{ $category->name }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div> --}}
                    {{-- </div> --}}
                </div>
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
                            <!-- {{-- 1) Filter bar --}} -->
                            <ul id="filters" class="clearfix">
                                {{-- “Filter” button to open modal --}}
                                <li>
                                    <span class="filter" data-toggle="modal" data-target="#filterModal">
                                        <i class="fa fa-filter"></i> Filter
                                    </span>
                                </li>
                                {{-- Dynamically list each filter group title --}}
                                <div class="filters-wrapper">
                                    <ul class="filters-list">
                                        @foreach ($filters as $key => $filter)
                                            <li>
                                                {{-- add data-key so we know which pill to activate --}}
                                                <span class="filter" data-key="{{ $key }}">
                                                    {{ $filter['title'] }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </ul>
                        </div>
                        <style>
                            .filters-wrapper {
                                position: relative;
                                overflow-x: auto;
                                max-width: 100%;
                                -webkit-overflow-scrolling: touch;
                                -webkit-mask-image: linear-gradient(to right, black 90%, transparent 100%);
                                mask-image: linear-gradient(to right, black 90%, transparent 100%);
                                scrollbar-width: none;
                            }

                            .filters-wrapper::-webkit-scrollbar {
                                display: none;
                            }

                            .filters-list {
                                display: flex;
                                list-style: none;
                                padding: 0;
                                margin: 0;
                                width: max-content;
                            }

                            .filters-list li {
                                margin-right: 10px;
                                white-space: nowrap;
                                flex-shrink: 0;
                            }
                        </style>
                        </ul>
                        <div id="gallery">
                            @if ($products->count())
                                @foreach ($products as $product)
                                    <div class="gallery-item logo" style="display: inline-block;">
                                        <div class="inside" style="height: 100%;">
                                            <div class="product product-3" style="display: flex; flex-direction: column;">
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
                                                                : $product->thumbnail_image;
                                                        @endphp
                                                        <img src="{{ asset('storage/images/' . $firstImage) }}"
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
                                                                style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                                                {{ $product->name }}
                                                            </h5>
                                                        </a>

                                                        <!-- <p class="mb-0" style="color:#FF7373;">{{ $product->quantity ?? 0 }} Units left</p> -->
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
                                                        @if ($product->quantity <= 0)
                                                            <span class="text-red-500"
                                                                style="font-size: small;font-weight: 600;">Out of
                                                                Stock</apan>
                                                            @elseif ($product->quantity <= 10)
                                                                <span class="mb-0"
                                                                    style="color:#FF7373;font-size: small;font-weight: 600;">
                                                                    {{ $product->quantity }} Units Left</span>
                                                        @endif
                                                        <input type="hidden" name="variation" id="variation"
                                                            class="variation" value="{{ $product->variations }}">
                                                        @if ($product->quantity <= 0)
                                                            <a href="{{ url('product/' . $product->slug) }}"
                                                                class="btn w-100 border radius-1 rounded-lg mt-1"
                                                                style="box-shadow:0 0 5px 1px rgba(0, 0, 0, 0.1);">
                                                                Out of Stock
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn w-100 border radius-1 rounded-lg mt-1 add-to-cart-btn"
                                                                data-slug="{{ $product->id }}"
                                                                style="box-shadow:0 0 5px 1px rgba(0, 0, 0, 0.1);">
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
                                <p class="text-center">No products found for this category.</p>
                            @endif
                        </div><!--/gallery-->
                    </div><!--/gallery-wrap-->
                </div>
            </div>
        </div>
        </div><!-- End .page-content -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
        <div class="banner-head mb-5">
            <img style="height: 250px; object-fit: cover; width: 100%;"
                src="{{ asset('website/new/assets/images/banners/banner-4.jpg') }}" alt="" />
        </div>
    </main><!-- End .main -->
    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="filterForm" method="GET" action="{{ route('category', $slug ?? 'decor') }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">FILTERS</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="row no-gutters">
                            <div class="col-4">
                                <div class="nav nav-pills flex-column custom-nav-pills" id="v-pills-tab" role="tablist">
                                    @foreach ($filters as $key => $filter)
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="v-pills-{{ $key }}-tab" data-toggle="pill"
                                            data-target="#v-pills-{{ $key }}" type="button" role="tab"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $filter['title'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-8">
                                <div class="tab-content" id="v-pills-tabContent">
                                    @foreach ($filters as $key => $filter)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                            id="v-pills-{{ $key }}" role="tabpanel">
                                            <div class="p-3" style="height: 75vh; overflow-y: auto;">

                                                @if ($key === 'sort')
                                                    <label class="font-weight-bold mb-2 d-block">Price Range</label>

                                                    <div
                                                        class="range-values-display d-flex justify-content-between mb-2 px-1">
                                                        <span><strong>₹</strong><span
                                                                class="minPriceVal">{{ request('min_price', $priceRange['min']) }}</span></span>
                                                        <span><strong>₹</strong><span
                                                                class="maxPriceVal">{{ request('max_price', $priceRange['max']) }}</span></span>
                                                    </div>

                                                    <div class="range-container mb-3 position-relative">
                                                        <div class="range-track" id="rangeTrack"></div>
                                                        <input type="range" id="minPrice" name="min_price"
                                                            min="{{ $priceRange['min'] }}"
                                                            max="{{ $priceRange['max'] }}" class="range-slider">
                                                        <input type="range" id="maxPrice" name="max_price"
                                                            min="{{ $priceRange['min'] }}"
                                                            max="{{ $priceRange['max'] }}" class="range-slider">
                                                    </div>

                                                    <div class="range-values mt-3">

                                                        <input type="number" id="minPriceInput"
                                                            min="{{ $priceRange['min'] }}"
                                                            max="{{ $priceRange['max'] }}"
                                                            class="form-control text-center">

                                                        <input type="number" id="maxPriceInput"
                                                            min="{{ $priceRange['min'] }}"
                                                            max="{{ $priceRange['max'] }}"
                                                            class="form-control text-center">
                                                    </div>
                                                    <hr>
                                                @endif

                                                <ul class="list-group border-0">
                                                    @foreach ($filter['options'] as $optKey => $optVal)
                                                        @php
                                                            $isList = is_numeric($optKey);
                                                            $value = $isList ? $optVal : $optKey;
                                                            $label = $optVal;
                                                            $name = $value;
                                                            $count = null;
                                                            $hexCode = null;

                                                            if (
                                                                preg_match(
                                                                    '/^(.*?)\s*\((#[0-9A-Fa-f]{6})\)\s*\((\d+)\)$/',
                                                                    $value,
                                                                    $m,
                                                                )
                                                            ) {
                                                                $name = trim($m[1]);
                                                                $hexCode = trim($m[2]);
                                                                $count = trim($m[3]);
                                                            } elseif (preg_match('/^(.*?)\s*\((\d+)\)$/', $value, $m)) {
                                                                $name = trim($m[1]);
                                                                $count = trim($m[2]);
                                                            }
                                                            $cnt = $count;

                                                        @endphp

                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center border-0">
                                                            <input type="checkbox" name="filters[{{ $key }}][]"
                                                                value="{{ $name }}"
                                                                class="form-check-input ml-0 mt-0"
                                                                {{ collect(request('filters.' . $key, []))->contains($name) ? 'checked' : '' }}>
                                                            <span class="pl-4 d-flex align-items-center">
                                                                @if ($hexCode)
                                                                    <span class="ml-2"
                                                                        style="display:inline-block; width:16px; height:16px; background-color: {{ $hexCode }}; border-radius: 50%; border: 1px solid #ccc;"></span>
                                                                @endif
                                                                {{ $key === 'sort' ? $label : $name }}
                                                            </span>

                                                            {{-- @if ($cnt !== null)
                                                                <span class="text-muted">({{ $cnt }})</span>
                                                            @endif --}}
                                                        </li>
                                                    @endforeach
                                                </ul>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="resetFilters" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary applyFilter">Apply Filters</button>
                    </div>
                </div>
            </form>
            <style>
                .range-container {
                    position: relative;
                    height: 40px;
                }

                .range-slider {
                    -webkit-appearance: none;
                    width: 100%;
                    height: 4px;
                    background: transparent;
                    position: absolute;
                    top: 18px;
                    pointer-events: none;
                    z-index: 1;
                }

                .range-slider::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    height: 18px;
                    width: 18px;
                    background: #ed672f;
                    border: 2px solid white;
                    border-radius: 50%;
                    cursor: pointer;
                    pointer-events: all;
                    z-index: 2;
                    position: relative;
                }

                .range-track {
                    position: absolute;
                    top: 18px;
                    height: 4px;
                    width: 100%;
                    background: #ddd;
                    border-radius: 5px;
                    z-index: 0;
                }

                .range-values {
                    display: flex;
                    justify-content: space-between;
                    gap: 10px;
                }

                .range-values input {
                    width: 100px;
                }

                .range-values-display {
                    font-weight: bold;
                    font-size: 15px;
                }
            </style>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const minSlider = document.getElementById("minPrice");
                    const maxSlider = document.getElementById("maxPrice");
                    const minInput = document.getElementById("minPriceInput");
                    const maxInput = document.getElementById("maxPriceInput");
                    const minOutput = document.querySelectorAll(".minPriceVal");
                    const maxOutput = document.querySelectorAll(".maxPriceVal");
                    const rangeTrack = document.getElementById("rangeTrack");

                    const minLimit = parseInt(minSlider.min);
                    const maxLimit = parseInt(maxSlider.max);

                    function clamp(val, min, max) {
                        return Math.max(min, Math.min(val, max));
                    }

                    function syncAll(minVal, maxVal) {
                        minVal = clamp(minVal, minLimit, maxLimit);
                        maxVal = clamp(maxVal, minLimit, maxLimit);
                        if (minVal > maxVal)[minVal, maxVal] = [maxVal, minVal];

                        minSlider.value = minVal;
                        maxSlider.value = maxVal;
                        minInput.value = minVal;
                        maxInput.value = maxVal;

                        minOutput.forEach(el => el.textContent = minVal);
                        maxOutput.forEach(el => el.textContent = maxVal);

                        const percent1 = ((minVal - minLimit) / (maxLimit - minLimit)) * 100;
                        const percent2 = ((maxVal - minLimit) / (maxLimit - minLimit)) * 100;

                        rangeTrack.style.background = `linear-gradient(
                            to right,
                            #ddd ${percent1}%,
                            #ed672f ${percent1}%,
                            #ed672f ${percent2}%,
                            #ddd ${percent2}%
                        )`;
                    }

                    minSlider.addEventListener("input", () => {
                        syncAll(parseInt(minSlider.value), parseInt(maxSlider.value));
                    });

                    maxSlider.addEventListener("input", () => {
                        syncAll(parseInt(minSlider.value), parseInt(maxSlider.value));
                    });

                    ["input", "change", "keyup"].forEach(evt => {
                        minInput.addEventListener(evt, () => {
                            syncAll(parseInt(minInput.value || minLimit), parseInt(maxInput.value ||
                                maxLimit));
                        });
                        maxInput.addEventListener(evt, () => {
                            syncAll(parseInt(minInput.value || minLimit), parseInt(maxInput.value ||
                                maxLimit));
                        });
                    });

                    document.getElementById("resetFilters")?.addEventListener("click", function() {
                        const form = document.getElementById("filterForm");

                        // Reset checkboxes
                        form.querySelectorAll("input[type='checkbox']").forEach(cb => cb.checked = false);

                        // Reset range sliders
                        const minDefault = parseInt(document.getElementById("minPrice").getAttribute("min"));
                        const maxDefault = parseInt(document.getElementById("maxPrice").getAttribute("max"));

                        document.getElementById("minPrice").value = minDefault;
                        document.getElementById("maxPrice").value = maxDefault;
                        document.getElementById("minPriceInput").value = minDefault;
                        document.getElementById("maxPriceInput").value = maxDefault;

                        // Reset displayed values
                        document.querySelectorAll(".minPriceVal").forEach(el => el.textContent = minDefault);
                        document.querySelectorAll(".maxPriceVal").forEach(el => el.textContent = maxDefault);

                        // Redirect to base category URL (clears query params)
                        window.location.href = form.getAttribute("action");
                    });
                    syncAll(parseInt(minInput.value || minLimit), parseInt(maxInput.value || maxLimit));
                });
            </script>
        </div>
    </div>
    {{-- {!! $subCategory->page_content !!} --}}
    <style>
        .pagination .page-item:first-child .page-link {
            background-color: #d5d5d5;
        }

        .pagination .page-item:last-child .page-link {
            background-color: #d5d5d5;
        }

        .nav-scroll-container {
            width: 100%;
            height: 77vh;
            overflow-y: scroll;
            scrollbar-width: none;
        }

        .nav-scroll-container::-webkit-scrollbar {
            width: 6px;
        }

        .nav-scroll-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .custom-nav-pills {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            flex-wrap: nowrap;
        }

        .custom-nav-pills .nav-link {
            width: 100%;
            white-space: wrap;
        }
    </style>
    
    

    <script>
        document.getElementById('resetFilters').addEventListener('click', function() {
            document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.getElementById('filterForm').submit();
        });
    </script>

    <script>
        // document.getElementById('filterForm').addEventListener('submit', function (e) {
        //     e.preventDefault();
        //     console.log();

        // });
    </script>
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
                        product: productId,
                        type: 0
                    },
                    success: function(response) {
                        toastr.success('Product added to cart successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            toastr.error("You must be logged in to add to cart.");
                        } else {
                            toastr.error("Something went wrong.");
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {

            $('.filters-list').on('click', '.filter', function() {

                var key = $(this).data('key');

                var $modal = $('#filterModal');

                $modal.modal('show')
                    .one('shown.bs.modal', function() {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {

                            var tabEl = document.getElementById('v-pills-' + key + '-tab');

                            new bootstrap.Tab(tabEl).show();

                        } else {

                            $('#v-pills-' + key + '-tab').tab('show');

                        }
                    });
            });
        });
        // 25.9.25
        //         const slider = document.querySelector('.scrool-auto');
        // let isDown = false;
        // let startX;
        // let scrollLeft;

        // slider.addEventListener('mousedown', (e) => {
        //   isDown = true;
        //   slider.classList.add('active'); // optional for styling
        //   startX = e.pageX - slider.offsetLeft;
        //   scrollLeft = slider.scrollLeft;
        // });

        // slider.addEventListener('mouseleave', () => {
        //   isDown = false;
        //   slider.classList.remove('active');
        // });

        // slider.addEventListener('mouseup', () => {
        //     console.log('up');
        //   isDown = false;
        //   slider.classList.remove('active');
        // });

        // slider.addEventListener('mousemove', (e) => {
        //     // console.log(e, isDown);
        //   if (!isDown) return;
        //   e.preventDefault();
        //   const x = e.pageX - slider.offsetLeft;
        //   const walk = (x - startX) * 1; // scroll-fast multiplier
        //   slider.scrollLeft = scrollLeft - walk;
        // });

        $('.scrool-auto').slick({
            dots: false,
            arrows:true,
            infinite: true,
            speed: 300,
            slidesToShow: 10,
            slidesToScroll: 1,
              responsive: [
                {
                  breakpoint: 1200,
                  settings: {
                    slidesToShow: 7,
                  }
                },
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 4,
                  }
                },
                {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                  }
                } 
              ]
        });
    </script>

@endsection
