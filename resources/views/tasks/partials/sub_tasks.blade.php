<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <strong>Sub Tasks</strong>
        <button class="btn btn-sm btn-outline-primary addSubTask">
            + Add
        </button>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Owner</th>
                    <th>Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($task->subTasks as $sub)
                <tr>
                    <td>{{ $sub->title }}</td>
                    <td>{{ $sub->status->name ?? '—' }}</td>
                    <td>{{ $sub->owner->name ?? '—' }}</td>
                    <td>{{ $sub->due_at ? $sub->due_at->format('d M Y') : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No sub-tasks
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
