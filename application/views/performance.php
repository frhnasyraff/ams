<?php
$summaryMode = (isset($_GET['summary']) && $_GET['summary'] === 'components') ? 'components' : 'assets';
$isComponents = $summaryMode === 'components';
$currentYear = date('Y');
$selectedYear = $this->input->post('year');
$selectedMonth = $this->input->post('month');
$months = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December',
];

$recordCount = $isComponents ? $components_summary_count : $assets_summary_count;
$modeLabel = $isComponents ? 'Components' : 'Assets';
$yearField = $isComponents ? 'summary_year_components' : 'summary_year_assets';
$monthField = $isComponents ? 'summary_month_components' : 'summary_month_assets';
$filterButton = $isComponents ? 'components_filter_btn' : 'assets_filter_btn';
$resetButton = $isComponents ? 'components_reset_btn' : 'assets_reset_btn';
$chartIds = $isComponents ? ['lineChartID4', 'lineChartID5', 'lineChartID6'] : ['lineChartID1', 'lineChartID2', 'lineChartID3'];
?>

<section id="orders-list" class="project-tab performance-dashboard-redesign" data-performance-mode="<?= $summaryMode ?>">
    <div class="performance-hero">
        <div class="performance-hero-main">
            <span class="performance-hero-icon"><i class="fa fa-line-chart"></i></span>
            <div>
                <span class="performance-eyebrow">Fleet intelligence</span>
                <h2>Performance Overview</h2>
                <p>Track serviceability, maintenance exposure and repair response from one clear workspace.</p>
            </div>
        </div>
        <div class="performance-live-card">
            <span><i class="fa fa-circle"></i> Live analytics</span>
            <strong><?= $modeLabel ?> performance</strong>
            <small id="performance_period_label">Current reporting view</small>
        </div>
    </div>

    <div class="performance-toolbar">
        <nav class="performance-mode-switch" id="nav-tab" aria-label="Performance category">
            <a class="nav-link <?= !$isComponents ? 'active' : '' ?>" href="?summary=assets">
                <i class="fa fa-truck"></i>
                <span>Assets</span>
                <span id="assets_summary_count" class="counter"><?= $assets_summary_count ?></span>
            </a>
            <a class="nav-link <?= $isComponents ? 'active' : '' ?>" href="?summary=components">
                <i class="fa fa-cubes"></i>
                <span>Components</span>
                <span id="components_summary_count" class="counter"><?= $components_summary_count ?></span>
            </a>
        </nav>

        <div class="performance-filter-group">
            <div class="performance-filter-copy">
                <span>Reporting period</span>
                <small>Choose a year or narrow it to one month</small>
            </div>
            <select name="<?= $yearField ?>" id="<?= $yearField ?>" class="select-box" aria-label="Select year">
                <option value="">All years</option>
                <?php for ($i = 0; $i < 5; $i++): $year = $currentYear - $i; ?>
                    <option value="<?= $year ?>" <?= ($selectedYear == $year) ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
            </select>
            <select name="<?= $monthField ?>" id="<?= $monthField ?>" class="select-box" aria-label="Select month">
                <option value="">All months</option>
                <?php foreach ($months as $key => $month): ?>
                    <option value="<?= $key ?>" <?= ($selectedMonth == $key) ? 'selected' : '' ?>><?= $month ?></option>
                <?php endforeach; ?>
            </select>
            <button id="<?= $filterButton ?>" class="performance-apply-btn" type="button">
                <i class="fa fa-filter"></i><span>Apply</span>
            </button>
            <button id="<?= $resetButton ?>" class="performance-reset-btn" type="button" aria-label="Reset filters" title="Reset filters">
                <i class="fa fa-refresh"></i>
            </button>
        </div>
    </div>

    <div class="performance-kpi-grid">
        <article class="performance-kpi-card kpi-blue">
            <span class="performance-kpi-icon"><i class="fa <?= $isComponents ? 'fa-cubes' : 'fa-truck' ?>"></i></span>
            <div><small>Total <?= $modeLabel ?></small><strong><?= $recordCount ?></strong><p>Records in this view</p></div>
        </article>
        <article class="performance-kpi-card kpi-green">
            <span class="performance-kpi-icon"><i class="fa fa-shield"></i></span>
            <div><small>Avg. Serviceability</small><strong id="performance_serviceability">—</strong><p>Operational availability</p></div>
        </article>
        <article class="performance-kpi-card kpi-coral">
            <span class="performance-kpi-icon"><i class="fa fa-wrench"></i></span>
            <div><small>Maintenance Rate</small><strong id="performance_maintenance">—</strong><p>Average monthly exposure</p></div>
        </article>
        <article class="performance-kpi-card kpi-amber">
            <span class="performance-kpi-icon"><i class="fa fa-clock-o"></i></span>
            <div><small>Repair Response</small><strong id="performance_repair">—</strong><p id="performance_repair_note">Average completion time</p></div>
        </article>
    </div>

    <div class="performance-chart-grid" id="nav-<?= $summaryMode ?>">
        <article class="performance-chart-card chart-primary">
            <header>
                <div>
                    <span class="performance-eyebrow">Availability</span>
                    <h3>Serviceability Trend</h3>
                    <p>Percentage of <?= strtolower($modeLabel) ?> ready for operation each month.</p>
                </div>
                <span class="performance-chart-badge badge-green"><i class="fa fa-arrow-up"></i> Operational health</span>
            </header>
            <div class="chart-container chart-container-wide">
                <canvas id="<?= $chartIds[0] ?>"></canvas>
            </div>
        </article>

        <article class="performance-chart-card chart-maintenance">
            <header>
                <div>
                    <span class="performance-eyebrow">Workload</span>
                    <h3>Maintenance Exposure</h3>
                    <p>Monthly share requiring maintenance attention.</p>
                </div>
                <span class="performance-chart-badge badge-coral"><i class="fa fa-wrench"></i> Maintenance</span>
            </header>
            <div class="chart-container">
                <canvas id="<?= $chartIds[1] ?>"></canvas>
            </div>
        </article>

        <article class="performance-chart-card chart-repair">
            <header>
                <div>
                    <span class="performance-eyebrow">Resolution</span>
                    <h3>Repair Turnaround</h3>
                    <p>Days and hours required to complete repair work.</p>
                </div>
                <span class="performance-chart-badge badge-amber"><i class="fa fa-clock-o"></i> Response time</span>
            </header>
            <div class="chart-container">
                <canvas id="<?= $chartIds[2] ?>"></canvas>
            </div>
        </article>
    </div>
</section>
