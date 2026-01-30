@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Update Notification'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/push_notification.png')}}" alt="">
                {{\App\CPU\translate('push_notification_update')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.provider-notification.update',[$notification['id']])}}" method="post"
                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                        enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('Title')}}</label>
                                <input type="text" value="{{$notification['title']}}" name="title" class="form-control"
                                        placeholder="{{\App\CPU\translate('New notification')}}" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">{{\App\CPU\translate('Description')}}</label>
                                <textarea name="description" class="form-control"
                                            required>{{$notification['description']}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <center>
                                <img class="upload-img-view mt-4" 
                                    id="viewer"
                                    onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'"
                                    src="{{asset('storage/app/public/notification')}}/{{$notification['image']}}"
                                        alt="image"/>
                            </center>
                            <label class="title-color">{{\App\CPU\translate('Image')}}</label>
                            <span class="text-info"> ( {{\App\CPU\translate('Ratio_1:1')}}  )</span>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{\App\CPU\translate('Choose file')}}</label>
                            </div>
                        </div>



                        
                                    <div class="form-group">
                                        <label for="resource_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('resource_type') }}</label>
                                        <select onchange="display_data(this.value)"
                                            class="js-example-responsive form-control w-100" name="resource_type" required>
                                            <option value="product"
                                                {{ $notification['resource_type'] == 'product' ? 'selected' : '' }}>Product</option>
                                            <option value="category"
                                                {{ $notification['resource_type'] == 'category' ? 'selected' : '' }}>Category</option>
                                            <option value="shop" {{ $notification['resource_type'] == 'shop' ? 'selected' : '' }}>
                                                Shop</option>
                                            <option value="brand" {{ $notification['resource_type'] == 'brand' ? 'selected' : '' }}>
                                                Brand</option>
                                        </select>
                                    </div>

                                    <div class="form-group " id="resource-product"
                                        style="display: {{ $notification['resource_type'] == 'product' ? 'block' : 'none' }}">
                                        <label for="product_id"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('product') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="product_id">
                                            @foreach (\App\Model\Product::active()->get() as $product)
                                                <option value="{{ $product['id'] }}"
                                                    {{ $notification['resource_id'] == $product['id'] ? 'selected' : '' }}>
                                                    {{ $product['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group " id="resource-category"
                                        style="display: {{ $notification['resource_type'] == 'category' ? 'block' : 'none' }}">
                                        <label for="name"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('category') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="category_id"
                                            id="sub_category_id">
                                            @foreach (\App\CPU\CategoryManager::parents() as $category)
                                                <option value="{{ $category['id'] }}"
                                                    onchange="sub_category({{ $category['id'] }})"
                                                    {{ $notification['resource_id'] == $category['id'] ? 'selected' : '' }}>
                                                    {{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                     @if ($notification['sub_category'])
                                        <div class="form-group sub_category_field">
                                            <label for="name"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('sub_category') }}</label>
                                            <select class="js-example-responsive form-control w-100 sub_category_cls"
                                                name="sub_category" id="sub_sub_category_id">
                                                @foreach (\App\CPU\CategoryManager::parents() as $category)
                                                    <option value="{{ $category['id'] }}"
                                                        {{ $notification['resource_id'] == $category['id'] ? 'selected' : '' }}>
                                                        {{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group sub_category_field  d--none">
                                            <label for="name"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('sub_category') }}</label>
                                            <select class="js-example-responsive form-control w-100 sub_category_cls"
                                                name="sub_category" id="sub_sub_category_id">

                                            </select>
                                        </div>
                                    @endif

                                    @if ($notification['sub_sub_category'])
                                        <div class="form-group sub_sub_category_field ">
                                            <label for="name"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('sub_sub_category') }}</label>
                                            <select class="js-example-responsive form-control w-100 sub_sub_category_cls"
                                                name="sub_sub_category">
                                                @foreach (\App\CPU\CategoryManager::parents() as $category)
                                                    <option value="{{ $category['id'] }}"
                                                        {{ $notification['resource_id'] == $category['id'] ? 'selected' : '' }}>
                                                        {{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="form-group sub_sub_category_field  d--none">
                                            <label for="name"
                                                class="title-color text-capitalize">{{ \App\CPU\translate('sub_sub_category') }}</label>
                                            <select class="js-example-responsive form-control w-100 sub_sub_category_cls"
                                                name="sub_sub_category">

                                            </select>
                                        </div>
                                    @endif





                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-secondary">{{\App\CPU\translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{\App\CPU\translate('Update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Table -->
    </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });


      $(document).ready(function() {
            // Bind onchange event to the select element
            $('select#sub_category_id').on('change', function() {
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
                url: "{{ route('admin.sub-category.get_sub_category') }}", // Replace with your PHP script URL{{ route('shop.apply') }}
                type: 'POST', // or 'GET' depending on your PHP script
                data: data,
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 1) {
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

        $(document).ready(function() {
            // Bind onchange event to the select element
            $('select#sub_sub_category_id').on('change', function() {
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
                url: "{{ route('admin.sub-sub-category.get_sub_sub_category') }}", // Replace with your PHP script URL{{ route('shop.apply') }}
                type: 'POST', // or 'GET' depending on your PHP script
                data: data,
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 1) {
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

        $(document).ready(function() {
            let category_id = "{{ $notification['resource_id'] }}";
            let sub_category_id = "{{ $notification['sub_category'] }}";
            let sub_sub_category_id = "{{ $notification['sub_sub_category'] }}";

            var data = {
                category_id: category_id,
                sub_category_id: sub_category_id,
                sub_sub_category_id: sub_sub_category_id,
                _token: '{!! csrf_token() !!}'
            };

            $.ajax({
                url: "{{ route('admin.sub-category.get_selected_category') }}", // Replace with your PHP script URL{{ route('shop.apply') }}
                type: 'POST', // or 'GET' depending on your PHP script
                data: data,
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 1) {
                        $('.sub_category_cls').html(response.sub_category);
                        $('.sub_sub_category_cls').html(response.sub_sub_category);
                    } else {
                        $('.sub_category_cls').html(response.sub_category);
                    }
                },
                error: function(error) {
                    // Handle errors here
                    console.error('Error:', error);
                }
            });
        });
@endpush
