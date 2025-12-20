<form id="subMenuForm" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="sub_menu_id" id="sub_menu_id" value="{{ $subMenu->id ?? '' }}">

    <div class="col-md-6 col-12">
        <label for="subMenuModule" class="form-label">Module</label>
        <select class="form-control form-control-sm" id="subMenuModule" name="module_id">
            <option value="">Select Module</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}"
                    {{ old('module_id', $subMenu->menu->module_id ?? '') == $module->id ? 'selected' : '' }}>
                    {{ $module->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuMenu" class="form-label">Menu</label>
        <select class="form-control form-control-sm" id="subMenuMenu" name="menu_id" {{ $subMenu ? '' : 'disabled' }}>
            <option value="">Select Menu</option>
            @foreach ($menus as $menu)
                <option value="{{ $menu->id }}"
                    {{ old('menu_id', $subMenu->menu_id ?? '') == $menu->id ? 'selected' : '' }}>
                    {{ $menu->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuName" class="form-label">Name</label>
        <input type="text" class="form-control form-control-sm" id="subMenuName" name="name"
            placeholder="Enter sub-menu name" value="{{ old('name', $subMenu->name ?? '') }}" required>
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuDisplayName" class="form-label">Display Name</label>
        <input type="text" class="form-control form-control-sm" id="subMenuDisplayName" name="display_name"
            placeholder="Enter display name" value="{{ old('display_name', $subMenu->display_name ?? '') }}" required>
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuUrl" class="form-label">URL</label>
        <input type="text" class="form-control form-control-sm" id="subMenuUrl" name="url"
            placeholder="Enter URL" value="{{ old('url', $subMenu->url ?? '') }}">
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuIcon" class="form-label">Icon</label>
        <input type="text" class="form-control form-control-sm" id="subMenuIcon" name="icon"
            placeholder="e.g. fa fa-user" value="{{ old('icon', $subMenu->icon ?? '') }}">
    </div>

    <div class="col-md-6 col-12">
        <label for="subMenuOrder" class="form-label">Order</label>
        <input type="number" class="form-control form-control-sm" id="subMenuOrder" name="order" min="0"
            placeholder="Enter order number" value="{{ old('order', $subMenu->order ?? 0) }}">
    </div>
    <div class="col-md-6 col-12">
        <label for="subMenuRoles" class="form-label">Roles</label>
        <select class="selectpicker form-control form-control-sm" data-size="3" id="subMenuRoles" name="role_ids[]" multiple
            data-live-search="true"  data-actions-box="true" title="Select one or more roles">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    {{ $subMenu && $subMenu->allowedUrls->where('url', $subMenu->url)->whereNull('user_id')->where('role_id', $role->id)->count() ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>
            @endforeach
        </select>
    </div>
</form>


<script>
    $(document).ready(function() {
        $('#offcanvasCustomFooter').html(
            `<button type="submit" id="save_sub_menu" class="btn btn-sm btn-primary">Save Menu</button>

            &nbsp;&nbsp;<button type="submit" id="add_new_sub_menu" class="btn btn-sm btn-primary d-none">Add New</button>`
        )


        $('#add_new_sub_menu').on('click', function(e) {
            e.preventDefault();
            $('#sub_menu_id').val('');
            $('#name').val('');
            $('#subMenuUrl').val('');
            $('#subMenuIcon').val('');
            $('#subMenuName').val('');
            $('#subMenuDisplayName').val('');
        })

        $('#save_sub_menu').on('click', function(e) {
            e.preventDefault();

            let formData = new FormData($('#subMenuForm')[0]);
            preloader.load();

            $.ajax({
                type: 'POST',
                url: "{{ route('subMenus.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    preloader.stop();
                    $('#sub_menu_id').val(response.sub_menu_id);
                    $('#add_new_sub_menu').removeClass('d-none');
                    popAlert(response.message, 'Success', 'success')
                },
                error: function(xhr) {
                    preloader.stop();
                    handleFormErrors(xhr.responseJSON)
                }
            });
        });

        $('#subMenuModule').on('change', function() {
            let moduleId = $(this).val();
            let $menuSelect = $('#subMenuMenu');
            $menuSelect.prop('disabled', true).html('<option value="">Select Menu</option>');

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
    });
</script>
