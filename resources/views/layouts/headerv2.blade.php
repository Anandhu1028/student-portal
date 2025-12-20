<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('v2/images/logo-dark.png') }}" alt="logo-sm-dark" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('v2/images/logo-sm-dark.png') }}" alt="logo-dark" height="25">
                    </span>
                </a>

                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('v2/images/logo-light.png') }}" alt="logo-sm-light" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('v2/images/logo-sm-light.png') }}" alt="logo-light" height="25">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn"
                id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

            <!-- start page title -->
            <div class="page-title-box align-self-center d-none d-md-block">
                <h4 class="page-title mb-0" id="page_title_header"></h4>
            </div>
            <!-- end page title -->
        </div>

        <div class="d-flex">

            <!-- App Search-->
            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="ri-search-line"></span>
                </div>
            </form>

            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-search-line"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="mb-3 m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="ri-search-line"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button>
            </div>

            <button data-route="{{ route('session.refresh') }}" id="refresh_page" type="button"
                class="btn header-item  noti-icon waves-effect">
                <i class=" ri-refresh-line "></i>
            </button>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-notification-3-line"></i>
                    <span class="noti-dot"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="#!" class="small"> Clear All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;" class="pb-2">

                        @foreach ($G_notifications as $notification)
                            <div role="button" class="text-reset notification-item {{ $notification->class_name }}"
                                data-url="{{ $notification->notification_link }}"
                                data-notification_id="{{ $notification->id }}">
                                <div class="d-flex">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="ri-notification-2-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1">{{ $notification->notification_header }} </h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1">{{ $notification->notification_message }}</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i>
                                                {{ \App\Helpers\TimeHelper::timeAgo($notification->created_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach


                    </div>
                    {{-- <div class="p-2 border-top">
                        <div class="d-grid">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                <i class="mdi mdi-arrow-right-circle me-1"></i> View More..
                            </a>
                        </div>
                    </div> --}}
                </div>


            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-map-pin-user-fill"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div data-simplebar style="max-height: 230px;" class="pb-2 pt-3">
                        <div role="button" class="text-reset notification-item ">
                            <div class="d-flex align-items-center gap-0">

                                <div class="flex-shrink-0">
                                    @if (Auth::user()->profile_picture)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                            class="img-fluid header-profile-user rounded-circle" alt="User Image">
                                    @else
                                        <img src="{{ asset('v2/images/users/avatar-2.jpg') }}"
                                            class="img-fluid header-profile-user rounded-circle" alt="">
                                    @endif
                                </div>

                                <div class="ms-2"> <!-- add manual small spacing if needed -->
                                    <span class="fw-medium user-name-text">{{ Auth::user()->name }}</span><br>
                                    <span class="user-name-sub-text">{{ Auth::user()->roles->role_name }}</span>
                                </div>

                            </div>

                        </div>
                        <div role="button" class="text-reset notification-item ">
                            <div class="d-flex">
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                        <i class="ri-logout-box-line"></i>
                                    </span>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <h6>Logout </h6>
                                    </button>
                                </form>
                            </div>
                        </div>


                    </div>
                    {{-- <div class="p-2 border-top">
                        <div class="d-grid">
                            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                <i class="mdi mdi-arrow-right-circle me-1"></i> View More..
                            </a>
                        </div>
                    </div> --}}
                </div>


            </div>

        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refresh_page');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const route = this.getAttribute('data-route');
                if (route) {
                    window.location.href = route;
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function() {

        $('.manage_university_notification').click(function(e) {
            e.preventDefault();
            let notification_id = $(this).data('notification_id') || '';
            preloader.load();
            $.ajax({
                url: $(this).data('url'),
                type: 'GET',
                data: {
                    'notification_id': notification_id,
                },
                success: function(response) {
                    preloader.stop();
                    const offcanvasEl = document.getElementById('offcanvasCustom');
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
                    $('#offcanvasCustomHead').html("Manage University");
                    $('#offcanvasCustomBody').html(response);
                    bsOffcanvas.show();
                },
                error: function(xhr) {
                    preloader.stop();
                    popAlert("Something went wrong. Please try again.");
                }
            });
        });
    });
</script>
