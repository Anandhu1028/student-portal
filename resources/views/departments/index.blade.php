@extends('layouts.layout_ajax')

@section('content')
<link rel="stylesheet" href="{{ asset('/libs/datatable/datatables.min.css') }}">
<script src="{{ asset('/libs/datatable/datatables.min.js') }}"></script>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Departments</h5>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th style="width: 40%">Description</th>
                    <th>Color</th>
                    <th>Status</th>
                    <th width="160">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr>
                    <td>
                        <a role="button"
                           class="text-primary editDept"
                           data-id="{{ $dept->id }}">
                            {{ $dept->name }}
                        </a>
                    </td>

                    <td>{{ $dept->description }}</td>

                    <td>
                        <span style="
                            display:inline-block;
                            width:22px;
                            height:22px;
                            border-radius:4px;
                            background:{{ $dept->color }};
                            border:1px solid #ddd;
                        "></span>
                    </td>

                    <td>
                        @if($dept->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-info editDept"
                                    data-id="{{ $dept->id }}">
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger deleteDept"
                                    data-id="{{ $dept->id }}">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.datatable').DataTable();
});

/* ------------------------------
   ADD / EDIT
--------------------------------*/
$(document).off('click', '#addDepartment').on('click', '#addDepartment', function () {
    openDeptForm();
});

$(document).off('click', '.editDept').on('click', '.editDept', function () {
    openDeptForm($(this).data('id'));
});

function openDeptForm(id = '') {
    preloader.load();

    $.post("{{ route('departments.manage') }}", {
        _token: "{{ csrf_token() }}",
        id: id
    }, function (html) {

        preloader.stop();

        const offcanvasEl = document.getElementById('offcanvasCustom');
        const oc = new bootstrap.Offcanvas(offcanvasEl);

        $('#offcanvasCustomHead').html('Manage Department');
        $('#offcanvasCustomBody').html(html);

        oc.show();
    });
}

/* ------------------------------
   DELETE (THIS WAS MISSING)
--------------------------------*/
$(document).off('click', '.deleteDept').on('click', '.deleteDept', function () {

    const id = $(this).data('id');

    preloader.load();

    $.ajax({
        url: "{{ route('departments.delete') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id
        },
        success: function (res) {
            preloader.stop();

            // Success modal
            $('#modal_title_custom').html(`
                <i class="ri-checkbox-circle-line text-success me-1"></i> Deleted
            `);

            $('#modal_body_custom').html(`
                <div class="alert alert-success mb-0">
                    ${res.message}
                </div>
            `);

            $('#modal_footer_custom').html(`
                <button class="btn btn-sm btn-primary" id="deptDeleteOk">
                    OK
                </button>
            `);

            $('#modal_custom').modal('show');
        },
        error: function (xhr) {
            preloader.stop();

            $('#modal_title_custom_error').html(`
                <i class="ri-error-warning-line text-danger me-1"></i> Error
            `);

            $('#modal_body_custom_error').html(
                xhr.responseJSON?.message || 'Delete failed'
            );

            $('#modal_custom_error').modal('show');
        }
    });
});

/* ------------------------------
   REDIRECT AFTER OK
--------------------------------*/
$(document).off('click', '#deptDeleteOk').on('click', '#deptDeleteOk', function () {
    $('#modal_custom').modal('hide');
    loadMenuPage("{{ route('departments.index') }}", "Manage Departments");
});
</script>
@endsection

@section('footer')
<button class="btn btn-sm btn-dark" id="addDepartment">
    <i class="ri-add-line"></i> Add Department
</button>
@endsection
