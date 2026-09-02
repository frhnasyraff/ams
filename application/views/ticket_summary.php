<div class="report-suite-page" data-report-tone="ticket">
    <section class="report-suite-hero">
        <div class="report-suite-hero-icon"><i class="fas fa-ticket-alt"></i></div>
        <div class="report-suite-hero-copy">
            <span>Service intelligence</span>
            <h2>Ticket Summary Report</h2>
            <p>Review ticket volume, severity, maintenance progress and completion status in one register.</p>
        </div>
        <div class="report-suite-live"><i class="fas fa-circle"></i> Live ticket data</div>
    </section>

    <section class="report-suite-stats" aria-live="polite">
        <article><i class="fas fa-ticket-alt"></i><div><span>Total Tickets</span><strong id="report-total-count">0</strong><small>Available in this report</small></div></article>
        <article><i class="far fa-check-square"></i><div><span>Selected</span><strong id="report-selected-count">0</strong><small>Ready for export</small></div></article>
        <article><i class="fas fa-file-export"></i><div><span>Export Format</span><strong id="report-export-format">Excel</strong><small>Choose from the toolbar</small></div></article>
    </section>

    <section class="report-suite-card">
        <header class="report-suite-card-heading">
            <div>
                <span>Ticket register</span>
                <h3>Maintenance Ticket Records</h3>
                <p>Use search and sorting to review individual ticket progress.</p>
            </div>
            <div class="report-export-toolbar">
                <label for="download_type_select">Export as</label>
                <select id="download_type_select" aria-label="Export format"><option value="excel">Excel</option></select>
                <button type="submit" form="downloadForm" id="downloadPdfBtn" class="report-download-button"><i class="fas fa-download"></i> Download</button>
            </div>
        </header>

        <form action="<?= site_url('Ticket_summary_report/downloadRecord') ?>" id="downloadForm" method="POST">
            <input type="hidden" name="download_type" id="download-type" value="excel">
            <div class="report-table-scroll">
                <table class="table report-suite-table" id="asset_summary" cellspacing="0">
                    <thead>
                        <tr>
                            <th><button type="button" class="report-select-all" id="select_all_checkboxes"><i class="far fa-square"></i><span class="report-select-all-label">Select All</span></button></th>
                            <th>Ticket Date</th>
                            <th>Ticket Number</th>
                            <th>Severity</th>
                            <th>Asset Type</th>
                            <th>Registration Number</th>
                            <th>Location</th>
                            <th>Managed By</th>
                            <th>Manufacturer</th>
                            <th>Part Number</th>
                            <th>Maintenance Type</th>
                            <th>Task Done</th>
                            <th>Completion Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </form>
    </section>
</div>

<div class="modal fade report-suite-modal" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="equipmentModalLabel"><i class="fas fa-list-ul"></i> Ticket Item Details</h5><button type="button" class="close hideEyeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body" id="modal-body-content"></div>
        <div class="modal-footer"><button type="button" class="btn report-modal-close hideEyeModal" data-dismiss="modal">Close</button></div>
    </div></div>
</div>
