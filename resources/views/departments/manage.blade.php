<form id="departmentForm">
@csrf
<input type="hidden" name="id" value="{{ $department->id ?? '' }}">

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Department Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ $department->name ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control auto-grow"
                  rows="1">{{ $department->description ?? '' }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="1" {{ isset($department) && $department->status ? 'selected' : '' }}>Active</option>
            <option value="0" {{ isset($department) && !$department->status ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Department Color</label>
        <input type="color" name="color"
               class="form-control form-control-color"
               value="{{ $department->color ?? '#0d6efd' }}"
               style="width:7%;">
    </div>
</div>
</form>

<script>
$('#offcanvasCustomFooter').html(`
    <button class="btn btn-sm btn-primary" id="saveDepartment">
        <i class="ri-save-line"></i> Save
    </button>
    <button class="btn btn-sm btn-light ms-2" data-bs-dismiss="offcanvas">
        Cancel
    </button>
`);

$(document).off('click', '#saveDepartment')
           .on('click', '#saveDepartment', function () {
    saveDepartment();
});

function saveDepartment() {
    preloader.load();

    $.post("{{ route('departments.save') }}", $('#departmentForm').serialize())
    .done(function (res) {

        preloader.stop();

        // Close offcanvas
        bootstrap.Offcanvas.getInstance(
            document.getElementById('offcanvasCustom')
        )?.hide();

        // Success modal
        $('#modal_title_custom').html(`
            <i class="ri-checkbox-circle-line text-success me-1"></i> ${res.title}
        `);

        $('#modal_body_custom').html(`
            <div class="alert alert-success mb-0">
                ${res.message}
            </div>
        `);

        $('#modal_footer_custom').html(`
            <button class="btn btn-sm btn-primary" id="deptOk">
                OK
            </button>
        `);

        $('#modal_custom').modal('show');
    })
    .fail(function (xhr) {
        preloader.stop();

        $('#modal_title_custom_error').html(`
            <i class="ri-error-warning-line text-danger me-1"></i> Error
        `);

        $('#modal_body_custom_error').html(
            xhr.responseJSON?.message || 'Something went wrong'
        );

        $('#modal_custom_error').modal('show');
    });
}

// Redirect ONLY after OK
$(document).off('click', '#deptOk')
           .on('click', '#deptOk', function () {
    $('#modal_custom').modal('hide');
    loadMenuPage("{{ route('departments.index') }}", "Manage Departments");
});

// Auto grow textarea
$('.auto-grow').on('input', function () {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
}).trigger('input');
</script>
