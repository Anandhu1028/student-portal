<div class="card subtask-panel">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Sub Tasks</strong>
        <button class="btn btn-sm btn-primary addSubTask">
            <i class="bi bi-plus-lg"></i> Add
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 subtask-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Due</th>
                        <th width="60">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($task->subTasks as $sub)
                    <tr>
                        <td class="subtask-title-cell">
                            <button class="btn btn-link p-0 text-start editSubTask"
                                data-id="{{ $sub->id }}"
                                data-title="{{ $sub->title }}"
                                data-description="{{ $sub->description }}"
                                data-status="{{ $sub->task_status_id }}"
                                data-priority="{{ $sub->task_priority_id }}"
                                data-due="{{ optional($sub->due_at)->format('Y-m-d') }}">
                                {{ $sub->title }}
                            </button>
                            @if($sub->description)
                            <div class="subtask-desc text-muted">
                                {{ $sub->description }}
                            </div>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $sub->owner->name ?? '—' }}</td>
                        <td class="text-nowrap">{{ $sub->due_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger deleteSubTask"
                                data-id="{{ $sub->id }}"
                                data-task="{{ $task->id }}"
                                title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path fill="#ff1717"
                                    d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z"/>
                            </svg>
                        </button>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No sub-tasks yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="subTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_at" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Sub Task</button>
            </div>
        </form>
    </div>
</div>

<style>
/* ================= SUB TASK PANEL ================= */
.subtask-panel {
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.subtask-panel .card-header {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 12px 16px;
    font-size: 14px;
}

.subtask-panel .card-header strong {
    font-weight: 600;
}

.subtask-panel .btn-primary {
    font-size: 12px;
    padding: 4px 12px;
}

/* ================= SUB TASK TABLE ================= */
.subtask-table {
    font-size: 13px;
}

.subtask-table thead {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.subtask-table thead th {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    padding: 10px 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.subtask-table tbody td {
    padding: 12px;
    vertical-align: top;
    border-bottom: 1px solid #f3f4f6;
}

.subtask-table tbody tr:last-child td {
    border-bottom: none;
}

.subtask-table tbody tr:hover {
    background: #f9fafb;
}

/* Title Cell */
.subtask-title-cell .editSubTask {
    font-size: 13px;
    font-weight: 500;
    color: #0d6efd;
    text-decoration: none;
    display: block;
    margin-bottom: 4px;
}

.subtask-title-cell .editSubTask:hover {
    text-decoration: underline;
}

.subtask-desc {
    font-size: 12px;
    line-height: 1.4;
    max-height: 2.8em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Delete Button */
.deleteSubTask {
    padding: 4px 8px;
    font-size: 12px;
}

/* Empty State */
.subtask-table tbody tr td[colspan] {
    font-size: 13px;
}

/* ================= MODAL OVERRIDES ================= */
.modal, .modal * {
    pointer-events: auto !important;
}

.modal-backdrop {
    pointer-events: none !important;
}

#subTaskModal .modal-dialog {
    max-width: 500px;
}

#subTaskModal .form-label {
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

#subTaskModal .form-control,
#subTaskModal .form-select {
    font-size: 13px;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 991px) {
    .subtask-panel {
        position: static;
        margin-top: 16px;
    }
}

@media (max-width: 768px) {
    .subtask-table {
        font-size: 12px;
    }
    
    .subtask-table thead th {
        padding: 8px;
    }
    
    .subtask-table tbody td {
        padding: 10px 8px;
    }
}
</style>

<script>
// OPEN CREATE MODAL
$(document).on('click', '.addSubTask', function() {
    const form = $('#subTaskForm')[0];
    form.reset();
    form.subtask_id.value = '';
    $('#subTaskModal .modal-title').text('Create Sub Task');
    
    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('subTaskModal')
    ).show();
});

// OPEN EDIT MODAL
$(document).on('click', '.editSubTask', function() {
    const btn = $(this);
    const form = $('#subTaskForm')[0];

    form.subtask_id.value = btn.data('id');
    form.title.value = btn.data('title');
    form.description.value = btn.data('description') || '';
    form.task_status_id.value = btn.data('status');
    form.task_priority_id.value = btn.data('priority');
    form.due_at.value = btn.data('due') || '';
    
    $('#subTaskModal .modal-title').text('Edit Sub Task');

    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('subTaskModal')
    ).show();
});

// SAVE (CREATE + UPDATE)
$(document).on('submit', '#subTaskForm', function (e) {
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
        .done(res => {
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('subTaskModal')
            );
            modal.hide();

            if (subtaskId) {
                updateSubTaskRow(res.data);
            } else {
                appendSubTaskRow(res.data);
            }

            form[0].reset();
            form.find('[name="subtask_id"]').val('');
        })
        .fail(xhr => {
            showAlert(xhr.responseJSON?.message || 'Failed to save sub-task', 'error');
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
        data: { _token: "{{ csrf_token() }}" }
    })
    .done(() => reloadTaskView(taskId))
    .fail(() => showAlert('Failed to delete sub-task', 'error'))
    .always(() => preloader.stop());
});



function appendSubTaskRow(sub) {
    const row = `
    <tr id="subtask-row-${sub.id}">
        <td class="subtask-title-cell">
            <button class="btn btn-link p-0 text-start editSubTask"
                data-id="${sub.id}"
                data-title="${sub.title}"
                data-description="${sub.description || ''}"
                data-due="${sub.due_at || ''}">
                ${sub.title}
            </button>
            ${sub.description ? `<div class="subtask-desc text-muted">${sub.description}</div>` : ''}
        </td>
        <td>${sub.owner?.name ?? '—'}</td>
        <td>${sub.due_at ?? '—'}</td>
        <td>
            <button class="btn btn-sm btn-outline-danger deleteSubTask"
                data-id="${sub.id}">
                🗑
            </button>
        </td>
    </tr>`;
    $('.subtask-table tbody').append(row);
}

function updateSubTaskRow(sub) {
    const row = $(`#subtask-row-${sub.id}`);

    row.find('.editSubTask')
        .text(sub.title)
        .data('title', sub.title)
        .data('description', sub.description || '');

    row.find('.subtask-desc').remove();

    if (sub.description) {
        row.find('.subtask-title-cell')
            .append(`<div class="subtask-desc text-muted">${sub.description}</div>`);
    }
}
</script>