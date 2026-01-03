<!--------------------------------------------------------------------
       THIS THE TASK LIST SECTION ( HERE LODE TABLE & ACTIVIY FILES).
 ----------------- ------------------------------------------------>
@extends('layouts.layout_ajax')
@section('content')

<div class="card task-list-card">

    {{-- HEADER --}}
    <div class="card-header">
        <h5 class="mb-0">Task List</h5>
    </div>

    <div class="container-fluid card-body pb-5">

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

            <input type="date"
                class="form-control form-control-sm taskFilter filter-sm"
                data-name="date">

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

{{-- FIXED FOOTER ACTION BAR --}}
<div class="task-footer-bar">
    <button class="btn btn-primary btn-sm" id="openCreateTask">
        + Create Task
    </button>
</div>



<div class="modal fade" id="globalSuccessModal" tabindex="-1"
    data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-top">
        <div class="modal-content success-modal">

            <div class="modal-header">
                <h5 class="modal-title">Success</h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                <div class="success-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="11"></circle>
                        <path d="M7 12.5l3 3 7-7"></path>
                    </svg>
                </div>

                <p id="successMessage" class="success-text">
                    Task updated successfully
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-sm bg-danger-subtle text-danger"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>






<style>
    /*modal*/

    /* TOP POSITIONED MODAL (EXACT BEHAVIOR) */
    .modal-top {
        margin: 80px auto 0;
        /* top spacing */
        max-width: 560px;
        /* exact visual width */
    }

    /* Modal polish */
    .success-modal {
        border-radius: 8px;
    }

    /* Header spacing */
    #globalSuccessModal .modal-header {
        padding: 16px 20px;
    }

    /* Body spacing */
    #globalSuccessModal .modal-body {
        padding: 30px 20px;
    }

    /* Footer alignment */
    #globalSuccessModal .modal-footer {
        padding: 12px 20px;
        justify-content: flex-end;
    }

    /* Icon styling */
    .success-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 16px;
    }

    .success-icon svg {
        width: 100%;
        height: 100%;
    }

    .success-icon circle,
    .success-icon path {
        fill: none;
        stroke: #22c55e;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Text */
    .success-text {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0;
    }



    /* FILTERS */
    .task-search {
        width: 10%;
        min-width: 120px;
    }

    .task-filters .filter-sm {
        width: 120px;
    }

    .task-footer-bar {
        position: fixed;
        bottom: 0;
        left: 260px;
        /* sidebar width */
        right: 0;
        height: 52px;



        display: flex;
        align-items: center;
        justify-content: flex-start;
        /* 🔥 LEFT SIDE */

        padding: 0 20px;
        z-index: 1000;
    }

    /* Mobile */
    @media (max-width: 992px) {
        .task-footer-bar {
            left: 0;
        }
    }
</style>
<script>
    let filterTimer = null;

    /* =========================================================
       LOAD TASKS
    ========================================================= */
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

        $.get("{{ route('tasks.list') }}", params)
            .done(html => {
                $('#taskTable').html(html);

                // Re-init tooltips
                document
                    .querySelectorAll('#taskTable [data-bs-toggle="tooltip"]')
                    .forEach(el => bootstrap.Tooltip.getOrCreateInstance(el));
            })
            .fail(() => {
                showAlert('Failed to reload task list', 'error');
            })
            .always(() => {
                preloader.stop();
            });
    }

    /* =========================================================
       FILTER EVENTS
    ========================================================= */
    $(document).on('change', '.taskFilter', () => loadTasks(1));

    $(document).on('keyup', '.taskSearch', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(() => loadTasks(1), 400);
    });

    $(document).on('click', '#resetFilters', function() {
        $('.taskFilter').val('');
        $('.taskSearch').val('');
        loadTasks(1);
    });

    /* =========================================================
       PAGINATION
    ========================================================= */
    $(document).on('click', '#taskTable .pagination a', function(e) {
        e.preventDefault();
        loadTasks(new URL(this.href).searchParams.get('page'));
    });

    /* =========================================================
       CREATE TASK (OPEN OFFCANVAS)
    ========================================================= */
    $(document).on('click', '#openCreateTask', function(e) {
        e.preventDefault();

        preloader.load();

        $.get("{{ route('tasks.create') }}")
            .done(res => {
                const oc = bootstrap.Offcanvas.getOrCreateInstance(
                    document.getElementById('offcanvasCustom')
                );

                $('#offcanvasCustomHead').text('Create Task');
                $('#offcanvasCustomBody').html(res);
                oc.show();

                setTimeout(() => {
                    $('.selectpicker').selectpicker('refresh');
                }, 100);
            })
            .fail(() => {
                showAlert('Unable to load Create Task form', 'error');
            })
            .always(() => {
                preloader.stop();
            });
    });

    /* =========================================================
       SAVE TASK (CREATE / UPDATE)
    ========================================================= */
    $(document).on('submit', '#taskCreateForm', function(e) {
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
            .done(res => {

                // Close offcanvas first
                bootstrap.Offcanvas
                    .getInstance(document.getElementById('offcanvasCustom'))
                    ?.hide();

                // Set success message
                $('#successMessage').text(
                    res?.message || 'Saved successfully'
                );

                // Show success modal
                bootstrap.Modal
                    .getOrCreateInstance(
                        document.getElementById('globalSuccessModal')
                    )
                    .show();
            })
            .fail(xhr => {
                showAlert(
                    xhr.responseJSON?.message || 'Save failed',
                    'error'
                );
            })
            .always(() => {
                preloader.stop();
                $(form).data('loading', false);
            });
    });

    /* =========================================================
       SUCCESS MODAL CLOSE → REFRESH LIST
    ========================================================= */
    $(document).on('click', '[data-bs-dismiss="modal"]', function() {

        const modalEl = document.getElementById('globalSuccessModal');
        const modal = bootstrap.Modal.getInstance(modalEl);

        if (modal) modal.hide();

        // Reload task list after success confirmation
        loadTasks(1);
    });
</script>


@endsection