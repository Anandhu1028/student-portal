<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Title</th>
    <th>Status</th>
    <th>Priority</th>
    <th>Owner</th>
    <th>Assigned</th>
    <th>Due</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($tasks as $task)
<tr data-task-row="{{ $task->id }}">
    <td>
        <a role="button"
        class="btn-link openTaskEdit"
        data-id="{{ $task->id }}">
        {{ $task->title }}
        </a>
    </td>

    <td>{{ $task->status->name }}</td>
    <td>{{ $task->priority->name }}</td>
    <td>{{ $task->owner->name }}</td>

    <td>
        @php $names = $task->assignees->pluck('name')->toArray(); @endphp
        <span class="assigned-users"
              data-bs-toggle="tooltip"
              title="{{ implode(', ', $names) }}">
            {{ collect($names)->take(2)->join(', ') }}
            @if(count($names) > 2) … @endif
        </span>
    </td>

    <td>
    @if($task->due_at)
        {{ \Carbon\Carbon::parse($task->due_at)->format('d M Y') }}
    @else
        <span class="text-muted">—</span>
    @endif
</td>


    <td class="text-nowrap">
        <button class="btn btn-sm btn-outline-primary openTaskView"
                data-id="{{ $task->id }}">
            View
        </button>

        <button class="btn btn-sm btn-outline-danger deleteTask"
                data-id="{{ $task->id }}">
            Delete
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">No tasks found</td>
</tr>
@endforelse
</tbody>
</table>

{{ $tasks->links() }}




<script>
/* ================= EDIT TASK ================= */
$(document).on('click', '.openTaskEdit', function (e) {
    e.preventDefault();

    const id = $(this).data('id');
    if (!id) return;

    preloader.load();

    $.get(`{{ route('tasks.create') }}?id=${id}`, function (html) {

        preloader.stop();

        const offcanvasEl = document.getElementById('offcanvasCustom');
        const oc = new bootstrap.Offcanvas(offcanvasEl);

        $('#offcanvasCustomHead').html('Edit Task');
        $('#offcanvasCustomBody').html(html);

        oc.show();

        // 🔥 CRITICAL FIX
        setTimeout(function () {
            $('.selectpicker').selectpicker('render');
            $('.selectpicker').selectpicker('refresh');
        }, 150);

    }).fail(function () {
        preloader.stop();
        showAlert('Failed to load task', 'error');
    });
});




/* ================= VIEW TASK ================= */
$(document).on('click', '.openTaskView', function () {
    const id = $(this).data('id');
    preloader.load();

    $.get(`{{ url('/tasks') }}/${id}?view=1`, function (html) {
        preloader.stop();

        const oc = new bootstrap.Offcanvas('#offcanvasCustom');
        $('#offcanvasCustomHead').html('Task Details');
        $('#offcanvasCustomBody').html(html);
        oc.show();
    }).fail(() => {
        preloader.stop();
        showAlert('Unable to load task', 'error');
    });
});

/* ================= DELETE TASK ================= */
$(document).on('click', '.deleteTask', function () {

    const taskId = $(this).data('id');
    $('#taskDeleteConfirm').remove();

    $('body').append(`
        <div class="modal fade" id="taskDeleteConfirm">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Confirm Delete</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this task?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-sm btn-danger confirmDeleteTask">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    `);

    const modal = new bootstrap.Modal('#taskDeleteConfirm');
    modal.show();

    $(document).one('click', '.confirmDeleteTask', function () {
        preloader.load();

        $.post("{{ route('tasks.delete') }}", {
            _token: "{{ csrf_token() }}",
            id: taskId
        }).done(() => {
            preloader.stop();
            modal.hide();
            $('#taskDeleteConfirm').remove();

            const row = $(`tr[data-task-row="${taskId}"]`);
            row.fadeOut(200, () => row.remove());

            showAlert('Task deleted successfully');
        }).fail(() => {
            preloader.stop();
            modal.hide();
            $('#taskDeleteConfirm').remove();
            showAlert('Delete failed', 'error');
        });
    });
});
</script>
