@extends('layouts.layout_ajax')
@section('content')


    <link rel="stylesheet" href="{{ asset('/libs/datatable/datatables.min.css') }}">
    <script src="{{ asset('/libs/datatable/datatables.min.js') }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">View Employees</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="view_students" style="font-size:10pt;"
                                class="table table-bordered table-striped datatable mt-2">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Emp Code</th>
                                        <th>User Category</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_view_students">
                                    @if (!(is_array($emp_dataAr) && empty($emp_dataAr)))
                                        @foreach ($emp_dataAr as $emp_data)
                                            <tr>
                                                <td>
                                                    <a role="button" class="add_new_user btn-link"
                                                        data-user_id="{{ $emp_data->users->id }}">
                                                        {{ $emp_data->first_name }}
                                                    </a>
                                                </td>
                                                <td>{{ $emp_data->email }}</td>
                                                <td>{{ $emp_data->phone_number }}</td>
                                                <td>{{ $emp_data->emp_code }}</td>

                                                <td>

                                                    <select class="student_category form-control form-control-sm">
                                                        @foreach ($user_categories as $user_category)
                                                            @php
                                                                if ($user_category->id == $emp_data->users->role_id) {
                                                                    $selected_role = 'selected';
                                                                } else {
                                                                    $selected_role = '';
                                                                }
                                                            @endphp
                                                            <option value="{{ $user_category->id }}"
                                                                emp_id="{{ $emp_data->users->id }}" {{ $selected_role }}>
                                                                {{ $user_category->role_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </td>
                                                <td>
                                                    @if ($emp_data->users->is_active != 1)
                                                        <i emp_id="{{ $emp_data->users->id }}"
                                                            id="toggle_emp_status_{{ $emp_data->users->id }}"
                                                            class="fa fa-toggle-off text-danger toggle_emp_status"
                                                            style="font-size:15pt;cursor:pointer;" aria-hidden="true"></i>
                                                    @else
                                                        <i emp_id="{{ $emp_data->users->id }}"
                                                            id="toggle_emp_status_{{ $emp_data->users->id }}"
                                                            class="fa fa-toggle-on text-success toggle_emp_status"
                                                            style="font-size:15pt;cursor:pointer;" aria-hidden="true"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if (!(is_array($emp_dataAr) && empty($emp_dataAr)))
                        <div class="pagination">
                            {{ $emp_dataAr->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('.datatable').dataTable();
            $(document).on('change', '.student_category', function(e) {
                e.preventDefault();
                const user_category = $(this).val();
                const emp_id = $(this).find(':selected').attr('emp_id')
                if (window.confirm("Are you sure want to change the user role?")) {
                    preloader.load();
                    $.ajax({
                        type: "post",
                        url: "{{ route('update_emp_role') }}",
                        data: {
                            "emp_id": emp_id,
                            "user_category": user_category,
                            "_token": "{{ csrf_token() }}"
                        },

                        success: function(response) {
                            preloader.stop();
                            console.log(response)
                            showAlert(response.message);
                        },
                        error: function(xhr) {
                            preloader.stop();
                            console.log(xhr)
                            if (xhr.responseJSON.message) {
                                let errors = xhr.responseJSON.message;
                                showAlert(errors)
                            } else {
                                showAlert("Something went wrong, Please contact IT support");
                            }

                        }
                    });
                } else {
                    return false;
                }

            });

            $(document).on('click', '.toggle_emp_status', function(e) {
                e.preventDefault();
                const emp_id = $(this).attr('emp_id');
                if (window.confirm("Are you sure want to change the user status?")) {
                    preloader.load();
                    $.ajax({
                        type: "post",
                        url: "{{ route('update_emp_status') }}",
                        data: {
                            "emp_id": emp_id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            preloader.stop();
                            const new_status = response.new_status;
                            const elem = $(`#toggle_emp_status_${response.emp_id}`);
                            let remove = "";
                            let add = "";
                            if (new_status == 0) {
                                remove = "fa-toggle-on text-success";
                                add = "fa-toggle-off text-danger";
                            } else {
                                remove = "fa-toggle-off text-danger";
                                add = "fa-toggle-on text-success";
                            }
                            elem.removeClass(remove);
                            elem.addClass(add)
                            showAlert(response.message);
                        },
                        error: function(xhr) {
                            preloader.stop();
                            if (xhr.responseJSON.message) {
                                let errors = xhr.responseJSON.message;
                                showAlert(errors)
                            } else {
                                showAlert("Something went wrong, Please contact IT support");
                            }
                        }
                    });
                } else {
                    return false;
                }
            });
        });

        function showAlert(message, alert_type = 'success') {
            $('#modalMessage').html(message);
            $('#alertModal').modal('show');

        }
    </script>
@endsection
@section('footer')
    <button class="btn btn-sm btn-dark add_new_user"  data-user_id="">
        <i class=" ri-user-add-line "></i>&nbsp;Add New User
    </button>

    <script>
        $(document).ready(function() {
            $('.add_new_user').click(function(e) {
                e.preventDefault();
                preloader.load()
                $.ajax({
                    url: `{{ route('employee.manage_user') }}/${$(this).data('user_id')}`, // Make sure this route returns just the form
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
