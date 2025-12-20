<div class="row">

    <div class="col">
        <div id="alert_messge" class="alert alert-info alert-sm d-none">

        </div>
    </div>
</div>
<form id="save_permissions_form">
    @csrf
    <input type="hidden" name="save_permission" value="save_permission">
    <input type="hidden" name="url_id" value="{{ $url_id }}">
    <div class="row">

        <div class="col-6">
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control form-control-sm" placeholder="Enter the name" id="name"
                    name="name" value="{{ $url_name }}">
            </div>
        </div>

        <div class="col-6">
            <div class="form-group">
                <label for="">URL</label>
                <input type="text" class="form-control form-control-sm" placeholder="Enter the url" id="url"
                    name="url" value="{{ $url }}">
            </div>
        </div>

        <div class="col-6">
            <div class="form-group">
                <label for="">Allowed Users</label>
                <select class="form-control form-control-sm selectpicker" placeholder="Enter the url"
                    name="allowed_users[]" data-actions-box="true" data-live-search="true" data-size="5" multiple>
                    @foreach ($selected_users as $selected_user_id => $selected_user)
                        <option value="{{ $selected_user_id }}" selected>{{ $selected_user }}</option>
                    @endforeach
                    @foreach ($other_users as $other_user)
                        <option value="{{ $other_user->id }}">{{ $other_user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-6">
            <div class="form-group">
                <label for="">Allowed Roles</label>
                <select class="form-control form-control-sm selectpicker" placeholder="Enter the url"
                    name="allowed_roles[]" data-actions-box="true" data-live-search="true" data-size="5" multiple>

                    @foreach ($selected_roles as $selected_role_id => $selected_role)
                        <option value="{{ $selected_role_id }}" selected>{{ $selected_role }}</option>
                    @endforeach
                    @foreach ($other_roles as $other_role)
                        <option value="{{ $other_role->id }}">{{ $other_role->role_name }}</option>
                    @endforeach
                </select>

            </div>
        </div>
    </div>

    <div class="row">

        <div class="col">
            <button type="button" class="btn btn-sm btn-dark" id="save_permission_btn">
                @if ($url_id == 'new')
                    Save
                @else
                    Update
                @endif
            </button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        $('#save_permission_btn').click(function(e) {
            e.preventDefault();
            preloader.load();
            $.ajax({
                type: "post",
                url: "{{ route('manage_user_permissions') }}",
                data: $('#save_permissions_form').serialize(),
                success: function(response) {
                    preloader.stop()
                    console.log(response)
                },
                error: function(xhr) {
                    preloader.stop()
                    $('#alert_messge').addClass('d-none');
                    $('#alert_messge').html('');
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        // console.log(errors)
                        $('#save_permissions_form .form-control').removeClass(
                            'error-outline');


                        $.each(errors, function(key, value) {

                            $('#' + key).addClass('error-outline');
                        });
                    } else {
                        $('#alert_messge').addClass('alert-info');
                        $('#alert_messge').removeClass('d-none');
                        $('#alert_messge').show();

                        $('#alert_messge').append(xhr.responseJSON.message);
                    }


                }
            });
        });
    });
</script>
