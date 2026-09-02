<?php
$assetStateMap = [];
foreach ($stateCount as $row) {
    $assetStateMap[(string) $row->state_name] = (int) $row->total_assets;
}
$itemStateMap = [];
foreach ($itemsStateCount as $row) {
    $itemStateMap[(string) $row->state_name] = (int) $row->total_items;
}

$assetLabels = [];
$assetValues = [];
foreach ($states as $state) {
    $assetLabels[] = $state->state_name;
    $assetValues[] = $assetStateMap[$state->state_name] ?? 0;
}
$assetLabels[] = 'N/A';
$assetValues[] = (int) ($unassignedAssets ?? 0);

$itemLabels = [];
$itemValues = [];
foreach ($states as $state) {
    $itemLabels[] = $state->state_name;
    $itemValues[] = $itemStateMap[$state->state_name] ?? 0;
}

$assignedAssets = max(0, (int) ($totalAssetsAll ?? 0) - (int) ($unassignedAssets ?? 0));
$assignedPercent = (int) ($totalAssetsAll > 0 ? round(($assignedAssets / $totalAssetsAll) * 100) : 0);
$unassignedPercent = max(0, 100 - $assignedPercent);
?>

<style>
html body:has(.location-summary-redesign) .location-summary-redesign .panel-title-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 14px !important;
    margin-bottom: 16px !important;
}

html body:has(.location-summary-redesign) .location-summary-redesign .panel-title-copy {
    display: inline-flex !important;
    align-items: center !important;
    gap: 9px !important;
}

html body:has(.location-summary-redesign) .location-summary-redesign .location-state-filter {
    min-width: 220px !important;
    height: 42px !important;
    margin: 0 !important;
    padding: 0 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 9px !important;
    border: 1px solid rgba(57, 205, 253, .36) !important;
    border-radius: 12px !important;
    background: #071a32 !important;
    color: #38cdfb !important;
}

html body:has(.location-summary-redesign) .location-summary-redesign .location-state-filter select {
    width: 100% !important;
    height: 40px !important;
    padding: 0 26px 0 0 !important;
    border: 0 !important;
    outline: 0 !important;
    color: #eaf5ff !important;
    background: #071a32 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
}

html body:has(.location-summary-redesign) .location-summary-redesign .distribution-panel .location-chart-wrap {
    width: 100% !important;
    padding: 16px 18px 12px !important;
    transition: height .2s ease !important;
}

@media (max-width: 620px) {
    html body:has(.location-summary-redesign) .location-summary-redesign .location-state-filter {
        width: 100% !important;
        min-width: 0 !important;
    }
}
</style>

<section class="location-summary-redesign">
    <div class="location-hero-card">
        <div class="location-hero-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <h2>Location Summary</h2>
            <p>Overview of assets by location and state coverage</p>
        </div>
    </div>

    <nav class="location-tabs" id="nav-tab" role="tablist">
        <a class="active" id="assets-tab" data-toggle="tab" href="#assets" role="tab" aria-controls="assets" aria-selected="true"><i class="fas fa-layer-group"></i> Assets</a>
        <a id="items-tab" data-toggle="tab" href="#items" role="tab" aria-controls="items" aria-selected="false"><i class="fas fa-cubes"></i> Items</a>
    </nav>

    <div class="tab-content location-tab-content">
        <div class="tab-pane fade show active" id="assets" role="tabpanel" aria-labelledby="assets-tab">
            <div class="location-kpi-grid">
                <button class="location-kpi-card" id="total-btn" type="button">
                    <span class="kpi-icon blue"><i class="fas fa-box-open"></i></span>
                    <small>Total Assets</small>
                    <strong><?= (int) ($totalAssetsAll ?? 0) ?></strong>
                </button>
                <button class="location-kpi-card" type="button" value="" id="active-location-card">
                    <span class="kpi-icon green"><i class="fas fa-map-pin"></i></span>
                    <small>Active Locations</small>
                    <strong><?= (int) ($activeLocations ?? 0) ?></strong>
                </button>
                <button class="location-kpi-card" type="button" value="N/A" id="filterTab">
                    <span class="kpi-icon purple"><i class="fas fa-question"></i></span>
                    <small>Unassigned</small>
                    <strong><?= (int) ($unassignedAssets ?? 0) ?></strong>
                </button>
                <button class="location-kpi-card" type="button" value="" id="states-with-assets-card">
                    <span class="kpi-icon amber"><i class="fas fa-landmark"></i></span>
                    <small>States with Assets</small>
                    <strong><?= (int) ($statesWithAssets ?? 0) ?></strong>
                </button>
            </div>

            <div class="location-analytics-grid">
                <div class="location-panel distribution-panel">
                    <div class="panel-title-row">
                        <div class="panel-title-copy"><h3>Asset Distribution by State</h3><i class="fas fa-info-circle"></i></div>
                        <label class="location-state-filter" for="asset-state-filter">
                            <i class="fas fa-filter"></i>
                            <select id="asset-state-filter" data-chart-type="asset" aria-label="Filter assets by state">
                                <option value="">All States</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="location-chart-wrap"><canvas id="assetLocationBarChart"></canvas></div>
                    <div class="location-insight"><i class="fas fa-lightbulb"></i><span>Bar chart shows every state, including unassigned assets.</span></div>
                </div>
                <div class="location-panel coverage-panel">
                    <div class="panel-title-row"><h3>Coverage Overview</h3><i class="fas fa-info-circle"></i></div>
                    <div class="coverage-content">
                        <div class="donut-wrap"><canvas id="assetCoverageDonut"></canvas><div class="donut-center"><strong><?= $unassignedPercent ?>%</strong><small>Unassigned</small></div></div>
                        <div class="coverage-list">
                            <div><span class="dot purple"></span><em>Unassigned / N/A</em><strong><?= (int) ($unassignedAssets ?? 0) ?> Assets</strong><b><?= $unassignedPercent ?>%</b></div>
                            <div><span class="dot blue"></span><em>Assigned</em><strong><?= $assignedAssets ?> Assets</strong><b><?= $assignedPercent ?>%</b></div>
                        </div>
                    </div>
                    <div class="location-insight"><i class="fas fa-chart-line"></i><span><?= $assignedPercent ?>% assets are assigned to active locations.</span></div>
                </div>
            </div>

            <div class="location-table-panel location-register-card">
                <div class="location-table-heading">
                    <div>
                        <span>Asset register</span>
                        <h3>Assets by Location</h3>
                    </div>
                    <p>Search, sort and review every asset assigned to a location.</p>
                </div>
                <div class="location-table-responsive">
                <table class="table location-data-table <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>" id="locations_tabel" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Asset Type</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Vendor Part</th>
                            <th>Manufacturing Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
            <div class="location-kpi-grid compact">
                <button class="location-kpi-card" id="item-total-btn" type="button">
                    <span class="kpi-icon blue"><i class="fas fa-cubes"></i></span>
                    <small>Total Items</small>
                    <strong><?= (int) ($totalItemsCount ?? 0) ?></strong>
                </button>
                <?php foreach (array_slice($states, 0, 3) as $state): ?>
                    <button class="location-kpi-card" value="<?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?>" id="filterTab" type="button">
                        <span class="kpi-icon green"><i class="fas fa-map-marker-alt"></i></span>
                        <small><?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?></small>
                        <strong><?= (int) ($itemStateMap[$state->state_name] ?? 0) ?></strong>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="location-panel distribution-panel item-panel">
                <div class="panel-title-row">
                    <div class="panel-title-copy"><h3>Item Distribution by State</h3><i class="fas fa-info-circle"></i></div>
                    <label class="location-state-filter" for="item-state-filter">
                        <i class="fas fa-filter"></i>
                        <select id="item-state-filter" data-chart-type="item" aria-label="Filter items by state">
                            <option value="">All States</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($state->state_name, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="location-chart-wrap"><canvas id="itemLocationBarChart"></canvas></div>
            </div>
            <div class="location-table-panel location-register-card">
                <div class="location-table-heading">
                    <div>
                        <span>Item register</span>
                        <h3>Items by Location</h3>
                    </div>
                    <p>View every item together with its parent asset and current status.</p>
                </div>
                <div class="location-table-responsive">
                <table class="table location-data-table <?= ($this->user_model->has_perm("edit_equipment_types") ? "" : "read-only"); ?>" id="item_locations_tabel" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Item Type</th>
                            <th>Location</th>
                            <th>Asset Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
window.locationSummaryPayload = {
    assetLabels: <?= json_encode($assetLabels) ?>,
    assetValues: <?= json_encode($assetValues) ?>,
    itemLabels: <?= json_encode($itemLabels) ?>,
    itemValues: <?= json_encode($itemValues) ?>,
    assignedAssets: <?= (int) $assignedAssets ?>,
    unassignedAssets: <?= (int) ($unassignedAssets ?? 0) ?>
};
</script>

