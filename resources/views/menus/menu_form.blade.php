<form id="menuForm" method="POST" class="row g-3">
    @csrf
    <input type="hidden" name="menu_id" id="menu_id" value="{{ $menu->id ?? '' }}">

    <div class="col-md-6 col-12">
        <label for="menuName" class="form-label">Name</label>
        <input type="text" class="form-control form-control-sm" id="menuName" name="name"
            placeholder="Enter name" value="{{ old('name', $menu->name ?? '') }}" required>
    </div>

    <div class="col-md-6 col-12">
        <label for="menuDisplayName" class="form-label">Display Name</label>
        <input type="text" class="form-control form-control-sm" id="menuDisplayName" name="display_name"
            placeholder="Enter display name in permission" value="{{ old('display_name', $menu->display_name ?? '') }}"
            required>
    </div>

    <div class="col-md-6 col-12">
        <label for="menuModule" class="form-label">Module</label>
        <select class="form-select form-select-sm" id="menuModule" name="module_id">
            <option value="">Select Module</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}"
                    {{ old('module_id', $menu->module_id ?? '') == $module->id ? 'selected' : '' }}>{{ $module->name }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="col-md-6 col-12">
        <label for="assignMenuModule" class="form-label">Assign Menu To Module</label>
        <select class="form-control form-control-sm selectpicker" id="assignMenuModule" name="assign_menu_to[]"
            multiple>
            <option value="">Select Module</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}">{{ $module->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 col-12">
        <label for="menuTitle" class="form-label">Menu Title</label>
        <select class="form-select form-select-sm" id="menuTitle" name="menu_title_id">
            <option value="">Select Menu Title</option>
            @foreach ($menuTitles as $menuTitle)
                <option value="{{ $menuTitle->id }}"
                    {{ old('menu_title_id', $menu->menu_title_id ?? '') == $menuTitle->id ? 'selected' : '' }}>
                    {{ $menuTitle->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 col-12">
        <label for="menuUrl" class="form-label">URL</label>
        <input type="text" class="form-control form-control-sm" id="menuUrl" name="url"
            placeholder="Route name" value="{{ old('url', $menu->url ?? '') }}">
        <small class="error-message" id="menuUrlError" style="display: none;">Invalid or duplicate URL</small>
    </div>

    <div class="col-md-6 col-12">
        <label for="menuIcon" class="form-label">Icon</label>
        <input type="text" class="form-control form-control-sm" id="menuIcon" name="icon"
            placeholder="e.g., fa fa-home" value="{{ old('icon', $menu->icon ?? '') }}">
    </div>
    {{-- @php
        echo $menu->id;
        echo '<pre>';
        print_r($menu->allowedUrls->where('menu_id', $menu->id)->whereNull('user_id')->toArray());
        echo '</pre>';
    @endphp --}}
    <div class="col-md-6 col-12">
        <label for="menuRoles" class="form-label">Roles</label>
        <select class="selectpicker form-control form-control-sm" data-size="3" id="menuRoles" name="role_ids[]" multiple
            data-live-search="true"  data-actions-box="true">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    {{ $menu && $menu->allowedUrls->where('role_id', $role->id)->whereNull('user_id')->count() ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#offcanvasCustomFooter').html(
            `<button type="submit" id="save_menu" class="btn btn-sm btn-primary">Save Menu</button>`)
        $('#save_menu').on('click', function(e) {
            e.preventDefault();

            let formData = new FormData($('#menuForm')[0]);
            preloader.load();

            $.ajax({
                type: 'POST',
                url: "{{ route('menus.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    preloader.stop();
                    $('#menu_id').val(response.menu_id);
                    popAlert(response.message, 'Success', 'success')
                },
                error: function(xhr) {
                    preloader.stop();
                    handleFormErrors(xhr.responseJSON)
                }
            });
        });
    });
</script>
