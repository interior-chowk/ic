<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo e(\App\CPU\translate('Ready to Leave')); ?>?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body"><?php echo e(\App\CPU\translate('Select "Logout" below if you are ready to end your current session')); ?>.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(\App\CPU\translate('Cancel')); ?></button>
                <a class="btn btn--primary" href="<?php echo e(route('seller.auth.logout')); ?>"><?php echo e(\App\CPU\translate('Logout')); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <center>
                            <h2 class="__color-8a8a8a">
                                <i class="tio-shopping-cart-outlined"></i> <?php echo e(\App\CPU\translate('You have new order, Check Please')); ?>.
                            </h2>
                            <hr>
                            <button onclick="check_order()" class="btn btn--primary"><?php echo e(\App\CPU\translate('Ok, let me check')); ?></button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="productFilterModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(\App\CPU\translate('Product_Filter')); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(url()->current()); ?>" method="GET" id="addForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="modalIdPlaceholder" name="order_id" value="">
                <input type="hidden" value="<?php echo e(isset($request_status) ? $request_status : ''); ?>" name="status">
                <div class="modal-body" style="text-align: <?php echo e(Session::get('direction') === "rtl" ? 'right' : 'left'); ?>;">
                    <!-- Other existing fields -->

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Category')); ?></label>
                         <?php ($category=\App\Model\Category::where(['position' => 0])->orderBy('name')->get()); ?>
                        <select class="form-control" name="category_id" id="category-id" onchange="category_fun()">
                            <option disabled selected >select category</option>
                            <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <option value="<?php echo e($categories->id); ?>"><?php echo e($categories->name); ?></option> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Subcategory')); ?></label>
                         <?php ($sub_category=\App\Model\Category::where(['position' => 1])->orderBy('name')->get()); ?>
                        <select class="form-control" name="sub_category_id" id="sub-category-id">
                            <!-- Populate subcategories dynamically based on selected category -->
                            <option disabled selected >select sub-category</option>
                            <?php $__currentLoopData = $sub_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_categories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <option value="<?php echo e($sub_categories->id); ?>"><?php echo e($sub_categories->name); ?></option> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Brand')); ?></label>
                         <?php ($brand=\App\Model\Brand::withCount('brandAllProducts')->with(['brandAllProducts'=> function($query){
                             $query->withCount('order_details');
                              }])->orderBy('brands.name')->get()); ?>
                        <select class="form-control" name="brand_id">
                            <!-- Populate brands dynamically -->
                             <option disabled selected >select brand</option>
                            <?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brands): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brands->id); ?>"><?php echo e($brands->defaultname); ?></option> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Minimum Price')); ?></label>
                        <input type="number" class="form-control" name="min_price" placeholder="<?php echo e(\App\CPU\translate('Minimum Price')); ?>">
                    </div>

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Maximum Price')); ?></label>
                        <input type="number" class="form-control" name="max_price" placeholder="<?php echo e(\App\CPU\translate('Maximum Price')); ?>">
                    </div>

                    <div class="form-group">
                        <label><?php echo e(\App\CPU\translate('Minimum Reviews')); ?></label>
                        <input type="number" class="form-control" name="min_reviews" placeholder="<?php echo e(\App\CPU\translate('Minimum Reviews')); ?>">
                    </div>

                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <?php echo e(\App\CPU\translate('Close')); ?>

                    </button>
                    <button class="btn btn--primary" name="filter" value="filter">
                        <?php echo e(\App\CPU\translate('filter')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--- product filter end -->

<script>
        function category_fun()
        {
                 var category_id =  $("#category-id").val();
               
             var data = {
            category_id: category_id,
            _token: '<?php echo csrf_token(); ?>'
        };
        
        $.ajax({
            url: "<?php echo e(route('seller.product.record')); ?>",
            type: 'POST', 
            data: data,
            success: function(response) {
                
               $('#sub-category-id').html(response);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
     }
</script>
<?php /**PATH D:\xampp\htdocs\adminic\resources\views/layouts/back-end/partials-service/_modals.blade.php ENDPATH**/ ?>