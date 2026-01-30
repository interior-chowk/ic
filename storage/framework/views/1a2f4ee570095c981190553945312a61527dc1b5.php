<?php
// Redirect to the Google Play Store
header('Location: https://play.google.com/store/apps/details?id=com.interiorchowk.app');
exit();
?>





<?php $__env->startSection('message'); ?>
    <style>
        .for-margin {
            margin: auto;

            margin-bottom: 10%;
        }

        .for-margin {}

        .page-not-found {
            margin-top: 30px;
            font-weight: 600;
            text-align: center;
        }
    </style>
    <div class="container ">
        <div class="col-md-3"></div>
        <div class="col-md-6 for-margin">
            <div class="for-image">
                <img style="" src="<?php echo e(asset('storage/app/public/png/404.png')); ?>" alt="">
            </div>
            <h2 class="page-not-found"><?php echo e(\App\CPU\translate('Page Not found')); ?></h2>

            <p style="text-align: center;">
                <?php echo e(\App\CPU\translate('We are sorry, the page you requested could not be found')); ?> <br>
                <?php echo e(\App\CPU\translate('Please go back to the homepage')); ?></p>
            <div style="text-align: center;">
                <a class="btn btn--primary" href="<?php echo e(route('seller-home')); ?>"> <?php echo e(\App\CPU\translate('HOME')); ?></a>
            </div>

        </div>
        <div class="col-md-3"></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors::minimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\adminic\resources\views/errors/404.blade.php ENDPATH**/ ?>