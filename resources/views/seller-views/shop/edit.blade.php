@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Shop Edit'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <!-- Content Row -->
    <div class="content container-fluid">

        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('/public/assets/back-end/img/shop-info.png') }}" alt="">
                {{ \App\CPU\translate('Edit_Shop_Info') }}
            </h2>
        </div>
        <!-- End Page Title -->

        @php($seller = \App\Model\Seller::where(['id' => auth('seller')->id()])->first())

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 ">{{ \App\CPU\translate('Edit_Shop_Info') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('seller.shop.update', [$shop->id]) }}" method="post"
                            style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="comp_name" class="title-color">{{ \App\CPU\translate('Shop_Name') }}
                                            <span class="text-danger">*</span> <span class="ml-2" data-toggle="tooltip"
                                                data-placement="top"
                                                title="{{ \App\CPU\translate('This name will be appear in website') }}">
                                                <img class="info-img"
                                                    src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                    alt="img"></span></label>
                                        <input type="text" name="comp_name" value="{{ $shop->comp_name }}"
                                            class="form-control" id="comp_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">{{ \App\CPU\translate('Contact') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="contact" value="{{ $shop->contact }}"
                                            class="form-control" id="name" required readonly
                                            {{ $seller->profile_edit_status == 0 ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label for="address"
                                            class="title-color">{{ \App\CPU\translate('Registered address') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea type="text" rows="4" name="address" value="" class="form-control" id="address" required
                                            readonly {{ $seller->profile_edit_status == 0 ? 'readonly' : '' }}>{{ $shop->address }}</textarea>
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <label for="address" class="title-color">Reg. Certificate No </label>
                                        <input type="text" class="form-control form-control-user" name="reg_cert"
                                            placeholder="{{ \App\CPU\translate('Company Registration Certificate No.') }}"
                                            value="{{ $shop->reg_cert }}">
                                    </div>
                                    <div class="form-group" style="display:none;">
                                        <div class="form-group">
                                            <label for="name" class="title-color">Certificate image</label>
                                            <div class="custom-file text-left">
                                                <input type="file" name="reg_cert_image"
                                                    id="Certificate_customFileUpload" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                    for="Certificate_customFileUpload">{{ \App\CPU\translate('choose') }}
                                                    {{ \App\CPU\translate('file') }}</label>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <img class="upload-img-view" id="Certificate_viewer"
                                                onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                                src="{{ asset('storage/app/public/shop/' . $shop->reg_cert_image) }}"
                                                alt="Product thumbnail" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address" class="title-color">GST <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-user" name="gst_no"
                                            placeholder="{{ \App\CPU\translate('GST No.') }}" value="{{ $shop->gst_no }}"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="title-color">
                                            </span>{{ \App\CPU\translate('Shop Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $shop->name }}"
                                            class="form-control" id="name" required readonly
                                            {{ $seller->profile_edit_status == 0 ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="title-color">PAN <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-user" name="pan"
                                            placeholder="{{ \App\CPU\translate('PAN No.') }}"
                                            value="{{ $shop->pan }}" required>
                                    </div>
                                </div>

                                {{-- Bank INFO --}}
                                <div class="col-md-4 mb-4">
                                    <label for="name" class="title-color">{{ \App\CPU\translate('Bank Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" value="{{ $shop->bank_name }}"
                                        class="form-control" id="name" required readonly>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="ifsc" class="title-color">{{ \App\CPU\translate('IFSC') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="ifsc" value="{{ $shop->ifsc }}"
                                        class="form-control" id="ifsc" required readonly>
                                </div>

                                <div class="col-md-4 mb-4">
                                    <label for="account_no" class="title-color">{{ \App\CPU\translate('Account No') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="account_no" value="{{ $shop->acc_no }}"
                                        class="form-control" id="account_no" required readonly>
                                </div>





                                <!-- Brand Name -->
                                <div class="col-md-4 mb-4">
                                    <label for="brand_name" class="title-color">
                                        {{ \App\CPU\translate('Brand Name') }}
                                        {{-- <span class="text-danger">*</span> --}}
                                    </label>
                                    <input type="text" name="brand_name" value="{{ $shop->brand_name ?? '' }}"
                                        class="form-control" id="brand_name">
                                </div>

                                <!-- Brand Logo -->
                                <div class="col-md-4 mb-4">
                                    <label for="brand_logo" class="title-color">
                                        {{ \App\CPU\translate('Brand Logo') }}
                                        {{-- <span class="text-danger">*</span> --}}
                                    </label>
                                    <input type="file" name="brand_logo" class="form-control" id="brand_logo"
                                        accept="image/*">

                                    @if (!empty($shop->brand_logo))
                                        <div class="mt-2">
                                            <img src="{{ env('CLOUDFLARE_R2_PUBLIC_URL') . $shop->brand_logo }}"
                                                alt="Brand Logo" height="50">
                                        </div>
                                    @endif
                                </div>

                                <!-- Trademark -->
                                <div class="col-md-4 mb-4">
                                    <label for="trademark" class="title-color">
                                        {{ \App\CPU\translate('Trademark') }}
                                        {{-- <span class="text-danger">*</span> --}}
                                    </label>
                                    <input type="file" name="trademark" class="form-control" id="trademark"
                                        accept="image/*" >

                                    @if (!empty($shop->trademark))
                                        <div class="mt-2">
                                            <a href="{{ env('CLOUDFLARE_R2_PUBLIC_URL') . $shop->trademark }}"
                                                alt="Trademark" target="_blank" height="50">View</a>
                                        </div>
                                    @endif



                                </div>





                                <div class="col-md-12 mb-3 text-center">
                                    <img src="{{ asset('storage/app/public/seller/' . $data->signature) }}"
                                        onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                        height="200" alt="">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="name"
                                        class="title-color">{{ \App\CPU\translate('Upload Signature') }} <span class="text-danger">*</span> </label>
                                    <input type="file" name="signature" value="" class="form-control"
                                        id="Signature_id" required>
                                </div>














                            </div>


                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-danger"
                                    href="{{ route('seller.shop.view') }}">{{ \App\CPU\translate('Cancel') }}</a>
                                <button type="submit" class="btn btn--primary"
                                    id="btn_update">{{ \App\CPU\translate('Update') }}</button>
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
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#gstviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readCertificateURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#Certificate_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readPanURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#pan_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readimagURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#image_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewerBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBottomBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewerBottomBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#gstcustomFileUpload").change(function() {
            readURL(this);
        });

        $("#Certificate_customFileUpload").change(function() {
            readCertificateURL(this);
        });

        $("#pan_customFileUpload").change(function() {
            readPanURL(this);
        });

        $("#image_customFileUpload").change(function() {
            readimagURL(this);
        });

        $("#customFileUpload").change(function() {
            readURL(this);
        });

        $("#BannerUpload").change(function() {
            readBannerURL(this);
        });
        $("#BottomBannerUpload").change(function() {
            readBottomBannerURL(this);
        });
    </script>
@endpush
