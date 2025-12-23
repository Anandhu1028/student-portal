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
.task-search { width: 10%; min-width: 120px; }
.task-filters .filter-sm { width: 120px; }
</style>

<script>
/* ================= LOAD TASKS ================= */
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

$('.taskFilter').on('change', loadTasks);
$('.taskSearch').on('keyup', loadTasks);

$('#resetFilters').on('click', function () {
    $('.taskFilter').val('');
    $('.taskSearch').val('');
    loadTasks();
});

/* ================= CREATE TASK (GLOBAL OFFCANVAS) ================= */
$('#openCreateTask').on('click', function (e) {
    e.preventDefault();

    preloader.load();

    $.ajax({
        url: "{{ route('tasks.create') }}",
        type: "GET",
        success: function (response) {

            preloader.stop();

            const offcanvasEl = document.getElementById('offcanvasCustom');
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);

            $('#offcanvasCustomHead').html('Create Task');
            $('#offcanvasCustomBody').html(response);

            bsOffcanvas.show();
            $('.selectpicker').selectpicker();
        },
        error: function () {
            preloader.stop();
            showAlert('Unable to load Create Task form', 'error');
        }
    });
});

/* ================= SAVE TASK ================= */
$(document).on('submit', '#taskCreateForm', function (e) {
    e.preventDefault();

    preloader.load();

    $.post("{{ route('tasks.store') }}", $(this).serialize())
        .done(function () {
            preloader.stop();

            bootstrap.Offcanvas
                .getInstance(document.getElementById('offcanvasCustom'))
                .hide();

            loadTasks();
        })
        .fail(function () {
            preloader.stop();
            showAlert('Failed to save task', 'error');
        });
});
</script>

@endsection
