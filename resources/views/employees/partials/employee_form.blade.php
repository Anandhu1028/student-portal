<style>
    .datepicker {
        z-index: 99999 !important;
    }

    .ui-datepicker {
        z-index: 9999 !important;
    }

    .ui-datepicker.ui-widget {
        position: absolute !important;
    }
</style>
@if (isset($user))
    <input type="hidden" name="user_id" class="user_id" value="{{ $user->id }}">
@endif



<div class="row">
    {{-- Name --}}
    <div class="col-md-6 mb-3">
        <label>Name *</label>
        <input type="text" name="name" id="id_name" class="form-control form-control-sm"
            placeholder="Enter full name" value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    {{-- Email --}}
    <div class="col-md-6 mb-3">
        <label>Email *</label>
        <input type="email" name="email" id="id_email" class="form-control form-control-sm"
            placeholder="Enter email" value="{{ old('email', $user->email ?? '') }}" required>
    </div>


    {{-- Phone --}}
    <div class="col-md-6 mb-3">
        <label>Phone Number</label>
        <input type="text" name="phone_number" id="id_phone_number" class="form-control form-control-sm"
            placeholder="Enter Phone number" value="{{ old('phone_number', $user->phone_number ?? '') }}">
    </div>

    {{-- User Category --}}
    <div class="col-md-6 mb-3">
        <label>User Category *</label>
        <select name="role_id" id="id_role_id" class="form-select form-select-sm" required>
            <option value="">Select role</option>
            @php
                if ($logged_user_category == 'super_admin') {
                    $user_not_in = [''];
                } else {
                    $user_not_in = ['super_admin', 'developer', 'it_admin'];
                }
            @endphp

            @foreach (App\Models\Roles::whereNotIn('unique_key', $user_not_in)->get() as $role)
                @if (old('role_id', $user->role_id ?? '') == $role->id)
                    <option value="{{ $role->id }}" selected>
                        {{ $role->role_name }}
                    </option>
                @else
                    <option value="{{ $role->id }}">
                        {{ $role->role_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>

    {{-- Password (only if new user) --}}
    @if (!isset($user))
        <div class="col-md-6 mb-3">
            <label>Password *</label>
            <div class="input-group input-group-sm">
                <input type="password" name="password" id="id_password" class="form-control form-control-sm"
                    placeholder="Enter password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('id_password')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label>Confirm Password *</label>
            <div class="input-group input-group-sm">
                <input type="password" name="password_confirmation" id="id_password_confirmation"
                    class="form-control form-control-sm" placeholder="Re-enter password" required>
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('id_password_confirmation')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- Profile Picture --}}
    <div class="col-md-6 mb-3">
        <label>Profile Picture</label>
        <input type="file" name="profile_picture" id="id_profile_picture" class="form-control form-control-sm">
    </div>


    <div class="col-md-6 mb-3">
        <label>Employee Code </label>
        <input type="text" name="emp_code" id="id_emp_code" class="form-control form-control-sm"
            placeholder="Enter Employee Code" value="{{ $emp_code }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Hire Date </label>
        <input type="text" name="hire_date" id="id_hire_date" class="datepicker form-control form-control-sm"
            placeholder="Select hire date" value="{{ $hire_date }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>Branch</label>
        <select name="branch_id" id="id_branch_id" class="form-select form-select-sm">
            <option value="">Select branch</option>
            @foreach (App\Models\Branches::all() as $b)
                @if (old('branch_id', $user->branch_id ?? '') == $b->id)
                    <option value="{{ $b->id }}" selected>
                        {{ $b->name }}
                    </option>
                @else
                    <option value="{{ $b->id }}">
                        {{ $b->name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>

    {{-- Default Module --}}
    <div class="col-md-6 mb-3">
        <label>Module *</label>
        <select name="default_module" id="id_default_module" class="form-select form-select-sm" required>
            <option value="">Select module</option>
            @foreach (App\Models\Module::where('is_active', true)->get() as $mod)
                @if (old('default_module', $user->default_module_id ?? '') == $mod->id)
                    <option value="{{ $mod->id }}" selected>
                        {{ $mod->name }}
                    </option>
                @else
                    <option value="{{ $mod->id }}">
                        {{ $mod->name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
    @if (isset($user))
        <input type="hidden" id="menu_id_load" name="menu_id_load" value="{{ $user->default_menu_id }}">
        <input type="hidden" id="sub_menu_id_load" name="sub_menu_id_load" value="{{ $user->default_sub_menu_id }}">
    @else
        <input type="hidden" id="menu_id_load" name="menu_id_load" value="">
        <input type="hidden" id="sub_menu_id_load" name="sub_menu_id_load" value="">
    @endif

    {{-- Default Menu --}}
    <div class="col-md-6 mb-3">
        <label>Default Menu *</label>
        <select name="default_menu" id="id_default_menu" class="form-select form-select-sm" required>
            <option value="">Select menu</option>
            {{-- Will be filled by AJAX --}}
        </select>
    </div>

    {{-- Default Sub Menu --}}
    <div class="col-md-6 mb-3">
        <label>Default Sub Menu</label>
        <select name="default_sub_menu" id="id_default_sub_menu" class="form-select form-select-sm">
            <option value="">Select sub menu</option>
            {{-- Will be filled by AJAX --}}
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <div class="form-check mt-4">
            <input type="checkbox" name="is_active" id="id_is_active" class="form-check-input" id="is_active"
                value="1" @if (old('is_active', $user->is_active ?? true)) checked @endif>
            <label class="form-check-label" for="is_active">Mark user as active</label>
        </div>
    </div>


</div>
<link href="{{ asset('libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<button type="submit" class="btn btn-primary btn-sm d-none" id="save_user">Save User</button>
<script src="{{ asset('libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ri-eye-line');
            icon.classList.add('ri-eye-off-line');
        } else {
            input.type = 'password';
            icon.classList.remove('ri-eye-off-line');
            icon.classList.add('ri-eye-line');
        }
    }

    $(document).ready(function() {

        $(".datepicker").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            todayBtn: true,
            todayHighlight: true,
            toggleActive: true,

        });



        $('#save_user').click(function(e) {
            e.preventDefault();

            let form = $('#user_form')[0]; // Get raw DOM element
            let formData = new FormData(form); // Create FormData object
            preloader.load()
            $.ajax({
                type: "post",
                url: "{{ route('employee.store_user') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    popAlert(response.message, 'Success', 'success')
                    $('.user_id').val(response.id)
                    $('#permission_form').html(response.permission_form)
                    preloader.stop()
                },
                error: function(xhr) {
                    preloader.stop()
                    handleFormErrors(xhr.responseJSON);
                }

            });
        });
    });
</script>
