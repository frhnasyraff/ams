<?php
$summary = $this->input->get('summary') === 'components' ? 'components' : 'assets';
$currentYear = date('Y');
$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];
?>

<div class="report-suite-page mtbf-report-page" data-report-tone="mtbf">
    <section class="report-suite-hero">
        <div class="report-suite-hero-icon"><i class="fas fa-heartbeat"></i></div>
        <div class="report-suite-hero-copy">
            <span>Reliability analytics</span>
            <h2>Mean Time Between Failures</h2>
            <p>Measure reliability trends and open detailed failure intervals for every asset or component type.</p>
        </div>
        <div class="report-suite-live"><i class="fas fa-circle"></i> Reliability monitor</div>
    </section>

    <section class="report-suite-stats">
        <article><i class="fas fa-crosshairs"></i><div><span>Current Scope</span><strong id="mtbf-current-scope"><?= $summary === 'components' ? 'Components' : 'Assets' ?></strong><small>Switch using the tabs below</small></div></article>
        <article><i class="far fa-calendar-alt"></i><div><span>Reporting Period</span><strong id="mtbf-current-period">All Time</strong><small>Apply month or year filters</small></div></article>
        <article><i class="fas fa-search-plus"></i><div><span>Analysis</span><strong>Drill-down</strong><small>Select a type for details</small></div></article>
    </section>

    <nav class="report-scope-tabs" aria-label="MTBF report scope">
        <a class="<?= $summary === 'assets' ? 'active' : '' ?>" href="<?= site_url('Mean_time_between_failure_report?summary=assets') ?>"><i class="fas fa-cube"></i> Assets</a>
        <a class="<?= $summary === 'components' ? 'active' : '' ?>" href="<?= site_url('Mean_time_between_failure_report?summary=components') ?>"><i class="fas fa-cubes"></i> Components</a>
    </nav>

    <section class="report-suite-card mtbf-report-card">
        <?php if ($summary === 'assets'): ?>
            <header class="report-suite-card-heading mtbf-card-heading">
                <div><span>Reliability register</span><h3>Asset Type MTBF</h3><p>Select an asset type to view its individual breakdown history.</p></div>
                <div class="report-suite-filter-toolbar">
                    <div class="report-export-group">
                        <label for="asset_download_type_select">Export</label>
                        <select id="asset_download_type_select"><option value="pdf">PDF</option><option value="excel">Excel</option></select>
                        <button type="submit" form="downloadForm" class="report-download-button" title="Download report"><i class="fas fa-download"></i></button>
                    </div>
                    <div class="report-filter-group">
                        <label for="summary_year_assets">Period</label>
                        <select name="summary_year_assets" id="summary_year_assets" class="select-box">
                            <option value="">All years</option>
                            <?php for ($i = 0; $i < 5; $i++): $year = $currentYear - $i; ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="summary_month_assets" id="summary_month_assets" class="select-box">
                            <option value="">All months</option>
                            <?php foreach ($months as $key => $month): ?><option value="<?= $key ?>"><?= $month ?></option><?php endforeach; ?>
                        </select>
                        <button id="assets_filter_btn" type="button" class="report-filter-button"><i class="fas fa-search"></i> Apply</button>
                    </div>
                </div>
            </header>
            <form action="<?= site_url('Mean_time_between_failure_report/downloadReport') ?>" id="downloadForm" method="POST">
                <input type="hidden" name="download_type" id="download_type">
                <input type="hidden" name="year" id="hidden_year">
                <input type="hidden" name="month" id="hidden_month">
                <div class="report-table-scroll">
                    <table class="table report-suite-table report-suite-table-compact" id="asset_types" cellspacing="0">
                        <thead><tr><th>Asset Type</th><th>Average MTBF (Days &amp; Hours)</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </form>
        <?php else: ?>
            <header class="report-suite-card-heading mtbf-card-heading">
                <div><span>Reliability register</span><h3>Component Type MTBF</h3><p>Select a component type to view its individual breakdown history.</p></div>
                <div class="report-suite-filter-toolbar">
                    <div class="report-export-group">
                        <label for="components_download_type_select">Export</label>
                        <select id="components_download_type_select"><option value="pdf">PDF</option><option value="excel">Excel</option></select>
                        <button type="submit" form="downloadFormComponents" class="report-download-button" title="Download report"><i class="fas fa-download"></i></button>
                    </div>
                    <div class="report-filter-group">
                        <label for="summary_year_components">Period</label>
                        <select name="summary_year_components" id="summary_year_components" class="select-box">
                            <option value="">All years</option>
                            <?php for ($i = 0; $i < 5; $i++): $year = $currentYear - $i; ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="summary_month_components" id="summary_month_components" class="select-box">
                            <option value="">All months</option>
                            <?php foreach ($months as $key => $month): ?><option value="<?= $key ?>"><?= $month ?></option><?php endforeach; ?>
                        </select>
                        <button id="components_filter_btn" type="button" class="report-filter-button"><i class="fas fa-search"></i> Apply</button>
                    </div>
                </div>
            </header>
            <form action="<?= site_url('Mean_time_between_failure_report/downloadComponentReport') ?>" id="downloadFormComponents" method="POST">
                <input type="hidden" name="download_type" id="component_download_type">
                <input type="hidden" name="year" id="component_hidden_year">
                <input type="hidden" name="month" id="component_hidden_month">
                <div class="report-table-scroll">
                    <table class="table report-suite-table report-suite-table-compact" id="component_table" cellspacing="0">
                        <thead><tr><th>Component Type</th><th>Average MTBF (Days &amp; Hours)</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </form>
        <?php endif; ?>
    </section>
</div>

<div class="modal fade report-suite-modal" id="breakdownModal" tabindex="-1" role="dialog" aria-labelledby="breakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="breakdownModalLabel"><i class="fas fa-chart-line"></i> Individual Asset MTBF Breakdown</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body"><div class="report-table-scroll"><table id="breakdownTable" class="table report-suite-table" style="width:100%"><thead><tr><th>Asset Code</th><th>Asset Name</th><th>Type</th><th>Serviceable Date</th><th>Unserviceable Date</th><th>MTBF Days &amp; Hours</th></tr></thead><tbody></tbody></table></div></div>
    </div></div>
</div>

<div class="modal fade report-suite-modal" id="componentbreakdownModal" tabindex="-1" role="dialog" aria-labelledby="componentbreakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="componentbreakdownModalLabel"><i class="fas fa-chart-line"></i> Individual Component MTBF Breakdown</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body"><div class="report-table-scroll"><table id="componentbreakdownTable" class="table report-suite-table" style="width:100%"><thead><tr><th>Component Code</th><th>Component Name</th><th>Serviceable Date</th><th>Unserviceable Date</th><th>MTBF Days &amp; Hours</th></tr></thead><tbody></tbody></table></div></div>
    </div></div>
</div>
