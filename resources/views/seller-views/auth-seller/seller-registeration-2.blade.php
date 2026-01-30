{{-- @extends('layouts.back-end.common_seller') --}}
@extends('layouts.back-end.common_seller_1')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/custom.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/seller-custom.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/responsive.css') }}">

<style>
    .input-with-icon {
        position: relative;
    }

    .input-with-icon input {
        padding-right: 40px;
    }

    .input-with-icon .icon-status {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        display: none;
    }

    .icon-status img {
        width: 20px;
        height: 20px;
    }
</style>

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @php
        $shop = \App\Model\Shop::where('seller_id', $id)->first();
    @endphp

    <section class="c-seller-login-w c-seller-registraion">
        <div class="c-seller-login-in">
            <div class="container">
                <div class="row align-items-end">

                    <div class="col-lg-5 col-md-12" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-login-left c-password-left c-seller-registraion-heading">
                            <h2> Seller <span> Registration</span></h2>
                            <img src="{{ asset('public/asset/img/seller-register.png') }}" alt="">
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-12 seller-register-part-2" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-registraion-step">
                            <ul>
                                <li class="done-form">
                                    <button type="button">
                                        <img src="{{ asset('public/asset/img/step-1.png') }}">
                                        <h3>Personal info.</h3>
                                    </button>
                                </li>
                                <li class="active">
                                    <button type="button">
                                        <img src="{{ asset('public/asset/img/step-2.png') }}">
                                        <h3>Business info </h3>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="c-seller-registraion-step-form">
                            <form class="__shop-apply" action="{{ route('seller.auth.seller-registeration-2') }}"
                                id="form-id" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="seller_id" value="{{ $id }}">

                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input id="gst" type="text" name="gst"
                                                value="{{ $shop?->gst_no ?? old('gst') }}" class="form-control"
                                                placeholder="GST Number" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input id="shop_name" type="text" name="shop_name"
                                                value="{{ $shop?->name ?? old('shop_name') }}" class="form-control"
                                                placeholder="Business name" readonly required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input id="shop_address" type="text" name="shop_address"
                                                value="{{ $shop?->address ?? old('shop_address') }}" class="form-control"
                                                placeholder="Registered Address" readonly required>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-12">
                                        <div class="form-group">
                                            <input id="pan" type="text" name="pan"
                                                value="{{ $shop?->pan ?? old('pan') }}" class="form-control"
                                                placeholder="PAN Number" required>
                                        </div>
                                    </div>

                                    <input id="pan_name" type="hidden" name="pan_name" value="{{ old('pan_name') }}">
                                    <div class="col-md-6">
                                        <small id="pan_holder_name_display" class="form-text text-muted"></small>
                                    </div> --}}

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <select id="bank_select" name="bank_name" class="form-control">
                                                <option value="{{ $shop?->bank_name ?? old('bank_name') }}">Select Bank
                                                </option>
                                                <option value="Axis Bank Ltd.">Axis Bank Ltd.</option>
                                                <option>Bandhan Bank Ltd.</option>
                                                <option>CSB Bank Limited</option>
                                                <option>City Union Bank Ltd.</option>
                                                <option>DCB Bank Ltd.</option>
                                                <option>Dhanlaxmi Bank Ltd.</option>
                                                <option>Federal Bank Ltd.</option>
                                                <option>HDFC Bank Ltd</option>
                                                <option>ICICI Bank Ltd.</option>
                                                <option>IndusInd Bank Ltd</option>
                                                <option>IDFC FIRST Bank Limited</option>
                                                <option>Jammu & Kashmir Bank Ltd.</option>
                                                <option>Karnataka Bank Ltd.</option>
                                                <option>Karur Vysya Bank Ltd.</option>
                                                <option>Kotak Mahindra Bank Ltd</option>
                                                <option>Nainital bank Ltd.</option>
                                                <option>RBL Bank Ltd.</option>
                                                <option>South Indian Bank Ltd.</option>
                                                <option>Tamilnad Mercantile Bank Ltd.</option>
                                                <option>YES Bank Ltd.</option>
                                                <option>IDBI Bank Limited</option>
                                                <option>Coastal Local Area Bank Ltd</option>
                                                <option>Krishna Bhima Samruddhi LAB Ltd</option>
                                                <option>Au Small Finance Bank Ltd.</option>
                                                <option>Capital Small Finance Bank Ltd</option>
                                                <option>Equitas Small Finance Bank Ltd</option>
                                                <option>ESAF Small Finance Bank Ltd.</option>
                                                <option>Suryoday Small Finance Bank Ltd.</option>
                                                <option>Ujjivan Small Finance Bank Ltd.</option>
                                                <option>Utkarsh Small Finance Bank Ltd.</option>
                                                <option>slice Small Finance Bank Ltd.</option>
                                                <option>Jana Small Finance Bank Ltd</option>
                                                <option>Shivalik Small Finance Bank Ltd</option>
                                                <option>Unity Small Finance Bank Ltd</option>
                                                <option>Airtel Payments Bank Ltd</option>
                                                <option>India Post Payments Bank Ltd</option>
                                                <option>FINO Payments Bank Ltd</option>
                                                <option>Paytm Payments Bank Ltd</option>
                                                <option>Jio Payments Bank Ltd</option>
                                                <option>NSDL Payments Bank Limited</option>
                                                <option>Bank of Baroda</option>
                                                <option>Bank of India</option>
                                                <option>Bank of Maharashtra</option>
                                                <option>Canara Bank</option>
                                                <option>Central Bank of India</option>
                                                <option>DMK JAOLI BANK</option>
                                                <option>Indian Bank</option>
                                                <option>Indian Overseas Bank</option>
                                                <option>Punjab & Sind Bank</option>
                                                <option>Punjab National Bank</option>
                                                <option>State Bank of India</option>
                                                <option>UCO Bank</option>
                                                <option>Union Bank of India</option>
                                                <option>National Bank for Agriculture and Rural Development</option>
                                                <option>Export-Import Bank of India</option>
                                                <option>National Housing Bank</option>
                                                <option>Small Industries Development Bank of India</option>
                                                <option>Assam Gramin Vikash Bank</option>
                                                <option>Andhra Pradesh Grameena Vikas Bank</option>
                                                <option>Andhra Pragathi Grameena Bank</option>
                                                <option>Arunachal Pradesh Rural Bank</option>
                                                <option>Aryavart Bank</option>
                                                <option>Bangiya Gramin Vikash Bank</option>
                                                <option>Baroda Gujarat Gramin Bank</option>
                                                <option>Baroda Rajasthan Kshetriya Gramin Bank</option>
                                                <option>Baroda UP Bank</option>
                                                <option>Chaitanya Godavari GB</option>
                                                <option>Chhattisgarh Rajya Gramin Bank</option>
                                                <option>Dakshin Bihar Gramin Bank</option>
                                                <option>Ellaquai Dehati Bank</option>
                                                <option>Himachal Pradesh Gramin Bank</option>
                                                <option>J&amp;K Grameen Bank</option>
                                                <option>Jharkhand Rajya Gramin Bank</option>
                                                <option>Karnataka Gramin Bank</option>
                                                <option>Karnataka Vikas Gramin Bank</option>
                                                <option>Kerala Gramin Bank</option>
                                                <option>Madhya Pradesh Gramin Bank</option>
                                                <option>Madhyanchal Gramin Bank</option>
                                                <option>Maharashtra Gramin Bank</option>
                                                <option>Manipur Rural Bank</option>
                                                <option>Meghalaya Rural Bank</option>
                                                <option>Mizoram Rural Bank</option>
                                                <option>Nagaland Rural Bank</option>
                                                <option>Odisha Gramya Bank</option>
                                                <option>Paschim Banga Gramin Bank</option>
                                                <option>Prathama U.P. Gramin Bank</option>
                                                <option>Puduvai Bharathiar Grama Bank</option>
                                                <option>Punjab Gramin Bank</option>
                                                <option>Rajasthan Marudhara Gramin Bank</option>
                                                <option>Saptagiri Grameena Bank</option>
                                                <option>Sarva Haryana Gramin Bank</option>
                                                <option>Saurashtra Gramin Bank</option>
                                                <option>Tamil Nadu Grama Bank</option>
                                                <option>Telangana Grameena Bank</option>
                                                <option>Tripura Gramin Bank</option>
                                                <option>Uttar Bihar Gramin Bank</option>
                                                <option>Utkal Grameen Bank</option>
                                                <option>Uttarbanga Kshetriya Gramin Bank</option>
                                                <option>Vidharbha Konkan Gramin Bank</option>
                                                <option>Uttarakhand Gramin Bank</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input id="acc_no" type="text" class="form-control" name="acc_no"
                                                value="{{ $shop?->acc_no ?? old('acc_no') }}" placeholder="Account No."
                                                required>
                                        </div>
                                    </div>

                                    {{-- IFSC --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input id="ifsc" type="text" class="form-control" name="ifsc"
                                                value="{{ $shop?->ifsc ?? old('ifsc') }}" placeholder="IFSC" required>
                                            <input type="hidden" id="name_at_bank" name="name_at_bank">
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <small id="bank_status" class="form-text text-muted"></small>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="c-btn-group text-center">
                                            <a href="{{ route('seller.auth.seller-registeration', ['id' => $id]) }}"
                                                class="c-btn-2">Back</a>
                                            <button type="button" class="c-btn-2 c-btn-border"
                                                onclick="preview_data(event)">Preview</button>
                                            <button type="submit" id="apply"
                                                class="c-btn-2 c-orange-btn">Next</button>
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
    @php
        $data = \App\Model\Seller::where('id', $id)->first();
        $shop = \App\Model\Shop::where('seller_id', $id)->first();
    @endphp

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                                <h6>First Name</h6>
                                <p id="modal_first_name">{{ optional($data)->f_name ?? '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Last Name</h6>
                                <p id="modal_last_name">{{ optional($data)->l_name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Email</h6>
                                <p id="modal_email">{{ optional($data)->email ?? '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Phone Number</h6>
                                <p id="modal_phone">{{ optional($data)->phone ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="business-info">
                        <h5 class="mb-3"><u>Business Info</u></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Company Name</h6>
                                <p id="modal_company_name">{{ optional($shop)->name ?? '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Address</h6>
                                <p id="modal_company_address">{{ optional($shop)->address ?? '' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Bank Account Number</h6>
                                <p id="modal_acc_no">{{ optional($shop)->acc_no ?? '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Bank Name</h6>
                                <p id="modal_bank_name">{{ optional($shop)->bank_name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>IFSC Code</h6>
                                <p id="modal_ifsc">{{ optional($shop)->ifsc ?? '' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>GST Number</h6>
                                <p id="modal_gst_filename">{{ optional($shop)->gst ?? '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>PAN Number</h6>
                                <p id="modal_pan_filename">{{ optional($shop)->pan ?? '' }}</p>
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
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('public/assets/back-end/js/vendor.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- FIXED: Safe preview_data -->
<script>
    function preview_data(e) {
        if (e && e.preventDefault) e.preventDefault();

        const safeSet = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '';
        };

        const gstInput = document.getElementById('gst');
        const accInput = document.getElementById('acc_no');
        const compAddr = document.getElementById('shop_address');
        const compName = document.getElementById('shop_name');
        const bankSelect = document.getElementById('bank_select');
        const ifscInput = document.getElementById('ifsc');

        const gstVal = gstInput ? gstInput.value.trim() : '';

        // ✅ Extract PAN from GST
        let panVal = '';
        if (gstVal.length === 15) {
            panVal = gstVal.substr(2, 10); // PAN extraction
        }

        safeSet('modal_gst_filename', gstVal);
        safeSet('modal_pan_filename', panVal); // ✅ PAN from GST
        safeSet('modal_acc_no', accInput ? accInput.value.trim() : '');
        safeSet('modal_company_address', compAddr ? compAddr.value.trim() : '');
        safeSet('modal_company_name', compName ? compName.value.trim() : '');
        safeSet(
            'modal_bank_name',
            bankSelect && bankSelect.selectedIndex >= 0 ?
            bankSelect.options[bankSelect.selectedIndex].text :
            ''
        );
        safeSet('modal_ifsc', ifscInput ? ifscInput.value.trim() : '');

        $("#previewModal").modal("show");
    }


    function close_preview() {
        $("#previewModal").modal("hide");
    }
</script>

<script>
    $(document).ready(function() {
        $('#bank_select').select2({
            placeholder: "Select a bank",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    $(function() {

        const imageBaseUrl = "{{ url('public/website/assets/images') }}/";

        console.log(imageBaseUrl);

        function setVerifyStatus($input, status) {
            let $icon = $input.closest('.form-group').find('.verified-icon');
            if (!$icon.length) {
                $icon = $('<span class="verified-icon"></span>');
                $input.closest('.form-group').append($icon);
            }

            if (status === 'loading') {
                $icon.html('<img src="https://i.gifer.com/ZZ5H.gif" width="20">').show();
            } else if (status === 'success') {
                $icon.html(`<img src="${imageBaseUrl}right tick.webp" class="tick">`).css('height', '60px')
                    .show();
            } else if (status === 'error') {
                $icon.html(`<img src="${imageBaseUrl}tick.webp" class="tick">`).css('height', '60px').show();
            } else {
                $icon.hide();
            }
        }

        const $gst = $('#gst');
        const $shopName = $('#shop_name');
        const $shopAddress = $('#shop_address');
        let gstCalling = false;

        function verifyGST() {
            if (gstCalling) return;
            const gstVal = $gst.val().trim();
            if (!gstVal) return;

            gstCalling = true;
            setVerifyStatus($gst, 'loading');

            $.ajax({
                url: "{{ route('seller.shop.verifyGst') }}",
                type: 'POST',
                data: {
                    gst: gstVal,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    gstCalling = false;
                    if (!res.success) {
                        alert(res.message || 'GST verification failed');
                        setVerifyStatus($gst, 'error');
                        return;
                    }
                    let d = res.data;
                    if (d.business_name) $shopName.val(d.business_name);
                    if (d.address) $shopAddress.val(d.address);

                    $shopName.prop('readonly', true);
                    $shopAddress.prop('readonly', true);

                    setVerifyStatus($gst, 'success');
                    setVerifyStatus($shopName, 'success');
                    setVerifyStatus($shopAddress, 'success');
                },
                error: function() {
                    gstCalling = false;
                    alert('Something went wrong while verifying GST');
                    setVerifyStatus($gst, 'error');
                }
            });
        }

        $gst.on('blur', verifyGST);


        // Bank verification
        function verifyBank() {
            var bank_name = $('#bank_select').val().trim();
            var acc_no = $('#acc_no').val().trim();
            var ifsc = $('#ifsc').val().trim();

            if (acc_no && ifsc) { // bank_name is optional now
                // $('#bank_status').html('<img src="https://i.gifer.com/ZZ5H.gif" width="25"> Verifying...');
                setVerifyStatus($('#acc_no'), 'loading');
                setVerifyStatus($('#ifsc'), 'loading');
                setVerifyStatus($('#bank_select'), bank_name ? 'loading' : '');

                $.ajax({
                    url: "{{ route('seller.verify.bank') }}",
                    type: "POST",
                    data: {
                        bank_name: bank_name,
                        acc_no: acc_no,
                        ifsc: ifsc,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log('Bankname:', response.data.data.name_at_bank);
                        if (response.data && response.data.data.name_at_bank) {
                            $('#name_at_bank').val(response.data.data.name_at_bank);
                        }
                        if (response.status) {

                            $('#apply').removeAttr('disabled');
                            setVerifyStatus($('#bank_select'), 'success');
                            setVerifyStatus($('#acc_no'), 'success');
                            setVerifyStatus($('#ifsc'), 'success');
                        } else {
                            $('#bank_status').html('❌ ' + response.message);
                            $('#apply').attr('disabled', 'disabled');
                            setVerifyStatus($('#bank_select'), 'error');
                            setVerifyStatus($('#acc_no'), 'error');
                            setVerifyStatus($('#ifsc'), 'error');
                        }
                    },
                    error: function() {
                        $('#bank_status').html('❌ Something went wrong! Try again.');
                        $('#apply').attr('disabled', 'disabled');
                        setVerifyStatus($('#bank_select'), 'error');
                        setVerifyStatus($('#acc_no'), 'error');
                        setVerifyStatus($('#ifsc'), 'error');
                    }
                });
            }
        }

        $('#bank_select, #acc_no, #ifsc').on('blur', verifyBank);

    });
</script>

<style>
    .verified-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        display: none;
    }

    .form-group {
        position: relative;
    }

    img.tick {
        height: 100%;
    }
</style>
