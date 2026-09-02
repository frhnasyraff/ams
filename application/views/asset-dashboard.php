<style>
    .use::after{
        background-color: <?= $in_use_color ?>;
    }

    .maintenance::after{
        background-color: <?= $maintenance_color ?>;
    }

    .faulty::after{
        background-color: <?= $faulty_type_color ?>;
    }

    .location::after{
        background-color: <?= $location_color ?>;
    }

    /* Common Dashboard Styling */
    .dashboard-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .dashboard-map {
        height: 500px;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        height: 500px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .dashboard-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        height: 150px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #eaeaea;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .dashboard-card a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .dashboard-card .name {
        font-size: 14px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-card .total {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    /* Color indicators */
    .folder::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        border-radius: 8px 0 0 8px;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .dashboard-cards {
            height: auto;
            overflow-y: visible;
        }
        
        .dashboard-map {
            height: 400px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-cards {
            grid-template-columns: 1fr;
        }
        
        .dashboard-card {
            height: 120px;
        }
    }


</style>

<div class="asset-general-dashboard redesigned-general-dashboard">
    <div class="general-dashboard-tabs-wrap">
        <nav class="general-tabs-shell">
            <div class="nav nav-tabs general-dashboard-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active" href="#"><i class="fas fa-truck"></i> Assets Dashboard</a>
                <!-- Legacy root URL breaks when app runs in /assets_IT-usman/: <a class="nav-item nav-link" href="/item_dashboard/index">Item Dashboard</a> -->
                <a class="nav-item nav-link" href="<?= base_url('item_dashboard/index') ?>"><i class="fas fa-cogs"></i> Item Dashboard</a>
            </div>
        </nav>
    </div>

    <div class="row general-dashboard-grid align-items-stretch">
        <div class="col-xl-7 col-lg-7 order-lg-0 order-md-1">
            <section class="general-map-card">
                <div class="general-section-title">
                    <span><i class="fas fa-map-marked-alt"></i></span>
                    <div><strong>Asset Location Map</strong><small>Live asset positions and location coverage</small></div>
                </div>
                <div class="general-map-frame">
                    <div id="map" style="height: 420px; width: 100%"></div>
                </div>
            </section>
        </div>

        <div class="col-xl-5 col-lg-5 order-lg-1 order-md-0">
            <section class="general-kpi-panel">
                <div class="general-section-title">
                    <span><i class="fas fa-chart-pie"></i></span>
                    <div><strong>Asset Overview</strong><small>Current asset status at a glance</small></div>
                </div>

                <div class="general-kpi-grid">
                    <a href="<?= site_url('assets') ?>" class="folder total general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-cubes"></i></span>
                        <span class="content"><span class="name">Total Asset</span><strong class="total"><?= $total_assets ?></strong></span>
                    </a>

                    <a href="<?= site_url('assets?filter=' . urlencode("SERVICEABLE")) ?>" class="folder use general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-check-circle"></i></span>
                        <span class="content"><span class="name">Serviceable</span><strong class="total"><?= $assets_seviceable ?></strong></span>
                    </a>

                    <a href="<?= site_url('assets?filter=' . urlencode("UNSERVICEABLE")) ?>" class="folder faulty general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-times-circle"></i></span>
                        <span class="content"><span class="name">Unserviceable</span><strong class="total"><?= $assets_unseviceable ?></strong></span>
                    </a>

                    <a href="<?= site_url('assets?filter=' . urlencode("MAINTENANCE")) ?>" class="folder maintenance general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-tools"></i></span>
                        <span class="content"><span class="name">Maintenance</span><strong class="total"><?= $assets_in_maintenance ?></strong></span>
                    </a>

                    <a href="<?= site_url('Location_summary') ?>" class="folder location general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span class="content"><span class="name">Total Locations</span><strong class="total"><?= $total_locations ?></strong></span>
                    </a>

                    <a href="<?= site_url('assets?filter=' . urlencode("STORE")) ?>" class="folder store general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-warehouse"></i></span>
                        <span class="content"><span class="name">Total Store</span><strong class="total"><?= $assets_in_store ?></strong></span>
                    </a>

                    <!-- Available KPI kept commented as requested previously; not removed.
                    <a href="<?= site_url('assets?filter=' . urlencode("AVAILABLE")) ?>" class="folder available general-kpi-card text-decoration-none">
                        <span class="kpi-icon"><i class="fas fa-box-open"></i></span>
                        <span class="content"><span class="name">Total Available</span><strong class="total"><?= $assets_in_available ?></strong></span>
                    </a>
                    -->
                </div>
            </section>
        </div>
    </div>
</div>
