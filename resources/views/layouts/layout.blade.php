<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Focus Academy</title>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('/css/adminlte.min.css?v=3.2.0') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/ajax_loader.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">


</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/ajax_loader.js') }}"></script>

    <div class="loader-container">
        <div id="ajaxloader">
            <div class="loader-border">
                <img id="loader" class="animation__shake shadow_loader" src="{{ asset('images/logo-n1.png') }}"
                    alt="Focuz" height="60" width="60">
            </div>
        </div>
    </div>
    <div class="wrapper">

        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('images/logo-n1.png') }}" alt="Focuz" height="60"
                width="60">
        </div>

        @include('layouts.topnav')


        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="{{ route('home_page') }}" class="brand-link">
                <img src="{{ asset('images/logo.png') }}" loading="lazy" alt="Focuz Academy Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Focuz Academy CRM</span>
            </a>

            @include('layouts.sidebar')

        </aside>

        <div class="content-wrapper">
            @if ($layout_menu_exist !== null)
            @php
            $padding_top = 'pt-5';
            $padding_top2 = 'pt-4';
            @endphp

            @if (view()->exists($layout_menu_exist))
            @include($layout_menu_exist)
            @else
            {{-- <p>The requested view does not exist.</p> --}}
            @endif
            @else
            @php
            $padding_top = 'pt-3';
            $padding_top2 = 'pt-2';
            @endphp
            @endif

            <section class="content-header  {{ $padding_top }}">
                <div class="container-fluid {{ $padding_top2 }}">
                    <div class="row pt-5">
                        <div class="col-sm-6 col-6 pl-3">
                            <h3>{{ ucwords(str_replace('_', ' ', $path)) }} </h3>

                        </div>
                        <div class="col-sm-6 col-6 text-right px-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">Home</a></li>
                                <li class="breadcrumb-item active">{{ ucwords(str_replace('_', ' ', $path)) }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            @yield('content')
        </div>
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center" id="modalMessage">
                        <!-- Message will be injected here -->
                    </div>
                </div>
            </div>
        </div>
        {{-- @include('layouts.footer') --}}

    </div>
    <a href="" id="download" target="blank" download></a>
    <a href="" id="c_view_file" target="blank"></a>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js?v=3.2.0') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // const currentPath = "{{ $path }}";
            // const requestMethod = "{{$requestMethod}}";
            // Object.keys(localStorage).forEach(key => {
            //     if (key == 'form_datas') {
            //         const value = localStorage.getItem(key);
            //         console.log(value)
            //         if (value) {
            //             try {
            //                 const jsonObject = JSON.parse(value);
            //                 console.log(requestMethod)
            //                 if (!(currentPath in jsonObject) || requestMethod == 'GET') {
            //                     // localStorage.removeItem(key);
            //                     console.log(jsonObject);
            //                 }
            //             } catch (e) {

            //             }
            //         }
            //     }


            // });
        });
    </script>

</body>

@yield('script')


</html>
