<!--------------------------------------------------------------------
       THIS THE TASK LIST SECTION ( HERE LODE TABLE & ACTIVIY FILES).
 ----------------- ------------------------------------------------>

@extends('layouts.layout_ajax')
@section('content')

<div class="card">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Task List</h5>
        <button class="btn btn-sm btn-primary" id="openCreateTask">
            + Create Task
        </button>
    </div>

    <div class="container-fluid card-body">

        {{-- FILTER BAR --}}
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap task-filters">

            <div class="task-search">
                <input type="text"
                    class="form-control form-control-sm taskSearch"
                    placeholder="Search…">
            </div>

            <select class="form-select form-select-sm taskFilter filter-sm"
                data-name="status">
                <option value="">Status</option>
                @foreach($statuses as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>

            <select class="form-select form-select-sm taskFilter filter-sm"
                data-name="priority">
                <option value="">Priority</option>
                @foreach($priorities as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>

            <select class="form-select form-select-sm taskFilter filter-sm"
                data-name="owner">
                <option value="">Owner</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>

            <button class="btn btn-sm btn-light border" id="resetFilters">
                Reset
            </button>

        </div>

        {{-- TABLE --}}
        <div id="taskTable">
            @include('tasks.partials.table')
        </div>

    </div>
</div>

<style>
    .task-search {
        width: 10%;
        min-width: 120px;
    }

    .task-filters .filter-sm {
        width: 120px;
    }
</style>

<script>

let filterTimer = null;

/* ================= LOAD TASKS (AJAX ONLY) ================= */
function loadTasks(page = 1) {
    const params = {
        filter: 1,
        status: $('[data-name="status"]').val(),
        priority: $('[data-name="priority"]').val(),
        owner: $('[data-name="owner"]').val(),
        search: $('.taskSearch').val(),
        page: page
    };

    preloader.load();

    $.get("{{ route('tasks.list') }}", params)
        .done(function (html) {
            $('#taskTable').html(html);

            // re-init tooltips
            document
                .querySelectorAll('#taskTable [data-bs-toggle="tooltip"]')
                .forEach(el => new bootstrap.Tooltip(el));
        })
        .always(() => preloader.stop());
}



/* ================= FILTER EVENTS ================= */
$(document).on('change', '.taskFilter', function () {
    loadTasks(1);
});

$(document).on('keyup', '.taskSearch', function () {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => loadTasks(1), 400);
});

$(document).on('click', '#resetFilters', function () {
    $('.taskFilter').val('');
    $('.taskSearch').val('');
    loadTasks(1);
});





/* ================= PAGINATION ================= */
$(document).on('click', '#taskTable .pagination a', function (e) {
    e.preventDefault();
    const page = new URL(this.href).searchParams.get('page');
    loadTasks(page);
});



/* ================= CREATE TASK (OFFCANVAS) ================= */
$(document).on('click', '#openCreateTask', function (e) {
    e.preventDefault();

    preloader.load();

    $.get("{{ route('tasks.create') }}")
        .done(function (response) {
            const offcanvasEl = document.getElementById('offcanvasCustom');
            const oc = new bootstrap.Offcanvas(offcanvasEl);

            $('#offcanvasCustomHead').html('Create Task');
            $('#offcanvasCustomBody').html(response);

            oc.show();

            setTimeout(() => {
                $('.selectpicker').selectpicker('refresh');
            }, 100);
        })
        .fail(() => showAlert('Unable to load Create Task form', 'error'))
        .always(() => preloader.stop());
});




/* ================= SAVE TASK ================= */
$(document)
    .off('submit.taskSave')
    .on('submit.taskSave', '#taskCreateForm', function (e) {

        e.preventDefault();

        const form = this;
        const data = new FormData(form);

        if ($(form).data('loading')) return;
        $(form).data('loading', true);

        preloader.load();

        $.ajax({
            url: "{{ route('tasks.store') }}",
            method: "POST",
            data: data,
            processData: false,
            contentType: false,
        })
        .done(() => {

            // close offcanvas
            const ocEl = document.getElementById('offcanvasCustom');
            if (ocEl) {
                const oc = bootstrap.Offcanvas.getInstance(ocEl);
                oc?.hide();
            }

            // show success modal
            $('#modal_title_custom').text('Success');
            $('#modal_body_custom').html(
                '<p class="mb-0">Task saved successfully.</p>'
            );

            const modalEl = document.getElementById('modal_custom');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        })
        .fail(xhr => {

            $('#modal_title_custom_error').text('Error');
            $('#modal_body_custom_error').html(
                `<p class="mb-0">${xhr.responseJSON?.message || 'Save failed'}</p>`
            );

            new bootstrap.Modal(
                document.getElementById('modal_custom_error')
            ).show();
        })
        .always(() => {
            preloader.stop();
            $(form).data('loading', false);
        });
});





/* ================= REFRESH LIST AFTER MODAL CLOSE ================= */
$('#modal_custom').on('hidden.bs.modal', function () {
    loadTasks(1);
});
</script>




@endsection