
@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Shop Edit'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
     <!-- Custom styles for this page -->
     <link href="{{asset('assets/back-end/css/croppie.css')}}" rel="stylesheet">
     <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <!-- Content Row -->
    <div class="content container-fluid">

    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{asset('/public/assets/back-end/img/shop-info.png')}}" alt="">
            {{\App\CPU\translate('Edit_Shop_Info')}}
        </h2>
    </div>
    <!-- End Page Title -->

 @php($seller=\App\Model\Seller::where(['id'=>auth('seller')->id()])->first())

 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 ">{{\App\CPU\translate('Edit_Shop_Info')}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('seller.shop.update',[$shop->id])}}" method="post"
                          style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="title-color"><span class="ml-2" data-toggle="tooltip" data-placement="top" title="{{\App\CPU\translate('This name will be appear in application')}}">
                                            <img class="info-img" src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="img">
                                         </span>{{\App\CPU\translate('Shop Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{$shop->name}}" class="form-control" id="name"
                                            required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }}>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="title-color">{{\App\CPU\translate('Contact')}} <!--<span class="text-info">( * {{\App\CPU\translate('country_code_is_must')}} {{\App\CPU\translate('like_for_BD_880')}} )</span>--><span class="text-danger">*</span></label>
                                    <input type="number" name="contact" value="{{$shop->contact}}" class="form-control" id="name"
                                            required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >
                                </div>
                                
                                 <div class="form-group">
                                    <label for="address" class="title-color">{{\App\CPU\translate('Billing / Registered address')}} <span class="text-danger">*</span></label>
                                    <textarea type="text" rows="4" name="billing_address" value="" class="form-control" id="billing_address"
                                            required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >{{$shop->billing_address}}</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="address" class="title-color">{{\App\CPU\translate('Pickup_address')}} <span class="text-danger">*</span></label>
                                    <textarea type="text" rows="4" name="address" value="" class="form-control" id="address"
                                            required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >{{$shop->address}}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">Country <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" id="shop_country" name="country" placeholder="{{\App\CPU\translate('Country')}}" value="{{$shop->country}}" required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" id="shop_state" name="state" placeholder="{{\App\CPU\translate('State')}}" value="{{$shop->state}}" required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" id="shop_city" name="city" placeholder="{{\App\CPU\translate('City')}}" value="{{$shop->city}}" required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" id="shop_pin" name="pincode" placeholder="{{\App\CPU\translate('Pincode')}}" value="{{$shop->pincode}}" required  {{ ($seller->profile_edit_status == 0) ? 'readonly' : '' }} >
                                </div>

                                <div class="form-group" style="display:none;">
                                    <label for="address" class="title-color">Reg. Certificate No </label>
                                    <input type="text" class="form-control form-control-user" name="reg_cert" placeholder="{{\App\CPU\translate('Company Registration Certificate No.')}}" value="{{$shop->reg_cert}}" >
                                </div>

                                <div class="form-group" style="display:none;">
                                    <div class="form-group">
                                        <label for="name" class="title-color">Certificate image</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="reg_cert_image" id="Certificate_customFileUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="Certificate_customFileUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="text-center">  
                                        <img class="upload-img-view" id="Certificate_viewer" onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"  src="{{asset('storage/shop/'.$shop->reg_cert_image)}}" alt="Product thumbnail"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">GST <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" name="gst_no" placeholder="{{\App\CPU\translate('GST No.')}}" value="{{$shop->gst_no}}" required>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                    <label for="name" class="title-color">GST image  <span class="text-danger">*</span></label>
                                    <div class="custom-file text-left">
                                        <input type="file" name="gst_cert_image" value="{{ $shop->gst_cert_image }}" id="gstcustomFileUpload" class="custom-file-input"
                                            >
                                        <label class="custom-file-label" for="gstcustomFileUpload">{{\App\CPU\translate($shop->gst_cert_image)}} {{\App\CPU\translate('')}}</label>
                                    </div>
                                    </div>
                                    <div class="text-center">
                                        <?php
                                         $gstCertImagePath = 'storage/shop/' . $shop->gst_cert_image;
                                         $gstCertImageExtension = pathinfo($gstCertImagePath, PATHINFO_EXTENSION);
                                        ?>
                                         @if ($gstCertImageExtension != 'pdf')
                                        <img class="upload-img-view" id="gstviewer"
                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/shop/'.$shop->gst_cert_image)}}" alt="Product thumbnail"/>
                                        @else
                                        <iframe src="{{asset('storage/shop/'.$shop->gst_cert_image)}}" style="width:600px; height:500px;" frameborder="0"></iframe>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="title-color">PAN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-user" name="pan" placeholder="{{\App\CPU\translate('PAN No.')}}" value="{{$shop->pan}}" required>
                                </div>

<!--  accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"-->

                                <div class="form-group">
                                    <div class="form-group">
                                    <label for="name" class="title-color">PAN image <span class="text-danger">*</span></label>
                                    <div class="custom-file text-left">
                                        <input type="file" name="pan_image" id="pan_customFileUpload" class="custom-file-input"
                                            >
                                        <label class="custom-file-label" for="pan_customFileUpload">{{\App\CPU\translate($shop->pan_image)}} {{\App\CPU\translate('')}}</label>
                                    </div>
                                    </div>
                                    <div class="text-center">
                                        <?php
                                        $panImagePath = 'storage/shop/' . $shop->pan_image;
                                         $panImageExtension = pathinfo($panImagePath, PATHINFO_EXTENSION);
                                        ?>
                                        @if ($panImageExtension != 'pdf')
                                        <img class="upload-img-view" id="pan_viewer"
                                        onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/shop/'.$shop->pan_image)}}" alt="Product thumbnail"/>
                                        @else
                                         <iframe src="{{asset('storage/shop/'.$shop->pan_image)}}" style="width:600px; height:500px;" frameborder="0"></iframe>
                                        @endif
                                    </div>
                                </div>





                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="title-color">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('Profile')}} {{\App\CPU\translate('image')}}</label>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image_customFileUpload" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="image_customFileUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <img class="upload-img-view" id="image_viewer"
                                    onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                    src="{{asset('storage/shop/'.$shop->image)}}" alt="Product thumbnail"/>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4 mt-2" style="display:none;">
                                <div class="form-group">
                                    <div class="flex-start">
                                        <label for="name" class="title-color">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('Banner')}} </label>
                                        <div class="mx-1" for="ratio">
                                            <span class="text-info">{{\App\CPU\translate('Ratio')}} : ( 6:1 )</span>
                                        </div>
                                    </div>
                                    <div class="custom-file text-left">
                                        <input type="file" name="banner" id="BannerUpload" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="BannerUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <center>
                                        <img class="upload-img-view upload-img-view__banner" id="viewerBanner"
                                             onerror="this.src='{{asset('assets/back-end/img/400x400/img2.jpg')}}'"
                                             src="{{asset('storage/shop/banner/'.$shop->banner)}}"alt="banner image"/>
                                    </center>
                                </div>
                            </div>

                            @if(theme_root_path() == "theme_aster")
                            <div class="col-md-6 mb-4 mt-2">
                                <div class="form-group">
                                    <div class="flex-start">
                                        <label for="name" class="title-color">{{translate('Upload')}} {{translate('Secondary')}} {{translate('Banner')}} </label>
                                        <div class="mx-1" for="ratio">
                                            <span class="text-info">{{translate('Ratio')}} : ( 6:1 )</span>
                                        </div>
                                    </div>
                                    <div class="custom-file text-left">
                                        <input type="file" name="bottom_banner" id="BottomBannerUpload" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="BottomBannerUpload">{{translate('choose')}} {{translate('file')}}</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <center>
                                        <img class="upload-img-view upload-img-view__banner" id="viewerBottomBanner"
                                             onerror="this.src='{{asset('assets/back-end/img/400x400/img2.jpg')}}'"
                                             src="{{asset('storage/shop/banner/'.$shop->bottom_banner)}}"alt="banner image"/>
                                    </center>
                                </div>
                            </div>
                            @endif

                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-danger" href="{{route('seller.shop.view')}}">{{\App\CPU\translate('Cancel')}}</a>
                            <button type="submit" class="btn btn--primary" id="btn_update">{{\App\CPU\translate('Update')}}</button>
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

                reader.onload = function (e) {
                    $('#gstviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function readCertificateURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#Certificate_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function readPanURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#pan_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function readimagURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image_viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewerBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBottomBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewerBottomBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#gstcustomFileUpload").change(function () {
            readURL(this);
        });
        
        $("#Certificate_customFileUpload").change(function () {
            readCertificateURL(this);
        });
        
        $("#pan_customFileUpload").change(function () {
            readPanURL(this);
        });
        
        $("#image_customFileUpload").change(function () {
            readimagURL(this);
        });
        
        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $("#BannerUpload").change(function () {
            readBannerURL(this);
        });
        $("#BottomBannerUpload").change(function () {
            readBottomBannerURL(this);
        });
   </script>

@endpush
