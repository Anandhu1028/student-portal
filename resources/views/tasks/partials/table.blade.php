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
<tr>
    <td>{{ $task->title }}</td>
    <td>{{ $task->status->name }}</td>
    <td>{{ $task->priority->name }}</td>
    <td>{{ $task->owner->name }}</td>
    <td>
        @php $names = $task->assignees->pluck('name')->toArray(); @endphp
        <span class="assigned-users" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ implode(', ', $names) }}">
            {{ collect($names)->take(2)->join(', ') }}
            @if(count($names) > 2)
                …
            @endif
        </span>
        <script>
            // Initialize tooltip for the first matching element on initial render
            const el = document.querySelector('.assigned-users');
            if (el) new bootstrap.Tooltip(el);
        </script>
    </td>
    <td>{{ optional($task->due_at)->format('d M Y') }}</td>
    <td class="text-nowrap">
    <button
        class="btn btn-sm btn-outline-primary openTaskView"
        data-id="{{ $task->id }}">
        View
    </button>

    <button
        class="btn btn-sm btn-outline-danger deleteTask"
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
$(document).on('click', '.openTaskEdit', function () {

    const taskId = $(this).data('id');
    if (!taskId) return;

    preloader.load();

    $.get("{{ url('/tasks') }}/" + taskId, function (html) {

        preloader.stop();

        const offcanvasEl = document.getElementById('offcanvasCustom');
        const oc = new bootstrap.Offcanvas(offcanvasEl);

        $('#offcanvasCustomHead').html('Edit Task');
        $('#offcanvasCustomBody').html(html);

        oc.show();
        $('.selectpicker').selectpicker();
    }).fail(function () {
        preloader.stop();
        showAlert('Failed to load task', 'error');
    });
});
/* ================= VIEW TASK ================= */
$(document).on('click', '.openTaskView', function () {

    const taskId = $(this).data('id');
    if (!taskId) return;

    preloader.load();

    $.get("{{ url('/tasks') }}/" + taskId + "?view=1", function (html) {

        preloader.stop();

        const offcanvasEl = document.getElementById('offcanvasCustom');
        const oc = new bootstrap.Offcanvas(offcanvasEl);

        $('#offcanvasCustomHead').html('Task Details');
        $('#offcanvasCustomBody').html(html);

        oc.show();
    }).fail(function () {
        preloader.stop();
        showAlert('Unable to load task', 'error');
    });
});



/* ================= DELETE TASK ================= */
$(document).on('click', '.deleteTask', function () {

    const taskId = $(this).data('id');
    if (!taskId) return;

        // Show bootstrap modal confirmation
        const modalHtml = `
        <div class="modal fade" id="taskDeleteConfirm" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this task?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteTaskBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>`;

        $('body').append(modalHtml);
        const $modal = $('#taskDeleteConfirm');
        const bs = new bootstrap.Modal($modal[0]);
        bs.show();

        $(document).one('click', '#confirmDeleteTaskBtn', function () {
                preloader.load();

                $.post("{{ route('tasks.delete') }}", {
                        _token: "{{ csrf_token() }}",
                        id: taskId
                })
                .done(function () {
                        preloader.stop();
                        bs.hide();
                        $modal.remove();
                        showAlert('Task deleted successfully');
                        loadTasks();
                })
                .fail(function () {
                        preloader.stop();
                        bs.hide();
                        $modal.remove();
                        showAlert('Failed to delete task', 'error');
                });
        });
});
</script>
