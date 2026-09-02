<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #2ec4b6;
            --warning-color: #ff9f1c;
            --danger-color: #e71d36;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --border-color: #dee2e6;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
        }
        
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.2);
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .stat-card .percentage {
            font-size: 0.85rem;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .percentage.positive {
            background-color: rgba(46, 196, 182, 0.1);
            color: var(--success-color);
        }
        
        .percentage.negative {
            background-color: rgba(231, 29, 54, 0.1);
            color: var(--danger-color);
        }
        
        .write-off-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--warning-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .write-off-card .badge {
            padding: 5px 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
            border-top: none;
            border-bottom: 2px solid var(--border-color);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background-color: rgba(46, 196, 182, 0.1);
            color: var(--success-color);
        }
        
        .status-maintenance {
            background-color: rgba(255, 159, 28, 0.1);
            color: var(--warning-color);
        }
        
        .action-btn {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .action-btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .action-btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .action-btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .action-btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .category-chip {
            display: inline-block;
            padding: 4px 12px;
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .info-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .divider {
            border-top: 1px solid var(--border-color);
            margin: 20px 0;
        }
        
        .highlight-box {
            background: linear-gradient(to right, rgba(67, 97, 238, 0.05), rgba(67, 97, 238, 0.1));
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 15px 0;
        }
        
        /* ========== NEW STYLES ========== */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        /* ================================ */
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-card h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- ========== ADD THIS LOADING OVERLAY ========== -->
<div id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading data...</p>
    </div>
</div>
<!-- ============================================== -->

<div class="dashboard-container depreciation-summary-page">

    <!-- ================= HEADER WITH FILTER ================= -->
    <div class="depreciation-summary-hero">
        <div class="depreciation-summary-intro">
            <span class="depreciation-summary-icon"><i class="fas fa-chart-line"></i></span>
            <div>
                <span class="depreciation-summary-eyebrow">Financial Overview</span>
                <h2>Asset Depreciation Summary</h2>
                <p>Portfolio value, depreciation movement and write-off exposure as of <?php echo $current_date; ?>.</p>
                <span class="filter-badge">
                    <i class="fas fa-layer-group me-1"></i>
                    <?php echo $selected_asset_type_name; ?>
                </span>
            </div>
        </div>

        <div class="filter-section depreciation-summary-filter">
            <div class="depreciation-filter-fields">
                <div>
                    <label class="form-label">Asset Type</label>
                    <select id="assetTypeFilter" class="form-select form-select-sm">
                        <?php foreach ($asset_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" 
                                <?php echo ($selected_asset_type_id == $type['id']) ? 'selected' : ''; ?>>
                                <?php echo $type['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Period</label>
                    <select id="timeFilter" class="form-select form-select-sm">
                        <option selected>Year to Date</option>
                        <option>Monthly</option>
                        <option>Quarterly</option>
                        <option>Annual</option>
                    </select>
                </div>
                <button id="applyFilter" class="btn btn-primary btn-sm">
                    <i class="fas fa-sync-alt me-1"></i> Apply
                </button>
            </div>
        </div>
    </div>

    <!-- ================= SUMMARY CARDS ================= -->
    <div class="row mb-4" id="summaryCards">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card summary-metric summary-metric-value">
                <div class="label">Total Asset Value</div>
                <h2 id="totalAssetValue">RM <?php echo number_format($summary_data['total_asset_value']); ?></h2>
                <div class="percentage <?php echo ($summary_data['vs_last_month'] >= 0) ? 'positive' : 'negative'; ?>">
                    <?php echo ($summary_data['vs_last_month'] >= 0) ? '+' : ''; ?><?php echo $summary_data['vs_last_month']; ?>% vs last month
                </div>
                <div class="info-text mt-1">
                    <small><span id="totalAssets"><?php echo $summary_data['total_assets']; ?></span> Assets</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card summary-metric summary-metric-depreciation">
                <div class="label">Total Depreciation</div>
                <h2 id="totalDepreciation">-RM <?php echo number_format($summary_data['total_accumulated_depreciation']); ?></h2>
                <div class="info-text">
                    <span id="depreciationRate"><?php echo $summary_data['depreciation_rate']; ?></span>% of total value
                </div>
                <div class="info-text mt-1">
                    <small>Current Year: RM <span id="currentYearDepreciation"><?php echo number_format($summary_data['total_current_year_depreciation']); ?></span></small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card summary-metric summary-metric-book">
                <div class="label">Net Book Value</div>
                <h2 id="netBookValue">RM <?php echo number_format($summary_data['net_book_value']); ?></h2>
                <div class="percentage <?php echo ($summary_data['ytd_variance'] >= 0) ? 'positive' : 'negative'; ?>">
                    <?php echo ($summary_data['ytd_variance'] >= 0) ? '+' : ''; ?><?php echo $summary_data['ytd_variance']; ?>% YTD variance
                </div>
                <div class="info-text mt-1">
                    <small>
                        <span id="activeAssets"><?php echo $summary_data['active_assets']; ?></span> Active, 
                        <span id="inactiveAssets"><?php echo $summary_data['inactive_assets']; ?></span> Inactive
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card summary-metric summary-metric-average">
                <div class="label">Avg. Depreciation / Year</div>
                <h2 id="avgDepreciationYear">RM <?php echo number_format($summary_data['avg_depreciation_per_year']); ?></h2>
                <div class="info-text">Based on 5-year trend</div>
                <div class="info-text mt-1">
                    <small>Total Depreciation: RM <span id="totalDepreciationValue"><?php echo number_format($summary_data['total_depreciation']); ?></span></small>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= WRITE-OFF + REPORTS ================= -->
    <div class="row mb-4">
        <!-- WRITE-OFF (LEFT) -->
        <div class="col-lg-8">
            <div class="stat-card depreciation-writeoff-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Earmarked for Write-Off</h5>
                    <span class="text-danger fw-semibold" id="writeOffSummary">
                        RM <?php echo number_format($summary_data['write_off_pending'], 2); ?>
                        · <?php echo $summary_data['write_off_items']; ?> Items Due
                    </span>
                </div>
                
                <p class="text-muted mb-3"><small>Pending final approval</small></p>

                <div id="writeOffAssetsContainer">
                    <?php if (!empty($write_off_assets)): ?>
                        <?php foreach ($write_off_assets as $asset): ?>
                            <div class="write-off-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo $asset['asset_name']; ?></strong><br>
                                        <small class="text-muted"><?php echo $asset['description']; ?></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold me-3">
                                            RM <?php echo number_format($asset['value']); ?>
                                        </div>
                                        <span class="badge bg-warning"><?php echo $asset['status']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No assets pending for write-off.
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($summary_data['write_off_items'] > 0): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong>Requires action by Oct 31</strong>
                            </div>
                            <button class="btn btn-danger btn-sm">
                                Process All
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- REPORTS (RIGHT) -->
        <div class="col-lg-4">
            <div class="stat-card depreciation-report-card text-center h-100 d-flex flex-column justify-content-center">
                <i class="fas fa-chart-bar fa-2x text-primary mb-3"></i>
                <h5>Detailed Reports Available</h5>
                <p class="text-muted mb-3">
                    View comprehensive depreciation schedules and category breakdowns.
                </p>
                <!-- UPDATED LINK: Changed to your specified route -->
                <a href="<?php echo site_url('asset_depreciation'); ?>" class="btn btn-primary btn-sm">
                    Go to Reports
                </a>
            </div>
        </div>
    </div>

    <!-- ================= TRANSACTIONS TABLE ================= -->
    <div class="row">
        <div class="col-12">
            <div class="table-card depreciation-transactions-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent Transactions</h5>
                    <a href="<?php echo base_url('transactions'); ?>" class="btn btn-outline-primary btn-sm">
                        View All
                    </a>
                </div>

                <div id="transactionsContainer">
                    <?php if (!empty($recent_transactions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ASSET NAME</th>
                                        <th>CATEGORY</th>
                                        <th>DATE</th>
                                        <th>VALUE</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsBody">
                                    <?php foreach ($recent_transactions as $t): ?>
                                        <tr>
                                            <td><strong><?php echo $t['asset_name']; ?></strong></td>
                                            <td>
                                                <span class="depreciation-category-badge"><?php echo $t['category']; ?></span>
                                            </td>
                                            <td><?php echo $t['date']; ?></td>
                                            <td>RM <?php echo number_format($t['value'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($t['status']); ?>">
                                                    <?php echo $t['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No recent transactions found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Base URL for AJAX calls
        const baseUrl = '<?php echo base_url(); ?>';
        
        // Apply Filter button click handler
        document.getElementById('applyFilter').addEventListener('click', function() {
            applyFilters();
        });
        
        // Also apply filter when dropdown changes (optional)
        document.getElementById('assetTypeFilter').addEventListener('change', function() {
            applyFilters();
        });
        
        function applyFilters() {
            const assetTypeId = document.getElementById('assetTypeFilter').value;
            
            // Show loading state
            showLoading(true);
            
            // AJAX call to get filtered data
            $.ajax({
                url: baseUrl + 'depreciation_summary/get_filtered_data',
                type: 'POST',
                data: {
                    asset_type_id: assetTypeId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response);
                    
                    if (response.success && response.data) {
                        const data = response.data;
                        const summary = data.summary;
                        const writeOffAssets = data.write_off_assets;
                        const transactions = data.recent_transactions;
                        
                        // Update summary cards
                        updateSummaryCards(summary);
                        
                        // Update write-off section
                        updateWriteOffSection(writeOffAssets);
                        
                        // Update transactions table
                        updateTransactionsTable(transactions);
                        
                        // Update filter badge
                        updateFilterBadge(assetTypeId);
                    } else {
                        alert('Error: Invalid response from server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    
                    if (xhr.status === 404) {
                        alert('Error: Server endpoint not found. Please check your routes.');
                    } else {
                        alert('Error loading filtered data. Please try again.');
                    }
                },
                complete: function() {
                    // Hide loading
                    showLoading(false);
                }
            });
        }
        
        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            const applyBtn = document.getElementById('applyFilter');
            
            if (show) {
                overlay.style.display = 'flex';
                applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
                applyBtn.disabled = true;
            } else {
                overlay.style.display = 'none';
                applyBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Apply Filter';
                applyBtn.disabled = false;
            }
        }
        
        function updateSummaryCards(summary) {
            // Convert all values to numbers first
            const convertToNumber = (value) => {
                if (typeof value === 'string') {
                    // Remove commas and convert to number
                    return parseFloat(value.toString().replace(/,/g, '')) || 0;
                }
                return parseFloat(value) || 0;
            };
            
            const formatNumber = (num) => {
                // Ensure it's a number
                const number = convertToNumber(num);
                // Format with commas
                return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };
            
            const formatCurrency = (num) => {
                const number = convertToNumber(num);
                return 'RM ' + formatNumber(number);
            };
            
            // Update total asset value
            const totalAssetValue = convertToNumber(summary.total_asset_value);
            document.getElementById('totalAssetValue').innerHTML = formatCurrency(totalAssetValue);
            
            // Update total depreciation
            const totalDepreciation = convertToNumber(summary.total_accumulated_depreciation);
            document.getElementById('totalDepreciation').innerHTML = '-RM ' + formatNumber(totalDepreciation);
            
            // Update depreciation rate
            const depreciationRate = convertToNumber(summary.depreciation_rate);
            document.getElementById('depreciationRate').textContent = depreciationRate.toFixed(1);
            
            // Update current year depreciation
            const currentYearDep = convertToNumber(summary.total_current_year_depreciation);
            document.getElementById('currentYearDepreciation').textContent = formatNumber(currentYearDep);
            
            // Update net book value
            const netBookValue = convertToNumber(summary.net_book_value);
            document.getElementById('netBookValue').innerHTML = formatCurrency(netBookValue);
            
            // Update asset counts
            document.getElementById('totalAssets').textContent = convertToNumber(summary.total_assets);
            document.getElementById('activeAssets').textContent = convertToNumber(summary.active_assets);
            document.getElementById('inactiveAssets').textContent = convertToNumber(summary.inactive_assets);
            
            // Update average depreciation
            const avgDepreciation = convertToNumber(summary.avg_depreciation_per_year);
            document.getElementById('avgDepreciationYear').innerHTML = formatCurrency(avgDepreciation);
            
            // Update total depreciation value
            const totalDepValue = convertToNumber(summary.total_depreciation);
            document.getElementById('totalDepreciationValue').textContent = formatNumber(totalDepValue);
            
            // Update percentage colors
            updatePercentageColors(summary);
        }
        
        function updatePercentageColors(summary) {
            // Convert to numbers
            const vsLastMonth = parseFloat(summary.vs_last_month) || 0;
            const ytdVariance = parseFloat(summary.ytd_variance) || 0;
            
            // Update vs last month percentage
            const vsLastMonthElem = document.querySelector('#summaryCards .stat-card:nth-child(1) .percentage');
            if (vsLastMonthElem) {
                vsLastMonthElem.className = 'percentage ' + 
                    (vsLastMonth >= 0 ? 'positive' : 'negative');
                vsLastMonthElem.innerHTML = 
                    (vsLastMonth >= 0 ? '+' : '') + vsLastMonth.toFixed(1) + '% vs last month';
            }
            
            // Update YTD variance percentage
            const ytdVarianceElem = document.querySelector('#summaryCards .stat-card:nth-child(3) .percentage');
            if (ytdVarianceElem) {
                ytdVarianceElem.className = 'percentage ' + 
                    (ytdVariance >= 0 ? 'positive' : 'negative');
                ytdVarianceElem.innerHTML = 
                    (ytdVariance >= 0 ? '+' : '') + ytdVariance.toFixed(1) + '% YTD variance';
            }
        }
        
        function updateWriteOffSection(writeOffAssets) {
            const container = document.getElementById('writeOffAssetsContainer');
            const summaryElem = document.getElementById('writeOffSummary');
            
            let html = '';
            let totalPending = 0;
            let itemCount = writeOffAssets.length;
            
            if (writeOffAssets.length > 0) {
                writeOffAssets.forEach(asset => {
                    const assetValue = parseFloat(asset.value) || 0;
                    totalPending += assetValue;
                    
                    html += `
                        <div class="write-off-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${asset.asset_name || 'N/A'}</strong><br>
                                    <small class="text-muted">${asset.description || 'Pending Review'}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold me-3">
                                        RM ${formatNumber(assetValue)}
                                    </div>
                                    <span class="badge bg-warning">${asset.status || 'Review'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Update summary
                summaryElem.innerHTML = 
                    `RM ${formatNumber(totalPending.toFixed(2))} · ${itemCount} Items Due`;
                
                // Add action alert if items exist
                html += `
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong>Requires action by Oct 31</strong>
                            </div>
                            <button class="btn btn-danger btn-sm">
                                Process All
                            </button>
                        </div>
                    </div>
                `;
            } else {
                html = '<div class="alert alert-info">No assets pending for write-off.</div>';
                summaryElem.innerHTML = 'RM 0.00 · 0 Items Due';
            }
            
            container.innerHTML = html;
            
            // Re-attach click handlers to new write-off cards
            document.querySelectorAll('.write-off-card').forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    const assetName = this.querySelector('strong').textContent;
                    alert(`Reviewing ${assetName}. In real implementation, this would open a detailed view.`);
                });
            });
        }
        
        function updateTransactionsTable(transactions) {
            const container = document.getElementById('transactionsContainer');
            
            if (transactions && transactions.length > 0) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ASSET NAME</th>
                                    <th>CATEGORY</th>
                                    <th>DATE</th>
                                    <th>VALUE</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                transactions.forEach(t => {
                    const status = t.status || 'active';
                    const statusClass = status.toLowerCase().includes('active') ? 'active' : 'maintenance';
                    const value = parseFloat(t.value) || 0;
                    
                    html += `
                        <tr>
                            <td><strong>${t.asset_name || 'N/A'}</strong></td>
                            <td>
                                <span class="depreciation-category-badge">${t.category || 'Uncategorized'}</span>
                            </td>
                            <td>${t.date || 'N/A'}</td>
                            <td>RM ${formatNumber(value.toFixed(2))}</td>
                            <td>
                                <span class="status-badge status-${statusClass}">
                                    ${status}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="alert alert-info">No recent transactions found.</div>';
            }
        }
        
        function updateFilterBadge(assetTypeId) {
            // Get the selected asset type name from dropdown
            const select = document.getElementById('assetTypeFilter');
            const selectedOption = select.options[select.selectedIndex];
            const assetTypeName = selectedOption.text;
            
            // Update filter badge
            const filterBadge = document.querySelector('.filter-badge');
            if (filterBadge) {
                filterBadge.innerHTML = `<i class="fas fa-filter me-1"></i> Showing: ${assetTypeName}`;
            }
        }
        
        function formatNumber(num) {
            // Convert to number first
            const number = typeof num === 'string' ? parseFloat(num.replace(/,/g, '')) : parseFloat(num);
            
            if (isNaN(number)) return '0';
            
            // Format with commas
            return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Add click handlers to existing write-off items
        document.querySelectorAll('.write-off-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const assetName = this.querySelector('strong').textContent;
                alert(`Reviewing ${assetName}. In real implementation, this would open a detailed view.`);
            });
        });
        
        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Test AJAX URL
        console.log('Base URL:', baseUrl);
        console.log('AJAX URL:', baseUrl + 'depreciation_summary/get_filtered_data');
    });
    </script>
</body>
</html>
