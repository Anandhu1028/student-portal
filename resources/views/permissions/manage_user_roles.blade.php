@extends('layouts.layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/datatables.min.css') }}">
    <script src="{{ asset('/js/datatables.min.js') }}"></script>
    <section class="content">
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header ">
                            <div class="row">
                                <div class="col d-flex justify-content-between">
                                    <h3 class="card-title">View Permissions (Common Permissions is not displayed)</h3>
                                    <button class="btn btn-sm btn-primary manage_permissions" data-url_id="new"
                                        id="add_new_permission">Add
                                        New</button>
                                </div>
                            </div>


                        </div>

                        <div class="card-body">
                            <div class="table-responsive">

                                <table class="table table-sm table-striped" id="table_permission">

                                    <thead>
                                        <tr>
                                            <th>URL Name</th>
                                            <th>URL</th>
                                            <th>Allowed Roles</th>
                                            <th>Allowed Users</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($urls as $url)
                                            <tr>
                                                <td>{{ $url->name }}</td>
                                                <td>{{ $url->url }}</td>
                                                <td>

                                                    @if (isset($allowedUrls[$url->id]))
                                                        {{ $allowedUrls[$url->id]->pluck('role.role_name')->filter()->unique()->implode(', ') }}
                                                        <div class="d-none"
                                                            id="url_permission_users_data_{{ $url->id }}">
                                                            <table class="table table-sm  table-striped">
                                                                <tr>
                                                                    <th colspan="3">Page : {{ $url->name }}</th>
                                                                </tr>
                                                                <tr>
                                                                    <td> Name</td>
                                                                    <td> Email </td>
                                                                    <td> Role</td>
                                                                </tr>
                                                                @foreach ($allowedUrls[$url->id] as $urlId => $entry)
                                                                    <tr>
                                                                        <td>{{ $entry->user->name }}</td>
                                                                        <td>{{ $entry->user->email }}</td>
                                                                        <td>{{ $entry->user->roles->role_name }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    @else
                                                        No Roles
                                                    @endif
                                                </td>
                                                <td>

                                                    <button
                                                        data-modaldata-id="#url_permission_users_data_{{ $url->id }}"
                                                        class="btn btn-transparent btn-primary btn-sm view_users_permission">View</button>
                                                </td>
                                                <td><button data-url_id="{{ $url->id }}"
                                                        class="btn btn-transparent btn-primary btn-sm manage_permissions">Edit</button>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal" id="modal_view_users" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Users Allowed</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body">

                </div>
            </div>

        </div>

    </div>

    <script>
        $(document).ready(function() {

            $('#table_permission').DataTable({
                lengthMenu: [10, 25, 100, 500, 1000, 5000, 10000],
                pageLength: 25 // Set default length if desired
            })

            $(document).on('click', '.view_users_permission', function(e) {
                e.preventDefault();
                const modal_data = $($(this).data('modaldata-id')).html();
                $('.modal-title').html('View Users Allowed');
                $('#modal_view_users').modal('show');
                $('#modal_body').html(modal_data);
            });




            $(document).on('click', '.manage_permissions', function(e) {
                e.preventDefault();
                const url_id = $(this).data('url_id');
                preloader.load()
                $.ajax({
                    type: "post",
                    url: "{{ route('manage_user_permissions') }}",
                    data: {
                        'url_id': url_id,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        preloader.stop()
                        $('.modal-title').html('Manage URL Permissions');
                        $('#modal_view_users').modal('show');
                        $('#modal_body').html(response);

                    },
                    error: function(xhr) {
                        preloader.stop()
                        //console.log(xhr.responseText)
                    }
                });

            });
        });
    </script>
@endsection
