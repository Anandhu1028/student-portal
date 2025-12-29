<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Sub Tasks</strong>
        <button class="btn btn-sm btn-outline-primary addSubTask">
            + Add
        </button>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Owner</th>
                    <th>Due</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="subTaskTableBody">
                @forelse($task->subTasks as $sub)
                <tr>
                    <td>
                        <button class="btn btn-link p-0 text-start editSubTask"
                            data-id="{{ $sub->id }}"
                            data-title="{{ $sub->title }}"
                            data-description="{{ $sub->description }}"
                            data-status="{{ $sub->task_status_id }}"
                            data-priority="{{ $sub->task_priority_id }}"
                            data-due="{{ optional($sub->due_at)->format('Y-m-d') }}">
                            {{ $sub->title }}
                        </button>
                    </td>
                    <td class="subtask-desc-cell">
                        @if($sub->description)
                        <div class="subtask-desc">
                            {{ $sub->description }}
                        </div>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>{{ $sub->status->name ?? '—' }}</td>
                    <td>{{ $sub->priority->name ?? '—' }} </td>
                    <td>{{ $sub->owner->name ?? '—' }}</td>
                    <td>{{ $sub->due_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger deleteSubTask"
                            data-id="{{ $sub->id }}"
                            data-task="{{ $task->id }}">
                            Delete
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No sub-tasks
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>






{{-- MODAL --}}
<div class="modal " id="subTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="subTaskForm" class="modal-content">
            @csrf

            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <input type="hidden" name="subtask_id">

            <div class="modal-header">
                <h5 class="modal-title">Sub Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status *</label>
                    <select name="task_status_id" class="form-select" required>
                        @foreach(\App\Models\TaskStatus::all() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Priority *</label>
                    <select name="task_priority_id" class="form-select" required>
                        @foreach(\App\Models\TaskPriority::all() as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_at" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>

        </form>
    </div>
</div>



<style>
    .modal,
    .modal * {
        pointer-events: auto !important;
    }

    .modal-backdrop {
        pointer-events: none !important;
    }

    .subtask-desc-cell {
        max-width: 300px;
        /* control column width */
        position: relative;
    }

    .subtask-desc {
        max-height: 1.5em;
        /* ~1 line */
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .subtask-desc-cell:hover .subtask-desc {
        max-height: 200px;
        /* enough to show full text */
        white-space: normal;
        background: #f8f9fa;
        padding: 6px 8px;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        z-index: 10;
    }
</style>




<script>
    // OPEN CREATE
    $(document).on('click', '.addSubTask', function() {
        const form = $('#subTaskForm')[0];
        form.reset();
        form.subtask_id.value = '';

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('subTaskModal')
        ).show();
    });

    // OPEN EDIT
    $(document).on('click', '.editSubTask', function() {

        const btn = $(this);
        const form = $('#subTaskForm')[0];

        form.subtask_id.value = btn.data('id');
        form.title.value = btn.data('title');
        form.description.value = btn.data('description') || '';
        form.task_status_id.value = btn.data('status');
        form.task_priority_id.value = btn.data('priority');
        form.due_at.value = btn.data('due') || '';

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('subTaskModal')
        ).show();
    });

    // SAVE (CREATE + UPDATE)
    $(document)
        .off('submit', '#subTaskForm')
        .on('submit', '#subTaskForm', function(e) {

            e.preventDefault();

            const form = $(this);
            const taskId = form.find('[name="task_id"]').val();
            const subtaskId = form.find('[name="subtask_id"]').val();

            let url = `/tasks/${taskId}/subtasks`;
            let data = form.serialize();

            if (subtaskId) {
                url = `/tasks/subtasks/${subtaskId}`;
                data += '&_method=PATCH';
            }

            preloader.load();

            $.post(url, data)
                .done(() => {
                    bootstrap.Modal.getInstance(
                        document.getElementById('subTaskModal')
                    ).hide();

                    reloadTaskView(taskId);
                })
                .fail(xhr => {
                    showAlert(xhr.responseJSON?.message || 'Save failed', 'error');
                })
                .always(() => preloader.stop());
        });

    // DELETE
    $(document).on('click', '.deleteSubTask', function() {

        if (!confirm('Delete this sub-task?')) return;

        const subTaskId = $(this).data('id');
        const taskId = $(this).data('task');

        preloader.load();

        $.ajax({
                url: `/tasks/subtasks/${subTaskId}`,
                type: 'DELETE',
                data: {
                    _token: csrf_token
                }
            })
            .done(() => reloadTaskView(taskId))
            .always(() => preloader.stop());
    });
</script>