@extends('layouts.layout_ajax')

@section('content')
<link rel="stylesheet" href="{{ asset('/libs/datatable/datatables.min.css') }}">
<script src="{{ asset('/libs/datatable/datatables.min.js') }}"></script>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Manage Departments</h5>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th width="40%">Description</th>
                    <th>Assigned Users</th>
                    <th>Color</th>
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr>
                    <td>
                        <a class="text-primary editDept" data-id="{{ $dept->id }}">
                            {{ $dept->name }}
                        </a>
                    </td>

                    <td>{{ $dept->description }}</td>

                    <td>
                        <select class="selectpicker deptUserSelect"
                                multiple
                                data-live-search="true"
                                data-actions-box="true"
                                data-dept="{{ $dept->id }}"
                                title="Select users">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ $dept->users->contains($user->id) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <span style="display:inline-block;width:22px;height:22px;border-radius:4px;background:{{ $dept->color }}"></span>
                    </td>

                    <td class="text-center">
                        <i class="fa {{ $dept->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }}
                           toggleDeptStatus"
                           data-id="{{ $dept->id }}"
                           style="cursor:pointer;font-size:18px"></i>
                    </td>

                    <td>
                        <button class="btn btn-sm btn-danger deleteDept" data-id="{{ $dept->id }}">
                            Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- CONFIRM MODAL --}}
<div class="modal fade" id="confirmDeptUserModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Update assigned users?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="confirmDeptUserCancel">Cancel</button>
                <button class="btn btn-primary" id="confirmDeptUserOk">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
/* =====================================================
   STATE
===================================================== */
let previousUsers = {};
let activeDeptId = null;

/* =====================================================
   INIT
===================================================== */
$(document).ready(function () {
    $('.datatable').DataTable();
    $('.selectpicker').selectpicker();
});

/* =====================================================
   SAVE STATE WHEN DROPDOWN OPENS
===================================================== */
$(document).on('shown.bs.select', '.deptUserSelect', function () {
    activeDeptId = $(this).data('dept');
    previousUsers[activeDeptId] = ($(this).val() || []).map(String);
});

/* =====================================================
   SHOW MODAL WHEN DROPDOWN CLOSES
===================================================== */
$(document).on('hidden.bs.select', '.deptUserSelect', function () {
    if (activeDeptId !== null) {
        $('#confirmDeptUserModal').modal('show');
    }
});

/* =====================================================
   CONFIRM OK → SYNC DB
===================================================== */
$(document).on('click', '#confirmDeptUserOk', function () {

    if (!activeDeptId) return;

    const select = $(`.deptUserSelect[data-dept="${activeDeptId}"]`);
    const newVals = (select.val() || []).map(String);
    const oldVals = previousUsers[activeDeptId] || [];

    const attach = newVals.filter(v => !oldVals.includes(v));
    const detach = oldVals.filter(v => !newVals.includes(v));

    // nothing changed
    if (!attach.length && !detach.length) {
        resetDeptModal();
        return;
    }

    $.post("{{ route('departments.user.toggle') }}", {
        _token: "{{ csrf_token() }}",
        department_id: activeDeptId,
        attach: attach,
        detach: detach
    })
    .done(() => {
        previousUsers[activeDeptId] = [...newVals];
    })
    .fail(() => {
        // rollback UI
        select.selectpicker('val', oldVals);
        alert('Update failed');
    })
    .always(() => {
        resetDeptModal();
    });
});

/* =====================================================
   CANCEL → ROLLBACK
===================================================== */
$(document).on('click', '#confirmDeptUserCancel', function () {
    if (!activeDeptId) return;

    const select = $(`.deptUserSelect[data-dept="${activeDeptId}"]`);
    select.selectpicker('val', previousUsers[activeDeptId]);

    resetDeptModal();
});

/* =====================================================
   RESET MODAL STATE
===================================================== */
function resetDeptModal() {
    activeDeptId = null;
    $('#confirmDeptUserModal').modal('hide');
}
</script>
@endsection
