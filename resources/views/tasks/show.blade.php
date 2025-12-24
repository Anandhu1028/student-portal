@extends('layouts.layout_ajax')
@section('content')

<div class="row g-3">

    {{-- LEFT : TASK INFO --}}
    <div class="col-md-4">
        <div class="card">
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

                <div class="mt-3 d-flex gap-2 flex-wrap">

    <button class="btn btn-sm btn-outline-secondary changeTaskStatus"
            data-status="{{ config('task.status.pause') }}">
        Pause
    </button>

    <button class="btn btn-sm btn-outline-success changeTaskStatus"
            data-status="{{ config('task.status.close') }}">
        Close
    </button>

    <button class="btn btn-sm btn-outline-warning changeTaskStatus"
            data-status="{{ config('task.status.reopen') }}">
        Reopen
    </button>

</div>


                <dl class="row small mb-0">
                    <dt class="col-5">Owner</dt>
                    <dd class="col-7">{{ $task->owner->name ?? '—' }}</dd>

                    <dt class="col-5">Assigned</dt>
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

        {{-- SUB TASKS --}}
        @include('tasks.partials.sub_tasks', ['task' => $task])
    </div>

    {{-- RIGHT : ACTIVITY --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
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

                    <div class="mb-2">
                        <textarea name="message"
                                  class="form-control form-control-sm"
                                  placeholder="Write a comment..."
                                  required></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <input type="file"
                               name="file"
                               class="form-control form-control-sm">

                        <button class="btn btn-sm btn-primary">
                            Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- COMMENT SUBMIT --}}
<script>


$(document).on('click', '.changeTaskStatus', function () {

    const statusId = $(this).data('status');
    preloader.load();

    $.post("{{ route('tasks.subtasks.change_status', $task->id) }}", {
        _token: "{{ csrf_token() }}",
        task_status_id: statusId
    })
    .done(function () {
        preloader.stop();
        reloadTaskView({{ $task->id }});
    })
    .fail(function () {
        preloader.stop();
        showAlert('Status update failed', 'error');
    });
});

$(document).on('submit', '#taskCommentForm', function (e) {
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
    .done(function () {
        preloader.stop();
        reloadTaskView({{ $task->id }});
    })
    .fail(function () {
        preloader.stop();
        showAlert('Failed to add comment', 'error');
    });
});

function reloadTaskView(id) {
    preloader.load();
    $.get(`/tasks/${id}?view=1`, function (html) {
        preloader.stop();
        $('#offcanvasCustomBody').html(html);
    });
}
</script>

@endsection
