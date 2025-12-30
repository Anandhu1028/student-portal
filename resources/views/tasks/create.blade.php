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
                        <option value="">Select status</option>
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
                        <option value="">Select priority</option>
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
                        value="{{ old(
                                'due_at',
                                isset($task->due_at)
                                    ? \Carbon\Carbon::parse($task->due_at)->format('Y-m-d')
                                    : ''
                           ) }}">
                </div>

                {{-- ASSIGNEES (FIXED) --}}
                <div class="col-md-6">
                    <label class="form-label">Assigned Users</label>

                    <select name="assignees[]"
                        class="form-control form-control-sm selectpicker"
                        multiple
                        data-live-search="true"
                        data-actions-box="true"
                        data-width="100%">

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
                        rows="3">{{ old('description', $task->description ?? '') }}</textarea>
                </div>


                <div class="col-12">
                    <label class="form-label">Screenshots / Attachments</label>
                    <input type="file"
                        name="attachments[]"
                        class="form-control form-control-sm"
                        multiple
                        accept="image/*">
                    <small class="text-muted">PNG, JPG, max 10MB each</small>
                </div>


            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-sm btn-primary">
                {{ $isEdit ? 'Update Task' : 'Save Task' }}
            </button>

        </div>
    </div>
</form>

<script>
    setTimeout(function() {

        const $picker = $('.selectpicker');

        if ($picker.data('selectpicker')) {
            $picker.selectpicker('destroy');
        }

        $picker.selectpicker({
            liveSearch: true,
            actionsBox: true,
            width: '100%'
        });

    }, 150);
</script>

@endsection