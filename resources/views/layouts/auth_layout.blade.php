<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Student Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="{{ asset('v2/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('v2/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />



</head>

<body>
    <div class="auth-maintenance d-flex align-items-center min-vh-100">
        <div class="bg-overlay bg-light"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="auth-full-page-content d-flex min-vh-100 py-sm-5 py-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100 py-0 py-xl-3">


                                <div class="card my-auto overflow-hidden">
                                    <div class="row g-0">
                                        <div class="col-lg-6">
                                            <div class="bg-overlay bg-primary"></div>
                                            <div class="h-100 bg-auth align-items-end">
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="p-lg-5 p-4">
                                                <div>


                                                    @yield('content')
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card -->

                                <div class="mt-5 text-center">
                                    <p class="mb-0">©
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script> <i
                                            class="mdi mdi-heart text-danger"></i> Student Portal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
    </div>


</body>

</html>
