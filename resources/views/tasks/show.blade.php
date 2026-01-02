<!----------------------------------------------
       THIS THE TASK VIEW SECTION (TASK DETAILS).
     ------------------------------------------------>


@extends('layouts.layout_ajax')
@section('content')

<div class="row g-3 task-view-wrapper">

    {{-- LEFT COLUMN: TASK INFO + ACTIVITY (WIDTH: 70%) --}}
    <div class="col-lg-8">

        {{-- TASK INFO CARD --}}
        <div class="card task-info-card mb-3">
            <div class="card-body">

                <h5 class="mb-1">
                    <span class="task-title-ellipsis"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="{{ $task->title }}">
                        {{ \Illuminate\Support\Str::limit($task->title, 60) }}
                    </span>
                </h5>

                <p class="text-muted small mb-3">{{ $task->description ?: '—' }}</p>

                {{-- UPLOADED SCREENSHOTS --}}
                @if($task->attachments->count())
                <div class="mb-3">
                    <div class="small text-muted mb-2 fw-semibold">Uploaded Screenshots</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($task->attachments as $file)
                        <div class="position-relative attachment-thumb">
                            <a href="{{ asset('storage/'.$file->file_path) }}"
                                target="_blank"
                                title="{{ $file->original_name }}">
                                <img src="{{ asset('storage/'.$file->file_path) }}"
                                    alt="attachment">
                            </a>
                            <button class="btn btn-sm btn-danger deleteAttachment"
                                data-id="{{ $file->id }}"
                                title="Remove">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- OWNER & STATUS & PRIORITY & ASSIGNED USERS & FORWARDS & DUE DATE --}}
                <div class="task-meta-wrapper">

                    {{-- ROW 1 --}}
                    <div class="task-meta-row">
                        <div class="meta-left">
                            <span class=" meta-label">Owner:</span>
                            <span class="meta-value">{{ optional($task->owner)->name ?? '—' }}</span>
                        </div>
                        <div class="meta-right">

                            {{-- STATUS --}}
                            <div class="inline-edit-wrapper">
                                <span class="meta-label">Status:</span>

                                <span class="badge bg-secondary status-badge"
                                    data-task-id="{{ $task->id }}"
                                    data-current="{{ $task->task_status_id }}">
                                    {{ optional($task->status)->name ?? '—' }}
                                </span>

                                <i class="ri-pencil-line inline-edit-icon toggleStatusEdit"></i>

                                <select class="form-select form-select-sm inline-select status-select d-none"
                                    data-task-id="{{ $task->id }}">
                                    @foreach(\App\Models\TaskStatus::all() as $status)
                                    <option value="{{ $status->id }}"
                                        {{ $task->task_status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PRIORITY --}}
                            <div class="inline-edit-wrapper ms-3">
                                <span class="meta-label">Priority:</span>

                                <span class="badge bg-info priority-badge"
                                    data-task-id="{{ $task->id }}"
                                    data-current="{{ $task->task_priority_id }}">
                                    {{ optional($task->priority)->name ?? '—' }}
                                </span>

                                <i class="ri-pencil-line inline-edit-icon togglePriorityEdit"></i>

                                <select class="form-select form-select-sm inline-select priority-select d-none"
                                    data-task-id="{{ $task->id }}">
                                    @foreach(\App\Models\TaskPriority::all() as $priority)
                                    <option value="{{ $priority->id }}"
                                        {{ $task->task_priority_id == $priority->id ? 'selected' : '' }}>
                                        {{ $priority->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                    </div>

                    {{-- ROW 2 --}}
                    <div class="task-meta-row">
                        <div class="meta-left">
                            <span class="meta-label">Assigned Users:</span>

                            <span class="meta-value d-flex flex-wrap gap-1">
                                @if($task->assignees && $task->assignees->count())
                                @foreach($task->assignees as $user)
                                <span class="badge bg-primary position-relative pe-3">
                                    {{ $user->name }}

                                    <button type="button"
                                        class="btn btn-sm btn-close deleteAssignee"
                                        data-task="{{ $task->id }}"
                                        data-user="{{ $user->id }}"
                                        title="Remove user">
                                    </button>
                                </span>
                                @endforeach
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </span>
                        </div>


                       <div class="meta-right">
    <span class="meta-label">Forward To:</span>

    @if($task->forwards && $task->forwards->count())
        <div class="d-flex flex-wrap gap-2">
            @foreach($task->forwards as $forward)

                @if($forward->department)
                    <span class="badge bg-warning text-dark forward-badge">
                        Dept: {{ $forward->department->name }}

                        <button
                            type="button"
                            class="btn btn-sm btn-danger forward-remove revokeForward"
                            data-id="{{ $forward->id }}"
                            title="Remove forward">
                            ×
                        </button>
                    </span>

                @elseif($forward->user)
                    <span class="badge bg-info text-dark forward-badge">
                        User: {{ $forward->user->name }}

                        <button
                            type="button"
                            class="btn btn-sm btn-danger forward-remove revokeForward"
                            data-id="{{ $forward->id }}"
                            title="Remove forward">
                            ×
                        </button>
                    </span>
                @endif

            @endforeach
        </div>
    @else
        <span class="text-muted">—</span>
    @endif
</div>




                    </div>

                    {{-- ROW 3 --}}
                    <div class="task-meta-row">
                        <div class="meta-left">
                            <span class="meta-label">Due Date:</span>
                            <span class="meta-value">
                                {{ $task->due_at?->format('d M Y') ?? '—' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ACTIVITY CARD --}}
        <div class="card activity-card">
            <div class="card-header">
                <strong>Activity</strong>
            </div>

            <div class="card-body activity-body">
                @include('tasks.partials.activity_timeline', [
                'activities' => $task->activities
                ])
            </div>

            {{-- COMMENT & FILE ATTACHMENT SECTION --}}

            <div class="card-footer comment-footer">
                <form id="taskCommentForm" enctype="multipart/form-data">
                    @csrf

                    {{-- Comment Input Area --}}
                    <div class="comment-input-wrapper">
                        <div class="user-avatar-small">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="comment-input-container">
                            <textarea name="message"
                                class="form-control comment-textarea"
                                placeholder="Write a comment..."
                                rows="1"></textarea>

                            {{-- File Preview Area --}}
                            <div id="filePreviewArea" class="file-preview-area" style="display: none;">
                                <div class="file-preview-item">
                                    <svg class="file-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="file-name">No file selected</span>
                                    <button type="button" class="remove-file-btn" title="Remove file">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="comment-actions">
                        <div class="action-left">
                            <label for="fileInput" class="attach-btn" title="Attach file">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Attach
                            </label>
                            <input type="file"
                                name="file"
                                id="fileInput"
                                class="file-input-hidden"
                                accept="image/*,.pdf,.doc,.docx">
                        </div>
                        <button type="submit" class="btn-post">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: SUB TASKS (WIDTH : 30%) --}}
    <div class="col-lg-4">
        @include('tasks.partials.sub_tasks', ['task' => $task])
    </div>

</div>

<style>
    .forward-badge {
    position: relative;
    padding-right: 22px;
}

.forward-remove {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    padding: 0;
    border-radius: 50%;
    font-size: 14px;
    line-height: 1;
    opacity: 0;
    transition: opacity 0.2s;
}

.forward-badge:hover .forward-remove {
    opacity: 1;
}



    .meta-value .badge,
    .meta-right .badge {
        font-size: 11px;
        padding: 4px 18px 4px 8px;
        border-radius: 10px;
    }

    .badge .btn-close {
        position: absolute;
        top: 2px;
        right: 4px;
        font-size: 10px;
        opacity: 0;
    }

    .badge:hover .btn-close {
        opacity: 1;
    }


    /* ================= STATUS & PRIORITY  ================= */
    .inline-edit-wrapper {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .inline-edit-icon {
        font-size: 14px;
        cursor: pointer;
        color: #6b7280;
    }

    .inline-edit-icon:hover {
        color: #111827;
    }

    .inline-select {
        width: auto;
        min-width: 140px;
        font-size: 12px;
    }



    /* ================= LAYOUT ================= */
    .task-view-wrapper {
        padding: 0;
    }

    /* ================= TASK INFO CARD ================= */
    .task-info-card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }



    /* Attachments */
    .attachment-thumb {
        position: relative;
        display: inline-block;
    }

    .attachment-thumb img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .attachment-thumb .deleteAttachment {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 20px;
        height: 20px;
        padding: 0;
        border-radius: 50%;
        font-size: 14px;
        line-height: 1;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .attachment-thumb:hover .deleteAttachment {
        opacity: 1;
    }

    /* ================= TASK META ================= */
    .task-meta-wrapper {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .task-meta-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 16px;
    }

    .meta-left {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 8px;
        align-items: center;
    }

    .meta-right {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .meta-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        white-space: nowrap;
    }

    .meta-value {
        font-size: 13px;
        font-weight: 500;
        color: #111827;
    }

    .meta-right .badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 500;
    }

    /* ================= ACTIVITY CARD ================= */
    .activity-card {
        height: 600px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .activity-card .card-header {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 16px;
        font-size: 14px;
    }

    .activity-body {
        max-height: 500px;
        overflow-y: auto;
        padding: 16px;
    }

    /* Custom scrollbar */
    .activity-body::-webkit-scrollbar {
        width: 6px;
    }

    .activity-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .activity-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .activity-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ================= ACTIVITY TIMELINE ================= */
    .activity-timeline {
        font-size: 13px;
    }

    .activity-item {
        align-items: flex-start;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #66d8ea 0%, #0c7387 100%);
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-user {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .activity-time {
        font-size: 11px;
        color: #9ca3af;
        white-space: nowrap;
    }

    .activity-message {
        font-size: 13px;
        line-height: 1.6;
        color: #374151;
        margin-top: 4px;
    }

    .activity-attachments {
        margin-top: 8px;
    }

    .activity-attachment {
        display: block;
        width: 80px;
        height: 80px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: transform 0.2s;
    }

    .activity-attachment:hover {
        transform: scale(1.05);
    }

    .activity-attachment img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 991px) {
        .task-meta-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .meta-right {
            justify-content: flex-start;
        }

        .activity-body {
            max-height: 400px;
        }
    }

    @media (max-width: 576px) {
        .meta-left {
            grid-template-columns: 1fr;
        }

        .task-title {
            font-size: 16px;
        }
    }


    /* ================= ENHANCED COMMENT FOOTER ================= */
    .comment-footer {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 16px;
    }

    /* Comment Input Wrapper */
    .comment-input-wrapper {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }

    .user-avatar-small {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #66d8ea 0%, #0c7387 100%);
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .comment-input-container {
        flex: 1;
    }

    .comment-textarea {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        line-height: 1.5;
        resize: none;
        transition: all 0.2s;
        min-height: 44px;
    }

    .comment-textarea:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
        min-height: 80px;
    }

    .comment-textarea::placeholder {
        color: #9ca3af;
    }

    /* File Preview Area */
    .file-preview-area {
        margin-top: 8px;
        animation: slideDown 0.2s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .file-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
    }

    .file-icon {
        width: 20px;
        height: 20px;
        color: #667eea;
        flex-shrink: 0;
    }

    .file-name {
        flex: 1;
        color: #374151;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .remove-file-btn {
        background: none;
        border: none;
        padding: 4px;
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s;
        display: flex;
        align-items: center;
    }

    .remove-file-btn:hover {
        color: #ef4444;
    }

    .remove-file-btn svg {
        width: 18px;
        height: 18px;
    }

    /* Comment Actions */
    .comment-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .action-left {
        display: flex;
        gap: 8px;
    }

    .file-input-hidden {
        display: none;
    }

    .attach-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0;
    }

    .attach-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .attach-btn svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
    }

    .btn-post {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #66cbeaff 0%, #4b9ca2ff 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(102, 197, 234, 0.2);
    }

    .btn-post:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 197, 234, 0.3);
    }

    .btn-post:active {
        transform: translateY(0);
    }

    .btn-post svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .comment-input-wrapper {
            flex-direction: column;
            gap: 8px;
        }

        .user-avatar-small {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }

        .comment-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .action-left {
            justify-content: center;
        }

        .btn-post {
            justify-content: center;
            width: 100%;
        }
    }

    
</style>

{{-- SCRIPTS --}}


<script>
    /* ================= GLOBALS ================= */
    const TASK_ID = {{ $task->id }};
    const CSRF = "{{ csrf_token() }}";


    /* ================= TASK RELOAD ================= */
    function reloadTaskView() {
        preloader.load();
        $.get(`/tasks/${TASK_ID}?view=1`)
            .done(html => $('#offcanvasCustomBody').html(html))
            .fail(() => showAlert('Failed to reload task view', 'error'))
            .always(() => preloader.stop());
    }

    function ajaxFail(xhr, msg) {
        showAlert(xhr.responseJSON?.message || msg, 'error');
    }


    /* ================= DELETE TASK ATTACHMENT ================= */
   $(document).off('click', '.deleteAttachment');
$(document).on('click', '.deleteAttachment', function (e) {

    e.preventDefault();
    e.stopPropagation();

    const btn = $(this);
    const attachmentId = btn.data('id');
    const wrapper = btn.closest('.attachment-thumb');

    if (!attachmentId) {
        showAlert('Invalid attachment', 'error');
        return;
    }

    if (!confirm('Are you sure you want to delete this attachment?')) return;

   

    $.ajax({
        url: "{{ route('tasks.attachments.delete', ':id') }}".replace(':id', attachmentId),
        method: 'POST', // 🔥 IMPORTANT: POST, NOT DELETE
        data: {
            _token: "{{ csrf_token() }}",
            _method: 'DELETE' // Laravel-safe delete
        }
    })
    .done(function (res) {
        if (res && res.success) {
            wrapper.fadeOut(200, function () {
                $(this).remove();
            });
            showAlert('Attachment deleted successfully', 'success');
        } else {
            showAlert(res?.message || 'Delete failed', 'error');
        }
    })
    .fail(function (xhr) {
        showAlert(
            xhr.responseJSON?.message || 'Failed to delete attachment',
            'error'
        );
    })
    .always(function () {
        preloader.stop();
    });
});

    /* ================= SUBMIT COMMENT ================= */
    $(document).on('submit', '#taskCommentForm', function (e) {

        e.preventDefault();

        const formData = new FormData(this);
        if (!formData.get('message')?.trim()) {
            showAlert('Comment cannot be empty', 'error');
            return;
        }

        preloader.load();

        $.ajax({
            url: "{{ route('tasks.comment', $task->id) }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(() => {
            this.reset();
            reloadTaskView();
        })
        .fail(xhr => ajaxFail(xhr, 'Failed to add comment'))
        .always(() => preloader.stop());
    });


    /* ================= DELETE FORWARD ================= */
   
$(document).off('click', '.revokeForward');
$(document).on('click', '.revokeForward', function (e) {

    e.preventDefault();
    e.stopPropagation();

    const btn = $(this);
    const forwardId = btn.data('id');
    const badge = btn.closest('.forward-badge');

    if (!confirm('Do you want to remove this forward?')) return;

    

    $.ajax({
        url: `/tasks/forwards/${forwardId}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF
        }
    })
    .done(function (res) {

        if (!res.success) {
            showAlert('Failed to delete forward', 'error');
            return;
        }

        // 🔥 REMOVE FORWARD BADGE INSTANTLY
        badge.fadeOut(200, function () {
            $(this).remove();
        });

        showAlert('Forward removed', 'success');

        // 🔥 INSTANT ACTIVITY APPEND
        if (res.activity) {
            $('.activity-timeline').prepend(`
                <div class="activity-item d-flex gap-3">
                    <div class="activity-avatar">
                        ${res.activity.user.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span class="activity-user">${res.activity.user}</span>
                            <span class="activity-time">${res.activity.time}</span>
                        </div>
                        <div class="activity-message">
                            ${res.activity.message}
                        </div>
                    </div>
                </div>
            `);
        }
    })
    .fail(function () {
        showAlert('Failed to delete forward', 'error');
    })
    .always(function () {
        preloader.stop();
    });
});



    /* ================= INLINE STATUS EDIT ================= */
    $(document).on('click', '.toggleStatusEdit', function () {
        const wrapper = $(this).closest('.inline-edit-wrapper');
        wrapper.find('.status-badge').addClass('d-none');
        wrapper.find('.status-select').removeClass('d-none').focus();
    });

    $(document).on('change', '.status-select', function () {

        const taskId = $(this).data('task-id');
        const statusId = $(this).val();

        preloader.load();

        $.post(`/tasks/${taskId}/status-inline`, {
            _token: CSRF,
            task_status_id: statusId
        })
        .done(() => reloadTaskView())
        .fail(() => showAlert('Failed to update status', 'error'))
        .always(() => preloader.stop());
    });


    /* ================= INLINE PRIORITY EDIT ================= */
    $(document).on('click', '.togglePriorityEdit', function () {
        const wrapper = $(this).closest('.inline-edit-wrapper');
        wrapper.find('.priority-badge').addClass('d-none');
        wrapper.find('.priority-select').removeClass('d-none').focus();
    });

    $(document).on('change', '.priority-select', function () {

        const taskId = $(this).data('task-id');
        const priorityId = $(this).val();

        preloader.load();

        $.post(`/tasks/${taskId}/priority-inline`, {
            _token: CSRF,
            task_priority_id: priorityId
        })
        .done(() => reloadTaskView())
        .fail(() => showAlert('Failed to update priority', 'error'))
        .always(() => preloader.stop());
    });


    /* ================= REMOVE ASSIGNED USER (INSTANT) ================= */
$(document).on('click', '.deleteAssignee', function () {

    const btn = $(this);
    const badge = btn.closest('.badge');
    const taskId = btn.data('task');
    const userId = btn.data('user');

    if (!confirm('Remove this user from task?')) return;

    

    $.ajax({
        url: `/tasks/${taskId}/assignees/${userId}`,
        type: 'DELETE',
        data: { _token: CSRF }
    })
    .done(() => {
        badge.fadeOut(200, function () {
            $(this).remove();
        });
        showAlert('User removed', 'success');
    })
    .fail(() => showAlert('Failed to remove user', 'error'))
    .always(() => preloader.stop());
});
</script>



@endsection