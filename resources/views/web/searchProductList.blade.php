@extends('layouts.back-end.common_seller_1')

@section('content')

<main class="main">

    <div class="page-content pb-0">

    <div class="container">
    {{-- Brand title --}}
    <div class="row mt-4">
        <div class="col-12">
        <h5 class="fw-bold">
    <span style="color: #1d4587;">
        {{ $products->count() }} 
    </span>
    products found matching 
    <span style="color: #e46725;">
        '{{ $tag }}'
    </span>
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
                            @if($products->count())
                                @foreach($products as $product)
                                    <div class="gallery-item logo" style="width: 250px; height: 400px; display: inline-block;">
                                        <div class="inside" style="height: 100%;">
                                            <div class="product product-3" style="height: 100%; display: flex; flex-direction: column;">
                                                <figure class="product-media" style="width: 100%; aspect-ratio: 1/1; overflow: hidden;">
                                                    @if($product->sku_discount)
                                                        <span class="product-label label-top">
                                                            @if($product->sku_discount_type === 'flat')
                                                                Rs. {{ number_format($product->sku_discount,0) }} Off
                                                            @else
                                                                {{ number_format($product->sku_discount,0) }}% Off
                                                            @endif
                                                        </span>
                                                    @endif

                                                    <a href="{{ url('product/' . $product->slug) }}">
                                                        @php
                                                            $images = json_decode($product->image, true);
                                                            $firstImage = isset($images[0]) ? $images[0] : 'default.jpg';
                                                        @endphp
                                                        <img src="{{ asset('storage/images/' . $firstImage) }}"
                                                             alt="{{ $product->name }}"
                                                             class="product-image rounded-lg"
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    </a>
                                                </figure>

                                                <div class="product-body" style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; padding-top: 10px;">
                                                    <div>
                                                        <div class="product-cat">
                                                            <a href="#">{{ $product->color_name ?? 'Category' }}</a>
                                                        </div>
                                                        <a href="{{ url('product/' . $product->slug) }}">
                                                            <h5 style="font-weight: 300; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                                                {{ $product->name }}
                                                            </h5>
                                                        </a>
                                                       
                                                        <p class="mb-0" style="color:#FF7373;">{{ $product->quantity ?? 0 }} Units left</p>
                                                    </div>

                                                    <div>
                                                        <div class="d-flex ml-auto">
                                                            <div class="product-price">
                                                                Rs. {{ $product->listed_price }}
                                                                @if($product->variant_mrp > $product->listed_price)
                                                                    <span class="price-cut">Rs. {{ $product->variant_mrp }}</span>
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
                                <p class="text-center">No products found for {{$tag}}.</p>
                            @endif
                        </div><!--/gallery-->

                    </div><!--/gallery-wrap-->
                </div>
            </div>
        </div>

    </div><!-- End .page-content -->

    <div class="banner-head mb-5">
        <img style="height: 250px; object-fit: cover; width: 100%;"src="{{ asset('website/new/assets/images/banners/banner-4.jpg') }}"
        alt="" />
    </div>

</main><!-- End .main -->


 <!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="filterForm" method="GET" action="{{ route('category', $subCategory->slug ?? 'decor') }}">
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
                                                            @if ($count !== null)
                                                                <span class="text-muted">({{ $count }})</span>
                                                            @endif
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
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
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


<script>
    $(document).ready(function() {
        $('.add-to-cart-btn').on('click', function() {
            const slug = $(this).data('slug');

            $.ajax({
                url: "{{ route('cart.add_1') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    product: { slug: slug }
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
