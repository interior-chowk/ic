<head>
    
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="_token" content="<?php echo e(csrf_token()); ?>">

    
    <?php if(isset($seo)): ?>
        <?php $og = json_decode($seo->og_tags, true); ?>

        <title><?php echo e($seo->meta_title ?? config('app.name')); ?></title>
        <meta name="description" content="<?php echo e($seo->meta_description ?? ''); ?>">
        <meta name="keywords" content="<?php echo e($seo->meta_keywords ?? ''); ?>">
        <link rel="canonical" href="<?php echo e($seo->canonical ?? url()->current()); ?>">

        
        <meta property="og:title" content="<?php echo e($og['title'] ?? $seo->meta_title); ?>">
        <meta property="og:description" content="<?php echo e($og['description'] ?? $seo->meta_description); ?>">
        <meta property="og:image" content="<?php echo e($og['image'] ?? ''); ?>">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php else: ?>
        <title><?php echo e(config('app.name')); ?></title>
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('head'); ?>

    
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo e(asset('public/asset/img/apple-touch-icon.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo e(asset('storage/app/public/company')); ?>/<?php echo e($web_config['fav_icon']->value); ?>">

    <meta name="theme-color" content="#ffffff">

    
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">

    
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/plugins/owl-carousel/owl.carousel.css')); ?>">
    <link rel="stylesheet"
        href="<?php echo e(asset('public/website/new/assets/css/plugins/magnific-popup/magnific-popup.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/plugins/jquery.countdown.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/skins/skin-demo-3.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/website/new/assets/css/demos/demo-3.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('public/assets/back-end/css/toastr.css')); ?>">

    <?php echo $__env->yieldPushContent('style'); ?>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="<?php echo e(asset('public/assets/back-end/js/toastr.js')); ?>"></script>

    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    
    <script src="<?php echo e(asset('public/asset/js/custom.js')); ?>" defer></script>

    <?php echo Toastr::message(); ?>


    
    

    
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TMC7F3KC');
    </script>
</head>
<?php /**PATH D:\xampp\htdocs\adminic\resources\views/layouts/back-end/includes-seller/head_1.blade.php ENDPATH**/ ?>