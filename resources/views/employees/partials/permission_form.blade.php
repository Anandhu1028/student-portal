@php
    use App\Models\AllowedUrls;
    use App\Models\Module;
    use App\Models\OtherMenuOperationUrls;

    $allowedUrls = AllowedUrls::query()
        ->where(function ($q) use ($user) {
            $q->where(function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('allowed', true);
            })->orWhere(function ($q2) use ($user) {
                $q2->where('role_id', $user->role_id)->where('allowed', true);
            });
        })
        ->whereNotIn('url', function ($sub) use ($user) {
            $sub->select('url')->from('allowed_urls')->where('user_id', $user->id)->where('allowed', false);
        })
        ->pluck('url')
        ->toArray();

    $modules = Module::with([
        'menus' => function ($q) {
            $q->where('is_active', true)->with([
                'subMenus' => function ($q2) {
                    $q2->where('is_active', true);
                },
                'menuTitle',
            ]);
        },
    ])->get();

    $otherMenuUrls = OtherMenuOperationUrls::with(['module', 'menus', 'sub_menus'])
        ->get()
        ->groupBy(function ($item) {
            return $item->sub_menu_id ?: 'menu-' . $item->menu_id;
        });


@endphp

<div class="row">
    <div class="col-12 mb-3">
        <style>
            .menu-row {
                background-color: #f8f9fa;
                font-weight: bold;
            }

            .submenu-row {
                background-color: #ffffff;
            }

            .extra-row {
                background-color: #f1f3f5;
                font-style: italic;
            }

            .permission-options {
                display: flex;
                gap: 15px;
            }

            .d-none {
                display: none;
            }

            .toggle-btn {
                cursor: pointer;
                border: none;
                background: none;
                font-size: 14px;
                padding: 0;
                margin-right: 5px;
            }
        </style>

        <form id="form_permission">
            @csrf
            <input type="hidden" name="user_id" class="user_id" value="{{ $userId }}">

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Menu Title</th>
                        <th>Menu / Submenu / Extra URL</th>
                        <th>Permission</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $module)
                        @foreach ($module->menus as $menu)
                            @php
                                $menuIdAttr = 'menu-' . $menu->id;

                                $menuUrlAllowed = isset($menu->url) && in_array($menu->url, $allowedUrls);

                                $hasChildren =
                                    $menu->subMenus->count() > 0 || isset($otherMenuUrls['menu-' . $menu->id]);
                            @endphp

                            {{-- Parent Menu --}}
                            <tr class="menu-row">
                                <td>{{ $module->name }} </td>
                                <td>{{ $menu->menuTitle->name ?? '-' }} </td>
                                <td>
                                    @if ($hasChildren)
                                        <button type="button" class="toggle-btn"
                                            data-target="group-{{ $menu->id }}">▶</button>
                                    @endif
                                    {{ $menu->name }}
                                </td>
                                <td class="permission-options">
                                    <label>
                                        <input type="radio" name="permission[menu-{{ $menu->id }}]" value="1"
                                            class="menu-radio-yes" data-menu="{{ $menuIdAttr }}"
                                            {{ $menuUrlAllowed ? 'checked' : '' }}> Yes
                                    </label>

                                    <label>
                                        <input type="radio" name="permission[menu-{{ $menu->id }}]" value="0"
                                            class="menu-radio-no" data-menu="{{ $menuIdAttr }}"
                                            {{ !$menuUrlAllowed ? 'checked' : '' }}> No
                                    </label>
                                </td>
                            </tr>

                            {{-- Child group (submenus + extras) --}}
                            @if ($hasChildren)
                <tbody id="group-{{ $menu->id }}" class="submenu-group d-none">
                    {{-- Submenus --}}
                    @foreach ($menu->subMenus as $subMenu)
                        @php
                            $subUrlAllowed = in_array($subMenu->url, $allowedUrls);
                        @endphp
                        <tr class="submenu-row">
                            <td></td>
                            <td></td>
                            <td>{{ $menu->name }} → {{ $subMenu->name }}</td>
                            <td class="permission-options">
                                <label>
                                    <input type="radio" name="permission[sub-{{ $subMenu->id }}]" value="1"
                                        class="submenu-radio" data-menu="{{ $menuIdAttr }}"
                                        {{ $subUrlAllowed ? 'checked' : '' }}> Yes
                                </label>
                                <label>
                                    <input type="radio" name="permission[sub-{{ $subMenu->id }}]" value="0"
                                        class="submenu-radio" data-menu="{{ $menuIdAttr }}"
                                        {{ !$subUrlAllowed ? 'checked' : '' }}> No
                                </label>
                            </td>
                        </tr>

                        {{-- Extras under submenu --}}
                        @if (isset($otherMenuUrls[$subMenu->id]))
                            @foreach ($otherMenuUrls[$subMenu->id] as $opUrl)
                                @php
                                    $extraChecked = in_array($opUrl->url, $allowedUrls);
                                @endphp
                                <tr class="extra-row">
                                    <td></td>
                                    <td></td>
                                    <td class="ps-5 text-muted">Extra: {{ $opUrl->name }}</td>
                                    <td class="permission-options">
                                        <label>
                                            <input type="radio" name="permission[extra-{{ $opUrl->id }}]"
                                                value="1" class="extra-radio" data-menu="{{ $menuIdAttr }}"
                                                {{ $extraChecked ? 'checked' : '' }}> Yes
                                        </label>
                                        <label>
                                            <input type="radio" name="permission[extra-{{ $opUrl->id }}]"
                                                value="0" class="extra-radio" data-menu="{{ $menuIdAttr }}"
                                                {{ !$extraChecked ? 'checked' : '' }}> No
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Extras under menu (no submenu case) --}}
                    @if (isset($otherMenuUrls['menu-' . $menu->id]))
                        @foreach ($otherMenuUrls['menu-' . $menu->id] as $opUrl)
                            @php
                                $extraChecked = in_array($opUrl->url, $allowedUrls);
                            @endphp
                            <tr class="extra-row">
                                <td></td>
                                <td></td>
                                <td class="ps-5 text-muted">Extra: {{ $opUrl->name }}</td>
                                <td class="permission-options">
                                    <label>
                                        <input type="radio" name="permission[extra-{{ $opUrl->id }}]"
                                            value="1" class="extra-radio" data-menu="{{ $menuIdAttr }}"
                                            {{ $extraChecked ? 'checked' : '' }}> Yes
                                    </label>
                                    <label>
                                        <input type="radio" name="permission[extra-{{ $opUrl->id }}]"
                                            value="0" class="extra-radio" data-menu="{{ $menuIdAttr }}"
                                            {{ !$extraChecked ? 'checked' : '' }}> No
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @endif
                @endforeach
                @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
    // Expand/Collapse toggle
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let target = document.getElementById(this.dataset.target);
            if (target.classList.contains('d-none')) {
                target.classList.remove('d-none');
                this.textContent = '▼';
            } else {
                target.classList.add('d-none');
                this.textContent = '▶';
            }
        });
    });

    // Menu Yes/No → set all children
    document.querySelectorAll('.menu-radio-yes, .menu-radio-no').forEach(menuRadio => {
        menuRadio.addEventListener('change', function() {
            if (!this.checked) return;
            let menuGroup = this.getAttribute('data-menu');
            let value = this.value;
            document.querySelectorAll(
                `.submenu-radio[data-menu="${menuGroup}"], .extra-radio[data-menu="${menuGroup}"]`
            ).forEach(radio => {
                if (radio.value === value) radio.checked = true;
            });
        });
    });

    // Child Yes/No → adjust parent
    document.querySelectorAll('.submenu-radio, .extra-radio').forEach(childRadio => {
        childRadio.addEventListener('change', function() {
            if (!this.checked) return;
            let menuGroup = this.getAttribute('data-menu');
            let parentYes = document.querySelector(`.menu-radio-yes[data-menu="${menuGroup}"]`);
            let parentNo = document.querySelector(`.menu-radio-no[data-menu="${menuGroup}"]`);
            let allYes = Array.from(document.querySelectorAll(
                `.submenu-radio[data-menu="${menuGroup}"][value="1"], .extra-radio[data-menu="${menuGroup}"][value="1"]`
            )).every(r => r.checked);
            let allNo = Array.from(document.querySelectorAll(
                `.submenu-radio[data-menu="${menuGroup}"][value="0"], .extra-radio[data-menu="${menuGroup}"][value="0"]`
            )).every(r => r.checked);
            if (allYes) {
                parentYes.checked = true;
            } else if (allNo) {
                parentNo.checked = true;
            } else {
                parentYes.checked = false;
                parentNo.checked = false;
            }
        });
    });

    // On load → set parent
    document.querySelectorAll('.menu-radio-yes').forEach(parentYes => {
        let menuGroup = parentYes.getAttribute('data-menu');
        let parentNo = document.querySelector(`.menu-radio-no[data-menu="${menuGroup}"]`);

        let childRadios = document.querySelectorAll(
            `.submenu-radio[data-menu="${menuGroup}"], .extra-radio[data-menu="${menuGroup}"]`
        );

        if (childRadios.length > 0) { // ✅ only run if children exist
            let allYes = Array.from(childRadios)
                .filter(r => r.value === "1")
                .every(r => r.checked);

            let allNo = Array.from(childRadios)
                .filter(r => r.value === "0")
                .every(r => r.checked);

            if (allYes) {
                parentYes.checked = true;
            } else if (allNo) {
                parentNo.checked = true;
            }
        }
    });

    function saveAuthForm() {
        let form = $('#form_permission')[0];
        let formData = new FormData(form);
        preloader.load();
        $.ajax({
            type: "post",
            url: "{{ route('employee.manage_user_permissions') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                preloader.stop();
                popAlert(response.message, 'Success', 'success');
            },
            error: function(xhr) {
                preloader.stop();
                handleFormErrors(xhr.responseJSON);
            }
        });
    }
</script>
