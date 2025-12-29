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

    /* ================= LOAD TASKS ================= */
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
            .done(function(html) {
                $('#taskTable').html(html);

                // re-init tooltips
                document.querySelectorAll('#taskTable [data-bs-toggle="tooltip"]')
                    .forEach(el => new bootstrap.Tooltip(el));
            })
            .always(() => preloader.stop());
    }

    /* ================= FILTER EVENTS ================= */

    // dropdown filters
    $(document).on('change', '.taskFilter', function() {
        loadTasks(1); // reset to page 1
    });

    // debounced search
    $(document).on('keyup', '.taskSearch', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(() => {
            loadTasks(1);
        }, 400);
    });

    // reset filters
    $(document).on('click', '#resetFilters', function() {
        $('.taskFilter').val('');
        $('.taskSearch').val('');
        loadTasks(1);
    });

    /* ================= PAGINATION (CRITICAL FIX) ================= */
    $(document).on('click', '#taskTable .pagination a', function(e) {
        e.preventDefault();

        const page = new URL(this.href).searchParams.get('page');
        loadTasks(page);
    });

    /* ================= CREATE TASK (OFFCANVAS) ================= */
    $(document).on('click', '#openCreateTask', function(e) {
        e.preventDefault();

        preloader.load();

        $.get("{{ route('tasks.create') }}")
            .done(function(response) {
                const offcanvasEl = document.getElementById('offcanvasCustom');
                const oc = new bootstrap.Offcanvas(offcanvasEl);

                $('#offcanvasCustomHead').html('Create Task');
                $('#offcanvasCustomBody').html(response);

                oc.show();
                $('.selectpicker').selectpicker();
            })
            .fail(() => showAlert('Unable to load Create Task form', 'error'))
            .always(() => preloader.stop());
    });

    /* ================= SAVE TASK ================= */
    $(document).on('submit', '#taskCreateForm', function(e) {
        e.preventDefault();

        preloader.load();

        $.post("{{ route('tasks.store') }}", $(this).serialize())
            .done(function() {
                bootstrap.Offcanvas
                    .getInstance(document.getElementById('offcanvasCustom'))
                    .hide();

                loadTasks(); // reload list with filters intact
            })
            .fail(() => showAlert('Failed to save task', 'error'))
            .always(() => preloader.stop());
    });
</script>


@endsection