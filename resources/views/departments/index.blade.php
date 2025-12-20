@extends('layouts.layout_ajax')

@section('content')

<link rel="stylesheet" href="{{ asset('/libs/datatable/datatables.min.css') }}">
<script src="{{ asset('/libs/datatable/datatables.min.js') }}"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Manage Departments</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable mt-2"
                               style="font-size:10pt;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th width="140">Action</th>
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
                                        <span class="badge"
                                              style="background: {{ $dept->color }};
                                                     color:#fff;
                                                     padding:6px 10px;">
                                            {{ $dept->color }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if($dept->status)
                                            <i class="fa fa-toggle-on text-success"
                                               style="font-size:16pt;"></i>
                                        @else
                                            <i class="fa fa-toggle-off text-danger"
                                               style="font-size:16pt;"></i>
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

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.datatable').dataTable();
});

$(document).on('click', '#addDepartment', function () {
    openDeptForm();
});

$(document).on('click', '.editDept', function () {
    openDeptForm($(this).data('id'));
});

function openDeptForm(id = '') {
    preloader.load();
    $.post("{{ route('departments.manage') }}", {
        _token: "{{ csrf_token() }}",
        id: id
    }, function (html) {
        preloader.stop();
        const offcanvas = new bootstrap.Offcanvas('#offcanvasCustom');
        $('#offcanvasCustomHead').html('Manage Department');
        $('#offcanvasCustomBody').html(html);
        offcanvas.show();
    });
}
</script>

@endsection

@section('footer')
<button class="btn btn-sm btn-dark" id="addDepartment">
    <i class="ri-add-line"></i>&nbsp;Add Department
</button>
@endsection
