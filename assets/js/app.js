// utility selectors
const select = (sel, root = document) => root.querySelector(sel);
const selectAll = (sel, root = document) => Array.from(root.querySelectorAll(sel));

function sidebarToggle() {
    const sidebar = select('.sidebar');
    const content = select('.main-content');
    const itemSpans = selectAll('.sidebar-item .nav-item span');

    if (!sidebar) return;

    const isMobile = window.innerWidth <= 768;
    const isOpen = sidebar.classList.contains('open');

    // Toggle class
    sidebar.classList.toggle('open', !isOpen);

    // For desktop, toggle margin on content
    if (!isMobile) {
        content.classList.toggle('sidebar-open', !isOpen);
    }

    const hamButton = select('#ham-sidebar');
    if (hamButton) {
        hamButton.style.backgroundColor = !isOpen ? 'var(--borderex)' : 'var(--border3b)';
        hamButton.setAttribute('aria-expanded', !isOpen);
    }

    const hamburger = select('#hamburger');
    if (hamburger) {
        hamburger.classList.toggle('active', !isOpen);
    }
}

function blackLayerActive(container) {
    if (!container) return;

    // Show the content panel
    container.style.right = '0';
    container.style.opacity = '1';
    // Show blacklayer
    selectAll('.blacklayer').forEach(bl => bl.style.display = 'block');


    setTimeout(() => {
        container.classList.add('open');
        container.style.right = '0';
        container.style.opacity = '1';
    }, 100);
}

function openContent(rawRecord) {
    const content = select('.admin-section:not(.d-none) .content-bar.record-bar');
    if (!content) return;

    if (rawRecord) {
        // Map database keys to expected keys for statmod calculations and charting
        const mappedRecord = {
            ...rawRecord,
            'rec_id': rawRecord.rec_id,
            'nic': rawRecord.nic,
            'name': rawRecord.name,
            'log_no': rawRecord.log_no,
            'created_at': rawRecord.created_at,
            'notes': rawRecord.notes,
            // These short keys are required by statmod.js and chart.js
            r: parseInt(rawRecord.score_r, 10) || 0,
            i: parseInt(rawRecord.score_i, 10) || 0,
            a: parseInt(rawRecord.score_a, 10) || 0,
            s: parseInt(rawRecord.score_s, 10) || 0,
            e: parseInt(rawRecord.score_e, 10) || 0,
            c: parseInt(rawRecord.score_c, 10) || 0
        };
        loadRecordData(mappedRecord, content);
    }

    blackLayerActive(content);
}


function closeContent() {
    const content = select('.admin-section:not(.d-none) .content-bar.open');
    if (!content) return;
    
    // Start the animation
    content.classList.remove('open');
    content.style.right = '-40vw';
    content.style.opacity = '0';

    const blayers = selectAll('.blacklayer');

    // Hide blacklayers after animation completes
    setTimeout(() => {
        blayers.forEach(bl => {
            bl.style.display = 'none';
        });
    }, 300);
}

function sectionNavigate(e) {
    e.preventDefault();
    const linkID = e.currentTarget.id;
    const targetId = linkID.replace('link-', '');

    selectAll('.admin-section').forEach(section => {
        const show = section.id === targetId;
        section.classList.toggle('d-none', !show);
    });

    selectAll('.sidebar-link').forEach(link => {
        link.parentElement.classList.toggle('active', link === e.currentTarget);
    });

    const sidebar = select('.sidebar');
    if (sidebar && window.innerWidth <= 768) {
        sidebarToggle();
    }
}

function navigateToSection(targetId) {
    const tlink = select(`.sidebar-link[data-section="${targetId}"]`);
    if (tlink) tlink.click();

    selectAll('.admin-section').forEach(section => {
        const show = section.id === targetId;
        section.classList.toggle('d-none', !show);
    });

    selectAll('.sidebar-link').forEach(link => {
        link.parentElement.classList.toggle('active', link === tlink);
    });
}

/**
 * Load and display record data
 * @param {string|object} record - Record ID or record object
 * @param {HTMLElement} container - The content panel container
 */
function loadRecordData(record, container) {
    if (!record) {
        console.warn('No record data provided to loadRecordData');
        return;
    }
    
    if (!container) {
        console.warn('No container provided to loadRecordData');
        return;
    }

    // If record is a string, fetch the data (TODO: implement fetch)
    if (typeof record === 'string') {
        console.log('TODO: Fetch record data for ID:', record);
        return;
    }

    viewRecord(record, container);
}


/**
 * Display record details in the provided container
 * @param {object} record - Record data object
 * @param {HTMLElement} container - Container to display record in
 */
function viewRecord(record, container) {
    if (!record || !container) {
        console.warn('Record or container not provided to viewRecord');
        return;
    }

    // Base Details - use context-sensitive selectors
    const recordIdEl = select('.record-rid', container);
    if (recordIdEl) recordIdEl.textContent = record['rec_id'] ?? 'N/A';
    const recordNameEl = select('.record-name', container);
    if (recordNameEl) recordNameEl.textContent = record['name'] ?? 'N/A';
    const recordNICEl = select('.record-nic', container);
    if (recordNICEl) recordNICEl.textContent = record['nic'] ?? 'N/A';
    const recordAttEl = select('.record-attempt', container);
    if (recordAttEl) recordAttEl.textContent = record['log_no'] ?? 'N/A';
    const recordDateEl = select('.record-date', container);
    if (recordDateEl) recordDateEl.textContent = record['created_at'] ?? 'N/A';

    // RIASEC scores
    ['r','i','a','s','e','c'].forEach(key => {
        const el = select(`.record-${key}`, container);
        if (el) el.textContent = record[key] ?? '';
    });

    // Additional metadata fields
    calculateRecStat(record).then(() => {
        const personalityEl = select('.record-personality', container);
        if (personalityEl) personalityEl.textContent = record.top3 ?? 'N/A';
        const maxCatEl = select('.record-max-category', container);
        if (maxCatEl) maxCatEl.textContent = riasecDef[record.maxCat?.category] ?? 'N/A';
        const minCatEl = select('.record-min-category', container);
        if (minCatEl) minCatEl.textContent = riasecDef[record.minCat?.category] ?? 'N/A';
        const scoreRangeEl = select('.record-score-range', container);
        if (scoreRangeEl) scoreRangeEl.textContent = record.score_range ?? 'N/A';
        const consistencyEl = select('.record-consistency', container);
        if (consistencyEl) consistencyEl.textContent = record.consistency_score !== undefined ? record.consistency_score.toFixed(2) : 'N/A';
    });

    // Create charts using data-chart-id attribute
    const canvases = selectAll('canvas[data-chart-id]', container);
    canvases.forEach(canvas => {
        const chartId = canvas.getAttribute('data-chart-id');
        if (chartId.includes('bar')) createRecordBarChart(record, container);
        if (chartId.includes('radar')) createRecordRadarChart(record, container);
    });

        // Notes field - use data attribute
        const notesEl = select('textarea[data-notes-section]', container);
        if (notesEl) {
            notesEl.value = record['notes'] ?? '';
            notesEl.disabled = true;
        }
}



/**
 * Generic chart toggle handler for bar/radar charts
 * Updated to work with data-chart-id attributes instead of element IDs
 * @param {Event} e - Click event
 */
function chartToggle(e) {
    const btn = e.currentTarget;
    const chartType = btn.getAttribute('data-chart'); // "bar" or "radar"
    const parentContainer = btn.closest('.record-chart-area');
    if (!parentContainer) return;

    // Update button active states and ARIA
    parentContainer.querySelectorAll('.toggle-btn').forEach(b => {
        const isActive = b === btn;
        b.classList.toggle('active', isActive);
        b.setAttribute('aria-pressed', isActive);
    });

    // Hide all chart containers
    parentContainer.querySelectorAll('.canvas-container').forEach(c => c.classList.add('d-none'));

    // Find and show the selected chart using data-chart-id
    const charts = parentContainer.querySelectorAll(`canvas[data-chart-id*="${chartType}"]`);
    if (charts.length > 0) {
        charts.forEach(canvas => {
            canvas.closest('.canvas-container')?.classList.remove('d-none');
        });
    }
}

/**
 * Activate notes editing mode for a specific section
 * @param {string} notesSection - The data-notes-section identifier
 */
function activateNotes(notesSection) {
    const textarea = document.querySelector(`textarea[data-notes-section="${notesSection}"]`);
    const editBtn = document.querySelector(`[data-action="edit-notes"][data-notes-section="${notesSection}"]`);
    const saveBtn = document.querySelector(`.save-notes-btn[data-notes-section="${notesSection}"]`);
    const cancelBtn = document.querySelector(`.cancel-notes-btn[data-notes-section="${notesSection}"]`);
    
    if (textarea) {
        textarea.disabled = false;
        textarea.focus();
    }
    if (editBtn) editBtn.disabled = true;
    if (saveBtn) saveBtn.disabled = false;
    if (cancelBtn) cancelBtn.disabled = false;
}

/**
 * Deactivate notes editing mode for a specific section
 * @param {string} notesSection - The data-notes-section identifier
 */
function deactivateNotes(notesSection) {
    const textarea = document.querySelector(`textarea[data-notes-section="${notesSection}"]`);
    const editBtn = document.querySelector(`[data-action="edit-notes"][data-notes-section="${notesSection}"]`);
    const saveBtn = document.querySelector(`.save-notes-btn[data-notes-section="${notesSection}"]`);
    const cancelBtn = document.querySelector(`.cancel-notes-btn[data-notes-section="${notesSection}"]`);
    
    if (textarea) textarea.disabled = true;
    if (editBtn) editBtn.disabled = false;
    if (saveBtn) saveBtn.disabled = true;
    if (cancelBtn) cancelBtn.disabled = true;
}

function tabSwitch(e) {
    e.preventDefault();
    const targetTab = e.currentTarget.getAttribute('data-tab');
    const container = e.currentTarget.closest('.tabbar-container');
    if (!container) return;

    const contentArea = container.nextElementSibling;
    selectAll('.tab-content', contentArea.parentElement).forEach(tc => tc.classList.add('d-none'));
    selectAll('.tab-btn', container).forEach(btn => {
        const isActive = btn === e.currentTarget;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-selected', isActive);
    });

    const targetEl = select(`#${targetTab}`);
    if (targetEl) targetEl.classList.remove('d-none');
}

function collapseRecord(e) {
    e.preventDefault();
    const btn = e.currentTarget;
    const user = btn.closest('.rper-i');
    if (!user) return;

    const userId = user.getAttribute('for');

    // Hide all other content rows
    const allContent = user.closest('tbody').querySelectorAll('.rper-r');
    allContent.forEach(rc => {
        if (rc.id !== userId) rc.classList.add('d-none');
    });
    
    const content = user.closest('tbody').querySelector(`.rper-r#${userId}`);
    if (!content) return;

    // Check current visibility state
    const isCurrentlyHidden = content.classList.contains('d-none');
    
    // Toggle visibility based on current state
    if (isCurrentlyHidden) {
        // Currently hidden, show it
        content.classList.remove('d-none');
        user.style.backgroundColor = 'var(--border1b)';
        btn.innerHTML = `<span class="fa fa-minus"></span>`;
    } else {
        // Currently visible, hide it
        content.classList.add('d-none');
        user.style.backgroundColor = '';
        btn.innerHTML = `<span class="fa fa-plus"></span>`;
    }
}

function collapseAdminMenu() {
    const menu = select('.admin-menu');
    const isOpen = menu.classList.contains('open');
    menu.classList.toggle('open', !isOpen);
    menu.style.display = !isOpen ? 'block' : 'none';

    // when clicked outside, close the menu
    if (!isOpen) {
        const outsideClickListener = (event) => {
            if (!menu.contains(event.target) && !event.target.closest('.admin-info')) {
                menu.classList.remove('open');
                menu.style.display = 'none';
                document.removeEventListener('click', outsideClickListener);
            }
        };
        document.addEventListener('click', outsideClickListener);
    }
}

// bind events once DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    selectAll('.sidebar-link').forEach(l => l.addEventListener('click', sectionNavigate));  // Navigation links
    selectAll('.tab-btn').forEach(b => b.addEventListener('click', tabSwitch)); // Tab buttons
    // selectAll('.btn.btn-status').forEach(b => b.addEventListener('click', statusToggle)); // Status toggle buttons
    selectAll('.btn.btn-collps').forEach(b => b.addEventListener('click', collapseRecord)); // Collapse record buttons
    selectAll('.record-chart-area .toggle-btn').forEach(b => b.addEventListener('click', chartToggle));  // Chart toggle buttons (updated to work with new data-chart-id)

    // Add resize listener for responsive sidebar
    window.addEventListener('resize', () => {
        const sidebar = select('.sidebar');
        const content = select('.main-content');
        const isMobile = window.innerWidth <= 768;
        const isOpen = sidebar.classList.contains('open');

        if (isMobile && isOpen) {
            // On mobile, if open, keep it, but ensure styles
            // Since CSS handles it, no need to do much
        } else if (!isMobile && isOpen) {
            content.classList.add('sidebar-open');
        } else {
            content.classList.remove('sidebar-open');
        }
    });
});


// simple error display helper; the page should contain an element with
// id="errorMessage" for visible feedback, otherwise falls back to alert.
function displayError(message) {
  const el = document.getElementById('errorMessage');
  if (el) {
    el.textContent = message;
    el.style.display = 'block';
  } else {
    // eslint-disable-next-line no-alert
    alert(message);
  }
}

/**
 * Collapses an alert element's height to 0 with a smooth transition,
 * then removes it from the DOM — so sibling alerts slide into place
 * rather than jumping when the gap closes.
 */
function collapseAndRemove(alertDiv, alertModal, duration = 100) {
    // 1. Lock the current rendered height as an explicit px value
    alertDiv.style.height = alertDiv.offsetHeight + 'px';
    alertDiv.style.overflow = 'hidden';

    // 2. Force a reflow so the browser registers the explicit height
    //    before we transition it to 0
    // eslint-disable-next-line no-unused-expressions
    alertDiv.offsetHeight;

    // 3. Transition height, padding and gap contribution to 0
    alertDiv.style.transition = `height ${duration}ms ease-out,
                                 padding ${duration}ms ease-out,
                                 margin ${duration}ms ease-out`;
    alertDiv.style.height  = '0';
    alertDiv.style.padding = '0';
    alertDiv.style.margin  = '0';

    // 4. Remove from DOM and conditionally hide the container
    setTimeout(() => {
        alertDiv.remove();
        if (!alertModal.querySelector('.alert')) {
            alertModal.style.display = 'none';
        }
    }, 1000);
}

function displayAlert(message, type) {
    const alertModal = document.getElementById('alert-modal');
    if (!alertModal) {
        alert(message);
        return;
    }

    // Icon map for each alert type
    const iconMap = {
        error:   'fa-times-circle',
        success: 'fa-check-circle',
        warning: 'fa-exclamation-triangle',
        info:    'fa-info-circle'
    };
    const iconClass = iconMap[type] || 'fa-info-circle';

    // Build the alert element using the existing markup structure
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${type}`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        <div class="alert-logo"><i class="fas ${iconClass}"></i></div>
        <div class="alert-text">
            <p class="alert-message">${message}</p>
        </div>
    `;

    // Show the container and append this alert (flex-direction:column-reverse stacks upward)
    alertModal.style.display = 'flex';
    alertModal.appendChild(alertDiv);

    // Each alert manages its own lifecycle — no shared timers
    const fadeTimer = setTimeout(() => {
        alertDiv.classList.add('fade-out');
    }, 4000);

    // After fade completes, collapse height so sibling alerts slide smoothly
    const hideTimer = setTimeout(() => {
        collapseAndRemove(alertDiv, alertModal);
    }, 4500);

    // Allow clicking an alert to dismiss it early
    alertDiv.addEventListener('click', () => {
        clearTimeout(fadeTimer);
        clearTimeout(hideTimer);
        alertDiv.classList.add('fade-out');
        // Wait for the CSS opacity transition (0.5s) then collapse
        setTimeout(() => collapseAndRemove(alertDiv, alertModal), 500);
    });
}

/**
 * Save notes for a specific section
 * @param {string} notesSection - The data-notes-section identifier
 */
async function saveRecordNotes(notesSection) {
    const saveBtn = document.querySelector(`.save-notes-btn[data-notes-section="${notesSection}"]`);
    const textarea = document.querySelector(`textarea[data-notes-section="${notesSection}"]`);
    const container = saveBtn.closest('.content-bar');
    const recIdEl = container ? container.querySelector('.record-rid') : null;

    if (!saveBtn || !textarea || !recIdEl) {
        console.error('Required elements for saving notes not found.');
        return;
    }

    const recId = recIdEl.textContent.trim();
    const notes = textarea.value;

    if (!recId || recId === 'N/A') {
        alert('Invalid record ID.');
        return;
    }

    const originalText = saveBtn.textContent;
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    try {
        const formData = new FormData();
        formData.append('rec_id', recId);
        formData.append('notes', notes);

        const response = await fetch('actions/update_notes.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            console.log('Notes saved successfully.');
            displayAlert('Notes saved successfully.', 'success');

            // Update notes field and sync it with frontend for the specific record
            if (data.recid == recId) {
                textarea.value = data.notes;
            }

            deactivateNotes(notesSection);
            
            // Refresh the record UI to ensure notes are reflected everywhere
            // try {
            //     const freshResp = await fetch('actions/get_record.php', {
            //         method: 'POST',
            //         headers: { 'Content-Type': 'application/json' },
            //         body: JSON.stringify({ rec_id: recId })
            //     });
            //     const freshData = await freshResp.json();
            //     if (freshData.success && freshData.record) {
            //         viewRecord(freshData.record, container);
            //     }
            // } catch (fetchErr) {
            //     console.warn('Could not refresh record after notes save:', fetchErr);
            // }
        } else {
            console.error('Failed to save notes:', data.message);
            displayAlert('Failed to save notes: ' + data.message, 'error');
            saveBtn.disabled = false;
        }
        saveBtn.textContent = originalText;
    } catch (error) {
        console.error('Error saving notes:', error);
        displayAlert('An error occurred while saving notes.', 'error');
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
    }
}

async function deleteRecord(recId) {
    try {
        const formData = new FormData();
        formData.append('rec_id', recId);

        const response = await fetch('actions/delete_record.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const recIdStr = recId.toString();

            // Reload the current paginated page of the records table (if pagination active)
            if (typeof loadRecordsPage === 'function' && typeof recCurrentPage !== 'undefined') {
                await loadRecordsPage(recCurrentPage);
            }

            // Sub-rows in the Records By Person tab store rec_id in the 2nd cell
            document.querySelectorAll('.rperson-table tbody tr').forEach(row => {
                if (row.cells[1]?.textContent.trim() === recIdStr) {
                    row.remove();
                }
            });

            // Reload charts/stats
            if (typeof loadData === 'function') {
                await loadData();
            }
            console.log('Record deleted successfully.');
            displayAlert('Record deleted successfully.', 'success');
        } else {
            console.error('Failed to delete record:', data.message);
            displayAlert('Failed to delete record: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting record:', error);
        displayAlert('An error occurred while deleting the record.', 'error');
    }
}

async function deletePerson(nic) {
    try {
        const formData = new FormData();
        formData.append('nic', nic);

        const response = await fetch('actions/delete_person.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Reload both AJAX-rendered tables to reflect the deletion
            if (typeof loadRecordsPage === 'function' && typeof recCurrentPage !== 'undefined') {
                await loadRecordsPage(recCurrentPage);
            }
            if (typeof loadPeoplePage === 'function') {
                await loadPeoplePage();
            }

            // Reload charts/stats
            if (typeof loadData === 'function') {
                await loadData();
            }
            console.log('All Records of this Person deleted successfully.');
            displayAlert('All Records of this Person deleted successfully.', 'success');
        } else {
            console.error('Failed to delete records of this person:', data.message);
            displayAlert('Failed to delete records of this person: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting records of this person:', error);
        displayAlert('An error occurred while deleting the records of this person.', 'error');
    }
}

async function deleteStaff(staff_id) {
    try {
        const formData = new FormData();
        formData.append('staff_id', staff_id);

        const response = await fetch('actions/delete_staff.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // --- Sync the staffs table row in-place ---
            const staffsTable = document.getElementById('staffs-table');
            if (staffsTable) {
                // Find the row where the first cell (staff_id) matches
                const rows = staffsTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    if (row.cells[0]?.textContent.trim() == staff_id) {
                        row.remove();
                    }
                });

                // Update the staffs count badge
                const countEl = staffsTable.closest('.tab-content')?.querySelector('.count-data');
                if (countEl) {
                    const remaining = staffsTable.querySelectorAll('tbody tr').length;
                    countEl.textContent = ` ${remaining} ${remaining === 1 ? 'Staff' : 'Staffs'} `;
                }
            }

            console.log('Staff deleted successfully.');
            displayAlert('Staff deleted successfully.', 'success');
        } else {
            console.error('Failed to delete staff:', data.message);
            displayAlert('Failed to delete staff: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting staff:', error);
        displayAlert('An error occurred while deleting the staff.', 'error');
    }
}

async function toggleRoleStatus(role_id) {
    try {
        const formData = new FormData();
        formData.append('role_id', role_id);

        const response = await fetch('actions/toggle_role-status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const newStatus = data.new_status; // 'active' or 'inactive'

            // --- Sync the roles table row in-place ---
            const rolesTable = document.getElementById('roles-table');
            if (rolesTable) {
                // Find the toggle button for this role by its onclick attribute pattern
                const toggleBtn = rolesTable.querySelector(`button[onclick*="confirmToggleRoleStatus(${role_id},"]`);
                const row = toggleBtn ? toggleBtn.closest('tr') : null;

                if (toggleBtn && row) {
                    // 1. Update status badge cell
                    const statusCell = row.querySelector('td[name="Role Status"] div');
                    if (statusCell) {
                        statusCell.className = `btn btn-sm btn-${newStatus}`;
                        statusCell.textContent = newStatus === 'active' ? 'Active' : 'Inactive';
                    }

                    // 2. Update the toggle button icon, class, and onclick
                    const isNowActive = newStatus === 'active';
                    toggleBtn.classList.toggle('btn-deactivate', isNowActive);
                    toggleBtn.classList.toggle('btn-activate', !isNowActive);
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-minus-square', isNowActive);
                        icon.classList.toggle('fa-plus-square', !isNowActive);
                    }
                    toggleBtn.setAttribute('onclick', `confirmToggleRoleStatus(${role_id}, '${newStatus}')`);
                }

                // Also sync the staffs table Role Status column for any staff with this role
                // and update the "Active Staffs" dashboard counter
                const staffsTable = document.getElementById('staffs-table');
                if (staffsTable && row) {
                    // Get role name from the roles table row (2nd <td>)
                    const roleName = row.cells[1]?.textContent.trim();
                    if (roleName) {
                        let affectedCount = 0;
                        staffsTable.querySelectorAll('tbody tr').forEach(staffRow => {
                            // Role column is the 6th cell (index 5) in the staffs table
                            const roleCell = staffRow.cells[5];
                            if (roleCell && roleCell.textContent.trim().toLowerCase() === roleName.toLowerCase()) {
                                // Sync role status badge in staffs table
                                const badge = staffRow.querySelector('td[name="Role Status"] div');
                                if (badge) {
                                    badge.className = `btn btn-sm btn-${newStatus}`;
                                    badge.textContent = newStatus === 'active' ? 'Active' : 'Inactive';
                                }
                                // Count staffs who are themselves active (they contribute to the active count)
                                const staffStatusBadge = staffRow.querySelector('td[name="Status"] div');
                                if (staffStatusBadge && staffStatusBadge.classList.contains('btn-active')) {
                                    affectedCount++;
                                }
                            }
                        });

                        // Adjust the dashboard "Active Staffs" counter
                        const activeStaffsEl = document.getElementById('active-staffs');
                        if (activeStaffsEl) {
                            const current = parseInt(activeStaffsEl.textContent.trim(), 10) || 0;
                            const isNowActive = newStatus === 'active';
                            activeStaffsEl.textContent = ` ${Math.max(0, current + (isNowActive ? affectedCount : -affectedCount))} `;
                        }
                    }
                }
            }

            console.log('Role status toggled successfully.');
            displayAlert('Role status toggled successfully.', 'success');
        } else {
            console.error('Failed to toggle role status:', data.message);
            displayAlert('Failed to toggle role status: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error toggling role status:', error);
        displayAlert('An error occurred while toggling the role status.', 'error');
    }
}


async function toggleStaffStatus(staff_id) {
    try {
        const formData = new FormData();
        formData.append('staff_id', staff_id);

        const response = await fetch('actions/toggle_staff-status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const newStatus = data.new_status; // 'active' or 'inactive'

            // --- Sync the staffs table row in-place ---
            const staffsTable = document.getElementById('staffs-table');
            if (staffsTable) {
                // Find the toggle button by matching onclick containing the staff_id and comma (status always included now)
                const toggleBtn = staffsTable.querySelector(`button[data-action="toggle-staff"][onclick*="confirmToggleStaffStatus(${staff_id},"]`);
                const row = toggleBtn ? toggleBtn.closest('tr') : null;

                if (toggleBtn && row) {
                    // 1. Update status badge cell
                    const statusCell = row.querySelector('td[name="Status"] div');
                    if (statusCell) {
                        statusCell.className = `btn btn-sm btn-${newStatus}`;
                        statusCell.textContent = newStatus === 'active' ? 'Active' : 'Inactive';
                    }

                    // 2. Update the toggle button class, icon, and onclick for next interaction
                    const isNowActive = newStatus === 'active';
                    toggleBtn.classList.toggle('btn-deactivate', isNowActive);
                    toggleBtn.classList.toggle('btn-activate', !isNowActive);
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-minus-square', isNowActive);
                        icon.classList.toggle('fa-plus-square', !isNowActive);
                    }
                    toggleBtn.setAttribute('onclick', `confirmToggleStaffStatus(${staff_id}, '${newStatus}')`);
                }
            }

            // --- Sync the "Active Staffs" dashboard counter ---
            const activeStaffsEl = document.getElementById('active-staffs');
            if (activeStaffsEl) {
                let current = parseInt(activeStaffsEl.textContent.trim(), 10) || 0;
                const isNowActive = newStatus === 'active';
                activeStaffsEl.textContent = ` ${Math.max(0, current + (isNowActive ? 1 : -1))} `;
            }

            console.log('Staff status toggled successfully.');
            displayAlert('Staff status toggled successfully.', 'success');
        } else {
            console.error('Failed to toggle Staff status:', data.message);
            displayAlert('Failed to toggle Staff status: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error toggling Staff status:', error);
        displayAlert('An error occurred while toggling the Staff status.', 'error');
    }
}