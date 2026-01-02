<!----------------------------------------------
       THIS THE TASK CREATING(EDITING) SECTION.
     ------------------------------------------------>

@extends('layouts.layout_ajax')

@section('content')
<form id="taskCreateForm" enctype="multipart/form-data">

    @csrf

    @if(isset($task))
        <input type="hidden" name="id" value="{{ $task->id }}">
    @endif

    @php
        $isEdit = isset($task);
        $assignedIds = old(
            'assignees',
            $isEdit ? $task->assignees->pluck('id')->toArray() : []
        );
    @endphp

    <div class="card">
        <div class="card-header">
            <h5>{{ $isEdit ? 'Edit Task' : 'Create Task' }}</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                {{-- TITLE --}}
                <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input type="text"
                        name="title"
                        class="form-control form-control-sm"
                        placeholder="Eg: Payment verification pending"
                        required
                        value="{{ old('title', $task->title ?? '') }}">
                </div>

                {{-- OWNER (READ ONLY) --}}
                <div class="col-md-6">
                    <label class="form-label">Owner *</label>

                    <input type="hidden"
                        name="task_owner_id"
                        value="{{ old('task_owner_id', $task->task_owner_id ?? auth()->id()) }}">

                    <input type="text"
                        class="form-control form-control-sm"
                        value="{{ auth()->user()->name }}"
                        readonly>
                </div>

                {{-- STATUS --}}
                <div class="col-md-6">
                    <label class="form-label">Status *</label>
                    <select name="task_status_id"
                        class="form-select form-select-sm"
                        required>
                        <option value="">Select task status</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->id }}"
                                {{ old('task_status_id', $task->task_status_id ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PRIORITY --}}
                <div class="col-md-6">
                    <label class="form-label">Priority *</label>
                    <select name="task_priority_id"
                        class="form-select form-select-sm"
                        required>
                        <option value="">Select priority level</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p->id }}"
                                {{ old('task_priority_id', $task->task_priority_id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DUE DATE --}}
                <div class="col-md-6">
                    <label class="form-label">Due Date</label>
                    <input type="date"
                        name="due_at"
                        class="form-control form-control-sm"
                        placeholder="Select due date"
                        value="{{ old(
                            'due_at',
                            isset($task->due_at)
                                ? \Carbon\Carbon::parse($task->due_at)->format('Y-m-d')
                                : ''
                        ) }}">
                </div>

                {{-- ASSIGNEES --}}
                <div class="col-md-6">
                    <label class="form-label">Assigned Users</label>
                    <select name="assignees[]"
                        class="form-control form-control-sm selectpicker"
                        multiple
                        data-live-search="true"
                        data-actions-box="true"
                        data-width="100%"
                        title="Select users to assign this task">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}"
                                {{ in_array($u->id, $assignedIds) ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DESCRIPTION --}}
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description"
                        class="form-control form-control-sm"
                        rows="3"
                        placeholder="Describe the issue, requirements, or action needed...">{{ old('description', $task->description ?? '') }}</textarea>
                </div>

                {{-- ATTACHMENTS --}}
                <div class="col-12">
                    <label class="form-label">Screenshots / Attachments</label>
                    <input type="file"
                        name="attachments[]"
                        class="form-control form-control-sm"
                        multiple
                        accept="image/*">
                    <small class="text-muted">
                        Upload screenshots or proofs (PNG, JPG — max 10MB each)
                    </small>
                </div>

            </div>
        </div>
    </div>
</form>


<script>
$(function () {

    // init selectpicker
    const $picker = $('.selectpicker');
    if ($picker.length) {
        $picker.selectpicker('destroy').selectpicker({
            liveSearch: true,
            actionsBox: true,
            width: '100%'
        });
    }

    // footer button (create[save] / edit[update])
    $('#offcanvasCustomFooter').html(`
        <button class="btn btn-sm btn-primary" onclick="submitTaskForm()">
            {{ isset($task) ? 'Update Task' : 'Save Task' }}
        </button>
    `);
});

// submit handler
function submitTaskForm() {
    $('#taskCreateForm').trigger('submit');
}


</script>


@endsection