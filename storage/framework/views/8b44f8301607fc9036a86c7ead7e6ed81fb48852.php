<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(Session::get('direction')); ?>"
    style="text-align: <?php echo e(Session::get('direction') === 'rtl' ? 'right' : 'left'); ?>;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="_token" content="<?php echo e(csrf_token()); ?>">
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo e(asset('storage/app/public/company')); ?>/<?php echo e($web_config['fav_icon']->value); ?>">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo e(asset('storage/app/public/company')); ?>/<?php echo e($web_config['fav_icon']->value); ?>">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/vendor.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/custom.css">

    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/vendor/icon-set/style.css">

    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/style.css">
    <?php if(Session::get('direction') === 'rtl'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/menurtl.css">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('public/css/lightbox.css')); ?>">
    <?php echo $__env->yieldPushContent('css_or_js'); ?>

    <script
        src="<?php echo e(asset('public/assets/back-end')); ?>/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js">
    </script>
    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end')); ?>/css/toastr.css">
</head>

<body class="footer-offset">

    <?php echo $__env->make('layouts.back-end.partials._front-settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row">
        <div class="col-12 position-fixed z-9999 mt-10rem">
            <div id="loading" style="display: none;">
                <center>
                    <img width="200"
                        src="<?php echo e(asset('storage/app/public/company')); ?>/<?php echo e(\App\CPU\Helpers::get_business_settings('loader_gif')); ?>"
                        onerror="this.src='<?php echo e(asset('public/assets/front-end/img/loader.gif')); ?>'">
                </center>
            </div>
        </div>
    </div>

    <?php echo $__env->make('layouts.back-end.partials-service._header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('layouts.back-end.partials-service._side-bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <main id="content" role="main" class="main pointer-event">

        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->make('layouts.back-end.partials-service._footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('layouts.back-end.partials-service._modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </main>

    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/custom.js"></script>

    <?php echo $__env->yieldPushContent('script'); ?>

    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/vendor.min.js"></script>
    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/theme.min.js"></script>
    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/sweet_alert.js"></script>
    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/toastr.js"></script>
    <?php echo Toastr::message(); ?>


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

    <?php if($errors->any()): ?>
        <script>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                toastr.error('<?php echo e($error); ?>', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </script>
    <?php endif; ?>

    <script>
        $(document).on('ready', function() {

            if (window.localStorage.getItem('hs-builder-popover') === null) {
                $('#builderPopover').popover('show')
                    .on('shown.bs.popover', function() {
                        $('.popover').last().addClass('popover-dark')
                    });

                $(document).on('click', '#closeBuilderPopover', function() {
                    window.localStorage.setItem('hs-builder-popover', true);
                    $('#builderPopover').popover('dispose');
                });
            } else {
                $('#builderPopover').on('show.bs.popover', function() {
                    return false
                });
            }

            $('.js-navbar-vertical-aside-toggle-invoker').click(function() {
                $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
            });

            var sidebar = $('.js-navbar-vertical-aside').hsSideNav();

            $('.js-nav-tooltip-link').tooltip({
                boundary: 'window'
            })

            $(".js-nav-tooltip-link").on("show.bs.tooltip", function(e) {
                if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                    return false;
                }
            });

            $('.js-hs-unfold-invoker').each(function() {
                var unfold = new HSUnfold($(this)).init();
            });

            $('.js-form-search').each(function() {
                new HSFormSearch($(this)).init()
            });

            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format(
                    'MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

            $('.js-clipboard').each(function() {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('script_2'); ?>

    <script src="<?php echo e(asset('public/assets/back-end')); ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo e(asset('public/js/lightbox.min.js')); ?>"></script>
    <audio id="myAudio">
        <source src="<?php echo e(asset('public/assets/back-end/sound/notification.mp3')); ?>" type="audio/mpeg">
    </audio>
    <script>
        var audio = document.getElementById("myAudio");

        function playAudio() {
            audio.play();
        }

        function pauseAudio() {
            audio.pause();
        }

        $("#reset").on('click', function() {
            let placeholderImg = $("#placeholderImg").data('img');
            console.log(placeholderImg)
            $('#viewer').attr('src', placeholderImg);
            $('.spartan_remove_row').click();
        });
    </script>
    <script>
        setInterval(function() {
            $.get({
                url: '<?php echo e(route('seller.get-order-data')); ?>',
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    if (data.new_order > 0) {
                        playAudio();
                        $('#popup-modal').appendTo("body").modal('show');
                    }
                },
            });
        }, 10000);

        function check_order() {
            location.href = '<?php echo e(route('seller.orders.list', ['status' => 'all'])); ?>';
        }
    </script>

    <script>
        $("#search-bar-input").keyup(function() {
            $("#search-card").css("display", "block");
            let key = $("#search-bar-input").val();
            if (key.length > 0) {
                $.get({
                    url: '<?php echo e(url('/')); ?>/admin/search-function/',
                    dataType: 'json',
                    data: {
                        key: key
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(data) {
                        $('#search-result-box').empty().html(data.result)
                    },
                    complete: function() {
                        $('#loading').hide();
                    },
                });
            } else {
                $('#search-result-box').empty();
            }
        });

        $(document).mouseup(function(e) {
            var container = $("#search-card");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        function form_alert(id, message) {
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + id).submit()
                }
            })
        }
    </script>

    <script>
        function call_demo() {
            toastr.info('Update option is disabled for demo!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
    <script>
        function openInfoWeb() {
            var x = document.getElementById("website_info");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
    </script>

    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write(
            '<script src="<?php echo e(asset('public/assets/back-end')); ?>/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
    <?php echo $__env->yieldPushContent('script'); ?>

    <script src="<?php echo e(asset('public/ckeditor/ckeditor.js')); ?>"></script>
    <script>
        CKEDITOR.replace('editor');
    </script>

    <script>
        initSample();
    </script>
    <script>
        function getRndInteger() {
            return Math.floor(Math.random() * 90000) + 100000;
        }
    </script>
</body>

</html>
<?php /**PATH D:\xampp\htdocs\adminic\resources\views/layouts/back-end/app-service.blade.php ENDPATH**/ ?>