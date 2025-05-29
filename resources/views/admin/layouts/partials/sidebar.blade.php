<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo justify-content-center">
        <a href="dashboard" class="app-brand-link">
            <img src="{{asset("public/assets/img/logo/logo.png")}}" alt="logo" style="width: 110px; height: auto;">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1 mt-2">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{route('dashboard')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>{{__('messages.dashboard')}}</div>
            </a>
        </li>
        <!-- Manage -->
        {{-- <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Manage</span>
        </li> --}}
        <li class="menu-item {{ request()->is('warehouse-cities*') ? 'active' : '' }}">
            <a href="{{route('cities.warehouse')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-flag"></i>
                <div>{{__('messages.warehouse_city')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('categories*') ? 'active' : '' }}">
            <a href="{{route('categories')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div>{{__('messages.category')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('products*') ? 'active' : '' }}">
            <a href="{{route('products')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>{{__('messages.products')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
            <a href="{{route('users')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>{{__('messages.users')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('sellers*') ? 'active' : '' }}">
            <a href="{{route('sellers')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div>{{__('messages.sellers')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('shops*') ? 'active' : '' }}">
            <a href="{{route('shops')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                <div>{{__('messages.shops')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('sales*','returns*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-shopping-bag"></i>
                <div data-i18n="User interface">{{__('messages.sale')}}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('sales*') ? 'active' : '' }}">
                    <a href="{{route('sales')}}" class="menu-link">
                        <div data-i18n="Alerts">{{__('messages.sale')}}</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ request()->is('goods-to-supplier*') ? 'active' : '' }}">
                    <a href="{{route('goods-to-supplier')}}" class="menu-link">
                        <div data-i18n="Alerts">Giving Goods to Supplier</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ request()->is('returns*') ? 'active' : '' }}">
                    <a href="{{route('returns')}}" class="menu-link">
                        <div data-i18n="Accordion">{{__('messages.return')}}</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->is('selling-cities*','office-sellers*','office-sales*','office-loan*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div data-i18n="User interface">{{ __('messages.office_sellers') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('selling-cities*') ? 'active' : '' }}">
                    <a href="{{route('cities.selling')}}" class="menu-link">
                        <div data-i18n="Alerts">{{ __('messages.selling_city') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('office-sellers*') ? 'active' : '' }}">
                    <a href="{{route('sellers.office')}}" class="menu-link">
                        <div data-i18n="Alerts">{{ __('messages.office_sellers') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('office-sales*') ? 'active' : '' }}">
                    <a href="{{route('sales.office')}}" class="menu-link">
                        <div data-i18n="Accordion">{{ __('messages.office_sales') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('office-loan*') ? 'active' : '' }}">
                    <a href="{{route('loan.office')}}" class="menu-link">
                        <div data-i18n="Accordion">{{ __('messages.office_loan') }}</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->is('orders*') ? 'active' : '' }}">
            <a href="{{route('orders')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div>{{__('messages.orders')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('damage-replace*') ? 'active' : '' }}">
            <a href="{{route('damage_replace')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-repost"></i>
                <div>{{__('messages.damage_replace')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('exchange-city*') ? 'active' : '' }}">
            <a href="{{route('exchange_city')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-repost"></i>
                <div>{{__('messages.exchange_city')}}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('loan*') ? 'active' : '' }}">
            <a href="{{route('loan')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div>{{ __('messages.loan') }}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('expenditure*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="User interface">{{__('messages.expenditure')}}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('expenditure/giving*') ? 'active' : '' }}">
                    <a href="{{route('expenditure.giving')}}" class="menu-link">
                        <div data-i18n="Alerts">{{__('messages.giving_expenditure')}}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('expenditure/office*') ? 'active' : '' }}">
                    <a href="{{route('expenditure.office')}}" class="menu-link">
                        <div data-i18n="Alerts">{{__('messages.office_expenditure')}}</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('expenditure/seller*') ? 'active' : '' }}">
                    <a href="{{route('expenditure.seller')}}" class="menu-link">
                        <div data-i18n="Alerts">{{__('messages.seller_expenditure')}}</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->is('report*') ? 'active' : '' }}">
            <a href="{{route('report')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-file-pdf"></i>
                <div>{{ __('messages.reports') }}</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('demands*') ? 'active' : '' }}">
            <a href="{{route('demands')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div>{{ __('messages.demands') }}</div>
            </a>
        </li>
    </ul>
</aside>
