<div id="sidebarMain" class="d-none">
    <aside style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="bg-white js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    @php($e_commerce_logo=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
                    <a class="navbar-brand" href="{{route('admin.dashboard.index')}}" aria-label="Front">
                        INTERIOR CHOWK
                        <!-- <img onerror="this.src='{{asset('assets/back-end/img/900x400/img1.jpg')}}'"
                             class="navbar-brand-logo-mini for-web-logo max-h-30"
                             src="{{asset("storage/company/$e_commerce_logo")}}" alt="Logo"> -->
                    </a>
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="d-none js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expand"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <!-- Search Form -->
                    <div class="sidebar--search-form pb-3 pt-4" style="display:none;">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="{{\App\CPU\translate('search_menu')}}...">
                        </div>
                    </div>
                    <!-- <div class="input-group">
                        <diV class="card search-card" id="search-card"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                            <div class="card-body search-result-box" id="search-result-box">

                            </div>
                        </diV>
                    </div> -->
                    <!-- End Search Form -->
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/dashboard')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" title="{{\App\CPU\translate('Dashboard')}}" href="{{route('admin.dashboard.index')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('Dashboard')}}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->

                        <!-- POS -->
                        @if (\App\CPU\Helpers::module_permission_check('pos_management'))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/pos*')?'active':''}}" style="display:none;">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" title="{{\App\CPU\translate('POS')}}" href="{{route('admin.pos.index')}}">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('POS')}}</span>
                            </a>
                        </li>
                        @endif
                        <!-- End POS -->

                        <!-- Order Management -->
                        @if(\App\CPU\Helpers::module_permission_check('order_management'))
                        <li class="nav-item {{Request::is('admin/orders*')?'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">{{\App\CPU\translate('order_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Order -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:void(0)" title="{{\App\CPU\translate('orders')}}">
                                <i class="tio-shopping-cart-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('orders')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                <!-- <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">-->
                                <li class="nav-item  {{str_contains(url()->current().'?instant='.request()->get('instant'),'/admin/orders/list/all?instant=0')==1?'active':''}}">

                                    <a class="nav-link" href="{{route('admin.orders.list',['all','instant'=>'0'])}}" title="{{\App\CPU\translate('All')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('standard_delivery')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where('instant_delivery_type',0)->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item  {{str_contains(url()->current().'?instant='.request()->get('instant'),'/admin/orders/list/all?instant=1')==1?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.orders.list',['all','instant'=>'1'])}}" title="{{\App\CPU\translate('All')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('instant_delivery')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where('instant_delivery_type',1)->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="{{\App\CPU\translate('pending')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'pending'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="{{\App\CPU\translate('confirmed')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('confirmed')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'confirmed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="{{\App\CPU\translate('Packaging')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Packaging')}}
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'processing'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}" title="{{\App\CPU\translate('out_for_delivery')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('out_for_delivery')}}
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'out_for_delivery'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="{{\App\CPU\translate('delivered')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('delivered')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'delivered'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="{{\App\CPU\translate('returned')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('returned')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where('order_status','returned')->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="{{\App\CPU\translate('failed')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Failed_to_Deliver')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'failed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}" style="display:none;">
                                    <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="{{\App\CPU\translate('canceled')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('canceled')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'canceled'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=0')==1 || str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=0')==1 || Request::is('admin/coupon*')||Request::is('admin/sellers/seller-list/inactive')||Request::is('admin/refund-section/refund/*')||Request::is('admin/customer/wallet/report')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Approval')}}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('Approval')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=0')==1 || Request::is('admin/coupon*') || str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=0')==1 || Request::is('admin/sellers/seller-list/inactive')||Request::is('admin/refund-section/refund/*')||Request::is('admin/customer/wallet/report')?'block':''}}">
                                
                                <li class="nav-item {{str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=0')==1?'active':''}}">
                                    <a class="nav-link" title="{{\App\CPU\translate('Products')}}" href="{{route('admin.product.list',['seller', 'status'=>'0'])}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Products')}}</span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.coupon.add-new')}}" title="{{\App\CPU\translate('coupon')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('coupon')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/sellers/seller-list/inactive')?'active':''}}">
                                    <a class="nav-link" title="{{\App\CPU\translate('Seller_List')}}" href="{{ route('admin.sellers.seller-list', ['status' => 'inactive']) }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Seller')}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=0')==1?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.provider.list',['isactive' => '0'])}}" title="{{\App\CPU\translate('Service_Provider')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Service_Provider')}} </span>
                                    </a>
                                </li>
                                <!--<li class="nav-item {{Request::is('admin/provider/list') || Request::is('admin/provider/view*')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.provider.list')}}" title="{{\App\CPU\translate('worker')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('worker')}} </span>
                                    </a>
                                </li>-->
                                <li class="nav-item {{Request::is('admin/refund-section/refund/*')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.refund-section.refund.list',['Filter'])}}" title="{{\App\CPU\translate('return_&_refund')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('return_&_refund')}} </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/customer/wallet/report')?'active':''}}">
                                    <a class="nav-link" title="{{\App\CPU\translate('wallet')}}" href="{{route('admin.customer.wallet.report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('wallet')}}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/brand/add-new')||Request::is('admin/category*') || Request::is('admin/product/home-products') ||Request::is('admin/sub*')||Request::is('admin/service-category*') ||Request::is('admin/service-sub*')||Request::is('admin/attribute*')||Request::is('admin/product/list/in_house') || Request::is('admin/product/bulk-import') || Request::is('admin/product/add-new')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('create')}}">
                                <i class="tio-category-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('create')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/brand/add-new') || Request::is('admin/product/home-products') ||Request::is('admin/category*') ||Request::is('admin/sub*')||Request::is('admin/service-category*') ||Request::is('admin/service-sub*')||Request::is('admin/attribute*')||Request::is('admin/product/list/in_house') || Request::is('admin/product/bulk-import') || Request::is('admin/product/add-new')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/product/home-products')?'active':''}}" title="{{\App\CPU\translate('home_Products')}}">
                                    <a class="nav-link " href="{{route('admin.product.home-products')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('home_Products')}}</span>
                                    </a>
                                </li>
                                
                                <li class="nav-item {{Request::is('admin/brand/add-new')?'active':''}}" title="{{\App\CPU\translate('brands')}}">
                                    <a class="nav-link " href="{{route('admin.brand.add-new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('brands')}}</span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/attribute*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.attribute.view')}}" title="{{\App\CPU\translate('Product_Attributes')}}">
                                        <i class="tio-circle nav-indicator-icon"></i>
                                        <span class="text-truncate">{{\App\CPU\translate('Product_Attributes')}}</span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/product/list/in_house') || Request::is('admin/product/bulk-import') || (Request::is('admin/product/add-new')) || (Request::is('admin/product/view/*')) || (Request::is('admin/product/barcode/*')))?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('InHouse Products')}}">
                                        <i class="tio-shop nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            <span class="text-truncate">{{\App\CPU\translate('InHouse Products')}}</span>
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{(Request::is('admin/product/list/in_house') || (Request::is('admin/product/stock-limit-list/in_house')) || (Request::is('admin/product/bulk-import')) || (Request::is('admin/product/add-new')) || (Request::is('admin/product/view/*')) || (Request::is('admin/product/barcode/*')))?'block':''}}">
                                        <li class="nav-item {{(Request::is('admin/product/list/in_house') || (Request::is('admin/product/add-new')) || (Request::is('admin/product/view/*')) || (Request::is('admin/product/barcode/*')))?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.product.list',['in_house', ''])}}" title="{{\App\CPU\translate('Products')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Products')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/product/bulk-import')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.product.bulk-import')}}" title="{{\App\CPU\translate('bulk_import')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('bulk_import')}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/category*') ||Request::is('admin/sub*')) ?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Product_Category')}}">
                                        <i class="tio-filter-list nav-icon"></i>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Product_Category')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{(Request::is('admin/category*') ||Request::is('admin/sub*'))?'block':''}}">
                                        <li class="nav-item {{Request::is('admin/category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.category.view')}}" title="{{\App\CPU\translate('Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Categories')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/sub-category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.sub-category.view')}}" title="{{\App\CPU\translate('Sub_Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Sub_Categories')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/sub-sub-category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.sub-sub-category.view')}}" title="{{\App\CPU\translate('Sub_Sub_Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Sub_Sub_Categories')}}</span>
                                            </a>
                                        </li>
                                         <li class="nav-item {{Request::is('admin/sub-sub-category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.category.category_request')}}" title="{{\App\CPU\translate('Sub_Sub_Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Categories_request')}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Pages of service provider  type of the Categories -->
                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/service-category*') ||Request::is('admin/service-sub*')) ?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Service_Category')}}">
                                        <i class="tio-filter-list nav-icon"></i>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Service_Category')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{(Request::is('admin/service-category*') ||Request::is('admin/service-sub*'))?'block':''}}">
                                        <li class="nav-item {{Request::is('admin/service-category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.service-category.view')}}" title="{{\App\CPU\translate('Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Categories')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/service-sub-category/view')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.service-sub-category.view')}}" title="{{\App\CPU\translate('Sub_Categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Sub_Categories')}}</span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>
                            </ul>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/brand/list')||str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=1')==1||str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=2')==1||Request::is('admin/customer/list')||Request::is('admin/sellers/seller-list/active')||str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=1')==1?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('list')}}">
                                <i class="tio-wishlist nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('list')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/brand/list')||str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=1')==1||str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=2')==1||Request::is('admin/customer/list')||Request::is('admin/sellers/seller-list/active')||str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=1')==1?'block':'none'}}">


                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product/list/seller*')||Request::is('admin/product/updated-product-list')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Products_List')}}">
                                        <i class="tio-airdrop nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Products_List')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/product/list/seller*')||Request::is('admin/product/updated-product-list')?'block':''}}">

                                        @if (\App\CPU\Helpers::get_business_settings('product_wise_shipping_cost_approval')==1)
                                        <li class="nav-item {{Request::is('admin/product/updated-product-list')?'active':''}}" style="display:none;">
                                            <a class="nav-link" title="{{\App\CPU\translate('updated_products')}}" href="{{route('admin.product.updated-product-list')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('updated_products')}} </span>
                                            </a>
                                        </li>
                                        @endif
                                        <!--<li class="nav-item {{str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=0')==1?'active':''}}">
                                            <a class="nav-link" title="{{\App\CPU\translate('New')}} {{\App\CPU\translate('Products')}}" href="{{route('admin.product.list',['seller', 'status'=>'0'])}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('New')}} {{\App\CPU\translate('Products')}} </span>
                                            </a>
                                        </li>-->
                                        <li class="nav-item {{str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=1')==1?'active':''}}">
                                            <a class="nav-link" title="{{\App\CPU\translate('Approved')}} {{\App\CPU\translate('Products')}}" href="{{route('admin.product.list',['seller', 'status'=>'1'])}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Approved')}} {{\App\CPU\translate('Products')}}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{str_contains(url()->current().'?status='.request()->get('status'),'/admin/product/list/seller?status=2')==1?'active':''}}">
                                            <a class="nav-link" title="{{\App\CPU\translate('Denied')}} {{\App\CPU\translate('Products')}}" href="{{route('admin.product.list',['seller', 'status'=>'2'])}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">{{\App\CPU\translate('Denied')}} {{\App\CPU\translate('Products')}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item {{Request::is('admin/brand/list')?'active':''}}" title="{{\App\CPU\translate('Brand_List')}}">
                                    <a class="nav-link " href="{{route('admin.brand.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Brand_List')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/customer/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.customer.list')}}" title="{{\App\CPU\translate('Customer_List')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Customer_List')}} </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/sellers/seller-list/active')?'active':''}}">
                                    <a class="nav-link" title="{{\App\CPU\translate('Seller_List')}}" href="{{route('admin.sellers.seller-list', ['status' => 'active'])}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{\App\CPU\translate('Seller_List')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{str_contains(url()->current().'?isactive='.request()->get('isactive'),'/admin/provider/list?isactive=1')==1?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.provider.list',['isactive' => '1'])}}" title="{{\App\CPU\translate('Service_Provider_List')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Service_Provider_List')}} </span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        @endif
                        <!--Order Management Ends-->

                        <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/coupon*') || Request::is('admin/deal*')) || Request::is('admin/banner*') || Request::is('admin/provider-banner*')|| Request::is('admin/notification*') || Request::is('admin/provider-notification*') || Request::is('admin/Membership_plan*') || Request::is('admin/Scheme_management*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Offers_&_Deals')}}">
                                <i class="tio-users-switch nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('Offers_&_Deals')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{(Request::is('admin/coupon*') || Request::is('admin/deal*')) || Request::is('admin/banner*') || Request::is('admin/provider-banner*') || Request::is('admin/notification*')|| Request::is('admin/provider-notification*') ||Request::is('admin/Membership_plan*') || Request::is('admin/Scheme_management*')?'block':'none'}}">
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.coupon.add-new')}}" title="{{\App\CPU\translate('coupon')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('coupon')}}</span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/deal/flash') || (Request::is('admin/deal/update*')))?'active':''}}" style="display:none;">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.deal.flash')}}" title="{{\App\CPU\translate('Flash_Deals')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('Flash_Deals')}}</span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/deal/day') || (Request::is('admin/deal/day-update*')))?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.deal.day')}}" title="{{\App\CPU\translate('deal_of_the_day')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('deal_of_the_day')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.banner.list')}}" title="{{\App\CPU\translate('banners')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('banners')}}
                                        </span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.banner.list')}}" title="{{\App\CPU\translate('banners')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('delivery banners')}}
                                        </span>
                                    </a>
                                </li>
                                
                                 <li class="navbar-vertical-aside-has-menu {{Request::is('admin/provider-banner*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.provider-banner.list')}}" title="{{\App\CPU\translate('service_provider_banners')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('service_provider_banners')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.notification.add-new')}}" title="{{\App\CPU\translate('Push_Notification')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Push_Notification')}}
                                        </span>
                                    </a>
                                </li>
                                
                                 <li class="navbar-vertical-aside-has-menu {{Request::is('admin/provider-notification*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.provider-notification.add-new')}}" title="{{\App\CPU\translate('Push_Notification_Provider')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Push_Notification_provider')}}
                                        </span>
                                    </a>
                                </li>
                                
                                 <li class="navbar-vertical-aside-has-menu {{Request::is('admin/Membership_plan*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.Membership_plan.add-new')}}" title="{{\App\CPU\translate('Push_Notification')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Membership_plan')}}
                                        </span>
                                    </a>
                                </li>
                                
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/Scheme_management*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.Scheme_management.add-new')}}" title="{{\App\CPU\translate('Push_Notification')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Scheme_management')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/deal/feature') || Request::is('admin/deal/edit*'))?'active':''}}" style="display:none;">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.deal.feature')}}" title="{{\App\CPU\translate('Featured_Deal')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Featured_Deal')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/blog*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.blog.list')}}" title="{{\App\CPU\translate('blogs')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('blogs')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/seo*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.seo.index')}}" title="{{\App\CPU\translate('blogs')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Seo')}}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/reviews/list')||Request::is('admin/reviews/provider-list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Reviews')}}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('Reviews')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/reviews/list') || Request::is('admin/reviews/provider-list')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/reviews/list')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.reviews.list')}}" title="{{\App\CPU\translate('Customer')}} {{\App\CPU\translate('Product')}} {{\App\CPU\translate('Reviews')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Customer')}} {{\App\CPU\translate('Product')}} {{\App\CPU\translate('Reviews')}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/reviews/provider-list')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.reviews.provider-list')}}" title="{{\App\CPU\translate('Customer')}} {{\App\CPU\translate('Service')}} {{\App\CPU\translate('Reviews')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Customer')}} {{\App\CPU\translate('Service')}} {{\App\CPU\translate('Reviews')}}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!--Report Management -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/order')||Request::is('admin/report/sale_register')||Request::is('admin/report/all-product') ||Request::is('admin/stock/product-in-wishlist') || Request::is('admin/stock/product-stock')||Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning')||Request::is('admin/sellers/withdraw_list') || Request::is('admin/report/membership')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('Reports')}}">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('Reports')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/report/order') || Request::is('admin/report/sale_register') || Request::is('admin/report/all-product') ||Request::is('admin/stock/product-in-wishlist') || Request::is('admin/stock/product-stock')||Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning')||Request::is('admin/sellers/withdraw_list') || Request::is('admin/report/membership')?'block':'none'}}">
                                <!-- <li class="nav-item {{Request::is('admin/report/sale_register')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.sale_register')}}" title="{{\App\CPU\translate('Sale_Register')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Sale_Register')}}</span>
                                    </a>
                                </li> -->
                                <li class="nav-item {{Request::is('admin/report/shipping_register')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.shipping_register')}}" title="{{\App\CPU\translate('Sale_Register')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Shipping Reports')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " href="{{route('admin.report.coupon_register')}}" title="{{\App\CPU\translate('Sale_Register')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Coupon Reports')}}</span>
                                    </a>
                                </li>

                                <!-- <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.order')}}" title="{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Order_Report')}}</span>
                                    </a>
                                </li> -->
                                <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.cancellation_report')}}" title="{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Cancellation_Report')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.sale_return')}}" title="{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Sale Return Report')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ (Request::is('admin/report/all-product') ||Request::is('admin/stock/product-in-wishlist') || Request::is('admin/stock/product-stock')) ?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.all-product')}}" title="{{\App\CPU\translate('Product_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Product_Report')}}</span>
                                    </a>
                                </li>
                                
                                 <!-- <li class="nav-item {{ Request::is('admin/report/membership') ?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.report.membership')}}" title="{{\App\CPU\translate('Membership_Transaction_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Membership_Transaction_Report')}}</span>
                                    </a>
                                </li> -->
                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/report/order_sale') || Request::is('admin/report/seller-earning'))?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.report.order_sale')}}" title="{{\App\CPU\translate('Sales_&_Transaction_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Sales_&_Orders_Report')}}
                                        </span>
                                    </a>
                                </li>
                                <!-- <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning'))?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.report.admin-earning')}}" title="{{\App\CPU\translate('Sales_&_Transaction_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Sales_&_Transaction_Report')}}
                                        </span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/report/admin-earning') || Request::is('admin/report/seller-earning'))?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.report.admin-earning')}}" title="{{\App\CPU\translate('Sales_&_Commission_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Sales_&_Commission_Report')}}
                                        </span>
                                    </a>
                                </li> -->
                                 <li class="navbar-vertical-aside-has-menu">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.report.order_sale_statement')}}" title="{{\App\CPU\translate('Sales_&_Commission_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Claim_Report')}}
                                        </span>
                                    </a>
                                </li>

                                 <li class="navbar-vertical-aside-has-menu">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.report.payment_record_report')}}" title="{{\App\CPU\translate('Sales_&_Commission_Report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Payment_Report')}}
                                        </span>
                                    </a>
                                </li>

                                <!-- <li class="nav-item {{Request::is('admin/sellers/withdraw_list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.sellers.withdraw_list')}}" title="{{\App\CPU\translate('withdraws')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('withdraws')}}</span>
                                    </a>
                                </li> -->
                            </ul>
                        </li>




                        <!--Report Management -->




                        <!--Product Management -->
                        @if(\App\CPU\Helpers::module_permission_check('product_management'))
                        <li class="nav-item {{(Request::is('admin/brand*') || Request::is('admin/category*') || Request::is('admin/sub*') || Request::is('admin/attribute*') || Request::is('admin/product*'))?'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">{{\App\CPU\translate('product_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @endif
                        <!--Product Management Ends-->

                        @if(\App\CPU\Helpers::module_permission_check('promotion_management'))
                        <!--promotion management start-->
                        <li class="nav-item {{(Request::is('admin/banner*') || (Request::is('admin/coupon*')) || (Request::is('admin/notification*')) || (Request::is('admin/deal*')))?'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">{{\App\CPU\translate('promotion_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/announcement')?'active':''}}" style="display:none;">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.business-settings.announcement')}}" title="{{\App\CPU\translate('announcement')}}">
                                <i class="tio-mic-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('announcement')}}
                                </span>
                            </a>
                        </li>
                        <!--promotion management end-->
                        @endif

                        <!-- end refund section -->
                        @if(\App\CPU\Helpers::module_permission_check('support_section'))
                        <li class="nav-item {{(Request::is('admin/support-ticket*') || Request::is('admin/contact*'))?'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">{{\App\CPU\translate('help_&_support_section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/contact*')?'active':''}}" style="display:none;">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.contact.list')}}" title="{{\App\CPU\translate('messages')}}">
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{\App\CPU\translate('messages')}}
                                        @php($message=\App\Model\Contact::where('seen',0)->count())
                                        @if($message!=0)
                                        <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                        @endif
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/support-ticket*')?'active':''}}" style="display:none;">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.support-ticket.view')}}" title="{{\App\CPU\translate('Support_Ticket')}}">
                                <i class="tio-chat nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    <span class="position-relative">
                                        {{\App\CPU\translate('Support_Ticket')}}
                                        @if(\App\Model\SupportTicket::where('status','open')->count()>0)
                                        <span class="btn-status btn-xs-status btn-status-danger position-absolute top-0 menu-status"></span>
                                        @endif
                                    </span>
                                </span>
                            </a>
                        </li>
                        @endif
                        <!--support section ends here-->

                        <!--Reports & Analytics section-->
                        @if(\App\CPU\Helpers::module_permission_check('report'))
                        <li class="nav-item {{(Request::is('admin/report/earning') || Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-report') || Request::is('admin/report/earning') || Request::is('admin/transaction/list') || Request::is('admin/refund-section/refund-list') || Request::is('admin/stock/product-in-wishlist') || Request::is('admin/reviews*') || Request::is('admin/stock/product-stock')) ? 'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">
                                {{\App\CPU\translate('Reports')}} & {{\App\CPU\translate('Analysis')}}
                            </small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>




                        @endif
                        <!--Reports & Analytics section End-->

                        <!--User management-->
                        @if(\App\CPU\Helpers::module_permission_check('user_section'))
                        <li class="nav-item {{(Request::is('admin/customer/list') ||Request::is('admin/sellers/subscriber-list')||Request::is('admin/sellers/seller-add') || Request::is('admin/sellers/seller-list') || Request::is('admin/delivery-man*'))?'scroll-here':''}}" style="display:none;">
                            <small class="nav-subtitle" title="">{{\App\CPU\translate('user_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>





                        <!-- start folder of the service provider  -->

                        <!--   end of the service provider --->


                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/subscriber-list')?'active':''}}" style="display:none;">
                            <a class="nav-link " href="{{route('admin.customer.subscriber-list')}}" title="{{\App\CPU\translate('subscribers')}}">
                                <span class="tio-user nav-icon"></span>
                                <span class="text-truncate">{{\App\CPU\translate('subscribers')}} </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/delivery-man*')?'active':''}}" style="display:none;">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('delivery-man')}}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('delivery-man')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/delivery-man*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/delivery-man/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.add')}}" title="{{\App\CPU\translate('add_new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('add_new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/delivery-man/list') || Request::is('admin/delivery-man/earning-statement*') || Request::is('admin/delivery-man/order-history-log*') || Request::is('admin/delivery-man/order-wise-earning*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.delivery-man.list')}}" title="{{\App\CPU\translate('List')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('List')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/delivery-man/chat')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.delivery-man.chat')}}" title="{{\App\CPU\translate('Chat')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('chat')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/delivery-man/withdraw-list') || Request::is('admin/delivery-man/withdraw-view*')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.withdraw-list')}}" title="{{\App\CPU\translate('withdraws')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('withdraws')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/delivery-man/emergency-contact')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.emergency-contact.index')}}" title="{{\App\CPU\translate('emergency_contact')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Emergency_Contact')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        @if(auth('admin')->user()->admin_role_id==1)
                        <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/employee*') || Request::is('admin/custom-role*'))?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('employees')}}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('employees')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/employee*') || Request::is('admin/custom-role*')?'block':'none'}}">
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/custom-role*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.custom-role.create')}}" title="{{\App\CPU\translate('Employee_Role_Setup')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{\App\CPU\translate('Employee_Role_Setup')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{(Request::is('admin/employee/list') || Request::is('admin/employee/add-new') || Request::is('admin/employee/update*'))?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.employee.list')}}" title="{{\App\CPU\translate('Employees')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Employees')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{(Request::is('admin/employee/career/list') || Request::is('admin/employee/career/add-new') || Request::is('admin/employee/career/update*'))?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.employee.career.list')}}" title="{{\App\CPU\translate('Career')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Career')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{(Request::is('admin/employee/applicants*') || Request::is('admin/employee/career/add-new') || Request::is('admin/employee/career/update*'))?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.employee.applicants.list')}}" title="{{\App\CPU\translate('Applicants')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CPU\translate('Applicants')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @endif
                        <!--User management end-->

                        <!--System Settings-->
                        @if(\App\CPU\Helpers::module_permission_check('system_settings'))



                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-config') || Request::is('admin/business-settings/web-config/app-settings') || Request::is('admin/product-settings/inhouse-shop') || Request::is('admin/business-settings/seller-settings') || Request::is('admin/customer/customer-settings') || Request::is('admin/refund-section/refund-index') || Request::is('admin/business-settings/shipping-method/setting') || Request::is('admin/business-settings/order-settings/index') || Request::is('admin/product-settings') || Request::is('admin/business-settings/web-config/delivery-restriction') || Request::is('admin/business-settings/cookie-settings') || Request::is('admin/business-settings/otp-setup') || Request::is('admin/business-settings/all-pages-banner*') || Request::is('admin/business-settings/delivery-restriction') || Request::is('admin/business-settings/mail') || Request::is('admin/business-settings/sms-module') || Request::is('admin/business-settings/captcha') || Request::is('admin/social-login/view') || Request::is('admin/social-media-chat/view') || Request::is('admin/business-settings/map-api') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/fcm-index') || Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/sp-social-media') ||  Request::is('admin/file-manager*') || Request::is('admin/business-settings/features-section')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{\App\CPU\translate('system_settings')}}">
                                <i class="tio-settings-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{\App\CPU\translate('system_settings')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/business-settings/web-config') || Request::is('admin/business-settings/web-config/app-settings') || Request::is('admin/product-settings/inhouse-shop') || Request::is('admin/business-settings/seller-settings') || Request::is('admin/customer/customer-settings') || Request::is('admin/refund-section/refund-index') || Request::is('admin/business-settings/shipping-method/setting') || Request::is('admin/business-settings/order-settings/index') || Request::is('admin/product-settings') || Request::is('admin/business-settings/web-config/delivery-restriction') || Request::is('admin/business-settings/cookie-settings') || Request::is('admin/business-settings/otp-setup') || Request::is('admin/business-settings/all-pages-banner*') || Request::is('admin/business-settings/delivery-restriction') || Request::is('admin/business-settings/mail') || Request::is('admin/business-settings/sms-module') || Request::is('admin/business-settings/captcha') || Request::is('admin/social-login/view') || Request::is('admin/social-media-chat/view') || Request::is('admin/business-settings/map-api') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/fcm-index') || Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/sp-social-media') || Request::is('admin/file-manager*') || Request::is('admin/business-settings/features-section')?'block':'none'}}">



                            <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/business-settings/web-config') || Request::is('admin/business-settings/web-config/app-settings') || Request::is('admin/product-settings/inhouse-shop') || Request::is('admin/business-settings/seller-settings') || Request::is('admin/customer/customer-settings') || Request::is('admin/refund-section/refund-index') || Request::is('admin/business-settings/shipping-method/setting') || Request::is('admin/business-settings/order-settings/index') || Request::is('admin/product-settings') || Request::is('admin/business-settings/web-config/delivery-restriction') || Request::is('admin/business-settings/cookie-settings') || Request::is('admin/business-settings/otp-setup') || Request::is('admin/business-settings/all-pages-banner*') || Request::is('admin/business-settings/delivery-restriction'))?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.business-settings.seller-settings.index')}}"
                               title="{{\App\CPU\translate('Business_Setup')}}">
                                <i class="tio-globe nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{\App\CPU\translate('Business_Setup')}}
                            </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{(Request::is('admin/business-settings/mail') || Request::is('admin/business-settings/sms-module') || Request::is('admin/business-settings/captcha') || Request::is('admin/social-login/view') || Request::is('admin/social-media-chat/view') || Request::is('admin/business-settings/map-api') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/fcm-index'))?'active':''}}">
                            <a class="nav-link " href="{{route('admin.business-settings.sms-module')}}"
                               title="{{\App\CPU\translate('3rd_party')}}">
                                <span class="tio-key nav-icon"></span>
                                <span class="text-truncate">{{\App\CPU\translate('3rd_party')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/sp-social-media') || Request::is('admin/file-manager*') || Request::is('admin/business-settings/features-section') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                               href="javascript:" title="{{\App\CPU\translate('Pages_&_Media')}}">
                                <i class="tio-pages-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{\App\CPU\translate('Pages_&_Media')}}
                            </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/sp-social-media') || Request::is('admin/file-manager*') || Request::is('admin/business-settings/features-section')?'block':'none'}}">
                                <li class="nav-item {{(Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/page*') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list')|| Request::is('admin/business-settings/features-section'))?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.terms-condition')}}"
                                       title="{{\App\CPU\translate('pages')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                      {{\App\CPU\translate('pages')}}
                                    </span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/social-media')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                       href="{{route('admin.business-settings.social-media')}}"
                                       title="{{\App\CPU\translate('Social_Media_Links')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('Social_Media_Links')}}
                                </span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/sp-social-media')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                       href="{{route('admin.business-settings.sp-social-media')}}"
                                       title="{{\App\CPU\translate('SP_Social_Media_Links')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{\App\CPU\translate('SP_Social_Media_Links')}}
                                </span>
                                    </a>
                                </li>

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/file-manager*')?'active':''}}" style="display:none;">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                       href="{{route('admin.file-manager.index')}}"
                                       title="{{\App\CPU\translate('gallery')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{\App\CPU\translate('gallery')}}
                                    </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                            </ul>
                        </li>

                        <!--End setting Management -->
                        @endif

                     
                        <!--System Settings end-->

                        <li class="nav-item pt-5">
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

@push('script_2')
<script>
    $(window).on('load', function() {
        if ($(".navbar-vertical-content li.active").length) {
            $('.navbar-vertical-content').animate({
                scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
            }, 10);
        }
    });

    //Sidebar Menu Search
    var $rows = $('.navbar-vertical-content .navbar-nav > li');
    $('#search-bar-input').keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        $rows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });
</script>
@endpush