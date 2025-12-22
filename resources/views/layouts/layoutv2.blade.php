<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Student Management Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Student Management Portal" name="description" />
    <meta content="Student Management Portal" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- plugin css -->
    <link href="{{ asset('v2/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout Js -->
    <script src="{{ asset('v2/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('v2/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('v2/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('v2/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('v2/css/app_custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <script src="{{ asset('v2/js/jquery.min.js') }}"></script>
    <script src="{{ asset('v2/js/default_validation.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('/css/ajax_loader.css') }}">
    <link href="{{ asset('libs/selectpicker/bootstrap-select.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

</head>

<body data-sidebar="colored">
    <script defer src="{{ asset('/js/ajax_loader.js') }}"></script>

    <div class="loader-container">
        <div id="ajaxloader">
            <div class="loader-border">
                <img id="loader" class="animation__shake shadow_loader" src="{{ asset('images/logo.png') }}"
                    alt="Focuz" height="60" width="60">
            </div>
        </div>
    </div>

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">



        @include('layouts.headerv2')
        @include('layouts.sidebarv2')

        <div class="main-content">

            <div class="page-content" id="page-content-ajax">

                @yield('content')
            </div>
            <!-- End Page-content -->


        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->

    <!-- /Right-bar -->

    @include('layouts.offcanvases')
    @include('layouts.bm_modals')
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('v2/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('v2/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('v2/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('v2/node-waves/waves.min.js') }}"></script>

    <script src="{{ asset('libs/selectpicker/bootstrap-select.min.js') }}"></script>

    <!-- Icon -->
    <script src="https://unicons.iconscout.com/release/v2.0.1/script/monochrome/bundle.js"></script>

    <!-- apexcharts -->
    <script src="{{ asset('v2/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Vector map-->
    {{-- <script src="{{ asset('v2/jsvectormap/jsvectormap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('v2/jsvectormap/maps/world-merc.js') }}"></script> --}}

    {{-- <script src="{{ asset('v2/appjs/dashboard.init.js') }}"></script> --}}

    <!-- App js -->
    <script src="{{ asset('v2/js/app.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

</body>

</html>
