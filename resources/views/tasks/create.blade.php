@extends('layouts.layout_ajax')

@section('content')
<form id="taskCreateForm">
@csrf

@if(isset($task))
    <input type="hidden" name="id" value="{{ $task->id }}">
@endif

@php
    $isEdit = isset($task);
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
                <input
                    name="title"
                    class="form-control form-control-sm"
                    required
                    value="{{ old('title', $task->title ?? '') }}">
            </div>

            {{-- OWNER --}}
            <div class="col-md-6">
                <label class="form-label">Owner *</label>

                @if(isset($task))
                    {{-- EDIT MODE → DROPDOWN --}}
                    <select name="task_owner_id"
                            class="form-select form-select-sm"
                            required>
                        <option value="">Select owner</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}"
                                @selected(old(
                                    'task_owner_id',
                                    
                                ) )>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    {{-- CREATE MODE → LOCKED TO LOGGED USER --}}
                    <input type="hidden"
                        name="task_owner_id"
                        value="{{ auth()->id() }}">

                    <input type="text"
                        class="form-control form-control-sm"
                        value="{{ auth()->user()->name }}"
                        readonly>
                @endif
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
                            @selected(
                                old(
                                    'task_status_id',
                                     ?? null
                                ) 
                            )>
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
                            @selected(
                                old(
                                    'task_priority_id',
                                    ?? null
                                ) == 
                            )>
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
                                ? \Illuminate\Support\Carbon::parse($task->due_at)->format('Y-m-d')
                                : ''
                       ) }}">
            </div>


            {{-- ASSIGNEES --}}
            <div class="col-12">
                <label class="form-label">Assigned Users</label>

                @php
                    $assignedIds = old(
                        'assignees',
                        $isEdit ? $task->assignees->pluck('id')->toArray() : []
                    );
                @endphp

                <select name="assignees[]"
                        class="selectpicker form-select form-select-sm"
                        multiple
                        data-live-search="true"
                        data-width="100%">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}"
                            @selected(in_array(, ))>
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

            

        </div>
    </div>

    <div class="card-footer text-end">
        <button class="btn btn-sm btn-primary">
            {{ $isEdit ? 'Update Task' : 'Save Task' }}
        </button>
    </div>
</div>
</form>
@endsection
