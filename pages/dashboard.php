

<section id="dashboard" class="admin-section">
    <div class="blacklayer"></div>
    <div class="container-fluid">
        <div class="section-heading row">
            <div class="col-12 text-center my-2">
                <h1 id="welcomeTitle"><span>Welcome to </span><br><b>The Career Key</b> - Admin Panel</h1>
                <p class="lead">
                    Here you can manage and analyze the records, view statistics, and perform administrative tasks to keep everything running smoothly.
                </p>
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
                    <input type="date" class="form-control" id="d-start-date">
                    <span class="input-group-text">to</span>
                    <input type="date" class="form-control" id="d-end-date">
                    <button class="btn btn-secondary" id="custom-filter" data-type="custom"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            
        </div>
        <div class="common-container dash-container" id="stats">
            <div class="stats-row row">
                <div class="col-md-3 stat-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Total Records</h6>
                            <h3 class="card-text" id="total-records"> 
                                
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Total People</h6>
                            <h3 class="card-text" id="total-people">  </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Total Staffs</h6>
                            <h3 class="card-text" id="total-staffs"> <?php echo count($staffs); ?> </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 stat-card">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Active Staffs</h6>
                            <h3 class="card-text" id="active-staffs" style="color: var(--active)"> <?php echo count($active_staffs); ?> </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="big-common-container">
            <div class="common-container common-h-container dash-container" id="top3stat">
                <h2>The Career Key Based Personality Types</h2>
                <div class="dash-area">
                    <div class="dash-chart-area">
                        <div class="canvas-container">
                            <canvas class="chart" id="top3statChart"></canvas>
                        </div>
                    </div>
                    <div class="dash-text-area">
                        <div class="col-md-6 dash-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Most Preferred Personality Type</h6>
                                    <h3 class="card-text" id="most-preferred-personality">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="common-container common-h-container dash-container" id="maxminstat">
                <h2>Most & Least Preferred Categories</h2>
                <div class="dash-area">
                    <div class="dash-chart-area">
                        <div class="canvas-container">
                            <canvas class="chart" id="maxMinStatChart"></canvas>
                        </div>
                    </div>
                    <div class="dash-text-area">
                        <div class="col-md-6 dash-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Most Preferred Category</h6>
                                    <h3 class="card-text" id="most-preferred-category">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 dash-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Least Preferred Category</h6>
                                    <h3 class="card-text" id="least-preferred-category">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        
    </div>

</section>

<script> 
    const dashSection = document.querySelector('section#dashboard');
    const dashFilterBtns = dashSection.querySelectorAll('.filter-row .filter-btns button');
    const dashCustomFilterBtn = dashSection.querySelector('#custom-filter');
    const dashCustomFilterLabel = dashSection.querySelector('.filter-row .filter-panel span.input-group-text');

    // Combine all filter buttons into one array to manage the 'active' state
    const dashFilterBtnsArray = [...Array.from(dashFilterBtns), dashCustomFilterBtn, dashCustomFilterLabel].filter(Boolean);

    const dashStartDateInput = dashSection.querySelector('#d-start-date');
    const dashEndDateInput = dashSection.querySelector('#d-end-date');

    dashFilterBtnsArray.forEach(btn => {
        btn.addEventListener("click", () => {
            // Remove active from all buttons
            dashFilterBtnsArray.forEach(b => b.classList.remove("active"));
            // Add active to clicked button
            btn.classList.add("active");
            if(btn.id == 'custom-filter') {
                dashCustomFilterLabel.classList.add('active');
            } else if (btn.id == 'remove-filter') {
                if (dashStartDateInput) dashStartDateInput.value = '';
                if (dashEndDateInput) dashEndDateInput.value = '';
            }
            
            // Trigger chart reload
            if (typeof loadData === 'function') {
                loadData();
            }
        });
    });

    [dashStartDateInput, dashEndDateInput].forEach(input => {
        if (input) {
            input.addEventListener('change', () => {
                if (dashCustomFilterBtn) {
                    dashCustomFilterBtn.click();
                }
            });
        }
    });

    getDashFilterType = () => {
        const activeBtn = dashSection.querySelector('.filter-row button.active');
        return activeBtn ? activeBtn.getAttribute('data-type') : null;
    }

    getDashFilterDates = () => {
        const start_date = dashSection.querySelector(".filter-panel #d-start-date").value;
        const end_date = dashSection.querySelector(".filter-panel #d-end-date").value;
        return {
            start_date: start_date,
            end_date: end_date
        };
    }
</script>