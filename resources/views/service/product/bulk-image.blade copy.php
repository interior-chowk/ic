@extends('layouts.back-end.app-seller')

@section('title', \App\CPU\translate('Product Bulk Import'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
    <div class="col-md-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn--primary">
                    <a style="color:#fff" href="{{ route('seller.product.search_bulk-import') }}">
                        {{ \App\CPU\translate('Back') }}
                    </a>
                </button>
            </div>
        <!-- Page Title -->
        <div class="mb-4">
            <h2 class="h1 mb-1 text-capitalize">
                <img src="{{asset('/public/assets/back-end/img/bulk-import.png')}}" class="mb-1 mr-1" alt="">
                {{\App\CPU\translate('bulk_Import')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-12">
                <div class="card card-body">
                    <h1 class="display-4">{{\App\CPU\translate('Instructions')}} : </h1>
                    <p>1. {{\App\CPU\translate('Download the format file and fill it with proper data')}}.</p>
                    <p>2. {{\App\CPU\translate('You can download the example file to understand how the data must be filled')}}.</p>
                    <p>3. {{\App\CPU\translate('Once you have downloaded and filled the format file, upload it in the form below and submit')}}.</p>
                    <p>4. {{\App\CPU\translate('After uploading products you need to edit them and set products images and choices')}}.</p>
                    <p>5. {{\App\CPU\translate('You can get brand and category id from their list, please input the right ids')}}.</p>
                    <p>6. {{\App\CPU\translate('You can upload your product images in product folder from gallery and copy image`s path')}}.</p>
                </div>
            </div>

            <div class="col-12 mt-2">
                <form class="product-form" action="{{route('seller.product.bulk_image_import')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card rest-part">
                        <div class="px-3 py-4 d-flex flex-wrap align-items-center gap-10 justify-content-center">
                            <h4 class="mb-0">{{\App\CPU\translate('Import Products File')}}</h4>
                            <input type="file" name="images[]" multiple>

                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row justify-content-center">
                                    <div class="col-auto">
                                        <div class="upload-file">
                                            <input type="file" name="products_file" accept=".xlsx, .xls" class="upload-file__input">
                                            <div class="upload-file__img upload-file__img_drag">
                                                <img src="{{asset('/public/assets/back-end/img/drag-upload-file.png')}}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-end">
                                <button type="reset" id="reset" onclick="resetImg();" class="btn btn-secondary px-4">{{\App\CPU\translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary px-4">{{\App\CPU\translate('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div class="card card-body">
                    <h1 class="display-4">Fields Description : </h1>
                    <p><strong>name</strong> = Product Title<br/>
                    <strong>categrory_id</strong> = Main Category Id<br/>
                    <strong>sub_category_id</strong> = Sub category Id<br/>
                    <strong>sub_sub_category_id</strong> = Sub sub category Id<br/>
                    <strong>brand_id</strong> = Brand Id<br/>
                    <strong>unit</strong> = Unit<br/>
                    <strong>hsn_code</strong> = HSN Code<br/>
                    <strong>sku</strong> = SKU<br/>
                    <strong>unit_price</strong> = Selling / Listing  Price<br/>
                    <strong>purchase_price</strong> = MRP<br/>
                    <strong>tax</strong> = Tax (Included in %)<br/>
                    <strong>discount</strong> = Flat discount in Rs.<br/>
                    <strong>discount_type</strong> = Discount Type (flat or percent)<br/>
                    <strong>current_stock</strong> = Current Stock Qty<br/>
                    <strong>min_qty</strong> = Minimum order qty<br/>
                    <strong>refundable</strong> = Refundable Available (1 / 0)* <br/>
                    <strong>return_days</strong> = Return Days<br/>
                    <strong>free_delivery</strong> = Fee Delivery Available (1 / 0)* <br/>
                    <strong>instant_delivery</strong> = Instant Delivery Available (1 / 0)* <br/>
                    <strong>length</strong> = Length in cm<br/>
                    <strong>breadth</strong> = Breadth in cm<br/>
                    <strong>height</strong> = Height in cm <br/>
                    <strong>weight</strong> = Weight in KG<br/>
                    <strong>details</strong> = Description of Product<br/>
                    <strong>thumbnail</strong> = Thumbnail (Product Image link)<br/>
                    <strong>youtube_video_url</strong> = Youtube Video Url<br/>
                    <strong>tags</strong> = Search Tag</p>
                    <br/>
                    <small>* 1 = Yes, 0 = No</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";

        $('.upload-file__input').on('change', function() {
            $(this).siblings('.upload-file__img').find('img').attr({
                'src': '{{asset('/public/assets/back-end/img/excel.png')}}',
                'width': 80
            });
        });

        function resetImg() {
            $('.upload-file__img img').attr({
                'src': '{{asset('/public/assets/back-end/img/drag-upload-file.png')}}',
                'width': 'auto'
            });
        }
    </script>
@endpush
