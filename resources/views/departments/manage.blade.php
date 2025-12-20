<form id="departmentForm">
    @csrf
    <input type="hidden" name="id" value="{{ $department->id ?? '' }}">

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name"
            class="form-control"
            value="{{ $department->name ?? '' }}">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description"
            class="form-control">{{ $department->description ?? '' }}</textarea>
    </div>

    <button class="btn btn-primary w-100">Save</button>
</form>

<script>
$('#departmentForm').submit(function(e){
    e.preventDefault();
    preloader.load();

    $.post("{{ route('departments.save') }}", $(this).serialize(), function(res){
        preloader.stop();
        showAlert(res.message);
        $('.offcanvas').offcanvas('hide');
        loadMenuPage("{{ route('departments.index') }}", "Manage Departments");
    }).fail(function(xhr){
        preloader.stop();
        showAlert('Validation failed', 'error');
    });
});
</script>
