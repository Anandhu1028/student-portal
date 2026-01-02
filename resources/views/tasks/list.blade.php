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

           {{-- DATE --}}
            <input type="date"
                class="form-control form-control-sm taskFilter filter-sm"
                data-name="date"
                title="Created date">



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

/* ================= LOAD TASKS (FINAL FIX) ================= */
function loadTasks(page = 1) {

    const params = {
        filter: 1,
        status: $('[data-name="status"]').val() || '',
        priority: $('[data-name="priority"]').val() || '',
        owner: $('[data-name="owner"]').val() || '',
        date: $('[data-name="date"]').val() || '',
        search: $('.taskSearch').val() || '',
        page: page
    };

    preloader.load();

    $.ajax({
        url: "{{ route('tasks.list') }}",
        type: "GET",
        data: params,
        cache: false
    })
    .done(function (html) {

        /* 🔥 THIS IS THE FIX */
        $('#taskTable').html(html);

        /* re-init tooltips */
        document
            .querySelectorAll('#taskTable [data-bs-toggle="tooltip"]')
            .forEach(el => bootstrap.Tooltip.getOrCreateInstance(el));
    })
    .fail(() => showAlert('Failed to reload task list', 'error'))
    .always(() => preloader.stop());
}

/* ================= FILTER EVENTS ================= */
$(document).on('change', '.taskFilter', () => loadTasks(1));

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
        .done(response => {
            const ocEl = document.getElementById('offcanvasCustom');
            const oc = bootstrap.Offcanvas.getOrCreateInstance(ocEl);

            $('#offcanvasCustomHead').html('Create Task');
            $('#offcanvasCustomBody').html(response);
            oc.show();

            setTimeout(() => $('.selectpicker').selectpicker('refresh'), 100);
        })
        .fail(() => showAlert('Unable to load Create Task form', 'error'))
        .always(() => preloader.stop());
});

/* ================= SAVE TASK (🔥 FIXED) ================= */
$(document)
    .off('submit.taskSave')
    .on('submit.taskSave', '#taskCreateForm', function (e) {

        e.preventDefault();

        const form = this;
        if ($(form).data('loading')) return;
        $(form).data('loading', true);

        preloader.load();

        $.ajax({
            url: "{{ route('tasks.store') }}",
            method: "POST",
            data: new FormData(form),
            processData: false,
            contentType: false
        })
        .done(() => {

            /* CLOSE OFFCANVAS */
            const ocEl = document.getElementById('offcanvasCustom');
            bootstrap.Offcanvas.getInstance(ocEl)?.hide();

            /* 🔥 INSTANT TABLE REFRESH */
            loadTasks(1);

            /* SUCCESS MODAL */
            $('#modal_title_custom').text('Success');
            $('#modal_body_custom').html('<p class="mb-0">Task saved successfully.</p>');
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modal_custom')
            ).show();
        })
        .fail(xhr => {
            $('#modal_title_custom_error').text('Error');
            $('#modal_body_custom_error').html(
                `<p class="mb-0">${xhr.responseJSON?.message || 'Save failed'}</p>`
            );
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modal_custom_error')
            ).show();
        })
        .always(() => {
            preloader.stop();
            $(form).data('loading', false);
        });
});
</script>





@endsection