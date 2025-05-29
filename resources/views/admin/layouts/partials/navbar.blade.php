<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-attached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        {{--<div class="navbar-nav align-items-center">--}}
            {{--<div class="nav-item d-flex align-items-center">--}}
                {{--<i class="bx bx-search fs-4 lh-0"></i>--}}
                {{--<input type="text" class="form-control border-0 shadow-none" placeholder="Search..."--}}
                    {{--aria-label="Search..." />--}}
            {{--</div>--}}
        {{--</div>--}}
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Place this tag where you want the button to render. -->
            <a class="nav-item" href="javascript:void(0);" data-bs-toggle="dropdown">
                <li>
                    <a class="me-3 fs-5" href="{{route('logout')}}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{-- <i class="bx bx-power-off me-2"></i> --}}
                        <span class="align-middle">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{asset('public/assets/img/avatars/1.png')}}" class="h-auto rounded-circle" style="width: 50px" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{asset('public/assets/img/avatars/1.png')}}" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    @if(Auth::check() && Auth::user()->name)
                                    <span class="fw-semibold d-block">{{Auth::user()->name}}</span>
                                    @else
                                    <span class="fw-semibold d-block">Guest</span>
                                    @endif
                                    <small class="text-muted">
                                        @if(Auth::check())
                                            @if(Auth::user()->role == 0)
                                            Admin
                                            @elseif( Auth::user()->role == 1)
                                            Seller
                                            @elseif( Auth::user()->role == 2)
                                            User
                                            @endif
                                        @else
                                        Guest
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    {{-- <li>
                        <a class="dropdown-item" href="{{route('logout')}}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li> --}}
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
