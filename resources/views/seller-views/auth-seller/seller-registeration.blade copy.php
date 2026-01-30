@extends('layouts.back-end.common_seller_1')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<style>
.col-lg-4.col-md-12.ms-auto.aos-init.aos-animate {
    margin-left: auto;
}
</style>

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{asset('public/asset/css/seller-custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/asset/css/responsive.css') }}">



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
                <div class="col-lg-7 col-md-12 seller-register-part" data-aos="zoom-in" data-aos-duration="500">
                    <div class="c-seller-registraion-step">
                        <ul>
                            <li class="active">
                                <button type="button">
                                    <img src="{{ asset('public/asset/img/step-1.png') }}">
                                    <h3>Personal info.</h3>
                                </button>
                            </li>
                            <li>
                                <button type="button">
                                    <img src="{{ asset('public/asset/img/step-2.png') }}">
                                    <h3>Business info</h3>
                                </button>
                            </li>
                            <li>
                                <button type="button">
                                    <img src="{{ asset('public/asset/img/step-3.png') }}">
                                    <h3>Upload docs</h3>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="c-seller-registraion-step-form">
                         <form class="__shop-apply" action="{{route('seller.auth.seller-registeration-store')}}" id="form-id" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="f_name" class="form-control" id="exampleFirstName" value="{{old('f_name')}}" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="l_name" class="form-control" id="exampleLastName" placeholder="Last Name " value="{{old('l_name')}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="email" class="form-control" id="exampleInputEmail" placeholder="E-mail address" value="{{old('email')}}">
                                    </div>
                                </div>
                                <input type="hidden" name="phone" id="phone_input">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="c-otp-w">
                                            <div class="input-group">
                                                <div class="form-outline">
                                                    <input type="search" name="phone" class="form-control" id="exampleInputPhone" placeholder="Mobile number" />
                                                    <small class="text-danger" id="show_phone_error" style="display:none">please_fill_phone_number</small>
                                                    <small class="text-danger" id="show_phone_error_verified" style="display:none">please_verified_phone_number</small>
                                                    <small class="text-danger" id="number_exit" style="display:none">Number Already exit !</small>

                                                </div>
                                                <button type="button" class="btn btn-primary get_otp">
                                                    Get OTP
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="otp_input_type" style="display:none">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="otp" placeholder="Enter OTP" id="partitioned">
                                        <small class="text-danger" id="show_otp_error" style="display:none">otp is not matched !</small>
                                    </div>

                                </div>
                                <div class="col-md-6" id="otp_verify_button" style="display:none">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary w-100  Verify_otp">
                                            Verify OTP
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="c-password-show-1">
                                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                        </div>
                                        <input type="password" name="password" class="form-control pass_log_id_new" id="exampleInputPassword" placeholder="Create Password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="c-password-show-2">
                                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                        </div>
                                        <input type="password" name="password_confirm" id="exampleRepeatPassword" class="form-control pass_log_id_new-2" placeholder="Repeat password" required>
                                        <div id="password-error" style="color: red; display: none;">Passwords do not match!</div>
                                    </div>
                                </div>
                                 <div id="password-error-inputs" style="color: red; display: none;"></div>
                                <div class="col-md-12">
                                    <div class="c-btn-group text-center">

                                        <!-- <a href="{{ route('seller.auth.seller-registeration-2') }}" class="c-btn-2 c-orange-btn">Next</a>-->
                                        <button type="submit" class="c-btn-2 c-orange-btn reg-form" disabled>Next</button>
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

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

{!! Toastr::message() !!}

@if ($errors->any())

    <script>
         
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true,
            positionClass: 'toast-top-right' // Set the position here
        });
        @endforeach
        
    </script>
@endif

@endsection

<!-- JS Implementing Plugins -->
<script src="{{asset('public/assets/back-end')}}/js/vendor.min.js"></script>

<script>
    $(document).ready(function() {
        $(".get_otp").click(function() {
             $('#number_exit').hide(); 
            var phone = $("#exampleInputPhone").val();

            if (phone.length == 10) {
                $("#phone_input").val(phone);
                $("#show_phone_error").hide();
                $("#show_phone_error_verified").hide();
               
                var data = {
                    phone: phone,
                    _token: '{!! csrf_token() !!}'
                };

            } else {

                $("#show_phone_error").show();

                return false;
            }

            $.ajax({
                url: "{{route('shop.send_otp')}}", // Replace with your PHP script URL{{route('shop.apply')}}
                type: 'POST', // or 'GET' depending on your PHP script
                data: data,
                success: function(response) {
                    if (response == 1) {
                        $(".get_otp").text("Re-send");
                        $('#otp_input_type').show();
                        $('#otp_verify_button').show();
                    } else if(response == 2){
                        $('#number_exit').show(); 

                    }
                },
                error: function(error) {
                    // Handle errors here
                    console.error('Error:', error);
                }
            });

        });
    });
</script>

<script>
    $(document).ready(function() {
        $(".Verify_otp").click(function() {
            var number = $("#partitioned").val();

            if (number) {
                $("#show_phone_error").hide();
                $("#show_phone_error_verified").hide();
                var data = {
                    number: number,
                    _token: '{!! csrf_token() !!}'
                };

            }

            $.ajax({
                url: "{{route('shop.Verify_otp')}}", // Replace with your PHP script URL{{route('shop.apply')}}
                type: 'POST', // or 'GET' depending on your PHP script
                data: data,
                success: function(response) {
                    if (response == 1) {
                        $('#otp_input_type').hide();
                        $('#otp_verify_button').hide();
                        $(".get_otp").removeClass("btn--primary");
                        $(".get_otp").addClass("btn-success");
                        $(".get_otp").text("Verified");
                        $('.get_otp').attr('disabled', 'disabled');
                        $('#exampleInputPhone').attr('disabled', 'disabled');
                        
                        var otp_verified = $(".get_otp").text();
                        
                         if(otp_verified == "Verified"){
                            $('.reg-form').removeAttr('disabled');
                            }
                    }
                    if (response == 0) {
                        $("#show_otp_error").show();
                    }
                },
                error: function(error) {
                    // Handle errors here
                    console.error('Error:', error);
                }
            });

        });
    });
</script>