@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Banner'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-1 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/banner.png') }}" alt="">
                {{ \App\CPU\translate('banner_update_form') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.banner.update', [$banner['id']]) }}" method="post"
                            enctype="multipart/form-data" class="banner_form">
                            @csrf
                            @method('put')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" id="id" name="id" value="{{ $banner['id'] }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="banner_type_select"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('banner_type') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="banner_type" required
                                            id="banner_type_select">
                                            @php
                                                $banner_types = [
                                                    'Main Banner',
                                                    'Instant Delivery Banner',
                                                    'Main Banner 2',
                                                    'Service Provider Banner 1 web',
                                                    'Service Provider Banner 2 web',
                                                    'Service Provider Banner 3 web',
                                                    'Seasonal Banner web',
                                                    'Banner 2',
                                                    'Banner 3',
                                                    'Banner 4',
                                                    'Banner 5',
                                                    'Banner 6',
                                                    'Banner 7',
                                                    'Banner 8',
                                                    'Banner 9',
                                                    'Banner 10',
                                                    'Main Banner web',
                                                    'Seasonal Banner',
                                                    'Discount 1 web',
                                                    'Discount 2 web',
                                                    'Discount 3 web',
                                                    'Discount 4 web',
                                                    'Discount 5 web',
                                                    'Luxury BG',
                                                    'Day BG',
                                                    'Main Banner 2 web',
                                                    'Choice 1 web',
                                                    'Choice 2 web',
                                                    'Choice 3 web',
                                                    'Choice 4 web',
                                                    'Choice 5 web',
                                                    'Day BG web',
                                                    'Tips 1',
                                                    'Tips 2',
                                                    'Tips 3',
                                                    'Tips 4',
                                                    'Tips 5',
                                                    'Tips 6',
                                                    'Banner 10 web',
                                                    'Banner 9 web',
                                                    'Banner 8 web',
                                                    'Banner 7 web',
                                                    'Banner 6 web',
                                                    'Banner 5 web',
                                                    'Banner 4 web',
                                                    'Banner 3 web',
                                                    'Banner 2 web',
                                                    'Desktop:1',
                                                    'Desktop:2',
                                                    'Desktop:3',
                                                    'Desktop:4',
                                                    'Desktop:5',
                                                    'Desktop:6',
                                                    'Desktop:7',
                                                    'Mobile:1',
                                                    'Mobile:2',
                                                    'Mobile:3',
                                                    'Mobile:4',
                                                    'Mobile:5',
                                                    'Mobile:6',
                                                    'Mobile:7',
                                                    'Discount 1',
                                                    'Discount 2',
                                                    'Discount 3',
                                                    'Discount 4',
                                                    'Discount 5',
                                                    'Choice 1',
                                                    'Choice 2',
                                                    'Choice 3',
                                                    'Choice 4',
                                                    'Choice 5',
                                                    'Luxury BG web',
                                                    'Discount BG web',
                                                    'Discount BG',
                                                    'Service Provider Banner 1',
                                                    'Service Provider Banner 2',
                                                    'Service Provider Banner 3',
                                                    'Instant Delivery Banner web',
                                                    'Product page banner 2 web',
                                                    'Product page banner 1 web',
                                                    'Product page banner 2',
                                                    'Product page banner 1',
                                                ];
                                            @endphp
                                            @foreach ($banner_types as $type)
                                                <option value="{{ $type }}"
                                                    {{ trim($banner['banner_type']) == $type ? 'selected' : '' }}>
                                                    {{ $type }}</option>
                                            @endforeach
                                            @if (theme_root_path() == 'theme_aster')
                                                <option value="Header Banner"
                                                    {{ trim($banner['banner_type']) == 'Header Banner' ? 'selected' : '' }}>
                                                    {{ \App\CPU\translate('Header Banner') }}</option>
                                                <option value="Sidebar Banner"
                                                    {{ trim($banner['banner_type']) == 'Sidebar Banner' ? 'selected' : '' }}>
                                                    {{ \App\CPU\translate('Sidebar Banner') }}</option>
                                                <option value="Top Side Banner"
                                                    {{ trim($banner['banner_type']) == 'Top Side Banner' ? 'selected' : '' }}>
                                                    {{ \App\CPU\translate('Top Side Banner') }}</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="resource_type"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('resource_type') }}</label>
                                        <select onchange="display_data(this.value)"
                                            class="js-example-responsive form-control w-100" name="resource_type" required>
                                            <option value="product"
                                                {{ $banner['resource_type'] == 'product' ? 'selected' : '' }}>Product
                                            </option>
                                            <option value="category"
                                                {{ $banner['resource_type'] == 'category' ? 'selected' : '' }}>Category
                                            </option>
                                            <option value="shop"
                                                {{ $banner['resource_type'] == 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="brand"
                                                {{ $banner['resource_type'] == 'brand' ? 'selected' : '' }}>Brand</option>
                                        </select>
                                    </div>

                                    {{-- Resource Product --}}
                                    <div class="form-group" id="resource-product"
                                        style="display: {{ $banner['resource_type'] == 'product' ? 'block' : 'none' }}">
                                        <label for="product_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('product') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="product_id">
                                            @foreach (\App\Model\Product::active()->get() as $product)
                                                <option value="{{ $product['id'] }}"
                                                    {{ $banner['resource_id'] == $product['id'] ? 'selected' : '' }}>
                                                    {{ $product['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Resource Category --}}
                                    <div class="form-group" id="resource-category"
                                        style="display: {{ $banner['resource_type'] == 'category' ? 'block' : 'none' }}">
                                        <label for="category_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('category') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="category_id"
                                            id="sub_category_id">
                                            @foreach (\App\CPU\CategoryManager::parents() as $category)
                                                <option value="{{ $category['id'] }}"
                                                    {{ $banner['resource_id'] == $category['id'] ? 'selected' : '' }}>
                                                    {{ $category['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Resource Shop --}}
                                    <div class="form-group" id="resource-shop"
                                        style="display: {{ $banner['resource_type'] == 'shop' ? 'block' : 'none' }}">
                                        <label for="shop_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('shop') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="shop_id">
                                            @foreach (\App\Model\Shop::active()->get() as $shop)
                                                <option value="{{ $shop['id'] }}"
                                                    {{ $banner['resource_id'] == $shop['id'] ? 'selected' : '' }}>
                                                    {{ $shop['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Resource Brand --}}
                                    <div class="form-group" id="resource-brand"
                                        style="display: {{ $banner['resource_type'] == 'brand' ? 'block' : 'none' }}">
                                        <label for="brand_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('brand') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="brand_id">
                                            @foreach (\App\Model\Brand::all() as $brand)
                                                <option value="{{ $brand['id'] }}"
                                                    {{ $banner['resource_id'] == $brand['id'] ? 'selected' : '' }}>
                                                    {{ $brand['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Sub Category --}}
                                    <div class="form-group sub_category_field"
                                        style="{{ $banner['sub_category'] ? '' : 'display:none;' }}">
                                        <label for="sub_category"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('sub_category') }}</label>
                                        <select class="js-example-responsive form-control w-100 sub_category_cls"
                                            name="sub_category" id="sub_sub_category_id">
                                            {{-- Populated by AJAX --}}
                                        </select>
                                    </div>

                                    {{-- Sub Sub Category --}}
                                    <div class="form-group sub_sub_category_field"
                                        style="{{ $banner['sub_sub_category'] ? '' : 'display:none;' }}">
                                        <label for="sub_sub_category"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('sub_sub_category') }}</label>
                                        <select class="js-example-responsive form-control w-100 sub_sub_category_cls"
                                            name="sub_sub_category">
                                            {{-- Populated by AJAX --}}
                                        </select>
                                    </div>

                                    {{-- URL --}}
                                    <div class="form-group mt-4 mb-0">
                                        <label for="url"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('banner_URL') }}</label>
                                        <input type="url" name="url" class="form-control" id="url" required
                                            placeholder="{{ translate('Enter_url') }}" value="{{ $banner['url'] }}">
                                    </div>

                                    {{-- Discount --}}
                                    <div class="form-group mt-4 mb-0">
                                        <label for="discount"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('banner_Discount') }}</label>
                                        <input type="text" name="discount" class="form-control" id="discount"
                                            required placeholder="{{ translate('Enter_discount') }}"
                                            value="{{ $banner['discount'] }}">
                                    </div>

                                    {{-- Video --}}
                                    <div class="col-md-6 d-flex flex-column justify-content-end mt-4">
                                        <div>
                                            <label for="video"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('Video') }}</label>
                                            <span class="text-info">ratio 9:16 </span>
                                            <div class="custom-file text-left">
                                                <input type="file" name="video" id="mbVideoFileUploader"
                                                    class="custom-file-input"
                                                    accept=".mp4, .mov, .avi, .mkv, .flv, .wmv, .webm, video/*">
                                                <label class="custom-file-label title-color"
                                                    for="mbVideoFileUploader">{{ \App\CPU\translate('choose') }}
                                                    {{ \App\CPU\translate('file') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Theme Fashion Fields --}}
                                    @if (theme_root_path() == 'theme_fashion')
                                        <div
                                            class="form-group mt-4 input_field_for_main_banner {{ $banner['banner_type'] != 'Main Banner' ? 'd-none' : '' }}">
                                            <label for="button_text"
                                                class="title-color text-capitalize">{{ translate('Button_Text') }}</label>
                                            <input type="text" name="btn_text" class="form-control" id="button_text"
                                                placeholder="{{ translate('Enter_button_text') }}"
                                                value="{{ $banner['button_text'] }}">
                                        </div>
                                        <div
                                            class="form-group mt-4 mb-0 input_field_for_main_banner {{ $banner['banner_type'] != 'Main Banner' ? 'd-none' : '' }}">
                                            <label for="background_color"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('background_color') }}</label>
                                            <input type="color" name="background_color" class="form-control"
                                                id="background_color" value="{{ $banner['background_color'] }}">
                                        </div>
                                    @endif
                                </div>

                                {{-- Image preview --}}
                                <div class="col-md-6 d-flex flex-column justify-content-end">
                                    <div>
                                        <center>
                                            <img class="ratio-4:1" id="mbImageviewer"
                                                src="{{ env('CLOUDFLARE_R2_PUBLIC_URL') }}{{ $banner['photo'] }}"
                                                alt="" />
                                        </center>
                                        <label for="image"
                                            class="mt-3">{{ \App\CPU\translate('Image') }}</label><span
                                            class="ml-1 text-info" id="theme_ratio">( {{ \App\CPU\translate('ratio') }}
                                            4:1 )</span>
                                        <br>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="mbimageFileUploader"
                                                class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                for="mbimageFileUploader">{{ \App\CPU\translate('choose') }}
                                                {{ \App\CPU\translate('file') }}</label>
                                        </div>

                                        @if (theme_root_path() == 'theme_fashion')
                                            <div
                                                class="form-group mt-4 input_field_for_main_banner {{ $banner['banner_type'] != 'Main Banner' ? 'd-none' : '' }}">
                                                <label for="title"
                                                    class="title-color text-capitalize">{{ translate('Title') }}</label>
                                                <input type="text" name="title" class="form-control" id="title"
                                                    placeholder="{{ translate('Enter_banner_title') }}"
                                                    value="{{ $banner['title'] }}">
                                            </div>
                                            <div
                                                class="form-group mb-0 input_field_for_main_banner {{ $banner['banner_type'] != 'Main Banner' ? 'd-none' : '' }}">
                                                <label for="sub_title"
                                                    class="title-color text-capitalize">{{ translate('Sub_Title') }}</label>
                                                <input type="text" name="sub_title" class="form-control"
                                                    id="sub_title"
                                                    placeholder="{{ translate('Enter_banner_sub_title') }}"
                                                    value="{{ $banner['sub_title'] }}">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12 d-flex justify-content-end gap-3">
                                    <button type="reset"
                                        class="btn btn-secondary px-4">{{ \App\CPU\translate('reset') }}</button>
                                    <button type="submit"
                                        class="btn btn--primary px-4">{{ \App\CPU\translate('update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Theme ratio
            theme_wise_ration();

            // Prefill select2 banner type
            let dbBannerType = @json(trim($banner->banner_type));
            $('#banner_type_select').val(dbBannerType).trigger('change');

            // Show main banner fields if selected
            if (dbBannerType === 'Main Banner') {
                $('.input_field_for_main_banner').removeClass('d-none');
            } else {
                $('.input_field_for_main_banner').addClass('d-none');
            }

            $('#banner_type_select').on('change', function() {
                let val = $(this).val();
                theme_wise_ration();
                if (val === 'Main Banner') {
                    $('.input_field_for_main_banner').removeClass('d-none');
                } else {
                    $('.input_field_for_main_banner').addClass('d-none');
                }
            });

            $(".js-example-theme-single").select2({
                theme: "classic"
            });
            $(".js-example-responsive").select2({
                width: 'resolve'
            });

            // Display resource type
            display_data('{{ $banner['resource_type'] }}');

            // AJAX sub-category load
            load_selected_category();
        });

        function theme_wise_ration() {
            let banner_type = $('#banner_type_select').val();
            let theme = '{{ theme_root_path() }}';
            let theme_ratio = {!! json_encode(THEME_RATIO) !!};
            let get_ratio = theme_ratio[theme][banner_type] ?? '';
            $('#theme_ratio').text(get_ratio);
        }

        function display_data(data) {
            $('#resource-product').hide()
            $('#resource-brand').hide()
            $('#resource-category').hide()
            $('#resource-shop').hide()
            if (data === 'product') {
                $('#resource-product').show();
            } else if (data === 'brand') {
                $('#resource-brand').show();
            } else if (data === 'category') {
                $('#resource-category').show();
            } else if (data === 'shop') {
                $('#resource-shop').show();
            }
        }

        // Image preview
        function mbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#mbImageviewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#mbimageFileUploader").change(function() {
            mbimagereadURL(this);
        });

        // AJAX load sub-category/sub-sub-category
        function load_selected_category() {
            var data = {
                category_id: "{{ $banner['resource_id'] }}",
                sub_category_id: "{{ $banner['sub_category'] }}",
                sub_sub_category_id: "{{ $banner['sub_sub_category'] }}",
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: "{{ route('admin.sub-category.get_selected_category') }}",
                type: 'POST',
                data: data,
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 1) {
                        $('.sub_category_cls').html(response.sub_category);
                        $('.sub_sub_category_cls').html(response.sub_sub_category);
                    }
                }
            });
        }
    </script>
@endpush
