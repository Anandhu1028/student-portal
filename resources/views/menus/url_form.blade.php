<form id="urlForm" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="url_id" id="url_id" value="{{ $operationUrl->id ?? '' }}">
    <div class="col-md-6 col-12">
        <label for="urlModule" class="form-label">Module</label>
        <select class="form-control form-control-sm" id="urlModule" name="module_id">
            <option value="">Select Module</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}"
                    {{ old('module_id', $operationUrl->module_id ?? '') == $module->id ? 'selected' : '' }}>
                    {{ $module->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 col-12">
        <label for="urlMenu" class="form-label">Menu</label>
        <select class="form-control form-control-sm" id="urlMenu" name="menu_id"
            {{ $operationUrl ? '' : 'disabled' }}>
            <option value="">Select Menu</option>
            @foreach ($menus as $menu)
                <option value="{{ $menu->id }}"
                    {{ old('menu_id', $operationUrl->menu_id ?? '') == $menu->id ? 'selected' : '' }}>
                    {{ $menu->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 col-12">
        <label for="urlSubMenu" class="form-label">Sub-Menu</label>
        <select class="form-control form-control-sm" id="urlSubMenu" name="sub_menu_id"
            {{ $operationUrl && $operationUrl->sub_menu_id ? '' : 'disabled' }}>
            <option value="">Select Sub-Menu</option>
            @foreach ($subMenus as $subMenu)
                <option value="{{ $subMenu->id }}"
                    {{ old('sub_menu_id', $operationUrl->sub_menu_id ?? '') == $subMenu->id ? 'selected' : '' }}>
                    {{ $subMenu->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 col-12">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control form-control-sm" id="name" name="name"
            placeholder="Enter Name" value="{{ old('name', $operationUrl->name ?? '') }}" required>
    </div>
    <div class="col-md-6 col-12">
        <label for="url" class="form-label">URL</label>
        <input type="text" class="form-control form-control-sm" id="url" name="url" placeholder="Enter URL"
            value="{{ old('url', $operationUrl->url ?? '') }}" required>
    </div>
    <div class="col-md-6 col-12">
        <label for="urlRoles" class="form-label">Roles</label>
        <select class="selectpicker form-control form-control-sm" data-size="3" id="urlRoles" name="role_ids[]" multiple
            data-live-search="true" data-actions-box="true" title="Select one or more roles">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    {{ $operationUrl && $operationUrl->allowedUrls->whereNull('user_id')->where('role_id', $role->id)->where('url', $operationUrl->url)->count() ? 'selected' : '' }}>
                    {{ $role->role_name }}</option>
            @endforeach
        </select>
    </div>
</form>
<script>
    $(document).ready(function() {

        $('#offcanvasCustomFooter').html(
            `<button type="submit" id="save_menu_operation" class="btn btn-sm btn-primary">Save Operation</button>
            &nbsp;&nbsp;<button type="submit" id="add_new_menu_operation" class="btn btn-sm btn-primary d-none">Add New</button>`
        )
        $('#add_new_menu_operation').on('click', function(e) {
            e.preventDefault();
            $('#url_id').val('');
            $('#name').val('');
            $('#url').val('');
        })
        $('#save_menu_operation').on('click', function(e) {
            e.preventDefault();
            let formData = new FormData($('#urlForm')[0]);
            preloader.load();
            $.ajax({
                type: 'POST',
                url: "{{ route('urls.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    preloader.stop();
                    $('#url_id').val(response.url_id);
                    $('#add_new_menu_operation').removeClass('d-none')
                    popAlert(response.message, 'Success', 'success')
                },
                error: function(xhr) {
                    preloader.stop();
                    handleFormErrors(xhr.responseJSON)
                }
            });
        });

        $('#urlModule').on('change', function() {
            let moduleId = $(this).val();
            let $menuSelect = $('#urlMenu');
            let $subMenuSelect = $('#urlSubMenu');
            $menuSelect.prop('disabled', true).html('<option value="">Select Menu</option>');
            $subMenuSelect.prop('disabled', true).html('<option value="">Select Sub-Menu</option>');

            if (moduleId) {
                $.ajax({
                    url: '{{ route('menus.byModule') }}',
                    method: 'GET',
                    data: {
                        module_id: moduleId
                    },
                    success: function(response) {
                        response.menus.forEach(menu => {
                            $menuSelect.append(
                                `<option value="${menu.id}">${menu.name}</option>`
                            );
                        });
                        $menuSelect.prop('disabled', false);
                    }
                });
            }
        });

        $('#urlMenu').on('change', function() {
            let menuId = $(this).val();
            let $subMenuSelect = $('#urlSubMenu');
            $subMenuSelect.prop('disabled', true).html('<option value="">Select Sub-Menu</option>');

            if (menuId) {
                $.ajax({
                    url: '{{ route('subMenus.byMenu') }}',
                    method: 'GET',
                    data: {
                        menu_id: menuId
                    },
                    success: function(response) {
                        response.subMenus.forEach(subMenu => {
                            $subMenuSelect.append(
                                `<option value="${subMenu.id}">${subMenu.name}</option>`
                            );
                        });
                        $subMenuSelect.prop('disabled', false);
                    }
                });
            }
        });
    });
</script>
