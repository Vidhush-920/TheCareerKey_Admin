

<div class="content-bar record-bar" id="record-bar" data-section="record">
        <div class="head-ribbon">
            <h4 class="title">Record Details</h4>
            <button type="button" class="btn close-btn" data-action="close-content" aria-label="Close panel" onclick="closeContent()"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="content-container record-container" id="record-container">
            <div class="record-data base-details" data-section="record">
                <div class="record-field base-detail-field">
                    <h5>R_ID: <span class="record-rid"></span></h5>
                </div>
                <div class="record-field base-detail-field">
                    <h6>Name: <span class="record-name"></span></h6>
                </div>
                <div class="record-field base-detail-field">
                    <h6>NIC No: <span class="record-nic"></span></h6>
                </div>
                <div class="record-field base-detail-field">
                    <h6>Attempt No: <span class="record-attempt"></span></h6>
                </div>
                <div class="record-field base-detail-field">
                    <h6>Recorded at: <span class="record-date"></span></h6>
                </div>
            </div>
            <div class="record-table-area" data-section="record">
                <h5 class="title">Record Details</h5>
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>R</th>
                            <th>I</th>
                            <th>A</th>
                            <th>S</th>
                            <th>E</th>
                            <th>C</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="record-r"></td>
                            <td class="record-i"></td>
                            <td class="record-a"></td>
                            <td class="record-s"></td>
                            <td class="record-e"></td>
                            <td class="record-c"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="record-chart-area" data-section="record">
                <h5 class="title">Record Chart</h5>
                <div class="record-chart-toggle" role="group" aria-label="Chart type selection">
                    <button type="button" class="btn btn-sm btn-secondary toggle-btn active" data-chart="bar" aria-label="Bar chart view">Bar Chart</button>
                    <button type="button" class="btn btn-sm btn-secondary toggle-btn" data-chart="radar" aria-label="Radar chart view" aria-pressed="true">Radar Chart</button>
                </div>
                <div class="record-chart-cons">
                    <div class="canvas-container">
                        <canvas class="chart" id="record-bar-chart" data-chart-id="record-bar"></canvas>
                    </div>
                    <div class="canvas-container d-none">
                        <canvas class="chart" id="record-radar-chart" data-chart-id="record-radar"></canvas>
                    </div>
                </div>
            </div>
            <div class="record-data other-record-data">
                <div class="record-field other-record-field">
                    <h6>Career Personality Type: </h6>
                    <h6 class="record-personality" id="record-personality"></h6>
                </div>
                <div class="record-field other-record-field">
                    <h6>Highly Relevant Category: </h6>
                    <h6 class="record-max-category" id="record-max-category"></h6>
                </div>
                <div class="record-field other-record-field">
                    <h6>Low Relevant Category: </h6>
                    <h6 class="record-min-category" id="record-min-category"></h6>
                </div>
                <div class="record-field other-record-field">
                    <h6>Score Range: </h6>
                    <h6 class="record-score-range" id="record-score-range"></h6>
                </div>
                <div class="record-field other-record-field">
                    <h6>Consistency: </h6>
                    <h6 class="record-consistency" id="record-consistency"></h6>
                </div>
            </div>
            <div class="record-data add-notes">
                <div class="record-field add-notes-field">
                    <div class="admin-notes-head">
                        <h6 class="title">Admin Notes: </h6>
                        <button class="btn btn-sm btn-secondary" data-action="edit-notes" data-notes-section="record" onclick="activateNotes('record')" <?php echo $s_crud_results == 0 ? 'disabled' : '' ?>>Edit Notes</button>
                    </div>
                    <textarea class="admin-notes" id="r-admin-notes" data-notes-section="record" rows="4" placeholder="Add your notes here..." disabled></textarea>
                    <div class="notes-btns">
                        <button class="btn btn-sm btn-primary mt-2 save-notes-btn" data-notes-section="record" disabled>Save Notes</button>
                        <button class="btn btn-sm btn-deactivate mt-2 cancel-notes-btn" data-notes-section="record" onclick="deactivateNotes('record')" disabled>Cancel</button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const saveBtn = document.querySelector('.save-notes-btn[data-notes-section="record"]');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => saveRecordNotes('record'));
        }
    });
</script>