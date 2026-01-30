<div id="headerMain" class="d-none">
    <header id="header"
        class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                <!-- Logo -->
                @php($shop = \App\User::where(['id' => auth()->id()])->first())
                
                @php($seller = \App\Model\Seller::where(['id' => auth('seller')->id()])->first())

                {{-- @dd($shop); --}}
                <a class="navbar-brand" href="{{ route('service.dashboard') }}" aria-label="">
                    {{-- @if (isset($shop))
                        <img class="navbar-brand-logo"
                            onerror="this.src='{{ asset('public/assets/back-end/img/160x160/img1.jpg') }}'"
                            src="{{ asset("storage/app/public/shop/$shop->image") }}" alt="Logo" height="40">
                        <img class="navbar-brand-logo-mini"
                            onerror="this.src='{{ asset('public/assets/back-end/img/160x160/img1.jpg') }}'"
                            src="{{ asset("storage/app/public/shop/$shop->image") }}" alt="Logo" height="40">
                    @else
                        <img class="navbar-brand-logo-mini"
                            src="{{ asset('public/assets/back-end/img/160x160/img1.jpg') }}" alt="Logo"
                            height="40">
                    @endif --}}
                </a>
            </div>

            <div class="navbar-nav-wrap-content-left">


                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3 d-xl-none">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                        data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                        data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
            </div>

            <div class="navbar-nav-wrap-content-right">

                <ul class="navbar-nav align-items-center flex-row">

                    {{-- ✅ FIXED: duplicate class + javascript: --}}
                    <li class="nav-item view-web-site-info">
                        <div class="hs-unfold">
                            <a class="bg-white js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                                onclick="openInfoWeb()" href="javascript:void(0)">
                                <i class="tio-info"></i>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker media align-items-center gap-3 navbar-dropdown-account-wrapper dropdown-toggle dropdown-toggle-left-arrow"
                                href="javascript:;"
                                data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                {{-- <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name mb-0">
                                        {{ $shop->name }}</h5>
                                    <span class="fz-12">{{ \Illuminate\Support\Str::limit($shop->name, 20) }}</span>
                                </div> --}}

                                {{-- <div class="avatar avatar-sm avatar-circle">
                                    <?php
                                        if (!empty($shop->image)) {
                                        ?>
                                    <img class="avatar-img" src="{{ asset('storage/app/public/shop/' . $shop->image) }}"
                                        alt="Shop Avatar">
                                    <?php
                                        } else {
                                        ?>
                                    <div class="avatar-initials" style="background:#073b74;color:#fff;">
                                        <?php
                                        $text = '';
                                        
                                        if (isset($shop) && !empty($shop->name)) {
                                            $words = preg_split('/\s+/', trim($shop->name));
                                        
                                            if (count($words) >= 2) {
                                                $text = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                                            } elseif (count($words) === 1) {
                                                $text = strtoupper(mb_substr($words[0], 0, 2));
                                            }
                                        }
                                        
                                        echo $text;
                                        ?>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div> --}}
                            </a>

                            <div id="accountNavbarDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account __w-16rem">

                                <a class="dropdown-item" href="javascript:"
                                    onclick="Swal.fire({
                                    title: '{{ \App\CPU\translate('Do you want to logout') }}?',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#377dff',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: `Yes`,
                                    denyButtonText: `Don't Logout`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='';
                                    } else{
                                    Swal.fire('Canceled', '', 'info')
                                    }
                                    })">
                                    <span class="text-truncate pr-2"
                                        title="Sign out">{{ \App\CPU\translate('Sign out') }}</span>
                                </a>

                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </header>
</div>

<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
