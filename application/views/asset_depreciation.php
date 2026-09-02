<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
/* Optional: Chart container styling */
#netBookChart {
    max-height: 250px;
    width: 100% !important;
}

/* Make sure chart is responsive */
.chart-container {
    position: relative;
    height: 250px;
    width: 100%;
}
</style>
<div class="container-fluid asset-depreciation-page">

<div id="loadingSpinner" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;background:#fff;padding:20px;border-radius:8px">
    <div class="spinner-border text-primary"></div>
    <p class="mt-2 mb-0">Loading asset data...</p>
</div>

<div class="page-header">
    <h3 class="page-title">Asset Depreciation Management</h3>
</div>

<!-- FILTER -->
<div class="card mb-3 asset-depreciation-filter">
    <div class="card-body">
        <div class="depreciation-filter-copy">
            <span class="depreciation-filter-icon"><i class="fa fa-chart-line"></i></span>
            <div>
                <h4>Depreciation Overview</h4>
                <p>Select an asset type to review its value, policy and monthly depreciation.</p>
            </div>
        </div>
        <div class="depreciation-filter-control">
            <label for="assetTypeFilter">Asset Type</label>
            <select id="assetTypeFilter" class="form-control"
                    onchange="loadAssetTypeDetails(this.value)">
                <option value="">-- Select Asset Type --</option>
                <?php foreach ($asset_types as $t): ?>
                    <option value="<?= $t['asset_id'] ?>">
                        <?= $t['name'] ?> (<?= $t['asset_count'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- SUMMARY -->
<div class="card mb-3" id="summarySection" style="display:none">
<div class="card-header"><strong>Summary Details</strong></div>
<div class="card-body">
<div class="depreciation-summary-grid">
    <div class="depreciation-metric metric-assets">
        <span class="metric-icon"><i class="fa fa-cubes"></i></span>
        <div><small id="assetTypeName">Assets</small><h4 id="assetCount"></h4></div>
    </div>
    <div class="depreciation-metric metric-cost">
        <span class="metric-icon"><i class="fa fa-coins"></i></span>
        <div><small>Total Cost</small><h4 id="totalCost"></h4></div>
    </div>
    <div class="depreciation-metric metric-accumulated">
        <span class="metric-icon"><i class="fa fa-layer-group"></i></span>
        <div><small>Accumulated Dep.</small><h4 id="totalAccumulated"></h4></div>
    </div>
    <div class="depreciation-metric metric-current">
        <span class="metric-icon"><i class="fa fa-calendar-check"></i></span>
        <div><small>Current Year Dep.</small><h4 id="totalCurrentYear"></h4></div>
    </div>
    <div class="depreciation-metric metric-impairment">
        <span class="metric-icon"><i class="fa fa-triangle-exclamation"></i></span>
        <div><small>ACC Impairment</small><h4 id="totalImpairment"></h4></div>
    </div>
    <div class="depreciation-metric metric-book">
        <span class="metric-icon"><i class="fa fa-book-open"></i></span>
        <div><small>Net Book Value</small><h4 id="totalNetBook"></h4></div>
    </div>
</div>
</div>
</div>

<!-- POLICY + DETAILS -->
<div class="row">
<div class="col-md-6">

<div class="card mb-3" id="policySection" style="display:none">
<div class="card-header">
    Depreciation Policy – <span id="policyAssetName"></span>
</div>
<div class="card-body">
<div class="depreciation-policy-grid">
    <div class="policy-field">
        <label for="depreciationMethod">Method</label>
        <select id="depreciationMethod" class="form-control form-control-sm" onchange="toggleDepreciationFields()">
        <?php foreach ($depreciation_methods as $m): ?>
        <option value="<?= $m['id'] ?>"><?= $m['method_name'] ?></option>
        <?php endforeach; ?>
        </select>
    </div>
    <div class="policy-field" id="usefulLifeField">
        <label for="usefulLifeYears">Useful Life</label>
        <input id="usefulLifeYears" class="form-control form-control-sm" type="number" placeholder="Years">
    </div>
    <div class="policy-field" id="salvageValueField">
        <label for="salvageValue">Salvage Value</label>
        <input id="salvageValue" class="form-control form-control-sm" type="number" placeholder="0.00">
    </div>
    <div class="policy-field" id="depreciationRateRow" style="display:none;">
        <label for="depreciationRate">Depreciation Rate (%)</label>
        <input id="depreciationRate" class="form-control form-control-sm" type="number" step="0.1" placeholder="e.g. 10">
    </div>
</div>

<button class="btn btn-primary btn-sm depreciation-save-policy" onclick="savePolicy()"><i class="fa fa-floppy-disk"></i> Save Policy</button>

<!-- GRAPH SECTION -->
<div class="mt-4">
    <div class="card">
        <div class="card-header">
            <strong>Net Book Value Trend (Yearly)</strong>
        </div>
        <div class="card-body">
            <canvas id="netBookChart" height="250"></canvas>
        </div>
    </div>
</div>

</div>
</div>

</div>

<div class="col-md-6">

<div class="card mb-3" id="detailsSection" style="display:none">
<div class="card-header">
    Monthly Depreciation
    <div class="float-right">
        <select id="yearFilter" class="form-control form-control-sm d-inline-block w-auto"
                onchange="loadAssetTypeDetails(currentAssetType)">
            <?php for($y=date('Y'); $y>=date('Y')-10; $y--): ?>
                <option value="<?= $y ?>" <?= $y==date('Y')?'selected':'' ?>>
                    <?= $y ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
</div>
<div class="card-body">
<div class="row">
<?php 
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
foreach ($months as $index => $m): ?>
<div class="col-md-3 mb-2">
<small class="text-muted"><?= $m ?></small>
<input class="form-control form-control-sm monthDep" 
       id="month_<?= $index ?>" 
       readonly 
       value="0.00">
</div>
<?php endforeach; ?>
</div>
<hr>

<input id="yearTotal" class="form-control mb-2" readonly placeholder="Total Year Depreciation">
<input id="netBookValue" class="form-control" readonly placeholder="Net Book Value">

</div>
</div>

</div>
</div>

<!-- ASSETS LIST -->
<!-- ASSETS LIST -->
<div class="card mb-3" id="assetsListSection" style="display:none">
    <div class="card-header">
        Assets in <span id="assetsListTitle"></span>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Name</th>
                    <th>Purchase Date</th>
                    <th>Cost</th>
                    <th>Accumulated Dep.</th>
                    <th>Current Year Dep.</th>
                    <th>Net Book</th>
                </tr>
            </thead>
            <tbody id="assetsListBody"></tbody>
        </table>
    </div>
</div>

<!-- PURCHASE -->
<div class="card" id="purchaseSection" style="display:none">
<div class="card-header">Asset Purchases</div>
<div class="card-body">
<table class="table table-bordered table-sm">
<thead>
<tr>
<th>Slip</th><th>Base</th><th>GST</th><th>Total</th><th>Date</th><th>Name</th>
</tr>
</thead>
<tbody id="purchaseBody"></tbody>
</table>
</div>
</div>

</div>

<script>
let currentAssetType = null;
let netBookChart = null;

// Function to create or update yearly chart
function updateYearlyChart(assetsData, selectedYear, assetName) {
    const ctx = document.getElementById('netBookChart');
    if(!ctx) return;
    
    // If no assets, clear chart and return
    if(!assetsData || assetsData.length === 0) {
        if(netBookChart) {
            netBookChart.destroy();
            netBookChart = null;
        }
        return;
    }
    
    // Calculate yearly data
    let yearlyData = calculateYearlyNetBookValues(assetsData, parseInt(selectedYear));
    
    if(yearlyData.length === 0) return;
    
    // Extract years and values for chart
    let years = yearlyData.map(item => item.year);
    let netBookValues = yearlyData.map(item => item.netBookValue);
    
    // If chart already exists, destroy it
    if (netBookChart) {
        netBookChart.destroy();
    }
    
    // Create new chart
    netBookChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: years,
            datasets: [{
                label: `Net Book Value`,
                data: netBookValues,
                backgroundColor: years.map(year => {
                    // Highlight selected year with different color
                    return year == selectedYear ? 'rgba(255, 99, 132, 0.8)' : 'rgba(54, 162, 235, 0.6)';
                }),
                borderColor: years.map(year => {
                    return year == selectedYear ? 'rgba(255, 99, 132, 1)' : 'rgba(54, 162, 235, 1)';
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: `Yearly Net Book Value Trend for ${assetName}`,
                    color: '#dbeafe',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let yearData = yearlyData[context.dataIndex];
                            return [
                                `Year: ${yearData.year}`,
                                `Net Book: ${yearData.netBookValue.toFixed(2)}`,
                                `Total Cost: ${yearData.totalCost.toFixed(2)}`,
                                `Depreciation: ${yearData.totalDepreciation ? yearData.totalDepreciation.toFixed(2) : '0.00'}`
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Net Book Value',
                        color: '#9fb5d3',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#8ea6c7',
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(80, 139, 204, 0.16)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Year',
                        color: '#9fb5d3',
                        font: {
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: '#8ea6c7',
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        color: 'rgba(80, 139, 204, 0.10)'
                    }
                }
            }
        }
    });
}

// Toggle between Straight Line and Reducing Balance fields
function toggleDepreciationFields() {
    let methodSelect = document.getElementById('depreciationMethod');
    let selectedMethod = methodSelect.options[methodSelect.selectedIndex].text;
    
    if (selectedMethod === 'Reducing Balance') {
        // Show depreciation rate, hide useful life and salvage value
        document.getElementById('depreciationRateRow').style.display = 'block';
        document.getElementById('usefulLifeField').style.display = 'none';
        document.getElementById('salvageValueField').style.display = 'none';
        
        // Change placeholder for salvage value field
        document.getElementById('salvageValue').placeholder = 'Depreciation Rate (%)';
    } else {
        // Show useful life and salvage value, hide depreciation rate
        document.getElementById('depreciationRateRow').style.display = 'none';
        document.getElementById('usefulLifeField').style.display = 'block';
        document.getElementById('salvageValueField').style.display = 'block';
        
        // Restore placeholder
        document.getElementById('salvageValue').placeholder = 'Salvage Value';
    }
}

// Update loadAssetTypeDetails function to handle new fields
function loadAssetTypeDetails(id){
    if(!id) return;
    
    currentAssetType = id;
    $('#loadingSpinner').show();
    
    let selectedYear = $('#yearFilter').val();
    
    $.get("<?= site_url('asset_depreciation/get_asset_type_details') ?>",
    {
        asset_type_id: id,
        year: selectedYear
    },
    function(res){
        $('#loadingSpinner').hide();
        if(!res.success) return alert('Failed');
        
        // Show all sections
        $('#summarySection,#policySection,#detailsSection,#purchaseSection,#assetsListSection').show();
        
        // Update summary
        $('#assetTypeName').text(res.asset_type.name);
        $('#policyAssetName').text(res.asset_type.name);
        
        // ================ SUMMARY SECTION ================
        // Yeh ab saare assets ka total dikhayega
        let totalCost = 0;
        let totalAccumulated = 0;
        let totalCurrentYear = 0;
        let totalNetBook = 0;
        
        res.assets.forEach(a => {
            totalCost += parseFloat(a.asset.price_of_purchase) || 0;
            totalAccumulated += a.depreciation.accumulated;
            totalCurrentYear += a.depreciation.current_year;
            totalNetBook += a.depreciation.net_book;
        });
        
        // Summary values - bold and no $ symbol
        $('#assetCount').html('<strong>' + res.assets.length + ' assets</strong>');
        $('#totalCost').html('<strong>' + totalCost.toFixed(2) + '</strong>');
        $('#totalAccumulated').html('<strong>' + totalAccumulated.toFixed(2) + '</strong>');
        $('#totalCurrentYear').html('<strong>' + totalCurrentYear.toFixed(2) + '</strong>');
        $('#totalImpairment').html('<strong>' + res.summary.total_acc_impairment.toFixed(2) + '</strong>');
        $('#totalNetBook').html('<strong>' + totalNetBook.toFixed(2) + '</strong>');
        
        // ================ POLICY SECTION ================
        $('#depreciationMethod').val(res.asset_type.depreciation_method_id);
        $('#usefulLifeYears').val(res.asset_type.useful_life_years);
        $('#salvageValue').val(res.asset_type.salvage_value);
        
        // Toggle fields based on selected method
        toggleDepreciationFields();
        
        // ================ MONTHLY DEPRECIATION SECTION ================
        // Agar multiple assets hain, toh first asset ka monthly depreciation dikhao
        // Ya phir total monthly calculation karo - aapki requirement ke hisaab se
        let monthlyArray = [0,0,0,0,0,0,0,0,0,0,0,0];
        
        if(res.assets.length > 0 && res.assets[0].depreciation.monthly_array){
            monthlyArray = res.assets[0].depreciation.monthly_array;
        }
        
        // Set each month individually
        for(let i=0; i<12; i++){
            $('#month_'+i).val(monthlyArray[i].toFixed(2));
        }
        
        // Update year total and net book
        $('#yearTotal').val('Total Year: ' + totalCurrentYear.toFixed(2));
        $('#netBookValue').val('Net Book: ' + totalNetBook.toFixed(2));
        
        // ================ UPDATE YEARLY CHART ================
        updateYearlyChart(res.assets, parseInt(selectedYear), res.asset_type.name);
        
        // ================ ASSETS LIST SECTION ================
        // Har asset ke liye depreciation details dikhao
        $('#assetsListTitle').text(res.asset_type.name);
        let assetsHtml = '';
        
        res.assets.forEach(a => {
            assetsHtml += `
                <tr>
                    <td>${a.asset.equipment_id}</td>
                    <td>${a.asset.equipment_name}</td>
                    <td>${a.asset.purchase_date}</td>
                    <td>${parseFloat(a.asset.price_of_purchase||0).toFixed(2)}</td>
                    <td>${a.depreciation.accumulated.toFixed(2)}</td>
                    <td>${a.depreciation.current_year.toFixed(2)}</td>
                    <td>${a.depreciation.net_book.toFixed(2)}</td>
                </tr>
            `;
        });
        $('#assetsListBody').html(assetsHtml);
        
        // ================ PURCHASE HISTORY SECTION ================
        let purchaseHtml = '';
        res.assets.forEach(a => {
            let price = parseFloat(a.asset.price_of_purchase) || 0;
            let gst   = price * 0.1;
            let total = gst + price;
            
            purchaseHtml += `
                <tr>
                    <td>DS-${a.asset.equipment_id}</td>
                    <td>${price.toFixed(2)}</td>
                    <td>${gst.toFixed(2)}</td>
                    <td>${total.toFixed(2)}</td>
                    <td>${a.asset.purchase_date}</td>
                    <td>${a.asset.equipment_name}</td>
                </tr>
            `;
        });
        $('#purchaseBody').html(purchaseHtml);
        
    },'json').fail(function(jqXHR, textStatus, errorThrown) {
        $('#loadingSpinner').hide();
        console.error('AJAX Error:', textStatus, errorThrown);
        alert('Error loading data. Please check console for details.');
    });
}

// Update savePolicy function
function savePolicy(){
    let methodSelect = document.getElementById('depreciationMethod');
    let selectedMethod = methodSelect.options[methodSelect.selectedIndex].text;

    let data = {
        asset_type_id: currentAssetType,
        depreciation_method_id: $('#depreciationMethod').val(),
        useful_life_years: null,
        salvage_value: null,
        depreciate_value: null
    };

    if (selectedMethod === 'Reducing Balance') {
        data.depreciate_value = $('#depreciationRate').val(); // % rate
    } else {
        data.useful_life_years = $('#usefulLifeYears').val();
        data.salvage_value     = $('#salvageValue').val();
    }

    $.post("<?= site_url('asset_depreciation/update_depreciation_policy') ?>",
        data,
        res => {
            alert(res.message);
            if(currentAssetType){
                loadAssetTypeDetails(currentAssetType);
            }
        },
        'json'
    );
}

// In the chart update function
function calculateYearlyNetBookValues(assets, selectedYear) {
    if(!assets || assets.length === 0) return [];
    
    let yearlyData = [];
    
    // Find min and max years
    let minYear = Infinity;
    let maxYear = -Infinity;
    
    assets.forEach(assetData => {
        let asset = assetData.asset;
        let purchaseDate = new Date(asset.purchase_date);
        let purchaseYear = purchaseDate.getFullYear();
        let depreciationMethod = asset.depreciation_method || 'Straight Line';
        
        if(depreciationMethod === 'Reducing Balance') {
            minYear = Math.min(minYear, purchaseYear - 2);
            maxYear = Math.max(maxYear, purchaseYear + 10);
        } else {
            let usefulLife = parseInt(asset.useful_life_years) || 1;
            minYear = Math.min(minYear, purchaseYear - 2);
            maxYear = Math.max(maxYear, purchaseYear + usefulLife + 2);
        }
    });
    
    // Calculate for each year
    for(let year = minYear; year <= maxYear; year++) {
        let totalNetBook = 0;
        let totalCost = 0;
        let totalDepreciation = 0;
        let accumulatedDepreciation = 0;
        
        assets.forEach(assetData => {
            let asset = assetData.asset;
            let purchaseDate = new Date(asset.purchase_date);
            let purchaseYear = purchaseDate.getFullYear();
            let cost = parseFloat(asset.price_of_purchase) || 0;
            let depreciationMethod = asset.depreciation_method || 'Straight Line';
            
            if(year < purchaseYear) {
                totalNetBook += 0;
            } else if(depreciationMethod === 'Reducing Balance') {
                let rate = parseFloat(asset.depreciate_value) / 100 || 0.1;
                let yearsPassed = year - purchaseYear;
                let remainingValue = cost;
                accumulatedDepreciation = 0;
                
                for(let i = 0; i < yearsPassed; i++) {
                    let yearDep = remainingValue * rate;
                    accumulatedDepreciation += yearDep;
                    remainingValue = remainingValue - yearDep;
                }
                
                // Add current year depreciation if we're at the current year
                if (yearsPassed >= 0) {
                    let currentYearDep = remainingValue * rate;
                    accumulatedDepreciation += currentYearDep;
                    remainingValue = remainingValue - currentYearDep;
                }
                
                totalNetBook += Math.max(0, remainingValue);
                totalCost += cost;
                totalDepreciation += accumulatedDepreciation;
                
            } else {
                // STRAIGHT LINE - CUMULATIVE CALCULATION
                let salvage = parseFloat(asset.salvage_value) || 0;
                let usefulLife = parseInt(asset.useful_life_years) || 1;
                let annualDep = (cost - salvage) / usefulLife;
                let monthlyDep = annualDep / 12;
                
                // Calculate months from purchase to end of current year
                let purchaseMonth = purchaseDate.getMonth() + 1; // JavaScript months are 0-based
                let totalMonths = 0;
                
                if (year > purchaseYear) {
                    // First year (partial)
                    totalMonths += (12 - purchaseMonth + 1);
                    // Full years in between
                    totalMonths += Math.min((year - purchaseYear - 1), usefulLife - 1) * 12;
                    // Current year (full or partial)
                    if (year <= purchaseYear + usefulLife - 1) {
                        totalMonths += 12; // Full year
                    } else if (year == purchaseYear + usefulLife) {
                        // Last year - might be partial
                        totalMonths += (purchaseMonth - 1);
                    }
                } else if (year == purchaseYear) {
                    // Same year purchase
                    totalMonths += (12 - purchaseMonth + 1);
                }
                
                // Cap at useful life
                totalMonths = Math.min(totalMonths, usefulLife * 12);
                
                accumulatedDepreciation = monthlyDep * totalMonths;
                totalNetBook += Math.max(salvage, cost - accumulatedDepreciation);
                totalCost += cost;
                totalDepreciation += accumulatedDepreciation;
            }
        });
        
        yearlyData.push({
            year: year,
            netBookValue: totalNetBook,
            totalCost: totalCost,
            totalDepreciation: totalDepreciation,
            isSelectedYear: year == selectedYear
        });
    }
    
    return yearlyData;
}

// Initialize when page loads
$(document).ready(function() {
    console.log('Page loaded, chart.js ready');
    
    // Initialize the depreciation fields toggle on page load
    toggleDepreciationFields();
});
</script>
