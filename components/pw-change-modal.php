<div class="modal-space" id="pw-change-modal-space">
    <div class="pw-change-modal" id="pw-change-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title m-0">Change Password</h4>
            </div>
            
            <div class="modal-mid-area">
                <form id="change-password-form">
                    <div class="form profile-form">
                        <div class="form-field profile-field">
                            <label for="current-password" class="form-label"><span class="req-mark text-danger">*</span> Current Password:</label>
                            <input type="password" class="form-control" id="current-password" name="current-password" placeholder="Enter current password" required>
                            <span id="current-pw-change-valid-msg" class="pw-valid-msg current-pw-change-valid-msg"></span>
                        </div>
                        <div class="form-field profile-field">
                            <label for="new-password" class="form-label"><span class="req-mark text-danger">*</span> New Password:</label>
                            <input type="password" class="form-control" id="new-password" name="new-password" placeholder="Enter new password" required>
                            <span id="new-pw-change-valid-msg" class="pw-valid-msg new-pw-change-valid-msg"></span>
                        </div>
                        <div class="form-field profile-field">
                            <label for="confirm-new-password" class="form-label"><span class="req-mark text-danger">*</span> Confirm New Password:</label>
                            <input type="password" class="form-control" id="confirm-new-password" name="confirm-new-password" placeholder="Confirm new password" required>
                            <span id="confirm-new-pw-change-valid-msg" class="pw-valid-msg confirm-new-pw-change-valid-msg"></span>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-buttons">
                <button type="button" class="btn btn-sm btn-primary" id="confirm-btn" onclick="changePassword()"><i class="fas fa-key"></i> Update Password</button>
                <button type="button" class="btn btn-sm btn-deactivate" id="cancel-btn" onclick="closePwChangeModal()">Cancel</button>
            </div>
        </div>
    </div> 
</div>


<script>

    const currPwMsg = document.getElementById('current-pw-change-valid-msg');
    const newPwMsg = document.getElementById('new-pw-change-valid-msg');
    const confirmNewPwMsg = document.getElementById('confirm-new-pw-change-valid-msg');
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmNewPasswordInput = document.getElementById('confirm-new-password');

    // Function to change password
    async function changePassword() {

        const currentPassword = currentPasswordInput.value;
        const newPassword = newPasswordInput.value;
        const confirmNewPassword = confirmNewPasswordInput.value;

        if (currentPasswordInput && currentPassword === '') {
            currPwMsg.textContent = 'Please enter your current password';
            currPwMsg.style.color = 'red';
            return;
        }

        if (newPasswordInput && newPassword === '') {
            newPwMsg.textContent = 'Please enter your new password';
            newPwMsg.style.color = 'red';
            return;
        }

        if (confirmNewPasswordInput && confirmNewPassword === '') {
            confirmNewPwMsg.textContent = 'Please confirm your new password';
            confirmNewPwMsg.style.color = 'red';
            return;
        }

        if (newPassword !== confirmNewPassword) {
            confirmNewPwMsg.textContent = 'New passwords do not match';
            confirmNewPwMsg.style.color = 'red';
            return;
        }

        const res = await fetch('actions/update_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'current-password=' + currentPassword + '&new-password=' + newPassword + '&confirm-new-password=' + confirmNewPassword
        })

        const data = await res.json();

        if (data.success) {
            displayAlert(data.message, 'success');
            closePwChangeModal();
        } else {
            displayAlert(data.message, 'error');
        }
    }

    (function() {
        // Inline Password Validation similar to staff-bar.php
        if (newPasswordInput && newPwMsg) {
            newPasswordInput.addEventListener('input', () => {
                if (newPasswordInput.value.length > 0 && newPasswordInput.value.length < 6) {
                    newPwMsg.textContent = 'At least 6 characters required.';
                    newPwMsg.style.color = 'red';
                } else {
                    newPwMsg.textContent = '';
                }
            });
        }

        if (confirmNewPasswordInput && confirmNewPwMsg) {
            confirmNewPasswordInput.addEventListener('input', () => {
                if (confirmNewPasswordInput.value && confirmNewPasswordInput.value !== newPasswordInput.value) {
                    confirmNewPwMsg.textContent = 'Passwords do not match.';
                    confirmNewPwMsg.style.color = 'red';
                } else {
                    confirmNewPwMsg.textContent = '';
                }
            });
        }

        // Removed duplicate listener; button already calls changePassword via onclick attribute.
        // const updatePwBtn = document.getElementById('confirm-btn');
        // if (updatePwBtn) {
        //     updatePwBtn.addEventListener('click', changePassword);
        // }
    })();

</script>