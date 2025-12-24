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
               <form id="taskCommentForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-2">
                            <textarea name="message"
                                    class="form-control form-control-sm"
                                    rows="2"
                                    placeholder="Write a comment…"
                                    required></textarea>
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <input type="file"
                                name="file"
                                class="form-control form-control-sm">

                            <button type="submit"
                                    class="btn btn-sm btn-primary"
                                    id="postCommentBtn">
                                Post
                            </button>
                        </div>
                    </form>




            </div>
        </div>
    </div>
</div>



<style>
/* ================= PAGE BACKGROUND ================= */
.row {
    background: #f6f8fb;
    padding: 18px;
    border-radius: 14px;
}

/* ================= GRID WIDTH CONTROL ================= */
.col-md-5 {
    flex: 0 0 74.666667%;
    max-width: 74.666667%;
}

.col-md-7 {
    flex: 0 0 25.333333%;
    max-width: 25.333333%;
}

/* ================= CARDS ================= */
.card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 10px 28px rgba(0,0,0,.06);
}

.card-header {
    background: #fff;
    font-weight: 600;
    border-bottom: 1px solid #eef0f3;
}

/* ================= TASK DETAILS ================= */
.card-body h4 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 6px;
}

.card-body p {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 14px;
}

.badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 20px;
}

/* meta labels */
dl dt {
    font-size: 11px;
    color: #adb5bd;
    text-transform: uppercase;
}

dl dd {
    font-size: 14px;
    font-weight: 500;
}

/* ================= SUB TASKS ================= */
#subTaskList {
    padding-left: 0;
}

#subTaskList .list-group-item {
    border: none;
    border-radius: 10px;
    margin-bottom: 10px;
    padding: 12px 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,.04);
    transition: transform .15s ease, box-shadow .15s ease;
}

#subTaskList .list-group-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
}

/* ================= ACTIVITY TIMELINE ================= */
.timeline {
    position: relative;
    padding-left: 28px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline li {
    position: relative;
}

.timeline li::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 8px;
    width: 10px;
    height: 10px;
    background: #0d6efd;
    border-radius: 50%;
}

/* avatar */
.avatar {
    width: 34px;
    height: 34px;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ================= ACTIVITY CARD FIXED HEIGHT ================= */
.col-md-7 .card {
    height: 926px;
    display: flex;
    flex-direction: column;
}

/* Activity body scrolls */
.col-md-7 .card-body {
    flex: 1;
    overflow-y: auto;
    padding-right: 10px;
    scroll-behavior: smooth;
}

/* ================= COMMENT FORM ================= */
#taskCommentForm textarea {
    border-radius: 10px;
    resize: none;
    font-size: 13px;
}

#taskCommentForm button {
    border-radius: 20px;
    padding: 4px 16px;
}

/* ================= CARD FOOTER SPACING ================= */
.col-md-7 .card-footer {
        padding-top: 20px;
}

/* ================= SCROLLBAR (ACTIVITY ONLY) ================= */
.col-md-7 .card-body::-webkit-scrollbar {
    width: 6px;
}

.col-md-7 .card-body::-webkit-scrollbar-thumb {
    background: #d6dae0;
    border-radius: 10px;
}

.col-md-7 .card-body::-webkit-scrollbar-track {
    background: transparent;
}
</style>



<script>



$(document).on('submit', '#taskCommentForm', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    const form = this;
    const formData = new FormData(form);
    const btn = document.getElementById('postCommentBtn');

    btn.disabled = true;
    btn.innerText = 'Posting…';

    $.ajax({
        url: "{{ route('tasks.comment', $task->id) }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    })
    .done(function (res) {
        appendActivity(res.activity);
        form.reset();
    })
    .fail(function (xhr) {
        console.error(xhr.responseText);
        alert('Failed to post comment');
    })
    .always(function () {
        btn.disabled = false;
        btn.innerText = 'Post';
    });
});

function appendActivity(activity) {

    let attachments = '';
    if (activity.attachments.length) {
        attachments = activity.attachments.map(att => `
            <div>
                <a href="/storage/${att.file_path}" target="_blank">
                    📎 Attachment
                </a>
            </div>
        `).join('');
    }

    const html = `
        <li class="mb-3">
            <div class="d-flex gap-2">
                <div class="avatar bg-primary text-white rounded-circle px-2">
                    ${activity.actor.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <strong>${activity.actor.name}</strong>
                    <small class="text-muted"> • just now</small>
                    <div class="small">${activity.message}</div>
                    ${attachments}
                </div>
            </div>
        </li>
    `;

    $('.timeline').prepend(html);
}





function appendSubTask(s) {
    $('#subTaskList').prepend(`
        <li class="list-group-item" id="subtask-${s.id}">
            <strong>${s.title}</strong>
            <div class="small text-muted">
                ${s.status.name} • ${s.priority.name} • ${s.due_at ?? '—'}
            </div>
            <div class="mt-1">
                <button class="btn btn-sm btn-outline-secondary editSubTask" data-id="${s.id}">Edit</button>
            </div>
        </li>
    `);
}

$(document).on('click', '#openAddSubTask', function () {
    const oc = new bootstrap.Offcanvas('#offcanvasCustom');

    $('#offcanvasCustomHead').html('Add Sub-task');
    $('#offcanvasCustomBody').html(`
        <form id="subTaskCreateForm">
            <input class="form-control mb-2" name="title" placeholder="Title" required>
            <textarea class="form-control mb-2" name="description"></textarea>
            <select name="task_status_id" class="form-select mb-2">@foreach(\App\Models\TaskStatus::all() as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            <select name="task_priority_id" class="form-select mb-2">@foreach(\App\Models\TaskPriority::all() as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
            <input type="date" class="form-control mb-2" name="due_at">
            <button class="btn btn-primary w-100">Save</button>
        </form>
    `);

    oc.show();
});

$(document).on('submit', '#subTaskCreateForm', function (e) {
    e.preventDefault();

    $.post('/tasks/{{ $task->id }}/subtasks', $(this).serialize())
        .done(res => {
            appendSubTask(res.data.subtask);
            appendActivity(res.data.activity);
            bootstrap.Offcanvas.getInstance('#offcanvasCustom').hide();
        });
});

$(document).on('submit', '#subTaskEditForm', function (e) {
    e.preventDefault();
    const id = {{ $subtask->id ?? 'null' }};

    $.ajax({
        url: '/tasks/subtasks/' + id,
        type: 'PATCH',
        data: $(this).serialize()
    }).done(res => {
        $('#subtask-' + id).replaceWith(`
            <li class="list-group-item" id="subtask-${id}">
                <strong>${res.data.subtask.title}</strong>
                <div class="small text-muted">
                    ${res.data.subtask.status.name} • ${res.data.subtask.priority.name}
                </div>
            </li>
        `);
        appendActivity(res.data.activity);
        bootstrap.Offcanvas.getInstance('#offcanvasCustom').hide();
    });
});

</script>




@endsection
