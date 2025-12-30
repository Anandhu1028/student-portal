<!----------------------------------------------
       THIS THE TASK LIST TABLE (TABLE SECTION ONLY).
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
            <td class="task-title-cell">
                <a role="button"
                    class="task-title openTaskEdit"
                    data-id="{{ $task->id }}">
                    {{ $task->title }}
                </a>
            </td>

            <td class="task-desc-cell">
                @if($task->description)
                <div class="task-desc"
                    data-short="{{ \Illuminate\Support\Str::limit($task->description, 50, '') }}"
                    data-full="{{ e($task->description) }}">
                    {{ \Illuminate\Support\Str::limit($task->description, 50, '') }}
                </div>
                @else
                <span class="text-muted">—</span>
                @endif
            </td>



            <td class="text-center">
                @php
                $files = $task->attachments ?? collect();
                $count = $files->count();
                @endphp

                @if($count > 0)
                <span class="badge bg-primary"
                    data-bs-toggle="tooltip"
                    title="{{ $files->pluck('original_name')->join(', ') }}">
                    {{ $count }}
                </span>
                @else
                <span class="text-muted">—</span>
                @endif
            </td>

            <td>{{ $task->status->name }}</td>
            <td>{{ $task->priority->name }}</td>
            <td>{{ $task->owner->name }}</td>

            <td>
                @php $names = $task->assignees->pluck('name')->toArray(); @endphp
                <span class="assigned-users" data-bs-toggle="tooltip" title="{{ implode(', ', $names) }}">
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
                {{-- VIEW --}}
                <button class="btn btn-sm btn-outline-primary openTaskView" data-id="{{ $task->id }}">
                    View
                </button>

                {{-- FORWARD --}}
                <button class="btn btn-sm btn-outline-success forwardTask" data-id="{{ $task->id }}"
                    title="Forward Task">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        class="me-1 align-middle">
                        <path fill="currentColor" d="M14 5v4C7 10 4 15 3 20c2.5-3.5 6-5.1 11-5.1V19l7-7z" />
                    </svg>
                    Forward
                </button>


                {{-- DELETE --}}
                <button class="btn btn-sm btn-outline-danger deleteTask" data-id="{{ $task->id }}">
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

<div class="modal fade" id="forwardTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="forwardTaskForm" class="modal-content" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="task_id" id="forwardTaskId">

            <div class="modal-header">
                <h5 class="modal-title">Forward Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- DEPARTMENT --}}
                <div class="mb-3">
                    <label class="form-label">Department *</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select department</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- MESSAGE --}}
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Optional message"></textarea>
                </div>

                {{-- ATTACHMENTS --}}
                <div class="mb-3">
                    <label class="form-label">Attachments</label>
                    <input type="file" name="attachments[]" class="form-control" multiple>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Forward</button>
            </div>
        </form>
    </div>
</div>

<style>
    .task-title {
        display: inline-block;
        max-width: 220px;


        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        transition: all 0.25s ease;
    }

    .task-title-cell:hover .task-title {
        white-space: normal;
        padding: 4px 6px;
        border-radius: 4px;
    }


    /* EXPAND ON HOVER */
    .task-desc-cell:hover .task-desc {
        max-height: 200px;
        /* controls expansion */
        white-space: normal;

        padding: 6px 8px;
        border-radius: 4px;
    }

    .task-desc {
        max-height: 1.6em;
        overflow: hidden;
        font-size: 0.875rem;
        color: #374151;

        transition: max-height 0.35s ease,
            padding 0.25s ease,
            background 0.25s ease;
    }

    /* expand on hover */
    .task-desc-cell:hover .task-desc {
        max-height: 300px;
        padding: 6px 8px;

        border-radius: 4px;
    }
</style>


<script>
    /* ================= Hover then task Description transition================= */
    $(document).on('mouseenter', '.task-desc-cell', function() {
        const el = $(this).find('.task-desc');
        el.text(el.data('full'));
    });

    $(document).on('mouseleave', '.task-desc-cell', function() {
        const el = $(this).find('.task-desc');
        el.text(el.data('short'));
    });



    /* ================= FORWARD TASK ================= */

    $(document).on('click', '.forwardTask', function() {
        $('#forwardTaskId').val($(this).data('id'));
        $('#forwardTaskForm')[0].reset();

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('forwardTaskModal')
        ).show();
    });

    $(document)
        .off('submit.forwardTask')
        .on('submit.forwardTask', '#forwardTaskForm', function(e) {

            e.preventDefault();

            const form = this;
            const taskId = $('#forwardTaskId').val();
            const data = new FormData(form);

            if ($(form).data('loading')) return;
            $(form).data('loading', true);



            $.ajax({
                    url: `/tasks/${taskId}/forward`,
                    method: 'POST',
                    data: data,
                    processData: false, //  REQUIRED
                    contentType: false, //  REQUIRED
                })
                .done(() => {
                    bootstrap.Modal
                        .getInstance(document.getElementById('forwardTaskModal'))
                        .hide();

                    showAlert('Task forwarded successfully');
                })
                .fail(xhr => {
                    showAlert(
                        xhr.responseJSON?.message || 'Forward failed',
                        'error'
                    );
                })
                .always(() => {
                    preloader.stop();
                    $(form).data('loading', false);
                });
        });



    /* ================= EDIT TASK ================= */
    $(document).on('click', '.openTaskEdit', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        if (!id) return;

        preloader.load();

        $.get(`{{ route('tasks.create') }}?id=${id}`, function(html) {

            preloader.stop();

            const offcanvasEl = document.getElementById('offcanvasCustom');
            const oc = new bootstrap.Offcanvas(offcanvasEl);

            $('#offcanvasCustomHead').html('Edit Task');
            $('#offcanvasCustomBody').html(html);

            oc.show();

            //  CRITICAL FIX
            setTimeout(function() {
                $('.selectpicker').selectpicker('render');
                $('.selectpicker').selectpicker('refresh');
            }, 150);

        }).fail(function() {
            preloader.stop();
            showAlert('Failed to load task', 'error');
        });
    });




    /* ================= VIEW TASK ================= */
    $(document).on('click', '.openTaskView', function() {
        const id = $(this).data('id');
        preloader.load();

        $.get(`{{ url('/tasks') }}/${id}?view=1`, function(html) {
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
    $(document).on('click', '.deleteTask', function() {

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

        $(document).one('click', '.confirmDeleteTask', function() {
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