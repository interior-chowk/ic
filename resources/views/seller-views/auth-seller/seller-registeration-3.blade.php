{{-- @extends('layouts.back-end.common_seller') --}}

@extends('layouts.back-end.common_seller_1')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <section class="c-seller-login-w c-seller-registraion">
        <div class="c-seller-login-in">
            <div class="container">
                <div class="row align-items-end">

                    <div class="col-lg-5 col-md-12 " data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-login-left c-password-left c-seller-registraion-heading">
                            <h2> Seller <span> Registration</span></h2>
                            <img src="{{ asset('public/asset/img/seller-register.png') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 seller-register-part-3" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-registraion-step">
                            <ul>
                                <li class="done-form">
                                    <button type="button">
                                        <img src="{{ asset('public/asset/img/step-1.png') }}">
                                        <h3>Personal info.</h3>
                                    </button>
                                </li>
                                <li class="done-form">
                                    <button type="button">
                                        <img src="{{ asset('public/asset/img/step-2.png') }}">
                                        <h3>Business info</h3>
                                    </button>
                                </li>
                                <li class="active">
                                    <button type="button">
                                        <img src="{{ asset('public/asset/img/step-3.png') }}">
                                        <h3>Upload docs</h3>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="c-seller-registraion-step-form">
                            <form class="__shop-apply" action="{{ route('seller.auth.seller-registeration-3') }}"
                                id="form-id" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="seller_id" value="{{ $id }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group c-file-upload-control">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12">
                                                    <label class="form-label c-bulet-label" for="GSTIN">GSTIN
                                                        Certificate</label>
                                                </div>
                                                <div class="col-lg-7 col-md-12">
                                                    <div class="information-tag">
                                                        <span class="ml-2" data-toggle="tooltip" data-placement="top"
                                                            title="{{ \App\CPU\translate('The Maximum file size for uploads is 5 MB. Do not upload files that exceed this size.') }}">
                                                            <img style="height: 16px; width: fit-content;" class="info-img"
                                                                src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="img">
                                                        </span>
                                                    </div>
                                                    <input type="file" class="form-control" name="gst_cert_image"
                                                        id="gst_img" required>
                                                    <small id="gst_filename" class="form-text text-muted"></small>
                                                </div>
                                            </div>

                                            <script>
                                                gst_img.onchange = evt => {
                                                    const [file] = gst_img.files;
                                                    if (file) {
                                                        const maxSizeMB = 5;
                                                        const maxSizeBytes = maxSizeMB * 1024 * 1024;

                                                        if (file.size > maxSizeBytes) {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'File too large',
                                                                text: `The maximum file size allowed is ${maxSizeMB} MB.`,
                                                                confirmButtonText: 'OK'
                                                            }).then(() => {
                                                                // Reset the file input
                                                                gst_img.value = '';
                                                                document.getElementById('gst_filename').textContent = '';
                                                                gstin_certificate_id.src = ''; // Optional: Remove the image preview if necessary
                                                            });
                                                        } else {
                                                            gstin_certificate_id.src = URL.createObjectURL(file);
                                                            document.getElementById('gst_filename').textContent = file.name;
                                                        }
                                                    }
                                                }
                                            </script>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group c-file-upload-control">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12">
                                                    <label class="form-label c-bulet-label" for="PAN">PAN </label>
                                                </div>
                                                <div class="col-lg-7 col-md-12">
                                                    <div class="information-tag">
                                                        <span class="ml-2" data-toggle="tooltip" data-placement="top"
                                                            title="{{ \App\CPU\translate('The Maximum file size for uploads is 5 MB. Do not upload files that exceed this size.') }}">
                                                            <img style="height: 16px; width: fit-content;" class="info-img"
                                                                src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="img">
                                                        </span>
                                                    </div>
                                                    <input type="file" class="form-control" name="pan_image"
                                                        id="PAN_file" required>
                                                    <small id="pan_filename" class="form-text text-muted"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            PAN_file.onchange = evt => {
                                                const [file] = PAN_file.files;
                                                if (file) {
                                                    const maxSizeMB = 5;
                                                    const maxSizeBytes = maxSizeMB * 1024 * 1024;

                                                    if (file.size > maxSizeBytes) {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'File too large',
                                                            text: `The maximum file size allowed is ${maxSizeMB} MB.`,
                                                            confirmButtonText: 'OK'
                                                        }).then(() => {
                                                            // Reset the file input
                                                            PAN_file.value = '';
                                                            document.getElementById('pan_filename').textContent = '';
                                                            pan_id.src = ''; // Optional: Remove the image preview if necessary
                                                        });
                                                    } else {
                                                        pan_id.src = URL.createObjectURL(file);
                                                        document.getElementById('pan_filename').textContent = file.name;
                                                    }
                                                }
                                            }
                                        </script>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group c-file-upload-control">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12">
                                                    <label class="form-label c-bulet-label" for="Cheque">Copy of Cheque
                                                    </label>
                                                </div>
                                                <div class="col-lg-7 col-md-12">
                                                    <div class="information-tag">
                                                        <span class="ml-2" data-toggle="tooltip" data-placement="top"
                                                            title="{{ \App\CPU\translate('The Maximum file size for uploads is 5 MB. Do not upload files that exceed this size.') }}">
                                                            <img style="height: 16px; width: fit-content;" class="info-img"
                                                                src="{{ asset('/public/assets/back-end/img/info-circle.svg') }}"
                                                                alt="img">
                                                        </span>
                                                    </div>
                                                    <input type="file" class="form-control" name="cheque"
                                                        id="Cheque_file" required>
                                                    <small id="cheque_filename" class="form-text text-muted"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            Cheque_file.onchange = evt => {
                                                const [file] = Cheque_file.files;
                                                if (file) {
                                                    const maxSizeMB = 5;
                                                    const maxSizeBytes = maxSizeMB * 1024 * 1024;

                                                    if (file.size > maxSizeBytes) {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'File too large',
                                                            text: `The maximum file size allowed is ${maxSizeMB} MB.`,
                                                            confirmButtonText: 'OK'
                                                        }).then(() => {
                                                            // Reset the file input
                                                            Cheque_file.value = '';
                                                            document.getElementById('cheque_filename').textContent = '';
                                                            cheque_image_id.src = ''; // Optional: Remove the image preview if necessary
                                                        });
                                                    } else {
                                                        cheque_image_id.src = URL.createObjectURL(file);
                                                        document.getElementById('cheque_filename').textContent = file.name;
                                                    }
                                                }
                                            }
                                        </script>
                                    </div>

                                    <style>
                                        .error {
                                            border: 1px solid red;
                                            /* Apply red border when checkbox is not checked */
                                        }
                                    </style>
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input error" type="checkbox" value=""
                                                name="remember" id="inputCheckd">
                                            <label class="form-check-label" for="inputCheckd">
                                                <?php
                                                $Business_settings = \App\Model\BusinessSetting::all();
                                                
                                                ?>
                                                @foreach ($Business_settings as $Business_setting)
                                                    @if ($Business_setting->type == 'seller_terms_condition')
                                                        <a href="{{ route('policy', ['type' => $Business_setting->type]) }}"
                                                            target="_blank">
                                                            I have read and agreed with the terms and conditions.
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="c-btn-group text-center">
                                            <a href="{{ route('seller.auth.seller-registeration-2', ['id' => $id]) }}"
                                                class="c-btn-2 ">Back</a>
                                            <a class="c-btn-2 c-btn-border cursor-pointer" style="cursor:pointer"
                                                onclick="preview_data()">Preview</a>
                                            <button type="submit" class="c-btn-2 c-orange-btn" id="apply"
                                                disabled>Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php($data = App\Model\Seller::join('shops', 'sellers.id', '=', 'shops.seller_id')->where('sellers.id', '=', $id)->first())

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Added 'modal-lg' class for wider modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" onclick="close_preview()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="personal-info">

                        <h5 class="mb-3"><u>Personal Info</u></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>First Name</h6>
                                    <p>{{ $data->f_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Last Name</h6>
                                    <p>{{ $data->l_name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Email</h6>
                                    <p>{{ $data->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Phone Number</h6>
                                    <p>{{ $data->phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="business-info">
                        <h5 class="mb-3"><u>Business Info</u></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Company Name</h6>
                                    <p>{{ $data->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Address</h6>
                                    <p>{{ $data->address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>State</h6>
                                    <p>{{ $data->state }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>City</h6>
                                    <p>{{ $data->city }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>Pincode</h6>
                                    <p>{{ $data->pincode }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Bank Account Number</h6>
                                    <p>{{ $data->acc_no }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Bank Name</h6>
                                    <p>{{ $data->bank_name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>Branch Name</h6>
                                    <p>{{ $data->bank_branch }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h6>IFSC Code</h6>
                                    <p>{{ $data->ifsc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="upload-docs">
                        <h5 class="mb-3"><u>Upload Docs</u></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>GSTIN Certificate</h6>
                                    <p class="mt-1"><img src="#" id="gstin_certificate_id"
                                            style="height: 100px; width: 100px;">
                                        <br>
                                        <small id="modal_gst_filename" class="form-text text-muted"></small>
                                    <p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>PAN</h6>
                                    <p class="mt-1"><img id="pan_id" style="height: 100px; width: 100px;">
                                        <br>
                                        <small id="modal_pan_filename" class="form-text text-muted"></small>
                                    <p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6>Copy of Cheque Image</h6>
                                    <p class="mt-1"><img id="cheque_image_id" style="height: 100px; width: 100px;">
                                        <br>
                                        <small id="modal_cheque_filename" class="form-text text-muted"></small>
                                    <p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="close_preview()">Close</button>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/assets/back-end') }}/js/vendor.min.js"></script>
    <script>
        $(document).on('ready', function() {
            $('#inputCheckd').change(function() {

                if ($(this).is(':checked')) {
                    $('#inputCheckd').removeClass('error');
                    $('#apply').removeAttr('disabled');
                } else {
                    $('#inputCheckd').addClass('error');
                    $('#apply').attr('disabled', 'disabled');
                }

            });

        });
    </script>
@endsection

<script>
    function preview_data(e) {

        document.getElementById('modal_gst_filename').textContent = document.getElementById('gst_filename').textContent;
        document.getElementById('modal_pan_filename').textContent = document.getElementById('pan_filename').textContent;
        document.getElementById('modal_cheque_filename').textContent = document.getElementById('cheque_filename')
            .textContent;

        $("#previewModal").modal("show");
        e.preventDefault(); // Correctly preventing default behavior
    }

    function close_preview() {
        $("#previewModal").modal("hide");

    }
</script>
