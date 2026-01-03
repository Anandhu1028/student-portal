<!----------------------------------------------
       TASK LIST TABLE (FINAL – TOOLTIP BASED)
------------------------------------------------>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Files</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Owner</th>
            <th>Assigned</th>
            <th>Due Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($tasks as $task)
            <tr data-task-row="{{ $task->id }}">

                {{-- TITLE --}}
                <td class="task-title-cell">
                    <span class="task-title openTaskEdit" role="button" data-id="{{ $task->id }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ $task->title }}">
                        {{ \Illuminate\Support\Str::limit($task->title, 30) }}
                    </span>
                </td>

                {{-- DESCRIPTION --}}
                <td class="task-desc-cell">
                    @if($task->description)
                        <span class="task-desc" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="{{ $task->description }}">
                            {{ \Illuminate\Support\Str::limit($task->description, 40) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                {{-- FILES --}}
                <td class="text-center">
                    @if($task->attachments->count())
                        <span class="badge bg-primary viewTaskFiles" role="button" data-bs-toggle="tooltip"
                            title="View attachments" data-files='@json(
                                $task->attachments->map(fn($f) => [
                                    "url" => url("storage/" . $f->file_path),
                                    "name" => $f->original_name
                                ])
                            )'>
                            {{ $task->attachments->count() }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>



                <td>{{ $task->status->name }}</td>
                <td>{{ $task->priority->name }}</td>
                <td>{{ $task->owner->name }}</td>

                {{-- ASSIGNED USERS --}}
                <td>
                    @php $names = $task->assignees->pluck('name')->toArray(); @endphp
                    <span class="assigned-users" data-bs-toggle="tooltip" title="{{ implode(', ', $names) }}">
                        {{ collect($names)->take(2)->join(', ') }}
                        @if(count($names) > 2) … @endif
                    </span>
                </td>

                {{-- DUE DATE --}}
                <td>
                    @if($task->due_at)
                        {{ \Carbon\Carbon::parse($task->due_at)->format('d M Y') }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                {{-- ACTIONS --}}
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary openTaskView" data-id="{{ $task->id }}">
                        View
                    </button>

                    <button class="btn btn-sm btn-outline-success forwardTask" data-id="{{ $task->id }}">
                        Forward
                    </button>

                    <button class="btn btn-sm btn-outline-danger deleteTask" data-id="{{ $task->id }}">
                        Delete
                    </button>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted">
                    No tasks found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $tasks->links() }}



<div class="modal fade" id="taskFilesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Task Attachments</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3" id="taskFilesContainer"></div>
            </div>

        </div>
    </div>
</div>

{{-- ================= FORWARD TASK MODAL ================= --}}
<div class="modal fade" id="forwardTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Forward Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="forwardTaskForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="forwardTaskId">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">— Select User —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Follow-up Date *</label>
                        <input type="date" name="follow_up_date" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-sm btn-primary" type="submit">Forward</button>
                </div>

            </form>

        </div>
    </div>
</div>




{{-- ===================== STYLES ===================== --}}
<style>
    .task-title,
    .task-desc,
    .assigned-users {
        display: inline-block;
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: help;
    }

    .task-title {

        color: #0c768a;
    }

    .task-desc {
        font-size: 0.875rem;
        color: #374151;
    }

    .viewTaskFiles {
        cursor: pointer;
    }

    #taskFilesContainer img {
        transition: transform .2s ease;
    }

    #taskFilesContainer img:hover {
        transform: scale(1.03);
    }
</style>
<script>
    /* =========================================================
       VIEW TASK FILES (MODAL PREVIEW)
    ========================================================= */
    $(document).off('click', '.viewTaskFiles');
    $(document).on('click', '.viewTaskFiles', function () {

        const files = $(this).data('files');
        const container = $('#taskFilesContainer');
        container.empty();

        if (!Array.isArray(files) || files.length === 0) {
            container.html('<p class="text-muted text-center">No attachments found</p>');
            return;
        }

        files.forEach(file => {
            if (!file.url) return;

            container.append(`
            <div class="col-md-4">
                <div class="border rounded p-2 text-center h-100">
                    <a href="${file.url}" target="_blank">
                        <img src="${file.url}"
                             class="img-fluid rounded mb-2"
                             style="max-height:180px;object-fit:contain">
                    </a>
                    <div class="small text-muted text-truncate"
                         title="${file.name}">
                        ${file.name}
                    </div>
                </div>
            </div>
        `);
        });

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('taskFilesModal'))
            .show();
    });


    /* =========================================================
       BOOTSTRAP TOOLTIPS
    ========================================================= */
    function initTooltips(context = document) {
        context.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            bootstrap.Tooltip.getOrCreateInstance(el);
        });
    }

    initTooltips();
    $(document).on('ajaxTableReloaded', () => initTooltips(document));


    /* =========================================================
       RELOAD TASK TABLE (CORE FUNCTION)
    ========================================================= */
    function reloadTaskTable() {

        preloader.load();

        $.get("{{ route('tasks.list') }}", {
            filter: 1,
            status: $('[data-name="status"]').val() || '',
            priority: $('[data-name="priority"]').val() || '',
            owner: $('[data-name="owner"]').val() || '',
            search: $('.taskSearch').val() || ''
        })
            .done(html => {
                $('.task-table-wrapper').html(html);
                $(document).trigger('ajaxTableReloaded');
            })
            .fail(() => showAlert('Failed to reload task list', 'error'))
            .always(() => preloader.stop());
    }


    /* =========================================================
       FORWARD TASK
    ========================================================= */
    $(document).off('click', '.forwardTask');
    $(document).on('click', '.forwardTask', function () {

        $('#forwardTaskId').val($(this).data('id'));
        $('#forwardTaskForm')[0].reset();

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('forwardTaskModal'))
            .show();
    });

    $(document).off('submit', '#forwardTaskForm');
    $(document).on('submit', '#forwardTaskForm', function (e) {

        e.preventDefault();

        const form = this;
        const taskId = $('#forwardTaskId').val();

        if ($(form).data('loading')) return;
        $(form).data('loading', true);

        preloader.load();

        $.ajax({
            url: `/tasks/${taskId}/forward`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: new FormData(form),
            processData: false,
            contentType: false
        })
            .done(() => {
                bootstrap.Modal
                    .getInstance(document.getElementById('forwardTaskModal'))
                    .hide();

                showAlert('Task forwarded successfully', 'success');

                // 🔥 instant UI update
                reloadTaskTable();
            })
            .fail(xhr => {
                showAlert(xhr.responseJSON?.message || 'Forward failed', 'error');
            })
            .always(() => {
                preloader.stop();
                $(form).data('loading', false);
            });
    });


    /* =========================================================
       EDIT TASK
    ========================================================= */
    $(document).off('click', '.openTaskEdit');
    $(document).on('click', '.openTaskEdit', function (e) {

        e.preventDefault();
        const id = $(this).data('id');
        if (!id) return;

        preloader.load();

        $.get(`{{ route('tasks.create') }}?id=${id}`)
            .done(html => {
                const oc = bootstrap.Offcanvas
                    .getOrCreateInstance(document.getElementById('offcanvasCustom'));

                $('#offcanvasCustomHead').html('Edit Task');
                $('#offcanvasCustomBody').html(html);
                oc.show();

                setTimeout(() => {
                    $('.selectpicker').selectpicker('render').selectpicker('refresh');
                }, 150);
            })
            .fail(() => showAlert('Failed to load task', 'error'))
            .always(() => preloader.stop());
    });


    /* =========================================================
       VIEW TASK DETAILS
    ========================================================= */
    $(document).off('click', '.openTaskView');
    $(document).on('click', '.openTaskView', function () {

        const id = $(this).data('id');
        preloader.load();

        $.get(`{{ url('/tasks') }}/${id}?view=1`)
            .done(html => {
                const oc = bootstrap.Offcanvas
                    .getOrCreateInstance(document.getElementById('offcanvasCustom'));

                $('#offcanvasCustomHead').html('Task Details');
                $('#offcanvasCustomBody').html(html);
                oc.show();
            })
            .fail(() => showAlert('Unable to load task', 'error'))
            .always(() => preloader.stop());
    });


    /* =========================================================
       DELETE TASK
    ========================================================= */
    $(document).off('click', '.deleteTask');
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

        const modal = bootstrap.Modal
            .getOrCreateInstance(document.getElementById('taskDeleteConfirm'));

        modal.show();

        $(document).one('click', '.confirmDeleteTask', function () {

            preloader.load();

            $.post("{{ route('tasks.delete') }}", {
                _token: "{{ csrf_token() }}",
                id: taskId
            })
                .done(() => {
                    modal.hide();
                    $(`tr[data-task-row="${taskId}"]`).fadeOut(200, function () {
                        $(this).remove();
                    });
                    showAlert('Task deleted successfully', 'success');
                })
                .fail(() => showAlert('Delete failed', 'error'))
                .always(() => {
                    preloader.stop();
                    $('#taskDeleteConfirm').remove();
                });
        });
    });
</script>