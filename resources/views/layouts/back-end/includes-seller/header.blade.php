<header class="c-header-w sticky-header">
    <div class="container">
        <nav class="navbar navbar-expand-lg d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('asset/img/logo icon.png') }}" alt="Logo">
                </a>
                <form class="d-flex ms-3">
                    <input class="form-control search-input" type="text" placeholder="Search here..." aria-label="Search">
                </form>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
               
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ url('/') }}#about-section">About Us</a></li>
                    <li class="c-drop-down-main">
                        <a href="#" class="c-drop-down-btn">We provide</a>
                        <ul class="c-drop-down-list">
                            <li><a href="{{ route('shopping') }}">Shopping</a></li>
                            <li><a href="{{ route('service') }}">Service</a></li>
                            <li><a href="{{ route('solution') }}">Solution</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('service-chowk') }}">Service chowk</a></li>
                    <li><a href="{{ route('seller-chowk') }}">Seller’s Chowk</a></li>
                    <li><a href="{{ route('seller.auth.seller-login') }}">Seller Login</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>