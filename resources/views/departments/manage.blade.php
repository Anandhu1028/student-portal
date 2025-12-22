<form id="departmentForm">
@csrf
<input type="hidden" name="id" value="{{ $department->id ?? '' }}">

<div class="row g-3">

    {{-- ROW 1 --}}
    {{-- Department Name --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Department Name</label>
        <input type="text"
               name="name"
               class="form-control"
               required
               value="{{ $department->name ?? '' }}">
    </div>

    {{-- Description --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description"
                  class="form-control auto-grow"
                  rows="1">{{ $department->description ?? '' }}</textarea>
    </div>

    {{-- ROW 2 --}}
    {{-- Status --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="1" {{ isset($department) && $department->status ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ isset($department) && !$department->status ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    {{-- Department Color --}}
    <div class="col-md-4" style="width:174px;">
        <label class="form-label fw-semibold">Department Color</label>
        <input type="color"
               name="color"
               class="form-control form-control-color"
               style="width:70px"
               value="{{ $department->color ?? '#0d6efd' }}">
    </div>

   {{-- Assigned Users --}}
    <div class="col-md-4">
   <label class="form-label section-label">Assigned Users</label>

    <select name="users[]"
            class="selectpicker"
            multiple
            data-live-search="true"
            data-actions-box="true"
            data-selected-text-format="count > 2"
            title="Select users">
        @foreach($users as $user)
            <option value="{{ $user->id }}"
                {{ isset($department) && $department->users->contains($user->id) ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
</div>



</div>
</form>


<style>
    .section-label {
    font-weight: 600;
    margin-bottom: 4px;
    padding-bottom: 6px;

    display: block;
}

/* EXACT Bootstrap input match */
.bootstrap-select > .dropdown-toggle {
    height: 38px !important;
    min-height: 38px;
    padding: 6px 12px;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    background-color: #fff;
    display: flex;
    align-items: center;
}

/* Vertically center selected text */
.bootstrap-select .filter-option {
    display: flex;
    align-items: center;
}

/* Remove extra padding inside */
.bootstrap-select .filter-option-inner-inner {
    padding: 0;
    line-height: normal;
}

/* Match focus behavior */
.bootstrap-select .dropdown-toggle:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
}



</style>


</style>

<script>
$('#offcanvasCustomFooter').html(`
    <button class="btn btn-sm btn-primary" id="saveDepartment">
        <i class="ri-save-line"></i> Save
    </button>
    <button class="btn btn-sm btn-light ms-2" data-bs-dismiss="offcanvas">
        Cancel
    </button>
`);

// INIT SELECTPICKER SAFELY (NO DUPLICATES)
setTimeout(() => {
    const $picker = $('.selectpicker');

    if ($picker.data('selectpicker')) {
        $picker.selectpicker('destroy');
    }

    $picker.selectpicker({
        size: 6
    });
}, 200);

// SAVE
$(document).off('click', '#saveDepartment').on('click', '#saveDepartment', function () {

    preloader.load();

    $.post("{{ route('departments.save') }}", $('#departmentForm').serialize())
        .done(function (res) {
            preloader.stop();

            bootstrap.Offcanvas.getInstance(
                document.getElementById('offcanvasCustom')
            )?.hide();

            $('#modal_title_custom').html(
                `<i class="ri-checkbox-circle-line text-success me-1"></i> Success`
            );

            $('#modal_body_custom').html(
                `<div class="alert alert-success mb-0">${res.message}</div>`
            );

            $('#modal_footer_custom').html(
                `<button class="btn btn-sm btn-primary" id="deptOk">OK</button>`
            );

            $('#modal_custom').modal('show');
        })
        .fail(function (xhr) {
            preloader.stop();
            alert(xhr.responseJSON?.message ?? 'Something went wrong');
        });
});

// Redirect
$(document).off('click', '#deptOk').on('click', '#deptOk', function () {
    $('#modal_custom').modal('hide');
    loadMenuPage("{{ route('departments.index') }}", "Manage Departments");
});

// Auto-grow textarea
$('.auto-grow').on('input', function () {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
}).trigger('input');
</script>
