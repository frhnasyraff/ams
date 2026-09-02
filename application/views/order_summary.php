<div class="row">
    <div class="col-md-12">
        <div class="row mb-3">
            <!-- <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_calibration/index') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $alertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Asset Calibration</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $alertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div>

            <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_calibration/index') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $itemalertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Item Calibration</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $itemalertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div> -->

            <div class="col-lg-4 col-md-6 mb-2">

                <a href="<?= site_url('Assets_Item_maintenance?filter=corrective') ?>" class="d-block text-decoration-none">
                    <div class="alert-box <?= $asset_maintenanceAlertMessage > 0 ? 'red' : 'green' ?>">
                        <div class="left">
                            <h2>Asset Maintenance</h2>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="right">
                            <h6 class="counter"><?= $asset_maintenanceAlertMessage ?></h6>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- <?php if (!empty($item_maintenanceAlertMessage)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <span class="fas fa-exclamation-triangle mr-4"></span>
                <div>
                    <?php echo $item_maintenanceAlertMessage; ?>
                </div>
            </div>
        <?php endif; ?> -->

    </div>

    <div class="col-lg-12">

        <div class="row mb-3 justify-content-around">
            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('assets') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>Asset Quantity</h4>
                        <h2><?= $totalAssets ?></h2>
                       
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('Location_summary') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Location Quantity</h4>
                        <h2><?= $totalLocations ?></h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
            <a href="<?= site_url('assets?filter=' . urlencode("SERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>Serviceable</h4>
                        <h2><?= $totalAssetsServiceable ?></h2>
                        <!-- <div class="percentage-changes">
                            <span class="up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                                90%
                            </span>
                            <p class="white-line">|</p>
                            <span class="down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-down">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                                10%
                            </span>
                        </div> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
            <a href="<?= site_url('assets?filter=' . urlencode("UNSERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue" id="faulty-box">
                        <h4>UnServiceable</h4>
                        <!-- <i class="fas fa-exclamation-triangle"></i> -->
                        <h2></h2>
                        <!-- <h2><?= $faulty_assets?></h2> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
            <a href="<?= site_url('assets?filter=' . urlencode("MAINTENANCE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green" id="maintenance-box">
                        <h4>Asset In Maintenance</h4>
                        <h2></h2>
                        <!-- <h2><?= $totalAssetsInMaintenance ?></h2>                        -->
                    </div>
                </a>
            </div>
        </div>



        <div class="row mb-3 justify-content-around">
            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items') ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Items Quantity</h4>
                        <h2><?= $total_items ?></h2>
                        <!-- <div class="percentage-changes">
                            <span class="up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                                90%
                            </span>
                            <p class="white-line">|</p>
                            <span class="down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-down">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                                10%
                            </span>
                        </div> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("STORE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>STORE</h4>
                        <h2><?= $storelocationItemCount ?></h2>
                        <!-- <div class="percentage-changes">
                            <span class="up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                                90%
                            </span>
                            <p class="white-line">|</p>
                            <span class="down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-down">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                                10%
                            </span>
                        </div> -->
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                <a href="<?= site_url('items?filter=' . urlencode("SERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Items Serviceable</h4>
                        <h2><?= $ServiceableCount ?></h2>
                        
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
<a href="<?= site_url('items?filter=' . urlencode("UNSERVICEABLE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box green">
                        <h4>UnServiceable</h4>
                        <h2><?= $UnserviceableCount ?></h2>
                    </div>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 mb-2">
                 <a href="<?= site_url('items?filter=' . urlencode("MAINTENANCE")) ?>" class="d-block text-decoration-none">
                    <div class="expiry-box blue">
                        <h4>Items In Maintenance</h4>
                        <h2><?= $MaintinenceItemCount ?></h2>
                        <!-- <div class="percentage-changes">
                            <span class="up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                                90%
                            </span>
                            <p class="white-line">|</p>
                            <span class="down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="#e74c3c" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-down">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="19 12 12 19 5 12"></polyline>
                                </svg>
                                10%
                            </span>
                        </div> -->
                    </div>
                </a>
            </div>

            
        </div>


        <div class="row">
            <div class="col-12">
                <div class="pie-chart-section">

                    <div class="row">
                        <div class="col-12">
                            <div class="pie-chart-section">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="summary-panel-heading"><div class="summary-heading-icon"><i class="fas fa-layer-group"></i></div><div><h4 class="pie-chart-heading">Summary</h4><p>Asset overview and operational status</p></div></div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container summary-ref-card summary-ref-card-cyan blue d-flex flex-column h-100"><div class="summary-ref-card-head"><span><i class="fas fa-cube"></i></span><strong>Asset Quantity</strong></div><div class="summary-ref-main"><div class="position-relative"><canvas id="pie-chart-quantity"
                                                    style="width:100%;height: 180px"></canvas>
                                                <div class="donut-absolute-center text-center">
                                                    <p id="pie-chart-asset-quantity"></p>
                                                </div>
                                            </div>
                                            <div id="assets-quantity" class="mt-auto" style="margin: 10px;">
                                                <div class="breakdown-list">
                                                    <div class="breakdown-item breakdown-headings">
                                                        <div class="type-heading" style="color: #FF9100;">Type</div>
                                                        <div class="total-heading" style="color: #FF9100;">Total</div>
                                                        <div class="percent-heading" style="color: #FF9100;">Color</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container summary-ref-card summary-ref-card-purple green d-flex flex-column h-100"><div class="summary-ref-card-head"><span><i class="fas fa-map-marker-alt"></i></span><strong>Assets by Location</strong></div><div class="summary-ref-main"><div class="position-relative"><canvas id="pie-chart-location"
                                                    style="width:100%;height: 180px"></canvas>
                                                <div class="donut-absolute-center text-center">
                                                    <p id="pie-chart-asset-location"></p>
                                                </div>
                                            </div>
                                            <div id="breakdown-list-location" class="mt-auto" style="margin: 10px;">
                                                <div class="breakdown-list">
                                                    <div class="breakdown-item breakdown-headings">
                                                        <div class="type-heading" style="color: #FF9100;">Type</div>
                                                        <div class="total-heading" style="color: #FF9100;">Total</div>
                                                        <div class="percent-heading" style="color: #FF9100;">Color</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container summary-ref-card summary-ref-card-blue creem d-flex flex-column h-100"><div class="summary-ref-card-head"><span><i class="fas fa-shield-alt"></i></span><strong>Asset Serviceable</strong></div><div class="summary-ref-main"><div class="position-relative"><canvas id="pie-chart-asset" style="width:100%;height: 180px"></canvas>
                                                <div class="donut-absolute-center text-center">
                                                    <p id="pie-chart-asset-total"></p>
                                                </div>
                                            </div>
                                            <div id="breakdown-list-asset-summary" class="mt-auto"
                                                style="margin: 10px;">
                                                <div class="breakdown-list">
                                                    <div class="breakdown-item breakdown-headings">
                                                        <div class="type-heading" style="color: #FF9100;">Type</div>
                                                        <div class="total-heading" style="color: #FF9100;">Total</div>
                                                        <div class="percent-heading" style="color: #FF9100;">Color</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container summary-ref-card summary-ref-card-red blue d-flex flex-column h-100"><div class="summary-ref-card-head"><span><i class="fas fa-exclamation-triangle"></i></span><strong>Unserviceable Assets</strong></div><div class="summary-ref-main"><div class="position-relative"><canvas id="pie-chart-faulty" style="width:100%;height: 180px"></canvas>
                                                <div class="donut-absolute-center text-center">
                                                    <p id="pie-chart-asset-faulty"></p>
                                                </div>
                                            </div>
                                            <div id="breakdown-list-faulty" class="mt-auto" style="margin: 10px;">
                                                <div class="breakdown-list mt-auto">
                                                    <div class="breakdown-item breakdown-headings">
                                                        <div class="type-heading" style="color: #FF9100;">Type</div>
                                                        <div class="total-heading" style="color: #FF9100;">Total</div>
                                                        <div class="percent-heading" style="color: #FF9100;">Color</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container summary-ref-card summary-ref-card-purple green d-flex flex-column h-100"><div class="summary-ref-card-head"><span><i class="fas fa-wrench"></i></span><strong>Asset Maintenance</strong></div><div class="summary-ref-main"><div class="position-relative"><canvas id="pie-chart-maintenance"
                                                    style="width:100%;height: 180px"></canvas>
                                                <div class="donut-absolute-center text-center">
                                                    <p id="pie-chart-asset-maintenance"></p>
                                                </div>
                                            </div>
                                            <div id="breakdown-list-maintenance" class="mt-auto" style="margin: 10px;">
                                                <div class="breakdown-list">
                                                    <div class="breakdown-item breakdown-headings">
                                                        <div class="type-heading" style="color: #FF9100;">Type</div>
                                                        <div class="total-heading" style="color: #FF9100;">Total</div>
                                                        <div class="percent-heading" style="color: #FF9100;">Color</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-2 d-flex">
                                        <div class="pie-container blue fleet-insights-card d-flex flex-column h-100">
                                            <?php // Legacy store chart canvas kept hidden so the existing store-summary.js can still initialise safely. ?>
                                            <div class="legacy-store-chart d-none">
                                                <canvas id="pie-chart-store-summary" style="width:100%;height: 1px"></canvas>
                                                <p id="pie-chart-store-summary-total"></p>
                                                <div id="breakdown-list-store-summary"></div>
                                            </div>
                                            <div class="fleet-insights-content">
                                                <div class="summary-card-title"><span><i class="fas fa-chart-line"></i></span> Fleet Insights</div>
                                                <div class="fleet-insight-grid">
                                                    <div class="fleet-insight-box"><span class="fleet-icon cyan"><i class="fas fa-cube"></i></span><small>Total Assets</small><strong><?= $totalAssets ?></strong><em>All registered assets</em></div>
                                                    <div class="fleet-insight-box"><span class="fleet-icon green"><i class="fas fa-shield-alt"></i></span><small>Serviceable %</small><strong><?= $totalAssets > 0 ? round(($totalAssetsServiceable / $totalAssets) * 100) : 0 ?>%</strong><em><?= $totalAssetsServiceable ?> of <?= $totalAssets ?> assets</em></div>
                                                    <div class="fleet-insight-box"><span class="fleet-icon amber"><i class="fas fa-map-marker-alt"></i></span><small>Locations</small><strong><?= $totalLocations ?></strong><em>Across all locations</em></div>
                                                    <div class="fleet-insight-box"><span class="fleet-icon violet"><i class="fas fa-tools"></i></span><small>Maintenance</small><strong><?= $totalAssetsInMaintenance ?? 0 ?></strong><em>Activities recorded</em></div>
                                                </div>
                                                <div class="fleet-status-strip"><i class="fas fa-sparkles"></i><div><strong>All systems operational</strong><small>Excellent fleet status across the board.</small></div></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-5 mt-5 order-lg-0 order-md-1">

        <table class="table" id="home" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>System Name</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>



    <div class="col-lg-5 mb-5 mt-5 order-lg-1 order-md-0">
        <div id="map" style="height: 350px; width: 98%"></div>
        <div class='quake-info'>
            <!-- <div><strong>Magnitude:</strong> <span id='mag'></span></div> -->
            <div><strong>Location:</strong> <span id='loc'></span></div>
            <div><strong>Asset Type:</strong> <span id='asset_type'></span></div>
            <div><strong>Asset Name:</strong> <span id='asset_name'></span></div>
            <div><strong>Asset Number:</strong> <span id='asset_num'></span></div>
            <!-- <div><strong>Date:</strong> <span id='date'></span></div> -->
        </div>
    </div>



</div>

