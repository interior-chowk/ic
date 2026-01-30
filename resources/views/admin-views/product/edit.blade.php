@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Product Edit'))
@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
<style>
    .editor-textarea {
        display: block !important;
    }
</style>

@section('content')

    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/products.png') }}" alt="">
                {{ \App\CPU\translate('Product') }} {{ \App\CPU\translate('Edit') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @php
            $specifications = DB::table('categories')->where('id', $product->sub_sub_category_id)->first();
            $specificationArray =
                $specifications && $specifications->specification ? explode(',', $specifications->specification) : [];
            $key_feature = DB::table('categories')->where('id', $product->sub_sub_category_id)->first();
            $key_featureArray =
                $key_feature && $key_feature->key_features ? explode(',', $key_feature->key_features) : [];

            $technical_spacification = DB::table('categories')->where('id', $product->sub_sub_category_id)->first();
            $technical_specificationArray =
                $technical_spacification && $technical_spacification->technical_specification
                    ? explode(',', $technical_spacification->technical_specification)
                    : [];

            $other_details = DB::table('categories')->where('id', $product->sub_sub_category_id)->first();
            $other_detailsArray =
                $other_details && $other_details->other_details ? explode(',', $other_details->other_details) : [];

            $x = DB::table('key_specification_values')->where('product_id', $product->id)->first();

            $y = json_decode($x->specification, true);
            $z = json_decode($x->key_features, true);
            $a = json_decode($x->technical_specification, true);
            $bx = json_decode($x->other_details, true);

        @endphp


        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('seller.product.update', $product->id) }}" method="post"
                    enctype="multipart/form-data" id="product_form">


                    @csrf

                    <div class="card">
                        <div class="px-4 pt-3">


                            @php($language = \App\Model\BusinessSetting::where('type', 'pnc_language')->first())

                            @php($language = $language->value ?? null)

                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])

                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach (json_decode($language) as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                            href="#"
                                            id="{{ $lang }}-link">{{ \App\CPU\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>



                        <div class="card-body">
                            @foreach (json_decode($language) as $lang)
                                <?php
                                if (count($product['translations'])) {
                                    $translate = [];
                                    foreach ($product['translations'] as $t) {
                                        if ($t->locale == $lang && $t->key == 'name') {
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                        if ($t->locale == $lang && $t->key == 'description') {
                                            $translate[$lang]['description'] = $t->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="{{ $lang != 'en' ? 'd-none' : '' }} lang_form" id="{{ $lang }}-form">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $lang }}_name">{{ \App\CPU\translate('Name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == 'en' ? 'required' : '' }} name="name[]"
                                            id="{{ $lang }}_name"
                                            value="{{ $translate[$lang]['name'] ?? $product['name'] }}"
                                            class="form-control" placeholder="{{ \App\CPU\translate('new_product') }}"
                                            required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    <div class="form-group pt-4">
                                        <label class="title-color">{{ \App\CPU\translate('description') }}
                                            ({{ strtoupper($lang) }})</label>
                                        <textarea cols="130" rows="10" name="description[]" class="textarea  editor-textarea" required>{!! $translate[$lang]['description'] ?? $product['details'] !!}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>





                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('General_Info') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Replacement_days') }}</label>
                                        <input type="number" min="0" step="0.01"
                                            placeholder="{{ \App\CPU\translate('Replacement day') }}"
                                            value="{{ $product->replacement_days }}" name="Replacement_days"
                                            class="form-control">
                                    </div>
                                    <input type="hidden" name="ids" class="form-control" id="ids"
                                        value="{{ $product->id }}">
                                    <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('HSN_code') }}</label>
                                        <input type="text" placeholder="{{ \App\CPU\translate('HSN Code') }}"
                                            value="{{ $product->HSN_code }}" name="HSN_code" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Return_Days') }}</label>
                                        <input type="number" min="0" step="0.01"
                                            placeholder="{{ \App\CPU\translate('Return days') }}"
                                            value="{{ $product->Return_days }}" name="Return_days" class="form-control"
                                            required>
                                    </div>
                                    <!-- <div class="col-md-4 mb-3" id="digital_product_type_show">
                                                <label for="digital_product_type" class="title-color">{{ \App\CPU\translate('digital_product_type') }}</label>
                                                <select name="digital_product_type" id="digital_product_type" class="form-control" required>
                                                    <option value="{{ old('digital_product_type') }}" {{ !$product->digital_product_type ? 'selected' : '' }} disabled>---Select---</option>
                                                    <option value="ready_after_sell" {{ $product->digital_product_type == 'ready_after_sell' ? 'selected' : '' }}>{{ \App\CPU\translate('Ready After Sell') }}</option>
                                                    <option value="ready_product" {{ $product->digital_product_type == 'ready_product' ? 'selected' : '' }}>{{ \App\CPU\translate('Ready Product') }}</option>
                                                </select>
                                            </div> -->

                                    <div class="col-md-4 mb-3">
                                        <label for="warehouse-select" class="title-color">Warehouse / Pickup Address<span
                                                class="ml-2" data-toggle="tooltip" data-placement="top"
                                                title="{{ \App\CPU\translate('From where the product is to be picked up by our shipping partner.') }}">
                                                <img class="info-img"
                                                    src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                    alt="img">
                                            </span></label>
                                        <select class="form-control" id="warehouse-select" name="warehouse">
                                            <?php
                                                $selectedwarehouse = DB::table('warehouse')
                                                    ->where('id', $product->add_warehouse)
                                                    ->first();
                                            ?>
                                            <option value="{{ $selectedwarehouse->id }}" selected disabled>
                                                {{ $selectedwarehouse->title }}</option>
                                            @foreach ($warehouse as $b)
                                                <option value="{{ $b->id }}"
                                                    {{ $b->id == $product->add_warehouse ? 'selected' : '' }}>
                                                    {{ $b->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3" id="minimum_order_qty">
                                        <label
                                            class="title-color">{{ \App\CPU\translate('minimum_order_quantity') }}</label>
                                        <input type="number" min="1" value={{ $product->minimum_order_qty }}
                                            step="1"
                                            placeholder="{{ \App\CPU\translate('minimum_order_quantity') }}"
                                            name="minimum_order_qty" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 mb-3 mt-4 physical_product_show" id="shipping_cost_multy1">
                                        <div
                                            class="border rounded px-3 py-2 min-h-40 d-flex justify-content-between gap-3">
                                            <label class="title-color">{{ \App\CPU\translate('free_delivery') }}
                                                <span class="ml-2" data-toggle="tooltip" data-placement="top"
                                                    title="{{ \App\CPU\translate('When you enables the Free Delivery tab, then delivery charges for this product will be recovered by the you.') }}">
                                                    <img class="info-img"
                                                        src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                        alt="img">
                                                </span>
                                            </label>

                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input" name="free_delivery"
                                                    id="" {{ $product->free_delivery == 1 ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="name"
                                            class="title-color">{{ \App\CPU\translate('Category') }}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="category_id" id="category_id"
                                            onchange="getRequest('{{ url('/') }}/seller/product/get-categories?parent_id='+this.value,'sub-category-select','select')">
                                            <option value="0" selected disabled>
                                                ---{{ \App\CPU\translate('Select') }}---</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category['id'] }}"
                                                    {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                                    {{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="name"
                                            class="title-color">{{ \App\CPU\translate('Sub_category') }}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="sub_category_id" id="sub-category-select"
                                            data-id="{{ $product->sub_category_id ? $product->sub_category_id : '' }}"
                                            onchange="getRequest('{{ url('/') }}/seller/product/get-categories?parent_id='+this.value,'sub-sub-category-select','select')">
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="name"
                                            class="title-color">{{ \App\CPU\translate('Sub_sub_category') }}</label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            data-id="{{ $product->sub_sub_category_id ? $product->sub_sub_category_id : '' }}"
                                            name="sub_sub_category_id" id="sub-sub-category-select">

                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="code"
                                            class="title-color">{{ \App\CPU\translate('product_code_sku') }}
                                            <span class="text-danger">*</span>
                                            <a class="style-one-pro"
                                                onclick="document.getElementById('generate_number').value = getRndIntegerAlpha()">
                                                {{ \App\CPU\translate('generate') }}
                                                {{ \App\CPU\translate('code') }}
                                            </a>
                                        </label>
                                        <input type="text" id="generate_number" name="code" class="form-control"
                                            value="{{ $product->code ? $product->code : '' }}"
                                            placeholder="{{ \App\CPU\translate('code') }}" required>
                                    </div>
                                    @if ($brand_setting)
                                        <div class="col-md-4 mb-3">
                                            <label for="name"
                                                class="title-color">{{ \App\CPU\translate('Brand') }}</label>
                                            <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control"
                                                name="brand_id">
                                                <option value="{{ null }}" selected disabled>
                                                    ---{{ \App\CPU\translate('Select') }}---</option>
                                                @foreach ($br as $b)
                                                    <option value="{{ $b['id'] }}"
                                                        {{ $b->id == $product->brand_id ? 'selected' : '' }}>
                                                        {{ $b['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-4 mb-3 physical_product_show">
                                        <label for="name"
                                            class="title-color">{{ \App\CPU\translate('Unit') }}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="unit">
                                            @foreach (\App\CPU\Helpers::units() as $x)
                                                <option value={{ $x }}
                                                    {{ $product->unit == $x ? 'selected' : '' }}>{{ $x }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- <div class="card mt-2 rest-part">
            <div class="card-body __coba-aspect">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <label class="title-color mb-0">{{ \App\CPU\translate('Youtube video link') }}</label>
                            <span class="badge badge-soft-info">(
                                {{ \App\CPU\translate('optional') }},
                                {{ \App\CPU\translate('please_provide_embed_link_not_direct_link') }}
                            )</span>
                        </div>

                        <input type="text" name="video_link"
                            placeholder="EX: https://www.youtube.com/embed/5R06LRdUCSE"
                            class="form-control" />
                    </div>
                </div>
            </div>
        </div> -->



                    <div class="card mt-2 rest-part">
                        <div class="card-body __coba-aspect">
                            <div class="row">
                                <div class="col-md-12 mb-4">

                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <label for="name" class="title-color mb-0">
                                                <h2>{{ \App\CPU\translate('Product Details') }}</h2>
                                            </label>
                                            <span class="text-info"></span>

                                            @if (!empty($specificationArray))
                                                <h6>Specifications</h6>
                                                <table border="0" style="width: 70%; border-collapse: collapse;">
                                                    <tbody>
                                                        @foreach ($specificationArray as $index => $spec)
                                                            <tr>
                                                                <td style="width: 20%;">
                                                                    <input type="text" name="specifications[]"
                                                                        value="{{ $spec }}" class="form-control"
                                                                        readonly
                                                                        style="background-color: #F9F9FA; margin: 10px; text-align: center;" />
                                                                </td>
                                                                <td style="width: 40%;">
                                                                    <input type="text"
                                                                        name="specification_values[{{ $index }}]"
                                                                        value="{{ $y[$index] ?? '' }}"
                                                                        class="form-control" style="margin: 10px;" />
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>No specifications found.</p>
                                            @endif

                                            @if (!empty($key_featureArray))
                                                <h6 style="margin-top: 10px;">Key Features</h6>
                                                <table border="0" style="width: 70%; border-collapse: collapse;">
                                                    <tbody>
                                                        @foreach ($key_featureArray as $index => $feature)
                                                            <tr>
                                                                <td style="width: 20%;">
                                                                    <input type="text" name="features[]"
                                                                        value="{{ $feature }}" class="form-control"
                                                                        readonly
                                                                        style="background-color: #F9F9FA; margin: 10px; text-align: center;" />
                                                                </td>
                                                                <td style="width: 40%;">
                                                                    <input type="text"
                                                                        name="features_values[{{ $index }}]"
                                                                        value="{{ $z[$index] ?? '' }}"
                                                                        class="form-control" style="margin: 10px;" />
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>No key features found.</p>
                                            @endif

                                            @if (!empty($technical_specificationArray))
                                                <h6 style="margin-top: 10px;">Technical Specification</h6>
                                                <table class="table" style="width: 70%; border-collapse: collapse;">
                                                    <tbody>
                                                        @foreach ($technical_specificationArray as $index => $technical)
                                                            <tr>
                                                                <td style="width: 20%;">
                                                                    <input type="text" name="technical_specification[]"
                                                                        value="{{ $technical }}" class="form-control"
                                                                        readonly
                                                                        style="background-color: #F9F9FA; margin: 10px; text-align: center;" />
                                                                </td>
                                                                <td style="width: 40%;">
                                                                    <input type="text"
                                                                        name="technical_specification_values[{{ $index }}]"
                                                                        value="{{ $a[$index] ?? '' }}"
                                                                        class="form-control" style="margin: 10px;" />
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>No technical specifications found.</p>
                                            @endif


                                            @if (!empty($other_detailsArray))
                                                <h6 style="margin-top: 10px;">Other Details</h6>
                                                <table border="0" style="width: 70%; border-collapse: collapse;">
                                                    <tbody>
                                                        @foreach ($other_detailsArray as $index => $other)
                                                            <tr>
                                                                <td style="width: 20%;">
                                                                    <input type="text" name="other_details[]"
                                                                        value="{{ $other }}" class="form-control"
                                                                        readonly
                                                                        style="background-color: #F9F9FA; margin: 10px; text-align: center;" />
                                                                </td>
                                                                <td style="width: 40%;">
                                                                    <input type="text"
                                                                        name="other_details_values[{{ $index }}]"
                                                                        value="{{ $bx[$index] ?? '' }}"
                                                                        class="form-control" style="margin: 10px;" />
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p>No other details found.</p>
                                            @endif


                                        </div>

                                        <!-- <div class="row g-2" id="thumbnail"></div> -->
                                    </div>


                                    <div class="card mt-2 rest-part physical_product_show">
                                        <div class="card-header">
                                            <h4 class="mb-0">{{ \App\CPU\translate('Variations') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <div class="d-flex gap-10 mb-2 align-items-center">
                                                        <label for="colors" class="mb-0">
                                                            {{ \App\CPU\translate('Colors') }} :
                                                        </label>
                                                        <label class="switcher">
                                                            <input type="checkbox" class="switcher_input"
                                                                id="color_switcher" name="colors_active"
                                                                {{ count($product['colors']) > 0 ? 'checked' : '' }}>
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </div>

                                                    <select
                                                        class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                                        name="colors[]" multiple="multiple" id="colors-selector"
                                                        {{ count($product['colors']) > 0 ? '' : 'disabled' }}>
                                                        @foreach (\App\Model\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                            <option value={{ $color->code }}
                                                                {{ in_array($color->code, $product['colors']) ? 'selected' : '' }}>
                                                                {{ $color['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="attributes" class="title-color">
                                                        {{ \App\CPU\translate('Attributes') }} :
                                                    </label>
                                                    <select class="form-control js-select2-custom"
                                                        name="choice_attributes[]" id="choice_attributes"
                                                        multiple="multiple" required>
                                                        @foreach (\App\Model\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                                            @if ($product['attributes'] != 'null')
                                                                <option value="{{ $a['id'] }}"
                                                                    {{ in_array($a->id, json_decode($product['attributes'], true)) ? 'selected' : '' }}>
                                                                    {{ $a['name'] }}
                                                                </option>
                                                            @else
                                                                <option value="{{ $a['id'] }}">{{ $a['name'] }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12 mt-2 mb-2">
                                                    <div class="customer_choice_options" id="customer_choice_options">
                                                        @include('seller-views.product.partials._choices', [
                                                            'choice_no' => json_decode($product['attributes']),
                                                            'choice_options' => json_decode(
                                                                $product['choice_options'],
                                                                true),
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mt-2 rest-part">
                                        <div class="card-header">
                                            <h4 class="mb-0">{{ \App\CPU\translate('Product price & stock') }}</h4>
                                        </div>
                                        <!---------- product discount ------>
                                        <div class="sku_combination" id="sku_combination">
                                            <!-- @include(
                                                'seller-views.product.partials._edit_sku_combinations',
                                                ['combinations' => json_decode($product['variation'], true)]
                                            ) -->
                                        </div>
                                        <!----- product quantity --->
                                        <!---------dimension-------->

                                        <div class="card mt-2 mb-2 rest-part">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    <span class="card-header-icon"><i class="tio-label"></i></span>
                                                    <span>{{ \App\CPU\translate('tags') }}</span>
                                                </h5>
                                            </div>
                                            <div class="card-body pb-0">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label
                                                                class="title-color">{{ \App\CPU\translate('search_tags') }}</label>
                                                            <input type="text" class="form-control" name="tags"
                                                                value="@foreach ($product->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                                                data-role="tagsinput">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--<div class="card mt-2 mb-2 rest-part">
                                <div class="card-header">
                                    <h4 class="mb-0">{{ \App\CPU\translate('seo_section') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <label class="title-color">{{ \App\CPU\translate('Meta_Title') }}</label>
                                            <input type="text" name="meta_title" value="{{ $product['meta_title'] }}" placeholder="" class="form-control">
                                        </div>

                                        <div class="col-md-8 mb-4">
                                            <label class="title-color">{{ \App\CPU\translate('Meta_Description') }}</label>
                                            <textarea rows="10" type="text" name="meta_description" class="form-control">
                                        {{ $product['meta_description'] }}
                                    </textarea>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="title-color">{{ \App\CPU\translate('Meta_Image') }}</label>
                                            </div>
                                            <div class="__coba-aspect">
                                                <div class="row g-2" id="meta_img">
                                                    <div class="col-sm-6 col-md-12 col-lg-6">
                                                        <img class="w-100" height="auto"
                                                                onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                                                src="{{ asset('storage/app/public/product/meta') }}/{{ $product['meta_image'] }}"
                                                                alt="Meta image">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>-->

                                        <div class="card mt-2 rest-part __coba-aspect">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-4">
                                                        <div class="mb-2 d-flex flex-wrap gap-1">
                                                            <label
                                                                class="title-color mb-0">{{ \App\CPU\translate('Youtube video link') }}</label>
                                                            <small class="badge-soft-info"> (
                                                                {{ \App\CPU\translate('optional') }},
                                                                {{ \App\CPU\translate('please_provide_embed_link_not_direct_link') }}
                                                                )</small>
                                                        </div>
                                                        <input type="text" value="{{ $product['video_url'] }}"
                                                            name="video_link"
                                                            placeholder="EX : https://www.youtube.com/embed/5R06LRdUCSE"
                                                            class="form-control">
                                                    </div>

                                                    <div class="col-12 d-flex justify-content-end mt-3">
                                                        @if ($product['request_status'] == 2)
                                                            <button type="button" onclick="check()"
                                                                class="btn btn--primary px-4">{{ \App\CPU\translate('resubmit') }}</button>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn--primary px-4">{{ \App\CPU\translate('update') }}</button>
                                                        @endif
                                                    </div>

                                                    <input type="hidden" id="color_image"
                                                        value="{{ $product->color_image }}">
                                                    <input type="hidden" id="images"
                                                        value="{{ $product->images }}">
                                                    <input type="hidden" id="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" id="remove_url"
                                                        value="{{ route('seller.product.remove-image') }}">
                                                </div>
                                            </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function calculateSellingPrice() {
            var discountType = $('#discount_type_').val();
            var unit_price = parseFloat($('#unit_price_').val());
            var discount = parseFloat($('#discount_').val());

            if (isNaN(unit_price) || isNaN(discount)) {
                $('#selling_price_').text('0.00');
                return;
            }

            var selling_price;
            if (discountType === 'percent') {
                selling_price = unit_price - (unit_price * discount / 100);
            } else if (discountType === 'flat') {
                selling_price = unit_price - discount;
            }

            $('#selling_price_').text(selling_price.toFixed(2));
        }

        // Bind the change event to the discount type select element
        $('#discount_type_').change(function() {
            calculateSellingPrice();
        });

        // Bind the keyup event to the discount input element
        $('#discount_').keyup(function() {
            calculateSellingPrice();
        });

        // Bind the keyup event to the unit price input element
        $('#unit_price_').keyup(function() {
            calculateSellingPrice();
        });
    });
</script>


@push('script_2')
    <script src="{{ asset('public/assets/back-end') }}/js/tags-input.min.js"></script>
    <!-- <script src="{{ asset('public/assets/select2/js/select2.min.js') }}"></script>-->
    <script src="{{ asset('public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>


    <script>
        $('#color_switcher').click(function() {
            let checkBoxes = $("#color_switcher");
            if ($('#color_switcher').prop('checked')) {
                $('#color_wise_image').show();
            } else {
                $('#color_wise_image').hide();
            }
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function() {
            // Preserve existing options
            let existingOptions = {};
            $('#customer_choice_options .row').each(function() {
                let choiceNo = $(this).find('input[name="choice_no[]"]').val();
                let choiceOptions = $(this).find('input[name^="choice_options_"]').val();
                existingOptions[choiceNo] = choiceOptions;
            });

            $('#customer_choice_options').html(null);

            // Add the selected options, including previously filled values
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text(), existingOptions[$(this)
                    .val()]);
            });
        });

        function add_more_customer_choice_option(i, name, existingValue = '') {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append(
                '<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i +
                '"><input type="text" class="form-control" name="choice[]" value="' + n +
                '" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' +
                i +
                '[]" placeholder="{{ trans('Enter choice values') }}" data-role="tagsinput" value="' + (
                    existingValue || '') + '" onchange="update_sku()"></div></div>'
            );

            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        document.addEventListener("DOMContentLoaded", function() {
            let inputs = document.querySelectorAll('.call-update-sku');
            inputs.forEach(input => {
                if (input.value.trim() !== "") {
                    update_sku();
                }
                input.addEventListener('change', function() {
                    update_sku();
                });
            });


        });


        $('#colors-selector').on('change', function() {
            update_sku();
            let checkBoxes = $("#color_switcher");
            if ($('#color_switcher').prop('checked')) {
                $('#color_wise_image').show();
                color_wise_image($('#colors-selector'));
            } else {
                $('#color_wise_image').hide();
            }
        });

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        function update_sku() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{ route('seller.product.sku_combination_edit') }}',
                data: $('#product_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        function color_wise_image(t) {
            let colors = t.val();
            let color_image = $('#color_image').val() ? $.parseJSON($('#color_image').val()) : [];
            let images = $.parseJSON($('#images').val());
            var product_id = $('#product_id').val();
            let remove_url = $('#remove_url').val();

            let color_image_value = $.map(color_image, function(item) {
                return item.color;
            });

            $('#color_wise_existing_image').html('')
            $('#color_wise_image_field').html('')

            $.each(colors, function(key, value) {
                let value_id = value.replace('#', '');
                let in_array_image = $.inArray(value_id, color_image_value);
                let input_image_name = "color_image_" + value_id;

                $.each(color_image, function(color_key, color_value) {
                    if ((in_array_image !== -1) && (color_value['color'] === value_id)) {
                        let image_name = color_value['image_name'];
                        let exist_image_html = `
                            <div class="col-6 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                    <span class="upload--icon" style="background: #${color_value['color']} ">
                                    <i class="tio-done"></i>
                                    </span>
                                        <img class="w-100" height="auto"
                                             onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                             src="{{ asset('storage/app/public/product/`+image_name+`') }}"
                                             alt="Product image">
                                        <a href="` + remove_url + `?id=` + product_id + `&name=` + image_name +
                            `&color=` + color_value['color'] + `"
                                           class="btn btn-danger btn-block">{{ \App\CPU\translate('Remove') }}</a>
                                    </div>
                                </div>
                            </div>`;
                        $('#color_wise_existing_image').append(exist_image_html)
                    }
                });
            });

            $.each(colors, function(key, value) {
                let value_id = value.replace('#', '');
                let in_array_image = $.inArray(value_id, color_image_value);
                let input_image_name = "color_image_" + value_id;

                if (in_array_image === -1) {
                    let html = ` <div class='col-6 col-md-6'> <label style='border: 2px dashed #ddd; border-radius: 3px; cursor: pointer; text-align: center; overflow: hidden; padding: 5px; margin-top: 5px; margin-bottom : 5px; position : relative; display: flex; align-items: center; margin: auto; justify-content: center; flex-direction: column;'>
                            <span class="upload--icon" style="background: ${value}">
                            <i class="tio-edit"></i>
                                <input type="file" name="` + input_image_name + `" id="` + value_id + `" class="d-none" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required="">
                            </span>
                            <img src="{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}" style="object-fit: cover;aspect-ratio:1"  alt="public/img">
                          </label> </div>`;
                    $('#color_wise_image_field').append(html)

                    $("#color_wise_image input[type='file']").each(function() {

                        var $this = $(this).closest('label');

                        function proPicURL(input) {
                            if (input.files && input.files[0]) {
                                var uploadedFile = new FileReader();
                                uploadedFile.onload = function(e) {
                                    $this.find('img').attr('src', e.target.result);
                                    $this.fadeIn(300);
                                };
                                uploadedFile.readAsDataURL(input.files[0]);
                            }
                        }

                        $(this)
                            .on("change", function() {
                                proPicURL(this);
                            });
                    });
                }
            });
        }

        $(document).ready(function() {
            let category = $("#category_id").val();
            let sub_category = $("#sub-category-select").attr("data-id");
            let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
            getRequest('{{ url('/') }}/seller/product/get-categories?parent_id=' + category +
                '&sub_category=' + sub_category, 'sub-category-select', 'select');
            getRequest('{{ url('/') }}/seller/product/get-categories?parent_id=' + sub_category +
                '&sub_category=' + sub_sub_category, 'sub-sub-category-select', 'select');
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function(m) {
                    return m;
                }
            });

            let checkBoxes = $("#color_switcher");
            if ($('#color_switcher').prop('checked')) {
                $('#color_wise_image').show();
                color_wise_image($('#colors-selector'));
            } else {
                $('#color_wise_image').hide();
            }

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                    .text;
            }
        });
    </script>

    <script>
        function check() {


            let totalSize = 0;
            const imageFileInputs = document.querySelectorAll('input[name="images[]"]');
            const imageFile = document.querySelector('input[name="image"]').files[0];


            if (imageFile != undefined) {
                totalSize += imageFile.size;
            } else {
                totalSize = 0;
            }


            imageFileInputs.forEach(input => {
                if (input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        totalSize += input.files[i].size;
                    }
                }
            });




            let totalSizeMB = totalSize / 1024 / 1024;


            if (totalSizeMB > 5) {

                Swal.fire({
                    title: '{{ \App\CPU\translate('File size too big') }}',
                    text: '{{ \App\CPU\translate('The total file size should be 5MB or less.') }}',
                    icon: 'error',
                    confirmButtonColor: '#377dff',
                    confirmButtonText: '{{ \App\CPU\translate('OK') }}'
                });
                return false;
            }

            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var formData = new FormData(document.getElementById('product_form'));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('seller.product.update', $product->id) }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ \App\CPU\translate('product updated successfully!') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#product_form').submit();
                    }
                }
            });
        };
    </script>

    <script>
        update_qty();

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

        $('input[name^="qty_"]').on('keyup', function() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
        });
    </script>

    <script>
        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $(".rest-part").removeClass('d-none');
            } else {
                $(".rest-part").addClass('d-none');
            }
        });

        $(document).ready(function() {
            product_type();
            digital_product_type();

            $('#product_type').change(function() {
                product_type();
            });

            $('#digital_product_type').change(function() {
                digital_product_type();
            });
        });

        function product_type() {
            let product_type = $('#product_type').val();

            if (product_type === 'physical') {
                $('#digital_product_type_show').hide();
                $('#digital_file_ready_show').hide();
                $('.physical_product_show').show();
                $("#digital_product_type").val($("#digital_product_type option:first").val());
                $("#digital_file_ready").val('');
            } else if (product_type === 'digital') {
                $('#digital_product_type_show').show();
                $('.physical_product_show').hide();

            }
        }

        function digital_product_type() {
            let digital_product_type = $('#digital_product_type').val();
            if (digital_product_type === 'ready_product') {
                $('#digital_file_ready_show').show();
            } else if (digital_product_type === 'ready_after_sell') {
                $('#digital_file_ready_show').hide();
                $("#digital_file_ready").val('');
            }
        }
    </script>

    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
<script>
    function getRndIntegerAlpha() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const length = 8; // You can adjust the length of the alphanumeric string
        let result = '';
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }
</script>
