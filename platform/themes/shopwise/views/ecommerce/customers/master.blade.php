<!-- START MAIN CONTENT -->
<div class="main_content">

    <!-- START SECTION SHOP -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="dashboard_menu">
                        <ul class="nav nav-tabs flex-column" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'customer.overview') active @endif" href="{{ route('customer.overview') }}"><i class="ti-layout-grid2"></i>{{ __('Overview') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'customer.orders' || Route::currentRouteName() == 'customer.orders.view') active @endif" href="{{ route('customer.orders') }}"><i class="ti-shopping-cart-full"></i>{{ __('Orders') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'customer.address' || Route::currentRouteName() == 'customer.address.create' || Route::currentRouteName() == 'customer.address.edit') active @endif" href="{{ route('customer.address') }}"><i class="ti-location-pin"></i>{{ __('My Addresses') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'customer.edit-account') active @endif" href="{{ route('customer.edit-account') }}"><i class="ti-id-badge"></i>{{ __('Account details') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if (Route::currentRouteName() == 'customer.change-password') active @endif" href="{{ route('customer.change-password') }}"><i class="ti-id-badge"></i>{{ __('Change password') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('customer.logout') }}"><i class="ti-lock"></i>{{ __('Logout') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="dashboard_content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION SHOP -->
