                    <div class="topbar">
                        <div class="topbar-head">
                            <div class="sidebar-logo">
                                <img src="assets/images/vcotlogo.png" alt="The Career Key Logo" />
                            </div>
                        </div>
                        
                        <div class="admin-icon">
                            <span class="admin-txt"><?php echo ucfirst($s_role); ?></span>
                            <div class="admin-profile">
                                <div class="admin-info" onclick="collapseAdminMenu()" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-controls="admin-menu">
                                    <i class="fas fa-user-circle i-u-profile"></i>
                                    <span class="admin-name"><?php echo $s_username; ?></span>
                                    <i class="fas fa-angle-down i-angle-down"></i>
                                </div>
                                <div class="admin-menu">
                                    <div class="menu-item" style="color: var(--textex);" onclick="openConfirmModal('Log Out?', 'Are you sure you want to log out?', logout, 'Log Out')" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-controls="logout-modal">
                                        <i class="fas fa-sign-out-alt"></i> 
                                        <span>Logout</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function logout() {
                            window.location.href = './actions/logout.php';
                        }
                    </script>
