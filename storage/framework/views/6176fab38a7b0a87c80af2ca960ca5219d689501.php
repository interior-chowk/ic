<div id="headerMain" class="d-none">
    <header id="header"
        class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                <!-- Logo -->
                <?php ($shop = \App\User::where(['id' => auth()->id()])->first()); ?>
                
                <?php ($seller = \App\Model\Seller::where(['id' => auth('seller')->id()])->first()); ?>

                
                <a class="navbar-brand" href="<?php echo e(route('service.dashboard')); ?>" aria-label="">
                    
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
                                

                                
                            </a>

                            <div id="accountNavbarDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account __w-16rem">

                                <a class="dropdown-item" href="javascript:"
                                    onclick="Swal.fire({
                                    title: '<?php echo e(\App\CPU\translate('Do you want to logout')); ?>?',
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
                                        title="Sign out"><?php echo e(\App\CPU\translate('Sign out')); ?></span>
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
<?php /**PATH D:\xampp\htdocs\adminic\resources\views/layouts/back-end/partials-service/_header.blade.php ENDPATH**/ ?>