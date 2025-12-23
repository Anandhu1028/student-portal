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
        {{ $task->assignees->pluck('name')->take(2)->join(', ') }}
        @if($task->assignees->count() > 2)
            …
        @endif
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

    if (!confirm('Are you sure you want to delete this task?')) return;

    preloader.load();

    $.post("{{ route('tasks.delete') }}", {
        _token: "{{ csrf_token() }}",
        id: taskId
    })
    .done(function () {
        preloader.stop();
        showAlert('Task deleted successfully');
        loadTasks();
    })
    .fail(function () {
        preloader.stop();
        showAlert('Failed to delete task', 'error');
    });
});
</script>
