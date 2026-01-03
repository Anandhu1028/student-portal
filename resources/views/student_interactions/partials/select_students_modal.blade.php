<div class="modal fade" id="selectStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title">Select Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- BATCH SELECT --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Batch(es)</label>

                    <select id="batch_select"
    class="form-control form-control-sm selectpicker"
    multiple
    data-live-search="true"
    data-actions-box="true"
    title="Select batch(es)">
    @foreach($batches as $batch)
        <option value="{{ $batch->id }}">
            {{ $batch->batch_name }}
            ({{ $batch->course->specialization ?? 'NA' }}
            - {{ $batch->university->name ?? 'NA' }})
        </option>
    @endforeach
</select>

                </div>

                {{-- STUDENTS TABLE --}}
                <div class="table-responsive d-none" id="studentTableWrapper">
                    <table class="table table-bordered" id="student_table" style="font-size:10pt;">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select_all_students">
                                </th>
                                <th>Name</th>
                                <th>University</th>
                                <th>Email</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody id="student_table_body"></tbody>
                    </table>

                    <div id="selected_student_inputs"></div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="confirmSelectStudentsBtn" disabled>
                    Select Students
                </button>
            </div>

        </div>
    </div>
</div>

<script>
$(document).on('shown.bs.modal', '#selectStudentsModal', function () {
    $('#batch_select').selectpicker('refresh');
});

/* ================= LOAD STUDENTS ================= */
$(document).on('changed.bs.select', '#batch_select', function () {

    const batchIds = $(this).val();

    console.log('Selected batch IDs:', batchIds);

    if (!batchIds || batchIds.length === 0) {
        $('#studentTableWrapper').addClass('d-none');
        $('#student_table_body').empty();
        $('#confirmSelectStudentsBtn').prop('disabled', true);
        return;
    }

    preloader.load();

    $.ajax({
        url: "{{ route('student-interactions.load-students-by-batch') }}",
        type: 'GET',
        data: { batch_ids: batchIds },

        success: function (students) {

            console.log('Students loaded:', students);

            const tbody = $('#student_table_body');
            tbody.empty();

            if (!students.length) {
                tbody.append(`
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No students found for selected batch(es)
                        </td>
                    </tr>
                `);
            }

            students.forEach(s => {
                tbody.append(`
                    <tr>
                        <td>
                            <input type="checkbox"
                                   class="student_select"
                                   value="${s.id}">
                        </td>
                        <td>${s.name}</td>
                        <td>${s.university?.name ?? '—'}</td>
                        <td>${s.email ?? '—'}</td>
                        <td>${s.phone ?? '—'}</td>
                    </tr>
                `);
            });

            $('#studentTableWrapper').removeClass('d-none');

            shiftSelect(
                "#student_table",
                ".student_select",
                "#select_all_students",
                "#selected_student_inputs",
                "#confirmSelectStudentsBtn",
                "student_ids"
            );

            preloader.stop();
        },

        error: function (xhr) {
            preloader.stop();
            console.error(xhr.responseText);
            alert('Failed to load students');
        }
    });
});


/* ================= CONFIRM ================= */
$(document).on('click', '#confirmSelectStudentsBtn', function () {

    const ids = $('input[name="student_ids[]"]').map(function () {
        return this.value;
    }).get();

    if (!ids.length) {
        alert('Select at least one student');
        return;
    }

    $('#offcanvasSelectedStudents').remove();
    const container = $('<div id="offcanvasSelectedStudents"></div>');

    ids.forEach(id => {
        container.append(`<input type="hidden" name="student_ids[]" value="${id}">`);
    });

    $('#offcanvasCustomBody').append(container);
    $('#editSelectedStudentsBtn').removeClass('d-none');

    bootstrap.Modal.getInstance(
        document.getElementById('selectStudentsModal')
    ).hide();
});
</script>
