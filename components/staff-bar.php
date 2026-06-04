<!-- Used to Display or Update An Existing Staff and Create a New Staff -->
<div class="content-bar staff-bar" id="staff-bar" data-section="staff">
    <div class="head-ribbon">
        <h4 class="title">Staff Form</h4>
        <button type="button" class="btn close-btn" data-action="close-content" aria-label="Close panel" onclick="closeContent()"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="content-container form-container staff-container" id="staff-container">
        <!-- Hidden field carries staff_id in edit mode -->
        <input type="hidden" id="staff-id-hidden">
        <div class="form staff-form base-details">
            <div class="form-field staff-field base-detail-field">
                <label for="staff-role"><span class="req-mark">*</span>Role:</label>
                <select id="staff-role" name="staff-role" required>
                    <option value="" disabled selected>Select a Role</option>
                    <?php 
                        foreach ($roles as $role): 
                            $role_id = $role['role_id'];
                            $role_name = $role['role_name'];
                            $isDisabled = ($s_crud_admins == 0 && ($role_name === 'admin' || $role_name === 'superadmin'));
                            ?>
                            <option value="<?php echo $role_id; ?>" <?php echo $isDisabled ? 'disabled style="display:none;"' : ''; ?>><?php echo $role_name; ?></option>
                        <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field staff-field base-detail-field">
                <label for="staff-username"><span class="req-mark">*</span>User Name: </label>
                <input type="text" id="staff-username" name="staff-username" placeholder="User name" required>
                <span id="username-valid-msg" class="username-valid-msg"></span>
            </div>
            <div class="row staff-name-row">
                <div class="form-field staff-field base-detail-field staff-name-field">
                    <label for="staff-fname"><span class="req-mark">*</span>First Name: </label>
                    <input type="text" id="staff-fname" name="staff-fname" placeholder="First name" required>
                </div>
                <div class="form-field staff-field base-detail-field staff-name-field">
                    <label for="staff-lname">Last Name: </label>
                    <input type="text" id="staff-lname" name="staff-lname" placeholder="Last name">
                </div>
            </div>
            <div class="form-field staff-field base-detail-field">
                <label for="staff-nic"><span class="req-mark">*</span>NIC No: </label>
                <input type="text" id="staff-nic" name="staff-nic" placeholder="NIC number" required>
            </div>
            <div class="form-field staff-field base-detail-field">
                <label for="staff-email"><span class="req-mark">*</span>Email: </label>
                <input type="email" id="staff-email" name="staff-email" placeholder="Email address" required>
            </div>
            <div class="form-field staff-field base-detail-field">
                <label for="staff-phone">Phone: </label>
                <input type="tel" id="staff-phone" name="staff-phone" placeholder="Phone number">
            </div>
        </div>
        <div class="form staff-form pw-details">
            <div class="form-field staff-field base-detail-field">
                <label for="staff-password"><span class="req-mark">*</span>Password: </label>
                <input type="password" id="staff-password" name="staff-password" placeholder="Password">
                <span id="pw-valid-msg" class="pw-valid-msg"></span>
            </div>
            <div class="form-field staff-field base-detail-field">
                <label for="staff-confirm-password"><span class="req-mark">*</span>Confirm Password: </label>
                <input type="password" id="staff-confirm-password" name="staff-confirm-password" placeholder="Confirm password">
                <span id="pw-match-msg" class="pw-match-msg"></span>
            </div>
        </div>
        <div class="form-btns">
            <button type="button" class="btn btn-sm btn-primary mt-2" id="save-staff-btn" data-action="save-staff">Save</button>
            <button type="button" class="btn btn-sm btn-delete mt-2" data-action="close-content" onclick="closeContent()">Cancel</button>
        </div>   
    </div>
</div>

<script>

    const sessionUserId = <?php echo json_encode($s_user_id); ?>;
    const sessionCrudStaff = <?php echo (int)$s_crud_staff; ?>;
    const sessionCrudAdmins = <?php echo (int)$s_crud_admins; ?>;

    function isEnableCrud(role) {
        if (sessionCrudStaff === 0) {
            return false;
        }
        if (sessionCrudAdmins === 0) {
            if (role === 'admin' || role === 'superadmin') {
                return false;
            }
        }
        return true;
    }

    function openContentSt(rawStaff = null) {
        const content = select('.admin-section:not(.d-none) .content-bar.staff-bar');
        if (!content) return;

        resetStaffForm(content);

        if (rawStaff) {
            const mappedStaff = {
                'staff_id':   rawStaff['staff_id'],
                'username':   rawStaff['username'],
                'role_id':    rawStaff['role_id'],
                'role':       rawStaff['role'],
                'fname':      rawStaff['fname'],
                'lname':      rawStaff['lname'],
                'nic':        rawStaff['nic'],
                'email':      rawStaff['email'],
                'phone':      rawStaff['phone'],
                'status':     rawStaff['status'],
                'role_name':  rawStaff['role_name'],
                'role_status':rawStaff['role_status']
            };
            loadStaffData(mappedStaff, content);
            const title = select('.title', content);
            if (title) title.textContent = 'Edit Staff';
        } else {
            const title = select('.title', content);
            if (title) title.textContent = 'Add Staff';
        }

        blackLayerActive(content);
    }

    function resetStaffForm(container) {
        // Clear hidden id
        const hiddenId = select('#staff-id-hidden', container);
        if (hiddenId) hiddenId.value = '';

        // Reset all text/email/tel/password inputs and enable them
        selectAll('input:not([type="hidden"])', container).forEach(inp => {
            inp.value = '';
            inp.disabled = false;
        });

        // Reset role select to placeholder and enable it
        const roleSelect = select('#staff-role', container);
        if (roleSelect) {
            roleSelect.selectedIndex = 0;
            roleSelect.disabled = false;
        }

        // Show password section (required for new staff)
        const pwSection = select('.pw-details', container);
        if (pwSection) pwSection.style.display = '';

        // Clear validation messages
        const pwValidMsg = select('#pw-valid-msg', container);
        const pwMatchMsg = select('#pw-match-msg', container);
        if (pwValidMsg) pwValidMsg.textContent = '';
        if (pwMatchMsg) pwMatchMsg.textContent = '';

        // Enable save button
        const saveBtn = select('#save-staff-btn', container);
        if (saveBtn) saveBtn.disabled = false;
    }

    function loadStaffData(staff, container) {
        if (!staff) return;
        viewStaff(staff, container);
    }

    function viewStaff(staff, container) {
        if (!staff || !container) return;

        // Set hidden staff_id
        const hiddenId = select('#staff-id-hidden', container);
        if (hiddenId) hiddenId.value = staff['staff_id'];

        const isMe = staff.staff_id == sessionUserId;
        const canSave = sessionCrudStaff === 1 && (isMe || isEnableCrud(staff.role));

        // Role select
        const roleSelect = select('select#staff-role', container);
        if (roleSelect) {
            selectAll('option', roleSelect).forEach(opt => {
                opt.selected = (opt.value == staff['role_id']);
            });
            roleSelect.disabled = !canSave || sessionCrudAdmins === 0;
        }

        // Text fields
        const usernameInput = select('input#staff-username', container);
        if (usernameInput) {
            usernameInput.value = staff['username'] ?? '';
            usernameInput.disabled = true;
        }

        const firstNameInput = select('input#staff-fname', container);
        if (firstNameInput) {
            firstNameInput.value = staff['fname'] ?? '';
            firstNameInput.disabled = true;
        }

        const lastNameInput = select('input#staff-lname', container);
        if (lastNameInput) {
            lastNameInput.value = staff['lname'] ?? '';
            lastNameInput.disabled = true;
        }

        const nicInput = select('input#staff-nic', container);
        if (nicInput) {
            nicInput.value = staff['nic'] ?? '';
            nicInput.disabled = true;
        }

        const emailInput = select('input#staff-email', container);
        if (emailInput) {
            emailInput.value = staff['email'] ?? '';
            emailInput.disabled = true;
        }

        const phoneInput = select('input#staff-phone', container);
        if (phoneInput) {
            phoneInput.value = staff['phone'] ?? '';
            phoneInput.disabled = true;
        }

        // Save button
        const saveBtn = select('#save-staff-btn', container);
        if (saveBtn) {
            saveBtn.disabled = !canSave;
        }

        // Hide password section when editing (optional — only update if filled)
        const passwordArea = select('.pw-details', container);
        if (passwordArea) passwordArea.style.display = 'none';
    }

    // ── Save (Add / Update) ──────────────────────────────────────────────────

    async function saveStaff() {
        const container = select('.admin-section:not(.d-none) .content-bar.staff-bar');
        if (!container) return;

        const staffId  = select('#staff-id-hidden', container)?.value.trim() ?? '';
        const roleId   = select('#staff-role', container)?.value.trim() ?? '';
        const username = select('#staff-username', container)?.value.trim() ?? '';
        const fname    = select('#staff-fname', container)?.value.trim() ?? '';
        const lname    = select('#staff-lname', container)?.value.trim() ?? '';
        const nic      = select('#staff-nic', container)?.value.trim() ?? '';
        const email    = select('#staff-email', container)?.value.trim() ?? '';
        const phone    = select('#staff-phone', container)?.value.trim() ?? '';
        const password = select('#staff-password', container)?.value ?? '';
        const confirm  = select('#staff-confirm-password', container)?.value ?? '';

        const isUpdate = staffId !== '';

        // Required field check
        if (!roleId || !username || !fname || !nic || !email) {
            displayAlert('Please fill in all required fields.', 'warning');
            return;
        }

        // Password validation
        if (!isUpdate && password === '') {
            displayAlert('Password is required for new staff.', 'warning');
            return;
        }
        if (password !== '' && password.length < 6) {
            displayAlert('Password must be at least 6 characters.', 'warning');
            return;
        }
        if (password !== '' && password !== confirm) {
            displayAlert('Passwords do not match.', 'warning');
            return;
        }

        const saveBtn = select('#save-staff-btn', container);
        const originalText = saveBtn?.textContent ?? 'Save';
        if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

        try {
            const formData = new FormData();
            formData.append('staff_id', staffId);
            formData.append('role_id',  roleId);
            formData.append('username', username);
            formData.append('fname',    fname);
            formData.append('lname',    lname);
            formData.append('nic',      nic);
            formData.append('email',    email);
            formData.append('phone',    phone);
            formData.append('password', password);

            const response = await fetch('actions/add_staff.php', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                if (data.is_update) {
                    updateStaffRow(data.staff);
                } else {
                    prependStaffRow(data.staff);
                    updateStaffCounter();
                }
                closeContent();
                console.log(data.message);
                displayAlert(data.message, 'success');
            } else {
                displayAlert(data.message, 'error');
            }
        } catch (err) {
            console.error('Error saving staff:', err);
            displayAlert('An error occurred while saving staff.', 'error');
        } finally {
            if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = originalText; }
        }
    }

    // ── DOM helpers ──────────────────────────────────────────────────────────

    function buildStaffRow(staff) {
        const cap = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        const statusCls     = staff.status      || 'active';
        const roleStatusCls = staff.role_status || 'active';
        const toggleCls  = staff.status === 'active' ? 'deactivate' : 'activate';
        const toggleIcon = staff.status === 'active' ? 'minus' : 'plus';

        const tr = document.createElement('tr');
        tr.setAttribute('data-username', (staff.username || '').toLowerCase());
        
        const isMe = staff.staff_id == sessionUserId;
        const crudEnabled = isEnableCrud(staff.role);
        
        let actionsHtml = '';
        if (!isMe) {
            actionsHtml = `
                <button type="button" class="btn btn-sm btn-edit" data-action="edit-staff" title="Edit Staff" data-id="${staff.staff_id}"><i class="fas fa-edit" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-sm btn-status btn-${toggleCls}" data-action="toggle-staff" title="Toggle Status" onclick="confirmToggleStaffStatus(${staff.staff_id}, '${staff.status}')" ${crudEnabled ? '' : 'disabled'}><i class="fas fa-${toggleIcon}-square" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-sm btn-delete" data-action="delete-staff" title="Delete Staff" onclick="openConfirmModal('Delete Staff?', 'Are you sure you want to delete this staff?', deleteStaff, 'Delete', '${staff.staff_id}')" ${crudEnabled ? '' : 'disabled'}><i class="fas fa-trash" aria-hidden="true"></i></button>
            `;
        } else {
            actionsHtml = `
                <button type="button" class="btn btn-sm btn-edit" data-action="edit-staff" title="Edit Staff" data-id="${staff.staff_id}"><i class="fas fa-edit" aria-hidden="true"></i></button>
            `;
        }

        tr.innerHTML = `
            <td>${staff.staff_id}</td>
            <td>${(staff.fname ?? '')} ${(staff.lname ?? '')}</td>
            <td>${staff.nic ?? ''}</td>
            <td>${staff.email ?? ''}</td>
            <td>${staff.phone ?? ''}</td>
            <td>${staff.role ?? ''}</td>
            <td name="Role Status"><div class="btn btn-sm btn-${roleStatusCls}">${cap(roleStatusCls)}</div></td>
            <td name="Status"><div class="btn btn-sm btn-${statusCls}">${cap(statusCls)}</div></td>
            <td>${actionsHtml}</td>`;

        // Wire edit button via addEventListener so we can pass the live object
        tr.querySelector('[data-action="edit-staff"]')
          .addEventListener('click', () => openContentSt(staff));

        return tr;
    }

    function prependStaffRow(staff) {
        const tbody = document.querySelector('#staffs-table tbody');
        if (!tbody) return;
        tbody.appendChild(buildStaffRow(staff));

        // Update count badge
        const countEl = document.querySelector('#staff-tab .count-data');
        if (countEl) {
            const count = tbody.querySelectorAll('tr').length;
            countEl.textContent = ` ${count} ${count === 1 ? 'Staff' : 'Staffs'} `;
        }
    }

    function updateStaffRow(staff) {
        const table = document.getElementById('staffs-table');
        if (!table) return;
        const cap = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

        table.querySelectorAll('tbody tr').forEach(row => {
            if (row.cells[0]?.textContent.trim() != staff.staff_id) return;
            
            row.setAttribute('data-username', (staff.username || '').toLowerCase());
            row.cells[1].textContent = `${staff.fname ?? ''} ${staff.lname ?? ''}`.trim();
            row.cells[2].textContent = staff.nic   ?? '';
            row.cells[3].textContent = staff.email ?? '';
            row.cells[4].textContent = staff.phone ?? '';
            row.cells[5].textContent = staff.role  ?? '';

            const roleStatusBadge = row.querySelector('td[name="Role Status"] div');
            if (roleStatusBadge) {
                roleStatusBadge.className = `btn btn-sm btn-${staff.role_status}`;
                roleStatusBadge.textContent = cap(staff.role_status);
            }

            const isMe = staff.staff_id == sessionUserId;
            const crudEnabled = isEnableCrud(staff.role);

            // Replace edit button to update its closure with fresh staff data
            const editBtn = row.querySelector('[data-action="edit-staff"]');
            if (editBtn) {
                const newBtn = editBtn.cloneNode(true);
                newBtn.disabled = false;
                newBtn.addEventListener('click', () => openContentSt(staff));
                editBtn.replaceWith(newBtn);
            }

            const statusBtn = row.querySelector('[data-action="toggle-staff"]');
            if (statusBtn) {
                statusBtn.disabled = (!isMe && !crudEnabled);
            }

            const deleteBtn = row.querySelector('[data-action="delete-staff"]');
            if (deleteBtn) {
                deleteBtn.disabled = (!isMe && !crudEnabled);
            }
        });
    }

    //Sync Staff-Counter in Dashboard
    
    function updateStaffCounter() {
        const tbody = document.querySelector('#staffs-table tbody');
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('tr');
        const count = rows.length;
        
        // Update tab counter
        const countEl = document.querySelector('#staff-tab .count-data');
        if (countEl) {
            countEl.textContent = ` ${count} ${count === 1 ? 'Staff' : 'Staffs'} `;
        }
        
        // Update Dashboard Total Staffs
        const totalStaffsEl = document.getElementById('total-staffs');
        if (totalStaffsEl) {
            totalStaffsEl.textContent = ` ${count} `;
        }
        
        // Update Dashboard Active Staffs
        const activeStaffsEl = document.getElementById('active-staffs');
        if (activeStaffsEl) {
            let activeCount = 0;
            rows.forEach(row => {
                const roleStatusBadge = row.querySelector('td[name="Role Status"] div');
                const statusBadge = row.querySelector('td[name="Status"] div');
                if (roleStatusBadge && statusBadge &&
                    roleStatusBadge.classList.contains('btn-active') &&
                    statusBadge.classList.contains('btn-active')) {
                    activeCount++;
                }
            });
            activeStaffsEl.textContent = ` ${activeCount} `;
        }
    }

    // ── Inline password validation ───────────────────────────────────────────
    (function () {
        const pwInput    = document.getElementById('staff-password');
        const confirmInp = document.getElementById('staff-confirm-password');
        const pwMsg      = document.getElementById('pw-valid-msg');
        const matchMsg   = document.getElementById('pw-match-msg');

        if (pwInput && pwMsg) {
            pwInput.addEventListener('input', () => {
                if (pwInput.value.length > 0 && pwInput.value.length < 6) {
                    pwMsg.textContent = 'At least 6 characters required.';
                    pwMsg.style.color = 'var(--danger, #e74c3c)';
                } else {
                    pwMsg.textContent = '';
                }
            });
        }

        if (confirmInp && matchMsg) {
            confirmInp.addEventListener('input', () => {
                if (confirmInp.value && confirmInp.value !== pwInput.value) {
                    matchMsg.textContent = 'Passwords do not match.';
                    matchMsg.style.color = 'var(--danger, #e74c3c)';
                } else {
                    matchMsg.textContent = '';
                }
            });
        }

        // Wire Save button
        const saveBtn = document.getElementById('save-staff-btn');
        if (saveBtn) saveBtn.addEventListener('click', saveStaff);
    })();

</script>