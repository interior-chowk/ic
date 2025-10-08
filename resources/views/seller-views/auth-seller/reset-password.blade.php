@extends('layouts.back-end.common_seller_1')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<style>
.col-lg-4.col-md-12.ms-auto.aos-init.aos-animate {
    margin-left: auto;
}
</style>

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/custom.css') }}">
  <link rel="stylesheet" type="text/css" href="{{asset('asset/css/seller-custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/responsive.css') }}">

<section class="c-seller-login-w c-seller-forget-password">
    <div class="c-seller-login-in">
        <div class="container">
            <div class="row align-items-end">

                <div class="col-lg-4 col-md-12 me-auto reset-password" data-aos="zoom-in" data-aos-duration="500" data-aos-delay="500">

                    <div class="c-password-right">
                        <img src="{{ asset('asset/img/password-top.png') }}" alt="">
                        <div class="c-password-right-in">
                            <div class="c-password-right-img">
                                <img src="{{ asset('asset/img/lock.png') }}" alt="">
                            </div>

                            <form method="POST" action="{{route('seller.auth.reset-passwords-update')}}">
                                @csrf

                                <div class="form-group d-none">
                                    <input type="text" name="reset_token" value="{{$token}}" required>
                                </div>
                                <div class="form-group">
                                    <label>Enter new password</label>
                                    <input type="text" name="password" placeholder="Enter new password" id="exampleInputPassword"  class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Repeat new password</label>
                                    <input type="text" name="confirm_password" placeholder="Enter new password" id="exampleRepeatPassword" class="form-control">
                                     <div id="password-error" style="color: red; display: none;">Passwords do not match!</div>
                                </div>

                                <input type="submit" name="" value="save" class="c-btn-2">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12" data-aos="zoom-in" data-aos-duration="500">
                    <div class="c-seller-login-left c-password-left c-reste-password">
                        <h2> Reset your <span> password?</span></h2>
                        <img src="{{ asset('asset/img/reset-password.png') }}" alt="">
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