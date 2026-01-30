<style>
    img {
        max-width: 75%;
    }

    h3 {
        color: #fff;
        font-size: 1.5rem;
    }
</style>
<footer class="footer footer-2" style="background:black!important;">
    <div class="footer-middle">
        <div class="container">
            <div class="row">
                <div class="col-6 col-sm-3 col-md-3">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
                        <li><a href="<?php echo e(url('about-us')); ?>">About Us </a></li>
                        <li><a href="<?php echo e(route('seller-chowk')); ?>">Seller’s Chowk </a></li>
                        <li><a href="<?php echo e(route('service-chowk')); ?>">Service Chowk </a></li>
                        <li><a href="<?php echo e(route('seller.auth.seller-login')); ?>">Seller Login</a></li>
                        <li><a href="<?php echo e(url('instant-delivery-products')); ?>">Instant Delivery</a></li>
                    </ul>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <h3>Customer Support</h3>
                    <ul style="list-style-type: none; padding-left: 0;">
                        <li><a href="<?php echo e(url('faqs')); ?>">FAQs</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#bulkEnquiryModal">Bulk Enquiry</a>
                        </li>

                    </ul>
                    <!-- <h3 style=" font-size: 1.5rem;">Any <span>QUERY ?</span></h3> -->
                    
                </div>
                <script>
                    function toggleForm() {
                        const form = document.getElementById("myForm");
                        if (form.style.display === "none" || form.style.display === "") {
                            form.style.display = "block";
                        } else {
                            form.style.display = "none";
                        }
                    }
                </script>
                <div class="col-6 col-sm-3 col-md-3">
                    <h3>Policies & Legal</h3>
                    <ul>
                        <li><a href="<?php echo e(url('termsAndCondition')); ?>">Terms & Conditions</a></li>
                        <li><a href="<?php echo e(url('privacy-policy')); ?>">Privacy Policy</a></li>
                        <li><a href="<?php echo e(url('refund-policy')); ?>">Return & Refund Policy</a></li>
                        <li><a href="<?php echo e(url('e-wallet-policy')); ?>">E-Wallet Policy</a></li>
                        <li><a href="<?php echo e(url('shipping-policy')); ?>">Shipping Policy</a></li>
                        <li><a href="<?php echo e(url('secure-payment-policy')); ?>">Secure Payment Policy</a></li>
                        <li><a href="<?php echo e(url('instant-delivery-policy')); ?>">Instant Delivery Policy</a></li>
                    </ul>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <h3>About InteriorChowk</h3>
                    <ul>
                        <li><a href="<?php echo e(url('about-us')); ?>">About Us</a></li>
                        <li><a href="<?php echo e(url('blog')); ?>">Blog</a></li>
                        <li><a href="<?php echo e(route('careers')); ?>">Careers</a></li>
                        <li><a href="<?php echo e(url('contact-us')); ?>">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6 col-sm-6 col-md-3">

                    <img style="height: 65px;display: inline-block;"
                        src="<?php echo e(asset('public/website/new/assets/images/white_ic_logo.webp')); ?>" class="footerImg"
                        alt="white logo" />
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <h3>Stay Connected</h3>

                    <ul style="display: flex; margin-top: 10px;">

                        <li style="width: 30px"><a href="https://www.instagram.com/interiorchowk/"><img
                                    src="<?php echo e(asset('storage/app/icon/Group-2.webp')); ?>" alt="Instagram" /></a></li>
                        <li style="width: 30px"><a
                                href="https://www.facebook.com/profile.php?id=61554788270651&mibextid=ZbWKwL"><img
                                    src="<?php echo e(asset('storage/app/icon/Group-3.webp')); ?>" alt="Facebook" /></a></li>
                        <li style="width: 30px"><a href="https://www.youtube.com/channel/UCLXmVanINf5oL1gNVHpCmbQ"><img
                                    src="<?php echo e(asset('storage/app/icon/Group-1.webp')); ?>" alt="Youtube" /></a></li>
                        <li style="width: 30px"><a href="https://www.linkedin.com/company/interiorchowk/"><img
                                    src="<?php echo e(asset('storage/app/icon/Group.webp')); ?>" alt="LinkedIn" /></a></li>

                    </ul>
                </div>
                
            </div>

        </div><!-- End .container -->
    </div><!-- End .footer-middle -->
</footer><!-- End .footer -->
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/main.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.hoverIntent.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.waypoints.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/bootstrap-input-spinner.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.plugin.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.magnific-popup.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.countdown.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/demos/demo-9.js')); ?>"></script>
<script src="https://use.fontawesome.com/e9084ed560.js"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.elevateZoom.min.js')); ?>"></script>
<script src="<?php echo e(asset('public/website/new/assets/js/jquery.magnific-popup.min.js')); ?>"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="<?php echo e(asset('public/asset/js/custom.js')); ?>"></script>


<?php echo $__env->yieldPushContent('script'); ?>
<script src="<?php echo e(asset('public/website/new/assets/js/owl.carousel.min.js')); ?>"></script>

<div class="modal fade" id="bulkEnquiryModal" tabindex="-1" aria-labelledby="bulkEnquiryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('bulk.enquiry.submit')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="user_id" value="<?php echo e(auth()->id() ?? 0); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkEnquiryLabel">Bulk Enquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="border: none; background: none; cursor: pointer; font-size: 1.5rem;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="productDetails" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productDetails" name="product_name"
                            list="productList" placeholder="Search product..." required>
                        <datalist id="productList">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->name); ?>"></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                    </div>

                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                            value="1" required>
                    </div>

                    
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remark" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Enquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\adminic\resources\views/layouts/back-end/includes-seller/footer_1.blade.php ENDPATH**/ ?>