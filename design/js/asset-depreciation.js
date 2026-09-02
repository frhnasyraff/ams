// Asset Depreciation Management Application
const DepreciationApp = {
    // Configuration
    config: {
        apiUrl: '<?php echo site_url("asset_depreciation"); ?>',
        currentView: 'list', 
        editMode: false,
        currentRecordId: null,
        deleteRecordId: null
    },

    // Sample Data (Static for now)
    data: {
        depreciations: [
            {
                id: 1,
                asset_id: 'ASSET-00123',
                asset_name: 'Dell XPS 15 Laptop',
                asset_type: 'IT Equipment',
                purchase_value: '1500.00',
                current_value: '750.00',
                depreciation_method: 'Straight Line',
                annual_depreciation: '300.00',
                accumulated_depreciation: '750.00',
                remaining_value: '750.00',
                purchase_date: '2023-01-15',
                useful_life: '5 years',
                status: 'active',
                notes: 'Laptop for development team'
            },
            {
                id: 2,
                asset_id: 'ASSET-00124',
                asset_name: 'Herman Miller Chair',
                asset_type: 'Office Furniture',
                purchase_value: '1200.00',
                current_value: '840.00',
                depreciation_method: 'Straight Line',
                annual_depreciation: '120.00',
                accumulated_depreciation: '360.00',
                remaining_value: '840.00',
                purchase_date: '2022-03-20',
                useful_life: '10 years',
                status: 'active',
                notes: 'Ergonomic office chair'
            },
            {
                id: 3,
                asset_id: 'ASSET-00125',
                asset_name: 'HP LaserJet Pro',
                asset_type: 'IT Equipment',
                purchase_value: '800.00',
                current_value: '320.00',
                depreciation_method: 'Declining Balance',
                annual_depreciation: '160.00',
                accumulated_depreciation: '480.00',
                remaining_value: '320.00',
                purchase_date: '2021-06-10',
                useful_life: '5 years',
                status: 'active',
                notes: 'Office printer'
            },
            {
                id: 4,
                asset_id: 'ASSET-00126',
                asset_name: 'Toyota Forklift',
                asset_type: 'Machinery',
                purchase_value: '25000.00',
                current_value: '15000.00',
                depreciation_method: 'Straight Line',
                annual_depreciation: '2500.00',
                accumulated_depreciation: '10000.00',
                remaining_value: '15000.00',
                purchase_date: '2020-08-15',
                useful_life: '10 years',
                status: 'active',
                notes: 'Warehouse forklift'
            },
            {
                id: 5,
                asset_id: 'ASSET-00127',
                asset_name: 'Apple iPhone 12',
                asset_type: 'IT Equipment',
                purchase_value: '1000.00',
                current_value: '300.00',
                depreciation_method: 'Sum of Years Digits',
                annual_depreciation: '233.33',
                accumulated_depreciation: '700.00',
                remaining_value: '300.00',
                purchase_date: '2022-01-30',
                useful_life: '3 years',
                status: 'completed',
                notes: 'Company mobile phone'
            }
        ],
        
        // Options for dropdowns
        options: {
            assets: [
                { id: 'ASSET-00123', name: 'Dell XPS 15 Laptop' },
                { id: 'ASSET-00124', name: 'Herman Miller Chair' },
                { id: 'ASSET-00125', name: 'HP LaserJet Pro' },
                { id: 'ASSET-00126', name: 'Toyota Forklift' },
                { id: 'ASSET-00127', name: 'Apple iPhone 12' },
                { id: 'ASSET-00128', name: 'Canon Camera' },
                { id: 'ASSET-00129', name: 'Microsoft Surface Pro' }
            ],
            depreciationMethods: [
                { value: 'straight_line', label: 'Straight Line' },
                { value: 'declining_balance', label: 'Declining Balance' },
                { value: 'sum_of_years_digits', label: 'Sum of Years Digits' }
            ],
            statusOptions: [
                { value: 'active', label: 'Active' },
                { value: 'completed', label: 'Completed' },
                { value: 'on_hold', label: 'On Hold' }
            ],
            usefulLifeYears: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
        }
    },

    // Initialize the application
    init: function() {
        this.showLoading(false);
        this.renderListView();
        this.populateDropdowns();
        this.updateSummaryCards();
    },

    // Show/hide loading spinner
    showLoading: function(show) {
        document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
    },

    // Switch to list view
    showListView: function() {
        this.config.currentView = 'list';
        this.config.editMode = false;
        document.getElementById('listView').style.display = 'block';
        document.getElementById('formView').style.display = 'none';
        this.renderListView();
    },

    // Switch to form view
    showFormView: function(recordId = null) {
        this.config.currentView = 'form';
        document.getElementById('listView').style.display = 'none';
        document.getElementById('formView').style.display = 'block';
        
        if (recordId) {
            this.config.editMode = true;
            this.config.currentRecordId = recordId;
            document.getElementById('formTitle').textContent = 'Edit Depreciation Schedule';
            document.getElementById('formSubtitle').textContent = 'Edit Record';
            document.getElementById('saveBtnText').textContent = 'Update Schedule';
            this.loadRecordForEdit(recordId);
        } else {
            this.config.editMode = false;
            this.config.currentRecordId = null;
            document.getElementById('formTitle').textContent = 'Add Depreciation Schedule';
            document.getElementById('formSubtitle').textContent = 'Add New';
            document.getElementById('saveBtnText').textContent = 'Save Depreciation Schedule';
            this.resetForm();
        }
    },

    // Render list view with data
    renderListView: function() {
        const searchTerm = document.getElementById('searchInput')?.value?.toLowerCase() || '';
        const statusFilter = document.getElementById('statusFilter')?.value || 'all';
        
        let filteredData = this.data.depreciations;
        
        // Apply search filter
        if (searchTerm) {
            filteredData = filteredData.filter(item => 
                item.asset_id.toLowerCase().includes(searchTerm) ||
                item.asset_name.toLowerCase().includes(searchTerm) ||
                item.asset_type.toLowerCase().includes(searchTerm)
            );
        }
        
        // Apply status filter
        if (statusFilter !== 'all') {
            filteredData = filteredData.filter(item => item.status === statusFilter);
        }
        
        const tableBody = document.getElementById('depreciationTableBody');
        const tableInfo = document.getElementById('tableInfo');
        
        if (filteredData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="13" class="text-center text-muted">
                        <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                        No depreciation records found
                    </td>
                </tr>
            `;
            tableInfo.textContent = 'No records found';
            return;
        }
        
        // Generate table rows
        let rowsHtml = '';
        filteredData.forEach(item => {
            const statusClass = this.getStatusClass(item.status);
            rowsHtml += `
                <tr>
                    <td><strong>${item.asset_id}</strong></td>
                    <td>${item.asset_name}</td>
                    <td>${item.asset_type}</td>
                    <td>
                        <span class="font-weight-bold text-success">
                            $${parseFloat(item.purchase_value).toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <span class="font-weight-bold text-primary">
                            $${parseFloat(item.current_value).toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-light">
                            ${item.depreciation_method}
                        </span>
                    </td>
                    <td>
                        <span class="text-warning">
                            $${parseFloat(item.annual_depreciation).toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <span class="text-danger">
                            $${parseFloat(item.accumulated_depreciation).toFixed(2)}
                        </span>
                    </td>
                    <td>
                        <span class="font-weight-bold text-info">
                            $${parseFloat(item.remaining_value).toFixed(2)}
                        </span>
                    </td>
                    <td>${item.purchase_date}</td>
                    <td>${item.useful_life}</td>
                    <td>
                        <span class="badge ${statusClass}">
                            ${this.capitalizeFirstLetter(item.status)}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="DepreciationApp.showFormView(${item.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a class="dropdown-item" href="#" onclick="DepreciationApp.viewDetails(${item.id})">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="DepreciationApp.showDeleteModal(${item.id}, '${item.asset_name}')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = rowsHtml;
        tableInfo.textContent = `Showing ${filteredData.length} of ${this.data.depreciations.length} entries`;
        this.updateSummaryCards(filteredData);
    },

    // Get status class for badge
    getStatusClass: function(status) {
        switch(status) {
            case 'active': return 'badge-success';
            case 'completed': return 'badge-info';
            case 'on_hold': return 'badge-warning';
            default: return 'badge-secondary';
        }
    },

    // Capitalize first letter
    capitalizeFirstLetter: function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },

    // Update summary cards
    updateSummaryCards: function(data = null) {
        const depreciations = data || this.data.depreciations;
        
        let totalCurrentValue = 0;
        let totalAnnualDepreciation = 0;
        let totalAccumulatedDepreciation = 0;
        
        depreciations.forEach(item => {
            totalCurrentValue += parseFloat(item.current_value);
            totalAnnualDepreciation += parseFloat(item.annual_depreciation);
            totalAccumulatedDepreciation += parseFloat(item.accumulated_depreciation);
        });
        
        document.getElementById('totalCurrentValue').textContent = `$${totalCurrentValue.toFixed(2)}`;
        document.getElementById('totalAnnualDep').textContent = `$${totalAnnualDepreciation.toFixed(2)}`;
        document.getElementById('totalAccumulated').textContent = `$${totalAccumulatedDepreciation.toFixed(2)}`;
        document.getElementById('totalAssets').textContent = depreciations.length;
    },

    // Populate dropdowns with options
    populateDropdowns: function() {
        // Asset dropdown
        const assetSelect = document.getElementById('assetId');
        let assetOptions = '<option value="">Select Asset</option>';
        this.data.options.assets.forEach(asset => {
            assetOptions += `<option value="${asset.id}">${asset.name} (${asset.id})</option>`;
        });
        assetSelect.innerHTML = assetOptions;
        
        // Depreciation method dropdown
        const methodSelect = document.getElementById('depreciationMethod');
        let methodOptions = '<option value="">Select Method</option>';
        this.data.options.depreciationMethods.forEach(method => {
            methodOptions += `<option value="${method.value}">${method.label}</option>`;
        });
        methodSelect.innerHTML = methodOptions;
        
        // Useful life dropdown
        const usefulLifeSelect = document.getElementById('usefulLife');
        let lifeOptions = '<option value="">Select Years</option>';
        this.data.options.usefulLifeYears.forEach(year => {
            lifeOptions += `<option value="${year}">${year} year${year > 1 ? 's' : ''}</option>`;
        });
        usefulLifeSelect.innerHTML = lifeOptions;
        
        // Status dropdown
        const statusSelect = document.getElementById('status');
        let statusOptions = '';
        this.data.options.statusOptions.forEach(status => {
            statusOptions += `<option value="${status.value}">${status.label}</option>`;
        });
        statusSelect.innerHTML = statusOptions;
    },

    // Load record for editing
    loadRecordForEdit: function(recordId) {
        const record = this.data.depreciations.find(item => item.id === recordId);
        if (!record) return;
        
        document.getElementById('recordId').value = record.id;
        document.getElementById('assetId').value = record.asset_id;
        document.getElementById('depreciationMethod').value = record.depreciation_method.toLowerCase().replace(/ /g, '_');
        document.getElementById('purchaseValue').value = record.purchase_value;
        document.getElementById('purchaseDate').value = record.purchase_date;
        
        // Extract years from useful_life string (e.g., "5 years" -> 5)
        const yearsMatch = record.useful_life.match(/\d+/);
        document.getElementById('usefulLife').value = yearsMatch ? yearsMatch[0] : '';
        
        document.getElementById('salvageValue').value = '0'; // Default
        document.getElementById('status').value = record.status;
        document.getElementById('notes').value = record.notes || '';
        
        // Set preview
        document.getElementById('previewAnnual').textContent = `$${record.annual_depreciation}`;
        document.getElementById('previewMonthly').textContent = `$${(parseFloat(record.annual_depreciation) / 12).toFixed(2)}`;
        document.getElementById('previewTotal').textContent = `$${record.accumulated_depreciation}`;
        document.getElementById('previewEndValue').textContent = `$${record.remaining_value}`;
    },

    // Reset form
    resetForm: function() {
        document.getElementById('recordId').value = '';
        document.getElementById('depreciationForm').reset();
        
        // Set default values
        document.getElementById('purchaseDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('salvageValue').value = '0';
        document.getElementById('status').value = 'active';
        
        // Reset preview
        document.getElementById('previewAnnual').textContent = '$0.00';
        document.getElementById('previewMonthly').textContent = '$0.00';
        document.getElementById('previewTotal').textContent = '$0.00';
        document.getElementById('previewEndValue').textContent = '$0.00';
        
        // Remove validation classes
        const formControls = document.querySelectorAll('#depreciationForm .form-control');
        formControls.forEach(control => {
            control.classList.remove('is-invalid');
        });
    },

    // Preview calculation
    previewCalculation: function() {
        const purchaseValue = parseFloat(document.getElementById('purchaseValue').value) || 0;
        const salvageValue = parseFloat(document.getElementById('salvageValue').value) || 0;
        const usefulLife = parseInt(document.getElementById('usefulLife').value) || 1;
        const method = document.getElementById('depreciationMethod').value;
        
        if (!purchaseValue || !usefulLife || !method) {
            this.showAlert('Please fill in all required fields for calculation', 'warning');
            return;
        }
        
        let annualDepreciation = 0;
        
        switch(method) {
            case 'straight_line':
                annualDepreciation = (purchaseValue - salvageValue) / usefulLife;
                break;
                
            case 'declining_balance':
                const rate = 2 / usefulLife;
                annualDepreciation = purchaseValue * rate;
                break;
                
            case 'sum_of_years_digits':
                const sumOfYears = (usefulLife * (usefulLife + 1)) / 2;
                annualDepreciation = (purchaseValue - salvageValue) * (usefulLife / sumOfYears);
                break;
        }
        
        const monthlyDepreciation = annualDepreciation / 12;
        const totalDepreciation = annualDepreciation * usefulLife;
        const endValue = Math.max(purchaseValue - totalDepreciation, salvageValue);
        
        // Update preview
        document.getElementById('previewAnnual').textContent = `$${annualDepreciation.toFixed(2)}`;
        document.getElementById('previewMonthly').textContent = `$${monthlyDepreciation.toFixed(2)}`;
        document.getElementById('previewTotal').textContent = `$${totalDepreciation.toFixed(2)}`;
        document.getElementById('previewEndValue').textContent = `$${endValue.toFixed(2)}`;
    },

    // Save depreciation record
    saveDepreciation: function() {
        if (!this.validateForm()) {
            return;
        }
        
        // For now, simulate AJAX call
        this.showLoading(true);
        
        setTimeout(() => {
            const formData = this.getFormData();
            
            if (this.config.editMode) {
                // Update existing record
                const index = this.data.depreciations.findIndex(item => item.id === this.config.currentRecordId);
                if (index !== -1) {
                    this.data.depreciations[index] = {
                        ...this.data.depreciations[index],
                        ...formData,
                        id: this.config.currentRecordId
                    };
                }
                this.showAlert('Depreciation schedule updated successfully!', 'success');
            } else {
                // Add new record
                const newId = Math.max(...this.data.depreciations.map(item => item.id)) + 1;
                this.data.depreciations.push({
                    id: newId,
                    ...formData
                });
                this.showAlert('Depreciation schedule saved successfully!', 'success');
            }
            
            this.showLoading(false);
            this.showListView();
        }, 1000);
    },

    // Get form data
    getFormData: function() {
        const purchaseValue = parseFloat(document.getElementById('purchaseValue').value);
        const annualDepreciation = parseFloat(document.getElementById('previewAnnual').textContent.replace('$', ''));
        
        return {
            asset_id: document.getElementById('assetId').value,
            asset_name: this.data.options.assets.find(a => a.id === document.getElementById('assetId').value)?.name || 'Unknown',
            asset_type: 'IT Equipment', // In real app, get from asset data
            purchase_value: purchaseValue.toFixed(2),
            current_value: document.getElementById('previewEndValue').textContent.replace('$', ''),
            depreciation_method: document.getElementById('depreciationMethod').options[document.getElementById('depreciationMethod').selectedIndex].text,
            annual_depreciation: annualDepreciation.toFixed(2),
            accumulated_depreciation: document.getElementById('previewTotal').textContent.replace('$', ''),
            remaining_value: document.getElementById('previewEndValue').textContent.replace('$', ''),
            purchase_date: document.getElementById('purchaseDate').value,
            useful_life: document.getElementById('usefulLife').value + ' years',
            status: document.getElementById('status').value,
            notes: document.getElementById('notes').value
        };
    },

    // Validate form
    validateForm: function() {
        let isValid = true;
        
        const requiredFields = [
            'assetId',
            'depreciationMethod',
            'purchaseValue',
            'purchaseDate',
            'usefulLife'
        ];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            this.showAlert('Please fill in all required fields', 'warning');
        }
        
        return isValid;
    },

    // View details (could show in modal)
    viewDetails: function(recordId) {
        const record = this.data.depreciations.find(item => item.id === recordId);
        if (!record) return;
        
        const detailsHtml = `
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Depreciation Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Asset ID:</strong> ${record.asset_id}</p>
                            <p><strong>Asset Name:</strong> ${record.asset_name}</p>
                            <p><strong>Asset Type:</strong> ${record.asset_type}</p>
                            <p><strong>Purchase Date:</strong> ${record.purchase_date}</p>
                            <p><strong>Useful Life:</strong> ${record.useful_life}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Purchase Value:</strong> $${record.purchase_value}</p>
                            <p><strong>Current Value:</strong> $${record.current_value}</p>
                            <p><strong>Annual Depreciation:</strong> $${record.annual_depreciation}</p>
                            <p><strong>Accumulated Depreciation:</strong> $${record.accumulated_depreciation}</p>
                            <p><strong>Remaining Value:</strong> $${record.remaining_value}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Depreciation Method:</strong> ${record.depreciation_method}</p>
                            <p><strong>Status:</strong> <span class="badge ${this.getStatusClass(record.status)}">${this.capitalizeFirstLetter(record.status)}</span></p>
                            ${record.notes ? `<p><strong>Notes:</strong> ${record.notes}</p>` : ''}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="DepreciationApp.showFormView(${record.id})" data-dismiss="modal">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        `;
        
        // Create and show modal
        const modalId = 'detailsModal';
        let modal = document.getElementById(modalId);
        if (!modal) {
            modal = document.createElement('div');
            modal.id = modalId;
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('role', 'dialog');
            document.body.appendChild(modal);
        }
        
        modal.innerHTML = detailsHtml;
        $(modal).modal('show');
    },

    // Show delete confirmation modal
    showDeleteModal: function(recordId, assetName) {
        this.config.deleteRecordId = recordId;
        document.getElementById('deleteAssetName').textContent = assetName;
        $('#deleteModal').modal('show');
    },

    // Confirm delete
    confirmDelete: function() {
        if (!this.config.deleteRecordId) return;
        
        const index = this.data.depreciations.findIndex(item => item.id === this.config.deleteRecordId);
        if (index !== -1) {
            this.data.depreciations.splice(index, 1);
            this.showAlert('Depreciation record deleted successfully!', 'success');
            this.renderListView();
            this.updateSummaryCards();
        }
        
        $('#deleteModal').modal('hide');
        this.config.deleteRecordId = null;
    },

    // Calculate depreciation (for calculator modal)
    calculateDepreciation: function() {
        const purchaseValue = parseFloat(document.getElementById('calcPurchaseValue').value) || 0;
        const salvageValue = parseFloat(document.getElementById('calcSalvageValue').value) || 0;
        const usefulLife = parseInt(document.getElementById('calcUsefulLife').value) || 1;
        const method = document.getElementById('calcMethod').value;
        
        if (!purchaseValue || !usefulLife) {
            this.showAlert('Please enter purchase value and useful life', 'warning');
            return;
        }
        
        let annualDepreciation = 0;
        
        switch(method) {
            case 'straight_line':
                annualDepreciation = (purchaseValue - salvageValue) / usefulLife;
                break;
                
            case 'declining_balance':
                const rate = 2 / usefulLife;
                annualDepreciation = purchaseValue * rate;
                break;
                
            case 'sum_of_years_digits':
                const sumOfYears = (usefulLife * (usefulLife + 1)) / 2;
                annualDepreciation = (purchaseValue - salvageValue) * (usefulLife / sumOfYears);
                break;
        }
        
        const monthlyDepreciation = annualDepreciation / 12;
        const totalDepreciation = annualDepreciation * usefulLife;
        
        document.getElementById('annualResult').textContent = annualDepreciation.toFixed(2);
        document.getElementById('monthlyResult').textContent = monthlyDepreciation.toFixed(2);
        document.getElementById('totalResult').textContent = totalDepreciation.toFixed(2);
        document.getElementById('calculationResult').style.display = 'block';
    },

    // Filter data
    filterData: function() {
        this.renderListView();
    },

    // Reset filters
    resetFilters: function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = 'all';
        this.renderListView();
    },

    // Show alert message
    showAlert: function(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        }[type] || 'fa-info-circle';
        
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show" 
                 style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        document.querySelectorAll('.alert-dismissible[style*="position: fixed"]').forEach(el => el.remove());
        
        // Add new alert
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const alertEl = document.getElementById(alertId);
            if (alertEl) {
                alertEl.remove();
            }
        }, 5000);
    }
};

// Global functions for onclick events
function showListView() {
    DepreciationApp.showListView();
}

function showFormView(recordId = null) {
    DepreciationApp.showFormView(recordId);
}

function previewCalculation() {
    DepreciationApp.previewCalculation();
}

function saveDepreciation() {
    DepreciationApp.saveDepreciation();
}

function filterData() {
    DepreciationApp.filterData();
}

function resetFilters() {
    DepreciationApp.resetFilters();
}

function calculateDepreciation() {
    DepreciationApp.calculateDepreciation();
}

function confirmDelete() {
    DepreciationApp.confirmDelete();
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    DepreciationApp.init();
});

