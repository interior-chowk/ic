<style>
    input::after {
        border: none !important;
    }
</style>
<footer class="c-footer-w">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="c-footer-nav">
                    <h3>Useful links</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li class="active"><a href="{{ url('/') }}">Home </a></li>
                                <li><a href="{{ url('/') }}#about-section">About Us </a></li>
                                <li><a href="{{ route('shopping') }}">Shopping </a></li>
                                <li><a href="{{ route('service') }}">Service</a></li>
                                <li><a href="{{ route('solution') }}">Solution </a></li>
                                <li><a href="{{ route('seller-chowk') }}">Seller’s Chowk </a></li>
                                <li><a href="{{ route('service-chowk') }}">Service Chowk </a></li>
                                <li><a href="{{ route('seller.auth.seller-login') }}">Seller Login</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $Business_settings = \App\Model\BusinessSetting::all();
                            
                            ?>
                            <ul>
                                @foreach ($Business_settings as $Business_setting)
                                    @if ($Business_setting->type == 'e_wallet_Policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">E-wallet
                                                policy</a></li>
                                    @elseif($Business_setting->type == 'seller_terms_condition')
                                        <!--<li class="active"><a href="{{ route('policy', ['type' => $Business_setting->type]) }}">Seller Terms & Conditions</a></li>-->
                                    @elseif($Business_setting->type == 'terms_condition')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Terms
                                                & Conditions</a></li>
                                    @elseif($Business_setting->type == 'privacy_policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Privacy
                                                Policy</a></li>
                                    @elseif($Business_setting->type == 'shipping_policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Shipping
                                                Policy</a></li>
                                    @elseif($Business_setting->type == 'refund-policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Return
                                                & refund policy</a></li>
                                    @elseif($Business_setting->type == 'instant_delivery_policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Instant
                                                Delivery Policy</a></li>
                                    @elseif($Business_setting->type == 'secure_payment_policy')
                                        <li class="active"><a
                                                href="{{ route('policy', ['type' => $Business_setting->type]) }}">Secure
                                                Payment Policy </a></li>
                                    @endif
                                @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 ms-auto me-auto">
                <div class="c-footer-form">
                    <h3>Any <span>QUERY ?</span></h3>
                    <form action="{{ route('callback-mail') }}" method="post">
                        @csrf
                        <input type="hidden" name="status_site" value="0">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" placeholder="Mobile no." class="form-control" required>
                        </div>
                        <div class="form-group custom-select">
                            <select class="form-control " name="interested" required>
                                <option> I am:- </option>
                                <option> Customer</option>
                                <option> Seller</option>
                                <option> Architect</option>
                                <option> Interior designer</option>
                                <option> Contractor</option>
                                <option> Worker</option>
                            </select>
                        </div>
                        <input type="submit" name="" class="btn" value="REQUEST FOR CALLBACK">
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="c-footer-info">
                    <div class="c-footer-info-in">
                        <h3>Contact US</h3>
                        <ul class="c-footer-info-con">
                            <li>
                                <span>
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                </span>

                                @php
                                    use Illuminate\Http\Request;
                                @endphp
                                @if (request()->is('seller/auth/login') ||
                                        request()->is('seller/auth/seller-login') ||
                                        request()->is('seller/auth/seller-registeration') ||
                                        request()->is('seller/auth/seller-registeration-1') ||
                                        request()->is('seller/auth/seller-registeration-2') ||
                                        request()->is('seller/auth/seller-registeration-3') ||
                                        request()->is('seller/auth/forget-password') ||
                                        request()->is('seller/auth/reset-passwords') ||
                                        request()->is('service-chowk') ||
                                        request()->is('seller-chowk'))
                                    <a href="mailto:support@interiorchowk.com">support@interiorchowk.com</a>
                                @else
                                    <a
                                        href="mailto:customersupport@interiorchowk.com">customersupport@interiorchowk.com</a>
                                @endif
                            </li>
                            <li>
                                <span>
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                </span>
                                <a href="tel:919953680690">9953 680 690 </a>
                            </li>

                        </ul>
                        <h3 class="mt-2">Follow US</h3>
                        <?php
                        $social_media = \App\Model\SocialMedia::all();
                        
                        ?>

                        <ul class="d-flex">
                            @foreach ($social_media as $social_media_link)
                                @if ($social_media_link->name == 'instagram')
                                    <li>
                                        <a href="{{ $social_media_link->link }}" target="_blank">
                                            <img src="{{ asset('public/asset/img/social-icon-1.png') }}"
                                                alt="">
                                        </a>
                                    </li>
                                @endif
                                @if ($social_media_link->name == 'facebook')
                                    <li>
                                        <a href="{{ $social_media_link->link }}" target="_blank">
                                            <img src="{{ asset('public/asset/img/social-icon-2.png') }}"
                                                alt="">
                                        </a>
                                    </li>
                                @endif
                                @if ($social_media_link->name == 'linkedin')
                                    <li>
                                        <a href="{{ $social_media_link->link }}" target="_blank">
                                            <img src="{{ asset('public/asset/img/social-icon-3.png') }}"
                                                alt="">
                                        </a>
                                    </li>
                                @endif
                                @if ($social_media_link->name == 'youtube')
                                    <li>
                                        <a href="{{ $social_media_link->link }}" target="_blank">
                                            <img src="{{ asset('public/asset/img/social-icon-4.png') }}"
                                                alt="">
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('public/asset/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/asset/js/slick.js') }}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('public/asset/js/custom.js') }}"></script>
<script src="{{ asset('public/asset/js/seller-custom.js') }}"></script>
<script src="{{ asset('public/asset/js/toastr.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
    use Illuminate\Support\Facades\Session;
@endphp

@if (session()->has('for_callback'))
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Your Request has sent",
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    @php
        session()->forget('for_callback');
    @endphp
@endif

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>
