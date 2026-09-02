<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">    
<style>
    .asset-consolidation-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .asset-table th:nth-child(1), 
    .asset-table td:nth-child(1) {
        width: 40px;
        min-width: 40px;
        max-width: 40px;
    }

    .asset-table th:nth-child(2), 
    .asset-table td:nth-child(2) {
        width: 80px;
        min-width: 80px;
        max-width: 80px;
        text-align: center;
    }

    .asset-table th:nth-child(3), 
    .asset-table td:nth-child(3) {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .asset-table th:nth-child(4), 
    .asset-table td:nth-child(4) {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    .asset-table th:nth-child(5), 
    .asset-table td:nth-child(5) {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    .asset-table th:nth-child(6), 
    .asset-table td:nth-child(6) {
        width: 100px;
        min-width: 100px;
        max-width: 100px;
        text-align: center;
    }

    .asset-table th:nth-child(7), 
    .asset-table td:nth-child(7) {
        width: 110px;
        min-width: 110px;
        max-width: 110px;
        text-align: center;
    }

    .asset-table th:nth-child(8), 
    .asset-table td:nth-child(8) {
        width: 90px;
        min-width: 90px;
        max-width: 90px;
    }

    .asset-table th:nth-child(9), 
    .asset-table td:nth-child(9) {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    .asset-table th:nth-child(10), 
    .asset-table td:nth-child(10) {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    .asset-table th:nth-child(11), 
    .asset-table td:nth-child(11) {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }

    /* Table container for scrolling */
    .table-container {
        overflow-x: auto;
        max-width: 100%;
    }

    /* Fixed table layout */
    .asset-table {
        width: 100%;
        min-width: 1150px;
        table-layout: fixed;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .asset-table {
            min-width: 1150px;
        }
    }
    
    .duplicate-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
        position: relative;
    }
    
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        height: fit-content;
    }
    
    .filter-card h6 {
        margin-bottom: 15px;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 8px;
    }
    
    .merge-section {
        background-color: #e7f3ff;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
        display: none;
    }
    
    .selected-asset-card {
        border: 2px solid #007bff;
        background-color: #f0f8ff;
    }
    
    /* CHANGES: Asset Name bold */
    .asset-table td:nth-child(3) {
        font-weight: bold !important;
    }
    
    /* CHANGES: Filter headings bold */
    .filter-card .form-label {
        font-weight: bold !important;
    }
    
    /* CHANGES: Toggle button styling */
    #toggleFilterBtn, #showFilterBtn {
        padding: 2px 8px;
        font-size: 12px;
    }
    
    /* NEW: Show filter button when filter is hidden */
    #showFilterBtn {
        display: none;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .asset-table {
            min-width: 1150px;
        }
    }
    
    /* Custom class for full-width table */
    .table-full-width {
        width: 100% !important;
    }
</style>
</head>
<body>
<div class="container-fluid mt-4 asset-consolidation-page">
    <!-- Page overview -->
    <div class="duplicate-header">
        <div class="consolidation-hero-main">
            <span class="consolidation-hero-icon"><i class="fas fa-object-group"></i></span>
            <div>
                <span class="consolidation-eyebrow">Data Quality Workspace</span>
                <h4>Asset Consolidation</h4>
                <p>Review potential duplicates, compare their records and merge the correct assets.</p>
            </div>
        </div>
        <div class="consolidation-hero-actions">
            <div class="consolidation-count">
                <strong><?php echo $total_duplicates; ?></strong>
                <span>Potential Duplicates</span>
            </div>
            <button class="btn btn-sm btn-outline-secondary" id="showFilterBtn" title="Show Filters">
                <i class="fas fa-sliders-h"></i> Show Filters
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Table -->
        <div class="col-lg-8 col-md-7" id="tableColumn">
            <div class="asset-consolidation-container">
                <!-- Merge Controls Section (Top) -->
                <div class="merge-section" id="mergeControls">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="consolidation-selection-title">Selected Assets for Merging</h6>
                            <div id="selectedAssets" class="consolidation-selected-assets">
                                <!-- Selected assets will appear here -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-primary" id="mergeBtn">
                                    <i class="fas fa-compress"></i> Merge Selected
                                </button>
                                <button class="btn btn-outline-secondary" id="clearSelectionBtn">
                                    Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- DataTable for Duplicate Groups -->
                <?php if (!empty($duplicate_assets)): ?>
                    <div class="card mb-3 consolidation-table-card">
                        <div class="card-header bg-light consolidation-table-header">
                            <div>
                                <h6 class="mb-0"><i class="fas fa-layer-group"></i> Duplicate Asset Groups</h6>
                                <small><?php echo count($duplicate_assets); ?> assets require review</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-container">
                                <table class="table table-bordered table-hover mb-0 asset-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAllCheckbox">
                                            </th>
                                            <th>GROUP</th>
                                            <th>ASSET NAME</th>
                                            <th>REGISTRATION</th>
                                            <th>SERIAL NO.</th>
                                            <th>CONFIDENCE</th>
                                            <th>STATUS</th>
                                            <th>PURCHASE DATE</th>
                                            <th>PRICE</th>
                                            <th>TYPE</th>
                                            <th>LOCATION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($duplicate_assets as $asset): ?>
                                            <?php 
                                            $asset_id = $asset['equipment_id'] ?? $asset['id'] ?? '';
                                            $confidence = $asset['confidence'] ?? 0;
                                            $group_index = $asset['group_index'] ?? 0;
                                            ?>
                                            <tr class="asset-row" data-id="<?php echo $asset_id; ?>" data-group="<?php echo $group_index; ?>">
                                                <td>
                                                    <input type="checkbox" class="asset-checkbox" 
                                                        value="<?php echo $asset_id; ?>"
                                                        data-group="<?php echo $group_index; ?>">
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">Group <?php echo $group_index + 1; ?></span>
                                                </td>
                                                <!-- CHANGE: Asset Name bold using strong tag -->
                                                <td title="<?php echo htmlspecialchars($asset['equipment_name']); ?>">
                                                    <strong style="font-weight: 600;"><?php echo $asset['equipment_name']; ?></strong>
                                                </td>
                                                <td title="<?php echo htmlspecialchars($asset['equipment_registration']); ?>">
                                                    <?php echo $asset['equipment_registration']; ?>
                                                </td>
                                                <td title="<?php echo htmlspecialchars($asset['serial_number']); ?>">
                                                    <?php echo $asset['serial_number']; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        if ($confidence >= 90) echo 'success';
                                                        elseif ($confidence >= 80) echo 'info';
                                                        elseif ($confidence >= 70) echo 'warning';
                                                        else echo 'secondary';
                                                    ?>">
                                                        <?php echo $confidence; ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $asset['equipment_status'] == 'Active' ? 'success' : 
                                                            ($asset['equipment_status'] == 'Inactive' ? 'danger' : 'warning');
                                                    ?>">
                                                        <?php echo $asset['equipment_status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo !empty($asset['purchase_date']) ? date('Y-m-d', strtotime($asset['purchase_date'])) : ''; ?></td>
                                                <td>RM <?php echo !empty($asset['price_of_purchase']) ? number_format($asset['price_of_purchase'], 2) : '0.00'; ?></td>
                                                <td title="<?php echo htmlspecialchars($asset['asset_type_name']); ?>">
                                                    <?php echo $asset['asset_type_name']; ?>
                                                </td>
                                                <td title="<?php echo htmlspecialchars($asset['location_name']); ?>">
                                                    <?php echo $asset['location_name']; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No duplicate assets found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Column - Filters -->
        <!-- CHANGE: Added id="filterColumn" for toggle functionality -->
        <div class="col-lg-4 col-md-5" id="filterColumn">
            <!-- CHANGE: Updated filter header with toggle button -->
            <div class="filter-card consolidation-filter-card">
                <div class="d-flex justify-content-between align-items-center mb-3 consolidation-filter-heading">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Filters</h6>
                    <button class="btn btn-sm btn-outline-secondary" id="toggleFilterBtn" title="Hide Filters">
                        <i class="fas fa-chevron-right"></i> Hide Filters
                    </button>
                </div>
                
                <form method="get" action="<?php echo base_url('asset_consolidation'); ?>" id="filterForm">
                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label">Search (All Fields)</label>
                        <input type="text" class="form-control form-control-sm" 
                            name="search" value="<?php echo $filters['search'] ?? ''; ?>"
                            placeholder="Search in all fields...">
                    </div>
                    
                    <!-- Asset Type -->
                    <div class="mb-3">
                        <label class="form-label">Asset Type</label>
                        <select class="form-control form-control-sm" name="asset_type">
                            <option value="">All Types</option>
                            <?php foreach ($asset_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"
                                    <?php echo ($filters['asset_type'] ?? '') == $type['id'] ? 'selected' : ''; ?>>
                                    <?php echo $type['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <select class="form-control form-control-sm" name="location_id">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location['id']; ?>"
                                    <?php echo ($filters['location_id'] ?? '') == $location['id'] ? 'selected' : ''; ?>>
                                    <?php echo $location['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="mb-3">
                        <label class="form-label">Purchase Date Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" 
                                    name="date_from" value="<?php echo $filters['date_from'] ?? ''; ?>">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" 
                                    name="date_to" value="<?php echo $filters['date_to'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="<?php echo base_url('asset_consolidation'); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-redo"></i> Reset Filters
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Quick Actions -->
            <div class="filter-card consolidation-quick-actions">
                <h6><i class="fas fa-bolt"></i> Quick Actions</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAllAssets()">
                        <i class="fas fa-check-double"></i> Select All Assets
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearAllSelections()">
                        <i class="fas fa-times"></i> Clear All Selections
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="selectAllGroups()">
                        <i class="fas fa-object-group"></i> Select All Groups
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Merge Modal -->
<div class="modal fade" id="mergeModal" tabindex="-1" aria-labelledby="mergeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mergeModalLabel">Merge Assets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mergeForm">
                    <input type="hidden" name="primary_id" id="primary_id">
                    <input type="hidden" name="merge_ids" id="merge_ids">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Primary Asset</label>
                            <select class="form-control" name="primary_asset" id="primary_asset_select" required>
                                <option value="">Select primary asset</option>
                            </select>
                            <small class="text-muted">This asset will remain active</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Location</label>
                            <select class="form-control" name="final_data[location_id]" id="final_location">
                                <option value="">Select Location</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo $location['id']; ?>">
                                        <?php echo $location['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <h6>Final Values (Select from assets or enter custom)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Equipment Name</label>
                            <select class="form-control" name="final_data[equipment_name]" id="final_name" required>
                                <option value="">Select from assets</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="custom_name" placeholder="Or enter custom name" style="display:none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Registration</label>
                            <select class="form-control" name="final_data[equipment_registration]" id="final_registration">
                                <option value="">Select from assets</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="custom_registration" placeholder="Or enter custom registration" style="display:none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number</label>
                            <select class="form-control" name="final_data[serial_number]" id="final_serial">
                                <option value="">Select from assets</option>
                            </select>
                            <input type="text" class="form-control mt-1" id="custom_serial" placeholder="Or enter custom serial" style="display:none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purchase Date</label>
                            <select class="form-control" name="final_data[purchase_date]" id="final_date" required>
                                <option value="">Select from assets</option>
                            </select>
                            <input type="date" class="form-control mt-1" id="custom_date" style="display:none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purchase Price</label>
                            <select class="form-control" name="final_data[price_of_purchase]" id="final_price" required>
                                <option value="">Select from assets</option>
                            </select>
                            <input type="number" step="0.01" class="form-control mt-1" id="custom_price" placeholder="Or enter custom price" style="display:none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset Type</label>
                            <select class="form-control" name="final_data[equipment_type]" id="final_asset_type">
                                <option value="">Select from assets</option>
                            </select>
                            <select class="form-control mt-1" id="custom_asset_type" style="display:none;">
                                <option value="">Select Type</option>
                                <?php foreach ($asset_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>">
                                        <?php echo $type['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Installed</label>
                            <select class="form-control" name="final_data[date_installed]" id="final_date_installed">
                                <option value="">Select from assets</option>
                            </select>
                            <input type="date" class="form-control mt-1" id="custom_date_installed" style="display:none;">
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Merging will deactivate all non-primary assets. This action cannot be undone.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmMergeBtn">Confirm Merge</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAssets = new Set();
    let assetData = {};
    
    // Individual checkbox selection
    document.querySelectorAll('.asset-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            updateSelection(this);
        });
    });
    
    function updateSelection(checkbox) {
        const assetId = checkbox.value;
        
        if (checkbox.checked) {
            selectedAssets.add(assetId);
            checkbox.closest('tr').classList.add('selected-asset-card');
            
            // Load asset data if not already loaded
            if (!assetData[assetId]) {
                fetchAssetData(assetId);
            }
        } else {
            selectedAssets.delete(assetId);
            checkbox.closest('tr').classList.remove('selected-asset-card');
        }
        
        updateMergeControls();
    }
    
    async function fetchAssetData(assetId) {
        try {
            const response = await fetch(`<?php echo base_url('asset_consolidation/get_asset_details/'); ?>${assetId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                assetData[assetId] = data.data;
                updateSelectedAssetsDisplay();
            }
        } catch (error) {
            console.error('Error fetching asset data:', error);
        }
    }
    
    function updateSelectedAssetsDisplay() {
        const container = document.getElementById('selectedAssets');
        if (selectedAssets.size === 0) {
            container.innerHTML = '<div class="alert alert-info">No assets selected</div>';
            return;
        }
        
        let html = '<div class="row g-2">';
        selectedAssets.forEach(id => {
            const asset = assetData[id];
            if (asset) {
                html += `
                    <div class="col-md-4">
                        <div class="card p-2">
                            <small><strong>${asset.equipment_name}</strong></small>
                            <small class="text-muted">${asset.equipment_registration}</small><br>
                            <small>${asset.asset_type_name} - ${asset.location_name}</small>
                        </div>
                    </div>
                `;
            }
        });
        html += '</div>';
        container.innerHTML = html;
    }
    
    function updateMergeControls() {
        const controls = document.getElementById('mergeControls');
        const mergeBtn = document.getElementById('mergeBtn');
        
        if (selectedAssets.size > 1) {
            controls.style.display = 'block';
            mergeBtn.disabled = false;
        } else {
            controls.style.display = 'none';
            mergeBtn.disabled = true;
        }
    }
    
    // Merge button click
    document.getElementById('mergeBtn').addEventListener('click', function() {
        if (selectedAssets.size < 2) {
            alert('Please select at least 2 assets to merge');
            return;
        }
        
        // Populate primary asset dropdown
        const primarySelect = document.getElementById('primary_asset_select');
        primarySelect.innerHTML = '<option value="">Select primary asset</option>';
        
        // Populate all dropdowns with values from selected assets
        const nameSelect = document.getElementById('final_name');
        const regSelect = document.getElementById('final_registration');
        const serialSelect = document.getElementById('final_serial');
        const dateSelect = document.getElementById('final_date');
        const priceSelect = document.getElementById('final_price');
        const typeSelect = document.getElementById('final_asset_type');
        const dateInstalledSelect = document.getElementById('final_date_installed');
        
        // Clear all dropdowns and add custom option
        nameSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        regSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        serialSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        dateSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        priceSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        typeSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        dateInstalledSelect.innerHTML = '<option value="">Select from assets</option><option value="custom">Custom...</option>';
        
        // Collect unique values from all selected assets
        let names = new Set();
        let registrations = new Set();
        let serials = new Set();
        let dates = new Set();
        let prices = new Set();
        let types = new Map();
        let datesInstalled = new Set();
        
        selectedAssets.forEach(id => {
            const asset = assetData[id];
            if (asset) {
                // Primary asset dropdown
                const option = document.createElement('option');
                option.value = id;
                option.textContent = `${asset.equipment_name} (${asset.equipment_registration}) - ${asset.location_name}`;
                primarySelect.appendChild(option);
                
                // Collect values for other dropdowns
                if (asset.equipment_name) names.add(asset.equipment_name);
                if (asset.equipment_registration) registrations.add(asset.equipment_registration);
                if (asset.serial_number) serials.add(asset.serial_number);
                if (asset.purchase_date) {
                    const date = new Date(asset.purchase_date);
                    const formattedDate = date.toISOString().split('T')[0];
                    dates.add(formattedDate);
                }
                if (asset.price_of_purchase) prices.add(asset.price_of_purchase);
                if (asset.equipment_type) {
                    types.set(asset.equipment_type, asset.asset_type_name);
                }
                if (asset.date_installed) {
                    const dateInstalled = new Date(asset.date_installed);
                    const formattedDateInstalled = dateInstalled.toISOString().split('T')[0];
                    datesInstalled.add(formattedDateInstalled);
                }
            }
        });
        
        // Populate dropdowns with unique values
        names.forEach(name => {
            const option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            nameSelect.appendChild(option);
        });
        
        registrations.forEach(reg => {
            const option = document.createElement('option');
            option.value = reg;
            option.textContent = reg;
            regSelect.appendChild(option);
        });
        
        serials.forEach(serial => {
            const option = document.createElement('option');
            option.value = serial;
            option.textContent = serial;
            serialSelect.appendChild(option);
        });
        
        dates.forEach(date => {
            const option = document.createElement('option');
            option.value = date;
            option.textContent = date;
            dateSelect.appendChild(option);
        });
        
        prices.forEach(price => {
            const option = document.createElement('option');
            option.value = price;
            option.textContent = `RM ${parseFloat(price).toFixed(2)}`;
            priceSelect.appendChild(option);
        });
        
        types.forEach((typeName, typeId) => {
            const option = document.createElement('option');
            option.value = typeId;
            option.textContent = typeName;
            typeSelect.appendChild(option);
        });
        
        datesInstalled.forEach(date => {
            const option = document.createElement('option');
            option.value = date;
            option.textContent = date;
            dateInstalledSelect.appendChild(option);
        });
        
        // Set default values from first asset
        const firstId = Array.from(selectedAssets)[0];
        const firstAsset = assetData[firstId];
        if (firstAsset) {
            setFormValues(firstAsset);
            primarySelect.value = firstId;
        }
        
        // Setup custom input toggles
        setupCustomInputs();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('mergeModal'));
        modal.show();
    });
    
    function setFormValues(asset) {
        // Set dropdown values
        if (asset.equipment_name) {
            document.getElementById('final_name').value = asset.equipment_name;
        }
        
        if (asset.equipment_registration) {
            document.getElementById('final_registration').value = asset.equipment_registration;
        }
        
        if (asset.serial_number) {
            document.getElementById('final_serial').value = asset.serial_number;
        }
        
        // Format date properly
        if (asset.purchase_date) {
            const date = new Date(asset.purchase_date);
            const formattedDate = date.toISOString().split('T')[0];
            document.getElementById('final_date').value = formattedDate;
        }
        
        if (asset.price_of_purchase) {
            document.getElementById('final_price').value = asset.price_of_purchase;
        }
        
        if (asset.equipment_type) {
            document.getElementById('final_asset_type').value = asset.equipment_type;
        }
        
        document.getElementById('final_location').value = asset.location_id || '';
        
        if (asset.date_installed) {
            const dateInstalled = new Date(asset.date_installed);
            const formattedDateInstalled = dateInstalled.toISOString().split('T')[0];
            document.getElementById('final_date_installed').value = formattedDateInstalled;
        }
    }
    
    function setupCustomInputs() {
        const fields = [
            { select: 'final_name', custom: 'custom_name', type: 'text' },
            { select: 'final_registration', custom: 'custom_registration', type: 'text' },
            { select: 'final_serial', custom: 'custom_serial', type: 'text' },
            { select: 'final_date', custom: 'custom_date', type: 'date' },
            { select: 'final_price', custom: 'custom_price', type: 'number' },
            { select: 'final_asset_type', custom: 'custom_asset_type', type: 'select' },
            { select: 'final_date_installed', custom: 'custom_date_installed', type: 'date' }
        ];
        
        fields.forEach(field => {
            const select = document.getElementById(field.select);
            const custom = document.getElementById(field.custom);
            
            custom.style.display = 'none';
            
            select.addEventListener('change', function() {
                if (this.value === 'custom') {
                    custom.style.display = 'block';
                    this.style.display = 'none';
                    
                    if (field.type === 'text' || field.type === 'number') {
                        custom.value = '';
                        custom.focus();
                    }
                } else if (this.value) {
                    custom.style.display = 'none';
                    this.style.display = 'block';
                }
            });
            
            if (field.type === 'text' || field.type === 'number') {
                custom.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        const option = document.createElement('option');
                        option.value = this.value;
                        option.textContent = this.value;
                        select.appendChild(option);
                        
                        select.value = this.value;
                        select.style.display = 'block';
                        this.style.display = 'none';
                    }
                });
                
                custom.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.blur();
                    }
                });
            } else if (field.type === 'date') {
                custom.addEventListener('change', function() {
                    if (this.value) {
                        const option = document.createElement('option');
                        option.value = this.value;
                        option.textContent = this.value;
                        select.appendChild(option);
                        
                        select.value = this.value;
                        select.style.display = 'block';
                        this.style.display = 'none';
                    }
                });
            } else if (field.type === 'select') {
                custom.addEventListener('change', function() {
                    if (this.value) {
                        select.value = this.value;
                        select.style.display = 'block';
                        this.style.display = 'none';
                    }
                });
            }
        });
    }
    
    // Primary asset selection change
    document.getElementById('primary_asset_select').addEventListener('change', function() {
        const assetId = this.value;
        if (assetId && assetData[assetId]) {
            setFormValues(assetData[assetId]);
        }
    });
    
    // Confirm merge
    document.getElementById('confirmMergeBtn').addEventListener('click', async function() {
        const primaryId = document.getElementById('primary_asset_select').value;
        const mergeIds = Array.from(selectedAssets);
        
        if (!primaryId) {
            alert('Please select a primary asset');
            return;
        }
        
        function getFieldValue(selectId, customId, isRequired = false) {
            const select = document.getElementById(selectId);
            const custom = document.getElementById(customId);
            
            if (custom.style.display === 'block' && custom.value) {
                return custom.value;
            }
            return select.value;
        }
        
        const finalData = {
            equipment_name: getFieldValue('final_name', 'custom_name', true),
            equipment_registration: getFieldValue('final_registration', 'custom_registration'),
            serial_number: getFieldValue('final_serial', 'custom_serial'),
            purchase_date: getFieldValue('final_date', 'custom_date', true),
            price_of_purchase: getFieldValue('final_price', 'custom_price', true),
            equipment_type: getFieldValue('final_asset_type', 'custom_asset_type'),
            location_id: document.getElementById('final_location').value,
            date_installed: getFieldValue('final_date_installed', 'custom_date_installed')
        };
        
        if (!finalData.equipment_name) {
            alert('Equipment Name is required');
            return;
        }
        if (!finalData.purchase_date) {
            alert('Purchase Date is required');
            return;
        }
        if (!finalData.price_of_purchase) {
            alert('Purchase Price is required');
            return;
        }
        
        const formData = new FormData();
        formData.append('primary_id', primaryId);
        formData.append('merge_ids', JSON.stringify(Array.from(mergeIds)));
        formData.append('final_data', JSON.stringify(finalData));
        
        try {
            const response = await fetch('<?php echo base_url("asset_consolidation/merge_assets"); ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while merging assets');
        }
    });
    
    // Filter Toggle Functionality - FIXED
    document.getElementById('toggleFilterBtn').addEventListener('click', function() {
        const filterColumn = document.getElementById('filterColumn');
        const tableColumn = document.getElementById('tableColumn');
        const showFilterBtn = document.getElementById('showFilterBtn');
        
        // Hide filter column using Bootstrap's d-none
        filterColumn.classList.add('d-none');
        filterColumn.classList.remove('col-lg-4', 'col-md-5');
        
        // Make table column full width
        tableColumn.classList.remove('col-lg-8', 'col-md-7');
        tableColumn.classList.add('col-lg-12', 'col-md-12');
        
        // Show the "Show Filters" button
        showFilterBtn.style.display = 'block';
    });
    
    // Show Filter Button functionality - FIXED
    document.getElementById('showFilterBtn').addEventListener('click', function() {
        const filterColumn = document.getElementById('filterColumn');
        const tableColumn = document.getElementById('tableColumn');
        const showFilterBtn = document.getElementById('showFilterBtn');
        
        // Show filter column
        filterColumn.classList.remove('d-none');
        filterColumn.classList.add('col-lg-4', 'col-md-5');
        
        // Revert table column to original width
        tableColumn.classList.remove('col-lg-12', 'col-md-12');
        tableColumn.classList.add('col-lg-8', 'col-md-7');
        
        // Hide the "Show Filters" button
        showFilterBtn.style.display = 'none';
    });
    
    // Clear selection
    document.getElementById('clearSelectionBtn').addEventListener('click', function() {
        selectedAssets.clear();
        document.querySelectorAll('.asset-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
            checkbox.closest('tr').classList.remove('selected-asset-card');
        });
        updateMergeControls();
    });
    
    // Quick action functions
    window.selectAllAssets = function() {
        document.querySelectorAll('.asset-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
            updateSelection(checkbox);
        });
    };
    
    window.clearAllSelections = function() {
        document.getElementById('clearSelectionBtn').click();
    };
    
    window.selectAllGroups = function() {
        document.querySelectorAll('.group-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change'));
        });
    };
    
    // Date range validation for filter form
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo = document.querySelector('input[name="date_to"]');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', validateDateRange);
        dateTo.addEventListener('change', validateDateRange);
    }
    
    function validateDateRange() {
        const from = dateFrom.value;
        const to = dateTo.value;
        
        if (from && to && new Date(from) > new Date(to)) {
            alert('End date must be after start date');
            dateTo.value = '';
        }
    }
});
</script>
