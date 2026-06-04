
<section id="profile" class="admin-section d-none">
    <div class="blacklayer"></div>
    <div class="container-fluid">
        <div class="section-heading row mb-4">
            <div class="col-12">
                <h1>My Profile</h1>
                <hr>
            </div>
        </div>
        
        <div class="row">

            <!-- Sample Profile Picture and Username Section -->
            <div class="dp-column">
                <div class="content-container dp-container p-4 h-100" style="background: var(--bg-1); border: 1px solid var(--border3b); border-radius: 8px;">
                    <div class="dp-wrapper">
                        <div class="dp-img">
                            <img src="./assets/images/avatar.png" alt="Profile">
                        </div>
                        <div class="dp-content">
                            <h4 class="dp-name"><?php echo $s_username; ?></h4>
                            <span class="dp-role role-admin"><?php echo ucfirst($s_role); ?></span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Personal Details Section -->
            <div class="profile-column">
                <div class="content-container form-container profile-container p-4 h-100" style="background: var(--bg-1); border: 1px solid var(--border3b); border-radius: 8px;">
                    <div class="profile-title-container">
                        <h4 style="color: var(--primary)">Personal Details</h4>
                        <div class="profile-actions">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="edit-profile-btn" onclick="activateProfileEdit()">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="change-password-btn" onclick="openPwChangeModal()">
                                <i class="fas fa-lock"></i> Change Password
                            </button>
                        </div>
                    </div>
                    <form id="profile-details-form">
                        <div class="form profile-form">
                            <div class="form-field profile-field mb-3">
                                <label for="profile-user-name"><span class="req-mark text-danger">*</span> Username:</label>
                                <input type="text" class="form-control" id="profile-user-name" name="profile-user-name" value="<?php echo $s_username; ?>" required disabled>
                            </div>
                            <div class="form-field-group row">
                                <div class="form-field profile-field pr-name-field mb-3">
                                    <label for="profile-first-name"><span class="req-mark text-danger">*</span> First Name:</label>
                                    <input type="text" class="form-control" id="profile-first-name" name="profile-first-name" value="<?php echo $s_firstname; ?>" required disabled>
                                </div>
                                <div class="form-field profile-field pr-name-field mb-3">
                                    <label for="profile-last-name"><span class="req-mark text-danger">*</span> Last Name:</label>
                                    <input type="text" class="form-control" id="profile-last-name" name="profile-last-name" value="<?php echo $s_lastname; ?>" required disabled>
                                </div>
                            </div>

                            <div class="form-field profile-field mb-3">
                                <label for="profile-nic"><span class="req-mark text-danger">*</span> NIC No:</label>
                                <input type="text" class="form-control" id="profile-nic" name="profile-nic" value="<?php echo $s_nic; ?>" required disabled>
                            </div>
                            <div class="form-field profile-field mb-3">
                                <label for="profile-email"><span class="req-mark text-danger">*</span> Email:</label>
                                <input type="email" class="form-control" id="profile-email" name="profile-email" value="<?php echo $s_email; ?>" required disabled>
                            </div>
                            <div class="form-field profile-field mb-4">
                                <label for="profile-mobile" >Mobile:</label>
                                <input type="text" class="form-control" id="profile-mobile" name="profile-mobile" value="<?php echo $s_phone; ?>" disabled>
                            </div>
                            <div class="form-actions" id="profile-actions" style="margin-top: 15px;">
                                <button type="submit" class="btn btn-success" id="save-profile-btn" disabled><i class="fas fa-save"></i> Save Changes</button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="cancel-profile-btn" onclick="cancelUpdateProfile()" disabled>Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            
        </div>
    </div>
</section>

<script>

    const formInputs = document.querySelectorAll('#profile-details-form input');

    function activateProfileEdit() {
        document.getElementById('edit-profile-btn').disabled = true;
        for (const input of formInputs) {
            input.disabled = false;
        }
        document.getElementById('save-profile-btn').disabled = false;
        document.getElementById('cancel-profile-btn').disabled = false;
    }

    function deactivateProfileEdit() {
        document.getElementById('edit-profile-btn').disabled = false;
        for (const input of formInputs) {
            input.disabled = true;
        }
        document.getElementById('save-profile-btn').disabled = true;
        document.getElementById('cancel-profile-btn').disabled = true;
    }

    function cancelUpdateProfile() {
        deactivateProfileEdit();
        
        //old values of profile should display again
        document.getElementById('profile-user-name').value = <?php echo json_encode($s_username); ?>;
        document.getElementById('profile-first-name').value = <?php echo json_encode($s_firstname); ?>;
        document.getElementById('profile-last-name').value = <?php echo json_encode($s_lastname); ?>;
        document.getElementById('profile-nic').value = <?php echo json_encode($s_nic); ?>;
        document.getElementById('profile-email').value = <?php echo json_encode($s_email); ?>;
        document.getElementById('profile-mobile').value = <?php echo json_encode($s_phone); ?>;
    }

    function openPwChangeModal() {
        document.getElementById('pw-change-modal-space').classList.add('active');
        document.getElementById('pw-change-modal').classList.add('show');
    }

    function closePwChangeModal() {
        document.getElementById('pw-change-modal-space').classList.remove('active');
        document.getElementById('pw-change-modal').classList.remove('show');
    }

    // Inline script for Profile editing toggles
    document.addEventListener('DOMContentLoaded', () => {
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-profile-btn');
        const formInputs = document.querySelectorAll('#profile-details-form input');

        if(editBtn && cancelBtn) {
            editBtn.addEventListener('click', () => {
                formInputs.forEach(input => {
                    // keep email disabled if you don't want them to change it, otherwise enable all except those you want to protect
                    if(input.id !== 'profile-email') { 
                        input.disabled = false;
                    }
                });
            });

            cancelBtn.addEventListener('click', () => {
                formInputs.forEach(input => {
                    input.disabled = true;
                });
                // You might want to reset the form values to their original state here
            });
        }
    });

    function getUpdatedProfile() {
        const user_name = document.getElementById('profile-user-name').value;
        const first_name = document.getElementById('profile-first-name').value;
        const last_name = document.getElementById('profile-last-name').value;
        const nic = document.getElementById('profile-nic').value;
        const email = document.getElementById('profile-email').value;
        const mobile = document.getElementById('profile-mobile').value;

        if (!user_name || !first_name || !last_name || !nic || !email) {
            displayAlert('Please fill all the required fields', 'warning');
            return null;
        }
        return { user_name, first_name, last_name, nic, email, mobile };
    }

    async function updateProfile() {
        const profile = getUpdatedProfile();
        if (!profile) {
            // Validation failed, re-enable button if it was disabled
            const updateBtn = document.getElementById('save-profile-btn');
            if (updateBtn) {
                updateBtn.textContent = 'Save Changes';
                updateBtn.disabled = false;
            }
            return;
        }

        const updateBtn = document.getElementById('save-profile-btn');
        if (!updateBtn.disabled) {
            updateBtn.textContent = 'Updating...';
            updateBtn.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('profile-user-name', profile.user_name);
            formData.append('profile-first-name', profile.first_name);
            formData.append('profile-last-name', profile.last_name);
            formData.append('profile-nic', profile.nic);
            formData.append('profile-email', profile.email);
            formData.append('profile-mobile', profile.mobile);

            const res = await fetch('actions/update_profile.php', {
                method: 'POST',
                body: formData,
            });

            const result = await res.json();
            if (result.success) {
                displayAlert(result.message, 'success');
                // Update visible profile fields
                document.querySelector('.dp-name').textContent = profile.user_name;
                document.querySelector('.topbar .admin-profile .admin-name').textContent = profile.user_name;
                // Broadcast updated profile for any staff table UI to react
                window.dispatchEvent(new CustomEvent('profileUpdated', { detail: result.profile }));
                deactivateProfileEdit();
            } else {
                displayAlert(result.message, 'error');
            }
        } catch (err) {
            console.error(err);
            displayAlert('An error occurred while updating the profile', 'error');
        } finally {
            if (updateBtn) {
                // Restore button state
                updateBtn.textContent = 'Save Changes';
            }
        }
    }

    const updateBtn = document.getElementById('save-profile-btn');
    if (updateBtn) {
        updateBtn.addEventListener('click', updateProfile);
    }
</script>