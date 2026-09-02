<style>
    /* General Container Styles */
    .pie-container {
        display: flex;
        align-items: flex-start;
        /* Align items at the start */
        justify-content: flex-start;
        /* Ensure the chart aligns at the start of the row */
        gap: px;
        /* Adds spacing between chart and list */
    }
    .item-pie-container {
        display: flex;
        align-items: flex-start;
        /* Align items at the start */
        justify-content: flex-start;
        /* Ensure the chart aligns at the start of the row */
        gap: px;
        /* Adds spacing between chart and list */
    }
    .chart-container {
        flex: 1;
        position: relative;
        max-width: 35%;
        /* Adjust chart width */
        margin-bottom: 10px;
    }

    .donut-absolute-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 20px;
        color: #000;
        font-weight: bold;
        text-align: center;
    }

    .breakdown-list {
        flex: 1;
        max-width: 40%;
        /* Adjust width of the breakdown list */
        margin-top: 20px;
    }

    .breakdown-list h3 {
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .breakdown-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        margin-top: 20px;
    }

    .color-bullet {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-right: 10px;
        border: 1px solid #ccc;
    }

    .item-label {
        font-size: 14px;
        color: #333;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .pie-container {
            flex-direction: column;
            /* Stack chart and list vertically */
            align-items: flex-start;
        }

        .chart-container,
        .breakdown-list {
            max-width: 100%;
            /* Expand elements to full width */
        }
    }
    @media (max-width: 768px) {
        .item-pie-container {
            flex-direction: column;
            /* Stack chart and list vertically */
            align-items: flex-start;
        }

        .chart-container,
        .breakdown-list {
            max-width: 100%;
            /* Expand elements to full width */
        }
    }
</style>



<nav>
    <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="asset_tab" data-toggle="tab" href="#nav-asset" role="tab"
            aria-controls="nav-asset" aria-selected="true">Asset</a>
        <a class="nav-item nav-link" id="item_tab" data-toggle="tab" href="#nav-item" role="tab"
            aria-controls="nav-item" aria-selected="false">Item</a>
    </div>
</nav>

<section class="summary-tab">
    <div class="row">
        <div class="col-12">
            <div class="tab-content">
                <div class="tab-pane show active" id="nav-asset" role="tabpanel" aria-labelledby="asset_tab">
                    <div class="row">
                        <div class="col-lg-10 pie-container creem d-flex align-items-start">
                            <!-- Chart Container -->
                            <div class="chart-container position-relative">
                                <canvas id="pie-chart-asset"></canvas>
                                <div class="donut-absolute-center text-center">
                                    <h3 id="pie-chart-asset-total" style="font-weight: bold;  margin-top: 10px;"></h3>
                                </div>
                            </div>

                            <!-- Breakdown List -->

                        </div>
                    </div>

                    <div class="card shadow mb-4 tabradius">
                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table" id="assets" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane" id="nav-item" role="tabpanel" aria-labelledby="item_tab">
                    <div class="summary-header">

                        <div class="row">
                            <div class="col-lg-10 item-pie-container creem d-flex align-items-start">
                                <!-- Chart Container -->
                                <div class="chart-container position-relative">
                                    <canvas id="pie-chart-item"></canvas>
                                    <div class="donut-absolute-center text-center">
                                        <h3 id="pie-chart-item-total" style="font-weight: bold;  margin-top: 10px;"></h3>
                                    </div>
                                </div>

                                <!-- Breakdown List -->

                            </div>
                        </div>

                        <div class="card shadow mb-4 tabradius">
                            <div class="card-body">

                                 <div class="table-responsive">

                                <table class="table" id="items" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>