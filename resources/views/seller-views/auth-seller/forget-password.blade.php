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
                <div class="col-lg-5 col-md-12" data-aos="zoom-in" data-aos-duration="500">
                    <div class="c-seller-login-left c-password-left">
                        <h2> Forget your <span> password?</span></h2>
                        <img src="{{ asset('asset/img/password-left.png') }}" alt="">

                    </div>
                </div>
                <div class="col-lg-4 col-md-12 ms-auto forget-password" data-aos="zoom-in" data-aos-duration="500" data-aos-delay="500">

                    <div class="c-password-right">
                        <img src="{{ asset('asset/img/password-top.png') }}" alt="">
                        <div class="c-password-right-in">
                            <div class="c-password-right-img">
                                <img src="{{ asset('asset/img/user-img.png') }}" alt="">
                            </div>
                            <p> Enter your E-mail ID and we’ll send you a link to reset your password.</p>
                            <form class="card-body needs-validation" action="{{route('seller.auth.forget-password-request')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="identity" placeholder="Enter your registered E-mail ID" class="form-control">
                                </div>
                                <button class="c-password-right-btn"></button>
                            </form>
                        </div>
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