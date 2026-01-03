<div class="card subtask-panel">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Sub Tasks</strong>
        <button class="btn btn-sm btn-primary addSubTask">+ Add</button>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm mb-0 subtask-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Owner</th>
                    <th>Due</th>
                    <th width="60"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($task->subTasks as $sub)
                <tr id="subtask-row-{{ $sub->id }}">
                    <td>
                        <button class="btn btn-link p-0 editSubTask"
                            data-id="{{ $sub->id }}"
                            data-title="{{ $sub->title }}"
                            data-description="{{ $sub->description }}"
                            data-due="{{ optional($sub->due_at)->format('Y-m-d') }}">
                            {{ $sub->title }}
                        </button>

                        @if($sub->description)
                            <div class="subtask-desc text-muted">{{ $sub->description }}</div>
                        @endif
                    </td>
                    <td>{{ $sub->owner->name ?? '—' }}</td>
                    <td>{{ $sub->due_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger deleteSubTask"
                            data-id="{{ $sub->id }}">🗑</button>
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



{{-- MODAL --}}
<div class="modal fade"
     id="subTaskModal"
     tabindex="-1"
     data-bs-backdrop="false">

    <div class="modal-dialog">
        <form id="subTaskForm" class="modal-content">
            @csrf
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <input type="hidden" name="subtask_id">

            <div class="modal-header">
                <h5 class="modal-title">Sub Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input class="form-control mb-2" name="title" placeholder="Title" required>
                <textarea class="form-control mb-2" name="description" rows="3" placeholder="Description"></textarea>
                <input class="form-control" type="date" name="due_at">
            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>




<style>

  .subtask-panel {
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    position: sticky;
    top: 20px;
}

.subtask-table {
    font-size: 13px;
}

.subtask-title-cell .editSubTask {
    font-weight: 500;
    color: #0d6efd;
    text-decoration: none;
}

.subtask-desc {
    font-size: 12px;
    line-height: 1.4;
    max-height: 2.8em;
    overflow: hidden;
}

#subTaskModal .modal-dialog {
    max-width: 500px;
}

#subTaskModal .modal-content {
    box-shadow: 0 12px 30px rgba(0,0,0,.15);
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
/* OPEN CREATE */
$(document).on('click', '.addSubTask', function () {
    const f = $('#subTaskForm')[0];
    f.reset();
    f.subtask_id.value = '';
    $('#subTaskModal .modal-title').text('Create Sub Task');
    bootstrap.Modal.getOrCreateInstance(subTaskModal).show();
});

/* OPEN EDIT */
$(document).on('click', '.editSubTask', function () {
    const f = $('#subTaskForm')[0];
    f.subtask_id.value = $(this).data('id');
    f.title.value = $(this).data('title');
    f.description.value = $(this).data('description') || '';
    f.due_at.value = $(this).data('due') || '';
    $('#subTaskModal .modal-title').text('Edit Sub Task');
    bootstrap.Modal.getOrCreateInstance(subTaskModal).show();
});

/* SAVE */
$(document).on('submit', '#subTaskForm', function (e) {
    e.preventDefault();

    const form = $(this);
    const subtaskId = form.find('[name=subtask_id]').val();
    const taskId = form.find('[name=task_id]').val();

    let url = `/tasks/${taskId}/subtasks`;
    let data = form.serialize();

    if (subtaskId) {
        url = `/tasks/subtasks/${subtaskId}`;
        data += '&_method=PATCH';
    }

    preloader.load();

    $.post(url, data)
        .done(res => {
            if (!res.data) return;

            subtaskId
                ? updateSubTaskRow(res.data)
                : appendSubTaskRow(res.data);

            setTimeout(() => {
                bootstrap.Modal.getInstance(subTaskModal).hide();
            }, 120);
        })
        .fail(xhr => {
            showAlert(xhr.responseJSON?.message || 'Failed to save sub-task', 'error');
        })
        .always(() => preloader.stop());
});

/* DELETE */
$(document).on('click', '.deleteSubTask', function () {
    if (!confirm('Delete this sub-task?')) return;

    const id = $(this).data('id');
    preloader.load();

    $.ajax({
        url: `/tasks/subtasks/${id}`,
        type: 'DELETE',
        data: { _token: "{{ csrf_token() }}" }
    })
    .done(() => $(`#subtask-row-${id}`).remove())
    .always(() => preloader.stop());
});

/* HELPERS */
function appendSubTaskRow(s) {
    $('.subtask-table tbody').append(`
        <tr id="subtask-row-${s.id}">
            <td>
                <button class="btn btn-link p-0 editSubTask"
                    data-id="${s.id}"
                    data-title="${s.title}"
                    data-description="${s.description || ''}"
                    data-due="${s.due_at || ''}">
                    ${s.title}
                </button>
                ${s.description ? `<div class="subtask-desc text-muted">${s.description}</div>` : ''}
            </td>
            <td>${s.owner?.name ?? '—'}</td>
            <td>${s.due_at ?? '—'}</td>
            <td><button class="btn btn-sm btn-outline-danger deleteSubTask" data-id="${s.id}">🗑</button></td>
        </tr>
    `);
}

function updateSubTaskRow(s) {
    const r = $(`#subtask-row-${s.id}`);
    r.find('.editSubTask')
        .text(s.title)
        .data('title', s.title)
        .data('description', s.description || '')
        .data('due', s.due_at || '');

    r.find('.subtask-desc').remove();
    if (s.description) {
        r.find('td:first').append(`<div class="subtask-desc text-muted">${s.description}</div>`);
    }
}
</script>


