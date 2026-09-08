<?php
$assetCards = [
    ['Total assets', $totalAssets ?? 0, 'assets', 'cube'],
    ['Locations', $totalLocations ?? 0, 'Location_summary', 'map-marker-alt'],
    ['Serviceable', $totalAssetsServiceable ?? 0, 'assets?filter=SERVICEABLE', 'check-circle'],
    ['Unserviceable', $UnServiceable_assets ?? 0, 'assets?filter=UNSERVICEABLE', 'exclamation-circle'],
    ['In maintenance', $totalAssetsInMaintenance ?? 0, 'assets?filter=MAINTENANCE', 'tools'],
];
$componentCards = [
    ['Total components', $total_items ?? 0, 'items'],
    ['In store', $storelocationItemCount ?? 0, 'items?filter=STORE'],
    ['Serviceable', $ServiceableCount ?? 0, 'items?filter=SERVICEABLE'],
    ['Unserviceable', $UnserviceableCount ?? 0, 'items?filter=UNSERVICEABLE'],
    ['In maintenance', $MaintinenceItemCount ?? 0, 'items?filter=MAINTENANCE'],
];
$summaryCharts = [
    ['Asset quantity', 'By asset type', 'pie-chart-quantity', 'pie-chart-asset-quantity', 'assets-quantity', 'cube'],
    ['Assets by location', 'Current assignments', 'pie-chart-location', 'pie-chart-asset-location', 'breakdown-list-location', 'map-marker-alt'],
    ['Serviceable assets', 'Ready for operation', 'pie-chart-asset', 'pie-chart-asset-total', 'breakdown-list-asset-summary', 'shield-alt'],
    ['Unserviceable assets', 'Requiring attention', 'pie-chart-faulty', 'pie-chart-asset-faulty', 'breakdown-list-faulty', 'exclamation-triangle'],
    ['Maintenance activity', 'Recorded maintenance', 'pie-chart-maintenance', 'pie-chart-asset-maintenance', 'breakdown-list-maintenance', 'tools'],
];
?>
<main class="ams-order-summary">
    <header class="ams-summary-heading">
        <div><span class="ams-eyebrow">Overview</span><h1>Asset Summary</h1><p>Asset availability, maintenance and locations in one place.</p></div>
        <a class="ams-maintenance-link" href="<?= site_url('Assets_Item_maintenance?filter=corrective') ?>">
            <i class="fas fa-tools" aria-hidden="true"></i>
            <span>Maintenance alerts <strong><?= isset($asset_maintenanceAlertMessage) ? (int) $asset_maintenanceAlertMessage : '—' ?></strong></span>
            <i class="fas fa-arrow-right" aria-hidden="true"></i>
        </a>
    </header>
    <section class="ams-summary-kpis" aria-label="Asset totals">
        <?php foreach ($assetCards as $index => $card): ?>
            <a class="ams-summary-kpi" href="<?= site_url($card[2]) ?>" <?= $index === 4 ? 'id="maintenance-box"' : '' ?>>
                <i class="fas fa-<?= $card[3] ?>" aria-hidden="true"></i><div><span><?= $card[0] ?></span><h2><?= (int) $card[1] ?></h2></div>
            </a>
        <?php endforeach; ?>
    </section>
    <details class="ams-component-overview">
        <summary>Component overview</summary>
        <div class="ams-summary-kpis">
            <?php foreach ($componentCards as $card): ?>
                <a class="ams-summary-kpi" href="<?= site_url($card[2]) ?>"><div><span><?= $card[0] ?></span><h2><?= (int) $card[1] ?></h2></div></a>
            <?php endforeach; ?>
        </div>
    </details>
    <section class="ams-summary-charts" aria-label="Asset breakdowns">
        <?php foreach ($summaryCharts as $chart): ?>
            <article class="ams-summary-card">
                <header><span class="ams-summary-icon"><i class="fas fa-<?= $chart[5] ?>" aria-hidden="true"></i></span><div><h2><?= $chart[0] ?></h2><p><?= $chart[1] ?></p></div></header>
                <div class="ams-summary-chart-body">
                    <div class="ams-summary-chart"><canvas id="<?= $chart[2] ?>" aria-label="<?= $chart[0] ?>" role="img"></canvas><div class="ams-donut-total"><p id="<?= $chart[3] ?>">—</p></div></div>
                    <div id="<?= $chart[4] ?>" class="ams-summary-breakdown" aria-live="polite"><p>Loading breakdown…</p></div>
                </div>
            </article>
        <?php endforeach; ?>
        <article class="ams-summary-card ams-fleet-card">
            <header><span class="ams-summary-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span><div><h2>Fleet insights</h2><p>Current asset readiness</p></div></header>
            <div class="ams-fleet-metrics">
                <div><span>Total assets</span><strong><?= (int) ($totalAssets ?? 0) ?></strong></div>
                <div><span>Serviceable</span><strong><?= ($totalAssets ?? 0) > 0 ? round(($totalAssetsServiceable / $totalAssets) * 100) : 0 ?>%</strong></div>
                <div><span>Locations</span><strong><?= (int) ($totalLocations ?? 0) ?></strong></div>
                <div><span>In maintenance</span><strong><?= (int) ($totalAssetsInMaintenance ?? 0) ?></strong></div>
            </div>
            <p class="ams-fleet-note">Based on registered asset statuses. Review maintenance records for individual schedules.</p>
            <div class="d-none" aria-hidden="true"><canvas id="pie-chart-store-summary"></canvas><p id="pie-chart-store-summary-total"></p><div id="breakdown-list-store-summary"></div></div>
        </article>
    </section>
    <section class="ams-summary-bottom" aria-label="Asset records and map">
        <article class="ams-summary-card ams-summary-records"><header><div><h2>Asset records</h2><p>Search and review current assignments.</p></div></header>
            <div class="ams-summary-table-wrap"><table class="table" id="home" width="100%" cellspacing="0"><thead><tr><th>System Name</th><th>Location</th><th>Status</th></tr></thead><tbody></tbody></table></div>
        </article>
        <article class="ams-summary-card ams-summary-map"><header><div><h2>Asset locations</h2><p>Select a marker to view its asset.</p></div></header>
            <div id="map"></div>
            <div class="quake-info"><div><strong>Location:</strong> <span id="loc"></span></div><div><strong>Asset Type:</strong> <span id="asset_type"></span></div><div><strong>Asset Name:</strong> <span id="asset_name"></span></div><div><strong>Asset Number:</strong> <span id="asset_num"></span></div></div>
        </article>
    </section>
</main>
