<?php
$orderCount = (int) $this->db
    ->query("SELECT COUNT(*) AS total FROM orders WHERE active=1 AND COALESCE(status, '') <> 'Completed'")
    ->row()
    ->total;

$orderRows = $this->db->query("SELECT o.created_at, o.order_number, o.status,
        c.company_name, l.name AS location_name
    FROM orders o
    LEFT JOIN companies c ON c.company_id = o.company_id
    LEFT JOIN equipments_asset a ON a.equipment_id = o.asset_id
    LEFT JOIN locations l ON l.id = a.location_id
    WHERE o.active=1 AND COALESCE(o.status, '') <> 'Completed'
    ORDER BY o.created_at DESC, o.id DESC LIMIT 5")->result_array();

$truckRows = $this->db
    ->query("SELECT * FROM equipments WHERE active=1 ORDER BY t_updated DESC, equipment_id DESC LIMIT 5")
    ->result_array();

$workerRows = $this->db->query("SELECT b.worker_name, b.ic_number, a.vh_time_start, c.equipment_registration
    FROM vehicle_history a
    JOIN workers b ON a.driver_id = b.worker_id
    JOIN equipments c ON c.equipment_id = a.equipment_id
    ORDER BY a.vh_id DESC LIMIT 5")->result_array();

$assetRows = $this->db->query("SELECT a.*, b.equipment_type_short_code, d.worker_name AS customer_name, c.*
    FROM equipments_asset a
    JOIN equipment_types_asset b ON a.equipment_type = b.equipment_type_id
    JOIN vehicle_history_asset c ON c.equipment_id = a.equipment_id
    JOIN workers d ON d.worker_id = c.driver_id
    WHERE a.active=1 AND b.active=1 AND d.active=1
    ORDER BY c.vh_id DESC LIMIT 5")->result_array();
?>

<div class="operations-dashboard-page">
    <section class="operations-hero">
        <div class="operations-hero-icon"><i class="fas fa-chart-line"></i></div>
        <div class="operations-hero-copy">
            <span>Live operations overview</span>
            <h2>Operations Command Centre</h2>
            <p>Track active orders, fleet deployment, drivers on duty and assets currently in use.</p>
        </div>
        <div class="operations-hero-meta">
            <span><i class="fas fa-circle"></i> Live dashboard</span>
            <strong><?= date('l, d F Y') ?></strong>
        </div>
    </section>

    <section class="operations-kpi-grid" aria-label="Operational summary">
        <article class="operations-kpi operations-kpi-orders">
            <div class="operations-kpi-icon"><i class="fas fa-clipboard-list"></i></div>
            <div><span>Active Orders</span><strong id="ops-kpi-orders"><?= $orderCount ?></strong><small>Current order queue</small></div>
        </article>
        <article class="operations-kpi operations-kpi-trucks">
            <div class="operations-kpi-icon"><i class="fas fa-truck-moving"></i></div>
            <div><span>Trucks Deployed</span><strong id="ops-kpi-trucks"><?= count($truckRows) ?></strong><small>Latest fleet activity</small></div>
        </article>
        <article class="operations-kpi operations-kpi-drivers">
            <div class="operations-kpi-icon"><i class="fas fa-id-card-alt"></i></div>
            <div><span>Drivers on Duty</span><strong id="ops-kpi-drivers"><?= count($workerRows) ?></strong><small>Recent deployments</small></div>
        </article>
        <article class="operations-kpi operations-kpi-assets">
            <div class="operations-kpi-icon"><i class="fas fa-cubes"></i></div>
            <div><span>Assets in Use</span><strong id="ops-kpi-assets"><?= count($assetRows) ?></strong><small>Currently assigned</small></div>
        </article>
    </section>

    <section class="operations-panel-grid">
        <article class="operations-panel-card" data-tone="orders">
            <header class="operations-panel-heading">
                <div class="operations-panel-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <span>Order activity</span>
                    <h3>Active Order List</h3>
                </div>
                <strong class="operations-count-pill" id="ops-count-orders"><?= $orderCount ?></strong>
            </header>
            <div class="operations-table-shell">
                <table class="table operations-table" id="order_list2" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order Number</th>
                            <th>Client</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderRows as $order): ?>
                            <tr>
                                <td><span class="operations-date-cell"><i class="far fa-calendar-alt"></i><?= htmlspecialchars(date('d M Y', strtotime((string) $order['created_at'])), ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-primary-cell"><i class="fas fa-file-alt"></i><?= htmlspecialchars((string) $order['order_number'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><?= htmlspecialchars((string) ($order['company_name'] ?: 'Internal Operations'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="operations-vehicle-cell"><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars((string) ($order['location_name'] ?: 'Unassigned'), ENT_QUOTES, 'UTF-8') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="operations-panel-card" data-tone="trucks">
            <header class="operations-panel-heading">
                <div class="operations-panel-icon"><i class="fas fa-truck-moving"></i></div>
                <div>
                    <span>Fleet movement</span>
                    <h3>Trucks Deployed</h3>
                </div>
                <strong class="operations-count-pill" id="ops-count-trucks"><?= count($truckRows) ?></strong>
            </header>
            <div class="operations-table-shell">
                <table class="table operations-table" id="trucks_deployed2" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Truck Number</th>
                            <th>Service Date</th>
                            <th>Mileage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($truckRows as $truck): ?>
                            <tr>
                                <td><span class="operations-primary-cell"><i class="fas fa-truck"></i><?= htmlspecialchars((string) $truck['equipment_registration'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-date-cell"><i class="far fa-calendar-alt"></i><?= htmlspecialchars((string) $truck['next_service_date'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-metric-chip"><?= number_format((float) $truck['next_service_mileage'], 2) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="operations-panel-card" data-tone="drivers">
            <header class="operations-panel-heading">
                <div class="operations-panel-icon"><i class="fas fa-user-clock"></i></div>
                <div>
                    <span>Workforce status</span>
                    <h3>Drivers on Duty</h3>
                </div>
                <strong class="operations-count-pill" id="ops-count-drivers"><?= count($workerRows) ?></strong>
            </header>
            <div class="operations-table-shell">
                <table class="table operations-table" id="worker_deployed2" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>IC / Passport</th>
                            <th>Truck Number</th>
                            <th>Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workerRows as $worker): ?>
                            <tr>
                                <td><span class="operations-primary-cell"><i class="fas fa-user"></i><?= htmlspecialchars((string) $worker['worker_name'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-id-chip"><?= htmlspecialchars((string) $worker['ic_number'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-vehicle-cell"><i class="fas fa-truck"></i><?= htmlspecialchars((string) $worker['equipment_registration'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-time-chip"><i class="far fa-clock"></i><?= htmlspecialchars((string) $worker['vh_time_start'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="operations-panel-card" data-tone="assets">
            <header class="operations-panel-heading">
                <div class="operations-panel-icon"><i class="fas fa-cubes"></i></div>
                <div>
                    <span>Asset movement</span>
                    <h3>Assets in Use</h3>
                </div>
                <strong class="operations-count-pill" id="ops-count-assets"><?= count($assetRows) ?></strong>
            </header>
            <div class="operations-table-shell">
                <table class="table operations-table" id="asset_in_use_m" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Type</th>
                            <th>Locations</th>
                            <th>Driver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assetRows as $asset): ?>
                            <tr>
                                <td><span class="operations-primary-cell"><i class="fas fa-cube"></i><?= htmlspecialchars((string) $asset['equipment_registration'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="operations-type-chip"><?= htmlspecialchars((string) $asset['equipment_type_short_code'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td>
                                    <span class="operations-route-links">
                                        <?php if (!empty(trim((string) ($asset['vh_location_start'] ?? '')))): ?>
                                            <a title="<?= htmlspecialchars((string) $asset['vh_location_start'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="operations-route-link is-start" href="https://maps.google.com/?q=<?= rawurlencode((string) $asset['vh_location_start']) ?>"><i class="fas fa-map-marker-alt"></i> Start</a>
                                        <?php else: ?>
                                            <span class="operations-route-empty">No start</span>
                                        <?php endif; ?>
                                        <?php if (!empty(trim((string) ($asset['vh_location_end'] ?? '')))): ?>
                                            <a title="<?= htmlspecialchars((string) $asset['vh_location_end'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="operations-route-link is-end" href="https://maps.google.com/?q=<?= rawurlencode((string) $asset['vh_location_end']) ?>"><i class="fas fa-flag-checkered"></i> End</a>
                                        <?php else: ?>
                                            <span class="operations-route-empty">No end</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><span class="operations-driver-cell"><i class="far fa-user-circle"></i><?= htmlspecialchars((string) $asset['customer_name'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</div>
