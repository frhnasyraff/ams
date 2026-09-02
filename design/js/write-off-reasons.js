$(document).ready(function() {
    
    const writeOffUrls = window.WRITE_OFF_URLS;
    
    console.log("JavaScript loaded successfully");
    console.log("Save URL:", writeOffUrls.save);

    function setModalTitle(title) {
        $('#reasonModalLabel').html('<span class="master-modal-title-icon"><i class="fas fa-file-signature"></i></span>' + title);
    }
    
    // Add new reason button click
    $('#addReasonBtn').click(function() {
        console.log("Add button clicked");
        setModalTitle('Add Write-off Reason');
        $('#reasonForm')[0].reset();
        $('#reasonId').val('');
        $('#status').val('active');
        $('#reasonModal').modal('show');
    });
    
    // Edit reason button click
    $(document).on('click', '.edit-reason', function() {
        const id = $(this).data('id');
        const reason = $(this).data('reason');
        const description = $(this).data('description');
        const status = $(this).data('status');
        
        setModalTitle('Edit Write-off Reason');
        $('#reasonId').val(id);
        $('#write_off_reason').val(reason);
        $('#description').val(description);
        $('#status').val(status);
        $('#reasonModal').modal('show');
    });
    
    // Delete reason button click
    $(document).on('click', '.delete-reason', function() {
        const id = $(this).data('id');
        const reason = $(this).data('reason');
        
        $('#deleteReasonName').text(reason);
        $('#deleteModal').data('id', id);
        $('#deleteModal').modal('show');
    });
    
    // Confirm delete
    $('#confirmDelete').click(function() {
        const id = $('#deleteModal').data('id');
        
        $.ajax({
            url: writeOffUrls.delete,
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Page reload karein
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', response.message);
                }
                $('#deleteModal').modal('hide');
            },
            error: function() {
                showAlert('error', 'An error occurred while deleting.');
                $('#deleteModal').modal('hide');
            }
        });
    });


    function loadReasons() {
        $.ajax({
            url: writeOffUrls.list,
            type: 'GET',
            data: {
                search: $('#searchInput').val(),
                status: $('#statusFilter').val()
            },
            dataType: 'json',
            success: function (res) {
                if (!res.success) return;

                let html = '';

                if (res.data.length === 0) {
                    html = `<tr><td colspan="6" class="text-center">No data found</td></tr>`;
                } else {
                    $.each(res.data, function (i, r) {
                        html += `
                            <tr>
                                <td>${r.id}</td>
                                <td>${r.write_off_reason}</td>
                                <td>${r.description ?? '-'}</td>
                                <td>
                                    <span class="badge badge-${r.status === 'active' ? 'success' : 'danger'}">
                                        ${r.status}
                                    </span>
                                </td>
                                <td>${r.created_at}</td>
                                <td>—</td>
                            </tr>
                        `;
                    });
                }

                $('#reasonTableBody').html(html);
            }
        });
    }

    // Search
    $('#searchBtn').on('click', loadReasons);

    // Status filter
    $('#statusFilter').on('change', loadReasons);

    // Reset
    $('#resetFilters').on('click', function () {
        $('#searchInput').val('');
        $('#statusFilter').val('');
        loadReasons();
    });
    
    
    // Save form (add/edit)
    $('#reasonForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        console.log("Form data:", formData);
        
        // Show loading on button
        const saveBtn = $('#saveBtn');
        const originalHtml = saveBtn.html();
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: writeOffUrls.save,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Save response:', response);
                
                if (response.success) {
                    showAlert('success', response.message);
                    $('#reasonModal').modal('hide');
                    // Page reload karein
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', response.message);
                    // Highlight error field
                    if (response.field) {
                        $('#' + response.field).addClass('is-invalid');
                        $('#reasonError').text(response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                showAlert('error', 'An error occurred while saving: ' + error);
            },
            complete: function() {
                saveBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Show alert message
    function showAlert(type, message) {
        // Remove existing alerts
        $('.alert-dismissible').remove();
        
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
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }
    
    // Simple test button for debugging
    $('<button type="button" class="btn btn-warning" id="testButton" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">Test JS</button>').appendTo('body');
    
    $('#testButton').click(function() {
        alert('JavaScript is working!');
        console.log('Test button clicked');
        console.log('jQuery version:', $.fn.jquery);
        
        // Test modal
        $('#reasonModal').modal('show');
    });
});
