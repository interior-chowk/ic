@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Update Notification'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/push_notification.png') }}" alt="">
                {{ \App\CPU\translate('push_notification_update') }}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.provider-notification.update', [$notification['id']]) }}" method="post"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <!-- Title -->
                            <div class="form-group">
                                <label class="input-label">{{ \App\CPU\translate('Title') }}</label>
                                <input type="text" value="{{ old('title', $notification['title']) }}" name="title"
                                    class="form-control" placeholder="{{ \App\CPU\translate('New notification') }}"
                                    required>
                            </div>

                            <!-- Description -->
                            <div class="form-group mb-0">
                                <label class="input-label">{{ \App\CPU\translate('Description') }}</label>
                                <textarea name="description" class="form-control" required>{{ old('description', $notification['description']) }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Image -->
                            <center>
                                <img class="upload-img-view mt-4" id="viewer"
                                    onerror="this.src='{{ asset('public/assets/back-end/img/160x160/img2.jpg') }}'"
                                    src="{{ asset('storage/app/public/notification') }}/{{ $notification['image'] }}"
                                    alt="image" />
                            </center>
                            <label class="title-color">{{ \App\CPU\translate('Image') }}</label>
                            <span class="text-info">( {{ \App\CPU\translate('Ratio_1:1') }} )</span>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label"
                                    for="customFileEg1">{{ \App\CPU\translate('Choose file') }}</label>
                            </div>
                        </div>

                        <!-- Resource Type -->
                        <div class="form-group col-12">
                            <label class="title-color text-capitalize">{{ \App\CPU\translate('resource_type') }}</label>
                            <select onchange="display_data(this.value)" class="js-example-responsive form-control w-100"
                                name="resource_type" required>
                                <option value="product"
                                    {{ old('resource_type', $notification['resource_type']) == 'product' ? 'selected' : '' }}>
                                    Product</option>
                                <option value="category"
                                    {{ old('resource_type', $notification['resource_type']) == 'category' ? 'selected' : '' }}>
                                    Category</option>
                                <option value="shop"
                                    {{ old('resource_type', $notification['resource_type']) == 'shop' ? 'selected' : '' }}>
                                    Shop</option>
                                <option value="brand"
                                    {{ old('resource_type', $notification['resource_type']) == 'brand' ? 'selected' : '' }}>
                                    Brand</option>
                            </select>
                        </div>

                        <!-- Product -->
                        <div class="form-group col-12" id="resource-product"
                            style="display: {{ old('resource_type', $notification['resource_type']) == 'product' ? 'block' : 'none' }}">
                            <label class="title-color text-capitalize">{{ \App\CPU\translate('product') }}</label>
                            <select class="js-example-responsive form-control w-100" name="product_id">
                                @foreach (\App\Model\Product::active()->get() as $product)
                                    <option value="{{ $product['id'] }}"
                                        {{ old('product_id', $notification['resource_id']) == $product['id'] ? 'selected' : '' }}>
                                        {{ $product['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="form-group col-12" id="resource-category"
                            style="display: {{ old('resource_type', $notification['resource_type']) == 'category' ? 'block' : 'none' }}">
                            <label class="title-color text-capitalize">{{ \App\CPU\translate('category') }}</label>
                            <select class="js-example-responsive form-control w-100" name="category_id" id="category_id">
                                <option value="">{{ \App\CPU\translate('Select Category') }}</option>
                                @foreach (\App\CPU\CategoryManager::parents() as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ old('category_id', $notification['resource_id']) == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Category -->
                        <div
                            class="form-group col-12 sub_category_field {{ $notification['sub_category'] || old('sub_category_id') ? '' : 'd--none' }}">
                            <label class="title-color text-capitalize">{{ \App\CPU\translate('sub_category') }}</label>
                            <select class="js-example-responsive form-control w-100" name="sub_category_id"
                                id="sub_category_id">
                                @if ($notification['sub_category'])
                                    <option value="{{ $notification['sub_category'] }}" selected>
                                        {{ $notification->subCategory->name ?? '' }}
                                    </option>
                                @endif
                                @if (old('sub_category_id'))
                                    <option value="{{ old('sub_category_id') }}" selected>
                                        {{ old('sub_category_id') }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <!-- Sub Sub Category -->
                        <div
                            class="form-group col-12 sub_sub_category_field {{ $notification['sub_sub_category'] || old('sub_sub_category_id') ? '' : 'd--none' }}">
                            <label class="title-color text-capitalize">{{ \App\CPU\translate('sub_sub_category') }}</label>
                            <select class="js-example-responsive form-control w-100" name="sub_sub_category_id"
                                id="sub_sub_category_id">
                                @if ($notification['sub_sub_category'])
                                    <option value="{{ $notification['sub_sub_category'] }}" selected>
                                        {{ $notification->subSubCategory->name ?? '' }}
                                    </option>
                                @endif
                                @if (old('sub_sub_category_id'))
                                    <option value="{{ old('sub_sub_category_id') }}" selected>
                                        {{ old('sub_sub_category_id') }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" class="btn btn-secondary">{{ \App\CPU\translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#customFileEg1").change(function() {
            readURL(this);
        });

        // Load subcategories
        function getSubCategories(categoryId, selectedSub = null, selectedSubSub = null) {
            $.ajax({
                url: "{{ route('admin.sub-category.get_sub_category') }}",
                type: "POST", // force POST to match your working Add page
                data: {
                    position: categoryId, // must match controller param
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        $('.sub_category_field').show();
                        $('#sub_category_id').html(response.sub_category);

                        if (selectedSub) {
                            $('#sub_category_id').val(selectedSub).trigger('change');
                            if (selectedSubSub) {
                                getSubSubCategories(selectedSub, selectedSubSub);
                            }
                        }
                    } else {
                        $('.sub_category_field').hide();
                        $('.sub_sub_category_field').hide();
                        $('#sub_category_id').html('');
                        $('#sub_sub_category_id').html('');
                    }
                }
            });
        }

        // Load sub-subcategories
        function getSubSubCategories(subCategoryId, selected = null) {
            $.ajax({
                url: "{{ route('admin.sub-sub-category.get_sub_sub_category') }}",
                type: "POST", // force POST
                data: {
                    parent_id: subCategoryId, // must match controller param
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        $('.sub_sub_category_field').show();
                        $('#sub_sub_category_id').html(response.sub_sub_category);

                        if (selected) {
                            $('#sub_sub_category_id').val(selected);
                        }
                    } else {
                        $('.sub_sub_category_field').hide();
                        $('#sub_sub_category_id').html('');
                    }
                }
            });
        }

        // Resource display
        function display_data(type) {
            $('#resource-product, #resource-category, #resource-shop, #resource-brand').hide();
            if (type === 'product') $('#resource-product').show();
            if (type === 'category') $('#resource-category').show();
            if (type === 'shop') $('#resource-shop').show();
            if (type === 'brand') $('#resource-brand').show();
        }

        $(document).ready(function() {
            // Prefill edit case
            let resourceType = "{{ old('resource_type', $notification['resource_type']) }}";
            let categoryId = "{{ old('category_id', $notification['resource_id'] ?? '') }}";
            let subCategoryId = "{{ old('sub_category_id', $notification['sub_category'] ?? '') }}";
            let subSubCategoryId = "{{ old('sub_sub_category_id', $notification['sub_sub_category'] ?? '') }}";

            if (resourceType === 'category' && categoryId) {
                getSubCategories(categoryId, subCategoryId, subSubCategoryId);
            }

            // Category change
            $('#category_id').on('change', function() {
                let id = $(this).val();
                if (id) {
                    getSubCategories(id);
                } else {
                    $('.sub_category_field').hide();
                    $('.sub_sub_category_field').hide();
                    $('#sub_category_id').html('');
                    $('#sub_sub_category_id').html('');
                }
            });

            // Subcategory change
            $('#sub_category_id').on('change', function() {
                let id = $(this).val();
                if (id) {
                    getSubSubCategories(id);
                } else {
                    $('.sub_sub_category_field').hide();
                    $('#sub_sub_category_id').html('');
                }
            });
        });
    </script>
@endpush
