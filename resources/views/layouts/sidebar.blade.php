<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" role="navigation">
            {{-- Dashboard --}}
            <li class="nav-item {{ Request::routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}" aria-current="{{ Request::routeIs('home') ? 'page' : 'false' }}">
                    <i class="feather icon-home"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item has-sub {{ Request::routeIs('promotions.*') ? 'open' : '' }}">
                <a href="#">
                    <i class="feather icon-credit-card"></i>
                    <span class="menu-title">Promotions</span>
                </a>
                <ul class="menu-content">
                    <li class="{{ Request::routeIs('promotions.*') ? 'active' : '' }}">
                        <a href="{{ route('promotions.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Promotions</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('promotions.create') ? 'active' : '' }}">
                        <a href="{{ route('promotions.create') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Create Promotion</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-sub {{ Request::routeIs('availability_banners.*') ? 'open' : '' }}">
                <a href="#">
                    <i class="feather icon-image"></i>
                    <span class="menu-title">Availability Banners</span>
                </a>
                <ul class="menu-content">
                    <li class="{{ Request::routeIs('availability_banners.index') ? 'active' : '' }}">
                        <a href="{{ route('availability_banners.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Banners</span>
                        </a>
                    </li>
                    <li class="{{ Request::routeIs('availability_banners.create') ? 'active' : '' }}">
                        <a href="{{ route('availability_banners.create') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Create banner</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
