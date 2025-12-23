@extends('layouts.layout_ajax')
@section('content')

<div class="row">
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-body">
                <h4>{{ $task->title }}</h4>
                <p class="text-muted">{{ $task->description }}</p>

                <div class="mb-2">
                    <span class="badge bg-secondary">Status: {{ $task->status->name ?? '—' }}</span>
                    <span class="badge bg-info">Priority: {{ $task->priority->name ?? '—' }}</span>
                </div>

                <dl class="row">
                    <dt class="col-4">Owner</dt>
                    <dd class="col-8">{{ $task->owner->name ?? '—' }}</dd>

                    <dt class="col-4">Assigned</dt>
                    <dd class="col-8">{{ $task->assignees->pluck('name')->join(', ') ?: '—' }}</dd>

                    <dt class="col-4">Due</dt>
                    <dd class="col-8">{{ optional($task->due_at)->format('d M Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        {{-- SUB-TASKS --}}
        @include('tasks.partials.sub_tasks', ['task' => $task])

    </div>

    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header">Activity</div>
            <div class="card-body">
                @include('tasks.partials.activity_timeline', ['activities' => $task->activities])
            </div>
            <div class="card-footer">
                <form id="taskCommentForm">
                    @csrf
                    <div class="mb-2">
                        <textarea name="message" class="form-control form-control-sm" placeholder="Write a comment..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="file" name="file" class="form-control form-control-sm" />
                        <button class="btn btn-sm btn-primary" id="postCommentBtn">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Submit comment
$(document).on('submit', '#taskCommentForm', function (e) {
    e.preventDefault();

    const $form = $(this);
    const data = new FormData(this);

    preloader.load();

    $.ajax({
        url: "{{ route('tasks.comment', $task->id) }}",
        type: 'POST',
        data: data,
        processData: false,
        contentType: false
    }).done(function (res) {
        preloader.stop();
        // prepend new activity
        loadTaskView({{ $task->id }});
        $form[0].reset();
    }).fail(function () {
        preloader.stop();
        showAlert('Failed to post comment', 'error');
    });
});

// helper to reload task view (offcanvas)
function loadTaskView(id) {
    preloader.load();
    $.get("{{ url('/tasks') }}/" + id + "?view=1")
        .done(function (html) {
            preloader.stop();
            $('#offcanvasCustomBody').html(html);
        })
        .fail(function () { preloader.stop(); showAlert('Unable to refresh task', 'error'); });
}

</script>

@endsection
