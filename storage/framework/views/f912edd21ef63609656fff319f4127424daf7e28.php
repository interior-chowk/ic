
<?php $__env->startSection('content'); ?>
    <?php $__env->startPush('style'); ?>
        <style>
            .truncate-line-1 {
                min-height: 44px;
                line-height: 1.2;
                display: flex;
                align-items: center;
                padding: 0 12px;
            }
        </style>
        <link rel="stylesheet" href="<?php echo e(asset('public/website/assets/css/home.css')); ?>">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [{
                "@type": "Organization",
                "@id": "https://interiorchowk.com/#organization",
                "name": "InteriorChowk",
                "url": "https://interiorchowk.com",
                "logo": {
                    "@type": "ImageObject",
                    "url": "https://interiorchowk.com/public/website/assets/images/logoic.png",
                    "width": 100,
                    "height": 32
                },
                "sameAs": [
                    "https://www.facebook.com/people/InteriorChowk/61554788270651/",
                    "https://www.instagram.com/interiorchowk/",
                    "https://www.linkedin.com/company/interiorchowk/",
                    "https://www.youtube.com/channel/UCLXmVanINf5oL1gNVHpCmbQ/"
                ],
                "contactPoint": {
                    "@type": "ContactPoint",
                    "telephone": "+91-9955680690",
                    "contactType": "Customer Support",
                    "email": "customersupport@interiorchowk.com",
                    "availableLanguage": ["English", "Hindi"],
                    "areaServed": "IN"
                },
                "description": "InteriorChowk offers premium home interior products online. Shop stylish furniture, decor, kitchenware, lighting & more. Trusted by homeowners across India.",
                "foundingDate": "2023",
                "founder": {
                    "@type": "Person",
                    "name": "Vivek Singh"
                },
                "address": {
                    "@type": "PostalAddress",
                    "addressLocality": "Greater Noida",
                    "addressRegion": "Uttar Pradesh",
                    "addressCountry": "IN"
                }
            },
            {
                "@type": "WebSite",
                "@id": "https://interiorchowk.com/#website",
                "url": "https://interiorchowk.com",
                "name": "InteriorChowk",
                "publisher": {
                    "@id": "https://interiorchowk.com/#organization"
                },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "https://interiorchowk.com/search?q={search_term_string}",
                    "query-input": "required name=search_term_string"
                }
            }
        ]
    }
</script>
    <?php $__env->stopPush(); ?>
    <main class="main">

        <!-- intro-section 1 start here-------------------------------------------------------------------------------------------- -->
        <div class="intro-section sectionMargin">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section2 slider_desktop">
                        <?php $__currentLoopData = $main_banner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="intro-slide">
                                <figure class="slide-image">
                                    <picture>
                                        <source media="(max-width: 480px)"
                                            srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>">
                                        <a href="<?php echo e($banner->url); ?>">
                                            <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>"
                                                alt="BannerIntro">
                                        </a>
                                    </picture>
                                </figure>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section2 slider_mobile">
                        <?php $__currentLoopData = $mobile_banner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="intro-slide">
                                <figure class="slide-image">
                                    <picture>
                                        <source media="(max-width: 480px)"
                                            srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>">
                                        <a href="<?php echo e($banner->url); ?>">
                                            <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>"
                                                alt="BannerSlider">
                                        </a>
                                    </picture>
                                </figure>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- section-3 start here-------------------------------------------------------------------------------------------- -->
        <div class="section-3 card_design sectionMargin">
            <div class="slide_mob">
                <div class="container">
                    <div class="row">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-3">
                                <div class="product product-4">
                                    <figure class="product-media">
                                        <a href="<?php echo e(url('category/' . $ca->slug)); ?>">
                                            <img src="<?php echo e($caturl . $ca->icon); ?>" alt="Product img" class="product-image">
                                        </a>
                                    </figure>
                                    <div class="product-footer">
                                        <p class="text-center">
                                            <a href="<?php echo e(url('category/' . $ca->slug)); ?>"><?php echo e($ca->name); ?></a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <div class="slider_desktop">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="owl-carousel category-carousel owl-simple">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="product product-4">
                                        <figure class="product-media" style="margin-bottom: 0px !important;">
                                            <a href="<?php echo e(url('category/' . $ca->slug)); ?>">
                                                <img src="<?php echo e($caturl . $ca->icon); ?>"
                                                    alt="Shop <?php echo e($ca->name); ?> Category" class="product-image">
                                            </a>
                                        </figure>

                                        <div class="product-footer">
                                            <p class="text-center">
                                                <a href="<?php echo e(url('category/' . $ca->slug)); ?>" class="truncate-line-1"
                                                    style="padding:0px 12px;">
                                                    <?php echo e($ca->name); ?>

                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- web service provider start here-------------------------------------------------------------------------------------------- -->
        
        <?php if(isset($Service_Provider_Banner_3->photo)): ?>
            <div class="custom_banner web-service-provider sectionMargin">
                <div class="container">
                    <div class="row service-provider-banners">
                        <div class="col-4 col-lg-4 short-banner">
                            <div class="banner-wrapper tall-banner">
                                <a href="<?php echo e($Service_Provider_Banner_3->url); ?>">
                                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Service_Provider_Banner_3->photo, '/')); ?>"
                                        alt="Banner-3">
                                </a>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 short-banner">
                            <div class="banner-wrapper short-banner">
                                <a href="<?php echo e($Service_Provider_Banner_1->url); ?>">
                                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Service_Provider_Banner_1->photo, '/')); ?>"
                                        alt="Banner">
                                </a>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 short-banner">
                            <div class="banner-wrapper short-banner">
                                <a href="<?php echo e($Service_Provider_Banner_2->url); ?>">
                                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Service_Provider_Banner_2->photo, '/')); ?>"
                                        alt="Banner">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="custom_banner service-mobile-provider d-none sectionMargin">
            <div class="container">
                <div class="row service-provider-banners">
                    <div class="col-lg-4 col-4 short-banner">
                        <div class="banner-wrapper tall-banner">
                            <a href="<?php echo e($Service_Provider_Banner_3->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Mob_Provider_Banner_3->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4 short-banner">
                        <div class="banner-wrapper short-banner">
                            <a href="<?php echo e($Service_Provider_Banner_1->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Mob_Provider_Banner_1->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4 short-banner">
                        <div class="banner-wrapper short-banner">
                            <a href="<?php echo e($Service_Provider_Banner_2->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Mob_Provider_Banner_2->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- intro section 2 start here-------------------------------------------------------------------------------------------- -->
        <?php if(!empty($main_banner_2)): ?>
            <div class="intro-section">
                <div class="container sectionCrouselMargin">
                    <div class="row">
                        <div class="col-12 col-lg-12 web-service-provider">
                            <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section3">
                                <?php $__currentLoopData = $main_banner_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="intro-slide">
                                        <figure class="slide-image">
                                            <picture>
                                                <source media="(max-width: 480px)"
                                                    srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>">
                                                <a href="<?php echo e($banner->url); ?>">
                                                    <img srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>"
                                                        alt="Banner">
                                                </a>
                                            </picture>
                                        </figure>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12 d-none service-mobile-provider elevateSliderMobile">
                            <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside section3">
                                <?php $__currentLoopData = $mob_main_banner_2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="intro-slide">
                                        <figure class="slide-image">
                                            <picture>
                                                <source media="(max-width: 480px)"
                                                    srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>">
                                                <a href="<?php echo e($banner->url); ?>">
                                                    <img srcset="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/')); ?>"
                                                        alt="Banner">
                                                </a>
                                            </picture>
                                        </figure>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
            <?php if($recently_viewed->isNotEmpty()): ?>
                <div class="page-content sectionCrouselMargin recViewItemWrapper">
                    <div class="recently-viewed-section">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="heading heading-flex">
                                        <div class="heading-left">
                                            <h2 class="title">Recently items you've viewed</h2>
                                        </div>
                                    </div>

                                    <div
                                        class="owl-carousel recently-viewed-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                        <?php $__currentLoopData = $recently_viewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product product-7">
                                                <figure class="product-media">
                                                    <?php if($item->sku_discount_type == 'percent' && $item->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new"><?php echo e(round($item->sku_discount, 0)); ?>%
                                                            off</span>
                                                    <?php elseif($item->sku_discount_type == 'flat' && $item->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new">₹<?php echo e(number_format($item->sku_discount, 0)); ?>

                                                            off</span>
                                                    <?php endif; ?>

                                                    <?php if($item->free_delivery == 1): ?>
                                                        <span class="product-label product-label-two label-sale">Free
                                                            Delivery</span>
                                                    <?php endif; ?>

                                                    <a href="<?php echo e(url('product/' . ($item->slug ?? '#'))); ?>">
                                                        <img src="<?php echo e(!empty($item->thumbnail_image)
                                                            ? asset('storage/app/public/images/' . $item->thumbnail_image)
                                                            : asset('public/website/assets/images/products/product-placeholder.jpg')); ?>"
                                                            alt="<?php echo e($item->name ?? 'Product'); ?>" class="product-image">
                                                    </a>
                                                </figure>

                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <a href="#">Planters</a>
                                                    </div>
                                                    <h3 class="product-title">
                                                        <a href="<?php echo e(url('product/' . ($item->slug ?? '#'))); ?>">
                                                            <?php echo e(Str::limit($item->name ?? 'Unnamed Product', 60)); ?>

                                                        </a>
                                                    </h3>
                                                    <div class="product-price" style="font-size: 2rem;">
                                                        ₹ <?php echo e($item->listed_price ?? '0.00'); ?>

                                                        <?php if(!empty($item->variant_mrp) && $item->variant_mrp > $item->listed_price): ?>
                                                            <span class="price-cut">₹ <?php echo e($item->variant_mrp); ?></span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" style="width: 40%;"></div>
                                                        </div>
                                                        <span class="ratings-text">( 1 Review )</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(!empty($Instant_Delivery_Banner->photo)): ?>
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin">
                <a href="<?php echo e(url('instant-delivery-products')); ?>" aria-label="Browse Instant Delivery Products">
                    <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                        style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Instant_Delivery_Banner->photo, '/')); ?>');">
                    </div>
                </a>
            </div>

            <div class="instant-delivery-banner-container d-none service-mobile-provider sectionMargin">
                <a href="<?php echo e(url('instant-delivery-products')); ?>" aria-label="Browse Instant Delivery Products">
                    <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                        style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Mob_Instant_Delivery_Banner->photo, '/')); ?>');">
                    </div>
                </a>
            </div>
        <?php endif; ?>


        <?php if(!empty($desktop1)): ?>
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin"
                style="height:324px;width:1296px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($desktop1->photo, '/')); ?>'); height:324px;width:1296px !important;">
                </div>
            </div>
        <?php endif; ?>

        <?php if(auth()->guard()->check()): ?>
            <?php if($related_products->isNotEmpty()): ?>
                <div class="page-content sectionCrouselMargin relViewItemWrapper">
                    <div class="related-products-section"
                        style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[1]->photo, '/')); ?>');">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="heading heading-flex">
                                        <div class="heading-left">
                                            <h2 class="title">Related item you've viewed</h2>
                                        </div>
                                    </div>

                                    <div
                                        class="owl-carousel related-products-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                        <?php $__currentLoopData = $related_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product product-7">
                                                <figure class="product-media">
                                                    <?php if($rp->sku_discount_type === 'percent' && $rp->sku_discount > 0): ?>
                                                        <span class="product-label label-new"><?php echo e(round($rp->sku_discount)); ?>%
                                                            off</span>
                                                    <?php elseif($rp->sku_discount_type === 'flat' && $rp->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new">₹<?php echo e(number_format($rp->sku_discount)); ?>

                                                            off</span>
                                                    <?php endif; ?>

                                                    <?php if($rp->free_delivery): ?>
                                                        <span class="product-label product-label-two label-sale">Free
                                                            Delivery</span>
                                                    <?php endif; ?>

                                                    <a href="<?php echo e(url('product/' . $rp->slug)); ?>">
                                                        <img src="<?php echo e(asset('storage/app/public/images/' . ($rp->thumbnail_image ?? 'default.jpg'))); ?>"
                                                            alt="<?php echo e($rp->name); ?>" class="product-image">
                                                    </a>
                                                </figure>

                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <a
                                                            href="<?php echo e(url('category/' . $rp->category_id)); ?>"><?php echo e($rp->category_id); ?></a>
                                                    </div>

                                                    <h3 class="product-title">
                                                        <a href="<?php echo e(url('product/' . $rp->slug)); ?>"
                                                            class="truncate-line-1"><?php echo e($rp->name); ?></a>
                                                    </h3>

                                                    <div class="product-price">
                                                        ₹<?php echo e(number_format($rp->listed_price, 0)); ?>

                                                        <?php if($rp->variant_mrp > $rp->listed_price): ?>
                                                            <span
                                                                class="price-cut">₹<?php echo e(number_format($rp->variant_mrp, 0)); ?></span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" style="width: <?php echo e(rand(20, 100)); ?>%;">
                                                            </div>
                                                        </div>
                                                        <span class="ratings-text">(<?php echo e(rand(1, 10)); ?> Reviews)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
            <?php if($more_related_products->isNotEmpty()): ?>
                <div class="page-content more-items-section sectionCrouselMargin">
                    <div class="more-related-products-banner"
                        style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[0]->photo, '/')); ?>');">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="heading heading-flex">
                                        <div class="heading-left">
                                            <h2 class="title">More items to consider</h2>
                                        </div>
                                    </div>

                                    <div
                                        class="owl-carousel more-items-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                        <?php $__currentLoopData = $more_related_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product product-7">
                                                <figure class="product-media">
                                                    <?php if($mp->sku_discount_type === 'percent' && $mp->sku_discount > 0): ?>
                                                        <span class="product-label label-new"><?php echo e(round($mp->sku_discount)); ?>%
                                                            off</span>
                                                    <?php elseif($mp->sku_discount_type === 'flat' && $mp->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new">₹<?php echo e(number_format($mp->sku_discount)); ?>

                                                            off</span>
                                                    <?php endif; ?>

                                                    <a href="<?php echo e(url('product/' . $mp->slug)); ?>">
                                                        <img src="<?php echo e(asset('storage/app/public/images/' . ($mp->thumbnail_image ?? 'default.jpg'))); ?>"
                                                            alt="<?php echo e($mp->name); ?>" class="product-image">
                                                    </a>
                                                </figure>

                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <a
                                                            href="<?php echo e(url('category/' . $mp->category_id)); ?>"><?php echo e($mp->category_id); ?></a>
                                                    </div>
                                                    <h3 class="product-title">
                                                        <a href="<?php echo e(url('product/' . $mp->slug)); ?>" class="truncate-line-1">
                                                            <?php echo e($mp->name); ?>

                                                        </a>
                                                    </h3>
                                                    <div class="product-price">
                                                        ₹<?php echo e(number_format($mp->listed_price, 0)); ?>

                                                        <?php if($mp->variant_mrp > $mp->listed_price): ?>
                                                            <span
                                                                class="price-cut">₹<?php echo e(number_format($mp->variant_mrp, 0)); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" style="width: <?php echo e(rand(20, 100)); ?>%;">
                                                            </div>
                                                        </div>
                                                        <span class="ratings-text">(<?php echo e(rand(1, 10)); ?> Reviews)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
            $fileUrl = null;
            $isVideo = false;
            $extension = null;
            if (!empty($Banner_3->photo)) {
                $extension = strtolower(pathinfo($Banner_3->photo, PATHINFO_EXTENSION));
                $isVideo = in_array($extension, ['mp4', 'webm']);
                $fileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_3->photo, '/');
            }

            $MobfileUrl = null;
            $MobisVideo = false;
            $Mobextension = null;
            if (!empty($Mob_Banner_3->photo)) {
                $Mobextension = strtolower(pathinfo($Mob_Banner_3->photo, PATHINFO_EXTENSION));
                $MobisVideo = in_array($Mobextension, ['mp4', 'webm']);
                $MobfileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Mob_Banner_3->photo, '/');
            }
        ?>

        <?php if($MobfileUrl): ?>
            <div class="video-banner-image d-none service-mobile-provider vidMobBgImg sectionBannerMargin">
                <a href="<?php echo e($Mob_Banner_3->resource_type == 'category' ? $Mob_Banner_3->url : url('banner_products/' . $Mob_Banner_3->id)); ?>"
                    area-label="Browse Banner Products">
                    <?php if($MobisVideo): ?>
                        <video autoplay muted loop playsinline class="banner-video">
                            <source src="<?php echo e($MobfileUrl); ?>" type="video/<?php echo e($Mobextension); ?>">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <div class="banner-image" style="background-image: url('<?php echo e($MobfileUrl); ?>');"></div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if(auth()->guard()->check()): ?>
            <?php if($wishlists->isNotEmpty()): ?>
                <div class="page-content sectionCrouselMargin wishListWrapper">
                    <div class="wishlist-section"
                        style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[1]->photo, '/')); ?>');">
                        <div class="container">

                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="heading heading-flex">
                                        <div class="heading-left">
                                            <h2 class="title">Love it? Get it.</h2>
                                        </div>
                                        <div class="heading-right">
                                            <button class="btn explore-more-btn"
                                                onclick="window.location.href='<?php echo e(url('/wishlist')); ?>'">
                                                Your wishlist <span class="circle-arrow"><i
                                                        class="bi bi-arrow-right"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="owl-carousel wishlist-carousel owl-simple carousel-equal-height carousel-with-shadow">
                                        <?php $__currentLoopData = $wishlists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wishlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product product-7 love-it">
                                                <div class="media">
                                                    <?php if($wishlist->sku_discount_type === 'percent' && $wishlist->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new"><?php echo e(round($wishlist->sku_discount)); ?>%
                                                            off</span>
                                                    <?php elseif($wishlist->sku_discount_type === 'flat' && $wishlist->sku_discount > 0): ?>
                                                        <span
                                                            class="product-label label-new">₹<?php echo e(number_format($wishlist->sku_discount)); ?>

                                                            off</span>
                                                    <?php endif; ?>

                                                    <img src="<?php echo e(asset('storage/app/public/images/' . $wishlist->thumbnail_image)); ?>"
                                                        alt="<?php echo e($wishlist->name); ?>" class="product-image loveitgetit">

                                                    <div class="media-body">
                                                        <h5 class="product-title truncate-line-2 mb-1">
                                                            <a
                                                                href="<?php echo e(url('product/' . $wishlist->slug)); ?>"><?php echo e(\Illuminate\Support\Str::limit($wishlist->name, 20, '..')); ?></a>
                                                        </h5>
                                                        <div class="product-cat">
                                                            <a href="#"><?php echo e($wishlist->category ?? 'Uncategorized'); ?></a>
                                                        </div>
                                                        <h6 class="product-type mb-0_5"><?php echo e($wishlist->variation); ?></h6>
                                                        <div class="product-price">
                                                            ₹ <?php echo e(number_format($wishlist->listed_price, 0)); ?>

                                                            <?php if($wishlist->discount_percent): ?>
                                                                <span class="price-cut">₹
                                                                    <?php echo e(number_format($wishlist->variant_mrp, 0)); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if($wishlist->free_delivery == 1): ?>
                                                            <button type="button" class="btndgn mt-1">Free Delivery</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="page-content sectionCrouselMargin trenBanWrapper">
            <div class="trending-banner"
                style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[0]->photo, '/')); ?>');">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="heading heading-flex">
                                <div class="heading-left">
                                    <h2 class="title">Trending now</h2>
                                </div>
                                <div class="heading-right">
                                    <button class="btn explore-more-btn d-flex align-items-center"
                                        onclick="window.location.href='<?php echo e(url('top-products')); ?>'">
                                        Explore more
                                        <span class="circle-arrow ms-3">
                                            <i class="bi bi-arrow-right"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="tab-content tab-content-carousel">
                                <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel">
                                    <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow trend-media"
                                        id="top-products-carousel">
                                        <?php $__currentLoopData = $top_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="product product-7">
                                                <figure class="product-media">
                                                    <?php if($tp->discount_type == 'percent' && $tp->discount > 0): ?>
                                                        <span
                                                            class="product-label label-new"><?php echo e(round($tp->discount, 0)); ?>%
                                                            off</span>
                                                    <?php elseif($tp->discount_type == 'flat' && $tp->discount > 0): ?>
                                                        <span
                                                            class="product-label label-new">₹<?php echo e(number_format($tp->discount, 0)); ?>

                                                            off</span>
                                                    <?php endif; ?>
                                                    <?php if($tp->free_delivery == 1): ?>
                                                        <span class="product-label product-label-two label-sale">Free
                                                            Delivery</span>
                                                    <?php endif; ?>
                                                    
                                                    <?php
                                                        $images = json_decode($tp->image, true);

                                                        $productImage =
                                                            !empty($images) && isset($images[0])
                                                                ? rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') .
                                                                    '/' .
                                                                    ltrim($images[0], '/')
                                                                : asset('storage/images/default.jpg');
                                                    ?>

                                                    <a href="<?php echo e(url('product/' . $tp->slug)); ?>"
                                                        onclick="setRecentlyViewed(<?php echo e($tp->product_id); ?>)">
                                                        <img src="<?php echo e($productImage); ?>" alt="<?php echo e($tp->name); ?>"
                                                            class="product-image">
                                                    </a>
                                                </figure>
                                                <div class="product-body">
                                                    <div class="product-cat">
                                                        <?php
                                                            $categories = json_decode($tp->category_ids, true);
                                                        ?>
                                                        <a href="#">
                                                            <?php if(!empty($categories)): ?>
                                                                Category <?php echo e($categories[0]['id']); ?>

                                                            <?php else: ?>
                                                                Unspecified
                                                            <?php endif; ?>
                                                        </a>
                                                    </div>
                                                    <h3 class="product-title">
                                                        <a href="<?php echo e(url('product/' . $tp->slug)); ?>"
                                                            onclick="setRecentlyViewed(<?php echo e($tp->product_id); ?>)"><?php echo e($tp->name); ?></a>
                                                    </h3>
                                                    <div class="product-price">
                                                        ₹<?php echo e(number_format($tp->listed_price, 0)); ?>

                                                        <?php if($tp->discount > 0): ?>
                                                            <span
                                                                class="price-cut">₹<?php echo e(number_format($tp->variant_mrp, 0)); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="ratings-container">
                                                        <div class="ratings">
                                                            <div class="ratings-val" style="width: 20%;"></div>
                                                        </div>
                                                        <span class="ratings-text">(2 Reviews)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <script>
                                    function setRecentlyViewed(productId) {
                                        $.ajax({
                                            url: "<?php echo e(route('recently_view')); ?>",
                                            type: "POST",
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                            },
                                            data: {
                                                product_id: productId
                                            },
                                            success: function(response) {},
                                            error: function(xhr, status, error) {
                                                console.error(xhr.responseText);
                                            }
                                        });
                                    }
                                </script>
                                <div class="tab-pane p-0 fade" id="trending-men-tab" role="tabpanel">
                                    <div class="banner-group">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="banner banner-overlay comBanner">
                                                        <a href="#">
                                                            <img src="<?php echo e(asset('public/website/assets/images/demos/demo-20/banners/banner-6.jpg')); ?>"
                                                                alt="Banner">
                                                        </a>
                                                        <div class="banner-content">
                                                            <h4 class="banner-subtitle text-white"><a
                                                                    href="#">INTERIOR
                                                                    CHOWK</a></h4>
                                                            <h3 class="banner-title text-white"><a
                                                                    href="#">Arcitech</a>
                                                            </h3>
                                                            <a href="#"
                                                                class="btn btn-outline-white-3 banner-link">Discover now
                                                                <i class="icon-long-arrow-right"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(!empty($Banner_3)): ?>
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin"
                style="height:400px;width:1266px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_3->photo, '/')); ?>'); height:400px;width:1266px !important;">
                </div>
            </div>

            
            <div class="architect-banner-container sectionBannerMargin arcBanCntWrapper d-block d-lg-none">
                <a href="<?php echo e($Banner_3->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_3->photo, '/')); ?>"
                        class="img-fluid w-100 rounded" alt="banner">
                </a>
            </div>
        <?php endif; ?>

        <div class="container featured topcategory sectionMargin">
            <h2 class="title text-center">Top categories</h2>
            <div class="row justify-content-center px-3">
                <?php $__currentLoopData = $top_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t_ca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-3">
                        <div class="category-card text-center">
                            <figure class="product-media">
                                <a href="<?php echo e(url('category/' . $t_ca->slug)); ?>">
                                    <img src="<?php echo e($caturl . $t_ca->icon); ?>" alt="<?php echo e($t_ca->name); ?>"
                                        class="product-image img-fluid">
                                </a>
                            </figure>
                            <div class="category-name-wrapper">
                                <a href="<?php echo e(url('category/' . $t_ca->slug)); ?>" class="category-name">
                                    <?php echo e($t_ca->name); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="container featured sectionMargin topBrandWrapper">
            <div class="heading heading-flex">
                <div class="heading-left">
                    <h2 class="title">Top brands</h2>
                </div>
                <div class="heading-right">
                    <button class="btn explore-more-btn d-flex align-items-center" type="button"
                        onclick="window.location.href='<?php echo e(url('brands')); ?>'">
                        Explore more
                        <span class="circle-arrow ms-3">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="featured-brands-tab" role="tabpanel">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                id="top-brands-carousel">
                                <?php $__currentLoopData = $top_brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="brand-card">
                                        <figure class="brand-logo-wrapper">
                                            <a href="<?php echo e(url('products_2', ['brand_id' => $tb->name])); ?>">
                                                <img src="<?php echo e(env('CLOUDFLARE_R2_PUBLIC_URL')); ?><?php echo e($tb->image); ?>"
                                                    alt="<?php echo e($tb->name); ?>" class="brand-logo-img">
                                            </a>
                                        </figure>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(!empty($Banner_4)): ?>
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin mb-4"
                style="height:400px;width:1266px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_4->photo, '/')); ?>'); height:400px;width:1266px !important;">
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($mobile3->photo)): ?>
            <div class="architect-banner-container sectionBannerMargin arcBanCntWrapper d-block d-lg-none">
                <a href="<?php echo e($mobile3->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($mobile3->photo, '/')); ?>"
                        class="img-fluid w-100 rounded" alt="banner">
                </a>
            </div>
        <?php endif; ?>

        <?php if(!empty($architects) && count($architects) > 0): ?>
            <div class="page-content sectionMargin topArchWrapper">
                <div class="section-6 top-interior-section"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[1]->photo, '/')); ?>');">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading heading-flex">
                                    <div class="heading-left">
                                        <h2 class="title">Top Architects</h2>
                                    </div>
                                    <div class="heading-right">
                                        <button class="btn explore-more-btn d-flex align-items-center"
                                            onclick="window.location.href='<?php echo e(url('architects')); ?>'">
                                            Explore more
                                            <span class="circle-arrow ms-3">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                    id="architects-carousel">
                                    <?php $__currentLoopData = $architects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="interior-card">
                                            <a href="<?php echo e(url('/interior-designers' . '/' . Str::slug($arch->name))); ?>">
                                                <div class="card text-center border-0">
                                                    <img src="<?php echo e(asset($arch->banner_image)); ?>"
                                                        class="card-img-top interior-banner-img" alt="Designer Banner">
                                                    <div class="interior-profile-img">
                                                        <img src="<?php echo e(asset('storage/app/public/service-provider/profile/' . $arch->image)); ?>"
                                                            alt="Architect Profile">
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="card-title"><?php echo e($arch->name ?? 'Unknown'); ?></div>
                                                        <p class="card-text text-dark truncate-text">
                                                            <?php echo e($arch->city ? str_replace(['[', ']', '"'], '', $arch->city) : 'Location not available'); ?>

                                                        </p>
                                                        <div class="ratings-container d-block">
                                                            <div class="ratings">
                                                                <div class="ratings-val" style="width: 20%;"></div>
                                                            </div>
                                                            <span class="ratings-text text-dark d-block ml-0 mt-1">( 2
                                                                Reviews
                                                                )</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($Banner_5)): ?>
            
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin"
                style="height:400px;width:1266px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_5->photo, '/')); ?>'); height:400px;width:1266px !important;">
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($mobile4->photo)): ?>
            <div class="architect-banner-container sectionBannerMargin arcBanCntWrapper d-block d-lg-none">
                <a href="<?php echo e($mobile4->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($mobile4->photo, '/')); ?>"
                        class="img-fluid w-100 rounded" alt="banner">
                </a>
            </div>
        <?php endif; ?>

        <?php if(!empty($interior_designer) && count($interior_designer) > 0): ?>
            <div class="page-content sectionMargin topInterDesiWrapper">
                <div class="section-6 top-interior-section"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[0]->photo, '/')); ?>');">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading heading-flex">
                                    <div class="heading-left">
                                        <h2 class="title">Top Interior Designers</h2>
                                    </div>
                                    <div class="heading-right">
                                        <button class="btn explore-more-btn d-flex align-items-center"
                                            onclick="window.location.href='<?php echo e(url('interior-designers')); ?>'">
                                            Explore more
                                            <span class="circle-arrow ms-3">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                    id="interior-designers-carousel">
                                    <?php $__currentLoopData = $interior_designer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="interior-card">
                                            <a
                                                href="<?php echo e(url('/interior-designers' . '/' . Str::slug($designer->name))); ?>">
                                                <div class="card text-center border-0">
                                                    <img src="<?php echo e(asset($designer->banner_image)); ?>"
                                                        class="card-img-top interior-banner-img" alt="Designer Banner">
                                                    <div class="interior-profile-img">
                                                        <img src="<?php echo e(asset('storage/app/public/service-provider/profile/' . $designer->image)); ?>"
                                                            alt="Designer Profile">
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <h5 class="card-title"><?php echo e($designer->name ?? 'Unknown'); ?></h5>
                                                        <p class="card-text text-dark truncate-text">
                                                            <?php echo e($designer->city ? str_replace(['[', ']', '"'], '', $designer->city) : 'Location not available'); ?>

                                                        </p>
                                                        <div class="ratings-container d-block">
                                                            <div class="ratings">
                                                                <div class="ratings-val" style="width: 20%;"></div>
                                                            </div>
                                                            <span class="ratings-text text-dark d-block ml-0 mt-1">( 2
                                                                Reviews
                                                                )</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($Desktop5)): ?>
            
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin"
                style="height:400px;width:1266px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Desktop5->photo, '/')); ?>'); height:400px;width:1266px !important;">
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($Banner_6->photo)): ?>
            <div class="architect-banner-container sectionBannerMargin arcBanCntWrapper d-block d-lg-none">
                <a href="<?php echo e($Banner_6->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_6->photo, '/')); ?>"
                        class="img-fluid w-100 rounded" alt="banner">
                </a>
            </div>
        <?php endif; ?>

        <?php if(!empty($Banner_7->photo)): ?>
            <?php
                $extension = strtolower(pathinfo($Banner_7->photo, PATHINFO_EXTENSION));
                $isVideo = in_array($extension, ['mp4', 'webm']);
                $fileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_7->photo, '/');
                $bannerLink =
                    $Banner_7->resource_type === 'category' ? $Banner_7->url : url('banner_products/' . $Banner_7->id);
            ?>
            <div class="media-banner-section sectionBannerMargin medBannerWrapper">
                <a href="<?php echo e($bannerLink); ?>">
                    <?php if($isVideo): ?>
                        <div class="media-banner video">
                            <video autoplay muted loop playsinline>
                                <source src="<?php echo e($fileUrl); ?>" type="video/<?php echo e($extension); ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php else: ?>
                        <div class="media-banner image" style="background-image: url('<?php echo e($fileUrl); ?>');"></div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if(!empty($contractors) && count($contractors) > 0): ?>
            <div class="page-content sectionMargin topContracWrapper">
                <div class="section-6 top-interior-section"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Seasonal_Banner[0]->photo, '/')); ?>');">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading heading-flex">
                                    <div class="heading-left">
                                        <h2 class="title">Top Contractors</h2>
                                    </div>
                                    <div class="heading-right">
                                        <button class="btn explore-more-btn d-flex align-items-center"
                                            onclick="window.location.href='<?php echo e(url('contractors')); ?>'">
                                            Explore more
                                            <span class="circle-arrow ms-3">
                                                <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                    id="contractor-carousel">

                                    <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="interior-card">
                                            <a href="<?php echo e(url('/interior-designers/' . Str::slug($contr->name))); ?>">
                                                <div class="card text-center border-0">
                                                    <img src="<?php echo e($contr->banner_image); ?>"
                                                        class="card-img-top interior-banner-img"
                                                        alt="Contractor banner for <?php echo e($contr->name ?? 'Interior designer'); ?>">

                                                    <div class="interior-profile-img">
                                                        <img src="<?php echo e(asset('storage/app/public/service-provider/profile/' . $contr->image)); ?>"
                                                            alt="Profile photo of <?php echo e($contr->name ?? 'Interior designer'); ?>"
                                                            style="width:35%;">
                                                    </div>

                                                    <div class="card-body p-3">
                                                        <div class="card-title">
                                                            <?php echo e($contr->name ?? 'Unknown'); ?>

                                                        </div>

                                                        <p class="card-text text-dark truncate-text">
                                                            <?php echo e($contr->city ? str_replace(['[', ']', '"'], '', $contr->city) : 'Location not available'); ?>

                                                        </p>

                                                        <div class="ratings-container d-block">
                                                            <div class="ratings">
                                                                <div class="ratings-val" style="width: 20%;"></div>
                                                            </div>
                                                            <span class="ratings-text text-dark d-block ml-0 mt-1">
                                                                (2 Reviews)
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($Banner_7)): ?>
            
            <div class="container instant-delivery-banner-container web-service-provider sectionBannerMargin"
                style="height:400px;width:1266px !important; margin:auto;">
                <div class="video-banner video-banner-bg text-right instant-delivery-banner"
                    style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_7->photo, '/')); ?>'); height:400px;width:1266px !important;">
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($mobile6->photo)): ?>
            <div class="architect-banner-container sectionBannerMargin arcBanCntWrapper d-block d-lg-none">
                <a href="<?php echo e($mobile6->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($mobile6->photo, '/')); ?>"
                        class="img-fluid w-100 rounded" alt="banner">
                </a>
            </div>
        <?php endif; ?>

        <?php
            $extension = strtolower(pathinfo($banner->photo, PATHINFO_EXTENSION));
            $isVideo = in_array($extension, ['mp4', 'webm']);
            $fileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($banner->photo, '/');
            $linkUrl = $banner->resource_type === 'brand' ? $banner->url : url('banner_products/' . $banner->id);

        ?>

        <?php if(!empty($Day_BG_w)): ?>
            <div class="page-content sectionMargin dealBanWrapper">
                <div class="deal-banner-wrapper"
                    style="background-image: url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Day_BG_w->photo, '/')); ?>');">
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="tab-content tab-content-carousel">
                                <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 col-md-12">
                                                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow dealBannMarTop"
                                                    id="deals-carousel">
                                                    <?php $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $current_time = now();
                                                            $end_time = \Carbon\Carbon::parse($deal->expire_date_time);
                                                            $expired = $current_time->greaterThan($end_time);
                                                            $time_left = $end_time->diff($current_time);
                                                            $time_display = $expired
                                                                ? 'Expired'
                                                                : ($time_left->d > 0
                                                                    ? "{$time_left->d}d {$time_left->h}h..."
                                                                    : ($time_left->h > 0
                                                                        ? "{$time_left->h}h {$time_left->i}m..."
                                                                        : ($time_left->i > 0
                                                                            ? "{$time_left->i}m {$time_left->s}s"
                                                                            : "{$time_left->s}s")));
                                                            $image = json_decode($deal->image, true);
                                                            $first_image = isset($image[0])
                                                                ? asset('storage/app/public/images/' . $image[0])
                                                                : asset(
                                                                    'storage/app/public/images/' .
                                                                        $deal->thumbnail_image,
                                                                );
                                                        ?>
                                                        <div class="product product-7 featured-item">
                                                            <figure class="product-media">
                                                                <?php if($deal->discount > 0): ?>
                                                                    <span class="product-label label-new">
                                                                        <?php echo e($deal->discount_type == 'percent' ? round($deal->discount, 0) . '%' : '₹' . number_format($deal->discount, 0)); ?>

                                                                        OFF
                                                                    </span>
                                                                <?php endif; ?>
                                                                <span
                                                                    class="product-label product-label-two label-sale">Free
                                                                    Delivery</span>
                                                                <a href="<?php echo e(url('product/' . $deal->slug)); ?>">
                                                                    <img src="<?php echo e($first_image); ?>"
                                                                        alt="<?php echo e($deal->name); ?>"
                                                                        class="product-image dealBanWithoutHeadImg">
                                                                </a>
                                                            </figure>
                                                            <div class="product-body">
                                                                <h3 class="product-title">
                                                                    <a
                                                                        href="<?php echo e(url('product/' . $deal->slug)); ?>"><?php echo e($deal->name); ?></a>
                                                                </h3>
                                                                <div class="product-price">
                                                                    ₹<?php echo e(number_format($deal->listed_price, 0)); ?>

                                                                    <?php if($deal->variant_mrp > $deal->listed_price): ?>
                                                                        <span
                                                                            class="price-cut">₹<?php echo e(number_format($deal->variant_mrp, 0)); ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="deal-timer">
                                                                    <span
                                                                        class="<?php echo e($expired ? 'timer-expired' : 'timer-text'); ?>">
                                                                        <?php echo e($expired ? 'Offer Expired' : 'Ends in: ' . $time_display); ?>

                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                    <?php if(count($deals) > 1): ?>
                                                        <a href="<?php echo e(url('deals')); ?>" class="d-block w-100 h-100">
                                                            <div>
                                                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_2->photo, '/')); ?>"
                                                                    alt="banner-img" class="deal-banner-image">
                                                            </div>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <style>
            .row.display-d.flower {
                margin-top: -40px;
            }
        </style>
        <div class="intro-section">
            <div class="container sectionBannerMargin blankBannerWrapper">
                <div class="row display-d flower">
                    <div class="col-md-12 col-lg-6">
                        <div class="banner banner-big banner-overlay comBanner">
                            <a href="<?php echo e($Discount_1->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_1->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="banner banner-overlay comBanner">
                            <a href="<?php echo e($Discount_2->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_2->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <?php $__currentLoopData = [$Discount_3, $Discount_4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="banner banner-small banner-overlay comBanner">
                                <a href="<?php echo e($discount->url); ?>">
                                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount->photo, '/')); ?>"
                                        alt="Banner-4">
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="row display-m">
                    <div class="col-md-12 col-lg-6 col-6">
                        <div class="banner banner-big banner-overlay comBanner">
                            <a href="<?php echo e($Discount_1->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_1->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                        <div class="banner banner-overlay comBanner">
                            <a href="<?php echo e($Discount_2->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_2->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 col-6">
                        <div class="banner banner-overlay comBanner">
                            <a href="<?php echo e($Discount_3); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_2->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                        <div class="banner banner-big banner-overlay comBanner">
                            <a href="<?php echo e(url('discount_products/' . $Discount_1->id)); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Discount_1->photo, '/')); ?>"
                                    alt="Banner">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container sectionBannerMargin d-lg-none imgGalleryWrapper"> <!-- Show only on mobile/tablet -->
                <div class="row imgGalleryHeight">
                    <div class="col-6 d-flex flex-column gap-2 respGalPadding"> <!-- gap between banners -->
                        <div class="banner banner-overlay flex-fill comBanner">
                            <a href="<?php echo e($discount_banner_2->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount_banner_2->photo, '/')); ?>"
                                    class="img-fluid" alt="Banner-2">
                            </a>
                        </div>
                        <div class="banner banner-overlay flex-fill comBanner">
                            <a href="<?php echo e($discount_banner_3->url); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount_banner_3->photo, '/')); ?>"
                                    class="img-fluid" alt="Banner-3">
                            </a>
                        </div>
                        <div class="banner banner-overlay flex-fill comBanner">
                            <a href="<?php echo e(url('discount_products/' . ($discount_banner_5->id ?? 0))); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount_banner_5->photo, '/')); ?>"
                                    class="img-fluid" alt="Banner-5">
                            </a>
                        </div>
                    </div>

                    <!-- Left Column: Bannner 1 and 4 -->
                    <div class="col-6 d-flex flex-column gap-2">
                        <div class="banner banner-overlay flex-fill comBanner">
                            <a href="<?php echo e(url('discount_products/' . ($discount_banner_1->id ?? 0))); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount_banner_1->photo, '/')); ?>"
                                    class="img-fluid" alt="Banner-1">
                            </a>
                        </div>
                        <div class="banner banner-overlay flex-fill comBanner">
                            <a href="<?php echo e(url('discount_products/' . ($discount_banner_4->id ?? 0))); ?>">
                                <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($discount_banner_4->photo, '/')); ?>"
                                    class="img-fluid" alt="Banner-4">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(!empty($Banner_9->photo)): ?>
                <div class="sectionBannerMargin vidBannerWrapper">
                    <?php
                        $ext = strtolower(pathinfo($Banner_9->photo, PATHINFO_EXTENSION));
                        $isVideo = in_array($ext, ['mp4', 'webm']);
                        $fileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_9->photo, '/');

                    ?>
                    <a href="<?php echo e($Banner_9->url); ?>">
                        <?php if($isVideo): ?>
                            <video autoplay muted loop playsinline class="banner-video">
                                <source src="<?php echo e($fileUrl); ?>" type="video/<?php echo e($ext); ?>">
                            </video>
                        <?php else: ?>
                            <div class="video-banner image-banner" style="background-image: url(<?php echo e($fileUrl); ?>);">
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php
                $showCreatorsChoice = !empty($choice_1?->name) || !empty($choice_2?->name);
            ?>

            <?php if($showCreatorsChoice): ?>
                <div class="container sectionBannerMargin">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h2 class="title text-center">Creator's Choice</h2>
                        </div>
                    </div>

                    <div class="row justify-content-center flex-nowrap overflow-auto responsive-slider">
                        <?php for($i = 1; $i <= 2; $i++): ?>
                            <?php $item = ${"choice_$i"} ?? null; ?>

                            <?php if($item && !empty($item->name)): ?>
                                <?php
                                    $cloudflareUrl = env('CLOUDFLARE_R2_PUBLIC_URL');

                                    $videoPath = $cloudflareUrl . ($item->video ?? '');
                                    $photoPath = $cloudflareUrl . ($item->photo ?? '');
                                    $images = json_decode($item->image ?? '[]', true);
                                    $firstImage = asset('/storage/app/public/images/' . ($images[0] ?? 'default.jpg'));
                                    $isVideo = pathinfo($videoPath, PATHINFO_EXTENSION) == 'mp4';

                                ?>

                                <div class="col-6 col-lg-2 choice-slide">
                                    <div class="banner product-banner choice-card comBanner">
                                        <button type="button" class="choice-btn" data-video="<?php echo e(asset($videoPath)); ?>"
                                            data-name="<?php echo e($item->name); ?>" data-listed="<?php echo e($item->listed_price); ?>"
                                            data-mrp="<?php echo e($item->variant_mrp); ?>" data-slug="<?php echo e($item->slug); ?>"
                                            data-description="<?php echo e(htmlentities($item->details)); ?>"
                                            data-images='<?php echo json_encode($images, 15, 512) ?>' onclick="openProductModal(this)"
                                            style="background:none; border:0; padding:0; cursor:pointer;">

                                            <?php if($isVideo): ?>
                                                <video autoplay muted loop class="choice-video">
                                                    <source src="<?php echo e(asset($videoPath)); ?>" type="video/mp4">
                                                </video>
                                            <?php else: ?>
                                                <img src="<?php echo e(asset($photoPath)); ?>" alt="Choice Image">
                                            <?php endif; ?>
                                        </button>


                                        <p class="choice-title">
                                            <?php echo e(strlen($item->name) > 15 ? substr($item->name, 0, 15) . '...' : $item->name); ?>

                                        </p>

                                        <div class="price-display">
                                            <span>₹<?php echo e(number_format($item->listed_price, 0)); ?></span>
                                            <span><del>₹<?php echo e(number_format($item->variant_mrp, 0)); ?></del></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="bags-women-tab" role="tabpanel"
                            aria-labelledby="bags-women-link">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
                                data-toggle="owl"
                                data-owl-options='{
                                        "nav": false,
                                        "dots": true,
                                        "margin": 20,
                                        "loop": false,
                                        "responsive": {
                                            "0": {
                                                "items":2
                                            },
                                            "480": {
                                                "items":2
                                            },
                                            "768": {
                                                "items":3
                                            },
                                            "1200": {
                                                "items":3,
                                                "nav": true,
                                                "dots": false
                                            }
                                        }
                                    }'>
                            </div><!-- End .product-body -->
                        </div><!-- End .product -->
                    </div><!-- End .owl-carousel -->
                </div><!-- .End .tab-pane -->
            </div>

            <div class="tab-pane p-0 fade" id="bags-men-tab" role="tabpanel" aria-labelledby="bags-men-link">
                <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl"
                    data-owl-options='{
                                        "nav": false,
                                        "dots": true,
                                        "margin": 20,
                                        "loop": false,
                                        "responsive": {
                                            "0": {
                                                "items":2
                                            },
                                            "480": {
                                                "items":2
                                            },
                                            "768": {
                                                "items":3
                                            },
                                            "992": {
                                                "items":4
                                            },
                                            "1200": {
                                                "items":4,
                                                "nav": true,
                                                "dots": false
                                            }
                                        }
                                    }'>
                </div><!-- End .owl-carousel -->
            </div><!-- .End .tab-pane -->
        </div><!-- End .tab-content -->
        <script>
            function openProductModal(el) {
                const videoUrl = el.getAttribute('data-video');
                const productName = el.getAttribute('data-name');
                const listedPrice = el.getAttribute('data-listed');
                const mrp = el.getAttribute('data-mrp');
                const description = el.getAttribute('data-description');
                const images = JSON.parse(el.getAttribute('data-images'));
                slug = el.getAttribute('data-slug');

                // Set video
                const video = document.getElementById('popupVideo');
                const source = document.getElementById('popupVideoSource');
                source.src = videoUrl;
                video.load();
                video.play();

                // Set product info
                document.getElementById('popupProductName').innerHTML = '<a href="product/' + slug + '">' + productName +
                    '</a>';
                document.getElementById('popupListedPrice').innerText = listedPrice;
                document.getElementById('popupMRP').innerText = mrp;
                document.getElementById('popupDescription').innerHTML = description;
                document.getElementById('more_cart').innerHTML =
                    '<button id="btn" class="bt btn-primary mr-2" style="background-color: #2E6CB2; border:1px solid #2E6CB2;">' +
                    '<a href="product/' + slug + '" style="color: white; text-decoration: none;">More Info</a>' +
                    '</button>';

                thumbnail = '<div><img src="/storage/app/public/images/' + images[0] + '" width="70px" alt="product">';
                document.getElementById('thumb').innerHTML = thumbnail;
                const container = document.getElementById('popupImageContainer');
                container.innerHTML = '';
                images.forEach((img, index) => {
                    const active = index === 0 ? 'active' : '';
                    container.innerHTML += `
            <div class="carousel-item ${active}">
                <img src="/storage/app/public/images/${img}" class="d-block w-100" alt="Image ${index + 1}">
            </div>
        `;
                });
                var myModal = new bootstrap.Modal(document.getElementById('videoModal'));
                myModal.show();
            }
        </script>

        <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content custom-modal-content">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Video -->
                            <div class="col-md-6">
                                <div class="video-wrapper">
                                    <video id="popupVideo" class="w-100 h-100" controls autoplay muted loop>
                                        <source id="popupVideoSource" src="" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </div>

                            <!-- Text + Image Carousel -->
                            <div class="col-md-6">
                                <div class="content-wrapper">
                                    <div class="d-flex align-items-center">
                                        <!-- Thumb -->
                                        <div id="thumb" style="width: 60px; height: 60px; flex-shrink: 0;"></div>

                                        <!-- Product Info -->
                                        <div class="ms-2 ml-1">
                                            <h5 id="popupProductName" class="text-dark"></h5>
                                            <div>
                                                <h6 class="d-inline text-success mb-0">₹<span
                                                        id="popupListedPrice"></span></h6>
                                                <span class="text-danger ms-2">₹<del id="popupMRP"></del></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bootstrap Carousel -->
                                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner" id="popupImageContainer">
                                            <!-- Images inserted via JS -->
                                        </div>
                                    </div>

                                    <h6>Description</h6>
                                    <div id="popupDescription" class="descriptions"></div>

                                    <div id="more_cart">
                                        <!-- Additional cart elements -->
                                    </div>
                                </div>
                            </div>
                        </div> <!-- row -->
                    </div>
                </div>
            </div>
        </div>

        <div class="section-9 d-none d-md-block">
            <div class="luxe-border">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="heading heading-flex flex-column">
                                <div class="heading-left py-4 text-center">
                                    <img class="luxe-img" src="<?php echo e(asset('public/website/assets/images/luxe-img.png')); ?>"
                                        alt="luxury" />
                                    <h2 class="title d-inline-block mx-4">The Luxe Vault</h2>
                                    <img class="luxe-img" src="<?php echo e(asset('public/website/assets/images/luxe-img.png')); ?>"
                                        alt="luxury" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content sectionMargin luxeBgWrapper">
            <div class="luxe-bg"
                style="background-image:url('<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Luxury_BG->photo, '/')); ?>');">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow tabCntMarTop"
                                id="luxe-carousel">
                                <?php $__currentLoopData = $luxe_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $images = json_decode($lp->image, true);
                                        $productImage =
                                            !empty($images) && isset($images[0])
                                                ? asset('storage/app/public/images/' . $images[0])
                                                : asset('storage/images/default.jpg');
                                        $categories = json_decode($lp->category_ids, true);
                                    ?>
                                    <div class="product product-7">
                                        <figure class="product-media">
                                            <span class="product-label label-new">New</span>
                                            <a href="<?php echo e(url('product/' . $lp->slug)); ?>">
                                                <img src="<?php echo e($productImage); ?>" alt="<?php echo e($lp->name); ?>"
                                                    class="product-image luxeBgWithoutHeadImg">
                                            </a>
                                        </figure>
                                        <div class="product-body">
                                            <div class="product-cat">
                                                <a href="#">
                                                    <?php if(!empty($categories)): ?>
                                                        Category <?php echo e($categories[0]['id']); ?>

                                                    <?php else: ?>
                                                        Unspecified
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                            <h3 class="product-title">
                                                <a href="<?php echo e(url('product/' . $lp->slug)); ?>"><?php echo e($lp->name); ?></a>
                                            </h3>
                                            <div class="product-price">
                                                ₹<?php echo e(number_format($lp->listed_price, 0)); ?>

                                                <span class="price-cut">₹<?php echo e(number_format($lp->variant_mrp, 0)); ?></span>
                                            </div>
                                            <div class="ratings-container">
                                                <div class="ratings">
                                                    <div class="ratings-val" style="width: 20%;"></div>
                                                </div>
                                                <span class="ratings-text">(2 Reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($luxe_products) > 1): ?>
                                    <?php if(!empty($Banner_2)): ?>
                                        <a href="<?php echo e(url('luxury-products')); ?>" class="luxury-banner-link">
                                            <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_2->photo, '/')); ?>"
                                                alt="luxury banner" class="luxury-banner-img">
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!empty($Banner_8->photo)): ?>
            <div class="architect-banner-container sectionMargin">
                <a href="<?php echo e($Banner_8->url); ?>">
                    <img src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_8->photo, '/')); ?>"
                        alt="bannerEight">
                </a>
            </div>
        <?php endif; ?>

        <?php
            $hasFirstTwo = !empty($tips_1?->photo) || !empty($tips_2?->photo);
            $hasOthers = !empty($other_tips) && $other_tips->count() > 0;
        ?>

        <?php if($hasFirstTwo || $hasOthers): ?>
            <div class="container tipsVideoWrapper">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-center title">Tips</h2>
                    </div>
                </div>
                <?php if($hasFirstTwo): ?>
                    <div class="row justify-content-center tips-row">
                        <?php for($i = 1; $i <= 2; $i++): ?>
                            <?php $item = ${"tips_$i"} ?? null; ?>
                            <?php if($item && !empty($item->photo)): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 tips-col">
                                    <div class="banner banner-overlay product-banner text-center comBanner">
                                        <button type="button"
                                            style="background:none; border:0; padding:0; width:100%; cursor:pointer;">

                                            <video width="100%" autoplay muted loop playsinline>
                                                <source
                                                    src="<?php echo e(rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($item->photo, '/')); ?>"
                                                    type="video/mp4">
                                            </video>

                                        </button>

                                        <p class="tip-label"><?php echo e($item->name ?? 'Tips'); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

                <?php if($hasOthers): ?>
                    <div class="tips-carousel-section mt-4">
                        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow tips-carousel-2">
                            <?php $__currentLoopData = $other_tips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="item text-center">
                                    <h5><?php echo e($tip->name); ?></h5>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>


        <?php if(!empty($Banner_10->photo)): ?>
            <div class="sectionBannerMargin">
                <?php
                    $extension = strtolower(pathinfo($Banner_10->photo, PATHINFO_EXTENSION));
                    $isVideo = in_array($extension, ['mp4', 'webm']);
                    $fileUrl = rtrim(env('CLOUDFLARE_R2_PUBLIC_URL'), '/') . '/' . ltrim($Banner_10->photo, '/');

                    $linkUrl =
                        $Banner_10->resource_type == 'category'
                            ? $Banner_10->url
                            : url('banner_products/' . $Banner_10->id);

                ?>
                <a href="<?php echo e($linkUrl); ?>">
                    <?php if($isVideo): ?>
                        <video class="custom-banner-video" autoplay muted loop playsinline>
                            <source src="<?php echo e($fileUrl); ?>" type="video/<?php echo e($extension); ?>">
                            Your browser does not support the video tag.
                        </video>
                    <?php else: ?>
                        <div class="custom-banner-image" style="background-image: url('<?php echo e($fileUrl); ?>');">
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endif; ?>
        <div class="welcome-section">
            <div class="container">
                <h2 class="title text-center welcome-title">Welcome to InteriorChowk</h2>
                <div class="row">
                    <div class="col-lg-6 d-flex align-items-stretch subscribe-div">
                        <div class="cta cta-box">
                            <div class="cta-content">
                                <p class="welcome-description">
                                    Welcome to InteriorChowk Free branding & promotion* InteriorChowk is committed to
                                    supporting your growth.

                                    <br>Join us on this journey, and let's turn your interior design dreams into reality
                                    together!

                                    <br>India's first dedicated marketplace for home interior buyer‘s where a multitude of
                                    sellers, interior designers, architects, contractors, workers and many more..

                                    <br><b>presence in the competitive market.</b>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 banner-overlay-div">
                        <div class="banner banner-overlay comBanner">
                            <iframe width="100%" height="315"
                                src="https://www.youtube-nocookie.com/embed/SpsiQwxOrKw" title="YouTube video player"
                                frameborder="0" loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                            </iframe>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container featured-section" style="display: none;">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="heading heading-flex">
                        <div class="heading-left">
                            <h2 class="title">Featured in</h2>
                        </div>
                        <div class="heading-right">
                            <button class="btn explore-more-btn d-flex align-items-center">
                                Explore More
                                <span class="circle-arrow ms-3">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content tab-content-carousel">
                        <div class="tab-pane p-0 fade show active" id="featured-women-tab" role="tabpanel"
                            aria-labelledby="featured-women-link">
                            <div class="owl-carousel owl-simple featured-carousel">
                                <?php for($i = 0; $i < 10; $i++): ?>
                                    <div class="product product-7 featured-item">
                                        <figure class="product-media">
                                            <a href="product.html">
                                                <img src="<?php echo e(asset('public/website/assets/images/logot.png')); ?>"
                                                    alt="Product image" class="product-image">
                                            </a>
                                        </figure>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="tab-pane p-0 fade" id="featured-men-tab" role="tabpanel"
                            aria-labelledby="featured-men-link">
                            <div class="owl-carousel owl-simple featured-carousel-alt"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-8 customer-carousel-section" style="display:none;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading heading-flex">
                            <div class="heading-left">
                                <h2 class="title">Happy Customers</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="owl-carousel owl-simple happy-customer-carousel">
                            <?php for($i = 1; $i <= 8; $i++): ?>
                                <article class="entry">
                                    <figure class="entry-media">
                                        <button type="button"
                                            style="background:none; border:0; padding:0; cursor:pointer;">

                                            <img src="<?php echo e(asset('public/website/assets/images/team/about-2/member-' . $i . '.jpg')); ?>"
                                                alt="Customer <?php echo e($i); ?>">

                                        </button>

                                    </figure>

                                    <div class="entry-body">
                                        <div class="entry-meta text-dark">
                                            <a href="#">Dec 12, 2025</a>
                                        </div>

                                        <h3 class="entry-title">
                                            <a href="">Aman Bhatnagar</a>
                                        </h3>

                                        <div class="ratings-container d-block">
                                            <div class="ratings">
                                                <div class="ratings-val" style="width: 20%;"></div>
                                            </div>
                                        </div>

                                        <div class="entry-content">
                                            <p>Hey! Remember, InteriorChowk or its team will never ask you for financial
                                                details or...</p>
                                        </div>
                                    </div>
                                </article>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="blog-posts blog-carousel-section" style="display: none;">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h2 class="title">From Our Blog</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="owl-carousel owl-simple blog-carousel">
                            <?php
                                $posts = [
                                    [
                                        'img' => 'post-2.jpg',
                                        'title' => 'Vivamus vestibulum ntulla.',
                                        'desc' =>
                                            'Phasellus hendrerit. Pelletesque aliquet nibh necurna In nisi neque, aliquet vel, dapibus id ...',
                                    ],
                                    [
                                        'img' => 'post-3.jpg',
                                        'title' => 'Praesent placerat risus.',
                                        'desc' =>
                                            'Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc ...',
                                    ],
                                    [
                                        'img' => 'post-4.jpg',
                                        'title' => 'Fusce pellentesque suscipit.',
                                        'desc' =>
                                            'Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero augue.',
                                    ],
                                    [
                                        'img' => 'post-1.jpg',
                                        'title' => 'Sed adipiscing ornare.',
                                        'desc' =>
                                            'Lorem ipsum dolor consectetuer adipiscing elit. Phasellus hendrerit. Pelletesque aliquet nibh ...',
                                    ],
                                ];
                            ?>

                            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="entry">
                                    <figure class="entry-media">
                                        <a href="">
                                            <img src="<?php echo e(asset('public/website/assets/images/demos/demo-13/blog/' . $post['img'])); ?>"
                                                alt="Blog image">
                                        </a>
                                    </figure>

                                    <div class="entry-body">
                                        <div class="entry-meta text-dark">
                                            <a href="#">Dec 12, 2023</a>, 0 Comments
                                        </div>

                                        <h3 class="entry-title">
                                            <a href=""><?php echo e($post['title']); ?></a>
                                        </h3>

                                        <div class="entry-content">
                                            <p><?php echo e($post['desc']); ?></p>
                                            <a href="" class="read-more">Read More</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div>
                            <a href="#" class="btn btn-outline-lightgray btn-more btn-round">
                                <span>View more articles</span><i class="icon-long-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cta pb-lg-3 mb-0 mt-1 lastSectionWrapper">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="cta-heading">
                            <?php echo $seo->content ?? ''; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php $__env->startPush('script'); ?>
        <script src="<?php echo e(asset('public/website/assets/js/home.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.back-end.common_seller_1', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\adminic\resources\views/welcomes_1.blade.php ENDPATH**/ ?>