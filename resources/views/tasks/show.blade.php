@extends('layouts.layout_ajax')
@section('content')

<div class="row g-3 task-view-wrapper">

    {{-- LEFT : TASK INFO (73%) --}}
    <div class="col-md-4 task-left">
        <div class="card task-info-card">
            <div class="card-body">

                <h5 class="mb-1">{{ $task->title }}</h5>
                <p class="text-muted small">{{ $task->description ?: '—' }}</p>

                <div class="mb-3">
                    <span class="badge bg-secondary">
                        Status: {{ $task->status->name ?? '—' }}
                    </span>
                    <span class="badge bg-info ms-1">
                        Priority: {{ $task->priority->name ?? '—' }}
                    </span>
                </div>

                @if($task->forwards->count())
                <div class="mt-3">
                    <div class="small text-muted mb-1">Forwarded To</div>

                    <div class="d-flex flex-wrap gap-1">
                        @foreach($task->forwards as $forward)
                        <span class="badge bg-warning text-dark d-flex align-items-center gap-1">
                            {{ $forward->department->name }}

                            <button class="btn btn-sm p-0 text-dark revokeForward"
                                data-id="{{ $forward->id }}"
                                title="Remove">
                                ×
                            </button>
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif


                <dl class="row small mb-0">
                    <dt class="col-5">Owner</dt>
                    <dd class="col-7">{{ $task->owner->name ?? '—' }}</dd>

                    <dt class="col-5">Assigned Users</dt>
                    <dd class="col-7">
                        {{ $task->assignees->pluck('name')->join(', ') ?: '—' }}
                    </dd>

                    <dt class="col-5">Due Date</dt>
                    <dd class="col-7">
                        {{ $task->due_at ? $task->due_at->format('d M Y') : '—' }}
                    </dd>
                </dl>

            </div>
        </div>

        @include('tasks.partials.sub_tasks', ['task' => $task])
    </div>

    {{-- RIGHT : ACTIVITY (27%) --}}
    <div class="col-md-8 task-right">
        <div class="card h-100 activity-card">
            <div class="card-header">
                <strong>Activity</strong>
            </div>

            <div class="card-body" style="max-height:480px;overflow:auto">
                @include('tasks.partials.activity_timeline', [
                'activities' => $task->activities
                ])
            </div>

            <div class="card-footer">
                <form id="taskCommentForm" enctype="multipart/form-data">
                    @csrf

                    <textarea name="message"
                        class="form-control form-control-sm mb-2"
                        placeholder="Write a comment..."
                        required></textarea>

                    <div class="d-flex gap-2">
                        <input type="file"
                            name="file"
                            class="form-control form-control-sm">

                        <button class="btn btn-sm btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>


<style>
    /* ================= DESIGN TOKENS ================= */

    :root {
        --bg-main: #ffffff;
        --bg-soft: #f8fafc;
        --border: #e5e7eb;
        --text-main: #111827;
        --text-muted: #6b7280;
        --shadow-soft: 0 6px 18px rgba(0, 0, 0, 0.05);
    }

    /* ================= LAYOUT (UNCHANGED) ================= */

    .task-view-wrapper {
        display: flex;
        align-items: stretch;
    }

    /* LEFT SIDE — 73% */
    .task-left {
        flex: 0 0 73%;
        max-width: 73%;
    }

    /* RIGHT SIDE — 27% */
    .task-right {
        flex: 0 0 27%;
        max-width: 27%;
    }

    /* ================= LEFT PANEL ================= */

    .task-info-card {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }

    .task-info-card .card-body {
        padding: 20px;
    }

    .task-info-card h5 {
        font-size: 17px;
        font-weight: 600;
        color: var(--text-main);
    }

    .task-info-card p {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 14px;
    }

    .task-info-card .badge {
        font-size: 11px;
        font-weight: 500;
        padding: 5px 10px;
    }

    .task-info-card dl {
        margin-top: 10px;
    }

    .task-info-card dt {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .task-info-card dd {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-main);
    }

    /* ================= RIGHT PANEL (ACTIVITY) ================= */

    .activity-card {
        background: var(--bg-soft);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }

    /* Header */
    .activity-card .card-header {
        background: var(--bg-main);
        border-bottom: 1px solid var(--border);
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Scroll body */
    .activity-card .card-body {
        background: var(--bg-soft);
        padding: 14px;
    }

    /* Activity timeline items */
    .activity-item {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 10px;
        font-size: 12.5px;
        transition: background 0.15s ease;
    }

    .activity-item:hover {
        background: #f9fafb;
    }

    .activity-item .user {
        font-weight: 600;
        color: var(--text-main);
        font-size: 12.5px;
    }

    .activity-item .time {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ================= COMMENT BOX ================= */

    .activity-card .card-footer {
        background: var(--bg-main);
        border-top: 1px solid var(--border);
        padding: 12px;
    }

    #taskCommentForm textarea {
        resize: none;
        min-height: 64px;
        font-size: 12.5px;
        border-radius: 8px;
    }

    #taskCommentForm input[type="file"] {
        font-size: 12px;
    }

    #taskCommentForm button {
        font-size: 12px;
        padding: 6px 16px;
        border-radius: 6px;
    }

    /* ================= RESPONSIVE ================= */

    @media (max-width: 992px) {
        .task-view-wrapper {
            flex-direction: column;
        }

        .task-left,
        .task-right {
            max-width: 100%;
            flex: 0 0 100%;
        }
    }
</style>



{{-- COMMENT SUBMIT --}}
<script>
    $(document).on('click', '.changeTaskStatus', function() {

        const statusId = $(this).data('status');
        preloader.load();

        $.post("{{ route('tasks.subtasks.change_status', $task->id) }}", {
                _token: "{{ csrf_token() }}",
                task_status_id: statusId
            })
            .done(function() {
                preloader.stop();
                reloadTaskView({
                    {
                        $task - > id
                    }
                });
            })
            .fail(function() {
                preloader.stop();
                showAlert('Status update failed', 'error');
            });
    });

    $(document).on('submit', '#taskCommentForm', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        preloader.load();

        $.ajax({
                url: "{{ route('tasks.comment', $task->id) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false
            })
            .done(function() {
                preloader.stop();
                reloadTaskView({
                    {
                        $task - > id
                    }
                });
            })
            .fail(function() {
                preloader.stop();
                showAlert('Failed to add comment', 'error');
            });
    });

    function reloadTaskView(id) {
        preloader.load();
        $.get(`/tasks/${id}?view=1`, function(html) {
            preloader.stop();
            $('#offcanvasCustomBody').html(html);
        });
    }


    // OPEN SUB TASK MODAL (DELEGATED)
    $(document).on('click', '.addSubTask', function() {
        const modalEl = document.getElementById('subTaskModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    $(document).on('submit', '#subTaskForm', function(e) {
        e.preventDefault();

        const form = $(this);
        const taskId = form.find('[name="task_id"]').val();

        preloader.load();

        $.post(`/tasks/${taskId}/subtasks`, form.serialize())
            .done(function() {
                preloader.stop();

                bootstrap.Modal
                    .getInstance(document.getElementById('subTaskModal'))
                    .hide();

                form[0].reset(); // clear inputs
                reloadTaskView(taskId); // refresh subtask list
            })
            .fail(function(xhr) {
                preloader.stop();
                showAlert(
                    xhr.responseJSON?.message || 'Failed to create sub-task',
                    'error'
                );
            });
    });



    $(document).on('click', '.revokeForward', function() {
        const id = $(this).data('id');

        if (!confirm('Remove this forwarded department?')) return;

        preloader.load();

        $.ajax({
                url: `/tasks/forwards/${id}`,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                }
            })
            .done(function() {
                reloadTaskView({
                    {
                        $task - > id
                    }
                });
            })
            .fail(function() {
                preloader.stop();
                showAlert('Failed to remove forward', 'error');
            });
    });
</script>

@endsection