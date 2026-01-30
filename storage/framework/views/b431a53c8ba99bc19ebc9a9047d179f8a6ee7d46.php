


<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/custom.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/seller-custom.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/responsive.css')); ?>">

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

    <section class="c-seller-login-w c-seller-registraion">
        <div class="c-seller-login-in">
            <div class="container">
                <div class="row align-items-end">

                    <div class="col-lg-5 col-md-12" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-login-left c-password-left c-seller-registraion-heading">
                            <h2> Seller <span> Registration</span></h2>
                            <img src="<?php echo e(asset('public/asset/img/seller-register.png')); ?>" alt="">
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-12 seller-register-part" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-registraion-step">
                            <ul>
                                <li class="active">
                                    <button type="button">
                                        <img src="<?php echo e(asset('public/asset/img/step-1.png')); ?>">
                                        <h3>Personal info.</h3>
                                    </button>
                                </li>
                                <li>
                                    <button type="button">
                                        <img src="<?php echo e(asset('public/asset/img/step-2.png')); ?>">
                                        <h3>Business info</h3>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="c-seller-registraion-step-form">
                            <form class="__shop-apply" action="<?php echo e(route('seller.auth.seller-registeration-store')); ?>"
                                id="form-id" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <input type="text" name="f_name" id="f_name" class="form-control"
                                                value="<?php echo e(old('f_name')); ?>" placeholder="First Name"
                                                oninput="this.value = this.value.replace(/\s/g, '');">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <input type="text" name="l_name" class="form-control"
                                                value="<?php echo e(old('l_name')); ?>" placeholder="Last Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <input type="text" name="email" class="form-control"
                                                value="<?php echo e(old('email')); ?>" placeholder="E-mail address">
                                            <small class="text-danger error-email" style="display:none"></small>
                                        </div>
                                    </div>

                                    <input type="hidden" name="phone" id="phone_input">

                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <input type="text" name="phone_display" class="form-control"
                                                id="exampleInputPhone" placeholder="Mobile number">
                                            <span class="icon-status" id="phone-icon"></span>
                                            <small class="text-danger error-phone" id="phone-error"
                                                style="display:none"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="otp_input_type" style="display:none">
                                        <div class="form-group input-with-icon">
                                            <input type="text" class="form-control" name="otp"
                                                placeholder="Enter OTP" id="partitioned">
                                            <span class="icon-status" id="otp-icon"></span>
                                            <small class="text-danger" id="otp-error" style="display:none"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <div class="c-password-show-1">
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            </div>
                                            <input type="password" name="password" class="form-control pass_log_id_new"
                                                placeholder="Create Password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group input-with-icon">
                                            <div class="c-password-show-2">
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            </div>
                                            <input type="password" name="password_confirm"
                                                class="form-control pass_log_id_new-2" placeholder="Repeat password"
                                                required>
                                            <div id="password-error" style="color: red; display: none;">Passwords do not
                                                match!</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="c-btn-group text-center">
                                            <button type="submit" class="c-btn-2 c-orange-btn reg-form"
                                                disabled>Next</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        var imageBaseUrl = "<?php echo e(url('public/website/assets/images')); ?>/";

        function setInputStatus($input, type, message = '') {
            let $icon = $input.siblings('.icon-status');
            let $error = $input.siblings('small.text-danger');

            if (type === 'loading') {
                $icon.html('<img src="https://i.gifer.com/ZZ5H.gif">').show();
                $error.hide();
            } else if (type === 'success') {
                $icon.html(`<img src="${imageBaseUrl}right tick.webp">`).show();
                $error.hide();
            } else if (type === 'error') {
                $icon.html(`<img src="${imageBaseUrl}tick.webp">`).show();
                $error.text(message).show();
            } else {
                $icon.hide();
                $error.hide();
            }
        }

        $(document).ready(function() {
            $("#exampleInputPhone").on('input', function() {
                var phone = $(this).val();
                $("#phone_input").val(phone); // sync hidden input

                if (phone.length === 10) {
                    setInputStatus($(this), 'loading');
                    $('#otp_input_type').hide();
                    $('.reg-form').attr('disabled', true);

                    $.ajax({
                        url: "<?php echo e(route('shop.send_otp')); ?>",
                        type: 'POST',
                        data: {
                            phone: phone,
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            console.log('OTP send response:', response);

                            if (response == 1) { // OTP sent successfully
                                setInputStatus($("#exampleInputPhone"), 'success');
                                $('#otp_input_type').css('display', 'block');
                                $('#partitioned').val('').removeAttr('disabled');
                                $('.reg-form').attr('disabled', true);
                            } else if (response == 2) { // Number exists
                                setInputStatus($("#exampleInputPhone"), 'error',
                                    'Number already exists!');
                                $('#otp_input_type').hide();
                                $('.reg-form').attr('disabled', true);
                            } else { // Any other error
                                setInputStatus($("#exampleInputPhone"), 'error',
                                    'Something went wrong!');
                                $('#otp_input_type').hide();
                                $('.reg-form').attr('disabled', true);
                            }
                        },
                        error: function() {
                            setInputStatus($("#exampleInputPhone"), 'error',
                                'Something went wrong!');
                            $('#otp_input_type').hide();
                            $('.reg-form').attr('disabled', true);
                        }
                    });
                } else {
                    setInputStatus($(this), '');
                    $('#otp_input_type').hide();
                    $('.reg-form').attr('disabled', true);
                }
            });

            // OTP input handler
            $("#partitioned").on('input', function() {
                var otp = $(this).val();

                if (otp.length === 4) {
                    setInputStatus($(this), 'loading');

                    $.ajax({
                        url: "<?php echo e(route('shop.Verify_otp')); ?>",
                        type: 'POST',
                        data: {
                            number: otp,
                            phone: $("#phone_input").val(),
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            console.log('OTP verify response:', response);

                            if (response == 1) { // OTP correct
                                $('#partitioned').attr('disabled', true);
                                setInputStatus($("#partitioned"), 'success');
                                $('.reg-form').removeAttr('disabled');
                            } else {
                                setInputStatus($("#partitioned"), 'error',
                                    'OTP is not matched!');
                                $('.reg-form').attr('disabled', true);
                            }
                        },
                        error: function() {
                            setInputStatus($("#partitioned"), 'error', 'Something went wrong!');
                            $('.reg-form').attr('disabled', true);
                        }
                    });
                } else {
                    setInputStatus($(this), '');
                    $('.reg-form').attr('disabled', true);
                }
            });
        });

        $(document).ready(function() {

            $('.__shop-apply').on('submit', function(e) {
                e.preventDefault();

                // clear previous errors
                $('.error-email').hide().text('');
                $('.error-phone').hide().text('');

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 'success') {
                            window.location.href = res.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            if (errors.email) {
                                $('.error-email').text(errors.email[0]).show();
                            }
                            if (errors.phone) {
                                $('.error-phone').text(errors.phone[0]).show();
                            }
                        }
                    }
                });
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.back-end.common_seller_1', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\adminic\resources\views/seller-views/auth-seller/seller-registeration.blade.php ENDPATH**/ ?>