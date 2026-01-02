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
                        <table id="view_students"
                               class="table table-bordered table-striped datatable mt-2"
                               style="font-size:10pt;">
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
                                @forelse ($emp_dataAr as $emp_data)

                                    {{-- SAFETY: skip broken employee rows --}}
                                    @if(!$emp_data->users)
                                        
                                        @continue
                                    @endif

                                    <tr>
                                        {{-- NAME --}}
                                        <td>
                                            <a role="button"
                                               class="add_new_user btn-link"
                                               data-user_id="{{ $emp_data->users->id }}">
                                                {{ $emp_data->first_name }}
                                            </a>
                                        </td>

                                        {{-- EMAIL --}}
                                        <td>{{ $emp_data->email }}</td>

                                        {{-- PHONE --}}
                                        <td>{{ $emp_data->phone_number ?? '—' }}</td>

                                        {{-- EMP CODE --}}
                                        <td>{{ $emp_data->emp_code ?? '—' }}</td>

                                        {{-- ROLE --}}
                                        <td>
                                            <select class="student_category form-control form-control-sm">
                                                @foreach ($user_categories as $user_category)
                                                    <option value="{{ $user_category->id }}"
                                                            emp_id="{{ $emp_data->users->id }}"
                                                            {{ $user_category->id == $emp_data->users->role_id ? 'selected' : '' }}>
                                                        {{ $user_category->role_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        {{-- STATUS --}}
                                        <td>
                                            @if ($emp_data->users->is_active != 1)
                                                <i emp_id="{{ $emp_data->users->id }}"
                                                   id="toggle_emp_status_{{ $emp_data->users->id }}"
                                                   class="fa fa-toggle-off text-danger toggle_emp_status"
                                                   style="font-size:15pt; cursor:pointer;"></i>
                                            @else
                                                <i emp_id="{{ $emp_data->users->id }}"
                                                   id="toggle_emp_status_{{ $emp_data->users->id }}"
                                                   class="fa fa-toggle-on text-success toggle_emp_status"
                                                   style="font-size:15pt; cursor:pointer;"></i>
                                            @endif
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No employees found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                @if($emp_dataAr instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer">
                        {{ $emp_dataAr->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    $('.datatable').dataTable();

    // CHANGE ROLE
    $(document).on('change', '.student_category', function (e) {
        e.preventDefault();

        const user_category = $(this).val();
        const emp_id = $(this).find(':selected').attr('emp_id');

        if (!emp_id) return;

        if (!confirm("Are you sure want to change the user role?")) return;

        preloader.load();

        $.ajax({
            type: "POST",
            url: "{{ route('update_emp_role') }}",
            data: {
                emp_id: emp_id,
                user_category: user_category,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                preloader.stop();
                showAlert(response.message);
            },
            error: function (xhr) {
                preloader.stop();
                showAlert(xhr.responseJSON?.message ?? 'Something went wrong', 'error');
            }
        });
    });

    // TOGGLE STATUS
    $(document).on('click', '.toggle_emp_status', function (e) {
        e.preventDefault();

        const emp_id = $(this).attr('emp_id');
        if (!emp_id) return;

        if (!confirm("Are you sure want to change the user status?")) return;

        preloader.load();

        $.ajax({
            type: "POST",
            url: "{{ route('update_emp_status') }}",
            data: {
                emp_id: emp_id,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                preloader.stop();

                const elem = $('#toggle_emp_status_' + response.emp_id);
                elem.toggleClass('fa-toggle-on text-success fa-toggle-off text-danger');

                showAlert(response.message);
            },
            error: function (xhr) {
                preloader.stop();
                showAlert(xhr.responseJSON?.message ?? 'Something went wrong', 'error');
            }
        });
    });

});
</script>

@endsection


@section('footer')
<button class="btn btn-sm btn-dark add_new_user" data-user_id="">
    <i class="ri-user-add-line"></i>&nbsp;Add New User
</button>

<script>
$(document).ready(function () {

    $('.add_new_user').on('click', function (e) {
        e.preventDefault();

        preloader.load();

        $.ajax({
            url: "{{ route('employee.manage_user') }}/" + ($(this).data('user_id') ?? ''),
            type: 'GET',
            success: function (response) {
                preloader.stop();

                const offcanvasEl = document.getElementById('offcanvasCustom');
                const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
                bsOffcanvas.show();

                $('#offcanvasCustomHead').html("Manage User");
                $('#offcanvasCustomBody').html(response);
            },
            error: function () {
                preloader.stop();
                alert("Something went wrong, Please contact support");
            }
        });
    });

});
</script>
@endsection