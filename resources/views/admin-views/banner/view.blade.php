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
                <img width="20" src="{{asset('/public/assets/back-end/img/banner.png')}}" alt="">
                {{\App\CPU\translate('banner')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row pb-4 d--none" id="main-banner"
             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-capitalize">{{ \App\CPU\translate('banner_form')}}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">

                                    <input type="hidden" id="id" name="id">
                                    <div class="form-group">
                                        <label for="name"
                                               class="title-color text-capitalize">{{\App\CPU\translate('banner_type')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="banner_type" required id="banner_type_select">
                                            <option value="Main Banner">{{ \App\CPU\translate('Main Banner')}}</option>
                                            <option value="Instant Delivery Banner">{{ \App\CPU\translate('Instant Delivery Banner')}}</option>
                                            <option value="Main Banner 2">{{ \App\CPU\translate('Main Banner 2')}}</option>
                                            <option value="Service Provider Banner 1">{{ \App\CPU\translate('Service Provider Banner 1')}}</option>
                                            <option value="Service Provider Banner 2">{{ \App\CPU\translate('Service Provider Banner 2')}}</option>
                                            <option value="Service Provider Banner 3">{{ \App\CPU\translate('Service Provider Banner 3')}}</option>
                                            <option value="Banner 2">{{ \App\CPU\translate('Banner 2')}}</option>
                                            <option value="Banner 3">{{ \App\CPU\translate('Banner 3')}}</option>
                                            <option value="Banner 4">{{ \App\CPU\translate('Banner 4')}}</option>
                    
                                            <option value="Banner 5">{{ \App\CPU\translate('Banner 5')}}</option>
                                            <option value="Banner 6">{{ \App\CPU\translate('Banner 6')}}</option>
                                            <option value="Banner 7">{{ \App\CPU\translate('Banner 7')}}</option>
                                            <option value="Banner 8">{{ \App\CPU\translate('Banner 8')}}</option>
                                            <option value="Banner 9">{{ \App\CPU\translate('Banner 9')}}</option>
                                            <option value="Banner 10">{{ \App\CPU\translate('Banner 10')}}</option>
                                            <option value="Discount 1">{{ \App\CPU\translate('Discount Banner 1')}}</option>
                                            <option value="Discount 2">{{ \App\CPU\translate('Discount Banner 2')}}</option>
                                            <option value="Discount 3">{{ \App\CPU\translate('Discount Banner 3')}}</option>
                                            <option value="Discount 4">{{ \App\CPU\translate('Discount Banner 4')}}</option>
                                            <option value="Discount 5">{{ \App\CPU\translate('Discount Banner 5')}}</option>
                                            <option value="Choice 1">{{ \App\CPU\translate('Creators Choice 1')}}</option>
                                            <option value="Choice 2">{{ \App\CPU\translate('Creators Choice 2')}}</option>
                                            <option value="Choice 3">{{ \App\CPU\translate('Creators Choice 3')}}</option>
                                            <option value="Choice 4">{{ \App\CPU\translate('Creators Choice 4')}}</option>
                                            <option value="Choice 5">{{ \App\CPU\translate('Creators Choice 5')}}</option>
                                            <option value="Tips 1">{{ \App\CPU\translate('Tips 1')}}</option>
                                            <option value="Tips 2">{{ \App\CPU\translate('Tips 2')}}</option>
                                            <option value="Tips 3">{{ \App\CPU\translate('Tips 3')}}</option>
                                            <option value="Tips 4">{{ \App\CPU\translate('Tips 4')}}</option>
                                            <option value="Tips 5">{{ \App\CPU\translate('Tips 5')}}</option>
                                            <option value="Tips 6">{{ \App\CPU\translate('Tips 6')}}</option>
                                            <option value="Luxury BG">{{ \App\CPU\translate('Luxury BG')}}</option>
                                            <option value="Day BG">{{ \App\CPU\translate('Deal of the day BG')}}</option>
                                            <option value="Discount BG">{{ \App\CPU\translate('Discount BG')}}</option>
                                            <option value="Product page banner 1">{{ \App\CPU\translate('Product page banner 1')}}</option>
                                            <option value="Product page banner 2">{{ \App\CPU\translate('Product page banner 2')}}</option>
                                            <option value="Seasonal Banner">{{ \App\CPU\translate('Seasonal Banner')}}</option>
                                            <!--web-->
                                            <option value="Seasonal Banner web">{{ \App\CPU\translate('Seasonal Banner Web')}}</option>
                                            <option value="Main Banner web">{{ \App\CPU\translate('Main Banner web')}}</option>
                                            <option value="Instant Delivery Banner web">{{ \App\CPU\translate('Instant Delivery Banner web')}}</option>
                                            <option value="Main Banner 2 web">{{ \App\CPU\translate('Main Banner 2 web')}}</option>
                                            <option value="Service Provider Banner 1 web">{{ \App\CPU\translate('Service Provider Banner 1 web')}}</option>
                                            <option value="Service Provider Banner 2 web">{{ \App\CPU\translate('Service Provider Banner 2 web')}}</option>
                                            <option value="Service Provider Banner 3 web">{{ \App\CPU\translate('Service Provider Banner 3 web')}}</option>
                                            <option value="Banner 2 web">{{ \App\CPU\translate('Banner 2 web')}}</option>
                                            <option value="Banner 3 web">{{ \App\CPU\translate('Banner 3 web')}}</option>
                                            <option value="Banner 4 web">{{ \App\CPU\translate('Banner 4 web')}}</option>
                    
                                            <option value="Banner 5 web">{{ \App\CPU\translate('Banner 5 web')}}</option>
                                            <option value="Banner 6 web">{{ \App\CPU\translate('Banner 6 web')}}</option>
                                            <option value="Banner 7 web">{{ \App\CPU\translate('Banner 7 web')}}</option>
                                            <option value="Banner 8 web">{{ \App\CPU\translate('Banner 8 web')}}</option>
                                            <option value="Banner 9 web">{{ \App\CPU\translate('Banner 9 web')}}</option>
                                            <option value="Banner 10 web">{{ \App\CPU\translate('Banner 10 web')}}</option>
                                            <option value="Discount 1 web">{{ \App\CPU\translate('Discount Banner 1 web')}}</option>
                                            <option value="Discount 2 web">{{ \App\CPU\translate('Discount Banner 2 web')}}</option>
                                            <option value="Discount 3 web">{{ \App\CPU\translate('Discount Banner 3 web')}}</option>
                                            <option value="Discount 4 web">{{ \App\CPU\translate('Discount Banner 4 web')}}</option>
                                            <option value="Discount 5 web">{{ \App\CPU\translate('Discount Banner 5 web')}}</option>
                                            <option value="Choice 1 web">{{ \App\CPU\translate('Creators Choice 1 web')}}</option>
                                            <option value="Choice 2 web">{{ \App\CPU\translate('Creators Choice 2 web')}}</option>
                                            <option value="Choice 3 web">{{ \App\CPU\translate('Creators Choice 3 web')}}</option>
                                            <option value="Choice 4 web">{{ \App\CPU\translate('Creators Choice 4 web')}}</option>
                                            <option value="Choice 5 web">{{ \App\CPU\translate('Creators Choice 5 web')}}</option>
                                            <option value="Tips 1 web">{{ \App\CPU\translate('Tips 1 web')}}</option>
                                            <option value="Tips 2 web">{{ \App\CPU\translate('Tips 2 web')}}</option>
                                            <option value="Tips 3 web">{{ \App\CPU\translate('Tips 3 web')}}</option>
                                            <option value="Tips 4 web">{{ \App\CPU\translate('Tips 4 web')}}</option>
                                            <option value="Tips 5 web">{{ \App\CPU\translate('Tips 5 web')}}</option>
                                            <option value="Tips 6 web">{{ \App\CPU\translate('Tips 6 web')}}</option>
                                            <option value="Luxury BG web">{{ \App\CPU\translate('Luxury BG web')}}</option>
                                            <option value="Day BG web">{{ \App\CPU\translate('Deal of the day BG web')}}</option>
                                            <option value="Discount BG web">{{ \App\CPU\translate('Discount BG web')}}</option>
                                            <option value="Product page banner 1 web">{{ \App\CPU\translate('Product page banner 1 web')}}</option>
                                            <option value="Product page banner 2 web">{{ \App\CPU\translate('Product page banner 2 web')}}</option>
                                            
                                            <!-- <option
                                                value="Footer Banner">{{ \App\CPU\translate('Footer Banner')}}</option>
                                            <option
                                                value="Popup Banner">{{ \App\CPU\translate('Popup Banner')}}</option>
                                            <option
                                                value="Main Section Banner">{{ \App\CPU\translate('Main Section Banner')}}</option> -->
                                            @if(theme_root_path() == 'theme_aster')
                                            <option
                                                value="Header Banner">{{ \App\CPU\translate('Header Banner')}}</option>
                                            <option
                                                value="Sidebar Banner">{{ \App\CPU\translate('Sidebar Banner')}}</option>
                                            <option
                                                value="Top Side Banner">{{ \App\CPU\translate('Top Side Banner')}}</option>
                                            @endif
                                        </select>
                                    </div>
                                    
                                    

                                    <div class="form-group">
                                        <label for="resource_id"
                                               class="title-color text-capitalize">{{\App\CPU\translate('resource_type')}}</label>
                                        <select onchange="display_data(this.value)"
                                                class="js-example-responsive form-control w-100"
                                                name="resource_type" required>
                                            <option value="product">{{ \App\CPU\translate('Product')}}</option>
                                            <option value="category">{{ \App\CPU\translate('Category')}}</option>
                                            <option value="shop">{{ \App\CPU\translate('Shop')}}</option>
                                            <option value="brand">{{ \App\CPU\translate('Brand')}}</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="resource-product">
                                        <label for="product_id"
                                               class="title-color text-capitalize">{{\App\CPU\translate('product')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="product_id">
                                            @foreach(\App\Model\Product::where('status',1)->get() as $product)
                                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group  d--none" id="resource-category">
                                        <label for="name"
                                               class="title-color text-capitalize">{{\App\CPU\translate('category')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="category_id" id="sub_category_id">
                                            <option value ="0">Select Type</option>
                                          @foreach(\App\CPU\CategoryManager::parents() as $category)
                                         <option value="{{$category['id']}}" onchange="sub_category({{$category['id']}})">{{$category['name']}}</option>
                                           @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group d--none" id="resource-shop">
                                        <label for="shop_id" class="title-color">{{\App\CPU\translate('shop')}}</label>
                                        <select class="w-100 js-example-responsive form-control" name="shop_id">
                                            @foreach(\App\Model\Shop::active()->get() as $shop)
                                                <option value="{{$shop['id']}}">{{$shop['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group d--none" id="resource-brand">
                                        <label for="brand_id"
                                               class="title-color text-capitalize">{{\App\CPU\translate('brand')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="brand_id">
                                            @foreach(\App\Model\Brand::all() as $brand)
                                                <option value="{{$brand['id']}}">{{$brand['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                     <div class="form-group sub_category_field  d--none">
                                        <label for="name" class="title-color text-capitalize">{{\App\CPU\translate('sub_category')}}</label>
                                        <select class="js-example-responsive form-control w-100 sub_category_cls"
                                                name="sub_category" id="sub_sub_category_id" >
                                           
                                        </select>
                                    </div>
                                    
                                      <div class="form-group sub_sub_category_field  d--none" >
                                        <label for="name" class="title-color text-capitalize">{{\App\CPU\translate('sub_sub_category')}}</label>
                                        <select class="js-example-responsive form-control w-100 sub_sub_category_cls"
                                                name="sub_sub_category"  >
                                        
                                        </select>
                                    </div>

                                    <div class="form-group mt-4 mb-0">
                                        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('banner_URL')}}</label>
                                        <input type="url" name="url" class="form-control" id="url" required placeholder="{{ translate('Enter_url') }}">
                                    </div>

                                    <div class="form-group mt-4 mb-0">
                                        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('banner_Discount')}}</label>
                                        <input type="text" name="discount" class="form-control" id="url"  placeholder="{{ translate('Enter_discount') }}">
                                    </div>


        <div class="col-md-6 d-flex flex-column justify-content-end mt-4">
    <div>
       
        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('Video')}}</label>
        <span class="text-info">ratio 9:16 </span>
        <div class="custom-file text-left">
            <input type="file" name="video" id="mbVideoFileUploader" class="custom-file-input"
                accept=".mp4, .mov, .avi, .mkv, .flv, .wmv, .webm, video/*">
            <label class="custom-file-label title-color" for="mbVideoFileUploader">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
        </div>
    </div>
</div>


                                    {{-- For Theme Fashion - New input Field - Start --}}
                                    @if(theme_root_path() == 'theme_fashion')
                                    <div class="form-group mt-4 input_field_for_main_banner">
                                        <label for="button_text" class="title-color text-capitalize">{{ translate('Button_Text')}}</label>
                                        <input type="text" name="btn_text" class="form-control" id="button_text" placeholder="{{ translate('Enter_button_text') }}">
                                    </div>
                                    <div class="form-group mt-4 mb-0 input_field_for_main_banner">
                                        <label for="background_color" class="title-color text-capitalize">{{ \App\CPU\translate('background_color')}}</label>
                                        <input type="color" name="background_color" class="form-control" id="background_color" value="#fee440">
                                    </div>
                                    @endif
                                    {{-- For Theme Fashion - New input Field - End --}}

                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-end">
                                    <div>
                                        <center class="mb-30 mx-auto">
                                            <img
                                                class="ratio-4:1"
                                                id="mbImageviewer"
                                                src="{{asset('assets/front-end/img/placeholder.png')}}"
                                                alt="banner image"/>
                                        </center>
                                        <label for="name"
                                         class="title-color text-capitalize">{{ \App\CPU\translate('Image')}}</label>
                                        <span class="text-info" id="theme_ratio">( {{\App\CPU\translate('ratio')}} 4:1 )</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="mbimageFileUploader"
                                                class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label title-color"
                                                for="mbimageFileUploader">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                        </div>

                                        {{-- For Theme Fashion - New input Field - Start --}}
                                        @if(theme_root_path() == 'theme_fashion')
                                        <div class="form-group mt-4 input_field_for_main_banner">
                                            <label for="title" class="title-color text-capitalize">{{ translate('Title')}}</label>
                                            <input type="text" name="title" class="form-control" id="title" placeholder="{{ translate('Enter_banner_title') }}">
                                        </div>
                                        <div class="form-group mb-0 input_field_for_main_banner">
                                            <label for="sub_title" class="title-color text-capitalize">{{ translate('Sub_Title')}}</label>
                                            <input type="text" name="sub_title" class="form-control" id="sub_title" placeholder="{{ translate('Enter_banner_sub_title') }}">
                                        </div>
                                        @endif
                                        {{-- For Theme Fashion - New input Field - End --}}

                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end flex-wrap gap-10">
                                    <button class="btn btn-secondary cancel px-4" type="reset">{{ \App\CPU\translate('reset')}}</button>
                                    <button id="add" type="submit"
                                            class="btn btn--primary px-4">{{ \App\CPU\translate('save')}}</button>
                                    <button id="update"
                                       class="btn btn--primary d--none text-white">{{ \App\CPU\translate('update')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="banner-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-6 mb-2 mb-md-0">
                                <h5 class="mb-0 text-capitalize d-flex gap-2">
                                    {{ \App\CPU\translate('banner_table')}}
                                    <span
                                        class="badge badge-soft-dark radius-50 fz-12">{{ $banners->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-md-8 col-lg-6">
                                <div
                                    class="d-flex align-items-center justify-content-md-end flex-wrap flex-sm-nowrap gap-2">
                                    <!-- Search -->
                                    <form action="{{ url()->current() }}" method="GET">
                                        <div class="input-group input-group-merge input-group-custom">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="tio-search"></i>
                                                </div>
                                            </div>
                                            <input id="datatableSearch_" type="search" name="search"
                                                   class="form-control"
                                                   placeholder="{{ \App\CPU\translate('Search_by_Banner_Type')}}"
                                                   aria-label="Search orders" value="{{ $search }}">
                                            <button type="submit" class="btn btn--primary">
                                                {{ \App\CPU\translate('Search')}}
                                            </button>
                                        </div>
                                    </form>
                                    <!-- End Search -->

                                    <div id="banner-btn">
                                        <button id="main-banner-add" class="btn btn--primary text-nowrap">
                                            <i class="tio-add"></i>
                                            {{ \App\CPU\translate('add_banner')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="columnSearchDatatable"
                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th class="pl-xl-5">{{\App\CPU\translate('SL')}}</th>
                                <th>{{\App\CPU\translate('image')}}</th>
                                <th>{{\App\CPU\translate('banner_type')}}</th>
                                <th>{{\App\CPU\translate('published')}}</th>
                                <th class="text-center">{{\App\CPU\translate('action')}}</th>
                            </tr>
                            </thead>
                            @foreach($banners as $key=>$banner)
                                <tbody>
                                <tr id="data-{{$banner->id}}">
                                    <td class="pl-xl-5">{{$banners->firstItem()+$key}}</td>
                                    <td>
                                        <img class="ratio-4:1" width="80"
                                             onerror="this.src='{{asset('assets/front-end/img/placeholder.png')}}'"
                                             src="{{asset('storage/banner')}}/{{$banner['photo']}}">
                                    </td>
                                    <td>{{\App\CPU\translate(str_replace('_',' ',$banner->banner_type))}}</td>
                                    <td>
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher_input status"
                                                   id="{{$banner->id}}" <?php if ($banner->published == 1) echo "checked" ?>>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-10 justify-content-center">
                                            <a class="btn btn-outline--primary btn-sm cursor-pointer edit"
                                               title="{{ \App\CPU\translate('Edit')}}"
                                               href="{{route('admin.banner.edit',[$banner['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-sm cursor-pointer delete"
                                               title="{{ \App\CPU\translate('Delete')}}"
                                               id="{{$banner['id']}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{$banners->links()}}
                        </div>
                    </div>

                    @if(count($banners)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                 src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('ready', function () {
            theme_wise_ration();
        });

        function theme_wise_ration(){
            let banner_type = $('#banner_type_select').val();
            let theme = '{{ theme_root_path() }}';
            let theme_ratio = {!! json_encode(THEME_RATIO) !!};
            let get_ratio= theme_ratio[theme][banner_type];

            $('#theme_ratio').text(get_ratio);
        }

        $('#banner_type_select').on('change',function(){
            theme_wise_ration();
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });

        function display_data(data) {

            $('#resource-product').hide()
            $('#resource-brand').hide()
            $('#resource-category').hide()
            $('#resource-shop').hide()

            if (data === 'product') {
                $('#resource-product').show()
            } else if (data === 'brand') {
                $('#resource-brand').show()
            } else if (data === 'category') {
                $('#resource-category').show()
            } else if (data === 'shop') {
                $('#resource-shop').show()
            }
        }
    </script>
    <script>
        function mbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#mbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mbimageFileUploader").change(function () {
            mbimagereadURL(this);
        });

        function fbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#fbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#fbimageFileUploader").change(function () {
            fbimagereadURL(this);
        });

        function pbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#pbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#pbimageFileUploader").change(function () {
            pbimagereadURL(this);
        });

    </script>
    <script>
        $('#main-banner-add').on('click', function () {
            $('#main-banner').show();
        });

        $('.cancel').on('click', function () {
            $('.banner_form').attr('action', "{{route('admin.banner.store')}}");
            $('#main-banner').hide();
        });

        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") === true) {
                var status = 1;
            } else if ($(this).prop("checked") === false) {
                var status = 0;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.banner.status')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if (data == 1) {
                        toastr.success('{{\App\CPU\translate('Banner_published_successfully')}}');
                    } else {
                        toastr.success('{{\App\CPU\translate('Banner_unpublished_successfully')}}');
                    }
                }
            });
        });

        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{\App\CPU\translate('Are_you_sure_delete_this_banner')}}?",
                text: "{{\App\CPU\translate('You_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{\App\CPU\translate('Yes')}}, {{\App\CPU\translate('delete_it')}}!',
                type: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.banner.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function (response) {
                            console.log(response)
                            toastr.success('{{\App\CPU\translate('Banner_deleted_successfully')}}');
                            $('#data-' + id).hide();
                        }
                    });
                }
            })
        });
    </script>
    <!-- Page level plugins -->
    <!-- New Added JS - Start -->
    <script>
        $('#banner_type_select').on('change',function(){
            let input_value = $(this).val();

            if (input_value == "Main Banner") {
                $('.input_field_for_main_banner').removeClass('d-none');
            } else {
                $('.input_field_for_main_banner').addClass('d-none');
            }
        });
    </script>
    <!-- New Added JS - End -->
    
    <!--- sub- category for the banner-->
   <script>
    $(document).ready(function () {
        // Bind onchange event to the select element
        $('select#sub_category_id').on('change', function () {
            // Get the selected value
            var selectedValue = $(this).val();
            
            // Call the sub_category function with the selected value
            sub_category(selectedValue);
        });
    });

    function sub_category(id) {
        $('.sub_sub_category_field').hide();
               var data = {
            position: id,
            _token: '{!! csrf_token() !!}'
        };
        
        $.ajax({
            url: "{{route('admin.sub-category.get_sub_category')}}", // Replace with your PHP script URL{{route('shop.apply')}}
            type: 'POST', // or 'GET' depending on your PHP script
            data: data,
            dataType: "JSON",
            success: function(response) {
          if(response.status == 1)
               {
                   $('.sub_category_field').show();
                   $('.sub_category_cls').html(response.sub_category)
               }
            },
            error: function(error) {
                // Handle errors here
                console.error('Error:', error);
            }
        });
    }
</script>

 <script>
    $(document).ready(function () {
        // Bind onchange event to the select element
        $('select#sub_sub_category_id').on('change', function () {
            // Get the selected value
            var selectedValue = $(this).val();
            
            // Call the sub_category function with the selected value
            sub_sub_category(selectedValue);
        });
    });

    function sub_sub_category(id) {
       
               var data = {
            parent_id: id,
            _token: '{!! csrf_token() !!}'
        };
        
        $.ajax({
            url: "{{route('admin.sub-sub-category.get_sub_sub_category')}}", // Replace with your PHP script URL{{route('shop.apply')}}
            type: 'POST', // or 'GET' depending on your PHP script
            data: data,
            dataType: "JSON",
            success: function(response) {
          if(response.status == 1)
               {
                   $('.sub_sub_category_field').show();
                   $('.sub_sub_category_cls').html(response.sub_sub_category)
               }
            },
            error: function(error) {
                // Handle errors here
                console.error('Error:', error);
            }
        });
    }
</script>
@endpush
