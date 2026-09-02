<style>
    /* Keep wide Corrective tables draggable without pushing either card off-screen. */
    html body#page-top#page-top .corrective-ops-page .corrective-table-card,
    html body#page-top#page-top .corrective-ops-page .corrective-table-scroll,
    html body#page-top#page-top .corrective-ops-page .dataTables_wrapper {
        min-width: 0 !important;
        max-width: 100% !important;
    }

    /* Wide maintenance tables are clearer as full-width rows, not split columns. */
    html body#page-top#page-top .corrective-ops-page .corrective-table-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 20px !important;
        width: 100% !important;
    }

    html body#page-top#page-top .corrective-ops-page .corrective-table-card {
        width: 100% !important;
        height: auto !important;
        max-height: none !important;
    }

    html body#page-top#page-top .corrective-ops-page .corrective-table-scroll {
        overflow: hidden !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scroll {
        width: 100% !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scrollHead {
        overflow: hidden !important;
        border-radius: 14px 14px 0 0 !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scrollBody {
        width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: auto !important;
        scrollbar-color: #2f80ff #061326 !important;
        overscroll-behavior-x: contain;
        touch-action: pan-x pan-y;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scrollBody::-webkit-scrollbar {
        height: 12px !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scrollBody::-webkit-scrollbar-track {
        border-radius: 999px;
        background: #061326;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_scrollBody::-webkit-scrollbar-thumb {
        min-width: 70px;
        border: 3px solid #061326;
        border-radius: 999px;
        background: linear-gradient(90deg, #2563eb, #2bbcf4);
    }

    html body#page-top#page-top .corrective-ops-page #correctiveAllStatus {
        min-width: 760px !important;
    }

    html body#page-top#page-top .corrective-ops-page #corrective {
        min-width: 680px !important;
    }

    /* Match the shared system pagination: Previous, numbered pages, Next. */
    html body#page-top#page-top .corrective-ops-page .corrective-table-footer {
        width: 100% !important;
        min-width: 0 !important;
        padding-top: 2px !important;
    }

    html body#page-top#page-top .corrective-ops-page .corrective-pages {
        min-width: 0 !important;
        margin-left: auto !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate.paging_simple_numbers {
        width: auto !important;
        max-width: 100% !important;
        min-height: 40px !important;
        margin: 0 !important;
        padding: 2px 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate > span {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button:not(.page-item) {
        position: relative !important;
        width: 38px !important;
        min-width: 38px !important;
        height: 38px !important;
        padding: 0 10px !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid rgba(72, 139, 207, .34) !important;
        border-radius: 11px !important;
        color: #a9c2e2 !important;
        background: #091d37 !important;
        box-shadow: none !important;
        font-size: .72rem !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        text-indent: 0 !important;
        overflow: visible !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.previous,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.next,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.previous:not(.page-item),
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.next:not(.page-item) {
        width: auto !important;
        min-width: 78px !important;
        padding: 0 14px !important;
        color: #dbeafe !important;
        font-size: .72rem !important;
        text-indent: 0 !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.previous::before,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.previous::after,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.next::before,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.next::after {
        content: none !important;
        display: none !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.current,
    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.current:hover {
        color: #fff !important;
        border-color: #4aa4ff !important;
        background: linear-gradient(135deg, #2f6ff2, #28b8f1) !important;
        box-shadow: 0 8px 18px rgba(47,128,255,.25) !important;
    }

    html body#page-top#page-top .corrective-ops-page .dataTables_paginate .paginate_button.disabled {
        display: inline-flex !important;
        opacity: .38 !important;
        color: #7f93b6 !important;
        cursor: not-allowed !important;
    }

    @media (max-width: 767px) {
        html body#page-top#page-top .corrective-ops-page .corrective-pages {
            width: 100% !important;
            margin-left: 0 !important;
        }

        html body#page-top#page-top .corrective-ops-page .dataTables_paginate,
        html body#page-top#page-top .corrective-ops-page .dataTables_paginate.paging_simple_numbers {
            justify-content: flex-start !important;
        }
    }
</style>

<section class="corrective-ops-page" aria-labelledby="corrective-page-title">
    <header class="corrective-hero">
        <div class="corrective-hero__copy">
            <div class="corrective-hero__icon" aria-hidden="true"><i class="fas fa-tools"></i></div>
            <div>
                <span class="corrective-eyebrow">Maintenance Operations</span>
                <h2 id="corrective-page-title">Corrective Maintenance</h2>
                <p>Monitor active faults, work progress and completed corrective jobs from one workspace.</p>
            </div>
        </div>
        <div class="corrective-live-card">
            <span class="corrective-live-card__label"><i></i> Live overview</span>
            <strong>Corrective queue monitor</strong>
            <small>Updates from the latest maintenance records</small>
        </div>
    </header>

    <div class="corrective-kpi-grid" aria-label="Corrective maintenance totals">
        <article class="corrective-kpi corrective-kpi--cyan">
            <span class="corrective-kpi__icon"><i class="fas fa-layer-group"></i></span>
            <div><span class="corrective-kpi__label">Total Jobs</span><strong id="corrective-kpi-total">0</strong><small>All corrective records</small></div>
        </article>
        <article class="corrective-kpi corrective-kpi--amber">
            <span class="corrective-kpi__icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div><span class="corrective-kpi__label">Requires Attention</span><strong id="corrective-kpi-maintenance">0</strong><small>Waiting for maintenance</small></div>
        </article>
        <article class="corrective-kpi corrective-kpi--violet">
            <span class="corrective-kpi__icon"><i class="fas fa-spinner"></i></span>
            <div><span class="corrective-kpi__label">In Progress</span><strong id="corrective-kpi-progress">0</strong><small>Work currently underway</small></div>
        </article>
        <article class="corrective-kpi corrective-kpi--green">
            <span class="corrective-kpi__icon"><i class="fas fa-check-circle"></i></span>
            <div><span class="corrective-kpi__label">Completed</span><strong id="corrective-kpi-complete">0</strong><small>Corrective jobs resolved</small></div>
        </article>
    </div>

    <div class="corrective-chart-grid">
        <article class="corrective-panel corrective-chart-card corrective-chart-card--cyan">
            <div class="corrective-panel__head">
                <div><span class="corrective-panel__kicker">Workload</span><h3>Status Overview</h3><p>Current corrective jobs by workflow stage.</p></div>
                <span class="corrective-panel__badge"><i class="fas fa-chart-pie"></i> Live status</span>
            </div>
            <div class="corrective-chart-content">
                <div class="corrective-chart-stage">
                    <canvas id="pie-chart-asset-all-status" aria-label="Corrective maintenance status chart"></canvas>
                    <div class="corrective-chart-center"><strong id="pie-chart-asset-total-all-status">0</strong><span>Open</span></div>
                </div>
                <div class="corrective-chart-legend">
                    <div><i class="corrective-dot corrective-dot--amber"></i><span>Requires Maintenance</span><strong id="corrective-legend-maintenance">0</strong></div>
                    <div><i class="corrective-dot corrective-dot--violet"></i><span>In Progress</span><strong id="corrective-legend-progress">0</strong></div>
                    <div><i class="corrective-dot corrective-dot--green"></i><span>Completed</span><strong id="corrective-legend-complete">0</strong></div>
                </div>
            </div>
        </article>

        <article class="corrective-panel corrective-chart-card corrective-chart-card--violet">
            <div class="corrective-panel__head">
                <div><span class="corrective-panel__kicker">Resolution</span><h3>Work Completion Mix</h3><p>Open workload compared with completed jobs.</p></div>
                <span class="corrective-panel__badge corrective-panel__badge--violet"><i class="fas fa-bolt"></i> Performance</span>
            </div>
            <div class="corrective-chart-content">
                <div class="corrective-chart-stage">
                    <canvas id="pie-chart-asset" aria-label="Corrective maintenance completion chart"></canvas>
                    <div class="corrective-chart-center"><strong id="pie-chart-asset-total">0</strong><span>Done</span></div>
                </div>
                <div class="corrective-chart-legend">
                    <div><i class="corrective-dot corrective-dot--blue"></i><span>Open Jobs</span><strong id="corrective-legend-open">0</strong></div>
                    <div><i class="corrective-dot corrective-dot--green"></i><span>Completed Jobs</span><strong id="corrective-legend-done">0</strong></div>
                    <div class="corrective-resolution-note"><i class="fas fa-info-circle"></i><span>Completion totals use the latest status for each ticket.</span></div>
                </div>
            </div>
        </article>
    </div>

    <div class="corrective-table-grid">
        <article class="corrective-panel corrective-table-card">
            <div class="corrective-panel__head">
                <div><span class="corrective-panel__kicker">Action Queue</span><h3>Active Maintenance Queue</h3><p>Jobs that still require attention or are in progress.</p></div>
                <span class="corrective-table-count"><strong id="corrective-active-count">0</strong> active</span>
            </div>
            <div class="corrective-table-scroll">
                <table class="table corrective-data-table" id="correctiveAllStatus" width="100%" cellspacing="0">
                    <thead><tr><th>Equipment Name</th><th>Update Date</th><th>Final Status</th><th>Remarks</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </article>

        <article class="corrective-panel corrective-table-card">
            <div class="corrective-panel__head">
                <div><span class="corrective-panel__kicker">Resolved Work</span><h3>Completed Corrective Jobs</h3><p>Assets with corrective work marked as complete.</p></div>
                <span class="corrective-table-count corrective-table-count--green"><strong id="corrective-complete-count">0</strong> completed</span>
            </div>
            <div class="corrective-table-scroll">
                <table class="table corrective-data-table" id="corrective" width="100%" cellspacing="0">
                    <thead><tr><th>Asset Type</th><th>Equipment Name</th><th>Location</th><th>Final Status</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </article>
    </div>

    <article class="corrective-panel corrective-matrix-card">
        <div class="corrective-panel__head">
            <div><span class="corrective-panel__kicker">Fault Analysis</span><h3>Corrective Fault Matrix</h3><p>Fault volume across each registered asset type.</p></div>
            <span class="corrective-table-count corrective-table-count--violet"><strong id="corrective-fault-count">0</strong> fault types</span>
        </div>
        <div class="corrective-table-scroll corrective-table-scroll--matrix">
            <table class="table corrective-data-table" id="assets" width="100%" cellspacing="0"><thead><tr></tr></thead><tbody></tbody></table>
        </div>
    </article>
</section>
