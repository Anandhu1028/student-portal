@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tasks</h5>
            <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
                + New Task
            </a>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Owner</th>
                        <th>Due</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->task_type ?? '—' }}</td>
                        <td>
                            {{ $task->status->name ?? '—' }}
                        </td>
                        <td>
                            {{ $task->priority->name ?? '—' }}
                        </td>
                        <td>
                            {{ $task->owner->name ?? '—' }}
                        </td>
                        <td>
                            {{ $task->due_at?->format('d M Y') ?? '—' }}
                        </td>
                        <td>
                            <a href="{{ route('tasks.show', $task->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i>No tasks found</i>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection