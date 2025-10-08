<style>
img{
    max-width: 75%;
}

    </style>
<footer class="footer footer-2" style="background:black!important;">
    <div class="footer-middle">
        <div class="container">
            <div class="row">
                <div class="col-6 col-sm-3 col-md-3">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="{{ url('test_1') }}">Home</a></li>
                        <li><a href="{{ url('about-us') }}">About Us </a></li>
                        <li><a href="{{ route('seller-chowk') }}">Seller’s Chowk </a></li>
                        <li><a href="{{ route('service-chowk') }}">Service Chowk </a></li>
                        <li><a href="{{ route('seller.auth.seller-login') }}">Seller Login</a></li>
                        <li><a href="{{ url('instant-delivery-products') }}">Instant Delivery</a></li>
                    </ul>
                </div>
                <div class="col-6 col-sm-3 col-md-3">
                    <h6>Customer Support</h6>
                    <ul style="list-style-type: none; padding-left: 0;">
                        <li><a href="{{url('faqs')}}">FAQs</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#bulkEnquiryModal">Bulk Enquiry</a></li>

                    </ul>
                    <!-- <h3 style=" font-size: 1.5rem;">Any <span>QUERY ?</span></h3> -->
                    <button onclick="toggleForm()" class="ftrQryBtn">Ask Your Query <i class="fa fa-chevron-down" aria-hidden="true"></i></button>
                    <form action="{{ route('callback-mail') }}" method="post" id="myForm" style="display: none;">
                        @csrf
                        <input type="hidden" name="status_site" value="0">
                        <ul style="list-style-type: none; padding-left: 0;">
                            <li>
                                <input type="text" name="name" placeholder="Name"
                                    class="form-control mb-2" required
                                    style="height: 30px; width: 50%; font-size: 1.2rem;">
                            </li>
                            <li>
                                <input type="text" name="phone" placeholder="Phone No."
                                    class="form-control mb-2" required
                                    style="height: 30px; width: 50%; font-size: 1.2rem;">
                            </li>
                            <li>
                                <select class="form-control mb-2" name="interested" required
                                    style="height: 30px; width: 50%; font-size: 1rem;">
                                    <option> I am:- </option>
                                    <option> Customer</option>
                                    <option> Seller</option>
                                    <option> Architect</option>
                                    <option> Interior designer</option>
                                    <option> Contractor</option>
                                    <option> Worker</option>
                                </select>
                            </li>
                            <li>
                                <input type="submit" class="btn btn-primary" value="Request Callback"
                                    style="height: 30px; width: 50%; font-size: 1rem;">
                            </li>
                        </ul>
                    </form>
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
                    <h6>Policies & Legal</h6>
                    <ul>
                        <li><a href="{{url('termsAndCondition')}}">Terms & Conditions</a></li>
                        <li><a href="{{url('privacy-policy')}}">Privacy Policy</a></li>
                        <li><a href="{{url('refund-policy')}}">Return & Refund Policy</a></li>
                        <li><a href="{{url('e-wallet-policy')}}">E-Wallet Policy</a></li>
                        <li><a href="{{url('shipping-policy')}}">Shipping Policy</a></li>
                        <li><a href="{{url('secure-payment-policy')}}">Secure Payment Policy</a></li>
                        <li><a href="{{url('instant-delivery-policy')}}">Instant Delivery Policy</a></li>
                    </ul>
                </div>
                 <div class="col-6 col-sm-3 col-md-3">
                    <h6>About InteriorChowk</h6>
                    <ul>
                        <li><a href="{{url('about-us')}}">About Us</a></li>
                        <li><a href="{{url('blog')}}">Blog</a></li>
                        <li><a href="{{ route('careers') }}">Careers</a></li>
                    </ul>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6 col-sm-6 col-md-3">
                  
                     <img style="height: 65px;display: inline-block;" src="{{ asset('website/new/assets/images/white_ic_logo.webp') }}" class="footerImg" alt="white logo" />
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <h6>Stay Connected</h6>
                      
                    <ul style="display: flex; margin-top: 10px;">
                      
                        <li style="width: 30px"><a href="https://www.instagram.com/interiorchowk/"><img src="{{ asset('storage/app/icon/Group-2.webp') }}" alt="Instagram" /></a></li>
                        <li style="width: 30px"><a href="https://www.facebook.com/profile.php?id=61554788270651&mibextid=ZbWKwL"><img src="{{ asset('storage/app/icon/Group-3.webp') }}" alt="Facebook" /></a></li>
                        <li style="width: 30px"><a href="https://www.youtube.com/channel/UCLXmVanINf5oL1gNVHpCmbQ"><img src="{{ asset('storage/app/icon/Group-1.webp') }}" alt="Youtube" /></a></li>
                        <li style="width: 30px"><a href="https://www.linkedin.com/company/interiorchowk/"><img src="{{ asset('storage/app/icon/Group.webp') }}" alt="LinkedIn" /></a></li>
                
                    </ul>
                </div>
                <div class="col-6 col-sm-6 col-md-6">
                    <h6>Download Our App</h6>
                    
                    <ul style="display: flex; margin-top: 10px;">
                        
                         <li style="margin-right: 25px;"><a href="https://play.google.com/store/apps/details?id=com.interiorchowk.app&hl=en_IN&pli=1"><img src="{{ asset('storage/app/icon/GOOGLE.webp') }}" alt="PlayStore" width="130px"/></a></li>
                        <li><a href="https://apps.apple.com/in/app/interiorchowk/id6554003290"><img src="{{ asset('storage/app/icon/APPLE.webp') }}" alt="AppStore" width="130px"/></a></li>
                    </ul>
                </div>
            </div>

           
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->
</footer><!-- End .footer -->
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('website/new/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/main.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.hoverIntent.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.plugin.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/demos/demo-9.js') }}"></script>
<script src="https://use.fontawesome.com/e9084ed560.js"></script>
<script src="{{ asset('website/new/assets/js/jquery.elevateZoom.min.js') }}"></script>
<script src="{{ asset('website/new/assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{asset('asset/js/custom.js')}}"></script> 


@stack('script')
<script src="{{ asset('website/new/assets/js/owl.carousel.min.js') }}"></script>
<!-- Bulk Enquiry Modal -->
<div class="modal fade" id="bulkEnquiryModal" tabindex="-1" aria-labelledby="bulkEnquiryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkEnquiryLabel">Bulk Enquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="border: none; background: none; cursor: pointer; font-size: 1.5rem;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="productDetails" class="form-label">Product Details</label>
                        <input type="text" class="form-control" id="productDetails" name="product_details" required>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Enquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>