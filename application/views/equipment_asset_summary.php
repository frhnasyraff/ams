<style>
    /* Asset report table: keep controls fixed while only the wide records
       area scrolls horizontally. */
    body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-table-scroll {
        width: 100% !important;
        min-width: 0 !important;
        overflow: visible !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_wrapper {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scroll,
    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollHead,
    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollBody {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollHead {
        position: sticky !important;
        top: 98px !important;
        z-index: 20 !important;
        overflow: hidden !important;
        background: #06162e !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(54, 202, 255, .75) #06162d !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollHeadInner,
    body:has(.equipment-asset-report-page) .equipment-asset-report-page table.report-suite-table {
        min-width: 1500px !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-dt-top,
    body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-dt-bottom {
        width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-dt-bottom {
        padding-top: 13px !important;
        border-top: 1px solid rgba(54, 202, 255, .14) !important;
    }

    @media (max-width: 767px) {
        body:has(.equipment-asset-report-page) .equipment-asset-report-page .dataTables_scrollHead {
            top: 82px !important;
        }

        body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-dt-top,
        body:has(.equipment-asset-report-page) .equipment-asset-report-page .report-dt-bottom {
            align-items: stretch !important;
            flex-direction: column !important;
        }
    }
</style>

<div class="report-suite-page equipment-asset-report-page" data-report-tone="asset">
    <section class="report-suite-hero">
        <div class="report-suite-hero-icon"><i class="fas fa-layer-group"></i></div>
        <div class="report-suite-hero-copy">
            <span>Inventory intelligence</span>
            <h2>Asset Summary Report</h2>
            <p>A complete operational register covering asset identity, ownership, maintenance and replacement details.</p>
        </div>
        <div class="report-suite-live"><i class="fas fa-circle"></i> Live asset data</div>
    </section>

    <section class="report-suite-stats" aria-live="polite">
        <article><i class="fas fa-database"></i><div><span>Total Records</span><strong id="report-total-count">0</strong><small>Available in this report</small></div></article>
        <article><i class="far fa-check-square"></i><div><span>Selected</span><strong id="report-selected-count">0</strong><small>Ready for export</small></div></article>
        <article><i class="fas fa-file-export"></i><div><span>Export Format</span><strong id="report-export-format">Excel</strong><small>Choose from the toolbar</small></div></article>
    </section>

    <section class="report-suite-card">
        <header class="report-suite-card-heading">
            <div>
                <span>Asset register</span>
                <h3>Detailed Asset Records</h3>
                <p>Search, sort and select the records required for your report.</p>
            </div>
            <div class="report-export-toolbar">
                <label for="download_type_select">Export as</label>
                <select id="download_type_select" aria-label="Export format">
                    <option value="excel">Excel</option>
                </select>
                <button type="submit" form="downloadForm" id="downloadPdfBtn" class="report-download-button"><i class="fas fa-download"></i> Download</button>
            </div>
        </header>

        <form action="<?= site_url('Equipment_asset_summary_report/downloadRecord') ?>" id="downloadForm" method="POST">
            <input type="hidden" name="download_type" id="download-type" value="excel">
            <div class="report-table-scroll">
                <table class="table report-suite-table" id="asset_summary" cellspacing="0">
                    <thead>
                        <tr>
                            <th><button type="button" class="report-select-all" id="select_all_checkboxes"><i class="far fa-square"></i><span class="report-select-all-label">Select All</span></button></th>
                            <th>Asset Type</th>
                            <th>Registration Number</th>
                            <th>Location</th>
                            <th>Date Installed</th>
                            <th>Managed By</th>
                            <th>Manufacturer Name</th>
                            <th>Part Number</th>
                            <th>Status</th>
                            <th>Last Maintenance</th>
                            <th>Next Maintenance</th>
                            <th>Store Location</th>
                            <th>Replacement Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </form>
    </section>
</div>

<div class="modal fade report-suite-modal" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel"><i class="fas fa-list-ul"></i> Asset Item Details</h5>
                <button type="button" class="close hideEyeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="modal-body-content"></div>
            <div class="modal-footer"><button type="button" class="btn report-modal-close hideEyeModal" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>
