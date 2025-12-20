@php
    $menus = session('menus', []);

@endphp
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('images/logo.png') }}" alt="logo-sm-dark" height="24">
                <span class="text-white">&nbsp;&nbsp;{{ config('app.name') }}</span>
            </span>
            <span class="logo-lg">
                <img src="{{ asset('images/logo.png') }}" alt="logo-dark" height="22">
                <span class="text-white" style="font-size: 17px">&nbsp;&nbsp;{{ config('app.name') }}
                </span>
            </span>
        </a>

        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('images/logo.png') }}" alt="logo-sm-light" height="30">
                <span class="text-white" style="font-size: 17px">&nbsp;&nbsp;{{ config('app.name') }}
                </span>
            </span>
            <span class="logo-lg">
                <img src="{{ asset('images/logo.png') }}" alt="logo-light" height="35">
                <span class="text-white" style="font-size: 20px;">&nbsp;&nbsp;{{ config('app.name') }}
                </span>
            </span>
        </a>

    </div>

    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn"
        id="vertical-menu-btn">
        <i class="ri-menu-2-line align-middle"></i>
    </button>

    <div data-simplebar class="vertical-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="dropdown mx-3 sidebar-user user-dropdown select-dropdown">
                <button type="button" class="btn btn-light w-100 waves-effect waves-light border-0"
                    id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-xs rounded-circle flex-shrink-0">
                                <div
                                    class="avatar-title border bg-light text-primary rounded-circle text-uppercase user-sort-name">
                                    IA</div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2 text-start">
                            <h6 class="mb-1 fw-medium user-name-text"> Intelligent Assist </h6>
                            <p class="font-size-13 text-muted user-name-sub-text mb-0"> Student Portal </p>
                        </div>
                        {{-- <div class="flex-shrink-0 text-end">
                            <i class="mdi mdi-chevron-down font-size-16"></i>
                        </div> --}}
                    </span>
                </button>
                {{-- <div class="dropdown-menu dropdown-menu-end w-100">
                    <!-- item-->
                    <a class="dropdown-item d-flex align-items-center px-3" href="#">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-xs rounded-circle flex-shrink-0">
                                <div class="avatar-title border rounded-circle text-uppercase dropdown-sort-name">
                                    C</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 dropdown-name">CRM</h6>
                            <p class="text-muted font-size-13 mb-0 dropdown-sub-desc">Designer Team</p>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center px-3" href="#">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-xs rounded-circle flex-shrink-0">
                                <div class="avatar-title border rounded-circle text-uppercase dropdown-sort-name">
                                    A</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 dropdown-name">Application Design</h6>
                            <p class="text-muted font-size-13 mb-0 dropdown-sub-desc">Flutter Devs</p>
                        </div>
                    </a>

                    <a class="dropdown-item d-flex align-items-center px-3" href="#">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-xs rounded-circle flex-shrink-0">
                                <div class="avatar-title border rounded-circle text-uppercase dropdown-sort-name">
                                    E</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 dropdown-name">Ecommerce</h6>
                            <p class="text-muted font-size-13 mb-0 dropdown-sub-desc">Developer Team</p>
                        </div>
                    </a>

                    <a class="dropdown-item d-flex align-items-center px-3" href="#">
                        <div class="flex-shrink-0 me-2">
                            <div class="avatar-xs rounded-circle flex-shrink-0">
                                <div class="avatar-title border rounded-circle text-uppercase dropdown-sort-name">
                                    R</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 dropdown-name">Reporting</h6>
                            <p class="text-muted font-size-13 mb-0 dropdown-sub-desc">Team Reporting</p>
                        </div>
                    </a>

                    <a class="btn btn-sm btn-link font-size-14 text-center w-100" href="javascript:void(0)">
                        View More..
                    </a>
                </div> --}}
            </div>
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                @foreach ($menus as $title => $menuGroup)
                    <li class="menu-title">{{ $title }}</li>
                    @foreach ($menuGroup as $menu)
                        @if (!empty($menu['submenus']))
                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    <span> {{ $menu['name'] }}</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    @foreach ($menu['submenus'] as $submenu)
                                        <li data-url="{{ route($submenu['url']) }}"
                                            data-actionmenu="{{ $submenu['action_menu'] }}" class="open_menu"
                                            data-menu_name="{{ $submenu['name'] }}">
                                            <a role="button">{{ $submenu['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            @php
                                $routeExists = Route::has($menu['url']);
                            @endphp
                            @if ($routeExists)
                                <li class="open_menu" data-menu_name="{{ $menu['name'] }}"
                                    data-url="{{ route($menu['url']) }}"
                                    data-actionmenu="{{ url($menu['action_menu']) }}">
                                    <a class="waves-effect">
                                        <i class="{{ $menu['icon'] }}"></i>
                                        <span> {{ $menu['name'] }}</span>

                                    </a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                @endforeach

            </ul>

        </div>
        <!-- Sidebar -->
    </div>

    <div class="dropdown px-3 sidebar-user sidebar-user-info">
        <button type="button" class="btn w-100 px-0 border-0" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center">
                <div class="flex-shrink-0">

                    @if (Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                            class="img-fluid header-profile-user rounded-circle" alt="User Image">
                    @else
                        <img src="{{ asset('v2/images/users/avatar-2.jpg') }}"
                            class="img-fluid header-profile-user rounded-circle" alt="">
                    @endif

                </div>

                <div class="flex-grow-1 ms-2 text-start">
                    <span class="ms-1 fw-medium user-name-text">{{ Auth::user()->name }}


                    </span>
                    <br />
                    <span class="ms-1 fw-medium user-name-sub-text">
                        {{ Auth::user()->roles->role_name }}
                    </span>
                </div>

                <div class="flex-shrink-0 text-end">
                    <i class="mdi mdi-dots-vertical font-size-16"></i>
                </div>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="mdi mdi-lock text-muted font-size-16 align-middle me-1"></i> <span
                        class="align-middle">Logout</span>
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        let currentRoute = urlParams.get('route');
        let route_name = "Dashboard";

        let lastPageFooter = "0";
        let routeParams = "";
        if (currentRoute) {

            route_name = localStorage.getItem('lastPageName');
            lastPageFooter = localStorage.getItem('lastPageFooter');
            routeParams = urlParams.get('route_params');
        } else {
            currentRoute = "{{ route('dashboard') }}"
        }
        loadMenuPage(currentRoute, route_name, lastPageFooter, routeParams, false);
        $(document).on('click', '.open_menu', function(e) {


            e.preventDefault();
            const menu_url = $(this).data('url'); // Example: /view_employees?page=2
            const menu_name = $(this).data('menu_name'); // Example: /view_employees?page=2
            const action_menu = $(this).data('action_menu'); // to know whether there is footer menu
            const route_params = $(this).data('route_params'); // to know whether there is footer menu
            loadMenuPage(menu_url, menu_name, true, route_params);
        });

        window.onpopstate = function() {
            const newParams = new URLSearchParams(window.location.search);
            const route_name = newParams.get('route');
            const route_params = newParams.get('route_params');
            if (route_name) {
                loadMenuPage(page, false);
            }
        };
    });

    function loadMenuPage(menu_url, menu_name = '', lastPageFooter = false, routeParams = null, updateUrl = true) {
        preloader.load();

        if (routeParams !== null && routeParams !== '') {
            menu_url = menu_url + '/' + routeParams
        }
        console.log(menu_url)
        $.ajax({
            url: menu_url,
            type: 'GET',

            success: function(data) {

                preloader.stop();
                // Remove old content
                $('#page-content-ajax').empty();

                // Optionally, remove old event handlers attached to elements inside it
                $('#page-content-ajax').off();

                $('#page-content-ajax').html(data);
                $('#page_title_header').html(menu_name)


                if (lastPageFooter) {
                    $('#footer_layout').removeClass('d-none');
                }

                if (updateUrl) {
                    const baseUrl = window.location.origin + window.location.pathname;
                    const parser = new URL(menu_url, window.location.origin);

                    // Parse the path and query string from menu_url
                    const path = parser.pathname.replace(/^\//, '');
                    const queryParams = [];
                    queryParams.push(`route=${path}`);

                    for (const [key, value] of parser.searchParams.entries()) {
                        queryParams.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
                    }

                    const newUrl = `${baseUrl}?${queryParams.join('&')}`;
                    history.pushState(null, '', newUrl);
                }

                localStorage.setItem('lastPageUrl', menu_url);
                localStorage.setItem('lastPageName', menu_name);
                localStorage.setItem('lastPageFooter', lastPageFooter);
            },
            error: function(xhr) {
                preloader.stop();
                console.log(xhr.responseJSON)
                $('#page-content-ajax').html('<p>Error loading content.</p>');
            }
        });
    }
</script>
