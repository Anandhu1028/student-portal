@extends('layouts.layout_ajax')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">View Menus</h3>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Module</th>
                                <th>URL</th>
                                <th>Sub-Menus</th>
                                <th>URLs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $menu)
                                <tr>
                                    <td>
                                        <a href="#"
                                            onclick="loadForm('menu', {{ $menu->id }})">{{ $menu->name }}</a>
                                    </td>
                                    <td>{{ $menu->display_name }}</td>
                                    <td>{{ $menu->module ? $menu->module->name : '-' }}</td>
                                    <td>{{ $menu->url ?? '-' }}</td>
                                    <td>
                                        @if ($menu->subMenus->count() > 0)
                                            <i role="button" class="fas fa-list action-icon" data-bs-toggle="collapse"
                                                data-bs-target="#subMenuAccordion{{ $menu->id }}" aria-expanded="false"
                                                aria-controls="subMenuAccordion{{ $menu->id }}"></i>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($menu->OperationUrls->count() > 0)
                                            <i role="button" class="fas fa-list action-icon" data-bs-toggle="collapse"
                                                data-bs-target="#operationAccordion{{ $menu->id }}"
                                                aria-expanded="false"
                                                aria-controls="operationAccordion{{ $menu->id }}"></i>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>


                                @if ($menu->subMenus->count() > 0)
                                    <tr class="collapse" id="subMenuAccordion{{ $menu->id }}">
                                        <td colspan="7">
                                            <div class="accordion-table sub-menu-table">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Display Name</th>
                                                            <th>URL</th>
                                                            <th>Order</th>
                                                            <th>Roles</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($menu->subMenus as $subMenu)
                                                            <tr>
                                                                <td>{{ $subMenu->name }}</td>
                                                                <td>{{ $subMenu->display_name }}</td>
                                                                <td>{{ $subMenu->url ?? '-' }}</td>
                                                                <td>{{ $subMenu->order }}</td>
                                                                <td>

                                                                </td>
                                                                <td>
                                                                    <a href="#"
                                                                        onclick="loadForm('subMenu', {{ $subMenu->id }})">Edit</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                @if ($menu->OperationUrls->count() > 0)
                                    <tr class="collapse" id="operationAccordion{{ $menu->id }}">
                                        <td colspan="7">
                                            <div class="accordion-table sub-menu-table">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Menu</th>
                                                            <th>Sub Menu</th>
                                                            <th>URL</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($menu->OperationUrls as $OperationUrl)
                                                            <tr>
                                                                <td>{{ $OperationUrl->menus->display_name }}</td>
                                                                <td>
                                                                    @if ($OperationUrl->sub_menu_id)
                                                                      {{ $OperationUrl->sub_menus->display_name }}
                                                                    @endif

                                                                </td>
                                                                <td>{{ $OperationUrl->url ?? '-' }}</td>

                                                                <td>
                                                                    <a href="#"
                                                                        onclick="loadForm('url', {{ $OperationUrl->id }})">Edit</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <button class="btn btn-sm btn-dark" onclick="loadForm('menu', null)">
        <i class="ri-add-line "></i>&nbsp;Add New Menu
    </button>

    <button class="btn btn-sm btn-dark" onclick="loadForm('subMenu', null)">
        <i class="ri-add-line "></i>&nbsp;Add New Sub Menu
    </button>

    <button class="btn btn-sm btn-dark" onclick="loadForm('url', null)">
        <i class="ri-add-line "></i>&nbsp;Add New Operation
    </button>
    <link href="{{ asset('libs/selectpicker/bootstrap-select.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('libs/selectpicker/bootstrap-select.min.js') }}"></script>
    <script>
        function loadForm(type, id = null) {
            let url;
            let title;
            switch (type) {
                case 'menu':
                    url = '{{ route('menus.form') }}' + (id ? '/' + id : '');
                    title = id ? 'Edit Menu' : 'Add Menu';
                    break;
                case 'subMenu':
                    url = '{{ route('subMenus.form') }}' + (id ? '/' + id : '');
                    title = id ? 'Edit Sub-Menu' : 'Add Sub-Menu';
                    break;
                case 'url':
                    url = '{{ route('urls.form') }}' + (id ? '/' + id : '');
                    title = id ? 'Edit URL' : 'Add URL';
                    break;
            }
            preloader.load()
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    preloader.stop()
                    const offcanvasEl = document.getElementById('offcanvasCustom');
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
                    bsOffcanvas.show();
                    $('#offcanvasCustomHead').html("Manage Menus");
                    $('#offcanvasCustomBody').html(response);
                    $('#offcanvasCustomBody .selectpicker').selectpicker()
                }
            });
        }
        $(document).ready(function() {
            $('#add_new_user').click(function(e) {
                e.preventDefault();
                preloader.load()
                $.ajax({
                    url: "{{ route('employee.manage_user') }}", // Make sure this route returns just the form
                    type: 'GET',
                    success: function(response) {

                        preloader.stop()
                        const offcanvasEl = document.getElementById('offcanvasCustom');
                        const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
                        bsOffcanvas.show();
                        $('#offcanvasCustomHead').html("Manage User");
                        $('#offcanvasCustomBody').html(response);

                    },
                    error: function() {
                        preloader.stop()
                        alert("Something went wrong, Please try again / contact support")
                    }
                });
            });
        });
    </script>
@endsection
