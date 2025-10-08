<div class="page-wrapper">

  <center>
    <div class="header_top">
      <div class="container">
        <div class="app-offer-banner">
          <span class="offer-text">
            Flat 10% off on first app order | Use Code: <strong>APPNEW10</strong>
          </span>
         <a href="https://reviewcard.in/interior-chowk/"> <img class="app-banner-img" src="https://interiorchowk.com/public/website/assets/images/interiorchowk-app-download.png" alt="Download App"></a>
        </div>
      </div><!-- End .header-top -->
    </div>
  </center>

  <header class="header header-intro-clearance">
    <div class="sticky-header ">
      <div class="header-middle" style="background-color:white;">
        <div class="container">
          <div class="header-left">
            <button class="mobile-menu-toggler">
              <span class="sr-only">Toggle mobile menu</span>
              <i class="fa-solid fa-bars"></i>
            </button>

            <a href="{{ url('/') }}" class="logo">
              <img src="{{ asset('website/assets/images/logoic.png') }}" alt="Interiorchowk Logo" width="100">
            </a>
            <div class="wishlist del_add">

              <a href="javascript:void(0)"
                title="Choose Delivery Pincode"
                style="border-left:1px solid #000; padding-left:15px; height:36px;"
                data-toggle="modal"
                data-target="#pincodeModal">
                <div class="icon">
                  <span style="font-size:13px;">Deliver to</span>
                </div>
                <!-- give this span an ID so we can update it dynamically -->
                <span id="pincodeDisplay" style="font-size:13px; font-weight:600; margin-bottom:8px;"><i class="fa fa-chevron-down" aria-hidden="true"></i>
                </span>
              </a>
            </div><!-- End .compare-dropdown -->
          </div><!-- End .header-left -->

          <div class="header-center" style="height: 56px;">

            <!-- Search Button -->
            <!-- HTML -->
            <div style="display: inline-block; position: relative; margin: 20px;">
              <button type="button" data-bs-toggle="modal" data-bs-target="#searchModal" class="custom-search-btn">
                <i class="fa-solid fa-magnifying-glass" style="color: #A3A3A3"></i>
                <p style="color: #A3A3A3">Search for <span id="rotating-product" style="display: inline-block">product</span></p>
              </button>
            </div>



            <!-- Search Modal -->
            <div class="modal fade custom-search-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
              <div class="modal-dialog" style="position: fixed; top: 0; left: 50%; transform: translateX(-50%); width: 90%; max-width: 630px;">
                <div class="modal-content" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">

                                 <!-- Modal Body -->
                  <div class="modal-body">
                    <!-- Search Input -->
                    <div class="custom-search-input">

                      {{-- <input type="text" id="searchInput" placeholder="Search..." oninput="hello()"> --}}
                         <input type="text" id="searchInput" placeholder="Search..." oninput="hello()">

                          <div id="list_suggestion" class="suggestion-box"></div>

                      <i class="fa-solid fa-magnifying-glass"></i>
                    </div> <!-- End of Search Input -->
                    <div id="list_suggestion"></div>
                    <h6 style="margin-top: 15px;" class="h6">Popular Choices</h6>
                    <div class="popular-searches">
                    </div> <!-- End of Popular Searches -->
                  </div> <!-- End of Modal Body -->
                </div> <!-- End of Modal Content -->
              </div> <!-- End of Modal Dialog -->
            </div> <!-- End of Modal -->



            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


            <!-- End .header-search -->
          </div>

          <div class="header-right">
            @auth
            @php
            $user = auth()->user();

            @endphp
            <div class="dropdown compare-dropdown">
              <a href="{{ url('my-account') }}" class="dropdown-toggle">
                <div class="icon">
                  @if($user->gender === 'Male')
                  <img style="width:25px; "
                    src="{{ asset('/public/website/assets/images/male.webp') }}"
                    class="rounded-circle" alt="Profile Picture">
                  @elseif($user->gender === 'Female')
                  <img style="width:25px; "
                    src="{{ asset('/public/website/assets/images/female.webp') }}"
                    class="rounded-circle" alt="Profile Picture">
                  @else
                  <img style="width:25px; "
                    src="{{ asset('storage/service-provider/' . ($user->image ?? 'def.png')) }}"
                    class="rounded-circle" alt="Profile Picture">
                  @endif
                </div>
                @if($user->gender == null)
                <p>{{('My Account') }}</p>
                @endif
                <p>{{ $user->f_name }}</p>
              </a>
            </div>
            @else
            <div class="dropdown compare-dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#loginModal">
                <div class="icon">
                  <img src="{{ asset('website/assets/images/login.png') }}" alt="Login Icon" width="25">
                </div>
                <p>Login</p>
              </a>
            </div>
            @endauth

            @php
            $id = auth()->id();
            @endphp

            @php
            $wishlistCount = 0;
            if (auth()->check()) {
            $wishlistCount = DB::table('wishlists')
            ->where('customer_id', auth()->id())
            ->count();
            }
            @endphp

            <div class="wishlist">
              @php
              $userId = $id ?? auth()->id();
              @endphp

              @if ($userId)
              <a href="{{ url('wishlist') }}" title="Wishlist">
                @else
                <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#loginModal">
                  @endif
                  <div class="icon">
                    <img src="{{ asset('website/assets/images/wishlist.png') }}" alt="Wishlist Icon" width="25">
                    @if($wishlistCount > 0)
                    <span class="wishlist-count badge">{{ $wishlistCount ?? 0 }}</span>
                    @endif
                  </div>
                  <p>Wishlist</p>
                </a>
            </div>


            @php

            $cart = DB::table('new_cart')
            ->join('sku_product_new', function($join) {
            $join->on('new_cart.variation', '=', 'sku_product_new.id');
            })
            ->leftJoin('products', 'sku_product_new.product_id', '=', 'products.id')
            ->select(
            'new_cart.*',
            'new_cart.id as cart_id',
            'new_cart.quantity as cart_qty',
            'sku_product_new.product_id as product_ids',
            'sku_product_new.*',
            'products.name'
            )
            ->where('new_cart.user_id', $id)
            ->where('new_cart.type', 0)
            ->get();

            $cartCount = count($cart);
            $cartTotal = 0;
            @endphp

            <div class="dropdown cart-dropdown">
              @if ($userId)
              <a onclick="window.location.href='{{ url('cart') }}'" class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                @else
                <a href="#" class="dropdown-toggle" data-toggle="modal" data-target="#loginModal">
                  @endif

                  <div class="icon">
                    <img src="{{ asset('website/assets/images/cart.png') }}" alt="Cart" width="25">
                    @if($cartCount > 0)
                    <span class="cart-count badge">{{ $cartCount }}</span>
                    @endif
                  </div>
                  <p>Cart</p>
                </a>
                @if($userId)
                <div class="dropdown-menu dropdown-menu-right">
                  <div class="dropdown-cart-products" style="height: 190px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;"">
            @forelse ($cart as $cartItem)
                @php
                    $price = $cartItem->listed_price ?? 0;
                    $qty = $cartItem->cart_qty; // Adjust if you have quantity support
                    $cartTotal += $price * $qty;

                    $images = json_decode($cartItem->image, true);
                @endphp

                <div class=" product">
                    <div class="product-cart-details">
                      <h4 class="product-title">
                        <a href="">
                          {{ \Illuminate\Support\Str::limit($cartItem->name, 40) }}
                        </a>
                      </h4>
                      <span class="cart-product-info">
                        <span class="cart-product-qty">{{ $qty }}</span>
                        x ₹{{ number_format($price, 2) }}
                      </span>
                    </div>

                    <figure class="product-image-container">
                      <a href="" class="product-image">
                        <img src="{{ asset('storage/images/' . $images[0]) }}" alt="{{ $cartItem->name }}">
                      </a>
                    </figure>

                    {{-- <a href="javascript:void(0);"
                      class="btn-remove delete-cart-items"
                      title="Remove Product"
                      data-cart-id="{{ $cartItem->cart_id }}">
                      <i class="icon-close"></i>
                    </a> --}}

                  </div>
                  @empty
                  <p class="text-center p-3">Your cart is empty</p>
                  @endforelse
                </div>

                @if($cartCount > 0)
                <div class="dropdown-cart-total">
                  <span>Total</span>
                  <span class="cart-total-price">₹{{ number_format($cartTotal, 2) }}</span>
                </div>

                <div class="dropdown-cart-action">
                  <a href="{{ url('cart') }}" class="btn btn-primary">View Cart</a>
                  <a href="{{ url('checkout') }}" class="btn btn-outline-primary-2">
                    <span>Checkout</span>
                    <i class="fa-solid fa-angle-right"></i>
                  </a>
                </div>
                @endif
            </div>
          </div>
          @endif
        </div><!-- End .header-right -->
      </div><!-- End .container -->
    </div><!-- End .header-middle -->

    <div class="search_mobile">
      <!-- HTML -->
      <div style="position: relative; margin: 10px 15px 10px 15px;">
        <button type="button" data-bs-toggle="modal" data-bs-target="#searchModal2" class="custom-search-btn">
          <i class="fa-solid fa-magnifying-glass" style="color: #a3a3a3;"></i>
          <p style="color: #A3A3A3">Search for <span id="rotating-product" style="display: inline-block">product</span></p>
        </button>
      </div>
      <!-- Search Modal -->
      <div class="modal fade custom-search-modal" id="searchModal2" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="position: fixed; top: 0; left: 50%; transform: translateX(-50%); width: 90%; max-width: 630px;">
          <div class="modal-content" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">

            <!-- Modal Body -->
            <div class="modal-body">
              <!-- Search Input -->
              <div class="custom-search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Search..." oninput="hello()">

              </div> <!-- End of Search Input -->
              <div id="list_suggestion"></div>

              <h6 style="margin-top: 15px;" class="h6">Popular Choices</h6>
              <div class="popular-searches">
              </div> <!-- End of Popular Searches -->
            </div> <!-- End of Modal Body -->
          </div> <!-- End of Modal Content -->
        </div> <!-- End of Modal Dialog -->
      </div> <!-- End of Modal -->
    </div>




<style>
.suggestion-box {
    position: absolute;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    max-height: 250px;
    overflow-y: auto;
    width: 300px;
    display: none;
    z-index: 1000;
    margin-top:300px;
}
.suggestion-box ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.suggestion-box li {
    padding: 10px;
}
.suggestion-box li a {
    display: block;
    text-decoration: none;
    color: #000;
}
.suggestion-box li a:hover {
    background: #f0f0f0;
}
</style>

<script>
function hello() {
    let x = document.getElementById('searchInput').value.trim();
    let list = document.getElementById('list_suggestion');

    if (x.length === 0) {
        list.style.display = 'none';
        list.innerHTML = "";
        return;
    }

    $.ajax({
        url: '{{ route("web_suggestion") }}',
        type: 'POST',
        data: {
            search: x,
            _token: '{{ csrf_token() }}'
        },
        success: function(result) {
            let suggestions = result.suggestion || [];
            list.innerHTML = "";

            if (suggestions.length > 0) {
                let html = "<ul>";
                suggestions.forEach(suggestions => {
                  
                          html += `<li><a href="/category/${suggestions.tag}">${suggestions.tag}</a></li>`;
                      
                });
                html += "</ul>";
                list.innerHTML = html;
                list.style.display = "block";
            } else {
                list.style.display = "none";
            }
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

$('#searchInput').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault();
        let query = $(this).val().trim();
        if (query.length === 0) return;

        $.ajax({
            url: '{{ route("web_search") }}', // New route for Enter search
            type: 'POST',
            data: {
                search: query,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
              
                   window.location.href = `/category/${encodeURIComponent(result.slug)}`;
                
            },
            error: function(xhr) {
                console.error("Error on Enter search:", xhr.responseText);
            }
        });
    }
});


</script>



 <script>
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif


        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif


        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif


        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
    </script>

<script>

// function hello() {
//     var x = document.getElementById('searchInput').value.trim();

//     // Hide popular searches and heading if any input
//     if (x.length > 0) {
//         document.getElementsByClassName('popular-searches')[0].style.display = 'none';
//         document.getElementsByClassName('h6')[0].style.display = 'none';
//     } else {
//         document.getElementsByClassName('popular-searches')[0].style.display = 'block';
//         document.getElementsByClassName('h6')[0].style.display = 'block';
//         document.getElementById('list_suggestion').innerHTML = '';
//         return; // Stop AJAX if input is empty
//     }

//     $.ajax({
//         url: '{{ route("web_suggestion") }}',
//         type: 'POST',
//         data: {
//             search: x,
//             _token: '{{ csrf_token() }}'
//         },
//         success: function(result) {
//             let suggestions = result.suggestion || [];
//                console.log(suggestions);
//             if (suggestions.length > 0) {
//                 let html = '<ul>';
//                 suggestions.forEach(item => {
//                   html += '<li><a href="search_product?name=' + (item.tag) + '" style="color: black;">' + item.tag + '</a></li>';
//                 });
//                 html += '</ul>';
//                 document.getElementById('list_suggestion').innerHTML = html;
//             } else {
//                 document.getElementById('list_suggestion').innerHTML = '<p>No suggestions found</p>';
//             }
//         },
//         error: function(xhr) {
//             console.error("Error:", xhr.responseText);
//         }
//     });
// }


$.ajax({
    url: '{{ route("popular_choice") }}',
    type: 'GET',
    success: function(result) {
        console.log(result.pop); // Check karne ke liye

        let htmls = '';
        Object.entries(result.pop).forEach(([slug, name]) => {
    htmls += `<button class="category-btn" data-slug="${slug}" type="button">${name} <span>→</span></button>`;
});

        // Set content inside first element with class "popular-searches"
        document.getElementsByClassName('popular-searches')[0].innerHTML = htmls;
    },
    error: function(xhr) {
        console.error("Error:", xhr.responseText);
    }
});

$(document).on('click', '.category-btn', function() {
    const slug = $(this).data('slug');
    console.log(slug);
     $.ajax({
        url: `/category/${slug}/products`, // Adjust route as needed
        type: 'GET',
        success: function(response) {
            // console.log('Products for category:', slug, response);
            if(response.status == true){
               window.location.href = `/category/${slug}`;
            }
          
        },
        error: function(err) {
            console.error('Error fetching products:', err);
        }
    });
    
})

</script>
    <div class="header-bottom sticky-header " style="--tw-shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
   --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
   box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow); border-top:1px solid #c9c9c9;">
      <div class="container">
        @php
        $cat = DB::table('categories')
        ->whereIn('name', [
        'Decor', 'Furnishing', 'Garden', 'Furniture', 'Kitchen',
        'Electronics', 'Electricals', 'Hardware & Sanitary'
        ])
        ->orderByRaw("FIELD(name, 'Decor', 'Furnishing', 'Garden', 'Furniture', 'Kitchen', 'Electronics', 'Electricals', 'Hardware & Sanitary')")
        ->get();

        $subCategories = DB::table('categories as c')
        ->whereIn('c.parent_id', $cat->pluck('id'))
        ->where('c.sub_parent_id', 0)
        ->orderBy('c.priority', 'asc')
        ->get()
        ->groupBy('parent_id');

        $subCategoryIds = $subCategories->flatten()->pluck('id');

        $sub_sub_categories = DB::table('categories')
        ->whereIn('sub_parent_id', $subCategoryIds)
        ->get()
        ->groupBy('sub_parent_id');
        @endphp

        <div class="header-center">
          <nav class="main-nav">
            <ul class="menu sf-js-enabled">
              @foreach($cat as $ca)
              <li>
                <a href="{{ url('category/' .$ca->slug) }}" style="white-space: nowrap; margin-left: 10px; margin-right: 10px;">{{ $ca->name }}</a>
                @if($subCategories->has($ca->id))
                <div class="megamenu megamenu-md">
                  <div class="row no-gutters">
                    <div class="col-md-12">
                      <div class="menu-col">
                        <div class="row">
                          <!-- Left side: first 5 subcategories -->
                          <div class="col-md-9">
                            <div class="row row-cols-lg-5 row-cols-md-3 row-cols-2">
                              @foreach($subCategories[$ca->id]->slice(0, 5) as $subCat)
                              <div class="col mb-3">
                                <a href="{{ url('category/' . $subCat->slug) }}" class="text-wrap">
                                  <div class="menu-title">{{ $subCat->name }}</div>
                                </a>
                                @if($sub_sub_categories->has($subCat->id))
                                <ul class="list-unstyled">
                                  @foreach($sub_sub_categories[$subCat->id] as $subsubcat)
                                  <li class="py-1">
                                    <a href="{{ url('category/' . $subsubcat->slug) }}" class="text-nowrap">{{ $subsubcat->name }}</a>
                                  </li>
                                  @endforeach
                                </ul>
                                @endif
                              </div>
                              @endforeach
                            </div>
                          </div>

                          <!-- Right side: subcategories after the first 5 -->
                          <div class="col-md-3">
                            <div class="row">
                              @foreach($subCategories[$ca->id]->slice(5) as $subCat)
                              <div class="col-md-12">
                                <a href="{{ url('category/' . $subCat->slug) }}" class="text-nowrap">
                                  <div class="menu-title">{{ $subCat->name }}</div>
                                </a>
                              </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
              </li>
              @endforeach
            </ul>
          </nav>
        </div>


        <div class="header-right">
          <i class="fa-solid fa-lightbulb"></i>
          <p>Explore Deals<span class="highlight">&nbsp;Up to 80% Off</span></p>
        </div>
      </div><!-- End .container -->

    </div><!-- End .header-bottom -->
</div>
</header><!-- End .header -->


<!-- Mobile Menu -->
<div class="mobile-menu-overlay"></div><!-- End .mobile-menu-overlay -->

<div class="mobile-menu-container">
  <div class="mobile-menu-wrapper">
    <span class="mobile-menu-close"><i class="fa-solid fa-xmark"></i></span>

    <form action="#" method="get" class="mobile-search">
      <!--  -->
      <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
      <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="row">
      <!-- Left Column: Category Tabs -->
      <div class="col-4">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          @foreach($cat as $index => $ca)
          <button class="nav-link {{ $loop->first ? 'active' : '' }}"
            id="v-pills-tab-{{ $ca->id }}"
            data-toggle="pill"
            data-target="#v-pills-{{ $ca->id }}"
            type="button"
            role="tab"
            aria-controls="v-pills-{{ $ca->id }}"
            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
            <img src="{{ asset('storage/category/' . $ca->icon) }}" alt="{{ $ca->name }}" />
            <span>{{ $ca->name }}</span>
          </button>
          @endforeach
        </div>
      </div>

      <!-- Right Column: Tab Content for Each Category -->
      <div class="col-8">
        <div class="tab-content" id="v-pills-tabContent">
          @foreach($cat as $ca)
          <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
            id="v-pills-{{ $ca->id }}"
            role="tabpanel"
            aria-labelledby="v-pills-tab-{{ $ca->id }}">

            <nav class="mobile-nav pr-4">
              <ul class="mobile-menu">
                @if($subCategories->has($ca->id))
                @foreach($subCategories[$ca->id] as $subCat)
                <li class="active">
                  <a href="{{ url('category/' .$subCat->slug) }}">{{ $subCat->name }}</a>
                  @if($sub_sub_categories->has($subCat->id))
                  <ul>
                    @foreach($sub_sub_categories[$subCat->id] as $subSubCat)
                    <li>
                      <a href="{{ url('category/' .$subSubCat->slug) }}">{{ $subSubCat->name }}</a>
                    </li>
                    @endforeach
                  </ul>
                  @endif
                </li>
                @endforeach
                @else
                <li>
                  <a href="#">No subcategories available.</a>
                </li>
                @endif
              </ul>
            </nav><!-- End .mobile-nav -->
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="social-icons">
      <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="fa-brands fa-facebook"></i></a>
      <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="fa-brands fa-twitter"></i></a>
      <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="fa-brands fa-square-instagram"></i></a>
      <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="fa-brands fa-youtube"></i></a>
    </div><!-- End .social-icons -->
  </div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->

<!-- Sign in / Register Modal -->
<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
        </button>

        <div class="form-box">
          <div class="form-tab">
            <ul class="nav nav-pills nav-fill nav-border-anim" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
              </li>
            </ul>
            <div class="tab-content" id="tab-content-5">
              <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                <form action="#">
                  <div class="form-group">
                    <label for="singin-email">Username or email address *</label>
                    <input type="text" class="form-control" id="singin-email" name="singin-email" required>
                  </div><!-- End .form-group -->

                  <div class="form-group">
                    <label for="singin-password">Password *</label>
                    <input type="password" class="form-control" id="singin-password" name="singin-password" required>
                  </div><!-- End .form-group -->

                  <div class="form-footer">
                    <button type="submit" class="btn btn-outline-primary-2">
                      <span>LOG IN</span>
                      <i class="fa-solid fa-angle-right"></i>
                    </button>

                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="signin-remember">
                      <label class="custom-control-label" for="signin-remember">Remember Me</label>
                    </div><!-- End .custom-checkbox -->

                    <a href="#" class="forgot-link">Forgot Your Password?</a>
                  </div><!-- End .form-footer -->
                </form>
                <div class="form-choice">
                  <p class="text-center">or sign in with</p>
                  <div class="row">
                    <div class="col-sm-6">
                      <a href="#" class="btn btn-login btn-g">
                        <i class="fa-brands fa-google"></i>
                        Login With Google
                      </a>
                    </div><!-- End .col-6 -->
                    <div class="col-sm-6">
                      <a href="#" class="btn btn-login btn-f">
                        <i class="fa-brands fa-facebook"></i>
                        Login With Facebook
                      </a>
                    </div><!-- End .col-6 -->
                  </div><!-- End .row -->
                </div><!-- End .form-choice -->
              </div><!-- .End .tab-pane -->
              <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                <form action="#">
                  <div class="form-group">
                    <label for="register-email">Your email address *</label>
                    <input type="email" class="form-control" id="register-email" name="register-email" required>
                  </div><!-- End .form-group -->

                  <div class="form-group">
                    <label for="register-password">Password *</label>
                    <input type="password" class="form-control" id="register-password" name="register-password" required>
                  </div><!-- End .form-group -->

                  <div class="form-footer">
                    <button type="submit" class="btn btn-outline-primary-2">
                      <span>SIGN UP</span>
                      <i class="fa-solid fa-angle-right"></i>
                    </button>

                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="register-policy" required>
                      <label class="custom-control-label" for="register-policy" style="font-size: 0px;">I agree to the <a href="#">privacy policy</a> *</label>
                    </div><!-- End .custom-checkbox -->
                  </div><!-- End .form-footer -->
                </form>
                <div class="form-choice">
                  <p class="text-center">or sign in with</p>
                  <div class="row">
                    <div class="col-sm-6">
                      <a href="#" class="btn btn-login btn-g">
                        <i class="fa-brands fa-google"></i>
                        Login With Google
                      </a>
                    </div><!-- End .col-6 -->
                    <div class="col-sm-6">
                      <a href="#" class="btn btn-login  btn-f">
                        <i class="fa-brands fa-facebook"></i>
                        Login With Facebook
                      </a>
                    </div><!-- End .col-6 -->
                  </div><!-- End .row -->
                </div><!-- End .form-choice -->
              </div><!-- .End .tab-pane -->
            </div><!-- End .tab-content -->
          </div><!-- End .form-tab -->
        </div><!-- End .form-box -->
      </div><!-- End .modal-body -->
    </div><!-- End .modal-content -->
  </div><!-- End .modal-dialog -->
</div><!-- End .modal -->

<!-- Modal -->
<!-- Modal -->
<div class="modal loginMdlRespo fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="width: 72%;">
      <div class="modal-body">
        <div class="container" style="width: 100%; padding-right: 15px; padding-left: 15px;">
          <div class="row">
            <!-- Left Image Section -->
            <div class="col-12 col-md-7 d-none d-md-block">
              <div class="bg-login text-center">
                <h1>INTERIOR <span>CHOWK</span></h1>
                <img class="asset-1" src="{{ asset('website/new/assets/images/Asset-1.png') }}" alt="interiorchowk login" style="width: 60%" />
                <img class="asset-2" src="{{ asset('website/new/assets/images/Asset-2.png') }}" alt="interiorchowk logo" style="width: 60%" />
              </div>
            </div>

            <!-- Right Login/OTP Section -->
            <div class="col-12 col-md-5">
              <div class="text-login mt-4">

                <!-- Login Section -->
                <div id="loginSection" class="slide-section active">
                  <h5 >LogIn / SignUp</h5>

                  <div class="form-group ">
                    <input type="text" class="form-control" id="phone" placeholder="Enter Phone Number" maxlength="10">
                    <span id="phoneError" class="text-danger"></span>
                  </div>

                  <h6 >Referral Code (Optional)</h6>
                  <div class="form-group ">
                    <input type="text" class="form-control" id="referral" placeholder="Enter Referral Code">
                    <span id="referralError" class="text-danger"></span>
                  </div>

                  <div class="form-group form-check mb-1">
                    <input type="checkbox" class="form-check-input agreeCheck" id="termsCheck">
                    <label class="form-check-label ml-2 agreeFont" for="termsCheck">
                      I agree to
                      <a href="{{url('termsAndCondition')}}" style="color: #E46725;">Terms & Conditions</a> and
                      <a href="{{url('privacy-policy')}}" style="color: #E46725;">Privacy Policy</a>
                    </label>
                    <span id="termsCheckError" class="text-danger"></span>
                  </div>

                  <button type="button" id="sendOtpBtnNew" class="btn btn-primary">
                    Send OTP
                  </button>
                </div>

                <!-- OTP Section -->
                <div id="otpSection" class="slide-section">
                  <h4>Verify OTP</h4>
                  <div class="row mt-3 otpBoxAlign">
                    <div class="col-md-12">
                      <h6>Enter 4-digit OTP </h6>
                    </div>
                    @for ($i = 0; $i < 4; $i++)
                      <div class="col-3">
                        <input type="text" maxlength="1" class="form-control mb-1 otp-input" data-index="{{ $i }}">
                      </div>
                    @endfor
                     <div class="col-12">
                      <span id="otpError" class="text-danger"></span>
                    </div>
                  </div>
                  <div class="resbtnOtp"><span id="otpTimer">Resend OTP in: 00:60</span></div>
                  <button type="button" id="verifyOtpBtnNew" class="btn btn-info">Verify O.T.P.</button>
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

<!-- Modal -->
<div class="modal fade" id="pincodeModal" tabindex="-1" aria-labelledby="pincodeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body p-5">
        <h6 class="mb-1">Enter your area pincode</h6>

        <div class="form-inline text-login">
          <div class="form-group mr-3" style="width: calc(100% - 95px); position:relative;">
            <input id="pincodeInput"
              type="text"
              class="form-control w-100 mb-1"
              placeholder="Enter 6‑digit pincode"
              maxlength="6"
              style="padding-right:105px;">
            <div style="position:absolute; right:0; top:0;">

              <button type="button"
                class="btn"
                onclick="applyPincode()"
                style="min-width:auto;background-color:#e46725; color:#fff;">
                Apply
              </button>
            </div>
          </div>

          <button type="button"
            class="btn btn-primary"
            onclick="getLocation()">
            <i class="fa-solid fa-location-arrow ml-0"></i>
            Locate
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// ✅ Send OTP Function
function sendOtp() {
    const resendBtn = document.getElementById('resendOtpBtn');
    if (resendBtn) resendBtn.style.display = 'none';

    const phone = document.getElementById('phone')?.value.trim();
    const referral = document.getElementById('referral')?.value.trim();
    const termsCheck = document.getElementById('termsCheck')?.checked;

    // Clear old errors
    $('#phoneError').text('');
    $('#referralError').text('');
    $('#termsCheckError').text('');

    $.ajax({
        url: "{{ route('sendOtpweb') }}",
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            phone: phone,
            referral: referral,
            termsCheck: termsCheck ? 1 : 0
        },
        success: function (response) {
            if (response.status) {
              console.log(response.status);
                document.querySelectorAll(".slide-section").forEach(s => s.classList.remove("active"));
                document.getElementById("otpSection").classList.add("active");

                startOTPTimer();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                if (errors.phone) {
                    $('#phoneError').text(errors.phone[0]);
                }
                if (errors.referral) {
                    $('#referralError').text(errors.referral[0]);
                }
                 if (errors.termsCheck) $('#termsCheckError').text(errors.termsCheck[0]);

    
                document.querySelectorAll(".slide-section").forEach(s => s.classList.remove("active"));
                document.getElementById("loginSection").classList.add("active");
            } else {
                alert("Something went wrong.");
            }
        }
    });
}

// Attach click event
document.getElementById("sendOtpBtnNew").addEventListener("click", function () {
    sendOtp();
});

$(document).on('click', '#resendOtpBtn', function () {
    sendOtp();
});



// ✅ Start OTP Countdown Timer
function startOTPTimer(durationInSeconds = 60) {
  const display = document.getElementById('otpTimer');
  let timer = durationInSeconds;
  document.getElementById('verifyOtpBtnNew').style.display = 'block';
  const interval = setInterval(function () {
    let minutes = Math.floor(timer / 60);
    let seconds = timer % 60;

    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    display.textContent = 'Resend OTP in: ' + minutes + ':' + seconds;

    if (--timer < 0) {
      clearInterval(interval);
      document.getElementById('verifyOtpBtnNew').style.display = 'none';
      // Show Send OTP button again
      display.innerHTML = `
        <button type="button" id="resendOtpBtn" class="btn btn-primary">Resend OTP</button>
      `;
    }
  }, 1000);
}

// ✅ OTP Auto Tab Input
$(document).on('keyup', '.otp-input', function () {
  let index = parseInt($(this).data('index'));
  if ($(this).val().length === 1 && index < 3) {
    $('.otp-input').eq(index + 1).focus();
  }
});

// ✅ Verify OTP Submit
$(document).on('click', '#verifyOtpBtnNew', function () {
  let otp = '';
  $('.otp-input').each(function () {
    otp += $(this).val().trim();
  });

  if (otp.length !== 4) {
        $('#otpError').text("Please enter all 4 digits of the OTP.");
        return;
    }

  const phone = $('#phone').val();
  if (!phone) {
    //alert("Phone number is missing.");
    return;
  }

  $.ajax({
    url: "{{ url('/logins') }}",
    type: 'POST',
    data: {
      _token: '{{ csrf_token() }}',
      otp: otp,
      phone: phone
    },
    success: function (res) {
      if (res.status) {
       toastr.success(res.message);
         setTimeout(function () {
            location.reload();
        }, 1000);
      } else {
       toastr.error(res.message);
      }
    },
    error: function (xhr) {
      console.error("XHR Response:", xhr.responseText);

    }
  });
});
$(document).ready(function () {
    $('#loginModal').on('hidden.bs.modal', function () {
        // Reset all text inputs
        $(this).find('input[type="text"], input[type="tel"]').val('');

        // Reset OTP inputs
        $(this).find('.otp-input').val('');

        // Uncheck checkboxes
        $(this).find('input[type="checkbox"]').prop('checked', false);

        // Clear all error messages
        $(this).find('.text-danger').text('');

        // Reset sections (show login, hide OTP)
        $('#loginSection').addClass('active');
        $('#otpSection').removeClass('active');

        // Reset OTP timer text
        $('#otpTimer').text('Resend OTP in: 00:60');
    });
});


$(document).ready(function () {
    // Live search on keyup
    $('#searchInput').on('keyup', function () {
        let query = $(this).val().trim();

        if (query.length > 1) {
            $.ajax({
                url: '/search',   // your Laravel route
                method: 'GET',
                data: { query: query },
                success: function (data) {
                    let suggestionBox = $('#list_suggestion');
                    suggestionBox.empty();

                    if (data.length > 0) {
                        data.forEach(function (item) {
                            suggestionBox.append(
                                `<div onclick="selectSuggestion('${item.name}')">${item.name}</div>`
                            );
                        });
                    } else {
                        suggestionBox.html('<div>No results found</div>');
                    }
                }
            });
        } else {
            $('#list_suggestion').empty();
        }
    });
});

// Fill input when suggestion clicked
function selectSuggestion(value) {
    $('#searchInput').val(value);
    $('#list_suggestion').empty();
}

</script>
