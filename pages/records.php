
<section id="records" class="admin-section d-none">
    <div class="blacklayer"></div>
    <div class="container-fluid">
        <div class="section-heading row mb-4">
            <div class="col-12">
                <h1>Record Management</h1>
                <hr>
            </div>
        </div>
        <div class="error-row row">
            <div class="col-12 my-2" id="errorMessage" style="display: none;"></div>
        </div>
        <div class="filter-row row">
            <div class="filter-btns">
                <div class="filter-btn">
                    <button type="button" class="btn btn-secondary active" id="remove-filter" data-type="all">All</button>
                </div>
                <div class="filter-btn">
                    <button type="button" class="btn btn-secondary" id="this-year-filter" data-type="this-year">This Year</button>
                </div>
                <div class="filter-btn">
                    <button type="button" class="btn btn-secondary" id="this-month-filter" data-type="this-month">This Month</button>
                </div>
                <div class="filter-btn">
                    <button type="button" class="btn btn-secondary" id="this-week-filter" data-type="this-week">This Week</button>
                </div>
                <div class="filter-btn">
                    <button type="button" class="btn btn-secondary" id="today-filter" data-type="today">Today</button>
                </div>
            </div>
            
            <div class="filter-panel">
                <div class="input-group">
                    <span class="input-group-text" id="filter-label">Date Range: </span>
                    <input type="date" class="form-control" id="r-start-date">
                    <span class="input-group-text">to</span>
                    <input type="date" class="form-control" id="r-end-date">
                    <button class="btn btn-secondary" id="custom-filter" data-type="custom"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            
        </div>
        <div class="common-container rec-table-container">
            <div class="tabbar-container" role="tablist">
                <button type="button" class="btn btn-sm tab-btn active" data-tab="record-tab" role="tab" aria-selected="true" aria-controls="record-tab">Records</button>
                <button type="button" class="btn btn-sm tab-btn" data-tab="recbp-tab" role="tab" aria-selected="false" aria-controls="recbp-tab">Records by Person</button>
            </div>
            <div id="record-tab" class="tab-content record-tab">
                
                <div class="row tab-row">
                    <h2>Records</h2>
                    <span class="count-data" id="count-record">Loading…</span>
                </div>
                <div class="table-div">
                    <table class="records-table" id="records-table">
                        <thead>
                            <tr>
                                <th>R_ID</th>
                                <th>NIC No</th>
                                <th>Name</th>
                                <th>Recorded at</th>
                                <th>R</th>
                                <th>I</th>
                                <th>A</th>
                                <th>S</th>
                                <th>E</th>
                                <th>C</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="records-table-body">
                            <tr><td colspan="11" class="table-loading">Loading records…</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-bar" id="records-pagination" aria-label="Records table pagination">
                    <div class="pagination-info" id="records-pag-info">Page 1</div>
                    <div class="pagination-controls">
                        <button class="btn btn-sm pag-btn" id="pag-first"  title="First page"    aria-label="First page">&#171;</button>
                        <button class="btn btn-sm pag-btn" id="pag-prev"   title="Previous page" aria-label="Previous page">&#8249;</button>
                        <span class="pag-pages" id="pag-pages"></span>
                        <button class="btn btn-sm pag-btn" id="pag-next"   title="Next page"     aria-label="Next page">&#8250;</button>
                        <button class="btn btn-sm pag-btn" id="pag-last"   title="Last page"     aria-label="Last page">&#187;</button>
                    </div>
                </div>
            </div>
            <div id="recbp-tab" class="tab-content recbp-tab d-none">
                <div class="row tab-row">
                    <h2>Records By Person</h2>
                    <span class="count-data" id="count-person">Loading…</span>
                </div>
                <div class="row tab-row">
                    <div class="search-box">
                        <input type="text" name="" class="search-bar recbp-search-bar" id="recbp-search-bar" placeholder="Search Records by NIC or Name..">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="table-div">
                    <table class="recbp-table" id="recbp-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>NIC No</th>
                                <th colspan="10"></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recbp-table-body">
                            <tr><td colspan="12" class="table-loading">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-bar" id="people-pagination" aria-label="People table pagination" >
                    <div class="pagination-info" id="people-pag-info">Page 1</div>
                    <div class="pagination-controls">
                        <button class="btn btn-sm pag-btn" id="ppl-pag-first"  title="First page"    aria-label="First page">&#171;</button>
                        <button class="btn btn-sm pag-btn" id="ppl-pag-prev"   title="Previous page" aria-label="Previous page">&#8249;</button>
                        <span class="pag-pages" id="ppl-pag-pages"></span>
                        <button class="btn btn-sm pag-btn" id="ppl-pag-next"   title="Next page"     aria-label="Next page">&#8250;</button>
                        <button class="btn btn-sm pag-btn" id="ppl-pag-last"   title="Last page"     aria-label="Last page">&#187;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/record-bar.php'; ?>
    
</section>

<script>
    // ── DOM refs ─────────────────────────────────────────────────────────────
    const recSection       = document.querySelector('section#records');
    const recFilterBtns    = recSection.querySelectorAll('.filter-row .filter-btns button');
    const recCustomFilterBtn   = recSection.querySelector('#custom-filter');
    const recCustomFilterLabel = recSection.querySelector('.filter-row .filter-panel span.input-group-text');
    const recFilterBtnsArray   = [...Array.from(recFilterBtns), recCustomFilterBtn, recCustomFilterLabel].filter(Boolean);
    const recStartDateInput    = recSection.querySelector('#r-start-date');
    const recEndDateInput      = recSection.querySelector('#r-end-date');

    // ── Pagination state ─────────────────────────────────────────────────────
    const REC_LIMIT = 50;
    let recCurrentPage = 1;
    let recTotalPages  = 1;

    const PPL_LIMIT = 50;
    let pplCurrentPage = 1;
    let pplTotalPages  = 1;

    // ── Filter helpers (kept for chart.js / dashboard compatibility) ─────────
    getRecFilterType = () => {
        const activeBtn = recSection.querySelector('.filter-row button.active');
        return activeBtn ? activeBtn.getAttribute('data-type') : 'all';
    };

    getRecFilterDates = () => {
        const start_date = recSection.querySelector('.filter-panel #r-start-date')?.value ?? '';
        const end_date   = recSection.querySelector('.filter-panel #r-end-date')?.value   ?? '';
        return { start_date, end_date };
    };


    // ── Build URL for paginated endpoint ─────────────────────────────────────
    function buildRecUrl(page) {
        const type  = getRecFilterType();
        const dates = getRecFilterDates();
        const params = new URLSearchParams({
            page,
            limit:       REC_LIMIT,
            filter_type: type,
            start_date:  dates.start_date,
            end_date:    dates.end_date,
        });
        return `actions/records-paginated.php?${params}`;
    }

    // ── Render one page of records into the tbody ─────────────────────────────
    function renderRecordsPage(data) {
        const tbody = document.getElementById('records-table-body');
        if (!tbody) return;

        if (!data.records || data.records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="table-empty">No records found.</td></tr>';
            return;
        }

        window._recCache = window._recCache || {};

        tbody.innerHTML = data.records.map(r => {
            window._recCache[r.rec_id] = r;
            return `<tr>
                <td>${escHtml(r.rec_id)}</td>
                <td>${escHtml(r.nic)}</td>
                <td>${escHtml(r.name)}</td>
                <td>${escHtml(r.created_at)}</td>
                <td>${escHtml(r.score_r)}</td>
                <td>${escHtml(r.score_i)}</td>
                <td>${escHtml(r.score_a)}</td>
                <td>${escHtml(r.score_s)}</td>
                <td>${escHtml(r.score_e)}</td>
                <td>${escHtml(r.score_c)}</td>
                <td class="table-actions">
                    <button type="button" class="btn btn-sm btn-edit" data-action="edit-record" title="View Record"
                        onclick="openContent(window._recCache[${r.rec_id}])">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-delete" data-action="delete-record" title="Delete Record"
                        onclick="openConfirmModal('Delete Record?','Are you sure you want to delete this record?',deleteRecord,'Delete',${r.rec_id})"
                        <?php echo $s_crud_results == 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Update pagination controls ────────────────────────────────────────────
    function updatePaginationUI(page, totalPages, total) {
        recCurrentPage = page;
        recTotalPages  = totalPages;

        const info = document.getElementById('records-pag-info');
        if (info) info.textContent = `Page ${page} of ${totalPages}  (${total} record${total !== 1 ? 's' : ''})`;

        const pagesEl = document.getElementById('pag-pages');
        if (pagesEl) {
            // Show a small window of page numbers around current page
            const range = [];
            const delta = 2;
            for (let i = Math.max(1, page - delta); i <= Math.min(totalPages, page + delta); i++) {
                range.push(i);
            }
            pagesEl.innerHTML = range.map(n =>
                `<button class="btn btn-sm pag-btn pag-num${n === page ? ' active' : ''}" data-page="${n}" aria-current="${n === page ? 'page' : 'false'}">${n}</button>`
            ).join('');
            pagesEl.querySelectorAll('.pag-num').forEach(btn => {
                btn.addEventListener('click', () => loadRecordsPage(parseInt(btn.dataset.page)));
            });
        }

        // Boundary buttons
        const first = document.getElementById('pag-first');
        const prev  = document.getElementById('pag-prev');
        const next  = document.getElementById('pag-next');
        const last  = document.getElementById('pag-last');
        if (first) first.disabled = page <= 1;
        if (prev)  prev.disabled  = page <= 1;
        if (next)  next.disabled  = page >= totalPages;
        if (last)  last.disabled  = page >= totalPages;

        // Show / hide the whole bar
        const bar = document.getElementById('records-pagination');
        if (bar) bar.style.display = totalPages <= 1 ? 'none' : 'flex';
    }

    // ── Main loader ───────────────────────────────────────────────────────────
    async function loadRecordsPage(page) {
        const tbody = document.getElementById('records-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="11" class="table-loading">Loading…</td></tr>';

        try {
            const res  = await fetch(buildRecUrl(page));
            const data = await res.json();

            renderRecordsPage(data);
            updatePaginationUI(data.page, data.total_pages, data.total);

            const total = data.total;
            const countEl = document.getElementById('count-record');
            if (countEl) countEl.textContent = `${total} ${total === 1 ? 'Record' : 'Records'}`;

        } catch (e) {
            console.error('Error loading records page:', e);
            if (tbody) tbody.innerHTML = '<tr><td colspan="11" class="table-error">Failed to load records.</td></tr>';
        }
    }

    // ── Build URL for people-filtered endpoint ────────────────────────────────
    function buildPeopleUrl(page) {
        const type  = getRecFilterType();
        const dates = getRecFilterDates();
        const params = new URLSearchParams({
            page,
            limit:       PPL_LIMIT,
            filter_type: type,
            start_date:  dates.start_date,
            end_date:    dates.end_date,
        });
        return `actions/people-filtered.php?${params}`;
    }

    // ── Render the RBP table from JSON ────────────────────────────────────────
    function renderPeopleTable(data) {
        const tbody = document.getElementById('recbp-table-body');
        if (!tbody) return;

        const people = data.people;
        if (!people || people.length === 0) {
            tbody.innerHTML = '<tr><td colspan="12" class="table-empty">No people found for this filter.</td></tr>';
            return;
        }

        window._recCache = window._recCache || {};

        tbody.innerHTML = people.map(person => {
            const nicId  = 'nic' + escHtml(person.nic);
            const subRows = person.records.map(r => {
                window._recCache[r.rec_id] = r;
                return `<tr>
                <td>${escHtml(r.log_no)}</td>
                <td>${escHtml(r.rec_id)}</td>
                <td>${escHtml(r.name)}</td>
                <td>${escHtml(r.created_at)}</td>
                <td>${escHtml(r.score_r)}</td>
                <td>${escHtml(r.score_i)}</td>
                <td>${escHtml(r.score_a)}</td>
                <td>${escHtml(r.score_s)}</td>
                <td>${escHtml(r.score_e)}</td>
                <td>${escHtml(r.score_c)}</td>
                <td class="table-actions">
                    <button type="button" class="btn btn-sm btn-edit" data-action="edit-record" title="Edit Record"
                        onclick="openContent(window._recCache[${r.rec_id}])">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-delete" data-action="delete-record" title="Delete Record"
                        onclick="openConfirmModal('Delete Record?','Are you sure you want to delete this record?',deleteRecord,'Delete',${r.rec_id})"
                        <?php echo $s_crud_results == 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`;
            }).join('');

            return `
            <tr class="rper-i" for="${nicId}" data-name="${escHtml(person.name).toLowerCase()}" data-nic="${escHtml(person.nic).toLowerCase()}">
                <td><button type="button" class="btn btn-sm btn-collps" data-action="expand-person" title="Expand Person"><i class="fa fa-plus"></i></button></td>
                <td>${escHtml(person.name)}</td>
                <td>${escHtml(person.nic)}</td>
                <td colspan="10"></td>
                <td class="table-actions">
                    <button type="button" class="btn btn-sm btn-delete" data-action="delete-person" title="Delete Person"
                        onclick="openConfirmModal('Delete Person?','Delete all records of this person?',deletePerson,'Delete','${escHtml(person.nic)}')"
                        <?php echo $s_crud_results == 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
            <tr class="rper-r d-none" id="${nicId}">
                <td></td><td></td><td></td><td></td>
                <td colspan="10">
                    <table class="rperson-table">
                        <thead><tr>
                            <th>Attempt No</th>
                            <th>R_ID</th>
                            <th>Name</th>
                            <th>Recorded at</th>
                            <th>R</th><th>I</th><th>A</th><th>S</th><th>E</th><th>C</th>
                            <th>Actions</th>
                        </tr></thead>
                        <tbody>${subRows}</tbody>
                    </table>
                </td>
            </tr>`;
        }).join('');

        // Re-bind collapse buttons after re-render
        tbody.querySelectorAll('.btn-collps').forEach(btn => {
            btn.addEventListener('click', collapseRecord);
        });
    }

    // ── Update people pagination controls ─────────────────────────────────────
    function updatePeoplePaginationUI(page, totalPages, total) {
        pplCurrentPage = page;
        pplTotalPages  = totalPages;

        const info = document.getElementById('people-pag-info');
        if (info) info.textContent = `Page ${page} of ${totalPages}  (${total} person${total !== 1 ? 's' : ''})`;

        const pagesEl = document.getElementById('ppl-pag-pages');
        if (pagesEl) {
            const range = [];
            const delta = 2;
            for (let i = Math.max(1, page - delta); i <= Math.min(totalPages, page + delta); i++) {
                range.push(i);
            }
            pagesEl.innerHTML = range.map(n =>
                `<button class="btn btn-sm pag-btn pag-num${n === page ? ' active' : ''}" data-page="${n}" aria-current="${n === page ? 'page' : 'false'}">${n}</button>`
            ).join('');
            pagesEl.querySelectorAll('.pag-num').forEach(btn => {
                btn.addEventListener('click', () => loadPeoplePage(parseInt(btn.dataset.page)));
            });
        }

        const first = document.getElementById('ppl-pag-first');
        const prev  = document.getElementById('ppl-pag-prev');
        const next  = document.getElementById('ppl-pag-next');
        const last  = document.getElementById('ppl-pag-last');
        if (first) first.disabled = page <= 1;
        if (prev)  prev.disabled  = page <= 1;
        if (next)  next.disabled  = page >= totalPages;
        if (last)  last.disabled  = page >= totalPages;

        const bar = document.getElementById('people-pagination');
        if (bar) bar.style.display = totalPages <= 1 ? 'none' : 'flex';
    }

    // ── Load & render the RBP table ───────────────────────────────────────────
    async function loadPeoplePage(page) {
        const tbody = document.getElementById('recbp-table-body');
        if (tbody) tbody.innerHTML = '<tr><td colspan="12" class="table-loading">Loading…</td></tr>';

        try {
            const res  = await fetch(buildPeopleUrl(page));
            const data = await res.json();

            renderPeopleTable(data);
            updatePeoplePaginationUI(data.page, data.total_pages, data.total);

            const total    = data.total;
            const countEl  = document.getElementById('count-person');
            if (countEl) countEl.textContent = `${total} ${total === 1 ? 'Person' : 'People'}`;

        } catch (e) {
            console.error('Error loading people:', e);
            if (tbody) tbody.innerHTML = '<tr><td colspan="12" class="table-error">Failed to load people.</td></tr>';
        }
    }

    // ── filterTableRows — triggers both tables ────────────────────────────────
    async function filterTableRows() {
        recCurrentPage = 1;
        pplCurrentPage = 1;
        await Promise.all([loadRecordsPage(1), loadPeoplePage(1)]);
    }

    // ── HTML escape helper ────────────────────────────────────────────────────
    function escHtml(v) {
        if (v == null) return '';
        return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Filter button wiring ─────────────────────────────────────────────────
    recFilterBtnsArray.forEach(btn => {
        btn.addEventListener('click', () => {
            recFilterBtnsArray.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (btn.id === 'custom-filter') {
                recCustomFilterLabel.classList.add('active');
            } else if (btn.id === 'remove-filter') {
                if (recStartDateInput) recStartDateInput.value = '';
                if (recEndDateInput)   recEndDateInput.value   = '';
            }
            if (typeof loadData === 'function') loadData();
            filterTableRows();
        });
    });

    [recStartDateInput, recEndDateInput].forEach(input => {
        if (input) input.addEventListener('change', () => recCustomFilterBtn?.click());
    });

    // ── Pagination button wiring ──────────────────────────────────────────────
    document.getElementById('pag-first')?.addEventListener('click', () => loadRecordsPage(1));
    document.getElementById('pag-prev') ?.addEventListener('click', () => loadRecordsPage(Math.max(1, recCurrentPage - 1)));
    document.getElementById('pag-next') ?.addEventListener('click', () => loadRecordsPage(Math.min(recTotalPages, recCurrentPage + 1)));
    document.getElementById('pag-last') ?.addEventListener('click', () => loadRecordsPage(recTotalPages));

    document.getElementById('ppl-pag-first')?.addEventListener('click', () => loadPeoplePage(1));
    document.getElementById('ppl-pag-prev') ?.addEventListener('click', () => loadPeoplePage(Math.max(1, pplCurrentPage - 1)));
    document.getElementById('ppl-pag-next') ?.addEventListener('click', () => loadPeoplePage(Math.min(pplTotalPages, pplCurrentPage + 1)));
    document.getElementById('ppl-pag-last') ?.addEventListener('click', () => loadPeoplePage(pplTotalPages));

    // ── Search bar wiring ─────────────────────────────────────────────────────
    const recbpSearchBar = document.getElementById('recbp-search-bar');
    if (recbpSearchBar) {
        recbpSearchBar.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const personRows = document.querySelectorAll('#recbp-table-body .rper-i');
            
            personRows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const nic = row.getAttribute('data-nic') || '';
                const nicId = row.getAttribute('for');
                const subTable = document.getElementById(nicId);
                
                if (name.includes(query) || nic.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                    if (subTable) subTable.classList.add('d-none');
                    const btn = row.querySelector('.btn-collps i');
                    if (btn) {
                        btn.classList.remove('fa-minus');
                        btn.classList.add('fa-plus');
                    }
                }
            });
        });
    }

    // ── Initial load ──────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        loadRecordsPage(1);
        loadPeoplePage(1);
    });
</script>