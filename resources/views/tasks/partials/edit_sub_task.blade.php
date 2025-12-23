<form id="subTaskEditForm">
    @csrf
    @method('PATCH')
    <div class="mb-2">
        <label class="form-label">Title</label>
        <input name="title" value="{{ $subtask->title }}" class="form-control form-control-sm" required />
    </div>
    <div class="mb-2">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control form-control-sm">{{ $subtask->description }}</textarea>
    </div>
    <div class="text-end">
        <button class="btn btn-sm btn-primary">Save</button>
    </div>

    <script>
    $(document).on('submit', '#subTaskEditForm', function (e) {
        e.preventDefault();
        const id = {{ $subtask->id }};
        preloader.load();
        $.ajax({
            url: "{{ url('/tasks/subtasks') }}/" + id,
            type: 'PATCH',
            data: $(this).serialize()
        }).done(function () { preloader.stop(); loadTaskView({{ $subtask->task_id }}); }).fail(function () { preloader.stop(); showAlert('Failed to save', 'error'); });
    });
    </script>
</form>
