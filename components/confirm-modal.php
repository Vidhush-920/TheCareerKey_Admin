<div class="modal-space" id="confirm-modal-space">
    <div class="confirm-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-mid-area">
                <p class="modal-message"></p>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-sm btn-primary" id="confirm-btn">Confirm</button>
                <button type="button" class="btn btn-sm btn-deactivate" id="cancel-btn">Cancel</button>
            </div>
        </div>
    </div> 
</div>

<script>
    const modalSpace = document.querySelector('#confirm-modal-space');
    const modalHeader = modalSpace.querySelector('.modal-header');
    const confirmModal = modalSpace.querySelector('.confirm-modal');
    const confirmBtn = modalSpace.querySelector('#confirm-btn');
    const cancelBtn = modalSpace.querySelector('#cancel-btn');

    function openConfirmModal(modalTitle, modalMessage, action, actionMessage, actionArg1 = null, actionArg2 = null) {
        
        // modalSpace.style.display = 'block';
        modalSpace.classList.add('active');
        confirmModal.querySelector('.modal-title').textContent = modalTitle;
        confirmModal.querySelector('.modal-message').textContent = modalMessage;
        confirmBtn.textContent = actionMessage;
        confirmBtn.classList.remove('btn-primary', 'btn-delete', 'btn-inactive', 'btn-active');
        if (actionMessage == 'Delete' ) {
            confirmBtn.classList.add('btn-delete');
            modalHeader.style.backgroundColor = 'var(--textex)';
        } else if (actionMessage == 'Deactivate') {
            confirmBtn.classList.add('btn-inactive');
            modalHeader.style.backgroundColor = 'var(--inactive)';
        } else if (actionMessage == 'Activate') {
            confirmBtn.classList.add('btn-active');
            modalHeader.style.backgroundColor = 'var(--active)';
        } else if (actionMessage == 'Log Out') {
            confirmBtn.classList.add('btn-deactivate');
            modalHeader.style.backgroundColor = 'var(--textex)';
        } else {
            confirmBtn.classList.add('btn-primary');
            modalHeader.style.backgroundColor = 'var(--primary)';
        }
        
        confirmBtn.onclick = function() {
            if(typeof action !== 'undefined' && action!=null && action!='') {
                if(typeof actionArg1 !== 'undefined' && actionArg1!=null && actionArg1!='') {
                    if(typeof actionArg2 !== 'undefined' && actionArg2!=null && actionArg2!='') {
                        action(actionArg1, actionArg2);
                    } else {
                        action(actionArg1);
                    }
                } else {
                    action();
                }
            }
            // modalSpace.style.display = 'none';
            modalSpace.classList.remove('active');
        };
        
        cancelBtn.onclick = function() {
            // modalSpace.style.display = 'none';
            modalSpace.classList.remove('active');
        };
    }
</script>