<!-- Bootstrap Stepper CSS -->
<link href="{{ asset('libs/bs-stepper/bs-stepper.min.css') }}" rel="stylesheet" />


<div id="stepper" class="bs-stepper">
    <div class="bs-stepper-header">
        <div class="step" data-target="#user-form"><button class="step-trigger"><span class="bs-stepper-circle"><i
                        class=" ri-user-line"></i></span><span>User Form</span></button></div>
        <div class="bs-stepper-line"></div>
        <div class="step" data-target="#user-permission-form"><button class="step-trigger"><span
                    class="bs-stepper-circle"><i class="ri-door-lock-box-fill "></i></span><span>User
                    Permission</span></button></div>
        @if ($user)
            <div class="bs-stepper-line"></div>
            <div class="step" data-target="#change-password-form"><button class="step-trigger"><span
                        class="bs-stepper-circle"><i class="ri-key-fill"></i></span><span>Change
                        Password</span></button>
            </div>
        @endif
    </div>
    <div class="bs-stepper-content">


        <div id="user-form" role="tabpanel" class="bs-stepper-pane fade" aria-labelledby="company trigger">
            <form method="POST" enctype="multipart/form-data" id="user_form"
                action="{{ route('employee.store_user') }}">
                @csrf
                @if ($user)
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif
                @include('employees.partials.employee_form')
            </form>
        </div>

        <div id="user-permission-form" role="tabpanel" class="bs-stepper-pane fade"
            aria-labelledby="user permission trigger">
            <div id="permission_form">
                @if ($user)
                    @include('employees.partials.permission_form')
                @else
                    <p>Please Save the User to continue</p>
                @endif
            </div>
        </div>

        @if ($user)
            <div id="change-password-form" role="tabpanel" class="bs-stepper-pane fade"
                aria-labelledby="change password trigger">
                @include('employees.partials.change_password_form')
            </div>
        @endif

    </div>
</div>

<script src="{{ asset('libs/bs-stepper/bs-stepper.min.js') }}"></script>


<script>
    $(document).ready(function() {
        const stepper = new Stepper(document.querySelector('#stepper'), {
            linear: false,
            animation: true
        })
        window.stepper = stepper;

        $('#offcanvasCustomFooter').html(`
         <button class="btn btn-sm btn-primary" onclick="save_emp_info()">Save</button>
        `);

        const id_default_module = $('#offcanvasCustom #id_default_module').val()

        if (id_default_module != '') {
            changeModule(id_default_module)
        }
        $('#offcanvasCustom #id_default_module').on('change', function(e) {
            e.preventDefault()

            const moduleId = $(this).val();

            changeModule(moduleId)

        });

        // Load sub menus on menu change
        $('#offcanvasCustom #id_default_menu').on('change', function() {
            const menuId = $(this).val();
            changeMenu(menuId)
        });

    });

    function save_emp_info() {
        let currentIndex = stepper._currentIndex;
        if (currentIndex == 0) {
            $('#save_user').click()
            // under employees/partials/employee_form.blade.php
        } else if (currentIndex == 1) {
            saveAuthForm();
        } else if (currentIndex == 2) {
            savePasswordChangeForm();
        }
    }

    function changeModule(moduleId) {
        $('#offcanvasCustom #id_default_menu').empty().append(
            '<option value="">Select Menu</option>');
        $('#offcanvasCustom #id_default_sub_menu').empty().append(
            '<option value="">Select Sub Menu</option>');

        if (moduleId) {
            $.get("{{ route('autocomplete.menus') }}", {
                module_id: moduleId,
            }, function(menus) {
                menus.forEach(menu => {
                    $('#offcanvasCustom #id_default_menu').append(
                        `<option value="${menu.id}">${menu.name}</option>`);
                });
                const menu_id = $('#offcanvasCustom #menu_id_load').val()
                $('#offcanvasCustom #id_default_menu').val(menu_id)
                $('#offcanvasCustom #menu_id_load').val('')
                if (menu_id != '') {
                    changeMenu(menu_id)
                }

            });
        }
    }

    function changeMenu(menuId) {
        $('#offcanvasCustom #id_default_sub_menu').empty().append(
            '<option value="">Select Sub Menu</option>');

        if (menuId) {
            $.get("{{ route('autocomplete.submenus') }}", {
                menu_id: menuId
            }, function(submenus) {

                submenus.forEach(submenu => {
                    $('#offcanvasCustom #id_default_sub_menu').append(
                        `<option value="${submenu.id}">${submenu.name}</option>`
                    );
                });

                const sub_menu_id = $('#offcanvasCustom #sub_menu_id_load').val()
                $('#offcanvasCustom #id_default_sub_menu').val(sub_menu_id)
                $('#offcanvasCustom #sub_menu_id_load').val('')
            });
        }
    }
</script>
