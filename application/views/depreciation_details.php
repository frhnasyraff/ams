<!-- ✅ ADDED: Custom CSS -->
<style>
/* Export Button Styling */
.export-btn {
    background-color: #000 !important;
    color: #fff !important;
    border: 1px solid #000 !important;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.export-btn:hover {
    background-color: #333 !important;
    border-color: #333 !important;
    color: #fff !important;
}

/* Reducing Balance Badge Styling */
.reducing-balance-badge {
    background-color: #000 !important;
    color: #fff !important;
    font-weight: normal !important;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
}

/* Back Button Styling */
.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: #fff;
}

/* Card Header Styling */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
    padding: 0.75rem 1.25rem;
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.table td, .table th {
    vertical-align: middle;
    padding: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
    
    .page-header > div {
        width: 100%;
    }
    
    .export-btn {
        align-self: flex-end;
    }
}
</style>

<div class="container-fluid">
    <!-- PAGE HEADER -->
    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h3 id="pageTitle"><?= $title ?></h3>
        </div>
        <?php if (!empty($rows)): ?>
            <!-- ✅ ADDED: Back Button -->
            <a href="javascript:history.back()" 
               class="btn btn-outline-secondary btn-sm me-3" style="height: 34px">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <!-- ✅ CHANGED: Export button styling -->
            <a href="<?= site_url('depreciation_details/export_csv/' . $export_asset_id) ?>" 
               class="btn btn-sm export-btn" 
               style="background-color: #000; color: #fff; border: 1px solid #000;">
                <i class="fas fa-download"></i> Export to CSV
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($asset) && isset($asset['name'])): ?>
        <!-- TOP ROW : TWO CARDS -->
        <div class="row mb-4" id="assetDetails">
            <!-- ASSET SUMMARY -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <strong>Asset Summary</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Asset Name:</strong> <span id="asset_name"><?= htmlspecialchars($asset['name']) ?></span></p>
                        <p><strong>Asset ID:</strong> <span id="asset_id"><?= htmlspecialchars($asset['asset_id']) ?></span></p>
                        <p><strong>Category:</strong> <span id="category"><?= htmlspecialchars($asset['category']) ?></span></p>
                        <p><strong>Acquisition Date:</strong> <span id="acquisition_date"><?= $asset['acquisition_date'] ?></span></p>
                        <p><strong>Acquisition Cost:</strong> RM <span id="cost"><?= number_format($asset['cost'], 2) ?></span></p>
                    </div>
                </div>
            </div>

            <!-- DEPRECIATION PARAMETERS -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Depreciation Parameters</strong>
                        <!-- ✅ CHANGED: Badge styling for Reducing Balance -->
                        <?php if ($asset['method'] === 'Reducing Balance'): ?>
                            <span class="badge reducing-balance-badge" 
                                  style="background-color: #000; color: #fff; font-weight: normal;">
                                <?= htmlspecialchars($asset['method']) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary" id="method">
                                <?= htmlspecialchars($asset['method']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($asset['method'] === 'Straight Line'): ?>
                            <div id="straight_line_params">
                                <p><strong>Useful Life:</strong> <span id="useful_life"><?= $asset['useful_life'] ?></span></p>
                                <p><strong>Salvage Value:</strong> RM <span id="salvage_value"><?= number_format($asset['salvage_value'], 2) ?></span></p>
                            </div>
                        <?php else: ?>
                            <div id="reducing_balance_params">
                                <p><strong>Depreciation Rate:</strong> <span id="depreciate_value"><?= $asset['depreciate_value'] ?></span>%</p>
                            </div>
                        <?php endif; ?>
                        <hr>
                        <h6 class="text-muted">Current Book Value</h6>
                        <h4>RM <span id="current_book_value"><?= number_format($asset['current_book'], 2) ?></span></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM ROW : FULL WIDTH TABLE -->
        <div class="row" id="scheduleTable">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong>Depreciation Schedule</strong>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($rows)): ?>
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Year</th>
                                        <th>Beginning Value</th>
                                        <th>Depreciation</th>
                                        <th>Accumulated</th>
                                        <th>Ending Value</th>
                                    </tr>
                                </thead>
                                <tbody id="depTableBody">
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= $row['year'] ?></td>
                                            <td>RM <?= number_format($row['beginning'], 2) ?></td>
                                            <td class="text-danger">(RM <?= number_format(abs($row['depreciation']), 2) ?>)</td>
                                            <td class="text-muted">(RM <?= number_format(abs($row['accumulated']), 2) ?>)</td>
                                            <td>RM <?= number_format($row['ending'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                No depreciation schedule available for this asset. Please check if the asset has:
                                <ul>
                                    <li>Purchase date set</li>
                                    <li>Purchase price set</li>
                                    <li>Depreciation parameters configured for the asset type</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h4>No Asset Data Found</h4>
            <p>The asset with ID <?= $asset_id ?? 'Unknown' ?> was not found or has incomplete data.</p>
            <p>Please check:</p>
            <ul>
                <li>The asset exists in the equipments_asset table</li>
                <li>The asset has equipment_type set</li>
                <li>The asset type exists in asset_types table</li>
                <li>The asset has purchase_date and price_of_purchase set</li>
            </ul>
            <!-- ✅ ADDED: Back Button for error page too -->
            <a href="javascript:history.back()" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    <?php endif; ?>
</div>
