<form id="password_change_form">
    @csrf
    <input type="hidden" name="user_id" class="user_id" value="{{ $userId }}">
    <div class="row">
        {{-- Current Password --}}
        <div class="col-md-4 mb-3">
            <label for="id_current_password">Current Password *</label>
            <div class="input-group input-group-sm">
                <input type="password" name="current_password" id="id_current_password"
                    class="form-control form-control-sm" placeholder="Enter current password" required>
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('id_current_password')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        </div>

        {{-- New Password --}}
        <div class="col-md-4 mb-3">
            <label for="id_password">New Password *</label>
            <div class="input-group input-group-sm">
                <input type="password" name="password" id="id_password" class="form-control form-control-sm"
                    placeholder="Enter new password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('id_password')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="col-md-4 mb-3">
            <label for="id_password_confirmation">Confirm New Password *</label>
            <div class="input-group input-group-sm">
                <input type="password" name="password_confirmation" id="id_password_confirmation"
                    class="form-control form-control-sm" placeholder="Re-enter new password" required>
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('id_password_confirmation')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        </div>
    </div>

</form>
<script>
    function savePasswordChangeForm() {
        let form = $('#password_change_form')[0];
        let formData = new FormData(form);

        $.ajax({
            url: "{{ route('employee.update_password') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                preloader.stop()
                popAlert(response.message, 'Success', 'success')
            },
            error: function(xhr) {
                preloader.stop()
                handleFormErrors(xhr.responseJSON);
            }
        });
    }
</script>
