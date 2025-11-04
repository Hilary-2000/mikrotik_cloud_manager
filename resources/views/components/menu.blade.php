<div>
    <!-- Because you are alive, everything is possible. - Thich Nhat Hanh -->
    <div class="navbar-header" style="height: 120px">
        <ul class="nav navbar-nav flex-row p-0 justify-content-center align-item-center">
            <li class="nav-item mr-auto p-0 text-center" style="width: fit-content"><a class="navbar-brand "
                    href="/Dashboard">
                    <img class="w-100 mx-auto" alt="Your Logo Appear Here"
                        src="/theme-assets/images/logo.jpeg" />
                </a></li>
            <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{$check_active("Dashboard")}}"><a href="{{route("Dashboard")}}"><i class="ft-home"></i><span class="menu-title" data-i18n="">Dashboard</span></a>
            </li>
            <li class="{{$check_active("Organizations")}}"><a href="{{Route::has("Organizations") ? route("Organizations") : "#"}}"><i class="ft-users"></i><span class="menu-title" data-i18n="">My Organizations</span></a>
            </li>
            {{-- <li class="{{$check_active("Packages")}}"><a href="{{Route::has("Packages") ? route("Packages") : "#"}}"><i class="ft-layers"></i><span class="menu-title" data-i18n="">Packages</span></a> --}}
            </li>
            <li class="{{$check_active("Transactions")}}"><a href="{{Route::has("Transactions") ? route("Transactions") : "#"}}"><i class="ft-tablet"></i><span class="menu-title" data-i18n="">Transactions</span></a>
            </li>
            <li class="{{$check_active("SMS")}}"><a href="{{Route::has("SMS") ? route("SMS") : "#"}}"><i class="ft-mail"></i><span class="menu-title" data-i18n="">SMS</span></a>
            </li>
            <li class="{{$check_active("Accounts")}}"><a href="{{Route::has("Accounts") ? route("Accounts") : "#"}}"><i class="ft-lock"></i><span class="menu-title" data-i18n="">Account and Profile</span></a>
            </li>
        </ul>
    </div>
    <!-- <a class="btn btn-danger btn-block btn-glow btn-upgrade-pro mx-1" href="https://themeselection.com/products/chameleon-admin-modern-bootstrap-webapp-dashboard-html-template-ui-kit/" target="_blank">Download PRO!</a> -->
    <div class="navigation-background">
    </div>
</div>