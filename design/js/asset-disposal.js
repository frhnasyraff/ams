$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select Asset...'
    });
    
    // URLs
    const urls = {
        save: '<?php echo site_url("asset_disposals/save"); ?>',
        getAssetDetails: '<?php echo site_url("asset_disposals/get_asset_details"); ?>'
    };
    
    // When asset is selected
    $('#asset_id').on('change', function() {
        const assetId = $(this).val();
        
        if (assetId) {
            $.ajax({
                url: urls.getAssetDetails,
                type: 'POST',
                data: { asset_id: assetId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#assetDetails').show();
                        $('#assetTag').text(response.asset_tag || 'N/A');
                        $('#serialNumber').text(response.serial_number || 'N/A');
                        $('#assetType').text(response.asset_type || 'N/A');
                        $('#purchaseDate').text(response.purchase_date || 'N/A');
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to load asset details');
                }
            });
        } else {
            $('#assetDetails').hide();
        }
    });
    
    // Trigger change if editing
    <?php if($disposal && $disposal->asset_id): ?>
        $('#asset_id').trigger('change');
    <?php endif; ?>
    
    // Form submission
    $('#saveBtn').click(function() {
        if (validateForm()) {
            submitForm();
        }
    });
    
    // Validate form
    function validateForm() {
        let valid = true;
        
        // Reset validation
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        
        // Check asset selection
        if (!$('#asset_id').val()) {
            $('#asset_id').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        // Check disposal method
        if (!$('#disposal_method').val()) {
            $('#disposal_method').addClass('is-invalid').next('.invalid-feedback').show();
            valid = false;
        }
        
        return valid;
    }
    
    // Submit form via AJAX
    function submitForm() {
        const formData = {
            id: $('#id').val(),
            asset_id: $('#asset_id').val(),
            disposal_method: $('#disposal_method').val(),
            status: $('#status').val(),
            disposal_date: $('#disposal_date').val(),
            assigned_to: $('#assigned_to').val(),
            notes: $('#notes').val()
        };
        
        $.ajax({
            url: urls.save,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#saveBtn').prop('disabled', true)
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
                $('#saveBtn').prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Save');
            },
            error: function() {
                showAlert('error', 'An error occurred while saving');
                $('#saveBtn').prop('disabled', false)
                    .html('<i class="fas fa-save"></i> Save');
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
});

