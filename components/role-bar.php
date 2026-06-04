<div class="content-bar role-bar" id="role-bar" data-section="role">
        <div class="head-ribbon">
            <h4 class="title">Role Form</h4>
            <button type="button" class="btn close-btn" data-action="close-content" aria-label="Close panel" onclick="closeContent()"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="content-container form-container role-container" id="role-container">
            <div class="form role-form base-details">
                <div class="form-field role-field base-detail-field">
                    <label for="role-name"><span class="req-mark">*</span>Role Name:</label>
                    <input type="text" id="role-name" name="role-name" placeholder="Role name" value="" readonly>
                </div>
                <div class="form-field role-field base-detail-field">
                    <label for="role-description"> Description:</label>
                    <textarea id="role-description" name="role-description" placeholder="Role description" value="" readonly></textarea>
                </div>
            </div>
            <div class="role-form role-permissions">
                <h5>Permissions</h5>
                <div class="permissions-list">
                    
                    <div class="permission-item">
                        <input type="checkbox" id="perm-edit-records" name="permissions" value="edit-records" readonly>
                        <label for="perm-edit-records">Edit Records</label>
                    </div>
                    <div class="permission-item">
                        <input type="checkbox" id="perm-view-records" name="permissions" value="view-records" readonly>
                        <label for="perm-view-records">View Records</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox" id="perm-cud-staff-n" name="permissions" value="cud-staff" readonly>
                        <label for="perm-cud-staff-n">Create, Edit, Delete Staff</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox" id="perm-view-staff" name="permissions" value="view-staff" checked readonly>
                        <label for="perm-view-staff">View Staff</label>
                    </div>
                    
                    <div class="permission-group">
                        <?php 
                        foreach($roles as $role) {                        
                            echo '<div class="permission-item">';
                            echo '<input type="checkbox" id="perm-cud-'.$role['role_name']. '" name="permissions" value="cud-staff" readonly>';
                            echo '<label for="perm-cud-'.$role['role_name']. '">'.ucfirst($role['role_name']).'</label>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    
                    <div class="permission-item">
                        <input type="checkbox" id="perm-cud-roles" name="permissions" value="cud-roles" readonly>
                        <label for="perm-cud-roles">Update Role</label>
                    </div>
                    <div class="permission-item">
                        <input type="checkbox" id="perm-view-roles" name="permissions" value="view-roles" checked readonly>
                        <label for="perm-view-roles">View Roles</label>
                    </div>
                    
                </div>
            </div> 
        </div>
    </div>

<script>

    function openContentRo(rawRole) {
        const content = select('.admin-section:not(.d-none) .content-bar.role-bar');
        if (!content) return;

        if (rawRole) {
            // Map database keys to expected keys for statmod calculations and charting
            const mappedRole = {
                ...rawRole,
                'role_id': rawRole.role_id,
                'role_name': rawRole.role_name,
                'role_description': rawRole.role_description,
                'crud_staff': rawRole.crud_staff,
                'crud_admins': rawRole.crud_admins,
                'update_role': rawRole.update_role,
                'view_results': rawRole.view_results,
                'crud_results': rawRole.crud_results,
                'role_status': rawRole.role_status
            };
            loadRoleData(mappedRole, content);
        }
        blackLayerActive(content);
    }

    /**
     * Load and display role data
     * @param {string|object} role - Role ID or role object
     * @param {HTMLElement} container - The content panel container
     */
    function loadRoleData(role, container) {
        if (!role) return;
        if (typeof role === 'string') {
            console.log('TODO: Fetch role data for ID:', role);
            return;
        }
        viewRole(role, container);
    }

    function viewRole(role, container) {
        if (!role || !container) return;

        const roleNameInput = select("#role-name", container);
        if (roleNameInput) roleNameInput.value = role['role_name'];

        const roleDescInput = select("#role-description", container);
        if (roleDescInput) roleDescInput.value = role['role_description'];

        const checkboxes = selectAll(".role-permissions input[type='checkbox']", container);
        checkboxes.forEach(checkbox => {
            if (checkbox.value == 'edit-records') checkbox.checked = role['crud_results'];
            if (checkbox.value == 'view-records') checkbox.checked = role['view_results'];
            if (checkbox.value == 'cud-staff') {
                if (checkbox.id == 'perm-cud-staff-n') checkbox.checked = role['crud_staff'];
                if (checkbox.id == 'perm-cud-staff') checkbox.checked = role['crud_staff'];
                if (checkbox.id == 'perm-cud-admin' || checkbox.id == 'perm-cud-superadmin') checkbox.checked = role['crud_admins'];
            }
            if (checkbox.value == 'cud-roles') checkbox.checked = role['update_role'];
        });
    }
        
</script>