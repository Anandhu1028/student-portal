<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Title</th>
    <th>Status</th>
    <th>Priority</th>
    <th>Owner</th>
    <th>Assigned</th>
    <th>Due</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
@forelse($tasks as $task)
<tr>
    <td>{{ $task->title }}</td>
    <td>{{ $task->status->name }}</td>
    <td>{{ $task->priority->name }}</td>
    <td>{{ $task->owner->name }}</td>
    <td>
        {{ $task->assignees->pluck('name')->take(2)->join(', ') }}
        @if($task->assignees->count() > 2)
            …
        @endif
    </td>
    <td>{{ optional($task->due_at)->format('d M Y') }}</td>
    <td>
        <a href="{{ route('tasks.show',$task) }}"
           class="btn btn-sm btn-outline-primary">
            View
        </a>
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
