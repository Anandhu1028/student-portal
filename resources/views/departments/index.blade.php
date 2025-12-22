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
                    <th>Assigned Users</th>
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
                    @if($dept->users->count())
                        @php
                            $names = $dept->users->pluck('name')->implode(', ');
                        @endphp

                        <div class="assigned-users-wrap">
                            <span class="assigned-users-text">
                                {{ $names }}
                            </span>
                        </div>
                    @else
                        <i class="text-secondary">No users selected</i>
                    @endif
                </td>


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

                    <td class="text-center">
                        @if($dept->status)
                            <i class="fa fa-toggle-on text-success toggleDeptStatus"
                            style="font-size:18px; cursor:pointer;"
                            data-id="{{ $dept->id }}"
                            data-status="1"></i>
                        @else
                            <i class="fa fa-toggle-off text-danger toggleDeptStatus"
                            style="font-size:18px; cursor:pointer;"
                            data-id="{{ $dept->id }}"
                            data-status="0"></i>
                        @endif
                    </td>


                    <td>
                        <div class="d-flex gap-2">
                            

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
<style>
.assigned-users-wrap {
    max-width: 220px;          /* adjust for your table */
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    cursor: pointer;
}

.assigned-users-text {
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all 0.35s ease;
}

/* ON HOVER — EXPAND */
.assigned-users-wrap:hover {
    max-width: 1000px;        /* large enough to fit all names */
    white-space: normal;
}

.assigned-users-wrap:hover .assigned-users-text {
    overflow: visible;
    text-overflow: unset;
}
</style>


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




$(document).off('click', '.toggleDeptStatus')
.on('click', '.toggleDeptStatus', function () {

    const el = $(this);
    const id = el.data('id');

    if (!confirm('Are you sure you want to change department status?')) {
        return;
    }

    preloader.load();

    $.post("{{ route('departments.save') }}", {
        _token: "{{ csrf_token() }}",
        id: id,
        toggle_status: true
    })
    .done(function (res) {

        preloader.stop();

        // Toggle icon visually
        if (res.status === 1) {
            el.removeClass('fa-toggle-off text-danger')
              .addClass('fa-toggle-on text-success')
              .data('status', 1);
        } else {
            el.removeClass('fa-toggle-on text-success')
              .addClass('fa-toggle-off text-danger')
              .data('status', 0);
        }

        showAlert('Department status updated');
    })
    .fail(function () {
        preloader.stop();
        showAlert('Failed to update status', 'error');
    });
});



document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});


</script>
@endsection

@section('footer')
<button class="btn btn-sm btn-dark" id="addDepartment">
    <i class="ri-add-line"></i> Add Department
</button>
@endsection
