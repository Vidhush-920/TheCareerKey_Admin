<?php 
    function is_enable_crud($s_crud_staff, $s_crud_admins, $role) {
        if($s_crud_staff == 0) {
            return false;
        } else {
            if($s_crud_admins == 0) {
                if($role == 'admin' || $role == 'superadmin') {
                    return false;
                }
            }
            return true;
        }
    }
?>

<section id="staffs" class="admin-section d-none">
    <div class="blacklayer"></div>
    <div class="container-fluid">
        <div class="section-heading row mb-4">
            <div class="col-12">
                <h1>Staff Management</h1>
                <hr>
            </div>
        </div>
        <div class="common-container">
            <div class="tabbar-container" role="tablist">
                <button type="button" class="btn btn-sm tab-btn active" data-tab="staff-tab" role="tab" aria-selected="true" aria-controls="staff-tab">Staffs</button>
                <button type="button" class="btn btn-sm tab-btn" data-tab="role-tab" role="tab" aria-selected="false" aria-controls="role-tab">Roles</button>
            </div>
            <div id="staff-tab" class="tab-content staff-tab">
                <div class="row tab-row">
                    <h2>Staffs</h2>
                    <span class="count-data"> <?php $stf = count($staffs); echo $stf; if ($stf == 1) { echo " Staff"; } else { echo " Staffs"; } ?> </span>
                </div>
                
                <div class="row tab-row">
                    <div class="search-box">
                        <input type="text" name="" class="search-bar staff-search-bar" id="staff-search-bar" placeholder="Search Staff..">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="action-btns">
                        <button type="button" class="btn btn-sm btn-primary" data-action="add-staff" onclick="openContentSt()" <?php echo $s_crud_staff == 0 ? 'disabled' : '' ?>><i class="fas fa-user-plus" aria-hidden="true"></i> Add New Staff</button>
                    </div>
                </div>
                <div class="table-div">
                    <table id="staffs-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>NIC</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Role Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffs as $row) { ?>
                                <tr data-username="<?php echo htmlspecialchars(strtolower($row['username']), ENT_QUOTES, 'UTF-8'); ?>" style="<?php echo $row['staff_id'] === $s_user_id ? 'font-weight: bold;' : ''; ?>">
                                    <td style="<?php echo $row['staff_id'] === $s_user_id ? 'background-color: #80ff9bff;' : ''; ?>"><?php echo $row['staff_id']; ?></td>
                                    <td><?php echo $row['fname'].' '.$row['lname']; ?></td>
                                    <td><?php echo $row['nic']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['phone']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td name="Role Status">
                                        <div class="btn btn-sm btn-<?php echo $row['role_status']; ?>"><?php echo ucfirst($row['role_status']); ?></div>
                                    </td>
                                    <td name="Status">
                                        <div class="btn btn-sm btn-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></div>
                                    </td>
                                    <td class="table-actions">
                                        <?php if($row['staff_id'] !== $s_user_id) { ?>
                                            <button type="button" class="btn btn-sm btn-edit" data-action="edit-staff" title="Edit Staff" data-id="<?php echo $row['staff_id']; ?>" onclick="openContentSt(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)"><i class="fas fa-edit" aria-hidden="true"></i></button>
                                            <button type="button" class="btn btn-sm btn-status btn-<?php echo $row['status'] === 'active' ? 'deactivate' : 'activate'; ?>" data-action="toggle-staff" title="Toggle Status" onclick="confirmToggleStaffStatus(<?php echo $row['staff_id'] ?>, '<?php echo $row['status']; ?>')" <?php echo is_enable_crud($s_crud_staff, $s_crud_admins, $row['role']) ? '' : 'disabled'; ?>><i class="fas fa-<?php echo $row['status'] === 'active' ? 'minus' : 'plus'; ?>-square" aria-hidden="true"></i></button>
                                            <button type="button" class="btn btn-sm btn-delete" data-action="delete-staff" title="Delete Staff" onclick="openConfirmModal('Delete Staff?', 'Are you sure you want to delete this staff?', deleteStaff, 'Delete', '<?php echo $row['staff_id']; ?>')" <?php echo is_enable_crud($s_crud_staff, $s_crud_admins, $row['role']) ? '' : 'disabled'; ?>><i class="fas fa-trash" aria-hidden="true"></i></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="role-tab" class="tab-content role-tab d-none">
                <div class="row tab-row">
                    <h2>Roles</h2>
                    <span class="count-data"> <?php $rls = count($roles); echo $rls; if ($rls == 1) { echo " Role"; } else { echo " Roles"; } ?> </span>
                </div>
                <div class="table-div">
                    <table id="roles-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $row) { ?>
                                <tr>
                                    <td><?php echo $row['role_id']; ?></td>
                                    <td><?php echo $row['role_name']; ?></td>
                                    <td name="Role Status"><div class="btn btn-sm btn-<?php echo $row['role_status']; ?>"><?php echo ucfirst($row['role_status']); ?></div></td>
                                    <td class="table-actions">
                                        <button type="button" class="btn btn-sm btn-edit" data-action="edit-role" title="View Role" onclick="openContentRo(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)"><i class="fas fa-eye" aria-hidden="true"></i></button>
                                        <?php if ($row['role_name'] !== 'superadmin') { ?>
                                            <button type="button" class="btn btn-sm btn-status btn-<?php echo $row['role_status'] === 'active' ? 'deactivate' : 'activate'; ?>" data-action="toggle-role" title="Toggle Status" onclick="confirmToggleRoleStatus(<?php echo $row['role_id']; ?>, '<?php echo $row['role_status']; ?>')" <?php if($s_update_role == 0 || $s_role_id == $row['role_id']) { echo "disabled"; } ?> >
                                                <i class="fas fa-<?php echo $row['role_status'] === 'active' ? 'minus' : 'plus'; ?>-square" aria-hidden="true"></i>
                                            </button>
                                        <?php } ?>
                                        <!-- <button type="button" class="btn btn-sm btn-delete" data-action="delete-role" title="Delete Role"><i class="fas fa-trash" aria-hidden="true"></i></button> -->
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <?php include 'components/staff-bar.php'; ?>
    
    <?php include 'components/role-bar.php'; ?>
    
</section>

<script>

    function confirmToggleStaffStatus(staff_id, status) {
        if (status == 'active') {
            openConfirmModal('Deactivate Staff?', 'Are you sure you want to deactivate this staff?', toggleStaffStatus, 'Deactivate', staff_id);
        } else {
            openConfirmModal('Activate Staff?', 'Are you sure you want to activate this staff?', toggleStaffStatus, 'Activate', staff_id);
        }
    }

    function confirmToggleRoleStatus(role_id, status) {
        if (status == 'active') {
            openConfirmModal('Deactivate Role?', 'Are you sure you want to deactivate this role?', toggleRoleStatus, 'Deactivate', role_id);
        } else {
            openConfirmModal('Activate Role?', 'Are you sure you want to activate this role?', toggleRoleStatus, 'Activate', role_id);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const staffSearchBar = document.getElementById('staff-search-bar');
        if (staffSearchBar) {
            staffSearchBar.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#staffs-table tbody tr');
                
                rows.forEach(row => {
                    const username = row.getAttribute('data-username') || '';
                    const name = row.cells[1]?.textContent.toLowerCase() || '';
                    const nic = row.cells[2]?.textContent.toLowerCase() || '';
                    const email = row.cells[3]?.textContent.toLowerCase() || '';
                    
                    if (username.includes(query) || name.includes(query) || nic.includes(query) || email.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });

    // Real-time sync: when the logged-in user saves their profile,
    // update their row in this table without a full page reload.
    const _currentStaffId = String(<?php echo json_encode($s_user_id); ?>);

    window.addEventListener('profileUpdated', function(e) {
        const p = e.detail; // fresh staff row returned by update_profile.php
        if (!p) return;

        // Find the row whose ID cell matches the current user's staff_id
        const rows = document.querySelectorAll('#staffs-table tbody tr');
        rows.forEach(function(row) {
            const idCell = row.cells[0];
            if (idCell && idCell.textContent.trim() === _currentStaffId) {
                // col 1 – Full Name
                row.cells[1].textContent = (p.fname || '') + ' ' + (p.lname || '');
                // col 2 – NIC
                row.cells[2].textContent = p.nic || '';
                // col 3 – Email
                row.cells[3].textContent = p.email || '';
                // col 4 – Phone
                row.cells[4].textContent = p.phone || '';
                // keep data-username in sync for the search filter
                row.setAttribute('data-username', (p.username || '').toLowerCase());
            }
        });
    });
</script>