<form id="subTaskEditForm">
    @csrf
    @method('PATCH')

    <input class="form-control mb-2" name="title" value="{{ $subtask->title }}" required>

    <textarea class="form-control mb-2" name="description">{{ $subtask->description }}</textarea>

    <select name="task_status_id" class="form-select mb-2">
        @foreach(\App\Models\TaskStatus::all() as $s)
            <option value="{{ $s->id }}" @selected($s->id == $subtask->task_status_id)>
                {{ $s->name }}
            </option>
        @endforeach
    </select>

    <select name="task_priority_id" class="form-select mb-2">
        @foreach(\App\Models\TaskPriority::all() as $p)
            <option value="{{ $p->id }}" @selected($p->id == $subtask->task_priority_id)>
                {{ $p->name }}
            </option>
        @endforeach
    </select>

    <input type="date" class="form-control mb-2" name="due_at" value="{{ $subtask->due_at?->format('Y-m-d') }}">

    <button class="btn btn-primary w-100">Save</button>
</form>