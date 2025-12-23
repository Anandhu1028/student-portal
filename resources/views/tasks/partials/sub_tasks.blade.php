<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Sub-tasks</h6>
        <button class="btn btn-sm btn-primary" id="openAddSubTask">Add</button>
    </div>

    <div class="card-body">
        @if($task->subTasks->isEmpty())
            <div class="text-center text-muted">No sub-tasks</div>
        @else
            <ul class="list-group">
                @foreach($task->subTasks as $s)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $s->title }}</strong>
                            <div class="small text-muted">{{ $s->owner->name ?? '—' }} • {{ $s->due_at?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary editSubTask" data-id="{{ $s->id }}">Edit</button>
                            <button class="btn btn-sm btn-outline-success changeSubTaskStatus" data-id="{{ $s->id }}">Status</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<script>
// open add subtask offcanvas
$(document).on('click', '#openAddSubTask', function () {
    const offcanvasEl = document.getElementById('offcanvasCustom');
    const oc = new bootstrap.Offcanvas(offcanvasEl);

    $('#offcanvasCustomHead').html('Add Sub-task');
    $('#offcanvasCustomBody').html(`
        <form id="subTaskCreateForm">
            <div class="mb-2">
                <input name="title" class="form-control form-control-sm" required placeholder="Title" />
            </div>
            <div class="mb-2">
                <textarea name="description" class="form-control form-control-sm" placeholder="Description"></textarea>
            </div>
            <div class="text-end"><button class="btn btn-sm btn-primary">Save</button></div>
        </form>
    `);
    oc.show();
});

// submit subtask
$(document).on('submit', '#subTaskCreateForm', function (e) {
    e.preventDefault();
    const id = {{ $task->id }};
    preloader.load();

    $.post("{{ url('/tasks') }}/" + id + "/subtasks", $(this).serialize())
        .done(function () { preloader.stop(); loadTaskView(id); })
        .fail(function () { preloader.stop(); showAlert('Failed to add sub-task', 'error'); });
});

// stub for edit/status — these will call controller endpoints
$(document).on('click', '.editSubTask', function () {
    const id = $(this).data('id');
    const offcanvasEl = document.getElementById('offcanvasCustom');
    const oc = new bootstrap.Offcanvas(offcanvasEl);
    preloader.load();

    $.get("{{ url('/tasks/subtasks') }}/" + id)
        .done(function (html) { preloader.stop(); $('#offcanvasCustomHead').html('Edit Sub-task'); $('#offcanvasCustomBody').html(html); oc.show(); })
        .fail(function () { preloader.stop(); showAlert('Unable to load sub-task', 'error'); });
});

$(document).on('click', '.changeSubTaskStatus', function () {
    const id = $(this).data('id');
    const status = prompt('Enter status id');
    if (!status) return;
    preloader.load();
    $.post("{{ url('/tasks/subtasks') }}/" + id + "/status", { _token: '{{ csrf_token() }}', task_status_id: status })
        .done(function () { preloader.stop(); loadTaskView({{ $task->id }}); })
        .fail(function () { preloader.stop(); showAlert('Failed to change status', 'error'); });
});
</script>
