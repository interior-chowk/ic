
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<style>
    .col-lg-4.col-md-12.ms-auto.aos-init.aos-animate {
        margin-left: auto;
    }
</style>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/custom.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/seller-custom.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('public/asset/css/responsive.css')); ?>">
    <section class="c-seller-login-w">
        <div class="c-seller-login-in">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-12" data-aos="zoom-in" data-aos-duration="500">
                        <div class="c-seller-login-left">
                            <img src="<?php echo e(asset('public/asset/img/seller-login-left.png')); ?>" alt="">
                            <div class="text-center">
                                <h3>If you don’t have seller account </h3>
                                <a href="<?php echo e(route('seller.auth.seller-registeration')); ?>" class="c-btn-2">Create Seller
                                    account</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 ms-auto" data-aos="zoom-in" data-aos-duration="500" data-aos-delay="500">
                        <div class="c-seller-login-right">
                            <h2>Seller <span>Login</span></h2>
                            <form id="form-id" action="<?php echo e(route('seller.auth.login')); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="c-form-group">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                    <input type="text" name="email" placeholder="Registred E-mail ID"
                                        class="c-form-control" id="signinSrEmail">
                                </div>
                                <div class="c-form-group">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                    <input type="password" name="password" placeholder="Password"
                                        class="c-form-control js-toggle-password" id="pass_log_id">
                                    <div class="c-password-show">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" name="remember"
                                        id="termsCheckbox">
                                    <label class="form-check-label" for="termsCheckbox">
                                        Remember me
                                    </label>
                                </div>
                                <!-- End Checkbox -->
                                
                                <?php ($recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha')); ?>
                                <?php if(isset($recaptcha) && $recaptcha['status'] == 1): ?>
                                    <div id="recaptcha_element" class="w-100" data-type="image"></div>
                                <?php else: ?>
                                    <div class="row">
                                        <div class="col-6 pr-0">
                                            <input type="text" class="form-control border-0"
                                                name="default_recaptcha_id_seller_login" value=""
                                                placeholder="<?php echo e(\App\CPU\translate('Enter captcha value')); ?>"
                                                class="border-0" autocomplete="off">
                                        </div>
                                        <div class="col-6 input-icons rounded bg-white">
                                            <a onclick="javascript:re_captcha();"
                                                class="d-flex align-items-center align-items-center">
                                                <img src="<?php echo e(URL('/seller/auth/code/captcha/1?captcha_session_id=default_recaptcha_id_seller_login')); ?>"
                                                    class="rounded __h-40" id="default_recaptcha_id">
                                                <i class="tio-refresh position-relative cursor-pointer p-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="text-center c-form-group">
                                    <input type="submit" name="" class="c-btn-2" value="Log In">
                                </div>
                                <div class="c-password-link">
                                    <a href="<?php echo e(route('seller.auth.forget-password')); ?>">
                                        <i class="fa fa-lock" aria-hidden="true"></i> Forget Password
                                    </a>
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
    <?php echo Toastr::message(); ?>

    <?php if($errors->any()): ?>
        <script>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                toastr.error('<?php echo e($error); ?>', Error, {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: 'toast-top-right' // Set the position here
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </script>
    <?php endif; ?>

    <!-- ========== END MAIN CONTENT ========== -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF SHOW PASSWORD
            // =======================================================
            $('.js-toggle-password').each(function() {
                new HSTogglePassword(this).init()
            });

            // INITIALIZATION OF FORM VALIDATION
            // =======================================================
            $('.js-validate').each(function() {
                $.HSCore.components.HSValidation.init($(this));
            });
        });
    </script>

    
    <?php if(isset($recaptcha) && $recaptcha['status'] == 1): ?>
        <script type="text/javascript">
            var onloadCallback = function() {
                grecaptcha.render('recaptcha_element', {
                    'sitekey': '<?php echo e(\App\CPU\Helpers::get_business_settings('recaptcha')['site_key']); ?>'
                });
            };
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script>
            $("#form-id").on('submit', function(e) {
                var response = grecaptcha.getResponse();

                if (response.length === 0) {
                    e.preventDefault();
                    toastr.error("<?php echo e(\App\CPU\translate('Please check the recaptcha')); ?>");
                }
            });
        </script>
    <?php else: ?>
        <script type="text/javascript">
            function re_captcha() {
                $url = "<?php echo e(URL('/seller/auth/code/captcha')); ?>";
                $url = $url + "/" + Math.random() + '?captcha_session_id=default_recaptcha_id_seller_login';
                document.getElementById('default_recaptcha_id').src = $url;
                console.log('url: ' + $url);
            }
        </script>
    <?php endif; ?>
    

    <?php if(env('APP_MODE') == 'demo'): ?>
        <script>
            function copy_cred() {
                $('#signinSrEmail').val('test.seller@gmail.com');
                $('#signupSrPassword').val('12345678');
                toastr.success('<?php echo e(\App\CPU\translate('Copied successfully')); ?>!', 'Success!', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        </script>
    <?php endif; ?>

    <!-- IE Support -->
    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write(
            '<script src="<?php echo e(asset('public/assets/admin')); ?>/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.back-end.common_seller_1', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\adminic\resources\views/seller-views/auth-seller/seller-login.blade.php ENDPATH**/ ?>