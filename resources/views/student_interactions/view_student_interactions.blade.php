@extends('layouts.layout_ajax')

@section('content')

<link rel="stylesheet" href="{{ asset('/libs/datatable/datatables.min.css') }}">
<script src="{{ asset('/libs/datatable/datatables.min.js') }}"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Student Interactions</h3>

                    <button class="btn btn-sm btn-primary" id="addInteractionBtn">
                        + Add Interaction
                    </button>
                </div>

                {{-- BULK ACTION BAR --}}
                <div class="card-body border-bottom d-none" id="bulkActionBar">
                    <button class="btn btn-sm btn-danger" id="closeInteractionsBtn">
                        Close Selected Interactions
                    </button>
                </div>

                {{-- TABLE --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="interaction_table"
                               class="table table-bordered table-striped datatable"
                               style="font-size:10pt;">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select_all_interactions">
                                    </th>
                                    <th>Student</th>
                                    <th>Interaction Type</th>
                                    <th>Remarks</th>
                                    <th>Follow-up Date</th>
                                    <th>Due Date</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($interactions as $interaction)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   class="interaction_select"
                                                   value="{{ $interaction->id }}">
                                        </td>
                                        <td>{{ $interaction->student->first_name ?? '—' }}</td>
                                        <td>{{ $interaction->type->name ?? '—' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($interaction->remarks, 40) }}</td>
                                        <td>{{ $interaction->follow_up_date ?? '—' }}</td>
                                        <td>{{ $interaction->due_date ?? '—' }}</td>
                                        <td>{{ $interaction->performer->name ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-success">Open</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div id="selected_interaction_inputs"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@section('footer')
<script src="{{ asset('js/shift_select.js') }}"></script>

<script>
$(document).ready(function () {

    $('.datatable').DataTable({
        order: [[4, 'desc']]
    });

    shiftSelect(
        "#interaction_table",
        ".interaction_select",
        "#select_all_interactions",
        "#selected_interaction_inputs",
        "#bulkActionBar",
        "interaction_ids"
    );
});

/* ================= ADD INTERACTION ================= */
$(document).on('click', '#addInteractionBtn', function () {

    preloader.load();

    $.get("{{ route('student-interactions.create') }}", function (html) {

        preloader.stop();

        const offcanvas = new bootstrap.Offcanvas('#offcanvasCustom');
        offcanvas.show();

        $('#offcanvasCustomHead').html('Add Student Interaction');
        $('#offcanvasCustomBody').html(html);
        $('#offcanvasCustomFooter').html(`
            <button class="btn btn-sm btn-primary" id="selectStudentsBtn">
                Select Students
            </button>
            <button class="btn btn-sm btn-outline-secondary ms-2 d-none"
                    id="editSelectedStudentsBtn">
                Edit Selected Students
            </button>
        `);
    });
});

/* ================= OPEN STUDENT MODAL ================= */
$(document).on('click', '#selectStudentsBtn', function () {

    preloader.load();
    $('#selectStudentsModal').remove();

    $.get("{{ route('student-interactions.select-students') }}", function (html) {

        preloader.stop();
        $('body').append(html);

        new bootstrap.Modal('#selectStudentsModal').show();
    });
});
</script>
@endsection
