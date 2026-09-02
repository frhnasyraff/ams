<style>
    /* Legacy inventory inline styles kept commented by design; active styling lives in steve-black-blue-theme.css. */
    <?php /*
    .pie-container { display: grid; align-items: flex-start; justify-content: flex-start; }
    .chart-container { width: 50%; height: 312px; margin-bottom: 10px; }
    .donut-absolute-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
    .breakdown-list { flex: 1; max-width: 40%; margin-top: 30px; }
    */ ?>
    /* Keep long category names readable without widening the whole page. */
    .inventory-summary-page .inventory-chart-frame {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(45, 190, 255, .7) rgba(4, 15, 32, .7);
    }

    .inventory-summary-page .inventory-chart-frame::-webkit-scrollbar {
        height: 8px;
    }

    .inventory-summary-page .inventory-chart-frame::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(4, 15, 32, .72);
    }

    .inventory-summary-page .inventory-chart-frame::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: linear-gradient(90deg, #247bff, #27d3ff);
    }

    .inventory-summary-page .inventory-chart-stage {
        position: relative;
        min-width: 100%;
        height: 252px;
    }

    .inventory-summary-page .inventory-chart-stage canvas {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 600px) {
        .inventory-summary-page .inventory-chart-stage {
            height: 304px;
        }
    }
</style>

<div class="inventory-summary-page">
    <section class="inventory-overview-hero">
        <div class="inventory-overview-copy">
            <span class="inventory-eyebrow"><i class="fas fa-layer-group"></i> Live inventory</span>
            <h2>Inventory Health Overview</h2>
            <p>Track quantity, availability and maintenance exposure across every inventory category.</p>
        </div>

    <nav class="inventory-tabs-shell" aria-label="Inventory summary type">
        <div class="nav nav-tabs inventory-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="asset_tab" data-toggle="tab" href="#nav-asset" role="tab" aria-controls="nav-asset" aria-selected="true">
                <i class="fas fa-box"></i><span>Asset</span>
            </a>
            <a class="nav-item nav-link" id="item_tab" data-toggle="tab" href="#nav-item" role="tab" aria-controls="nav-item" aria-selected="false">
                <i class="fas fa-cubes"></i><span>Component</span>
            </a>
        </div>
    </nav>
    </section>

    <section class="inventory-kpi-grid" aria-label="Inventory snapshot" aria-live="polite">
        <article class="inventory-kpi inventory-kpi-total">
            <span class="inventory-kpi-icon"><i class="fas fa-boxes"></i></span>
            <div class="inventory-kpi-copy">
                <span data-inventory-label="total">Total Assets</span>
                <strong data-inventory-metric="total">0</strong>
                <small data-inventory-note="total">Across 0 categories</small>
            </div>
        </article>
        <article class="inventory-kpi inventory-kpi-serviceable">
            <span class="inventory-kpi-icon"><i class="fas fa-check-circle"></i></span>
            <div class="inventory-kpi-copy">
                <span data-inventory-label="serviceable">Serviceable</span>
                <strong data-inventory-metric="serviceable">0</strong>
                <small data-inventory-note="serviceable">Ready for operation</small>
            </div>
        </article>
        <article class="inventory-kpi inventory-kpi-store">
            <span class="inventory-kpi-icon"><i class="fas fa-warehouse"></i></span>
            <div class="inventory-kpi-copy">
                <span data-inventory-label="store">In Store</span>
                <strong data-inventory-metric="store">0</strong>
                <small data-inventory-note="store">Available in storage</small>
            </div>
        </article>
        <article class="inventory-kpi inventory-kpi-attention">
            <span class="inventory-kpi-icon"><i class="fas fa-tools"></i></span>
            <div class="inventory-kpi-copy">
                <span data-inventory-label="attention">Maintenance</span>
                <strong data-inventory-metric="attention">0</strong>
                <small data-inventory-note="attention">Requires attention</small>
            </div>
        </article>
    </section>

    <section class="summary-tab inventory-summary-section">
        <div class="tab-content">
            <div class="tab-pane show active" id="nav-asset" role="tabpanel" aria-labelledby="asset_tab">
                <div class="inventory-chart-card">
                    <div class="inventory-card-title">
                        <span class="inventory-title-icon"><i class="fas fa-chart-bar"></i></span>
                        <div>
                            <h3>Asset Distribution</h3>
                            <p>Compare inventory condition across asset categories.</p>
                        </div>
                        <span class="inventory-live-pill"><i class="fas fa-circle"></i> Live overview</span>
                    </div>
                    <div class="inventory-chart-frame">
                        <div class="inventory-chart-stage">
                            <canvas id="stackedChartID"></canvas>
                        </div>
                    </div>
                </div>

                <div class="inventory-table-card">
                    <div class="inventory-table-heading">
                        <div>
                            <span class="inventory-section-kicker">Asset breakdown</span>
                            <h3>Inventory by Equipment Type</h3>
                        </div>
                        <p>Detailed quantity and condition for each asset category.</p>
                    </div>
                    <div class="table-responsive inventory-table-responsive">
                        <table class="table inventory-table" id="assets" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Equipment Type</th>
                                    <th>Locations</th>
                                    <th>Asset Quantity</th>
                                    <th>Serviceable</th>
                                    <th>In Store</th>
                                    <th>Corrective</th>
                                    <th>Preventive</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="nav-item" role="tabpanel" aria-labelledby="item_tab">
                <div class="inventory-chart-card">
                    <div class="inventory-card-title">
                        <span class="inventory-title-icon"><i class="fas fa-chart-bar"></i></span>
                        <div>
                            <h3>Component Distribution</h3>
                            <p>Compare component availability across item categories.</p>
                        </div>
                        <span class="inventory-live-pill"><i class="fas fa-circle"></i> Live overview</span>
                    </div>
                    <div class="inventory-chart-frame">
                        <div class="inventory-chart-stage">
                            <canvas id="stackedChartItem"></canvas>
                        </div>
                    </div>
                </div>

                <div class="inventory-table-card">
                    <div class="inventory-table-heading">
                        <div>
                            <span class="inventory-section-kicker">Component breakdown</span>
                            <h3>Inventory by Component Type</h3>
                        </div>
                        <p>Detailed quantity and condition for each component category.</p>
                    </div>
                    <div class="table-responsive inventory-table-responsive">
                        <table class="table inventory-table" id="items" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Component Type</th>
                                    <th>Locations</th>
                                    <th>Total Quantity</th>
                                    <th>Serviceable</th>
                                    <th>In Store</th>
                                    <th>Corrective</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
