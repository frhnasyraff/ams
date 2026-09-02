<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

<?php
// asset-disposal-request-form.php के शुरुआत में ये checks add करें
$edit_id = isset($edit_id) ? $edit_id : null;
$request = isset($request) ? $request : null;
$assets = isset($assets) ? $assets : [];
$write_off_reasons = isset($write_off_reasons) ? $write_off_reasons : [];
?>

<style>
    .write-off-container {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .section-title {
        color: #133C81;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid #133C81;
    }
    
    .form-control, .select2-container .select2-selection {
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        height: 40px;
    }
    
    .form-control:focus {
        border-color: #133C81;
        box-shadow: 0 0 0 0.2rem rgba(19, 60, 129, 0.25);
    }
    
    textarea.form-control {
        height: 120px;
        resize: vertical;
    }
    
    .upload-area {
        border: 2px dashed #133C81;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        background-color: #e9ecef;
        border-color: #0d2c63;
    }
    
    .upload-icon {
        font-size: 48px;
        color: #133C81;
        margin-bottom: 15px;
    }
    
    .workflow-step {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #133C81;
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #133C81;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: bold;
    }
    
    .step-content {
        flex: 1;
    }
    
    .step-title {
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    
    .step-status {
        font-size: 14px;
        color: #666;
    }
    
    .status-pending {
        color: #ffc107;
        font-weight: 600;
    }
    
    .status-completed {
        color: #28a745;
        font-weight: 600;
    }
    
    .status-awaiting {
        color: #6c757d;
        font-weight: 600;
    }
    
    .btn-submit {
        background-color: #133C81;
        color: white;
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 5px;
        border: none;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background-color: #0d2c63;
        color: white;
    }
    
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }
    
    .asset-info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border-left: 4px solid #133C81;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 8px;
    }
    
    .info-label {
        width: 150px;
        font-weight: 600;
        color: #666;
    }
    
    .info-value {
        flex: 1;
        color: #333;
    }
    
    .hidden {
        display: none;
    }
</style>

<div class="container-fluid disposal-request-page">
    <div class="write-off-container disposal-request-shell">
        <div class="disposal-request-hero">
            <div class="disposal-request-heading">
                <span class="disposal-request-icon"><i class="fas fa-file-signature"></i></span>
                <div>
                    <span class="disposal-request-eyebrow">Asset Lifecycle</span>
                    <h3><?php echo $edit_id ? 'Edit Asset Write-Off Request' : 'New Asset Write-Off Request'; ?></h3>
                    <?php if($request && isset($request->request_number)): ?>
                        <p>Request ID: <strong><?php echo $request->request_number; ?></strong></p>
                    <?php else: ?>
                        <p>Document the asset, write-off reason and supporting evidence for review.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="disposal-request-hero-action">
                <a href="<?php echo site_url('asset_disposals'); ?>" class="btn btn-cancel">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <form id="writeOffForm" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id" value="<?php echo $request ? $request->id : ''; ?>">
            <input type="hidden" id="status" name="status" value="<?php echo $request ? $request->status : 'new'; ?>">
            
            <!-- Section 1: Asset Identification -->
            <div class="form-section disposal-form-section">
                <h5 class="section-title"><i class="fas fa-cube"></i> Asset Identification</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="equipment_asset_id">Select Asset *</label>
                            <select id="equipment_asset_id" name="equipment_asset_id" class="form-control select2" required>
                                <option value="">Search for Asset (e.g. MacBook Pro 16)</option>
                                <?php if(!empty($assets)): ?>
                                    <?php foreach($assets as $asset): ?>
                                        <option value="<?php echo $asset->equipment_id; ?>" 
                                            <?php echo ($request && $request->equipment_asset_id == $asset->equipment_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($asset->equipment_name) . ' (' . $asset->equipment_id . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No assets available</option>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select an asset</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="serial_number">Serial Number</label>
                            <input type="text" id="serial_number" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                
                <div id="assetDetails" class="asset-info-card hidden">
                    <div class="info-row">
                        <div class="info-label">Asset Tag:</div>
                        <div class="info-value" id="assetTagDisplay"></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Serial Number:</div>
                        <div class="info-value" id="serialNumberDisplay"></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Asset Name:</div>
                        <div class="info-value" id="assetNameDisplay"></div>
                    </div>
                </div>
            </div>
            
            <!-- Section 2: Write-Off Details -->
            <div class="form-section disposal-form-section">
                <h5 class="section-title"><i class="fas fa-clipboard-list"></i> Write-Off Details</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="write_off_reason_id">Reason for Write-Off *</label>
                            <select id="write_off_reason_id" name="write_off_reason_id" class="form-control" required>
                                <option value="">Select a reason</option>
                                <?php if(!empty($write_off_reasons)): ?>
                                    <?php foreach($write_off_reasons as $reason): ?>
                                        <option value="<?php echo $reason->id; ?>" 
                                            <?php echo ($request && $request->write_off_reason_id == $reason->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($reason->write_off_reason ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select a reason</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estimated_value">Estimated Write-Off Value</label>
                            <div class="input-group">                               
                                <input type="number" step="0.01" min="0" id="estimated_value" 
                                name="estimated_value" class="form-control"
                                value="<?php echo $request ? $request->estimated_value : ''; ?>" placeholder="Enter estimated value">
                            </div>
                            <small class="form-text text-muted">Enter 0.00 if no value</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="disposal_method_id">Disposal Method *</label>
                            <select id="disposal_method_id" name="disposal_method_id" class="form-control" required>
                                <option value="">Select Disposal Method</option>
                                <?php if(!empty($disposal_methods)): ?>
                                    <?php foreach($disposal_methods as $dm): ?>
                                        <option value="<?= $dm->id ?>" 
                                            <?= ($request && $request->disposal_method_id == $dm->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dm->disposal_method) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Please select disposal method</div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label for="justification">Detailed Justification *</label>
                    <textarea id="justification" name="justification" class="form-control" required 
                              placeholder="Provide a clear explanation for the write-off..."><?php echo $request ? $request->justification : ''; ?></textarea>
                    <div class="invalid-feedback">Please provide detailed justification</div>
                </div>
            </div>
            
            <!-- Section 3: Supporting Documentation -->
            <div class="form-section disposal-form-section">
                <h5 class="section-title"><i class="fas fa-paperclip"></i> Supporting Documentation</h5>
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h5>Click to upload or drag and drop</h5>
                    <p class="text-muted">Photos, PDF, Word or Excel files (Max 20MB)</p>
                    <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                    <?php if($request && $request->attachment): ?>
                        <div class="mt-3">
                            <p>Current file: <?php echo basename($request->attachment); ?></p>
                            <a href="<?php echo site_url('asset_disposal_requests/download_attachment/' . $request->id); ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="fileName" class="mt-2 text-center" style="display: none;"></div>
            </div>
            
            <!-- Buttons -->
            <div class="form-section disposal-form-actions">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php if($request && $request->status == 'new'): ?>
                            <button type="button" id="saveDraft" class="btn btn-outline-secondary">
                                <i class="fas fa-save"></i> Save Draft
                            </button>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if($request): ?>
                            <!-- Show Approve/Reject buttons for existing requests -->
                            <button type="button" class="btn btn-success mr-2" id="approveBtn" data-id="<?= $request->id ?>">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger mr-2" id="rejectBtn" data-id="<?= $request->id ?>">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" id="cancelBtn" class="btn btn-cancel mr-2">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" id="submitBtn" class="btn btn-submit">
                            <?php if($request && $request->status == 'draft'): ?>
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            <?php elseif($request): ?>
                                <i class="fas fa-save"></i> Save Changes
                            <?php else: ?>
                                <i class="fas fa-save"></i> Submit
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // URLs
    const urls = {
        save: '<?php echo site_url("asset_disposal_requests/save"); ?>',
        save_changes: '<?php echo site_url("asset_disposal_requests/save_changes"); ?>',
        getAssetDetails: '<?php echo site_url("asset_disposal_requests/get_asset_details"); ?>',
        change_status: '<?php echo site_url("asset_disposal_requests/change_status_ajax"); ?>'
    };
    
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        placeholder: 'Search for Asset...'
    });
    
    // When asset is selected
    $('#equipment_asset_id').on('change', function() {
        const assetId = $(this).val();
        
        if (assetId) {
            $.ajax({
                url: urls.getAssetDetails,
                type: 'POST',
                data: { asset_id: assetId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#serial_number').val(response.serial_number);
                        $('#assetDetails').removeClass('hidden');
                        $('#assetTagDisplay').text(response.asset_tag);
                        $('#serialNumberDisplay').text(response.serial_number);
                        $('#assetNameDisplay').text(response.asset_name);
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to load asset details');
                }
            });
        } else {
            $('#assetDetails').addClass('hidden');
            $('#serial_number').val('');
        }
    });
    
    // File upload
    $('#uploadArea').click(function(event) {
        if (!$(event.target).is('#attachment')) {
            $('#attachment').trigger('click');
        }
    });

    $('#attachment').on('click', function(event) {
        event.stopPropagation();
    });

    $('#uploadArea').on('dragover dragenter', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).addClass('is-dragging');
    }).on('dragleave drop', function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).removeClass('is-dragging');
    }).on('drop', function(event) {
        const files = event.originalEvent.dataTransfer.files;
        if (files.length) {
            $('#attachment')[0].files = files;
            $('#attachment').trigger('change');
        }
    });
    
    $('#attachment').change(function() {
        const file = this.files[0];
        if (file) {
            $('#fileName').html('<i class="fas fa-file"></i> Selected: ' + file.name).show();
            
            if (file.size > 20 * 1024 * 1024) {
                showAlert('error', 'File size must be less than 20MB');
                $(this).val('');
                $('#fileName').hide();
            }
        }
    });
    
    // Save as draft
    $(document).off('click', '#saveDraft').on('click', '#saveDraft', function() {
        $('#status').val('draft');
        submitForm();
    });
    
    // Submit or Save Changes
    $(document).off('click', '#submitBtn').on('click', '#submitBtn', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        // Your existing submit logic here
        const id = $('#id').val();
        if (!id) {
            if ($('#status').val() === 'new') {
                $('#status').val('submitted');
            }
            if (validateForm()) {
                submitForm();
            }
        } else {
            if (validateForm()) {
                saveChanges();
            }
        }
    });
    
    // Cancel
    $(document).off('click', '#cancelBtn').on('click', '#cancelBtn', function() {
        if (confirm('Are you sure you want to cancel? Unsaved changes will be lost.')) {
            window.location.href = '<?php echo site_url("asset_disposals"); ?>';
        }
    });
    
    // Approve button
    $('#approveBtn').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to approve this request?')) {
            changeStatus(id, 'approved');
        }
    });
    
    // Reject button
    $('#rejectBtn').click(function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to reject this request?')) {
            changeStatus(id, 'rejected');
        }
    });
    
    // Validate form
    function validateForm() {
        let valid = true;
        
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        
        if (!$('#equipment_asset_id').val()) {
            $('#equipment_asset_id').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        if (!$('#write_off_reason_id').val()) {
            $('#write_off_reason_id').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        if (!$('#disposal_method_id').val()) {
            $('#disposal_method_id').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        if (!$('#justification').val().trim()) {
            $('#justification').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        return valid;
    }
    
    // Change status function
    function changeStatus(id, status) {
        $.ajax({
            url: urls.change_status,
            type: 'POST',
            data: { 
                id: id, 
                status: status
            },
            dataType: 'json',
            beforeSend: function() {
                if (status == 'approved') {
                    $('#approveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');
                } else {
                    $('#rejectBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
                }
            },
            success: function(response) {
                if(response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to update status');
            },
            complete: function() {
                $('#approveBtn').prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
                $('#rejectBtn').prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
            }
        });
    }
    
    // Submit new form
// function submitForm() {
//     const formData = new FormData($('#writeOffForm')[0]);
    
//     // Make sure to include assigned_to and disposal_date
//     formData.append('assigned_to', $('#assigned_to').val());
//     formData.append('disposal_date', $('#disposal_date').val());
    
//     $.ajax({
//         url: urls.save,
//         type: 'POST',
//         data: formData,
//         dataType: 'json',
//         contentType: false,
//         processData: false,
//         beforeSend: function() {
//             $('#submitBtn, #saveDraft').prop('disabled', true)
//                 .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
//         },
//         success: function(response) {
//             if (response.success) {
//                 showAlert('success', response.message);
//                 setTimeout(() => {
//                     window.location.href = response.redirect;
//                 }, 1500);
//             } else {
//                 showAlert('error', response.message);
//                 if (response.field) {
//                     $('#' + response.field).addClass('is-invalid').focus();
//                 }
//             }
//             $('#submitBtn, #saveDraft').prop('disabled', false)
//                 .html('<i class="fas fa-paper-plane"></i> Submit for Approval');
//         },
//         error: function() {
//             showAlert('error', 'An error occurred while saving');
//             $('#submitBtn, #saveDraft').prop('disabled', false)
//                 .html('<i class="fas fa-paper-plane"></i> Submit for Approval');
//         }
//     });
// }
var processingForm = false;
function submitForm() {
    // Prevent multiple submissions
    if (processingForm) {
        return;
    }
    
    processingForm = true;
    
    const formData = new FormData();
    
    // Add all form fields manually
    formData.append('id', $('#id').val());
    formData.append('equipment_asset_id', $('#equipment_asset_id').val());
    formData.append('write_off_reason_id', $('#write_off_reason_id').val());
    formData.append('disposal_method_id', $('#disposal_method_id').val());
    formData.append('estimated_value', $('#estimated_value').val());
    formData.append('justification', $('#justification').val());
    formData.append('status', $('#status').val());
    
    // Only append attachment if file is selected
    const attachmentFile = $('#attachment')[0].files[0];
    if (attachmentFile) {
        // Check file size (20MB limit)
        if (attachmentFile.size > 20 * 1024 * 1024) {
            showAlert('error', 'File size must be less than 20MB');
            processingForm = false;
            return;
        }
        formData.append('attachment', attachmentFile);
    }
    
    $.ajax({
        url: urls.save,
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#submitBtn, #saveDraft').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        },
        success: function(response) {
            processingForm = false;
            
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                showAlert('error', response.message || 'Error occurred');
                if (response.field) {
                    $('#' + response.field).addClass('is-invalid').focus();
                }
            }
            $('#submitBtn, #saveDraft').prop('disabled', false)
                .html('<i class="fas fa-paper-plane"></i> Submit for Approval');
        },
        error: function(xhr, status, error) {
            processingForm = false;
            let errorMsg = 'An error occurred while saving';
            
            if (xhr.status === 413) {
                errorMsg = 'File size is too large. Please upload a file less than 20MB.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            showAlert('error', errorMsg);
            $('#submitBtn, #saveDraft').prop('disabled', false)
                .html('<i class="fas fa-paper-plane"></i> Submit for Approval');
        }
    });
}
    
    // Save changes
function saveChanges() {
    const formData = {
        id: $('#id').val(),
        equipment_asset_id: $('#equipment_asset_id').val(),
        write_off_reason_id: $('#write_off_reason_id').val(),
        disposal_method_id: $('#disposal_method_id').val(),
        estimated_value: $('#estimated_value').val(),
        justification: $('#justification').val(),
    };
    
    $.ajax({
        url: urls.save_changes,
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#submitBtn').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                showAlert('error', response.message);
                if (response.field) {
                    $('#' + response.field).addClass('is-invalid').focus();
                }
            }
            $('#submitBtn').prop('disabled', false)
                .html('<i class="fas fa-save"></i> Save Changes');
        },
        error: function() {
            showAlert('error', 'An error occurred while saving');
            $('#submitBtn').prop('disabled', false)
                .html('<i class="fas fa-save"></i> Save Changes');
        }
    });
}
    
    // Show alert
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" 
                 style="position: fixed; top: 100px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }
    
    // Auto-fill asset details if editing
    <?php if($request && $request->equipment_asset_id): ?>
        $('#equipment_asset_id').trigger('change');
    <?php endif; ?>
});
</script>
