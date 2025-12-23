@extends('layouts.layout_ajax')

@section('content')
<form method="POST" action="{{ route('tasks.store') }}">
@csrf

<div class="card">
    <div class="card-header">
        <h5>Create Task</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Title *</label>
                <input name="title" class="form-control form-control-sm" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Owner *</label>
                <select name="task_owner_id" class="form-select form-select-sm" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select name="task_status_id" class="form-select form-select-sm" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Priority *</label>
                <select name="task_priority_id" class="form-select form-select-sm" required>
                    @foreach($priorities as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control form-control-sm"></textarea>
            </div>

            <div class="col-12">
                <label class="form-label">Assigned Users</label>
                <select name="assignees[]" class="form-select form-select-sm" multiple>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    <div class="card-footer text-end">
        <button class="btn btn-sm btn-primary">
            Save Task
        </button>
    </div>
</div>
</form>
@endsection
<script>
$(document).on('click', '#saveTaskBtn', function () {

    const form = $('#taskCreateForm');

    if (!form.length) {
        showAlert('Task form not loaded', 'error');
        return;
    }

    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }

    preloader.load();

    $.ajax({
        url: "{{ route('tasks.store') }}",
        type: "POST",
        data: form.serialize(),
        success: function () {

            preloader.stop();

            // Close offcanvas
            const canvas = bootstrap.Offcanvas.getInstance(
                document.getElementById('offcanvasCustom')
            );
            if (canvas) canvas.hide();

            // Clear footer (important)
            $('#offcanvasCustomFooter').html('');

            // Reload task list
            loadTasks();

            showAlert('Task created successfully');
        },
        error: function (xhr) {
            preloader.stop();
            showAlert(
                xhr.responseJSON?.message || 'Failed to save task',
                'error'
            );
        }
    });
});


function loadTasks(page = 1) {
    $.get("{{ route('tasks.list') }}", {
        filter: 1,
        status: $('[data-name="status"]').val(),
        priority: $('[data-name="priority"]').val(),
        owner: $('[data-name="owner"]').val(),
        search: $('.taskSearch').val(),
        page: page
    }, function (html) {
        $('#taskTable').html(html);
    });
}

</script>
